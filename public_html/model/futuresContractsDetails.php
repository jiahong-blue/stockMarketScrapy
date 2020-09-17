<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockPDO.class.php';

$pdo = StockPDO::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

/**
 * 三大法人-區分各期貨契約
 * https://www.taifex.com.tw/cht/3/futContractsDateExcel
 * futures_contracts_details_contract
 */
switch ( $method ) {
    case 'GET':
        $check = $_REQUEST['date'] !== null &&
                 tools_validateDate($_REQUEST['date']);

        if ( $check ) {
            $date = $_REQUEST['date'];
        } else {
            $date = $pdo->getLastRowDate('futures_contracts_details');
        }

        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));

        $sql = "SELECT `sub`.`contract`,`major`.`item`,
                `major`.`tv_long_volume`,`major`.`tv_long_value`,
                `major`.`tv_short_volume`,`major`.`tv_short_value`,
                `major`.`tv_net_volume`,`major`.`tv_net_value`,
                `major`.`cv_long_volume`,`major`.`cv_long_value`,
                `major`.`cv_short_volume`,`major`.`cv_short_value`,
                `major`.`cv_net_volume`,`major`.`cv_net_value`
                FROM futures_contracts_details major 
                LEFT JOIN futures_contracts_details_contract sub 
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

            $processV = array_diff_key( $v, ['contract'=> 0]);
            $processData[$v['contract']][] = $processV;
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