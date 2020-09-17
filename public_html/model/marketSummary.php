<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 大盤統計資訊
 * https://www.twse.com.tw/exchangeReport/MI_INDEX
 * market_summary
 */
switch ( $method ) {
    case 'GET':
       $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('market_summary');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`summary`,
                `major`.`trade_value`,
                `major`.`trade_volume`,
                `major`.`transaction`
                FROM market_summary major 
                LEFT JOIN market_summary_summary sub 
                ON major.summary = sub.id
                WHERE date >= :date_1 AND date < :date_2";

        $data = $pdo->queryData(
            $sql, 
            ['date_1' => $date, 'date_2' => $tomorrow]
        );

        $res = ['stat' => 'success', 'data' => $data, 'date' => $date];

        echo json_encode($res);
        exit;

    case 'POST':
        $message = [
            'stat' => 'error',
            'info' => 'Not support POST'
        ];
        echo json_encode($message);
        exit;
}