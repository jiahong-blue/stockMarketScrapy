<?php
// api 只回傳json格式
header('Content-Type: application/json;charset=UTF-8');

switch ( $path ) {
    case '/api/futDailyMarket':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/futDailyMarket.php';
        exit;

    case '/api/callsPutsDetails':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/callsPutsDetails.php';
        exit;

    case '/api/futuresContractsDetails':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/futuresContractsDetails.php';
        exit;

    case '/api/largeTraderOpt':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/largeTraderOpt.php';
        exit;

    case '/api/openInterestLargeTraders':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/openInterestLargeTraders.php';
        exit;

    case '/api/marginTransactionSummary':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/marginTransactionSummary.php';
        exit;

    case '/api/netChangePrice':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/netChangePrice.php';
        exit;

    case '/api/marketSummary':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/marketSummary.php';
        exit;

    case '/api/tradeValueForeignOtherInvestors':
        require_once $_SERVER['DOCUMENT_ROOT'] . 'model/tradeValueForeignOtherInvestors.php';
        exit;

    default:
        $message = [
            'stat' => 'error',
            'info' => 'Data not found'
        ];
        echo json_encode($message);
        exit;
}