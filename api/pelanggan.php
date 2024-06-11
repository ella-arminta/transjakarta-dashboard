<?php
// Connect to sql database
include 'mysql_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if all required parameters are set
    if (isset($_GET['type']) && $_GET['type'] == 'card' && isset($_GET['id_pelanggan']) && isset($_GET['startdate']) && isset($_GET['enddate'])) {
        $id_pelanggan = $_GET['id_pelanggan'];
        $startdate = $_GET['startdate'];
        $enddate = $_GET['enddate'];

        if (DateTime::createFromFormat('Y-m-d', $startdate) !== false && DateTime::createFromFormat('Y-m-d', $enddate) !== false) {
            $total_pengeluaran = 0;

            if ($conn) {
                $stmt = $conn->prepare("
                    SELECT SUM(payAmount) as sum, COUNT(*) as count ,SUM(TIMESTAMPDIFF(SECOND, tapInTime, tapOutTime)) AS total_duration_seconds
                    FROM `transaction`
                    WHERE payCardID = ?
                    AND tapInTime BETWEEN ? AND ?
                ");

                if ($stmt) {
                    $stmt->execute([$id_pelanggan, $startdate, $enddate]);
                    $result = $stmt->fetch();
                    
                    if ($result) {
                      // get rata-rata pengeluaran waktu
                      // 
                      
                        echo json_encode($result);
                    } else {
                        echo json_encode([]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to prepare SQL statement']);
                }
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Database connection error']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}

// <!-- waktu :  harian / mingguan / bulanan -->

// <!-- CARDS -->
// <!-- total pengeluaran perwaktu -->
// <!-- total berapa kali ak sudah naik perwaktu -->
// <!-- total waktu yang digunakan dalam bis perwaktu -->
// <!-- SELECT SUM(payAmount), COUNT(*),SUM(TIMESTAMPDIFF(SECOND, tapInTime, tapOutTime)) AS total_duration_seconds
// FROM `transaction`
// WHERE payCardID = '5343809282239143' 
// AND tapInTime BETWEEN '2022-12-01 00:00:00' AND '2022-12-31 23:59:59';

// <!-- total rata-rata pengeluaran perwaktu -->
// <!-- [
//   // Match transactions for the specified payCardID and tapInTime range
//   {
//     $match: {
//       payCardID: 5343809282239143, // Assuming payCardID is a string
//       // hourIn: { $gte: 0, $lte: 23 }, // Assuming hourIn represents the hour of the day for tapInTime
//       $expr: {
//         $and: [
//           // Filter by tapInTime within the specified date range
//           { $gte: [ { $toDate: "$tapInTime" }, ISODate("2022-12-01T00:00:00Z")] },
//           { $lte: [ { $toDate: "$tapInTime" }, ISODate("2022-12-31T23:59:59Z")] }
//         ]
//       }
//     }
//   },
//   // Group transactions by month and year, calculating the sum of payAmount for each group
//   {
//     $group: {
//       _id: { month: "$month", year: "$year" },
//       sumPayAmount: { $sum: "$payAmount" }
//     }
//   },
//   // Sort the results by month and year in ascending order
//   {
//     $sort: {  "_id.year": 1, "_id.month": 1 }
//   },
//   {
//     $group: {
//       _id: null,
//       averagePayAmountPerMonth: { $avg: "$sumPayAmount" }
//     }
//   }
// ] -->

// <!-- total rata-rata naik perwaktu  -->
// <!-- [
//   // Match transactions for the specified payCardID and tapInTime range
//   {
//     $match: {
//       payCardID: 5343809282239143, // Assuming payCardID is a string
//       // hourIn: { $gte: 0, $lte: 23 }, // Assuming hourIn represents the hour of the day for tapInTime
//       // $expr: {
//       //   $and: [
//       //     // Filter by tapInTime within the specified date range
//       //     { $gte: [ { $toDate: "$tapInTime" }, ISODate("2022-12-01T00:00:00Z")] },
//       //     { $lte: [ { $toDate: "$tapInTime" }, ISODate("2022-12-31T23:59:59Z")] }
//       //   ]
//       // }
//     }
//   },
//   // Group transactions by month and year, calculating the sum of payAmount for each group
//   {
//     $group: {
//       _id: { month: "$month", year: "$year" },
//       countRide: { $sum: 1 }
//     }
//   },
//   // Sort the results by month and year in ascending order
//   {
//     $sort: {  "_id.year": 1, "_id.month": 1 }
//   },
//   {
//     $group: {
//       _id: null,
//       averageRidePerMonth: { $avg: "$countRide" }
//     }
//   }
// ] -->
// <!-- Total waktu rata-rata digunakan perwaktu -->
// <!-- [
//   // Match transactions for the specified payCardID and tapInTime range
//   {
//     $addFields: {
//       tapInTime: { $toDate: "$tapInTime" },
//       tapOutTime: { $toDate: "$tapOutTime" }
//     }
//   },
//   {
//     $match: {
//       payCardID: 5343809282239143, // Assuming payCardID is a string
//       // year: 2022,
//       // month:12,
//       tapInTime: {
//         $gte: ISODate("2022-12-01T00:00:00Z"),
//         $lte: ISODate("2022-12-31T23:59:59Z")
//       }
//     }
//   },
//   // Convert tapInTime and tapOutTime to Date objects
  
//   // Project the necessary fields and calculate the duration in seconds
//   {
//     $project: {
//       year: { $year: "$tapInTime" },
//       month: { $month: "$tapInTime" },
//       tapInTime: 1,
//       durationSeconds: {
//         $cond: [
//           { $and: [ { $ne: [ "$tapInTime", null ] }, { $ne: [ "$tapOutTime", null ] } ] },
//           { $subtract: [ "$tapOutTime", "$tapInTime" ] },
//           null // Handle cases where either tapInTime or tapOutTime is null
//         ]
//       }
//     }
//   },
//   {
//     $group: {
//       _id: {year: '$year', month: '$month'},
//       sumPerYearPerMonth: {
//         $sum: '$durationSeconds'
//       }
//     }
//   },
//   {
//     $group: {
//     	_id:null,
//     	averagePerYearPerMonth: {
//         $avg: '$sumPerYearPerMonth'
//       }
//     }
//   }
// ] -->


// <!-- analisis trend pelanggan -->
// <!-- y: count ride -->
// <!-- x: bulan -->
// <!-- grouping: harga (premium, regular)  -->

// <!-- PIE CHART -->
// <!-- rute yang sering digunakan perwaktu -->

// <!-- BAR CHART -->
// <!-- rata-rata durasi perjalanan per rute -->
// <!-- x: top 5 rutes favoritku --> 
// <!-- y: rata-rata durasi perjalanan ku per 5 rute favorit -->

// <!-- Analisis pengeluaran per bulan -->
// <!-- x: month -->
// <!-- y: uang yang dikeluarkan -->
// <!-- [
//   // Match transactions for the specified payCardID and tapInTime range
//   {
//     $match: {
//       payCardID: 5343809282239143, // Assuming payCardID is a string
//       // hourIn: { $gte: 0, $lte: 23 }, // Assuming hourIn represents the hour of the day for tapInTime
//       // $expr: {
//       //   $and: [
//       //     // Filter by tapInTime within the specified date range
//       //     { $gte: [ { $toDate: "$tapInTime" }, ISODate("2022-12-01T00:00:00Z")] },
//       //     { $lte: [ { $toDate: "$tapInTime" }, ISODate("2022-12-31T23:59:59Z")] }
//       //   ]
//       // }
//     }
//   },
//   // Group transactions by month and year, calculating the sum of payAmount for each group
//   {
//     $group: {
//       _id: { month: "$month"},
//       sumPayAmount: { $sum: "$payAmount" }
//     }
//   },
//   // Sort the results by month and year in ascending order
//   {
//     $sort: { "_id.month": 1 }
//   }
// ] -->

// <!-- geospatial -->
// <!-- list orang yang sering ditemui (corridor namenya sama, waktunya sama) -->

?>
