<?php
// new as of 0.9.1
// report settings table
$sql[] = "CREATE TABLE t_report_settings (`id` INTEGER PRIMARY KEY  NOT NULL ,`name` VARCHAR,`minDate` VARCHAR,`maxDate` VARCHAR,`activeFlag` INTEGER, `categories` VARCHAR, `type` VARCHAR, `range` INTEGER, `debit` INTEGER, `credit` INTEGER)";
$sql[] = "INSERT INTO t_report_settings (`name`, `type`, `range`, `activeFlag`, `categories`, `debit`, `credit`) VALUES ('3', 'range', '3', '0', '', '1', '1');";
$sql[] = "INSERT INTO t_report_settings (`name`, `type`, `range`, `activeFlag`, `categories`, `debit`, `credit`) VALUES ('6', 'range', '6', '0', '', '1', '1');";
$sql[] = "INSERT INTO t_report_settings (`name`, `type`, `range`, `activeFlag`, `categories`, `debit`, `credit`) VALUES ('12', 'range', '12', '1', '', '1', '1');";

// new as of 0.9.1
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_total_balance' AS SELECT total(amount) as total, debit FROM t_items group by debit";

// new as of 0.9.1
$sql[] = "CREATE UNIQUE INDEX i_unique_report_name ON t_report_settings (`name` ASC)";

// update the version to 0.9.1
$sql[] = "UPDATE t_version SET `cb_version` = '0.9.1' WHERE 1=1 AND ID=1;";
?>