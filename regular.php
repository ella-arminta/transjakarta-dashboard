<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;

$client = new Client();
#felina
$transjakarta = $client->pdds->dftransjakarta;
$paycard = $client->pdds->paycard;
// $paycard = $client->pdds->paycard;

#ella
// $transjakarta = $client->transjakarta->transaction3;

$year = isset($_GET['year']) ? intval($_GET['year']) : 2022;

/// Query for top 5 stops per hour
$pipelineStops = [
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

$resultStops = $transjakarta->aggregate($pipelineStops);

$hourlyTopStops = [];
foreach ($resultStops as $doc) {
    $hourlyTopStops[$doc['hourIn']] = $doc['stops'];
}

$hourlyTopStopsJson = json_encode($hourlyTopStops);
echo "<script>const hourlyTopStops = $hourlyTopStopsJson;</script>";

// Query for top 5 cards used by age group and gender
$pipelineCards = [
    // [
    //     '$match' => [
    //         'year' => intval($year)  // Assuming 'year' is a variable in your PHP script
    //     ]
    // ],
    [
        '$project' => [
            'age' => 1,  // Directly use the age field
            'payCardBank' => 1
        ]
    ],
    [
        '$group' => [
            '_id' => [
                'payCardBank' => '$payCardBank',
                'ageGroup' => [
                    '$switch' => [
                        'branches' => [
                            ['case' => ['$lte' => ['$age', 17]], 'then' => '0-17'],
                            ['case' => ['$and' => [['$gt' => ['$age', 17]], ['$lte' => ['$age', 25]]]], 'then' => '18-25'],
                            ['case' => ['$and' => [['$gt' => ['$age', 25]], ['$lte' => ['$age', 35]]]], 'then' => '26-35'],
                            ['case' => ['$and' => [['$gt' => ['$age', 35]], ['$lte' => ['$age', 45]]]], 'then' => '36-45'],
                            ['case' => ['$and' => [['$gt' => ['$age', 45]], ['$lte' => ['$age', 55]]]], 'then' => '46-55'],
                            ['case' => ['$gt' => ['$age', 55]], 'then' => '55+']
                        ],
                        'default' => 'Unknown'
                    ]
                ]
            ],
            'count' => ['$sum' => 1]
        ]
    ],
    [
        '$sort' => [
            '_id.ageGroup' => 1
        ]
    ]
];

$resultCards = $paycard->aggregate($pipelineCards);
// echo var_dump($resultCards->toArray());
// exit();
$usageData = [
    // '0-17' => ['flazz' => 10, 'otherBank' => 5],
    // '18-25' => ['flazz' => 20, 'otherBank' => 15],
    // // Tambahkan lebih banyak grup usia jika perlu
];

// $documents = $resultCards->toArray();
foreach ($resultCards as $doc) {
// echo "hi";
    $ageGroup = $doc->_id->ageGroup;

    $payCardBank = $doc->_id->payCardBank;
    $count = $doc->count;
    if (!isset($usageData[$ageGroup])) {
        $usageData[$ageGroup] = [];
    }
    $usageData[$ageGroup][$payCardBank] = $count;
}
//  echo var_dump($usageData);
// exit();
$usageDataJson = json_encode($usageData);
echo "<script>const usageData = $usageDataJson;</script>";

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
                <div class="chart-container" style="width: 80%; height: 600px; margin-top: 50px;">
                    <canvas id="usageChart"></canvas>
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
            const ctxStops = document.getElementById('hourlyTopStopsChart').getContext('2d');
            // const ctxCards = document.getElementById('usageChart').getContext('2d');

            // Chart for top 5 stops per hour
            const hours = Object.keys(hourlyTopStops);
            const datasetsStops = [];

            for (let i = 0; i < 5; i++) {
                const stopCounts = hours.map(hour => hourlyTopStops[hour][i] ? hourlyTopStops[hour][i].count : 0);
                const stopNames = hours.map(hour => hourlyTopStops[hour][i] ? hourlyTopStops[hour][i].tapInStopsName : '');
                
                datasetsStops.push({
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

            const configStops = {
                type: 'bar',
                data: {
                    labels: hours,
                    datasets: datasetsStops
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

            const hourlyTopStopsChart = new Chart(ctxStops, configStops);

        //     if (!usageData || Object.keys(usageData).length === 0) {
        // console.error("No data available to display the chart.");
        // return;
    // }

        // Chart for top 5 cards used by age group and gender
        var labels = Object.keys(usageData);

        // echo console.log(labels);

    // Ensure labels array is not empty
    if (labels.length === 0) {
        console.error("No labels found in usageData.");
        return;
    }

        console.log("Labels:", labels);
        console.log("UsageData:", usageData);


        // Extract datasets (categories with counts) for each age group
        var datasets = Object.keys(usageData[labels[0]]).map(category => ({
            label: category,
            data: labels.map(label => usageData[label][category]),
            backgroundColor: getRandomColor(), // Function to generate random colors for bars
            borderColor: '#ffffff',  // Border color of bars
            borderWidth: 1  // Border width of bars
        }));

        const ctx = document.getElementById('usageChart').getContext('2d');
        const usageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                scales: {
                    x: {
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Age Group'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Usage Count'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true // Display false for less cluttered graph, can be true for more detailed legend
                    }
                }
            }
        });
    });
    
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    </script>
</body>
</html>

