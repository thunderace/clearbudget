<?php
/**
* Load all transactions for a given category
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
$report = class_report::getInstance();
$reportSettings = new class_reportsettings();
$reportSettings->getReportSettings();
// build a category tree
foreach($report->categories as $id=>$name) {
  $categories[$id] = $report->getCategoryName($id);
  }


// if filter is set then we set the selected flters
$categorySelected = $context->categorySelected;
if($categorySelected === null) $categorySelected = false;
$monthSelected = $context->monthSelected;
if($monthSelected === null) $monthSelected = false;

// we need to reverse the month as sqlite store this in US format and we use EU format :-(
if($monthSelected!=false) {
  $monthSelected = explode('-', $monthSelected);
  $monthSelected = $monthSelected[1].'-'.$monthSelected[0].'-%';
  }
  
// get the debit flag
$debit = $context->debit;

// setup the query
function buildQuery($categorySelected, $debit, $monthSelected, &$db, &$reportSettings, &$report) {
  if($categorySelected != false) {
    if($monthSelected != false) $db->addWhere('operationDate', 'like', $monthSelected);
    else {
      class_debug::addMsg(__FILE__, 'adding constraint from report settings', DEBUGDEBUG);
      // add date constraints
      switch($reportSettings->type) {
        case 'range':
          $month = $report->getLimitMonthForRange($reportSettings->range);
          $db->addWhere('operationDate', '>=', $month);
        break;
        case 'custom':
          $db->addWhereBetween('operationDate', $reportSettings->minDate, $reportSettings->maxDate);
        break;
        }
      }
    $db->addWhere('category', '=', $categorySelected);
    }
  else {
    if($monthSelected != false) $db->addWhere('operationDate', 'like', $monthSelected);
    else {
      // add date constraints
      switch($reportSettings->type) {
        case 'range':
          $month = $report->getLimitMonthForRange($reportSettings->range);
          $db->addWhere('operationDate', '>=', $month);
        break;
        case 'custom':
          $db->addWhereBetween('operationDate', $reportSettings->minDate, $reportSettings->maxDate);
        break;
        }
      }
    }
  if($debit == "1" || $debit == "0") $db->addWhere('debit', '=', $debit);
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
  }

buildQuery($categorySelected, $debit, $monthSelected, $db, $reportSettings, $report);
if($categorySelected != false) $count = $db->select('v_items', true);
else $count = $db->select('v_items_unfiled', true);
$results = $db->fetchAllRows();

// get the category name if we have a category
$category = '';
if($categorySelected > 0) {
  $category = $report->getCategoryName($categorySelected);
  }
?>