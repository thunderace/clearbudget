<?php
/**
* gets the data necessary to build the graphs
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

// instantiate the necessary objects
$reportSettings = new class_reportsettings();
// get the active report settings
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

// top 10 categories overall spending
$chart = new GoogleChart('p3', '', 500, 180); // $keys->graphTitle_topsDebitsOverall
if(count($debitTotalPerCategory) > 0) {
  // sort by value
  asort($debitTotalPerCategory, SORT_NUMERIC);
  $debitTotalPerCategory = array_reverse($debitTotalPerCategory, true);
  // keep only the top 10 items
  $topsDebitsPerCategory = array_slice($debitTotalPerCategory, 0, 10, true);
  // keep all other items
  $otherDebits = array_slice($debitTotalPerCategory, 10, null, true);
  // build the colors array
  foreach($topsDebitsPerCategory as $key=>$val) {
    $pctVal = $report->formatNumber($val / $totalDebits * 100);
    if($key>1) {
      $chart->addLegend($report->getCategoryName($key).' ('.$pctVal.'%)');
      $chart->addValue($pctVal);
      $chart->addColor($categoryColor[$key]);
      }
    else {
      $otherDebits[$key] = $val;
      }
    }
  // compute the overall value of all 'others'
  $tmpVal = 0;
  foreach($otherDebits as $key=>$val) {
    $tmpVal += $val;
    }
  $tmpVal = $report->formatNumber($tmpVal / $totalDebits * 100);
  $chart->addLegend('Others ('.$tmpVal.'%)');
  $chart->addValue($tmpVal);
  $chart->addColor('444444');
  $url3 = $chart->getURL();
  }
else {
  $url3 = 'style/blank.jpg';
  }

// top 10 categories monthly spending
$chart = new GoogleChart('bvs', '', 350, 160); //$keys->graphTitle_topsDebitsMonthly
if(isset($topsDebitsPerCategory)) {
  // reverse the array as to get the smallest first
  $topsDebitsPerCategory = array_reverse($topsDebitsPerCategory, true);
  // go through each item
  foreach($topsDebitsPerCategory as $category=>$val) {
    // if category is uncategorized we skip it
    if($category === 1) continue;
    // graph legend (category name)
    //$chart->addLegend($report->getCategoryName($category));
    // category color
    $chart->addColor($categoryColor[$category]);
    // initialize the other array
    $others['total'] = 0;
    // go through each month
    foreach($dates as $date) {    
      // check if there is spending for that month
      if(isset($debitPerCategoryPerMonth[$category][$date])) {
        if($totalDebitsPerMonth[$date]>0) {
          $pctVal = $report->formatNumber($debitPerCategoryPerMonth[$category][$date] / $totalDebitsPerMonth[$date] * 100);
          }
        else { 
          $pctVal = 0;
          }
        $chart->addValue($pctVal);
        @$others['total'][$date] += $pctVal;
        }
      else {
        $chart->addValue('0.0');
        @$others['total'][$date] += 0; 
        }
      }
    $chart->addValueSerie();
    }
  $chart->addColor('444444');
  //$chart->addLegend('Others');
  foreach($dates as $date) {
    if(isset($others['total'][$date])) $chart->addValue(100-$others['total'][$date]);
    else $chart->addValue(100);
    $month = explode('-', $date);
    $chart->addxAxis($month[0]);
    }
  $chart->addyAxis('0|20|40|60|80|100');
  $url4 = $chart->getURL();
  }
else {
  $url4 = 'style/blank.jpg';
  }

// top 10 categories compared to each other monthly
$chart = new GoogleChart('lc', '', 300, 160); //$keys->graphTitle_topsDebitsMonthly
if(isset($topsDebitsPerCategory)) {
  // reverse the array as to get the smallest first
  $topsDebitsPerCategory = array_reverse($topsDebitsPerCategory, true);
  $maxVal = 0;
  $minVal = 10000000000; 
  // go through each item
  foreach($topsDebitsPerCategory as $category=>$val) {
    // if category is uncategorized we skip it
    if($category === 1) continue;
    // graph legend (category name)
    //$chart->addLegend($report->getCategoryName($category));
    // category color
    $chart->addColor($categoryColor[$category]);
    // go through each month
    foreach($dates as $date) {
      // check if there is spending for that month
      if(isset($debitPerCategoryPerMonth[$category][$date])) {
        $val = $report->formatNumber($debitPerCategoryPerMonth[$category][$date]);
        $chart->addValue($val);
        // get the min and max val
        if($val>$maxVal) $maxVal = $val;
        if($val<$minVal) $minVal = $val;
        }
      else {
        $minVal = 0;
        $chart->addValue('0');
        }
      }
    $chart->addValueSerie();
    }
  //echo $minVal.' - '.$maxVal;
  $chart->addScaling($minVal, $maxVal);
  
  foreach($dates as $date) {
    $month = explode('-', $date);
    $chart->addxAxis($month[0]);
    }
  
  if($minVal < $maxVal) $intervals = ($maxVal - $minVal) / 4;
  else $intervals = $maxVal / 4;
  $yAxis = '';
  for($i=$minVal; $i<=$maxVal-$intervals; $i=$i+$intervals) {
    // detect the 0
    if($i < 0 && $i+$intervals > 0) $yAxis .= '0|';
    $yAxis .= $report->formatNumber($i).'|';
    }
  $yAxis .= $report->formatNumber($maxVal);
  
  $chart->addyAxis($yAxis);
  $url2 = $chart->getURL();
  }
else {
  $url2 = 'style/blank.jpg';
  }


?>