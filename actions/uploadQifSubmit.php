<?php
/**
* Parse a given QIF file and load the transactions in the DB.
* QIF format is line based with each line being one of the following:
* 
* ^ end of items, D date, T amount, C cleared, P payee, M memo, A address, L category, S cartegory split, E memo split, $ amount split
*
* As this is a very poor format, we are loosy as well and we load what we can parse, everything is ignored
* Date management is a nightmare so we will load as we can and offer the possibility to edit the dates
*  
*  @author       Fabrice Douteaud <admin@clearbudget.net>
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
if(!file_exists($_FILES["datafile"]['tmp_name'])) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQifFile;
  return;
  }

// a bit debuging as to check the locale parameters (important for date parsing)
class_debug::addMsg(__FILE__, 'Parsing QIF dates as '.$context->QIFLocaleType, DEBUGDEBUG);
$dateFormat = $context->QIFLocaleType;

// be sure we detect the right line ending
@ini_set('auto_detect_line_endings', true);
  
// load the file in an array
$lines = @file($_FILES["datafile"]['tmp_name']);
// if loading was unsuccessful, we exit with error (file type not correct most probably)
if($lines == false) {
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQifFile2;
  return;
  }

// counter of lines processed
$lineCounter = 1;
// counter of item inserted successfuly
$insertCounter = 0;  
// counter of item not inserted because duplicated
$duplicateCounter = 0;
// counter of error during insert (not enough data, not valid...)
$errorCounter = 0;
// array to hold and compare magic number (but still allows n equal fields per file)
$magics = array();
// get today's date
$today = date("Y-m-d");

// insert a record in the batch records
$db->addFields('originalFileName', $_FILES['datafile']['name']);
$db->addFields('importCount', 0);
$db->addFields('importDuplicate', 0);
$db->addFields('importDate', $today);
$db->addFields('importType', IMPORTQIFFILE);
$importId = $db->insert('t_imports');

// get a new transaction object
$transaction = & $transactionFactory->getTransaction();
// go line by line to process the file
foreach($lines as $line) {
  // increment the line counter
  $lineCounter++;
  if($line != '') {
    
    // get the line identifier
    $id = substr($line, 0, 1);
    // get the content of that line
    $content = trim(substr($line, 1, strlen($line)));
    switch ($id) {
      case '^':
        set_time_limit(10); 
        // add the null category
        $transaction->category = 1;
        // add the import batch ID
        $transaction->importId = $importId;
        // add the current date
        $transaction->createDate = $today;
        // create the magic string
        $magic = $transaction->getCheckSum();
        $magics[$magic][] = $lineCounter;
        $magicCounter = count($magics[$magic]);
        if($magicCounter > 1) {
          // it is a duplicate record so we add the sequence number
          $transaction->getCheckSum($magicCounter);
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
          // remove the magic number for that transaction from the array
          $magics[$magic] = null;
          }
        // null out the existing object
        $transaction = null;
        // get a new transaction object
        $transaction = & $transactionFactory->getTransaction();
      break;
      case 'D':
        $transaction->operationDate = formatQIFDate($content, $invalidDateFormat);
      break;
      case 'T':
        if($content == '') continue;
        $st = substr($content, 0, 1);
        if($st == '-') {
          $transaction->debit = '1';
          $content = substr($content, 1, strlen($content)-1);
          }
        $transaction->amount = cleanAmount($content);
      break;
      case 'C':
        $transaction->cleared = $content;
      break;
      case 'P':
        $transaction->payee = clean($content);
      break;
      case 'M':
        $transaction->memo = clean($content);
      break;
      default:
      break;
      } // switch
    } // if
  } // foreach

if(!$invalidDateFormat && ($insertCounter > 0 || $duplicateCounter > 0)) {
  // update the import record with the data
  $db->addFields('importCount', $insertCounter);
  $db->addFields('importDuplicate', $duplicateCounter);
  $db->addWhere('id', '=', $importId);
  $db->update('t_imports');

  $return['msg'] = sprintf($_FILES['datafile']['name'].' - '.$keys->text_importedTransactions.': %d, '.$keys->text_duplicatedRecords.': %d, '.$keys->error_GenericError.': %d', $insertCounter, $duplicateCounter, $errorCounter);
  $return['error'] = false;
  $return['importCount'] = $insertCounter;
  $return['importId'] = $importId;
  class_debug::addMsg(__FILE__, 'Imported '.$insertCounter.' items with '.$duplicateCounter.' duplicates', DEBUGINFO);
  }
elseif(!$invalidDateFormat && $insertCounter == 0 && $duplicateCounter == 0) {
  // remove everything as this is not a valid import (only errors)
  $db->addWhere('id', '=', $importId);
  $db->delete('t_imports');
  $db->addWhere('importId', '=', $importId);
  $db->delete('t_items');
  class_debug::addMsg(__FILE__, 'Import failed - only errors', DEBUGERROR);
  $return['error'] = true;
  $return['msg'] = $errorCounter.' '.$keys->error_GenericError;
  }
else {
  // remove everything as this is not a valid import (dates are malformed)
  $db->addWhere('id', '=', $importId);
  $db->delete('t_imports');
  $db->addWhere('importId', '=', $importId);
  $db->delete('t_items');
  class_debug::addMsg(__FILE__, 'Import failed - dates are malformed', DEBUGERROR);
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQifFile4;
  }
?>