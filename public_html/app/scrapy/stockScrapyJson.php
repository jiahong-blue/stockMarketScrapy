<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockScrapy.class.php';
require_once $root . '/util/stockCurl.class.php';
require_once $root . '/util/stockPDO.class.php';

// 爬蟲物件
$scrapy = new StockScrapy(new StockCurl());
// PDO
$pdo = StockPDO::getInstance();

// opt參數
$opt = [
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36',
    CURLOPT_RETURNTRANSFER => true
];

// url列表
$urlList = [
    [
        'url' => 'https://www.twse.com.tw/exchangeReport/MI_MARGN',
        'param' => [
            'response' => 'json',
            'selectType' => 'MS'
        ],
        'isPost' => 0,
    ],
    [
        'url' => 'https://www.twse.com.tw/exchangeReport/MI_INDEX',
        'param' => [
            'response' => 'json'
        ],
        'isPost' => 0,
    ],
    [
        'url' => 'https://www.twse.com.tw/fund/BFI82U',
        'param' => [
            'response' => 'json',
            'dayDate' => '',
            'weekDate' => '',
            'monthDate' => '',
            'type' => 'day'
        ],
        'isPost' => 0,
    ],
];

// 設定curl參數
$scrapy->setCurlOptions($opt);
// 設定urlList
$scrapy->setUrlList($urlList);
// 開始爬
$data = $scrapy->runScrapy();

// https://www.twse.com.tw/exchangeReport/MI_MARGN
$res = json_decode($data['https://www.twse.com.tw/exchangeReport/MI_MARGN']['data'], true);
foreach ( $res['creditList'] as $k => $v ) {
    $pdo->insertData(
        'margin_transaction_summary', 
        [
            'item' => $k+1,
            'margin_purchase' => (int) str_replace(',', '', $v[1]),
            'margin_sale' => (int) str_replace(',', '', $v[2]),
            'cash_redemption' => (int) str_replace(',', '', $v[3]),
            'balance_previous_day' => (int) str_replace(',', '', $v[4]),
            'Balance_today' => (int) str_replace(',', '', $v[5])
        ]
    );
}

// https://www.twse.com.tw/exchangeReport/MI_INDEX
$res = json_decode($data['https://www.twse.com.tw/exchangeReport/MI_INDEX']['data'], true);
foreach ( $res['data8'] as $k => $v) {
    $pdo->insertData(
        'net_change_price',
        [
            'type' => $k+1,
            'overall_market' => $v[1],
            'stocks' => $v[2]
        ]
    );
}

foreach ( $res['data7'] as $k => $v ) {
    $pdo->insertData(
        'market_summary',
        [
            'summary' => $k+1,
            'trade_value' => (int) str_replace(',', '', $v[1]),
            'trade_volume' => (int) str_replace(',', '', $v[2]),
            'transaction' => (int) str_replace(',', '', $v[3])
        ]
    );
}

// https://www.twse.com.tw/fund/BFI82U
$res = json_decode($data['https://www.twse.com.tw/fund/BFI82U']['data'], true);
foreach ( $res['data'] as $k => $v ) {
    $pdo->insertData(
        'trade_value_foreign_other_investors',
        [
            'item' => $k+1,
            'total_buy' => (int) str_replace(',', '', $v[1]),
            'total_sell' => (int) str_replace(',', '', $v[2]),
            'difference'  => (int) str_replace(',', '', $v[3])
        ]
    );
}