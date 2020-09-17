<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE trade_value_foreign_other_investors_item (
    id INT NOT NULL AUTO_INCREMENT,
    item VARCHAR(50) DEFAULT NULL COMMENT '單位名稱',
    PRIMARY KEY(id)
) COMMENT='三大法人買賣金額統計表的item代號'";

$pdo->createTable($sql);

$pdo->insertData('trade_value_foreign_other_investors_item', ['item' => '自營商(自行買賣)']);
$pdo->insertData('trade_value_foreign_other_investors_item', ['item' => '自營商(避險)']);
$pdo->insertData('trade_value_foreign_other_investors_item', ['item' => '投信']);
$pdo->insertData('trade_value_foreign_other_investors_item', ['item' => '外資及陸資']);
$pdo->insertData('trade_value_foreign_other_investors_item', ['item' => '合計']);


$sql = "CREATE TABLE trade_value_foreign_other_investors (
    id INT UNSIGNED AUTO_INCREMENT,
    item INT COMMENT '單位名稱(代號)',
    total_buy BIGINT UNSIGNED DEFAULT 0 COMMENT '買進金額',
    total_sell BIGINT UNSIGNED DEFAULT 0 COMMENT '賣出金額',
    `difference` BIGINT DEFAULT 0 COMMENT '買賣差額',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_tvfoiItem FOREIGN KEY (item)
    REFERENCES trade_value_foreign_other_investors_item(id)
) COMMENT='三大法人買賣金額統計表'";

$pdo->createTable($sql);