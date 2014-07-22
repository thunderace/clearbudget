<?php
$sql[] = "DROP VIEW IF EXISTS `v_total_balance`";
$sql[] = "CREATE VIEW 'v_total_balance' AS SELECT total(amount) as total, debit FROM t_items WHERE deleteFlag=0 GROUP BY debit";

$sql[] = "ALTER TABLE `t_settings` ADD COLUMN `initialBalance` INTEGER DEFAULT 0;";

// update the version to 0.9.3
$sql[] = "UPDATE t_version SET `cb_version` = '0.9.3' WHERE 1=1 AND ID=1;";
?>