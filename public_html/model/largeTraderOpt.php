<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 選擇權大額交易人未沖銷部位結構表
 * https://www.taifex.com.tw/cht/3/largeTraderOptQryTbl
 * large_trader_opt_contract
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('large_trader_opt');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`contract`,`major`.`contract_month`,
          `major`.`buy_position_top5`,`major`.`buy_position_top5_percent`,
          `major`.`buy_position_top10`,`major`.`buy_position_top10_percent`,
          `major`.`sell_position_top5`,`major`.`sell_position_top5_percent`,
          `major`.`sell_position_top10`,`major`.`sell_position_top10_percent`,
          `major`.`oi__market`,`major`.`date`
                FROM large_trader_opt major 
                LEFT JOIN large_trader_opt_contract sub 
                ON major.contract = sub.id
                WHERE date >= :date_1 AND date < :date_2";

        $data = $pdo->queryData(
            $sql, 
            ['date_1' => $date, 'date_2' => $tomorrow]
        );

        // 處理資料
        $processData = [];
        foreach ( $data as $v ) {
            if ( $processData[$v['contract']] === null ) {
                $processData[$v['contract']] = [];
            }
            $tempValue = array_slice($v, 1);
            $processData[$v['contract']][] = $tempValue;
        }

        $res = ['stat' => 'success', 'data' => $processData, 'date' => $date];

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