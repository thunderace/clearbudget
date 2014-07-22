<?php
// Tables
$sql[] = "CREATE TABLE `t_categories` (`id` INTEGER PRIMARY KEY  DEFAULT '', `name` VARCHAR DEFAULT '', `color` VARCHAR DEFAULT '', `maxAmountPerMonth` INTEGER DEFAULT '0');";
$sql[] = "CREATE TABLE `t_items` (`id` INTEGER PRIMARY KEY  DEFAULT '', `parentId` INTEGER DEFAULT 0, `isParent` INTEGER DEFAULT 0, `amount` FLOAT DEFAULT '', `operationDate` DATETIME DEFAULT '', `payee` VARCHAR DEFAULT '', `memo` VARCHAR DEFAULT '', `cleared` VARCHAR DEFAULT '', `category` INTEGER DEFAULT '', `createDate` DATETIME DEFAULT '', `debit` INTEGER DEFAULT '', `checkSum` VARCHAR DEFAULT '', `comments` VARCHAR DEFAULT '', `deleteFlag` INTEGER DEFAULT 0, `investmentFlag` INTEGER DEFAULT 0, `importId` INTEGER DEFAULT '', `importedBy` VARCHAR DEFAULT '', `modifiedBy` VARCHAR DEFAULT '');";
$sql[] = "CREATE TABLE `t_keywords` (`id` INTEGER PRIMARY KEY  DEFAULT '', `keyword` VARCHAR DEFAULT '', `category` INTEGER DEFAULT '');";
$sql[] = "CREATE TABLE `t_settings` (`id` INTEGER PRIMARY KEY  DEFAULT '',`secureAccess` INTEGER DEFAULT '0' ,`currency` VARCHAR DEFAULT 'USD' , `initialBalance` INTEGER DEFAULT 0, `language` VARCHAR DEFAULT 'en', `uid` VARCHAR DEFAULT '');";
$sql[] = "CREATE TABLE `t_version` (`id` INTEGER PRIMARY KEY  DEFAULT '' ,`cb_version` VARCHAR DEFAULT '0.9.7');";
$sql[] = "CREATE TABLE `t_imports` (`id` INTEGER PRIMARY KEY  DEFAULT '' ,`originalFileName` VARCHAR DEFAULT '' ,`importCount` INTEGER DEFAULT '' ,`importDuplicate` INTEGER DEFAULT '' ,`importDate` DATETIME DEFAULT '', `importType` INTEGER DEFAULT '');";
$sql[] = "CREATE TABLE `t_report_settings` (`id` INTEGER PRIMARY KEY  NOT NULL ,`name` VARCHAR,`minDate` VARCHAR,`maxDate` VARCHAR,`activeFlag` INTEGER, `categories` VARCHAR, `type` VARCHAR, `range` INTEGER, `debit` INTEGER, `credit` INTEGER)";
$sql[] = "CREATE TABLE `t_tasks_reminder` (`id` INTEGER PRIMARY KEY  NOT NULL , `memo` VARCHAR, `reminderDay` INTEGER, `amount` FLOAT,  `type` VARCHAR DEFAULT 'monthly', `createDate` DATETIME)";
$sql[] = "CREATE TABLE `t_users` (`id` INTEGER PRIMARY KEY  DEFAULT '' , `language` VARCHAR DEFAULT 'en',`username` VARCHAR DEFAULT '' ,`password` VARCHAR DEFAULT '' ,`type` VARCHAR DEFAULT 'user', `enabled` VARCHAR DEFAULT '1', `createDate` DATETIME DEFAULT '')";
// Views
$sql[] = "CREATE VIEW `v_distinct_month` AS SELECT distinct strftime(\"%Y-%m-01\", operationDate) as month FROM t_items WHERE deleteFlag=0 AND isParent=0 ORDER BY operationDate DESC;";
$sql[] = "CREATE VIEW `v_categories` AS select * from t_categories ORDER BY name ASC;";
$sql[] = "CREATE VIEW `v_items` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category,isParent, parentId, t_categories.name as categoryName, comments, deleteFlag, investmentFlag, importType, importedBy, modifiedBy FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
$sql[] = "CREATE VIEW `v_items_unfiled` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, comments, isParent, parentId, t_categories.name as categoryName, comments, importType, importedBy, modifiedBy FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE category = \"1\" AND deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
$sql[] = "CREATE VIEW `v_settings` AS SELECT id, secureAccess, currency, initialBalance, language FROM t_settings LIMIT 0,1;";
$sql[] = "CREATE VIEW `v_users` AS SELECT id, language, username, password, type, enabled, createDate FROM t_users;";
$sql[] = "CREATE VIEW `v_total_amount` AS SELECT count(amount) as count, total(amount) as total, strftime(\"%m\", operationDate) as month, strftime(\"%Y\", operationDate) as year, debit, category, isParent, parentId, name, color, maxAmountPerMonth, strftime(\"%m-%Y\", operationDate) as limitMonth, operationDate FROM t_items JOIN t_categories ON t_categories.id=t_items.category WHERE deleteFlag=0 and isParent=0 GROUP BY debit, category, strftime(\"%Y-%m\", operationDate) ORDER BY operationDate ASC;";
$sql[] = "CREATE VIEW `v_suggestions_items` AS  SELECT id, payee, memo,operationDate  FROM t_items WHERE deleteFlag=0 AND isParent=0 ORDER BY operationDate DESC;";
$sql[] = "CREATE VIEW `v_total_balance` AS SELECT total(amount) as total, debit FROM t_items WHERE deleteFlag=0 and isParent=0 GROUP BY debit;";
$sql[] = "CREATE VIEW `v_tasks_reminder` AS select id, memo, amount, reminderDay, type, createDate from t_tasks_reminder;";
// Indexes
$sql[] = "CREATE UNIQUE INDEX `i_items` ON `t_items` (`checkSum` ASC);";
$sql[] = "CREATE UNIQUE INDEX `i_keyword` ON `t_keywords` (`keyword` ASC);";
$sql[] = "CREATE UNIQUE INDEX `i_category` ON `t_categories` (`name` ASC);";
$sql[] = "CREATE UNIQUE INDEX `i_unique_report_name` ON `t_report_settings` (`name` ASC)";
$sql[] = "CREATE UNIQUE INDEX `i_unique_username` ON `t_users` (`username` ASC)";
// default data
$sql[] = "INSERT INTO t_report_settings (`name`, `type`, `range`, `activeFlag`, `categories`, `debit`, `credit`) VALUES ('3', 'range', '3', '0', '', '1', '1');";
$sql[] = "INSERT INTO t_report_settings (`name`, `type`, `range`, `activeFlag`, `categories`, `debit`, `credit`) VALUES ('6', 'range', '6', '0', '', '1', '1');";
$sql[] = "INSERT INTO t_report_settings (`name`, `type`, `range`, `activeFlag`, `categories`, `debit`, `credit`) VALUES ('12', 'range', '12', '1', '', '1', '1');";
$sql[] = "INSERT INTO t_version (`cb_version`) VALUES ('0.9.7');";
$sql[] = "INSERT INTO t_categories (`name`,`color`) VALUES ('uncategorized', '990000');";
$sql[] = "INSERT INTO t_settings (`language`, `secureAccess`, `currency`, `initialBalance`) VALUES ('en', '0', 'USD', '0');";
?>