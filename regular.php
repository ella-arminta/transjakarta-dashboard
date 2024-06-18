<?php
require_once 'C:\xampp\htdocs\transjakarta-dashboard\vendor\autoload.php';

$client = new MongoDB\Client();
$transjakarta = $client->pdds->dftransjakarta;

$year = isset($_GET['year']) ? intval($_GET['year']) : 2022;

$pipeline = [
    [
        '$match' => [
            'year' => $year,
            'payAmount' => ['$in' => [0, 3500]]
        ]
    ],
    [
        '$group' => [
            '_id' => [
                'hourIn' => ['$hour' => ['$dateFromString' => ['dateString' => '$tapInTime']]],
                'tapInStopsName' => '$tapInStopsName'
            ],
            'count' => ['$sum' => 1]
        ]
    ],
    [
        '$sort' => [
            '_id.hourIn' => 1,
            'count' => -1
        ]
    ],
    [
        '$group' => [
            '_id' => '$_id.hourIn',
            'stops' => [
                '$push' => [
                    'tapInStopsName' => '$_id.tapInStopsName',
                    'count' => '$count'
                ]
            ]
        ]
    ],
    [
        '$project' => [
            'hourIn' => '$_id',
            'stops' => [
                '$slice' => ['$stops', 5]
            ]
        ]
    ],
    [
        '$sort' => [
            'hourIn' => 1
        ]
    ]
];

$result = $transjakarta->aggregate($pipeline);

$hourlyTopStops = [];
foreach ($result as $doc) {
    $hourlyTopStops[$doc['hourIn']] = $doc['stops'];
}

$hourlyTopStopsJson = json_encode($hourlyTopStops);
echo "<script>const hourlyTopStops = $hourlyTopStopsJson;</script>";
echo "<script>const selectedYear = $year;</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transjakarta Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <main>
        <?php include 'navbar.php'; ?>

        <section class="content">
            <div>
                <h1>Transjakarta Analysis for Regular Bus</h1>
                <div>
                    <label for="yearSelect">Select Year: </label>
                    <select id="yearSelect" onchange="updateYear()">
                        <option value="2022" <?= $year == 2022 ? 'selected' : '' ?>>2022</option>
                        <option value="2023" <?= $year == 2023 ? 'selected' : '' ?>>2023</option>
                    </select>
                </div>
                <div class="chart-container" style="width: 80%; height: 600px;">
                    <canvas id="hourlyTopStopsChart"></canvas>
                </div>
            </div>
        </section>
    </main>

    <script src="https://kit.fontawesome.com/e52db3bf8a.js" crossorigin="anonymous"></script>
    <script>
        const navItems = document.querySelectorAll(".nav-item");

        navItems.forEach((navItem, i) => {
            navItem.addEventListener("click", () => {
                navItems.forEach((item, j) => {
                    item.className = "nav-item";
                });
                navItem.className = "nav-item active";
            });
        });

        function updateYear() {
            const selectedYear = document.getElementById('yearSelect').value;
            window.location.href = `?year=${selectedYear}`;
        }

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('hourlyTopStopsChart').getContext('2d');

            const hours = Object.keys(hourlyTopStops);
            const datasets = [];

            for (let i = 0; i < 5; i++) {
                const stopCounts = hours.map(hour => hourlyTopStops[hour][i] ? hourlyTopStops[hour][i].count : 0);
                const stopNames = hours.map(hour => hourlyTopStops[hour][i] ? hourlyTopStops[hour][i].tapInStopsName : '');
                
                datasets.push({
                    label: `Stop ${i+1}`,
                    data: stopCounts,
                    backgroundColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`,
                    borderColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`,
                    borderWidth: 1,
                    datalabels: {
                        anchor: 'end',
                        align: 'start',
                        formatter: function(value, context) {
                            return stopNames[context.dataIndex];
                        }
                    }
                });
            }

            const config = {
                type: 'bar',
                data: {
                    labels: hours,
                    datasets: datasets
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: `Top 5 Stops per Hour in Year ${selectedYear}`
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const stopName = context.chart.data.datasets[context.datasetIndex].datalabels.formatter(context.raw, context);
                                    return `${label}: ${stopName} (${context.raw})`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Hour'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Count'
                            },
                            beginAtZero: true
                        }
                    }
                }
            };

            const hourlyTopStopsChart = new Chart(ctx, config);
        });
    </script>
</body>
</html>
