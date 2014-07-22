<?php
/**
* Parse a given CSV file and load the transactions in the DB.
* Default CSV line format is:
* PAYEE,DATE,MEMO,COMMENT,DEBIT,AMOUNT
* 
* A CSVFieldsInFirstLine tells if the first line define the fields list or not. If yes, the field will be interpreted as indicated in the first line
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

// default the return value
$return['error'] = true;
$return['msg'] = $keys->error_GenericError;

// load the CSVFieldsInFirstLine flag
$CSVFieldsInFirstLine = $context->CSVFieldsInFirstLine;
// a flag telling if the field format is not valid
$invalidLineFormat = false;
// a flag telling if a line is not valid
$invalidLine = false;

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
  $return['msg'] = $keys->error_ImproperCall;
  class_debug::addMsg(__FILE__, $keys->error_ImproperCall.' - no file present', DEBUGERROR);
  return;
  }

// be sure we detect the right line ending
@ini_set('auto_detect_line_endings', true);

// a flag that tells us we have an invalid date format
$invalidDateFormat = false;
// counter of lines processed
$lineCounter = 0;
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
// get the CSV fields
$CSVfields = explode(',', CSVLINEFORMAT);
$numFields = count($CSVfields);

// insert a record in the batch records
$db->addFields('originalFileName', $_FILES['datafile']['name']);
$db->addFields('importCount', 0);
$db->addFields('importDuplicate', 0);
$db->addFields('importDate', $today);
$db->addFields('importType', IMPORTCSVFILE);
$importId = $db->insert('t_imports');

// open the imported file
$handle = fopen($_FILES["datafile"]['tmp_name'], "r");

// Compute the column-field relation
// if $CSVFieldsInFirstLine is true, load the first line off the array and parse it as field definitions
// else, get the field definitions from the default
if($CSVFieldsInFirstLine == '1') {
  $data = fgetcsv($handle, 0, ',', '"');
  // check if the correct number of fields is given
  if(count($data) != $numFields) {
    $return['error'] = true;
    $return['msg'] = $keys->error_CSVInvalidFieldsList.' - '.$keys->text_CSVFieldsDefaultOrder;
    return;
    } 
  // transform everything to lowercase
  $dataToLower = array();
  foreach($data as $index=>$field) {
    $dataToLower[$index] = strtolower($field);
    }

  // check if these are the fields we expect
  foreach($CSVfields as $CSVField) {
    // if a field is given but not in the fields expected, throw an error
    if(!in_array(strtolower($CSVField), $dataToLower)) {
      $return['error'] = true;
      $return['msg'] = sprintf($keys->error_CSVMissingMandatoryField, $CSVField).' - '.$keys->text_CSVFieldsDefaultOrder;
      class_debug::addMsg(__FILE__, $keys->error_CSVMissingMandatoryField.' '.$CSVField, DEBUGERROR);
      // remove any imported data
      $db->addWhere('id', '=', $importId);
      $db->delete('t_imports');
      $db->addWhere('importId', '=', $importId);
      $db->delete('t_items');
      return;
      }
    }
  // save the field order
  $fields = $data;
  }
else {
  // save the field order from the default
  $fields = $CSVfields;
  }

// load the file line by line
while(($data = fgetcsv($handle, 0, ',', '"')) !== FALSE) {
  $lineCounter++;
  if(count($data) != $numFields) {
    class_debug::addMsg(__FILE__, 'line '.$lineCounter.' has '.count($data).' items, expecting: '.$numFields, DEBUGINFO);
    $errorCounter ++;
    }
  else {
    // get a new transaction object
    $transaction = & $transactionFactory->getTransaction();
    // instantiate the object with the CSV values
    foreach($data as $index=>$value) {
      switch($fields[$index]) {
        case 'payee': $transaction->payee = clean($value); break;
        case 'memo': $transaction->memo = clean($value); break;
        break;
        case 'operationDate':
          // re-use the function for QIF updload as this is the same mess
          $transaction->operationDate = formatQIFDate($value, $invalidDateFormat);
        break;
        case 'amount':
          // try to detect if it is a debit or a credit, default will be debit
          $st = substr($value, 0, 1);
          if($st == '-') {
            $transaction->debit = '1';
            $value = substr($value, 1, strlen($value)-1);
            }
          elseif($st == '+') {
            $transaction->debit = '0';
            $value = substr($value, 1, strlen($value)-1);
            }
          else $transaction->debit = '1';
          $transaction->amount = cleanAmount($value);
        break;
        }
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
    }
  }

if(!$invalidDateFormat && $insertCounter > 0) {
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
elseif(!$invalidDateFormat && $insertCounter == 0 && $duplicateCounter > 0) {
  // remove everything as this is not a valid import (only duplicates)
  $db->addWhere('id', '=', $importId);
  $db->delete('t_imports');
  $db->addWhere('importId', '=', $importId);
  $db->delete('t_items');
  class_debug::addMsg(__FILE__, 'Import failed - only duplicates', DEBUGERROR);
  $return['error'] = true;
  $return['msg'] = $keys->error_ImproperQifFile5;
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