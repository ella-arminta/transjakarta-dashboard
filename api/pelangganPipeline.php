<?php
function getPipelineAveragePayAmount($waktu, $id_pelanggan, $startdate, $enddate){
    $pipeline = [];
    if ($waktu == 'bulan'){
        $pipeline = [
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan),
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' =>  $startdate . ' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'payAmount' => 1
                ]
            ],
            [
                '$group' => [
                    '_id' => ['month' => '$month', 'year' => '$year'],
                    'sumPayAmount' => ['$sum' => '$payAmount']
                ]
            ],
            [
                '$sort' => ['_id.year' => 1, '_id.month' => 1]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'averagePayAmountPerMonth' => ['$avg' => '$sumPayAmount']
                ]
            ]
        ];
    }else if ($waktu == 'tahun'){
        $pipeline = [
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan),
                    // '$expr' => [
                    //     '$and' => [
                    //         ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $startdate .' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                    //         ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                    //     ]
                    // ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'payAmount' => 1
                ]
            ],
            [
                '$group' => [
                    '_id' => ['year' => '$year'],
                    'sumPayAmount' => ['$sum' => '$payAmount']
                ]
            ],
            [
                '$sort' => ['_id.year' => 1]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'averagePayAmountPerMonth' => ['$avg' => '$sumPayAmount']
                ]
            ]
        ];                            
    }else if($waktu == 'minggu'){
        $pipeline = [
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan),
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $startdate .' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'week' => ['$week' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'payAmount' => 1
                ]
            ],
            [
                '$group' => [
                    '_id' => ['week' => '$week', 'year' => '$year'],
                    'sumPayAmount' => ['$sum' => '$payAmount']
                ]
            ],
            [
                '$sort' => ['_id.year' => 1, '_id.week' => 1]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'averagePayAmountPerMonth' => ['$avg' => '$sumPayAmount']
                ]
            ]
        ];
    }else if($waktu == 'hari'){
        $pipeline = [
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan),
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' =>  $startdate . ' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'day' => ['$dayOfMonth' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'payAmount' => 1
                ]
            ],
            [
                '$group' => [
                    '_id' => ['day' => '$day', 'month' => '$month', 'year' => '$year'],
                    'sumPayAmount' => ['$sum' => '$payAmount']
                ]
            ],
            [
                '$sort' => ['_id.year' => 1, '_id.month' => 1, '_id.day' => 1]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'averagePayAmountPerMonth' => ['$avg' => '$sumPayAmount']
                ]
            ]
        ];
    }

    return $pipeline;
}

