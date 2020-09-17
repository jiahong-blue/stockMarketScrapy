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
    ],
    [
        'url' => 'https://www.taifex.com.tw/cht/3/futDailyMarketExcel',
        'param' => [],
        'isPost' => 0,
    ],
    [
        'url' => 'https://www.taifex.com.tw/cht/3/futContractsDateExcel',
        'param' => [],
        'isPost' => 0,
    ],
    [
        'url' => 'https://www.taifex.com.tw/cht/3/callsAndPutsDateExcel',
        'param' => [],
        'isPost' => 0,
    ],
    [
        'url' => 'https://www.taifex.com.tw/cht/3/largeTraderFutQryTbl',
        'param' => [],
        'isPost' => 0,
    ],
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

/**
 * https://www.taifex.com.tw/cht/3/futDailyMarketExcel
 */
// 整理Html文本
$content = $data['https://www.taifex.com.tw/cht/3/futDailyMarketExcel']['data'];
$content = $parse->removeNewline($content);
$table = $parse->parseBlockElement($content, 'table');
$tableList = $parse->parseElement($table, 'table')[0];
// 清空白
$tableList = $parse->removeWhitespace($tableList);
// 臺股期貨  ( TX ) 行情表
$tr = $parse->parseElement($tableList[1], 'tr');

$contract = $pdo->queryData('SELECT * FROM daily_market_report_contract');
$contractNoMap = processArrayToKeyValue($contract);
$contractNo = 0;
$last = count($tr[0]) - 1;
foreach ( $tr[0] as $k => $v ) {
	// 第0筆沒資料和最後不要
	if ( $k === 0 || $k === $last) {
		continue;
	}
    // 此文本td有的用大寫，並清tag
	$td = $parse->parseElement($v, 'td')[0];
	$td = $parse->removeAlltag($td);

	if ( $contractNoMap[$td[0]] === null) {
		// 新增資料
		$pdo->insertData('daily_market_report_contract', ['contract' => $td[0]]);
		// 重新讀取
		$contract = $pdo->queryData('SELECT * FROM daily_market_report_contract');
		$contractNoMap = processArrayToKeyValue($contract);
	}
	$contractNo = $contractNoMap[$td[0]];

	$pdo->insertData(
		'daily_market_report',
		[
			'contract' => $contractNo,
			'contract_month' => $td[1],
			'open' => (int) $td[2],
			'high' => (int) $td[3],
			'low' => (int) $td[4],
			'last' => (int) $td[5],
			'change' => $td[6],
			'percent' => $td[7],
			'volume_after_hours' => (int) $td[8],
			'volume_regular' => (int) $td[9],
			'volume_total' => (int) $td[10],
			'settlement_price' => (int) $td[11],
			'open_interest' => (int) $td[12],
			'best_bid' => (int) $td[13],
			'best_ask' => (int) $td[14],
			'Historical_high' => (int) $td[15],
			'Historical_low' => (int) $td[16]
		]
	);
}

/**
 * https://www.taifex.com.tw/cht/3/futContractsDateExcel
 */
// 整理文本
$content = $data['https://www.taifex.com.tw/cht/3/futContractsDateExcel']['data'];
$content = $parse->removeNewline($content);
// table內的table
$table = $parse->parseBlockElement($content, 'table');
$tableInnerContent = $parse->parseContent($table);
$table = $parse->parseBlockElement($tableInnerContent, 'table');
$table = $parse->removeWhitespace($table);

$tr = $parse->parseElement($table, 'tr')[0];
$count = count($tr);
$last = $count - 4;

