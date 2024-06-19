<?php
// Connect to sql database
include 'mysql_connection.php';
include_once 'pelangganPipeline.php';
require '../vendor/autoload.php'; // Make sure Composer's autoload file is included
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if all required parameters are set
    if (isset($_GET['type']) && $_GET['type'] == 'card' && isset($_GET['id_pelanggan']) && isset($_GET['startdate']) && isset($_GET['enddate'])) {
        $id_pelanggan = $_GET['id_pelanggan'];
        $startdate = $_GET['startdate'];
        $enddate = $_GET['enddate'];
        $waktu = isset($_GET['range']) ? $_GET['range'] : 'bulan';

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
                        // Cari lama hari dari tanggal awal dan akhir
                        $date1 = new DateTime($startdate);
                        $date2 = new DateTime($enddate);
                        $diff = $date2->diff($date1)->format("%a");
                        $diff = intval($diff);
                        
                        // Connect to MongoDB
                        $client = new MongoClient('mongodb://localhost:27017');
                        $database = $client->selectDatabase('transjakarta');
                        $collection = $database->selectCollection('transaction3');

                        // AVERAGE PAY AMOUNT PER WAKTU
                        $pipeline = [];
                        $pipeline = getPipelineAveragePayAmount($waktu, $id_pelanggan, $startdate, $enddate);
                        $result2 = $collection->aggregate($pipeline)->toArray();
                        $result['averagePayAmountPerMonth'] = isset($result2[0]['averagePayAmountPerMonth']) ? $result2[0]['averagePayAmountPerMonth'] : 0;
                        
                        // AVERAGE JUMLAH TRANSAKSI PER WAKTU
                        $pipeline = [];
                        $pipeline = getPipelineJumlahTransaksi($waktu, $id_pelanggan, $startdate, $enddate);
                        $result2 = $collection->aggregate($pipeline)->toArray();
                        $result['averageJumlahTransaksi'] = isset($result2[0]['averageRidePerMonth']) ? $result2[0]['averageRidePerMonth'] : 0;

                        // AVERAGE DURATION PER WAKTU
                        $pipeline = [];
                        $pipeline = getPipelineAverageDuration($waktu, $id_pelanggan, $startdate, $enddate);
                        $result2 = $collection->aggregate($pipeline)->toArray();
                        $result['averageDuration'] = isset($result2[0]['averagePerYearPerMonth']) ? $result2[0]['averagePerYearPerMonth'] : 0;
                                                
                        $result['waktu'] = $waktu;

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
    } 
    else if(isset($_GET['type']) && $_GET['type'] == '#1'){
        $result = [];
        $year = $_GET['year'];
        // Connect to MongoDB
        $client = new MongoClient('mongodb://localhost:27017');
        $database = $client->selectDatabase('transjakarta');
        $collection = $database->selectCollection('transaction3');

        // Min Transaksi per bulan
        $pipeline = getMinTransaksi($year);
        $result1 = $collection->aggregate($pipeline)->toArray();
        $result['min'] = isset($result1[0]['minCountTransaksi']) ? $result1[0]['minCountTransaksi'] : 0;

        // Max Transaksi per bulan
        $pipeline = getMaxTransaksi($year);
        $result1 = $collection->aggregate($pipeline)->toArray();
        $result['max'] = isset($result1[0]['maxCountTransaksi']) ? $result1[0]['maxCountTransaksi'] : 0;

        // Avg Transaksi per bulan
        $pipeline = getAvgTransaksi($year);
        $result1 = $collection->aggregate($pipeline)->toArray();
        $result['avg'] = isset($result1[0]['avgCountTransaksi']) ? $result1[0]['avgCountTransaksi'] : 0;
    
        // Avg Transaksi per bulan
        $pipeline = getClassTransaction($result['avg'], $result['max'], $year);
        $result1 = $collection->aggregate($pipeline);
        $labels = [];
        $dataOfLabels = [];
        foreach ($result1 as $document) {
            $id = (string) $document->_id; 
            $countTransaksi = $document->countTransaksi;

            $labels[] = $id;
            $dataOfLabels[] = $countTransaksi;
        }

        $response = [
            'min' => $result['min'],
            'max' => $result['max'],
            'avg' => $result['avg'],
            'analisis' => [
                'labels' => $labels,
                'data' => $dataOfLabels
            ]
        ];
        echo json_encode($response);
    }
    else if (isset($_GET['type']) && $_GET['type'] == '#2'){
        $year = $_GET['year'];

        // Connect to MongoDB
        $client = new MongoClient('mongodb://localhost:27017');
        $database = $client->selectDatabase('transjakarta');
        $collection = $database->selectCollection('transaction3');

        // Distribusi Premium dan Regular per bulan
        $pipeline = getPremRegDist($year);
        $result1 = $collection->aggregate($pipeline);
        $month = [];
        $premium = [];
        $regular = [];
        foreach ($result1 as $document) {
            $month[] = (string) $document->_id->month; 
            if($document->_id->busType == 'Premium'){
                $premium[] = $document->countTransaksi;
            }
            else{
                $regular[] = $document->countTransaksi;
            }
        }

        $response = [
            'month' => $month,
            'premium' => $premium,
            'regular' => $regular,
        ];
        echo json_encode($response);
    }
    else if(isset($_GET['type']) && $_GET['type'] == '#3'){
        $year = $_GET['year'];

        // Connect to MongoDB
        $client = new MongoClient('mongodb://localhost:27017');
        $database = $client->selectDatabase('transjakarta');
        $collection = $database->selectCollection('transaction3');

        // Distribusi Premium dan Regular per bulan
        $pipeline = getAgePremRegDist($year);
        $result1 = $collection->aggregate($pipeline);
        $hasilPremium = [
            '0-17' => 0,
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '55+' => 0,
        ];
        $hasilRegular = [
            '0-17' => 0,
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '55+' => 0,
        ];

        foreach ($result1 as $document) {
            $age = $document->_id->ageGroup;
            if($document->_id->busType == 'Premium'){
                $hasilPremium[$age] = $document->countTransaksi;
            }
            else{
                $hasilRegular[$age] = $document->countTransaksi;
            }
        }

        $response = [
            'premium' => array_values($hasilPremium),
            'regular' => array_values($hasilRegular),
        ];
        echo json_encode($response);
    }
    else if(isset($_GET['type']) && $_GET['type'] == '#4'){
        $year = $_GET['year'];
        $hour = $_GET['hour'];
        $jenisKelamin = $_GET['jenisKelamin'];

        // Connect to MongoDB
        $client = new MongoClient('mongodb://localhost:27017');
        $database = $client->selectDatabase('transjakarta');
        $collection = $database->selectCollection('transaction3');

        // DestinasiPopuler
        $pipeline = getDestinasiPopuler($year,$hour, $jenisKelamin);
        $result1 = $collection->aggregate($pipeline);
        $hasil = [];
        foreach ($result1 as $document) {
            $hasil[] = [
                'latitude' =>  floatval($document->_id->tapOutStopsLat),
                'longitude' =>  floatval($document->_id->tapOutStopsLon),
                'fillOpacity' => floatval($document->countTransaksi) * 0.05,
                'radius' =>  floatval($document->countTransaksi) * 50,
            ];
        }

        echo json_encode($hasil);
    }
    else if (isset($_GET['type']) && $_GET['type'] == '#5'){
        $client = new MongoClient('mongodb://localhost:27017');
        $database = $client->selectDatabase('transjakarta');
        $collection = $database->selectCollection('transaction3');
        $year = $_GET['year'];
        $pipeline =  [
            [
                '$addFields' => [
                    'tapInTime' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ]
            ],
            [
                '$match' => [
                    'year' => intval($year)
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'paycard',
                    'localField' => 'payCardID',
                    'foreignField' => 'payCardID',
                    'as' => 'payCard'
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => '$tapInTime'],
                    'month' => ['$month' => '$tapInTime'],
                    'payAmount' => 1,
                    'age' => ['$arrayElemAt' => ['$payCard.age', 0]],
                    'hour' => ['$hour' => '$tapInTime']
                ]
            ],
            [
                '$match' => [
                    'hour' => ['$ne' => null]
                ]
            ],
            [
                '$group' => [
                    '_id' => [
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
                        ],
                        'hour' => '$hour'
                    ],
                    'count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => [
                    '_id.ageGroup' => 1,
                    '_id.hour' => 1
                ]
            ]
        ];
        

        $results = $collection->aggregate($pipeline);

        $data = [];
        foreach ($results as $result) {
            $hour = $result->_id['hour'];
            $ageGroup = $result->_id['ageGroup'];
            $count = $result->count;

            if (!isset($data[$ageGroup])) {
                $data[$ageGroup] = array_fill(0, 24, 0);
            }
            $data[$ageGroup][$hour] = $count;
        }

        echo json_encode($data);
    }
    else if (isset($_GET['type']) && $_GET['type'] == '#6'){
        // Connect to MongoDB
        $client = new MongoClient('mongodb://localhost:27017');
        $collection = $client->transjakarta->paycard;
        $pipeline = getGroupByJenisKelaminAge();
        $result1 = $collection->aggregate($pipeline);
        $cewek = [
            '0-17' => 0,
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '55+' => 0,
        ];
        $cowok = [
            '0-17' => 0,
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '55+' => 0,
        ];
        foreach ($result1 as $document) {
            if ($document->_id->payCardSex == 'F')
                $cewek[$document->_id->ageGroup] = $document->count;
            else
                $cowok[$document->_id->ageGroup] = $document->count;
        }
        $hasil = [
            'cewek' => $cewek,
            'cowok' => $cowok
        ];
        echo json_encode($hasil);
    }
    else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
?>
