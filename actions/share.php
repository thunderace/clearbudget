<?php
/**
* Build the data to be shared
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

$db->addfields('count(id)');
$db->select('t_categories');
$rawData = $db->fetchRow();
$numOfCategories = $rawData["count(id)"];

$db->addfields('count(id)');
$db->select('t_keywords');
$rawData = $db->fetchRow();
$numOfKeywords = $rawData["count(id)"];

$db->addfields('language');
$db->addfields('currency');
$db->addWhere('id', '=', '1');
$db->select('t_settings');
$rawData = $db->fetchRow();
$language = $rawData["language"];
$currency = $rawData["currency"];

$db->addfields('count(id)');
$db->addWhere('debit', '=', '1');
$db->select('v_items');
$rawData = $db->fetchRow();
$numDebits = $rawData["count(id)"];

$db->addfields('count(id)');
$db->addWhere('debit', '=', '0');
$db->select('v_items');
$rawData = $db->fetchRow();
$numCredits = $rawData["count(id)"];

$db->addfields('total, limitMonth');
$db->addWhere('debit', '=', '0');
$db->select('v_total_amount');
while(($rawData = $db->fetchRow()) !== false) {
  if(isset($creditPerMonth[$rawData["limitMonth"]])) $creditPerMonth[$rawData["limitMonth"]] += $rawData["total"];
  else $creditPerMonth[$rawData["limitMonth"]] = $rawData["total"];
  }

$db->addfields('total, limitMonth');
$db->addWhere('debit', '=', '1');
$db->select('v_total_amount');
while(($rawData = $db->fetchRow()) !== false) {
  if(isset($debitPerMonth[$rawData["limitMonth"]])) $debitPerMonth[$rawData["limitMonth"]] += $rawData["total"];
  else $debitPerMonth[$rawData["limitMonth"]] = $rawData["total"];
  }


$db->addfields('uid');
$db->addWhere('id', '=', '1');
$db->select('t_settings');
$rawData = $db->fetchRow();
if($rawData["uid"] != '') $uid = $rawData["uid"];
else {
  $uid = uniqid("", true);
  $db->addFields('uid', $uid);
  $db->addWhere('id', '=', '1');
  $db->update('t_settings');
}

$userAgent = $_SERVER['HTTP_USER_AGENT'];
$data['userAgent'] = $userAgent;
$data['numOfCategories'] = $numOfCategories;
$data['numOfKeywords'] = $numOfKeywords;
$data['currency'] = $currency;
$data['language'] = $language;
$data['numDebits'] = $numDebits;
$data['numCredits'] = $numCredits;
$data['debitPerMonth'] = $debitPerMonth;
$data['creditPerMonth'] = $creditPerMonth;
$data['clearBudgetVersion'] = CB_VERSION;
$data = urlencode(json_encode($data));
?>