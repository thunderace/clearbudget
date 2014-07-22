<?php
// new as of 0.7.1
$sql[] = "ALTER TABLE `t_settings` ADD COLUMN `range` VARCHAR";
$sql[] = "ALTER TABLE `t_settings` ADD COLUMN `limitMonth` VARCHAR";
$sql[] = "UPDATE t_settings SET `range` = '12';";

// new as of 0.7.1
$sql[] = "CREATE TABLE IF NOT EXISTS `t_version` (`id` INTEGER PRIMARY KEY  DEFAULT '' ,`cb_version` VARCHAR DEFAULT '');";

// new as of 0.7.1
$sql[] = "INSERT INTO t_version (`cb_version`) VALUES ('0.7.1');";

// new as of 0.7.1
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_distinct_month' AS SELECT distinct strftime(\"%Y-%m-01\", operationDate) as month FROM t_items ORDER BY operationDate DESC;";

// new as of 0.7.1
$sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS `i_category` ON `t_categories` (`name` ASC);";

// new as of 0.7.1
$sql[] = "DROP VIEW IF EXISTS `v_total_amount`";
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_total_amount' AS  SELECT count(amount) as count, total(amount) as total, strftime(\"%m\", operationDate) as month, strftime(\"%Y\", operationDate) as year, debit, category, name, color, maxAmountPerMonth, strftime(\"%m-%Y\", operationDate) as limitMonth, operationDate FROM t_items JOIN t_categories ON t_categories.id=t_items.category GROUP BY debit, category, strftime(\"%m\", operationDate) ORDER BY operationDate ASC;";

$sql[] = "DROP view IF EXISTS `v_all_month`";
?>