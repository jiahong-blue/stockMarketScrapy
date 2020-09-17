<?php
if ( $_SERVER['DOCUMENT_ROOT'] ) {
    $root = $_SERVER['DOCUMENT_ROOT'];
} else {
    $root = getenv('ROOT');
}
require_once $root . '/util/stockPDO.class.php';

$pdo = StockPDO::getInstance();

$sql = "CREATE TABLE futures_contracts_details_contract (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` VARCHAR(15) NOT NULL COMMENT '商品名稱',
    PRIMARY KEY (id)
) COMMENT='期貨契約contract代號'";
$pdo->createTable($sql);

$sql = "CREATE TABLE futures_contracts_details (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract` INT UNSIGNED COMMENT '商品名稱(代號)',
    `item` VARCHAR(20) COMMENT '身份別',
    `tv_long_volume` INT UNSIGNED DEFAULT 0 COMMENT '交易多方口數',
    `tv_long_value` BIGINT UNSIGNED DEFAULT 0 COMMENT '交易多方契約金額',
    `tv_short_volume` INT UNSIGNED DEFAULT 0 COMMENT '交易空方口數',
    `tv_short_value` BIGINT UNSIGNED DEFAULT 0 COMMENT '交易空方契約金額',
    `tv_net_volume` INT DEFAULT 0 COMMENT '交易多空淨額口數',
    `tv_net_value` BIGINT DEFAULT 0 COMMENT '交易多空淨額契約金額',
    `cv_long_volume` INT UNSIGNED DEFAULT 0 COMMENT '未平倉多方口數',
    `cv_long_value` BIGINT UNSIGNED DEFAULT 0 COMMENT '未平倉多方契約金額',
    `cv_short_volume` INT UNSIGNED DEFAULT 0 COMMENT '未平倉空方口數',
    `cv_short_value` BIGINT UNSIGNED DEFAULT 0 COMMENT '未平倉空方契約金額',
    `cv_net_volume` INT DEFAULT 0 COMMENT '未平倉多空淨額口數',
    `cv_net_value` BIGINT DEFAULT 0 COMMENT '未平倉多空淨額契約金額',
    `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_fcdContract FOREIGN KEY (`contract`)
    REFERENCES futures_contracts_details_contract(id)
) COMMENT='期貨契約'";
$pdo->createTable($sql);
