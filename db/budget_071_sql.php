<?php
// new as of 0.7.1
$sql[] = "ALTER TABLE `t_items` ADD COLUMN `importId` INTEGER";
// since we alter the t_items table, it is better to re-index it
$sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS `i_items` ON `t_items` (`checkSum` ASC);";

// new as of 0.7.2
$sql[] = "CREATE TABLE IF NOT EXISTS `t_imports` (`id` INTEGER PRIMARY KEY  DEFAULT '' ,`originalFileName` VARCHAR DEFAULT '' ,`importCount` INTEGER DEFAULT '' ,`importDuplicate` INTEGER DEFAULT '' ,`importDate` DATETIME DEFAULT '');";

// update the version to 0.7.2
$sql[] = "UPDATE t_version SET `cb_version` = '0.7.2' WHERE 1=1 AND ID=1;";
?>