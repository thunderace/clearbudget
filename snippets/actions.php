<?php
/**
* Display the action screen
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      snippets
* @access       public
*/

/***********************************************************************

  Copyright (C) 2008  Fabrice Douteaud (admin@clearbudget.net)

    This file is part of ClearBudget.

    ClearBudget is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ClearBudget is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ClearBudget.  If not, see <http://www.gnu.org/licenses/>.


************************************************************************/
//prevent direct access
if(!defined('ENGINEON')) die('Direct access forbidden');
$userSettings = class_settings::getInstance();
?>
<h1><img src="style/icons/wrench.png"/> <?php echo $keys->link_menuActions; ?></h1>
<table class="tableReport" width="100%">
  <tr>
    <th><?php echo $keys->text_actionTransaction; ?></th>
    <th><?php echo $keys->text_actionLoadTransactions; ?></th>
    <th><?php echo $keys->text_actionCategorisation; ?></th>
    <th><?php echo $keys->link_pageBackup; ?></th>
  </tr>
  <tr>
    <td>
    <a href="#manageUpload:subAction1"><img src="style/icons/table_edit.png"/> <?php echo $keys->link_manageUploads; ?></a><br/>
    <a href="#listMonthItems:subAction1"><img src="style/icons/table.png"/> <?php echo $keys->link_monthlyTransactions; ?></a><br/>
    <a href="#taskScheduler:subAction1"><img src="style/icons/clock_add.png"/> <?php echo $keys->linkText_taskReminderEditLink; ?></a><br/>
    </td>
    <td>
    <a href="#uploadQif:subAction1"><img src="style/icons/table_add.png"/> <?php echo $keys->link_uploadQif; ?></a><br/>
    <a href="#uploadQfx:subAction1"><img src="style/icons/table_gear.png"/> <?php echo $keys->link_uploadQfx; ?></a><br/>
    <a href="#uploadCSV:subAction1"><img src="style/icons/page_white_text.png"/> <?php echo $keys->link_uploadCSV; ?></a><br/>
    <a href="#addTransactionsManualy:subAction1"><img src="style/icons/table_go.png"/> <?php echo $keys->link_uploadManual; ?></a><br/>
    </td>
    <td>
    <a href="#editCategories:subAction1"><img src="style/icons/tag_blue_edit.png"/> <?php echo $keys->link_editCategory; ?></a><br/>
    <a href="#editKeywords:subAction1"><img src="style/icons/link_edit.png"/> <?php echo $keys->link_editKeywords; ?></a><br/>
    <a href="#autoFileItem:subAction1"><img src="style/icons/database_gear.png"/> <?php echo $keys->link_autoSort; ?></a><br/>
    <a href="#keywordSuggestor:subAction1"><img src="style/icons/link_break.png"/> <?php echo $keys->link_keywordSuggestor; ?></a><br/>

    </td>
    <td>
    <a href="index.php?ajax=1&action=doBackup"><img src="style/icons/database_save.png"/> <?php echo $keys->link_pageBackup; ?></a>
    </td>
  </tr>
</table>
<br/>
<div id="subAction1">&nbsp;</div>
