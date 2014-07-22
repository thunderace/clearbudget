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
setcookie('homePage', 'overview-simple', time()+(3600*720), "/"); // expire in 30 days

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

// foreach category, compute the dispersion and then the derivate to get the trend ratio
// formula is
// b = n(Sum(x*y))-(Sum(x)*Sum(y)) / n(Sum(x^2)-(Sum(x)^2)
// a = Avg(y)-b(Avg(x))
// line equation would be: Y = a+bX
foreach($spendingSummary as $type=>$transactions) {
  foreach($transactions as $id=>$data) {
   class_debug::addMsg(__FILE__, 'Cat '.$id, DEBUGDEBUG);
   $sumX = 0;
   $sumX2 = 0;
   $sumY = 0;
   $count = 0;
   $sumXY = 0;
   $first = true;
   $ratios = array();
   $previous = 0;
   foreach($data as $item) {
     $midRatio = 0;
     $count++;
     $sumY += $item['total'];
     $sumX += $count;
     $sumX2 += ($count*$count);
     $sumXY += ($item['total']*$count);
     if(!$first) {
       if($previous!=0) $midRatio = $item['total']/$previous;
       else $midRatio = 0;
       $ratios[] = $midRatio;
      }
     $first = false;
     $previous = $item['total'];
     class_debug::addMsg(__FILE__, "x=$count, y={$item['total']} sumX=$sumX, sumX2=$sumX2, sumY=$sumY, sumXY=$sumXY, midRatio=$midRatio", DEBUGDEBUG);
     }
   // average the ratios
   if(count($ratios) > 0) $ratio = array_sum($ratios) / count($ratios);
   else $ratio = 0;
   $ratio = 100*(1-$ratio);
   $categoryStats[$type][$id]['ratio'] = sprintf("%.2f", $ratio);
   $categoryStats[$type][$id]['ratioFormated'] = sprintf("%+.2f", $ratio);
   // get the median equation from the least square method ("methode des moindres carres" in French)
   $denom = (($count*$sumX2)-($sumX*$sumX));
   if($denom != 0) $b = (($count*$sumXY)-($sumX*$sumY)) / (($count*$sumX2)-($sumX*$sumX));
   else $b = 0;
   $a = ($sumY/$count)-($b*($sumX/$count));
   $categoryStats[$type][$id]['a'] = $a;
   $categoryStats[$type][$id]['b'] = $b;
   class_debug::addMsg(__FILE__, "a=$a, b=$b, ratio=$ratio", DEBUGDEBUG);
   $y1 = $a + $b*1;
   $y2 = $a + $b*2;
   // TODO: find ratio maybe from sin of angle...
   //$trend = 1+abs($y2/$y1);//(1-sqrt(pow($y2-$y1,2)));
   //if($b<0) $trend = -$trend;
   //$categoryStats[$type][$id]['trend'] = sprintf("%+.2f", $trend);
   $categoryStats[$type][$id]['lastMonthTotal'] = sprintf("%.2f", $item['total']);
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