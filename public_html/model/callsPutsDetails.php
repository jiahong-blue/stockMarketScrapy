<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 三大法人-選擇權買賣權分計
 * https://www.taifex.com.tw/cht/3/callsAndPutsDateExcel
 * calls_puts_details_contract
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('calls_puts_details');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`contract`,`major`.`call_put`,`major`.`item`,
                    `major`.`tv_long_volume`,`major`.`tv_long_value`,
                    `major`.`tv_short_volume`,`major`.`tv_short_value`,
                    `major`.`tv_net_volume`,`major`.`tv_net_value`,
                    `major`.`cv_long_volume`,`major`.`cv_long_value`,
                    `major`.`cv_short_volume`,`major`.`cv_short_value`,
                    `major`.`cv_net_volume`,`major`.`cv_net_value`
                FROM calls_puts_details major 
                LEFT JOIN calls_puts_details_contract sub 
                ON major.contract = sub.id
                WHERE date >= :date_1 AND date < :date_2";

        $data = $pdo->queryData(
            $sql, 
            ['date_1' => $date, 'date_2' => $tomorrow]
        );

        $processData = [];
        foreach ( $data as $v ) {
            if ( $processData[$v['contract']] === null ) {
                $processData[$v['contract']] = [];
            }

            if ( $processData[$v['contract']][$v['call_put']] === null ) {
                $processData[$v['contract']][$v['call_put']] = [];
            }
            $processV = array_diff_key( $v, ['contract'=> 0, 'call_put' => 0]);
            $processData[$v['contract']][$v['call_put']][] = $processV;
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