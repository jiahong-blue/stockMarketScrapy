<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 三大法人買賣金額統計表
 * https://www.twse.com.tw/fund/BFI82U
 * trade_value_foreign_other_investors
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('trade_value_foreign_other_investors');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`item`,
                    `major`.`total_buy`,
                    `major`.`total_sell`,
                    `major`.`difference`
                FROM trade_value_foreign_other_investors major 
                LEFT JOIN trade_value_foreign_other_investors_item sub 
                ON major.item = sub.id
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