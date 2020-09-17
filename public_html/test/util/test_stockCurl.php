<?php
require_once __DIR__ .'/testCase.class.php';
require $_SERVER['DOCUMENT_ROOT'] . 'util/stockCurl.class.php';

$testCase = new TestCase(new StockCurl());

/**
 * getUrl
 */
$testCase->testMethod(
    'getUrl', 
    ['predictValue' => '', 'predictType' => 'string', 'param' => []],
    '測試getUrl,剛建物建不帶參數下預設為""'
);

/**
 * setUrl
 */
$testCase->testMethod(
    'setUrl', 
    ['predictValue' => null, 'predictType' => 'NULL', 'param' => ['abc']],
    '測試setUrl,傳abc'
);

$testCase->testMethod(
    'getUrl', 
    ['predictValue' => 'abc', 'predictType' => 'string', 'param' => []],
    '確認上一步建立是否有誤'
);

/**
 * getCurlOptions
 */
$testCase->testMethod(
    'getCurlOptions', 
    [
        'predictValue' => [
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36',
            CURLOPT_RETURNTRANSFER => 1
        ], 
        'predictType' => 'array', 
        'param' => []
    ],
    '確認上一步建立是否有誤'
);

/**
 * setCurlOptions
 */
$testCase->testMethod(
    'setCurlOptions', 
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => ['']
    ],
    '輸入""時'
);

$testCase->testMethod(
    'setCurlOptions', 
    [
        'predictValue' => false, 
        'predictType' => 'boolean', 
        'param' => ['abc']
    ],
    '輸入"abc"時'
);

$testCase->testMethod(
    'setCurlOptions', 
    [
        'predictValue' => true, 
        'predictType' => 'boolean', 
        'param' => [['testKey' => 'testValue']]
    ],
    '輸入"[ testKey => testValue ]"時'
);

$testCase->testMethod(
    'getCurlOptions', 
    [
        'predictValue' => ['testKey' => 'testValue'], 
        'predictType' => 'array', 
        'param' => []
    ],
    '確認上一步建立是否有誤'
);

/**
 * initializeCurlOptions
 */
$testCase->testMethod(
    'initializeCurlOptions',
    [
        'predictValue' => null, 
        'predictType' => 'NULL', 
        'param' => []
    ],
    '測試initializeCurlOptions'
);

$testCase->testMethod(
    'getCurlOptions', 
    [
        'predictValue' => [
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36',
            CURLOPT_RETURNTRANSFER => 1
        ], 
        'predictType' => 'array', 
        'param' => []
    ],
    '確認上一步建立是否有誤'
);

/**
 * runRequest
 */
$testCase->testMethod(
    'runRequest', 
    [
        'predictValue' => [
            'status' => 'fail',
            'info' => " error no: 6 message: Couldn't resolve host name",
            'data' => null
        ], 
        'predictType' => 'array', 
        'param' => []
    ],
    '因之前所設定的資料是錯的，所以會是回傳失敗'
);

$testCase->testMethod(
    'setUrl', 
    ['predictValue' => null, 'predictType' => 'NULL', 'param' => ['http://example.com']],
    '重新setUrl,http://example.com'
);

$testCase->testMethod(
    'runRequest', 
    [
        'predictValue' => [
            'status' => 'success',
            'info' => "success",
            'data' => ''
        ], 
        'predictType' => 'array', 
        'param' => []
    ],
    '向http://example.com請求資源,因回傳的資料太長，所以預測的值沒填，會錯'
);

// 抓json
$testCase->testMethod(
    'setUrl', 
    ['predictValue' => null, 'predictType' => 'NULL', 'param' => ['https://www.twse.com.tw/exchangeReport/MI_MARGN']],
    '重新setUrl,https://www.twse.com.tw/exchangeReport/MI_MARGN'
);

$testCase->testMethod(
    'runRequest', 
    [
        'predictValue' => [
            'status' => 'success',
            'info' => "success",
            'data' => ''
        ], 
        'predictType' => 'array', 
        'param' => []
    ],
    '向http://example.com請求資源,因回傳的資料太長，所以預測的值沒填，會錯'
);

$testCase->testMethod(
    'setCurlOptions', 
    [
        'predictValue' => true, 
        'predictType' => 'boolean', 
        'param' => [[ CURLOPT_POSTFIELDS => 'response=json&selectType=MS&' ]]
    ],
    '輸入[ CURLOPT_POSTFIELDS => response=json&selectType=MS&date= ]時'
);

$testCase->testMethod(
    'runRequest', 
    [
        'predictValue' => [
            'status' => 'success',
            'info' => "success",
            'data' => ''
        ], 
        'predictType' => 'array', 
        'param' => []
    ],
    '請求資源,因回傳的資料太長，所以預測的值沒填，會錯'
);