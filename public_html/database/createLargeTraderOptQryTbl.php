<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

/**
 * 選擇權大額交易人未沖銷部位結構表
 */

 $pdo = StockPDO::getInstance();

 $sql = "CREATE TABLE large_trader_opt_contract (
     id INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `contract` VARCHAR(25) NOT NULL COMMENT '契約名稱',
     PRIMARY KEY (id)
 ) COMMENT='選擇權大額交易人的代號'";
$pdo->createTable($sql);

 $sql = "CREATE TABLE large_trader_opt (
     id INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `contract` INT UNSIGNED COMMENT '契約名稱(代號)',
     `contract_month` VARCHAR(15) DEFAULT '' COMMENT '到期月份(週別)',
     `buy_position_top5` VARCHAR(25) DEFAULT '0' COMMENT '買方前五大交易人合計(特定法人合計)部位數',
     `buy_position_top5_percent` VARCHAR(20) DEFAULT '0' COMMENT '買方前五大交易人合計(特定法人合計)百分比',
     `buy_position_top10` VARCHAR(25) DEFAULT '0' COMMENT '買方前十大交易人合計(特定法人合計)部位數',
     `buy_position_top10_percent` VARCHAR(20) DEFAULT '0' COMMENT '買方前十大交易人合計(特定法人合計)百分比',
     `sell_position_top5` VARCHAR(25) DEFAULT '0' COMMENT '賣方方前五大交易人合計(特定法人合計)部位數',
     `sell_position_top5_percent` VARCHAR(20) DEFAULT '0' COMMENT '賣方方前五大交易人合計(特定法人合計)百分比',
     `sell_position_top10` VARCHAR(25) DEFAULT '0' COMMENT '賣方方前十大交易人合計(特定法人合計)部位數',
     `sell_position_top10_percent` VARCHAR(20) DEFAULT '0' COMMENT '賣方方前十大交易人合計(特定法人合計)百分比',
     `oi__market` VARCHAR(30) DEFAULT '0' COMMENT '全市場未沖銷部位數',
     `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (ID),
     CONSTRAINT FK_ltoContract FOREIGN KEY (`contract`)
     REFERENCES large_trader_opt_contract(id)
 ) COMMENT='選擇權大額交易人未沖銷部位結構表'";
 $pdo->createTable($sql);