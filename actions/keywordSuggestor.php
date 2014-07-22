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

// the internal array of unique words
$words = array();
// the cloud array
$cloud = array();
// the number of transactions retreived
$numTransactions = 0;

// get all existing keyword for that user
$keywords = array();
$db->addFields('keyword');
$db->select('t_keywords', true);
$res = $db->fetchAllRows();
foreach($res as $keyword) {
  $keywords[] = $keyword['keyword'];
  }

// get all 'words' in the transaction table
$db->addFields('payee');
$db->addFields('memo');
$db->addLimits(0, KEYWORDSUGGESTIONNUMTRANSACTION);
$db->select('v_suggestions_items');

// builds an array with all words, with the word count
while(($trnWords = $db->fetchRow()) !== false) {
  // increment the transaction counter
  $numTransactions++;
  // a flag to tell us if we already added this transaction for this word
  $transactionAdded = null;
  // go through all fields we selected
  foreach($trnWords as $trnWordField) {
    // parse the payee field
    $tmp = explode(' ', $trnWordField);
    foreach($tmp as $word) {
      // make the word lower case (and make sure we do not have extra white space)
      $word = strtolower(trim($word));
      // if the word is more than 3 caracter
      // and if it was not already counted for that transaction
      // and if it is not already a keyword
      if(strlen($word)>3 && !isset($transactionAdded[$word]) && !in_array($word, $keywords)) {
        if(isset($words[$word])) {
          // add one more occurence of this work in this new transaction
          $words[$word]++;
          }
        else {
          // first time we see this word
          $words[$word] = 1; 
          }
        $transactionAdded[$word] = true;
        }
      }
    }
  }

class_debug::addMsg(__FILE__, 'Retreived '.$numTransactions.' transactions from DB', DEBUGDEBUG);

// sort the array by transaction count
asort($words, SORT_NUMERIC);
// reverse so biggest are first
$words = array_reverse($words, true);

$tag = 10;
$counter = 0;
foreach($words as $word=>$count) {
  // percentage of transaction with this word
  $pct = sprintf("%2.0f", $count/$numTransactions*100);
  // we show only the words that are in 10% or more of transactions
  if($pct > KEYWORDSUGGESTIONTHRESHOLD) {
    $cloud[$word]['tag'] = $tag;
    $cloud[$word]['id'] = $counter++;
    $cloud[$word]['msg'] = sprintf($keys->text_suggestionSuccess, $pct);
    $tag--;
    if($tag<0) $tag = 0;
    }
  }
// sort the cloud array alphabetically (more human friendly)
ksort($cloud);
?>