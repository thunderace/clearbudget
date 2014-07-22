<?php
/**
* Defautl page if action is unknown
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      actions
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
$db = class_db::getInstance();
// get the reminders
$taskObj = new class_task();
$tasks = $taskObj->getRemindersForToday();
// get the total amount of reminder for the current month
$totalTasksAmount = $taskObj->getMonthlyRemindersTotalAmount(date('Y-m'));

// get the debit flag
$debit = $context->debit;

// instantiate the necessary objects
$reportSettings = new class_reportsettings();
// get the active report settings
$availableReportRanges = $reportSettings->getAllReportSettingNames();
$reportSettings->getReportSettings();
// get the report object
$report = class_report::getInstance();
// get the reporting data
$report->getData();
// the category colors
$categoryColor = $report->categoryColor;
// get all categories
$categories = $report->categories;
// get the total balance
$totalBalance = $report->getTotalBalance();
$balance = $totalBalance['total'];
$creditBalance = $totalBalance['credit'];
$debitBalance = $totalBalance['debit'];

// if no data, we go to the wizard page
$errorFlag = false;
if($context->totalItemCount < 1 && $report->validData === false) {
  // set the error tag
  $context->error = 'error_NoTransactionData';
  // redirect to wizard
  $context->nextAction = 'errorDisplay';
  $context->redirect('errorDisplay');
  return;
  }
elseif($context->totalItemCount>0 && $report->validData === false) {
  // set the error tag
  $error = 'error_noReportData';
  $context->errorFlag = true;
  return;
  }
elseif($report->reportSettings->debit == null) {
  // set the error tag
  $error = 'error_noReportSelected';
  $context->errorFlag = true;
  return;
  }

// make sure the homePage is set to this page
setcookie('homePage', 'overview-timeline', time()+(3600*720), "/"); // expire in 30 days

// add date constraints
switch($report->reportSettings->type) {
   case 'range':
     $month = $report->getLimitMonthForRange($reportSettings->range);
     $db->addWhere('operationDate', '>=', $month);
   break;
   case 'custom':
     $db->addWhereBetween('operationDate', $report->reportSettings->minDate, $report->reportSettings->maxDate);
   break;
  }
$db->addFields('id');
$db->addFields('operationDate');
$db->addFields('category');
$db->addFields('categoryName');
$db->addFields('amount');
$db->addFields('debit');
$db->addFields('payee');
$db->addFields('memo');
$db->addFields('comments');
$db->addFields('parentId');
$db->addFields('importType');
if($debit == '0' || $debit == '1') $db->addWhere('debit', '=', $debit);
$count = $db->select('v_items', true);
$results = $db->fetchAllRows();

foreach($results as $item) {
  class_debug::addMsg(__FILE__, 'debit is '.$item['debit'].' and operation date is '.$item["operationDate"], DEBUGINFO);
  // simulate a group by operation date
  // we group transactions per day to avoid too many dots on the timeline
  $items[$item['operationDate']][$item['debit']][] = $item;
}

?>