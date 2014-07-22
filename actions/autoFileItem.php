<?php
/**
* Auto file the transaction from the set of user keywords
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
$db = class_db::getInstance();
$transactionFactory = class_transactionFactory::getInstance();

$updateCount = 0;
$keywords = null;

// get all the keywords
$db->addFields('keyword');
$db->addFields('category');
$db->select('t_keywords');
while(($item = $db->fetchRow()) !== false) {
  $keywords[strtolower(trim($item['keyword']))] = $item['category'];
  }
class_debug::addMsg(__FILE__, 'Got '.count($keywords).' keywords', DEBUGDEBUG);

if(count($keywords) == 0) return;

// get all the unfiled items
$db->addFields('id');
$db->addFields('payee');
$db->addFields('memo');
$db->select('v_items_unfiled');
$items = $db->fetchAllRows();
class_debug::addMsg(__FILE__, 'Searching over '.count($items).' transactions', DEBUGINFO);
// foreach item, check if a keyword is matching, if yes, categorize this item accordingly
foreach($items as $item) {
  set_time_limit(30);
  $haystack = $item['payee'];
  $itemId = $item['id'];
  class_debug::addMsg(__FILE__, 'Checking transaction id: '.$itemId, DEBUGDEBUG);
  // go through each keywords
  foreach($keywords as $keyword=>$category) {
    class_debug::addMsg(__FILE__, 'Looking for keyword: '.$keyword, DEBUGDEBUG);
    // check if keyword is present in the item payee
    $test = strpos($haystack, $keyword);
    if($test !== false) {
      class_debug::addMsg(__FILE__, 'keyword found', DEBUGDEBUG);
      $transaction = & $transactionFactory->getTransaction($itemId);
      $transaction->category = $category;
      $transaction->save();
      $updateCount++;
      break;
      }
    }
  }
class_debug::addMsg(__FILE__, 'Updated '.$updateCount.' records', DEBUGINFO);
?>