function getPipelineJumlahTransaksi($waktu, $id_pelanggan, $startdate, $enddate){
    $pipeline = [];
    if ($waktu == 'bulan'){
        $pipeline = [
            // Match transactions for the specified payCardID and tapInTime range
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan), // Assuming payCardID is a string
                    // 'hourIn' => [ '$gte' => 0, '$lte' => 23 ], // Assuming hourIn represents the hour of the day for tapInTime
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' =>  $startdate . ' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'day' => ['$dayOfMonth' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                ]
            ],
            // Group transactions by month and year, calculating the sum of payAmount for each group
            [
                '$group' => [
                    '_id' => [ 'month' => '$month', 'year' => '$year' ],
                    'countRide' => [ '$sum' => 1 ]
                ]
            ],
            // Sort the results by month and year in ascending order
            [
                '$sort' => [ '_id.year' => 1, '_id.month' => 1 ]
            ],
            // Group to calculate the average ride per month
            [
                '$group' => [
                    '_id' => null,
                    'averageRidePerMonth' => [ '$avg' => '$countRide' ]
                ]
            ]
        ];
    }else if($waktu == 'tahun'){
        $pipeline = [
            // Match transactions for the specified payCardID and tapInTime range
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan), // Assuming payCardID is a string
                    // 'hourIn' => [ '$gte' => 0, '$lte' => 23 ], // Assuming hourIn represents the hour of the day for tapInTime
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' =>  $startdate . ' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'day' => ['$dayOfMonth' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                ]
            ],
            // Group transactions by month and year, calculating the sum of payAmount for each group
            [
                '$group' => [
                    '_id' => [ 'year' => '$year' ],
                    'countRide' => [ '$sum' => 1 ]
                ]
            ],
            // Sort the results by month and year in ascending order
            [
                '$sort' => [ '_id.year' => 1]
            ],
            // Group to calculate the average ride per month
            [
                '$group' => [
                    '_id' => null,
                    'averageRidePerMonth' => [ '$avg' => '$countRide' ]
                ]
            ]
        ];
    }
    else if($waktu == 'minggu'){
        $pipeline = [
            // Match transactions for the specified payCardID and tapInTime range
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan), // Assuming payCardID is a string
                    // 'hourIn' => [ '$gte' => 0, '$lte' => 23 ], // Assuming hourIn represents the hour of the day for tapInTime
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' =>  $startdate . ' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'week' => ['$week' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                ]
            ],
            // Group transactions by month and year, calculating the sum of payAmount for each group
            [
                '$group' => [
                    '_id' => [ 'week' => '$week', 'year' => '$year' ],
                    'countRide' => [ '$sum' => 1 ]
                ]
            ],
            // Sort the results by month and year in ascending order
            [
                '$sort' => [ '_id.year' => 1, '_id.week' => 1 ]
            ],
            // Group to calculate the average ride per month
            [
                '$group' => [
                    '_id' => null,
                    'averageRidePerMonth' => [ '$avg' => '$countRide' ]
                ]
            ] 
        ];
    }else if($waktu == 'hari'){
        $pipeline = [
            // Match transactions for the specified payCardID and tapInTime range
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan), // Assuming payCardID is a string
                    // 'hourIn' => [ '$gte' => 0, '$lte' => 23 ], // Assuming hourIn represents the hour of the day for tapInTime
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' =>  $startdate . ' 00:00:00', 'format' => '%Y-%m-%d %H:%M:%S']]]],
                            ['$lte' => [ ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']], ['$dateFromString' => ['dateString' => $enddate . ' 23:59:59', 'format' => '%Y-%m-%d %H:%M:%S']]]]
                        ]
                    ]
                ]
            ],
            [
                '$project' => [
                    'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                    'day' => ['$dayOfMonth' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                ]
            ],
            // Group transactions by month and year, calculating the sum of payAmount for each group
            [
                '$group' => [
                    '_id' => [ 'day' => '$day', 'month' => '$month', 'year' => '$year' ],
                    'countRide' => [ '$sum' => 1 ]
                ]
            ],
            // Sort the results by month and year in ascending order
            [
                '$sort' => [ '_id.year' => 1, '_id.month' => 1, '_id.day' => 1 ]
            ],
            // Group to calculate the average ride per month
            [
                '$group' => [
                    '_id' => null,
                    'averageRidePerMonth' => [ '$avg' => '$countRide' ]
                ]
            ]
        ];
    }
    return  $pipeline;
}

