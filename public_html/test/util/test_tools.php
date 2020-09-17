<?php
require_once __DIR__ .'/testCase.class.php';
require $_SERVER['DOCUMENT_ROOT'] . 'util/tools.php';

// 測tools_isNull
$res = tools_isNull(null);
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為null'
);

// 測tools_isNull
$res = tools_isNull('');
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為""'
);

// 測tools_isNull
$res = tools_isNull('');
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為"  "'
);

// 測tools_isNull
$res = tools_isNull('0');
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為"0"'
);

// 測tools_isNull
$res = tools_isNull(0);
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為0'
);

// 測tools_isNull
$res = tools_isNull([]);
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為[]'
);

// 測tools_isNull
$res = tools_isNull(true);
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為true'
);

// 測tools_isNull
$res = tools_isNull(false);
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為true'
);

// 測tools_isNull
$res = tools_isNull([0]);
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為[0]'
);

// 測tools_isNull
$res = tools_isNull('abc');
TestCase::testNonClassMethod(
    'tools_isNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為abc'
);

/**
 * tools_isArrayExceptNull
 */
$res = tools_isArrayExceptNull('abc');
TestCase::testNonClassMethod(
    'tools_isArrayExceptNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為abc'
);

$res = tools_isArrayExceptNull([]);
TestCase::testNonClassMethod(
    'tools_isArrayExceptNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為[]'
);

$res = tools_isArrayExceptNull([0]);
TestCase::testNonClassMethod(
    'tools_isArrayExceptNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為[0]'
);

$res = tools_isArrayExceptNull(0);
TestCase::testNonClassMethod(
    'tools_isArrayExceptNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為0'
);


/**
 * tools_isStringExceptNull
 */
$res = tools_isStringExceptNull('abc');
TestCase::testNonClassMethod(
    'tools_isStringExceptNull',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為abc'
);

$res = tools_isStringExceptNull('');
TestCase::testNonClassMethod(
    'tools_isStringExceptNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為""'
);

$res = tools_isStringExceptNull('  ');
TestCase::testNonClassMethod(
    'tools_isStringExceptNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為"  "'
);

$res = tools_isStringExceptNull([0]);
TestCase::testNonClassMethod(
    'tools_isStringExceptNull',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為[0]'
);

/**
 * tools_isOneOrZero
 */
$res = tools_isOneOrZero(2);
TestCase::testNonClassMethod(
    'tools_isOneOrZero',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為2'
);

$res = tools_isOneOrZero([345]);
TestCase::testNonClassMethod(
    'tools_isOneOrZero',
    [
        'result' => $res,
        'predictValue' => false,
        'predictType' => 'boolean'
    ],
    '測值為[345]'
);

$res = tools_isOneOrZero(0);
TestCase::testNonClassMethod(
    'tools_isOneOrZero',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為0'
);

$res = tools_isOneOrZero(1);
TestCase::testNonClassMethod(
    'tools_isOneOrZero',
    [
        'result' => $res,
        'predictValue' => true,
        'predictType' => 'boolean'
    ],
    '測值為1'
);