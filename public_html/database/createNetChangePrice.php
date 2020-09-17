<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE net_change_price_type (
    id INT NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(50) DEFAULT NULL COMMENT '類型',
    PRIMARY KEY(id)
) COMMENT='漲跌證券數合計的type代號'";

$pdo->createTable($sql);

$pdo->insertData('net_change_price_type', ['type' => '上漲(漲停)']);
$pdo->insertData('net_change_price_type', ['type' => '下跌(跌停)']);
$pdo->insertData('net_change_price_type', ['type' => '持平']);
$pdo->insertData('net_change_price_type', ['type' => '未成交']);
$pdo->insertData('net_change_price_type', ['type' => '無比價']);

$sql = "CREATE TABLE net_change_price (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` INT COMMENT '類型(代號)',
    overall_market VARCHAR(20) COMMENT '整體市場',
    stocks VARCHAR(20) COMMENT '股票',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_ncpType FOREIGN KEY (`type`)
    REFERENCES net_change_price_type(id)
) COMMENT='漲跌證券數合計 Net Change of Price (Number of Listed Securities)'";

$pdo->createTable($sql);