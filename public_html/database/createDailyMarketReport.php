<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE daily_market_report_contract (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) COMMENT='每日市場行情的contract代號'";
$pdo->createTable($sql);

$sql = "CREATE TABLE daily_market_report (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` INT UNSIGNED COMMENT '契約代號',
    `contract_month` VARCHAR(20) COMMENT '到期月份(週別)',
    `open` INT UNSIGNED DEFAULT 0 COMMENT '開盤價',
    `high` INT UNSIGNED DEFAULT 0 COMMENT '最高價',
    `low` INT UNSIGNED DEFAULT 0 COMMENT '最低價',
    `last` INT UNSIGNED DEFAULT 0 COMMENT '最後成交價',
    `change` VARCHAR(20) DEFAULT '0' COMMENT '漲跌價',
    `percent` VARCHAR(20) DEFAULT '0' COMMENT '漲跌%',
    `volume_after_hours` INT UNSIGNED DEFAULT 0 COMMENT '盤後交易時段成交量',
    `volume_regular` INT UNSIGNED DEFAULT 0 COMMENT '一般交易時段成交量',
    `volume_total` INT UNSIGNED DEFAULT 0 COMMENT '合計成交量',
    `settlement_price` INT UNSIGNED DEFAULT 0 COMMENT '結算價',
    `open_interest` INT UNSIGNED DEFAULT 0 COMMENT '未沖銷契約量',
    `best_bid` INT UNSIGNED DEFAULT 0 COMMENT '最後最佳買價',
    `best_ask` INT UNSIGNED DEFAULT 0 COMMENT '最後最佳賣價',
    `Historical_high` INT UNSIGNED DEFAULT 0 COMMENT '歷史最高價',
    `Historical_low` INT UNSIGNED DEFAULT 0 COMMENT '歷史最低價',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_dmrContract FOREIGN KEY (`contract`)
    REFERENCES daily_market_report_contract(id)
) COMMENT='每日市場行情'";
$pdo->createTable($sql);