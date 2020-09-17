<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE market_summary_summary (
    id INT NOT NULL AUTO_INCREMENT,
    summary VARCHAR(50) DEFAULT NULL COMMENT '成交統計(這應該是項目)',
    PRIMARY KEY(id)
) COMMENT='大盤統計資訊的summary代號'";

$pdo->createTable($sql);
$pdo->insertData('market_summary_summary', ['summary' => '1.一般股票']);
$pdo->insertData('market_summary_summary', ['summary' => '2.台灣存託憑證']);
$pdo->insertData('market_summary_summary', ['summary' => '3.受益憑證']);
$pdo->insertData('market_summary_summary', ['summary' => '4.ETF']);
$pdo->insertData('market_summary_summary', ['summary' => '5.受益證券']);
$pdo->insertData('market_summary_summary', ['summary' => '6.變更交易股票']);
$pdo->insertData('market_summary_summary', ['summary' => '7.認購(售)權證']);
$pdo->insertData('market_summary_summary', ['summary' => '8.轉換公司債']);
$pdo->insertData('market_summary_summary', ['summary' => '9.附認股權特別股']);
$pdo->insertData('market_summary_summary', ['summary' => '10.附認股權公司債']);
$pdo->insertData('market_summary_summary', ['summary' => '11.認股權憑證']);
$pdo->insertData('market_summary_summary', ['summary' => '12.公司債']);
$pdo->insertData('market_summary_summary', ['summary' => '13.ETN']);
$pdo->insertData('market_summary_summary', ['summary' => '證券合計(1+6)']);
$pdo->insertData('market_summary_summary', ['summary' => '總計(1~13)']);


$sql = "CREATE TABLE market_summary (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    summary INT COMMENT '成交統計(代號)',
    trade_value BIGINT UNSIGNED DEFAULT 0 COMMENT '成交金額(NT)',
    trade_volume BIGINT UNSIGNED DEFAULT 0 COMMENT '成交股數(股)',
    `transaction` INT UNSIGNED DEFAULT 0 COMMENT '成交筆數',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAiNT FK_msSummary FOREIGN KEY (summary)
    REFERENCES market_summary_summary(id)
) COMMENT='大盤統計資訊'";

$pdo->createTable($sql);
