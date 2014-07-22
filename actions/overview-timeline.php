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
// get the reminders
$taskObj = new class_task();
$tasks = $taskObj->getRemindersForToday();
// get the total amount of reminder for the current month
$totalTasksAmount = $taskObj->getMonthlyRemindersTotalAmount(date('Y-m'));

// instantiate the necessary objects
$reportSettings = new class_reportsettings();
// get the active report settings
$availableReportRanges = $reportSettings->getAllReportSettingNames();
$reportSettings->getReportSettings();
// get the report object
$report = class_report::getInstance();
// get the reporting data
$report->getData();

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

// build the array of spending per category
$dates = $report->dates;
$categories = $report->categories;
$categoryColor = $report->categoryColor;
$debitTotalPerCategory = $report->debitTotalPerCategory;
$creditTotalPerCategory = $report->creditTotalPerCategory;
$debitPerCategoryPerMonth = $report->debitPerCategoryPerMonth;
$creditPerCategoryPerMonth = $report->creditPerCategoryPerMonth;
$debitsPctPerCategory = $report->debitPctPerCategory;
$creditsPctPerCategory = $report->creditPctPerCategory;

// make sure the homePage is set to this page
setcookie('homePage', 'overview-timeline', time()+(3600*720), "/"); // expire in 30 days

// add an entry for each date, if none, then 0
foreach($categories as $categoryId=>$categoryName) {
  // ignore 0 values in the beginning of the series - this takes care of newly created categories or period with no spending/income
  $debitInit = false;
  $creditInit = false;
  foreach($dates as $date) {
      // get the total debits for this category/month
     if(isset($debitPerCategoryPerMonth[$categoryId][$date])) {
      $debit = $debitPerCategoryPerMonth[$categoryId][$date];
      }
     else {
       $debit = 0;
     }
    // add the value to the array
    $tmp['total'] = $debit;
    $tmp['date'] = $date;
    if($tmp['total'] != 0 || $debitInit) {
      $spendingSummary['debits'][$categoryId][] = $tmp;
      $debitInit = true;
      }

    // get the total credits for this category/month
    if(isset($creditPerCategoryPerMonth[$categoryId][$date])) {
      $credit = $creditPerCategoryPerMonth[$categoryId][$date];
      }
     else {
      $credit = 0;
     }
    // add the value to the array
    $tmp['total'] = $credit;
    $tmp['date'] = $date;
    if($tmp['total'] != 0 || $creditInit) {
      $spendingSummary['credits'][$categoryId][] = $tmp;
      $creditInit = true;
      }
    }
  }

// if no data, we go to the wizard page
$errorFlag = false;
if($context->totalItemCount < 1) {
  // set the error tag
  $context->error = 'error_NoTransactionData';
  // redirect to wizard
  $context->nextAction = 'errorDisplay';
  $context->redirect('errorDisplay');
  return;
  }

?>