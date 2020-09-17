<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 用來取得臺股期貨  ( TX ) 行情表
 * https://www.taifex.com.tw/cht/3/futDailyMarketExcel
 * 
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('daily_market_report');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`contract`, `major`.`contract_month`,
                 `major`.`open`, `major`.`high`,`major`.`low`,
                 `major`.`last`,`major`.`change`,`major`.`percent`,
                 `major`.`volume_after_hours`,`major`.`volume_regular`,
                 `major`.`volume_total`,`major`.`settlement_price`,
                 `major`.`open_interest`,`major`.`best_bid`,
                 `major`.`best_ask`, `major`.`Historical_high`,
                 `major`.`Historical_low`
                FROM daily_market_report major 
                LEFT JOIN daily_market_report_contract sub 
                ON major.contract = sub.id
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