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

$toDiv = $context->toDiv;
if($toDiv === null) $toDiv = false;

// get month/year in the DB
$availableMonthYear = $report->getAllMonthYear();

// build a category tree
foreach($report->categories as $id=>$name) {
  $categories[$id] = $report->getCategoryName($id);
  }

 // get the user input
$monthSelected = $context->monthSelected;
if($monthSelected === null) $monthSelected = false;
$debit = $context->debit;

// we need to reverse the month as sqlite store this in US format and we use EU format :-(
if($monthSelected!=false) {
  $date = utf8_encode(strftime('%b %Y', strtotime('01-'.$monthSelected)));
  $monthSelected = explode('-', $monthSelected);
  $monthSelected = $monthSelected[1].'-'.$monthSelected[0];
  }
else {
  $date = '';
  if(is_array($availableMonthYear) && count($availableMonthYear)>0) {
    $split = explode('-', $availableMonthYear[0]['month']);
    $year=$split[0];
    $month=$split[1];
    $monthSelected = $year.'-'.$month;
    }
  }

// setup the query
function buildQuery($monthSelected, $debit, &$db, &$report) {
  if($monthSelected != false) $db->addWhere('operationDate', 'like', $monthSelected.'-%');
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
  if($debit == "1" || $debit == "0") $db->addWhere('debit', '=', $debit);
  }

buildQuery($monthSelected, $debit, $db, $reportSettings, $report);
$count = $db->select('v_items', true);
$results = $db->fetchAllRows();

?>