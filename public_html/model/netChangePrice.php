<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 漲跌證券數合計
 * https://www.twse.com.tw/exchangeReport/MI_INDEX
 * net_change_price
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('net_change_price');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`type`,
                    `major`.`overall_market`,
                    `major`.`stocks`
                FROM net_change_price major 
                LEFT JOIN net_change_price_type sub 
                ON major.type = sub.id
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