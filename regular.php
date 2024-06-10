<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TransJakarta Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php
    require_once 'C:\xampp\htdocs\transjakarta-dashboard\vendor\autoload.php';

    $client = new MongoDB\Client();
    $transjakarta = $client->pdds->dftransjakarta;

    $years = $transjakarta->distinct('year');
    $selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : 2022;
    $selectedHour = isset($_GET['hour']) ? (int)$_GET['hour'] : 7;

    // Filter data berdasarkan tahun dan jam tertentu
    $query = [
        'year' => $selectedYear,
        'tapInTime' => [
            '$gte' => new MongoDB\BSON\UTCDateTime((new DateTime("$selectedYear-01-01 $selectedHour:00:00"))->getTimestamp()*1000),
            '$lt' => new MongoDB\BSON\UTCDateTime((new DateTime("$selectedYear-01-01 " . ($selectedHour + 1) . ":00:00"))->getTimestamp()*1000)
        ]
    ];

    $pipeline = [
        ['$match' => $query],
        ['$group' => ['_id' => '$tapInStopsName', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]],
        ['$limit' => 5]
    ];

    $result = $transjakarta->aggregate($pipeline);

    $stops = [];
    $counts = [];
    foreach ($result as $doc) {
        $stops[] = $doc['_id'];
        $counts[] = $doc['count'];
    }
    ?>

    <h1>Top 5 Stops Terame pada Jam <?php echo $selectedHour; ?>:00 di Tahun <?php echo $selectedYear; ?></h1>
    <form action="reguler.php" method="get">
        <label for="year">Year:</label>
        <select id="year" name="year">
            <?php
            foreach ($years as $year) {
                echo "<option value='" . htmlspecialchars($year) . "'"
                . ($selectedYear == $year ? ' selected' : '') 
                . ">" . htmlspecialchars($year) . "</option>";
            }
            ?>
        </select>
        <br><br>

        <label for="hour">Hour:</label>
        <select id="hour" name="hour">
            <?php
            for ($i = 0; $i < 24; $i++) {
                echo "<option value='" . $i . "'"
                . ($selectedHour == $i ? ' selected' : '') 
                . ">" . $i . ":00</option>";
            }
            ?>
        </select>
        <br><br>

        <input type="submit" value="Filter">
    </form>

    <canvas id="stopsChart" width="400" height="200"></canvas>
    <script>
        var ctx = document.getElementById('stopsChart').getContext('2d');
        var stopsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($stops); ?>,
                datasets: [{
                    label: 'Jumlah Penumpang',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="navbar.css">
</head>
<body>
    <main>
      <?php include 'navbar.php'; ?>

      <section class="content">
        <div class="left-content">
            <h1>Transjakarta Analysis for Regular Bus</h1>

          
        </div>

        <div class="right-content">
          
          

          

          
        </div>
      </section>
    </main>

    <script src="https://kit.fontawesome.com/e52db3bf8a.js" crossorigin="anonymous"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
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
