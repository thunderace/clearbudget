<?php
/**
* factory class for transaction objects
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
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

/**
* factory class for transaction objects 
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_transactionFactory {
  /**
	* @var object Hold the instance of the current class
	* @access private
	* @static	
	*/
  private static $_instance = null;
  /**
	* @var array array of transactions objects
	* @access public
	*/
  public $transactions = null;
  
  /**
  * Returns a transactions objects 
  *   
  * @return void
  * @access public
  */
  public function deleteTransaction($id) {
    // get a db handler
    $db = class_db::getInstance();
    // add the where clause
    $db->addWhere('id', '=', $id);
    // delete the record
    $db->delete('t_items');
    }
    
  /**
  * Returns a transactions objects 
  *   
  * @return object a transaction object
  * @access public
  */
  public function & getTransaction($id = null) {
    // if id is given, create and load transation id
    // else create an empty transation object
    if($id != null) {
      $transaction = new class_transaction($id);
      class_debug::addMsg(__FILE__, 'Created new transaction object for id: '.$id, DEBUGINFO);
      }
    else {
      $transaction = new class_transaction();
      class_debug::addMsg(__FILE__, 'Getting new transaction object', DEBUGINFO);
      }
    return $transaction;
    }
  
  /**
  * Returns a transactions objects for a given parent 
  *   
  * @return object a transaction object
  * @access public
  */
  public function getChildTransactions($parentId) {
    $transactions = array();
    // internal counter
    $trnCounter = 0;
    if($parentId > 0) {
      // get a DB object
      $db = class_db::getInstance();
      $db->addFields('id');
      $db->addWhere('parentId', '=', $parentId);
      // load the transaction ids
      $db->select('t_items');
      $results = $db->fetchAllRows();
      if($results != false) {
        // foreach result, load the object
        foreach($results as $result) {
          $id = $result['id'];
          // create a new transaction object
          $transactions[$id] = new class_transaction($id);
          $trnCounter ++;
          }
        }
      }
    class_debug::addMsg(__FILE__, 'created '.$trnCounter.' transaction objects for parent id '.$parentId, DEBUGINFO);
    return $transactions;
    }

  /**
  * Returns transactions objects for a given import Id 
  *   
  * @return object a transaction object
  * @access public
  */
  public function getImportedTransactions($importId) {
    $transactions = array();
    // internal counter
    $trnCounter = 0;
    if($importId > 0) {
      // get a DB object
      $db = class_db::getInstance();
      $db->addFields('id');
      $db->addWhere('importId', '=', $importId); // add the import id
      $db->addWhere('parentId', '=', "0"); // where it does not have a parent = raw import
      // load the transaction ids
      $db->select('t_items');
      $results = $db->fetchAllRows();
      if($results != false) {
        // foreach result, load the object
        foreach($results as $result) {
          $id = $result['id'];
          // create a new transaction object
          $transactions[$id] = new class_transaction($id);
          $trnCounter ++;
          }
        }
      }
    class_debug::addMsg(__FILE__, 'created '.$trnCounter.' transaction objects for import id '.$importId, DEBUGINFO);
    return $transactions;
    }
  
  /**
  * Provide an instance of the current class 
  *   
  * Implementation of the singleton pattern
  *   
  * @return object An instance of this class
  * @access public
  * @static
  */  
  public static function getInstance() {
    if(is_null(self::$_instance)) {
      self::$_instance = new self();
      }
    return self::$_instance;
    } // getInstance()

  /**
  * class constructor 
  * access to this class is private as to implement the singleton pattern.
  *
  * @return void
  * @access private
  */
  private function __construct() {
    } // __construct()
  }
?>