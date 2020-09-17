<?php

require_once __DIR__ .'/testCase.class.php';
require $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$testCase = new TestCase($pdo);
$testCase->testMethod(
    'getInstance', 
    ['predictValue' => $pdo, 'predictType' => 'object', 'param' => []]
);

// 測showTables，因目前未有任何表，所以沒error，不然應該預測的數值會錯
$testCase->testMethod(
    'getTables',
    ['predictValue' => [], 'predictType' => 'array', 'param' => ['null']],
    '因目前未有任何表，所以沒error，不然應該預測的數值會錯'
);

// 測createTable
$pdo->dropTable('test_table');
$testCase->testMethod(
    'createTable',
    [
        'predictValue' => null, 
        'predictType' => 'NULL', 
        'param' => [
            'CREATE TABLE test_table (
                col1 varchar(50),
                col2 integer
            )'
        ]
    ],
    '建表不返回任何值'
);

// 測queryData
$testCase->testMethod(
    'queryData',
    [
        'predictValue' => [[ 'result' => '1']], 
        'predictType' => 'array', 
        'param' => [
            'SELECT COUNT(1) AS result
            FROM information_schema.tables
            WHERE table_name = "test_table"'
        ]
    ],
    '測上一步建的表test_table'
);

// 測insertData
$testCase->testMethod(
    'insertData',
    [
        'predictValue' => 1, 
        'predictType' => 'integer', 
        'param' => [
            'test_table',
            [
                'col1' => 'test 1',
                'col2' => 1
            ]
        ]
    ],
    '測試insertData,新增資料col1="test 1", col2=1'
);

// 測insertData
$testCase->testMethod(
    'insertData',
    [
        'predictValue' => 1, 
        'predictType' => 'integer', 
        'param' => [
            'test_table',
            [
                'col2' => 2
            ]
        ]
    ],
    '測試insertData,新增資料col2=2'
);

// 測updateData,沒輸入參數的狀態
$testCase->testMethod(
    'updateData',
    [
        'predictValue' => 0, 
        'predictType' => 'integer', 
        'param' => [
            'test_table',
            [],
            []
        ]
    ],
    '測試updateData,若參數都沒帶的狀況'
);

// 測updateData,將col2=2的資料，其col1改為'test update'
$testCase->testMethod(
    'updateData',
    [
        'predictValue' => 1, 
        'predictType' => 'integer', 
        'param' => [
            'test_table',
            [
                'col1' => 'test update'
            ],
            [
                'col2' => 2
            ]
        ]
    ],
    '測試updateData,將col2=2的資料，其col1改為"test update"'
);

// 測上一步資料是否真的變動
$testCase->testMethod(
    'queryData',
    [
        'predictValue' => [[ 'col1' => 'test update']], 
        'predictType' => 'array', 
        'param' => [
            'SELECT  col1
            FROM test_table
            WHERE col2 = 2'
        ]
    ],
    '用queryData測上一步是否真的成功'
);

// 測deleteData
$testCase->testMethod(
    'deleteData',
    [
        'predictValue' => 0, 
        'predictType' => 'integer', 
        'param' => [
            'test_table',
            []
        ]
    ],
    '測測deleteData,都不帶參數'
);

// 測deleteData
$testCase->testMethod(
    'deleteData',
    [
        'predictValue' => 1, 
        'predictType' => 'integer', 
        'param' => [
            'test_table',
            [
                'col2' => 1
            ]
        ]
    ],
    '測測deleteData,刪col2=1'
);

// 測上一步資料是否真的變動
$testCase->testMethod(
    'queryData',
    [
        'predictValue' => [], 
        'predictType' => 'array', 
        'param' => [
            'SELECT  col1
            FROM test_table
            WHERE col2 = 1'
        ]
    ],
    '用queryData測上一步是否真的成功'
);