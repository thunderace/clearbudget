<?php
/**
* Detailed status page for the selected report date range
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

// if there are valid data we get all reporting data and build graph URL (to Google Charts)
$dates = $report->dates;
$categories = $report->categories;
$categoryColor = $report->categoryColor;
$debitTotalPerCategory = $report->debitTotalPerCategory;
$creditTotalPerCategory = $report->creditTotalPerCategory;
$debitsPctPerCategory = $report->debitPctPerCategory;
$creditsPctPerCategory = $report->creditPctPerCategory;
$totalDebitsPerMonth = $report->debitTotalPerMonth;
$totalCreditsPerMonth = $report->creditTotalPerMonth;
$totalDebits = $report->totalDebits;
$totalCredits = $report->totalCredits;
$debitsPctPerMonth = $report->debitPctPerMonth;
$creditsPctPerMonth = $report->creditPctPerMonth;
$debitPerCategoryPerMonth = $report->debitPerCategoryPerMonth;
$creditPerCategoryPerMonth = $report->creditPerCategoryPerMonth;
$colCount = count($dates) + 4; // 4 for the extra columns (category, total, %, avg)

// make sure the homePage is set to this page
setcookie('homePage', 'overview-detailed', time()+(3600*720), "/"); // expire in 30 days

// compute the total of categoryMaxAmountPerMonth
$totalMaxAmountPerMonth = 0;
foreach($report->categoryMaxAmountPerMonth as $amount) {
  $totalMaxAmountPerMonth += $amount;
  }

// tag cloud per category spending
// top 10 categories
// sort by value
if(count($categories) > 1 && is_array($debitsPctPerCategory)) {
  $cloudTop10 = $debitsPctPerCategory;
  asort($cloudTop10, SORT_NUMERIC);
  $cloudTop10 = array_reverse($cloudTop10, true);
  // redo a nice array sorted by value and with the category name
  $tag = count($cloudTop10);
  if($tag > 10) $tag = 10;
  foreach($cloudTop10 as $id=>$pct) {
    $cloud[$report->getCategoryName($id)]['tag'] = $tag;
    $cloud[$report->getCategoryName($id)]['catId'] = $id;
    $tag--;
    if($tag<1) $tag = 1;
    }
  // order this array by alphabetical order
  ksort($cloud, SORT_STRING);
  }
else {
  $cloud = false;
  }

// monthly spending VS credits
$chart = new GoogleChart('lc', '', 350, 90);
$chart->addColor('97EE14');
$chart->addColor('FF2725');
$chart->addColor('0027EE');
$chart->addColor('AAAAAA');
$maxVal = 0;
$minVal = 10000000000; 
// add the x axis
foreach($dates as $date) {
  $month = explode('-', $date);
  $chart->addxAxis($month[0]);
  }
// add the credit
foreach($dates as $date) {  
  if(isset($totalCreditsPerMonth[$date])) $credit = $totalCreditsPerMonth[$date];
  else $credit = 0;
  $chart->addValue($credit);
  if($credit>$maxVal) $maxVal = $credit;
  if($credit<$minVal) $minVal = $credit;
  }
$chart->addLineStyle('2,2,0');
$chart->addValueSerie();
// add the debit
foreach($dates as $date) {  
  if(isset($totalDebitsPerMonth[$date])) $debit = $totalDebitsPerMonth[$date];
  else $debit = 0;  
  $chart->addValue($debit);
  if($debit>$maxVal) $maxVal = $debit;
  if($debit<$minVal) $minVal = $debit;
  }
$chart->addLineStyle('2,2,0');
$chart->addValueSerie();
// add the total per month
foreach($dates as $date) {  
  if(isset($totalDebitsPerMonth[$date])) $debit = $totalDebitsPerMonth[$date];
  else $debit = 0;
  if(isset($totalCreditsPerMonth[$date])) $credit = $totalCreditsPerMonth[$date];
  else $credit = 0;
  $val = $credit - $debit;
  $chart->addValue($val);
  if($val>$maxVal) $maxVal = $val;
  if($val<$minVal) $minVal = $val;
  }
$chart->addLineStyle('1,2,2');
$chart->addValueSerie();

// add the 0 line if minVal < 0
if($minVal < 0) {
  foreach($dates as $date) {  
    $chart->addValue("0");
    }
  $chart->addLineStyle('1,1,1');
  $chart->addValueSerie();
  }
$chart->addScaling($minVal, $maxVal);
// build the y axis
if($minVal < $maxVal) $intervals = ($maxVal-$minVal) / 4;
else $intervals = $maxVal / 4;
$yAxis = '';
for($i=$minVal; $i<=$maxVal-$intervals; $i=$i+$intervals) {
  // detect the 0
  if($i < 0 && $i+$intervals > 0) $yAxis .= '0|';
  $yAxis .= $report->formatNumber($i).'|';
  }
$yAxis .= $report->formatNumber($maxVal);
$chart->addyAxis($yAxis);
$url5 = $chart->getURL();
?>