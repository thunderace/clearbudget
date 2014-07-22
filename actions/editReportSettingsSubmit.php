<?php
/**
* Edit or add a report settings
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
$reportSettings = new class_reportsettings();

// preset the return message to its defaults
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError;

// get the values from the user
$categories = $context->categories;
$id = clean($context->reportId);
$name = clean($context->name);
$type = clean($context->type);
$transactions = clean($context->transactions);
$range = clean($context->range);
$startDate = clean($context->startDate);
$endDate = clean($context->endDate);

// check the name - must be present, can be anything
if(!$name) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportWrongName;
  return;
  }
  
// type must be 'range' or 'custom', nothing else so far
if($type != 'range' && $type != 'custom') {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportPickType;
  return;
  }

// if type is range then range must be there and numeric
if($type == 'range' && (!$range || !is_numeric($range))) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportWrongRange;
  return;
  }

// valid range is between 0 and 12 - screen cannot fit more than 12 month!
if($type == 'range' && ($range > 12 || $range < 0)) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportRangeTooLarge;
  return;
  }

// if type is custom then start and end date must be specified
if($type == 'custom' && (!$startDate || !$endDate)) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportWrongDateRange;
  return;
  }
// dates are given
elseif($type == 'custom') {
  $t1 = strtotime($startDate);
  $t2 = strtotime($endDate);
  $maxRange = strtotime("-12 month", $t2);
  // max range cannot be over 12 month
  if($t1<$maxRange) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ReportWrongDateRange;
    return;
    }
  // start date cannot be after end date
  if($t1>$t2) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ReportStartDateAfterEndDate;
    return;
    }
  }

// the transactions type must be 'all' or 'debit' or 'credit'
if($transactions!='all' && $transactions!='debit' && $transactions!='credit') {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportPickDebitCredit;
  return;
  }
// if good then we set the internal variables
else {
  switch($transactions) {
    case 'all':
      $debit = 1;
      $credit = 1;
    break;
    case 'credit':
      $debit = "0";
      $credit = 1;
    break;
    case 'debit':
      $debit = 1;
      $credit = "0";
    break;
    }
  }

// categories must be defined
if(!$categories || !is_array($categories)) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportWrongCategories;
  return;
  }
else {
  $tmp = '';
  foreach($categories as $catid) {
    $tmp .= $catid.',';
    }
  $categories = $tmp;
  // remove the extra coma
  $categories = substr($categories, 0, strlen($categories)-1);
  // validate if we got only numbers
  $_categories = str_replace(',', '', $categories);
  if(!is_numeric($_categories)) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ReportWrongCategories;
    return;
    }
  }


// update the report setting object with these settings
if($id > 0) {
  $reportSettings->id = $id;
  $reportSettings->activeFlag = '1';
  }
else {
  $reportSettings->activeFlag = '0';
  }
$reportSettings->name = $name;
$reportSettings->minDate = $startDate;
$reportSettings->maxDate = $endDate;
$reportSettings->type = $type;
$reportSettings->range = $range;
$reportSettings->categories = $categories;
$reportSettings->debit = $debit;
$reportSettings->credit = $credit;

// test the query to see if constraints aren't too strong
$report = class_report::getInstance();
$numRows = $report->testQuery($reportSettings);
if($numRows>0) {
  // save the object to the DB
  $id = $reportSettings->updateReportSettings();
  // set this report to be the active one
  $reportSettings->setActiveReport($id);
  }
else {
  $return['error'] = true;
  $return['msg'] = $keys->error_ReportSettingsTooStrict;
  return;
  }

// return the success code
$return['error'] = false;
//$return['msg'] = $keys->success.$numRows.' (id: '.$id.' - name: '.$name.' - type: '.$type.' - range: '.$range.' - debit: '.$debit.' - credit:'.$credit.' - startdate: '.$startDate.' - endDate: '.$endDate.' - categories: '.$categories.')';
$return['msg'] = $keys->success;
?>