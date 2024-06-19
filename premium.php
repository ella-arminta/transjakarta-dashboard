<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;

try {
    $client = new Client();
    // $transjakarta = $client->projekpdds->dftransjakarta;
    $transjakarta = $client->transjakarta->transaction3;

    // Default to current year and month if not provided
    $currentYear = date('Y');
    $currentMonth = date('m');

    // Process form input
    $year = isset($_GET['year']) ? intval($_GET['year']) : $currentYear;
    $month = isset($_GET['month']) ? intval($_GET['month']) : $currentMonth;

    // Ensure month is within valid range
    if ($month < 1 || $month > 12) {
        $month = $currentMonth; // fallback to current month if invalid
    }

    // Aggregation pipeline to filter by year and month and project yearMonth
    $pipeline = [
        [
            '$match' => [
                'year' => $year,
                'month' => $month
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'year' => '$year',
                    'month' => '$month'
                ],
                'count' => ['$sum' => 1]
            ]
        ],
        [
            '$project' => [
                'yearMonth' => [
                    '$concat' => [
                        ['$substr' => ['$year', 0, -1]], '-', ['$substr' => ['$month', 0, -1]]
                    ]
                ],
                'count' => 1,
                '_id' => 0
            ]
        ],
        [
            '$sort' => [
                'yearMonth' => 1
            ]
        ]
    ];

    $results = $transjakarta->aggregate($pipeline);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transjakarta Customer Count</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="navbar.css">
  <style>
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
      text-align: left;
      padding: 8px;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <main>
    <?php include 'navbar.php'; ?>

    <section class="content">
      <div class="left-content" style="grid-template-rows: 10% 90%;">
        <h1>Transjakarta Analysis for Premium Bus</h1>

        <div class="container mt-5" style="height : 800px;">
          <h1>Customer Count Per Month</h1>
          <form method="GET" class="mb-4">
            <div class="row">
              <div class="col">
                <select name="year" class="form-select" required>
                  <option value="2022" <?php if ($year == 2022) echo 'selected'; ?>>2022</option>
                  <option value="2023" <?php if ($year == 2023) echo 'selected'; ?>>2023</option>
                </select>
              </div>
              <div class="col">
                <select name="month" class="form-select" required>
                  <?php
                  $months = [
                      1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                      5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                      9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                  ];

                  foreach ($months as $num => $name) {
                      $selected = ($month == $num) ? 'selected' : '';
                      echo "<option value=\"$num\" $selected>$name</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col">
                <button type="submit" class="btn btn-primary">Filter</button>
              </div>
            </div>
          </form>

          <table class="table table-bordered">
            <thead>
              <tr>
                <!-- <th>Month-Year</th> -->
                <th>Customer Count</th>
              </tr>
            </thead>
            <tbody>
              <?php
        if (isset($results)) {
            foreach ($results as $result) {
                echo "<tr>";
                //echo "<td>" . htmlspecialchars($result['yearMonth']) . "</td>";
                echo "<td>" . htmlspecialchars($result['count']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No results found.</td></tr>";
        }
        ?>
            </tbody>
          </table>
          <h2>Customer Performance Comparison Year by Year</h2>
                <form method="GET">
                    <label for="monthFilter">Select Month:</label>
                    <select id="monthFilter" name="months">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <button type="submit">Filter</button>
                </form>
                <table id="customerPerformanceTable">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Number of Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once __DIR__ . '/vendor/autoload.php';

                        //use MongoDB\Client;

                        $client = new Client();
                        // $transjakarta = $client->projekpdds->dftransjakarta;
                        $transjakarta = $client->transjakarta->transaction3;

                        // Initialize default month (if not set)
                        $selectedMonth = isset($_GET['months']) ? intval($_GET['months']) : null;

                        // Aggregation pipeline stages
                        $pipeline = [
                            [
                                '$match' => [
                                    '$or' => [
                                        ['$and' => [['month' => $selectedMonth ?? 4], ['year' => 2022]]],
                                        ['$and' => [['month' => $selectedMonth ?? 4], ['year' => 2023]]]
                                    ]
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => ['month' => '$month', 'year' => '$year'],
                                    'count' => ['$sum' => 1]
                                ]
                            ],
                            [
                                '$sort' => ['_id.year' => 1, '_id.month' => 1]
                            ]
                        ];

                        // Execute aggregation pipeline
                        $cursor = $transjakarta->aggregate($pipeline);

                        // Convert cursor to array
                        $result = iterator_to_array($cursor);

                        // Output table rows
                        foreach ($result as $entry) {
                            echo '<tr>';
                            echo '<td>' . $entry['_id']['month'] . '</td>';
                            echo '<td>' . $entry['_id']['year'] . '</td>';
                            echo '<td>' . $entry['count'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <title>Transaction Visualization</title>
    <?php
require_once __DIR__ . '/vendor/autoload.php';

//use MongoDB\Client;

// Menghubungkan ke MongoDB
$client = new Client();
$transjakarta = $client->projekpdds->dftransjakarta;

// Variabel untuk menyimpan nilai default bulan dan tahun
$selectedMonth = isset($_POST['month']) ? (int) $_POST['month'] : 1; // Default bulan Januari
$selectedYear = isset($_POST['year']) ? (int) $_POST['year'] : date('Y'); // Default tahun saat ini

// Pilihan bulan dan tahun
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

$years = range(date('Y') - 2, date('Y') - 1);

// Pipeline MongoDB
$pipeline = [
    [
        '$match' => [
            "month" => $selectedMonth,
            "year" => $selectedYear
        ]
    ],
    [
        '$group' => [
            '_id' => '$payCardID',
            'count' => ['$sum' => 1]
        ]
    ],
    [
        '$sort' => ['count' => -1]
    ],
    [
        '$facet' => [
            'maxCustomer' => [
                ['$limit' => 1],
                [
                    '$group' => [
                        '_id' => null,
                        'customer' => ['$first' => '$_id'],
                        'count' => ['$first' => '$count']
                    ]
                ]
            ],
            'minCustomer' => [
                ['$sort' => ['count' => 1]],
                ['$limit' => 1],
                [
                    '$group' => [
                        '_id' => null,
                        'customer' => ['$first' => '$_id'],
                        'count' => ['$first' => '$count']
                    ]
                ]
            ]
        ]
    ]
];

// Eksekusi pipeline MongoDB
$result = $transjakarta->aggregate($pipeline)->toArray();

// Memproses hasil
$maxCustomer = isset($result[0]->maxCustomer[0]) ? $result[0]->maxCustomer[0] : null;
$minCustomer = isset($result[0]->minCustomer[0]) ? $result[0]->minCustomer[0] : null;

// Data untuk visualisasi
$data = [
    ['Customer', 'Count'],
    ['Max Customer', $maxCustomer ? $maxCustomer->count : 0],
    ['Min Customer', $minCustomer ? $minCustomer->count : 0]
];

// Konversi data ke format JSON
$jsonData = json_encode($data);

?>
    <!-- Memuat Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo $jsonData; ?>);

            var options = {
                title: 'Transaction Count for Max and Min Customers',
                chartArea: {width: '50%'},
                hAxis: {
                    title: 'Count',
                    minValue: 0
                },
                vAxis: {
                    title: 'Customer'
                }
            };

            var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <h2>Transaction Count</h2>
    <form method="post">
        <label for="month">Month:</label>
        <select id="month" name="month">
            <?php foreach ($months as $key => $value): ?>
                <option value="<?php echo $key; ?>" <?php if ($key == $selectedMonth) echo 'selected="selected"'; ?>><?php echo $value; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="year">Year:</label>
        <select id="year" name="year">
            <?php foreach ($years as $year): ?>
                <option value="<?php echo $year; ?>" <?php if ($year == $selectedYear) echo 'selected="selected"'; ?>><?php echo $year; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Submit">
    </form>

    <div id="chart_div" style="width: 400px; height: 200px;"></div>
        </div>
      </div>

      <div class="right-content">
        <h2 style="font-size: 125%;">Customer Performance Comparison TM vs LM</h2>
        <table id="customerPerformanceTable">
          <thead>
            <tr>
              <th>Month</th>
              <th>Year</th>
              <th>Number of Transactions</th>
            </tr>
          </thead>
          <tbody>
            <?php
                        require_once __DIR__ . '/vendor/autoload.php';
                        $pipeline = [
                            [
                                '$match' => [
                                    '$or' => [
                                        ['$and' => [['month' => 4], ['year' => 2022]]],
                                        ['$and' => [['month' => 3], ['year' => 2022]]],
                                        ['$and' => [['month' => 4], ['year' => 2023]]],
                                        ['$and' => [['month' => 3], ['year' => 2023]]]
                                    ]
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => ['month' => '$month', 'year' => '$year'],
                                    'count' => ['$sum' => 1]
                                ]
                            ],
                            [
                                '$sort' => ['_id.year' => 1, '_id.month' => 1]
                            ]
                        ];

                        // Execute aggregation pipeline
                        $cursor = $transjakarta->aggregate($pipeline);

                        // Convert cursor to array
                        $result = iterator_to_array($cursor);

                        // Output table rows
                        foreach ($result as $entry) {
                            echo '<tr>';
                            echo '<td>' . $entry['_id']['month'] . '</td>';
                            echo '<td>' . $entry['_id']['year'] . '</td>';
                            echo '<td>' . $entry['count'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
          </tbody>
        </table>
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
  </script>
</body>
</html>