// 處理table內資料並存到DB
$contractMap = processArrayToKeyValue(
	$pdo->queryData('SELECT * FROM futures_contracts_details_contract')
);
$contractNo = 0;
foreach ( $tr as $k => $v ) {
	// 前三筆不要
	if ( $k < 3 ) {
		continue;
	}

	// 47筆後面不要
	if ( $k >= $last ) {
	    break;
	}

	$td = $parse->parseElement($v, 'td')[0];
	$col = $parse->removeAlltag($td);

	// 每三筆一次循環，因key不存在，新增並重新取得contractMap
	$check = $k%3 === 0 && $contractMap[$col[1]] === null;
	if ( $check ) {

		$pdo->insertData(
			'futures_contracts_details_contract', 
			[ 'contract' => $col[1] ]
		);
		$contractMap = processArrayToKeyValue(
			$pdo->queryData('SELECT * FROM futures_contracts_details_contract')
        );
	}

	if ( $k%3 === 0 ) {
        $contractNo = $contractMap[$col[1]];
		$pdo->insertData(
			'futures_contracts_details',
			[
				'contract' => $contractNo,
				'item' => $col[2],
				'tv_long_volume' => changeInt($col[3]),
				'tv_long_value' => changeInt($col[4]),
				'tv_short_volume' => changeInt($col[5]),
				'tv_short_value' => changeInt($col[6]),
				'tv_net_volume' => changeInt($col[7]),
				'tv_net_value' => changeInt($col[8]),
				'cv_long_volume' => changeInt($col[9]),
				'cv_long_value' => changeInt($col[10]),
				'cv_short_volume' => changeInt($col[11]),
				'cv_short_value' => changeInt($col[12]),
				'cv_net_volume' => changeInt($col[13]),
				'cv_net_value' => changeInt($col[14])
			]
		);

	} else {
		$pdo->insertData(
			'futures_contracts_details',
			[
				'contract' => $contractNo,
				'item' => $col[0],
				'tv_long_volume' => changeInt($col[1]),
				'tv_long_value' => changeInt($col[2]),
				'tv_short_volume' => changeInt($col[3]),
				'tv_short_value' => changeInt($col[4]),
				'tv_net_volume' => changeInt($col[5]),
				'tv_net_value' => changeInt($col[6]),
				'cv_long_volume' => changeInt($col[7]),
				'cv_long_value' => changeInt($col[8]),
				'cv_short_volume' => changeInt($col[9]),
				'cv_short_value' => changeInt($col[10]),
				'cv_net_volume' => changeInt($col[11]),
				'cv_net_value' => changeInt($col[12])
			]
		);

	}
}

/**
 * https://www.taifex.com.tw/cht/3/callsAndPutsDateExcel 
 */

// 處理文本 table內包table
$content = $data['https://www.taifex.com.tw/cht/3/callsAndPutsDateExcel']['data'];
$content = $parse->removeNewline($content);
$content = $parse->parseBlockElement($content, 'table');
$innerContent = $parse->parseContent($content);
$table = $parse->parseBlockElement($innerContent, 'table');

// 解析table並存到db
$tr = $parse->parseElement($table, 'tr');
$trRmSpace = $parse->removeWhitespace($tr[0]);

$contractMap = processArrayToKeyValue(
	$pdo->queryData('SELECT * FROM calls_puts_details_contract')
);
$constract = 0;
$callOrPut = '';
foreach ( $trRmSpace as $k => $v ) {
	// 前三筆跳過
	if ( $k < 3 ) {
		continue;
	}
	
	// 處理欄位資料
	$td = $parse->parseElement($v, 'td')[0];
	$col = $parse->removeAllTag($td);

	$check = ($k-3)%6;

	switch ( $check ) {
		case 0:
			if ( $contractMap[$col[1]] === null ) {
				$pdo->insertData(
					'calls_puts_details_contract',
					[ 'contract' => $col[1] ]
				);
				$contractMap = processArrayToKeyValue(
					$pdo->queryData('SELECT * FROM calls_puts_details_contract')
				);
            }

			$contract = $contractMap[$col[1]];
            $callOrPut = $col[2];

			$pdo->insertData(
				'calls_puts_details',
			    [
                    'contract' => $contract,
                    'call_put'  => $callOrPut,
                    'item' => $col[3],
                    'tv_long_volume' => changeInt($col[4]),
                    'tv_long_value' => changeInt($col[5]),
                    'tv_short_volume' => changeInt($col[6]),
                    'tv_short_value' => changeInt($col[7]),
                    'tv_net_volume' => changeInt($col[8]),
                    'tv_net_value' => changeInt($col[9]),
                    'cv_long_volume' => changeInt($col[10]),
                    'cv_long_value' => changeInt($col[11]),
                    'cv_short_volume' => changeInt($col[12]),
                    'cv_short_value' => changeInt($col[13]),
                    'cv_net_volume' => changeInt($col[14]),
                    'cv_net_value' => changeInt($col[15])
				]
			);
			break;

		case 3:
			$callOrPut = $col[0];
			$pdo->insertData(
				'calls_puts_details',
				[
                    'contract' => $contract,
                    'call_put'  => $callOrPut,
                    'item' => $col[1],
                    'tv_long_volume' => changeInt($col[2]),
                    'tv_long_value' => changeInt($col[3]),
                    'tv_short_volume' => changeInt($col[4]),
                    'tv_short_value' => changeInt($col[5]),
                    'tv_net_volume' => changeInt($col[6]),
                    'tv_net_value' => changeInt($col[7]),
                    'cv_long_volume' => changeInt($col[8]),
                    'cv_long_value' => changeInt($col[9]),
                    'cv_short_volume' => changeInt($col[10]),
                    'cv_short_value' => changeInt($col[11]),
                    'cv_net_volume' => changeInt($col[12]),
                    'cv_net_value' => changeInt($col[13])
				]
			);
            break;
            
		default:
            $pdo->insertData(
                'calls_puts_details',
                [
                    'contract' => $contract,
                    'call_put'  => $callOrPut,
                    'item' => $col[0],
                    'tv_long_volume' => changeInt($col[1]),
                    'tv_long_value' => changeInt($col[2]),
                    'tv_short_volume' => changeInt($col[3]),
                    'tv_short_value' => changeInt($col[4]),
                    'tv_net_volume' => changeInt($col[5]),
                    'tv_net_value' => changeInt($col[6]),
                    'cv_long_volume' => changeInt($col[7]),
                    'cv_long_value' => changeInt($col[8]),
                    'cv_short_volume' => changeInt($col[9]),
                    'cv_short_value' => changeInt($col[10]),
                    'cv_net_volume' => changeInt($col[11]),
                    'cv_net_value' => changeInt($col[12])
                ]
            );
			break;
	}
}