function getPipelineAverageDuration($waktu, $id_pelanggan, $startdate, $enddate){
    $pipeline = [];
    if ($waktu == 'bulan'){
        $pipeline = [
            // Add fields to convert tapInTime and tapOutTime to Date objects
            [
                '$addFields' => [
                    'tapInTime' => ['$toDate' => '$tapInTime'],
                    'tapOutTime' => ['$toDate' => '$tapOutTime']
                ]
            ],
            // Match transactions for the specified payCardID and tapInTime range
            [
                '$match' => [
                    'payCardID' => intval($id_pelanggan), // Assuming payCardID is an integer
                    'tapInTime' => [
                        '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($startdate.'T00:00:00Z') * 1000),
                        '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($enddate.'T23:59:59Z') * 1000)
                    ]
                ]
            ],
            // Project the necessary fields and calculate the duration in seconds
            [
                '$project' => [
                    'year' => ['$year' => '$tapInTime'],
                    'month' => ['$month' => '$tapInTime'],
                    'tapInTime' => 1,
                    'durationSeconds' => [
                        '$cond' => [
                            [
                                '$and' => [
                                    ['$ne' => ['$tapInTime', null]],
                                    ['$ne' => ['$tapOutTime', null]]
                                ]
                            ],
                            ['$subtract' => ['$tapOutTime', '$tapInTime']],
                            null // Handle cases where either tapInTime or tapOutTime is null
                        ]
                    ]
                ]
            ],
            // Group by year and month, calculating the sum of durationSeconds for each group
            [
                '$group' => [
                    '_id' => ['year' => '$year', 'month' => '$month'],
                    'sumPerYearPerMonth' => ['$sum' => '$durationSeconds']
                ]
            ],
            // Group to calculate the average sum per year per month
            [
                '$group' => [
                    '_id' => null,
                    'averagePerYearPerMonth' => ['$avg' => '$sumPerYearPerMonth']
                ]
            ]
        ];        
    }else if($waktu == 'tahun'){
        
    }
    return $pipeline;
}
#1
function getMinTransaksi($year){
    $pipeline = [
        [
            '$match' => [
                'year' => intval($year)
            ]
        ],
        [
            '$project' => [
                'year' => [
                    '$year' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'month' => [
                    '$month' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'payCardID' => 1
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'month' => '$month',
                    'year' => '$year',
                    'payCardID' => '$payCardID'
                ],
                'countTransaksi' => [
                    '$sum' => 1
                ]
            ]
        ],
        [
            '$group' => [
                '_id' => '$_id.year',
                'minCountTransaksi' => [
                    '$min' => '$countTransaksi'
                ]
            ]
        ],
        [
            '$project' => [
                'year' => '$_id',
                'minCountTransaksi' => 1
            ]
        ]
    ];   
    return $pipeline; 
}
function getMaxTransaksi($year){
    $pipeline = [
        [
            '$match' => [
                'year' => intval($year)
            ]
        ],
        [
            '$project' => [
                'year' => [
                    '$year' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'month' => [
                    '$month' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'payCardID' => 1
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'month' => '$month',
                    'year' => '$year',
                    'payCardID' => '$payCardID'
                ],
                'countTransaksi' => [
                    '$sum' => 1
                ]
            ]
        ],
        [
            '$group' => [
                '_id' => '$_id.year',
                'maxCountTransaksi' => [
                    '$max' => '$countTransaksi'
                ]
            ]
        ],
        [
            '$project' => [
                'year' => '$_id',
                'maxCountTransaksi' => 1
            ]
        ]
    ];   
    return $pipeline; 
}
function getAvgTransaksi($year){
    $pipeline = [
        [
            '$match' => [
                'year' => intval($year)
            ]
        ],
        [
            '$project' => [
                'year' => [
                    '$year' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'month' => [
                    '$month' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'payCardID' => 1,
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'month' => '$month',
                    'year' => '$year',
                    'payCardID' => '$payCardID'
                ],
                'countTransaksi' => [
                    '$sum' => 1
                ]
            ]
        ],
        [
            '$group' => [
                '_id' => '$_id.year',
                'avgCountTransaksi' => [
                    '$avg' => '$countTransaksi'
                ]
            ]
        ],
        [
            '$project' => [
                'year' => '$_id',
                'avgCountTransaksi' => 1
            ]
        ]
    ];   
    return $pipeline; 
}
function getClassTransaction($avg,$max,$year){
    $avg = ceil($avg);
    $max = ceil($max);
    $pipeline = $pipeline = [
        [
            '$match' => [
                'year' => intval($year)  // Convert $year to integer if needed
            ]
        ],
        [
            '$project' => [
                'year' => [
                    '$year' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'month' => [
                    '$month' => [
                        '$dateFromString' => [
                            'dateString' => '$tapInTime',
                            'format' => '%Y-%m-%d %H:%M:%S'
                        ]
                    ]
                ],
                'payCardID' => 1
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'month' => '$month',
                    'year' => '$year',
                    'payCardID' => '$payCardID'
                ],
                'countTransaksi' => [
                    '$sum' => 1
                ]
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    '$switch' => [
                        'branches' => [
                            [
                                'case' => [
                                    '$lt' => ['$countTransaksi', $avg]
                                ],
                                'then' => '0 - '. (string) ($avg- 1) . ' (jarang)'
                            ],
                            [
                                'case' => [
                                    '$and' => [
                                        ['$gte' => ['$countTransaksi', $avg]],
                                        ['$lt' => ['$countTransaksi', $max]]
                                    ]
                                ],
                                'then' => [
                                    '$concat' => [
                                        ['$toString' => $avg],
                                        ' - ',
                                        ['$toString' => ['$subtract' => [$max, 1]]],
                                        ' (sedang)'
                                    ]
                                ]
                            ],
                            [
                                'case' => [
                                    '$gte' => ['$countTransaksi', $max]
                                ],
                                'then' => '> '. (string) $max . ' (sering)'
                            ]
                        ],
                        'default' => 'Unknown'
                    ]
                ],
                'countTransaksi' => [
                    '$sum' => 1
                ]
            ]
        ]
    ];    
    return $pipeline;
}

// #2
function getPremRegDist($year){
    $pipeline = [
        ['$match' => ['year' => intval($year)]],
        [
            '$project' => [
                'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                'payAmount' => 1
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'month' => '$month',
                    'year' => '$year',
                    'busType' => [
                        '$switch' => [
                            'branches' => [
                                ['case' => ['$lte' => ['$payAmount', 3500]], 'then' => 'Regular'],
                                ['case' => ['$gt' => ['$payAmount', 3500]], 'then' => 'Premium']
                            ],
                            'default' => 'Unknown'
                        ]
                    ]
                ],
                'countTransaksi' => ['$sum' => 1]
            ]
        ],
        [
            '$sort' => ['_id.month' => 1]
        ]
    ];
    return $pipeline;
}
// #3 
function getAgePremRegDist($year){
    $pipeline = [
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
                'year' => ['$year' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                'month' => ['$month' => ['$dateFromString' => ['dateString' => '$tapInTime', 'format' => '%Y-%m-%d %H:%M:%S']]],
                'payAmount' => 1,
                'age' => ['$arrayElemAt' => ['$payCard.age', 0]]  // Retrieve the first element of the array
            ]
        ],
        [
            '$group' => [
                '_id' => [
                    'month' => '$month',
                    'year' => '$year',
                    'busType' => [
                        '$switch' => [
                            'branches' => [
                                ['case' => ['$lte' => ['$payAmount', 3500]], 'then' => 'Regular'],
                                ['case' => ['$gt' => ['$payAmount', 3500]], 'then' => 'Premium']
                            ],
                            'default' => 'Unknown'
                        ]
                    ],
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
                'countTransaksi' => ['$sum' => 1]
            ]
        ],
        [
            '$sort' => [
                '_id.month' => 1,
                '_id.ageGroup' => 1
            ]
        ]
    ];    
    return $pipeline;
}
// #4 
function getDestinasiPopuler($year, $hour, $jenisKelamin) {
    $hour = intval($hour);
    $pipeline = [];
    if($jenisKelamin != 'semua') {
        $pipeline = [
            [
                '$lookup' => [
                    'from' => 'paycard',
                    'localField' => 'payCardID',
                    'foreignField' => 'payCardID',
                    'as' => 'payCard'
                ]
            ],
            [
                '$unwind' => '$payCard'
            ],
            [
                '$match' => [
                    'year' => intval($year),
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$hour' => ['$toDate' => '$tapOutTime']], $hour]],
                            ['$lt' => [ ['$hour' => ['$toDate' => '$tapOutTime']], $hour + 1]]
                        ]
                    ],
                    'payCard.payCardSex' => $jenisKelamin
                ]
            ]
        ];
    } else {
        $pipeline[] = 
            [
                '$match' => [
                    'year' => intval($year),
                    '$expr' => [
                        '$and' => [
                            ['$gte' => [ ['$hour' => ['$toDate' => '$tapOutTime']], $hour]],
                            ['$lt' => [ ['$hour' => ['$toDate' => '$tapOutTime']], $hour + 1]]
                        ]
                    ]
                ]
            ];
    }
    
    $pipeline = array_merge($pipeline, [
        [
            '$group' => [
                '_id' => [
                    'hour' => ['$hour' => ['$toDate' => '$tapOutTime']],
                    'tapOutStops' => '$tapOutStops',
                    'tapOutStopsLat' => '$tapOutStopsLat',
                    'tapOutStopsLon' => '$tapOutStopsLon'
                ],
                'countTransaksi' => ['$sum' => 1]
            ]
        ],
        [
            '$project' => [
                '_id' => 1,
                'countTransaksi' => 1
            ]
        ],
        [
            '$sort' => ['countTransaksi' => -1]
        ],
        [
            '$limit' => 30
        ]
    ]);
    return $pipeline;
}
?>