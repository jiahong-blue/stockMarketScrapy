<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 信用交易統計
 * https://www.twse.com.tw/exchangeReport/MI_MARGN
 * margin_transaction_summary
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('margin_transaction_summary');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`item`,`major`.`margin_purchase`,
                    `major`.`margin_sale`,`major`.`cash_redemption`,
                    `major`.`balance_previous_day`,`major`.`Balance_today`
                FROM margin_transaction_summary major 
                LEFT JOIN margin_transaction_summary_item sub 
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