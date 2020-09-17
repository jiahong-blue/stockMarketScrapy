<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE calls_puts_details_contract (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` VARCHAR(15) NOT NULL,
    PRIMARY KEY (id)
) COMMENT='選擇權買賣權分計contract代號'";
$pdo->createTable($sql);

$sql = "CREATE TABLE calls_puts_details (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` INT UNSIGNED NOT NULL COMMENT '商品名稱(代號)',
    `call_put` VARCHAR(15) DEFAULT '' COMMENT '權別',
    `item` VARCHAR(15) DEFAULT '' COMMENT '身份別',
    `tv_long_volume` INT DEFAULT 0 COMMENT '交易買方口數',
    `tv_long_value` BIGINT DEFAULT 0 COMMENT '交易買方契約金額',
    `tv_short_volume` INT DEFAULT 0 COMMENT '交易賣方口數',
    `tv_short_value`BIGINT DEFAULT 0 COMMENT '交易賣方契約金額',
    `tv_net_volume` INT DEFAULT 0 COMMENT '交易淨口數',
    `tv_net_value` BIGINT DEFAULT 0 COMMENT '交易淨契約金額',
    `cv_long_volume` INT DEFAULT 0 COMMENT '未平倉買方口數',
    `cv_long_value` BIGINT DEFAULT 0 COMMENT '未平倉買方契約金額',
    `cv_short_volume` INT DEFAULT 0 COMMENT '未平倉賣方口數',
    `cv_short_value` BIGINT DEFAULT 0 COMMENT '未平倉賣方契約金額',
    `cv_net_volume` INT DEFAULT 0 COMMENT '未平倉淨口數',
    `cv_net_value` BIGINT DEFAULT 0 COMMENT '未平倉淨契約金額',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_cpdContract FOREIGN KEY (`contract`)
    REFERENCES calls_puts_details_contract(id)
) COMMENT='選擇權買賣權分計'";
$pdo->createTable($sql);