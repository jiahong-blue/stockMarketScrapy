<?php
require_once __DIR__ .'/testCase.class.php';
require $_SERVER['DOCUMENT_ROOT'] . 'util/stockCurl.class.php';
require $_SERVER['DOCUMENT_ROOT'] . 'util/stockScrapy.class.php';

$curlObj = new StockCurl();
$scrapy = new StockScrapy($curlObj);
$testCase = new TestCase($scrapy);

/**
 * getUrlList
 */
$testCase->testMethod(
    'getUrlList',
    ['predictValue' => [], 'predictType' => 'array', 'param' => []],
    '因目前未有任何資料，回傳[]'
);

/**
 * setUrlList
 */
$testCase->testMethod(
    'setUrlList',
    ['predictValue' => false, 'predictType' => 'boolean', 'param' => [[]]],
    '測試setUrlList，傳[]應該會回傳false'
);

$testCase->testMethod(
    'setUrlList',
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => [
            [
                'test' => 'testvalue'
            ]
        ]
    ],
    '測試setUrlList，傳[test => testvalue ]應該會回傳false'
);

// key都對，但value的格式錯
$testCase->testMethod(
    'setUrlList',
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => [
            [
                [
                    'url' => 'testvalue',
                    'param' => 'test',
                    'isPost' => 0
                ]
            ]
        ]
    ],
    '測試setUrlList，傳複雜的參數，應該會回傳false'
);

// key都對，且value的格式也對
$testCase->testMethod(
    'setUrlList',
    [
        'predictValue' => true, 
        'predictType' => 'boolean', 
        'param' => [
            [
                [
                    'url' => 'testvalue',
                    'param' => [],
                    'isPost' => 0
                ]
            ]
        ]
    ],
    '測試setUrlList，傳複雜的參數，應該會回傳true'
);

// 一筆對，一筆錯
$testCase->testMethod(
    'setUrlList',
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => [
            [
                [
                    'url' => 'testvalue',
                    'param' => [],
                    'isPost' => 0
                ],
                [
                    'url' => 'testvalue',
                    'param' => 6536,
                    'isPost' => 0
                ],
            ]
        ]
    ],
    '測試setUrlList，傳複雜的參數，應該會回傳false'
);

/**
 * getCurlOptions
 */
$testCase->testMethod(
    'getCurlOptions',
    [
        'predictValue' => [], 
        'predictType' => 'array', 
        'param' => []
    ],
    '測試getCurlOptions，目前無資料'
);

/**
 * setCurlOptions
 */
$testCase->testMethod(
    'setCurlOptions',
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => ['sdfasf']
    ],
    '測試setCurlOptions，傳sdfasf預測會失敗'
);

$testCase->testMethod(
    'setCurlOptions',
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => [[]]
    ],
    '測試setCurlOptions，傳[]預測會失敗'
);

$testCase->testMethod(
    'setCurlOptions',
    [
        'predictValue' => true, 
        'predictType' => 'boolean', 
        'param' => [[ CURLOPT_POST => 1 ]]
    ],
    '測試setCurlOptions，傳[CURLOPT_POST => 1]預測會成功'
);

$testCase->testMethod(
    'getCurlOptions',
    [
        'predictValue' => [ CURLOPT_POST => 1], 
        'predictType' => 'array', 
        'param' => []
    ],
    '測試上一步是否正確'
);

/**
 * runScrapy
 */
$testCase->testMethod(
    'setUrlList',
    [
        'predictValue' => true, 
        'predictType' => 'boolean', 
        'param' => [
            [
                [
                    'url' => 'http://example.com',
                    'param' => [],
                    'isPost' => 0
                ],
                [
                    'url' => 'https://www.twse.com.tw/exchangeReport/MI_MARGN',
                    'param' => ['response'=>'json', 'selectType'=>'MS'],
                    'isPost' => 0
                ],
            ]
        ]
    ],
    '先設定要抓的網站'
);

$testCase->testMethod(
    'runScrapy',
    [
        'predictValue' => [ 'http://example.com' => 'test'], 
        'predictType' => 'array', 
        'param' => []
    ],
    '測試是否能抓到，回傳資料太長，無法對'
);