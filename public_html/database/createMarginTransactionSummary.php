<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE margin_transaction_summary_item (
    id INT NOT NULL AUTO_INCREMENT,
    item VARCHAR(50) DEFAULT NULL COMMENT '項目',
    PRIMARY KEY(id)
) COMMENT='信用交易統計的item代號'";

$pdo->createTable($sql);
$pdo->insertData('margin_transaction_summary_item', ['item' => '融資(交易單位)']);
$pdo->insertData('margin_transaction_summary_item', ['item' => '融券(交易單位)']);
$pdo->insertData('margin_transaction_summary_item', ['item' => '融資金額(仟元)']);

$sql = "CREATE TABLE margin_transaction_summary (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    item INT NOT NULL COMMENT '項目代號(FK)',
    margin_purchase INT DEFAULT 0 COMMENT '買進',
    margin_sale INT DEFAULT 0 COMMENT '賣出',
    cash_redemption INT DEFAULT 0 COMMENT '現金(券)償還',
    balance_previous_day INT DEFAULT 0 COMMENT '前日餘額',
    Balance_today INT DEFAULT 0 COMMENT '今日餘額',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id),
    CONSTRAINT FK_mtsItem FOREIGN KEY (item)
    REFERENCES margin_transaction_summary_item(id)
) COMMENT='信用交易統計'";

$pdo->createTable($sql);