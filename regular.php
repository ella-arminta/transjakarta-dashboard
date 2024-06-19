<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;

$client = new Client();
$transjakarta = $client->pdds->dftransjakarta;
// $transjakarta = $client->pdds->transjakarta;
$transjakarta = $client->transjakarta->transaction3;

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
$pipelineCards = 
    [
        [
            '$match' => [
                'year' => $year,
                'payAmount' => ['$in' => [0, 3500]]
            ]
        ],
        [
            '$lookup' => [
                'from' => 'dfpaycards',
                'localField' => 'payCardID',
                'foreignField' => 'payCardIDTable',
                'as' => 'payCardDetails'
            ]
        ],
        [
            '$unwind' => '$payCardDetails'
        ],
        [
            '$group' => [
                '_id' => [
                    'payCardBank' => '$payCardDetails.payCardBank',
                    'ageGroup' => [
                        '$switch' => [
                            'branches' => [
                                [
                                    'case' => [
                                        '$and' => [
                                            ['$gte' => ['$payCardDetails.payCardBirthDate', 2000]],
                                            ['$lte' => ['$payCardDetails.payCardBirthDate', 2007]]
                                        ]
                                    ],
                                    'then' => '18-25'
                                ],
                                [
                                    'case' => [
                                        '$and' => [
                                            ['$gte' => ['$payCardDetails.payCardBirthDate', 1990]],
                                            ['$lte' => ['$payCardDetails.payCardBirthDate', 1999]]
                                        ]
                                    ],
                                    'then' => '26-35'
                                ],
                                [
                                    'case' => [
                                        '$and' => [
                                            ['$gte' => ['$payCardDetails.payCardBirthDate', 1980]],
                                            ['$lte' => ['$payCardDetails.payCardBirthDate', 1989]]
                                        ]
                                    ],
                                    'then' => '36-45'
                                ],
                                [
                                    'case' => [
                                        '$and' => [
                                            ['$gte' => ['$payCardDetails.payCardBirthDate', 1970]],
                                            ['$lte' => ['$payCardDetails.payCardBirthDate', 1979]]
                                        ]
                                    ],
                                    'then' => '46-55'
                                ],
                                [
                                    'case' => ['$gte' => ['$payCardDetails.payCardBirthDate', 1965]],
                                    'then' => '56+'
                                ],
                            ],
                        ]
                    ]
                ],
                'totalUsage' => ['$sum' => 1]
            ]
        ],
        [
            '$sort' => [
                '_id.ageGroup' => 1,
                'totalUsage' => -1
            ]
        ],
        [
            '$group' => [
                '_id' => '$_id.ageGroup',
                'banks' => [
                    '$push' => [
                        'payCardBank' => '$_id.payCardBank',
                        'totalUsage' => '$totalUsage'
                    ]
                ]
            ]
        ],
        [
            '$project' => [
                '_id' => 0,
                'ageGroup' => '$_id',
                'banks' => ['$slice' => ['$banks', 5]]
            ]
        ],
        [
            '$sort' => [
                'ageGroup' => 1
            ]
        ]
    ];
    


$resultCards = $transjakarta->aggregate($pipelineCards);

$usageData = [];
foreach ($resultCards as $doc) {
    $ageGroup = $doc['_id']['ageGroup'];
    $gender = $doc['_id']['gender'];
    $count = $doc['totalUsage'];

    if (!isset($usageData[$ageGroup])) {
        $usageData[$ageGroup] = ['Male' => 0, 'Female' => 0];
    }
    $usageData[$ageGroup][$gender] = $count;
}

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
            const ctxCards = document.getElementById('usageChart').getContext('2d');

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

            // Chart for top 5 cards used by age group and gender
            const ageGroups = Object.keys(usageData);
            const maleData = ageGroups.map(ageGroup => usageData[ageGroup].Male);
            const femaleData = ageGroups.map(ageGroup => usageData[ageGroup].Female);

            const configCards = {
                type: 'bar',
                data: {
                    labels: ageGroups,
                    datasets: [
                        {
                            label: 'Male',
                            data: maleData,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Female',
                            data: femaleData,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: `Transaction Count by Age Group and Gender in Year ${selectedYear}`
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    return `${label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Age Group'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Transaction Count'
                            },
                            beginAtZero: true
                        }
                    }
                }
            };

            const usageChart = new Chart(ctxCards, configCards);
        });
    </script>
</body>
</html>

