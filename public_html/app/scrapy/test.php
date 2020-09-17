<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockScrapy.class.php';
require_once $root . '/util/stockCurl.class.php';
require_once $root . '/util/stockPDO.class.php';
require_once $root . '/util/stockHtmlParse.class.php';

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
        'url' => 'https://www.taifex.com.tw/cht/3/largeTraderOptQryTbl',
        'param' => [],
        'isPost' => 0,
    ]
];
// 設定curl參數
$scrapy->setCurlOptions($opt);
// 設定urlList
$scrapy->setUrlList($urlList);
// 開始爬
$data = $scrapy->runScrapy();

// html解析
$parse = new HTMLParse();

// tools
function processArrayToKeyValue($contractArray) {
    $res = [];
    foreach( $contractArray as $v) {
        $res[$v['contract']] = (int) $v['id'];
    }
    return $res;
}

function changeInt($value) {
	return (int) str_replace(',', '', $value);
}

/**
 * https://www.taifex.com.tw/cht/3/largeTraderOptQryTbl
 */
// 整理Html文本
$content = $data['https://www.taifex.com.tw/cht/3/largeTraderOptQryTbl']['data'];
$content = $parse->removeNewline($content);
$content = $parse->removeWhitespace($content);

$table = $parse->parseBlockElement($content, 'table');
$content = $parse->parseContent($table, 'table');
$table2 = $parse->parseBlockElement($content, 'table');

$tr = $parse->parseElement($table2, 'tr');

// 取得contract mapping
$contract = $pdo->queryData('SELECT * FROM large_trader_opt_contract');
$contractNoMap = processArrayToKeyValue($contract);

// 處理table文本並存到DB
$contractNo = 0;
foreach ($tr[0] as $k => $v) {
    // 前三筆不要
    if ( $k < 3 ) {
        continue;
    }
    // 取欄位資料
    $td = $parse->parseElement($v, 'td');
    $col = $parse->removeAlltag($td[1]);

    // 當column為11時表示進下一個商品，需重新取得契約
    if ( count($col) === 11 ) {
        if ( $contractNoMap[$col[0]] === null ) {
            // 新增資料
            $pdo->insertData('large_trader_opt_contract', ['contract' => $col[0]]);
            // 重新取得資料
            $contract = $pdo->queryData('SELECT * FROM large_trader_opt_contract');
            $contractNoMap = processArrayToKeyValue($contract);
        }
        $contractNo = $contractNoMap[$col[0]];

        // 當第一個欄位資料為'-',表示沒有資料，就不用紀錄了，跳
        if ( $col[1] === '-' ) {
            continue;
        }

        $pdo->insertData(
            'large_trader_opt',
            [
                'contract' => $contractNo,
                'contract_month' => $col[1],
                'buy_position_top5' => $col[2],
                'buy_position_top5_percent' => $col[3],
                'buy_position_top10' => $col[4],
                'buy_position_top10_percent' => $col[5],
                'sell_position_top5' => $col[6],
                'sell_position_top5_percent' => $col[7],
                'sell_position_top10' => $col[8],
                'sell_position_top10_percent' => $col[9],
                'oi__market' => $col[10]
            ]
        );
    } else {
        $pdo->insertData(
            'large_trader_opt',
            [
                'contract' => $contractNo,
                'contract_month' => $col[0],
                'buy_position_top5' => $col[1],
                'buy_position_top5_percent' => $col[2],
                'buy_position_top10' => $col[3],
                'buy_position_top10_percent' => $col[4],
                'sell_position_top5' => $col[5],
                'sell_position_top5_percent' => $col[6],
                'sell_position_top10' => $col[7],
                'sell_position_top10_percent' => $col[8],
                'oi__market' => $col[9]
            ]
        );
    }
}