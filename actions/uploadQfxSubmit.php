<?php
/**
* Parse a given QFX file and load the transactions in the DB.
* QFX format is again a strange format...looks like XML but it is not, it is SGML and I did not
* find any parser for it!
* This parser is only designed to get the Bank transactions statements
* 
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
global $context;
$db = class_db::getInstance();
$transactionFactory = class_transactionFactory::getInstance();

// a flag that tells us we have an invalid date format
$invalidDateFormat = false;

// First we check if we have the correct data from the client
// check if we get there from a POST with a file uploaded
if($_SERVER["REQUEST_METHOD"] != 'POST' || !isset($_FILES["datafile"])) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperCall;
  class_debug::addMsg(__FILE__, 'called to upload file not in POST and no file is present', DEBUGINFO);
  return;
  }

// check if the file has been uploaded correctly
if($_FILES['datafile']['size']==0 || !file_exists($_FILES["datafile"]['tmp_name'])) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQfxFile;
  return;
  }

// do some cleanup as to be able to parse this stuff correctly
$file = file_get_contents($_FILES["datafile"]['tmp_name']);
// add a line break before each XML start tag - a bit brute force but should work on most files
$file = str_replace('<', "\n<", $file);
// put back the content to the same file (so we let php do the cleanup for us)
file_put_contents($_FILES["datafile"]['tmp_name'], $file);

// be sure we detect the right line ending
@ini_set('auto_detect_line_endings', true);

// load the file in an array
$lines = @file($_FILES["datafile"]['tmp_name'], FILE_SKIP_EMPTY_LINES);
// if loading was unsuccessful, we exit with error (file type not correct most probably)
if($lines == false) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQfxFile2;
  return;
  }

// check if the first line starts with '<OFX>' which should be the case in a valid QFX file
// we do not do that anymore as it is a bit more tricky (Extra white space or tab might be there as well)
/*
if(strtoupper(substr($lines[0], 0, 5)) != '<OFX>') {
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQifFile3;
  return;
  }
*/

// counter of lines processed
$lineCounter = 1;
// counter of item inserted successfuly
$insertCounter = 0;  
// counter of item not inserted because duplicated
$duplicateCounter = 0;
// a flag to tell if the list of transactions has been detected
$inTransactionsList = false;
// get today's date
$today = date("Y-m-d");

// insert a record in the batch records
$db->addFields('originalFileName', $_FILES['datafile']['name']);
$db->addFields('importCount', 0);
$db->addFields('importDuplicate', 0);
$db->addFields('importDate', $today);
$db->addFields('importType', IMPORTQFXFILE);
$importId = $db->insert('t_imports');

// get a new transaction object
$transaction = & $transactionFactory->getTransaction();

// define an alternate payee string. In case records do not indicate the <NAME> tag,
//try to replace with the altName for check or by the <MEMO> tage if any
$altName = null;

// go line by line to process the file
foreach($lines as $line) {
  $lineCounter++;
  if($line != '') {
    // remove any white space
    $line = clean($line);
    // look for the XML end tag
    $pos1 = strpos($line, '>');
    // if none is found, this line is discarded
    if($pos1 === false) continue;
    // get the identifier
    $id = strtoupper(substr($line, 0, $pos1+1));
    class_debug::addMsg(__FILE__, 'id is '.$id, DEBUGDEBUG);
    // get the content of that line
    $content = substr($line, $pos1+1, strlen($line));
    //parseQFX(&$item, &$content, &$inTransactionsList, &$db, &$insertCounter, &$duplicateCounter);
    switch ($id) {
      case '<BANKTRANLIST>':
        // we are in the bank transaction list
        $inTransactionsList = true;
        class_debug::addMsg(__FILE__, 'Transactions list started', DEBUGDEBUG);
      break;
       case '</BANKTRANLIST>':
        $inTransactionsList = false;
        class_debug::addMsg(__FILE__, 'Transactions list ended', DEBUGDEBUG);
      break;
      case '</STMTTRN>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        // make sure we do not time out
        set_time_limit(10); 
        // add the null category
        $transaction->category = '1';
        // add the import batch ID
        $transaction->importId = $importId;
        // add the current date
        $transaction->createDate = $today;
        // add the cleared flag as *
        $transaction->cleared = '*';

        // try to find the payee
        if($transaction->payee == '') {
            if($altName!=null) $transaction->payee = 'Check: '.$altName;
            elseif($transaction->memo!='') $transaction->payee = $transaction->memo;
            else $transaction->payee = 'unknown';
            }
        // save the transaction data
        $result = $transaction->save();
        // process the result
        if($result == 1) {
          // everything is ok, increment the insert counter
          $insertCounter++;
          }
        else {
          // if duplicates, increment the duplicate counter
          if($result == -1) {
            $duplicateCounter++;
            }
          // if error (missing mandatory fields), increment the error counter
          if($result == -2) {
            $errorCounter++;
            }
          }
        // null out the existing object
        $transaction = null;
        // reset the alternate payee string
        $altName = null;
        // get a new transaction object
        $transaction = & $transactionFactory->getTransaction();
      break;
      case '<FITID>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        $transaction->checksum = $content;
      break;
      case '<DTUSER>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        $transaction->operationDate = formatQFXDate($content, $invalidDateFormat);
      break;
      case '<DTPOSTED>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        $transaction->operationDate = formatQFXDate($content, $invalidDateFormat);
      break;
      case '<TRNAMT>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        $transaction->debit = '0';
        if($content == '') continue;
        $st = substr($content, 0, 1);
        if($st == '-') {
          $transaction->debit = '1';
          $content = substr($content, 1, strlen($content)-1);
          }
        $transaction->amount = cleanAmount($content);
      break;
      case '<CHECKNUM>':
          if(!$inTransactionsList) continue;
          // this is an alternate payee, in case of checks, there is no <NAME> tag but should be a <CHECKNUM> tag
          $altName = clean($content);
      break;
      case '<NAME>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        $transaction->payee = clean($content);
      break;
      case '<MEMO>':
        // if we are not in the bank transactions list, we do not do anything
        if(!$inTransactionsList) continue;
        $transaction->memo = clean($content);
      break;
      default:
        // well...lots of fields we do not care of! If only we could have valid sample of QFX files, that would help :-)
      break;
      } // switch 
    } // if
  } // foreach

// if date was valid and we inserted something
if(!$invalidDateFormat && ($insertCounter > 0 || $duplicateCounter > 0) ) {
  // update the import record with the data
  $db->addFields('importCount', $insertCounter);
  $db->addFields('importDuplicate', $duplicateCounter);
  $db->addWhere('id', '=', $importId);
  $db->update('t_imports');

  $return['msg'] = sprintf($_FILES['datafile']['name'].' - '.$keys->text_importedTransactions.': %d, '.$keys->text_duplicatedRecords.': %d', $insertCounter, $duplicateCounter);
  $return['error'] = false;
  $return['importCount'] = $insertCounter;
  $return['importId'] = $importId;
  }
else {
  // remove everything as this is not a valid import (dates are malformed)
  $db->addWhere('id', '=', $importId);
  $db->delete('t_imports');
  $db->addWhere('importId', '=', $importId);
  $db->delete('t_items');

  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQfxFile4;
  }
?>