/**
 * https://www.taifex.com.tw/cht/3/largeTraderFutQryTbl
 */
$content = $data['https://www.taifex.com.tw/cht/3/largeTraderFutQryTbl']['data'];
$content = $parse->removeNewline($content);
$table = $parse->parseBlockElement($content, 'table');
$innerContent = $parse->parseContent($table);
$table = $parse->parseBlockElement($innerContent, 'table');

// 處理table內資料並存到DB
$tr = $parse->parseElement($table, 'tr');
$trRmSpace = $parse->removeWhitespace($tr[0]);
$contractMap = processArrayToKeyValue(
    $pdo->queryData('SELECT * FROM open_interest_large_traders_contract')
);
$contractNo = 0;
foreach ( $trRmSpace as $k => $v) {
    if ( $k < 3 ) {
        continue;
    }

    $td = $parse->parseElement($v, 'td');
    $col = $parse->removeAllTag($td[0]);

    // 只有臺股期貨和其他不同要另外處理
    if ( $k < 6 ) {
        if ( $k === 3 ) {
            if ( $contractMap[$col[0]] === null ) {
                $pdo->insertData(
                    'open_interest_large_traders_contract',
                    ['contract' => $col[0]]
                );
                $contractMap = processArrayToKeyValue(
                    $pdo->queryData('SELECT * FROM open_interest_large_traders_contract')
                );
            }
            $contractNo = $contractMap[$col[0]];
            continue;
        }

        $pdo->insertData(
            'open_interest_large_traders',
            [
                'contract' => $contractNo,
                'contract_month' => $col[0],
                'buy_top5_oi' => $col[1],
                'buy_top5_percent' => $col[2],
                'buy_top10_oi' => $col[3],
                'buy_top10_percent' => $col[4],
                'sell_top5_oi' => $col[5],
                'sell_top5_percent' => $col[6],
                'sell_top10_oi' => $col[7],
                'sell_top10_percent' => $col[8],
                'market_oi' => changeInt($col[9])
            ]
        );
        
        continue;
    }

    $check = $k%2;

    // 其餘資料
    if ( $check === 0 ) {
        if ( $contractMap[$col[0]] === null ) {
            $pdo->insertData(
                'open_interest_large_traders_contract',
                ['contract' => $col[0]]
            );
            $contractMap = processArrayToKeyValue(
                $pdo->queryData('SELECT * FROM open_interest_large_traders_contract')
            );
        }
        $contractNo = $contractMap[$col[0]];

        $pdo->insertData(
            'open_interest_large_traders',
            [
                'contract' => $contractNo,
                'contract_month' => $col[1],
                'buy_top5_oi' => $col[2],
                'buy_top5_percent' => $col[3],
                'buy_top10_oi' => $col[4],
                'buy_top10_percent' => $col[5],
                'sell_top5_oi' => $col[6],
                'sell_top5_percent' => $col[7],
                'sell_top10_oi' => $col[8],
                'sell_top10_percent' => $col[9],
                'market_oi' => changeInt($col[10])
            ]
        );

    } else {
        $pdo->insertData(
            'open_interest_large_traders',
            [
                'contract' => $contractNo,
                'contract_month' => $col[0],
                'buy_top5_oi' => $col[1],
                'buy_top5_percent' => $col[2],
                'buy_top10_oi' => $col[3],
                'buy_top10_percent' => $col[4],
                'sell_top5_oi' => $col[5],
                'sell_top5_percent' => $col[6],
                'sell_top10_oi' => $col[7],
                'sell_top10_percent' => $col[8],
                'market_oi' => changeInt($col[9])
            ]
        );
    }
}