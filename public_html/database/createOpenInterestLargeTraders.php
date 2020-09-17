<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE open_interest_large_traders_contract (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` VARCHAR(50) NOT NULL COMMENT '契約名稱',
    PRIMARY KEY (id)
) COMMENT='期貨大額交易人未沖銷部位結構表contract代號'";
$pdo->createTable($sql);

$sql = "CREATE TABLE open_interest_large_traders (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` INT UNSIGNED NOT NULL COMMENT '契約名稱(代號)',
    `contract_month` VARCHAR(30) DEFAULT '' COMMENT '到期月份(週別)',
    `buy_top5_oi` VARCHAR(25) DEFAULT '0' COMMENT '買方前五大交易人合計部位數',
    `buy_top5_percent` VARCHAR(25) DEFAULT '0' COMMENT '買方前五大交易人合計百分比',
    `buy_top10_oi` VARCHAR(25) DEFAULT '0' COMMENT '買方前十大交易人合計部位數',
    `buy_top10_percent` VARCHAR(25) DEFAULT '0' COMMENT '買方前十大交易人合計百分比',
    `sell_top5_oi` VARCHAR(25) DEFAULT '0' COMMENT '賣方前五大交易人合計部位數',
    `sell_top5_percent` VARCHAR(25) DEFAULT '0' COMMENT '賣方前五大交易人合計百分比',
    `sell_top10_oi` VARCHAR(25) DEFAULT '0' COMMENT '賣方前十大交易人合計部位數',
    `sell_top10_percent` VARCHAR(25) DEFAULT '0' COMMENT '賣方前十大交易人合計百分比',
    `market_oi` INT DEFAULT 0 COMMENT '全市場未沖銷部位數',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_oiltContract FOREIGN KEY (`contract`)
    REFERENCES open_interest_large_traders_contract(id)
) COMMENT='期貨大額交易人未沖銷部位結構表'";
$pdo->createTable($sql);