<?php
// modify tables
$sql[] = "ALTER TABLE t_items ADD COLUMN `importedBy` VARCHAR DEFAULT '';";
$sql[] = "ALTER TABLE t_items ADD COLUMN `modifiedBy` VARCHAR DEFAULT '';";
$sql[] = "ALTER TABLE t_settings ADD COLUMN `uid` VARCHAR DEFAULT '';";

// modify views
$sql[] = "DROP VIEW IF EXISTS `v_settings`";
$sql[] = "CREATE VIEW `v_settings` AS SELECT id, secureAccess, currency, initialBalance, language FROM t_settings LIMIT 0,1;";
$sql[] = "DROP VIEW IF EXISTS `v_items`";
$sql[] = "CREATE VIEW `v_items` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category,isParent, parentId, t_categories.name as categoryName, comments, deleteFlag, investmentFlag, importType, importedBy, modifiedBy FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
$sql[] = "DROP VIEW IF EXISTS `v_items_unfiled`";
$sql[] = "CREATE VIEW `v_items_unfiled` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, comments, isParent, parentId, t_categories.name as categoryName, comments, importType, importedBy, modifiedBy FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE category = \"1\" AND deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";

// new table
$sql[] = "CREATE TABLE `t_users` (`id` INTEGER PRIMARY KEY  DEFAULT '' ,`language` VARCHAR DEFAULT 'en' ,`username` VARCHAR NOT NULL DEFAULT '' ,`password` VARCHAR NOT NULL DEFAULT '' ,`type` VARCHAR DEFAULT 'user',  `enabled` VARCHAR DEFAULT '1', `createDate` DATETIME DEFAULT '')";

// new view
$sql[] = "CREATE VIEW `v_users` AS SELECT id, language, username, password, type, enabled, createDate FROM t_users;";

// new indeces
$sql[] = "CREATE UNIQUE INDEX `i_unique_username` ON `t_users` (`username` ASC)";

// update the version to 0.9.7
$sql[] = "UPDATE t_version SET `cb_version` = '0.9.7' WHERE ID=1;";
?>