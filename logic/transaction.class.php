<?php
/**
* File holding the transation
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
* Class define a transation object with its properties and methods
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_transaction {
  /**
	* @var integer binary based control flag to check mandatory fields
	* @access private	
	*/
  const INSERT_CONTROL_FLAG = 31;
  const UPDATE_CONTROL_FLAG = 8;
  /**
	* @var object Holds an instance of the DB object
	* @access private	
	*/
  private $db = null;
  /**
	* @var integer transaction id 
	* @access private	
	*/
  private $id = 0;
  /**
	* @var integer the current user id
	* @access private
	*/
  private $currentUser = 0;
  /**
	* @var integer transaction parent id 
	* @access private	
	*/
  private $parentId = '0';
  /**
	* @var boolean flag if parent 
	* @access private	
	*/
  private $isParent = '0';
  /**
	* @var float transaction amount 
	* @access private	
	*/
  private $amount = '0';
  /**
	* @var date transaction date 
	* @access private	
	*/
  private $operationDate = "";
  /**
	* @var string transaction payee 
	* @access private	
	*/
  private $payee = '';
  /**
	* @var string transaction memo 
	* @access private	
	*/
  private $memo = '';
  /**
	* @var boolean transaction clear flag (not used) 
	* @access private	
	*/
  private $cleared = '0';
  /**
	* @var integer transaction category 
	* @access private	
	*/
  private $category = '1';
  /**
	* @var date transaction record creation date 
	* @access private	
	*/
  private $createDate = "";
  /**
	* @var boolean transaction debit flag 
	* @access private	
	*/
  private $debit = '0';
  /**
	* @var string transaction unique checksum 
	* @access private	
	*/
  private $checkSum = '';
  /**
	* @var string transaction comments 
	* @access private	
	*/
  private $comments = '';
  /**
	* @var boolean transaction deletion flag 
	* @access private	
	*/
  private $deleteFlag = '0';
  /**
	* @var boolean transaction investment flag (not used)  
	* @access private	
	*/
  private $investmentFlag = '0';
  /**
	* @var integer transaction import id 
	* @access private	
	*/
  private $importId = '0';
  /**
	* @var integer the importer user id
	* @access private
	*/
  private $importedBy = '';
  /**
	* @var integer the last modifier user id
	* @access private
	*/
  private $modifiedBy = '';
  /**
	* @var boolean a flag telling if this transaction is editable by the user 
	* @access private	
	*/
  private $editable = true;
  /**
	* @var array list of fields that have been set via the __set method 
	* @access private	
	*/
  private $fieldsSet = array();
  
  /**
	* magic method to set the private variables 
	*     		
	* @param string the field name
	* @param mixed the field value
	* @return void   
	* @access public
	*/
  public function __set($field, $value) {
    class_debug::addMsg(__FILE__, 'Setting '.$field.' = '.$value, DEBUGDEBUG);
    switch($field) {
      case 'amount':
        if(is_numeric($value)) {
          $this->amount = trim($value);
          $this->fieldsSet[] = 'amount';      
          class_debug::addMsg(__FILE__, 'amount = '.$value, DEBUGDEBUG);
          }
      break;
      case 'payee':
        if(strlen($value) > 0) {
          $this->payee = trim(strtolower($value));
          $this->fieldsSet[] = 'payee';
          class_debug::addMsg(__FILE__, 'payee = '.$value, DEBUGDEBUG);
          }
      break;
      case 'importId': 
        if(is_numeric($value)) {
          $this->importId = $value;
          $this->fieldsSet[] = 'importId';
          class_debug::addMsg(__FILE__, 'importId = '.$value, DEBUGDEBUG);
          }
      break;
      case 'isParent': 
        if($value == '0' || $value == '1') {
          $this->isParent = $value;
          $this->fieldsSet[] = 'isParent';
          class_debug::addMsg(__FILE__, 'isParent = '.$value, DEBUGDEBUG);
          }
      break;
      case 'parentId': 
        if(is_numeric($value)) {
          $this->parentId = $value;
          $this->fieldsSet[] = 'parentId';
          class_debug::addMsg(__FILE__, 'parentId = '.$value, DEBUGDEBUG);
          }
      break;
      case 'operationDate':
        if(preg_match('/^(\d\d\d\d)-(\d\d?)-(\d\d?)$/', $value, $matches)) { 
          if(checkdate($matches[2], $matches[3], $matches[1])) {
            $this->operationDate = $value;
            $this->fieldsSet[] = 'operationDate';
            class_debug::addMsg(__FILE__, 'operationDate = '.$value, DEBUGDEBUG);
            }
          }
      break;
      case 'memo': 
        if(strlen($value) > 0) {
          $this->memo = trim(strtolower($value));
          $this->fieldsSet[] = 'memo';
          class_debug::addMsg(__FILE__, 'memo = '.$value, DEBUGDEBUG);
          }
      break;
      case 'cleared': 
        $this->cleared = $value;
        $this->fieldsSet[] = 'cleared';
        class_debug::addMsg(__FILE__, 'cleared = '.$value, DEBUGDEBUG);
      break;
      case 'createDate':
        if(preg_match('/^(\d\d\d\d)-(\d\d?)-(\d\d?)$/', $value, $matches)) { 
          if(checkdate($matches[2], $matches[3], $matches[1])) {
            $this->createDate = $value;
            $this->fieldsSet[] = 'createDate';
            class_debug::addMsg(__FILE__, 'createDate = '.$value, DEBUGDEBUG);
            }
          }
      break;
      case 'debit': 
        if($value == '0' || $value == '1') {
          $this->debit = $value;
          $this->fieldsSet[] = 'debit';
          class_debug::addMsg(__FILE__, 'debit = '.$value, DEBUGDEBUG);
          }
      break;
      case 'comments': 
        $this->comments = $value;
        $this->fieldsSet[] = 'comments';
        class_debug::addMsg(__FILE__, 'comments = '.$value, DEBUGDEBUG);
      break;
      case 'category': 
        if(strlen($value) > 0) {
          $this->category = $value;
          $this->fieldsSet[] = 'category';
          class_debug::addMsg(__FILE__, 'category = '.$value, DEBUGDEBUG);
          }
      break;
      case 'deleteFlag': 
        if($value == '0' || $value == '1') {
          $this->deleteFlag = $value;
          $this->fieldsSet[] = 'deleteFlag';
          class_debug::addMsg(__FILE__, 'deleteFlag = '.$value, DEBUGDEBUG);
          }
      break;
      case 'investmentFlag':
        if($value == '0' || $value == '1') { 
          $this->investmentFlag = $value;
          $this->fieldsSet[] = 'investmentFlag';
          class_debug::addMsg(__FILE__, 'investmentFlag = '.$value, DEBUGDEBUG);
          }
      break;
      case 'id':
        if($value == '0') { 
          $this->id = '0';
          $this->fieldsSet[] = 'id';
          class_debug::addMsg(__FILE__, 'id = 0', DEBUGDEBUG);
          }
      break;
      }
    }
    
  /**
	* magic method to access the private variables in read-only 
	*     		
	* @return mixed the value if it exists or null
	* @access public
	*/
  public function __get($field) {
    if(isset($this->$field)) {
      return $this->$field;
      }
    return null;
    }
  
  /**
	* checks if all mandatory fields are valid 
	*     		
	* @param string the query type (insert or update)
	* @return boolean true is ok, false otherwise   
	* @access private
	*/
  private function checkMandatoryFields($queryType) {
    // binary based control flag
    $controlFlag = 0;
    // return value default to false
    $return = false;
    
    // list all fields that have been set
    foreach($this->fieldsSet as $field) {
      class_debug::addMsg(__FILE__, 'controling flag for '.$field, DEBUGDEBUG);
      // build the control flag based on the data set by the user
      // Mandatory fields for Insert:
      // amount, operationDate, payee, checksum, importId
      // Mandatory fields for update:
      // checksum
      switch($field) {
        case 'amount': 
          $controlFlag = $controlFlag|1;
          class_debug::addMsg(__FILE__, 'updating control flag with '.$field, DEBUGDEBUG);
        break;
        case 'operationDate':
          $controlFlag = $controlFlag|2;
          class_debug::addMsg(__FILE__, 'updating control flag with '.$field, DEBUGDEBUG);
        break;
        case 'payee':
          $controlFlag = $controlFlag|4;
          class_debug::addMsg(__FILE__, 'updating control flag with '.$field, DEBUGDEBUG);
        break;
        case 'importId':
          $controlFlag = $controlFlag|16;
          class_debug::addMsg(__FILE__, 'updating control flag with '.$field, DEBUGDEBUG);
        break;
        }
      }
    
    // check if checkSum is not empty, has to be 32 or 34,35,36 (md5 or md5-sequence#)
    $checkSumLen = strlen($this->checkSum);
    if($checkSumLen > 31 && $checkSumLen < 37) {
      $controlFlag = $controlFlag|8;
      class_debug::addMsg(__FILE__, 'updating control flag with checksum', DEBUGDEBUG);
      }
        
    // base on queryType, compare the controlFlag with the mandatory one
    if($queryType == 'update') {
      if($controlFlag >= self::UPDATE_CONTROL_FLAG) {
        $return = true;
        }
      else class_debug::addMsg(__FILE__, 'Control flag is '.$controlFlag.', requires '.self::UPDATE_CONTROL_FLAG, DEBUGINFO);
      }
    elseif($queryType == 'insert') {
      if($controlFlag == self::INSERT_CONTROL_FLAG) {
        $return = true;
        }
      else class_debug::addMsg(__FILE__, 'Control flag is '.$controlFlag.', requires '.self::INSERT_CONTROL_FLAG, DEBUGINFO);
      }
    return $return;
    }
  
  /**
	* Saves the transaction data 
	*
	* @return void
	* @access public	
	*/		
  public function save() {
    // the return flag, by default it is a failure
    $return = 0;
    
    // if checkSum is not provided, compute it
    //if(strlen($this->checkSum) == 0) {
      $this->getCheckSum();
      //}
    
    // check if mandatory fields are present
    if($this->id > 0) {
      if(!$this->checkMandatoryFields('update')) {
        class_debug::addMsg(__FILE__, 'Transaction not updated - missing mandatory fields', DEBUGDEBUG);
        return -2;
        }
      }
    else {
      if(!$this->checkMandatoryFields('insert')) {
        class_debug::addMsg(__FILE__, 'Transaction not inserted - missing mandatory fields', DEBUGDEBUG);
        return -2;
        }
      }
      
    // adds the fields to the query
    $this->db->addFields('isParent', $this->isParent);
    $this->db->addFields('parentId', $this->parentId);
    $this->db->addFields('checkSum', $this->checkSum);
    $this->db->addFields('amount', $this->amount);
    $this->db->addFields('operationDate', $this->operationDate);
    $this->db->addFields('payee', $this->payee);
    $this->db->addFields('memo', $this->memo);
    $this->db->addFields('cleared', $this->cleared);
    $this->db->addFields('category', $this->category);
    $this->db->addFields('debit', $this->debit);
    $this->db->addFields('comments', $this->comments);
    $this->db->addFields('deleteFlag', $this->deleteFlag);
    $this->db->addFields('investmentFlag', $this->investmentFlag);
    $this->db->addFields('importId', $this->importId);
      
    // do the update or insert
    if($this->id > 0) {
      $this->db->addFields('modifiedBy', $this->currentUser);
      $this->db->addWhere('id', '=', $this->id);
      $result = $this->db->update('t_items');
      if(!$result) {
        $return = -1;
        class_debug::addMsg(__FILE__, 'Transaction not updated - duplicate', DEBUGDEBUG);
        }
      else { 
        $return = 1;
        class_debug::addMsg(__FILE__, 'Transaction updated', DEBUGDEBUG);
        }
      }
    else {
      // set the create date to now
      $this->createDate = date("Y-m-d");
      $this->db->addFields('createDate', $this->createDate);
      $this->db->addFields('importedBy', $this->currentUser);
      $id = $this->db->insert('t_items');
      if(!$id) {
        $return = -1;
        class_debug::addMsg(__FILE__, 'Transaction not inserted - duplicate', DEBUGDEBUG);
        }
      else {
        $this->id = $id;
        $return = 1;
        class_debug::addMsg(__FILE__, 'Transaction inserted', DEBUGDEBUG);
        }
      }
    class_debug::addMsg(__FILE__, 'Transaction saved', DEBUGINFO);
    return $return;
    }

    
  /**
	* Loads all transation data 
	* 	
	* @return void
	* @access public	
	*/
  public function load() {
    if(!($this->id > 0)) return false;
    
    // set the fields to retreive
    $this->db->addFields('isParent');
    $this->db->addFields('parentId');
    $this->db->addFields('checkSum');
    $this->db->addFields('amount');
    $this->db->addFields('operationDate');
    $this->db->addFields('payee');
    $this->db->addFields('memo');
    $this->db->addFields('cleared');
    $this->db->addFields('category');
    $this->db->addFields('createDate');
    $this->db->addFields('debit');
    $this->db->addFields('comments');
    $this->db->addFields('deleteFlag');
    $this->db->addFields('investmentFlag');
    $this->db->addFields('importId');
    $this->db->addFields('importedBy');
    $this->db->addFields('modifiedBy');
    $this->db->addWhere('id', '=', $this->id);
    
    // run the query
    $this->db->select('t_items');
    $item = $this->db->fetchRow();
    if($item == false) {
      class_debug::addMsg(__FILE__, 'Unable to retreive transaction '.$this->id, DEBUGINFO);
      return false;
      }
    
    // load the data
    $this->isParent = (string)$item['isParent'];
    $this->parentId = (string)$item['parentId'];
    $this->checkSum = (string)$item['checkSum'];
    $this->amount = (string)sprintf("%.2f", $item['amount']);
    $this->operationDate = (string)$item['operationDate'];
    $this->payee = (string)$item['payee'];
    $this->memo = (string)$item['memo'];
    $this->cleared = (string)$item['cleared'];
    $this->category = (integer)$item['category'];
    $this->createDate = (string)$item['createDate'];
    $this->debit = (string)$item['debit'];
    $this->comments = (string)$item['comments'];
    $this->deleteFlag = (string)$item['deleteFlag'];
    $this->investmentFlag = (string)$item['investmentFlag'];
    $this->importId = (integer)$item['importId'];
    $this->importedBy = (string)$item['importedBy'];
    $this->modifiedBy = (string)$item['modifiedBy'];
    
    // load meta information
    // is this transaction editable
    $this->isEditable();
    
    class_debug::addMsg(__FILE__, 'Transaction '.$this->id.' loaded', DEBUGINFO);
    return true;
    }

  /**
	* finds out if this transaction is editable 
	* 	
	* @return void
	* @access private
	*/
  private function isEditable() {
    // by default, a transaction cannot be edited
    $this->editable = false;
    
    // set the fields to retreive
    $this->db->addFields('importType');
    $this->db->addWhere('id', '=', $this->importId);
    
    // run the query
    $this->db->select('t_imports');
    $item = $this->db->fetchRow();
    if($item == false) {
      class_debug::addMsg(__FILE__, 'Unable to retreive impor '.$this->id, DEBUGINFO);
      return false;
      }
    
    // load the data
    $importType = (string)$item['importType'];
    if($importType == IMPORTMANUAL && $this->parentId == 0 ) $this->editable = true;
    }
  /**
	* generate a checksum of the transaction
	* This allows to enforce transaction uniqueness   
	*   	
	* @return void
	* @access public
	*/
  public function getCheckSum($sequence = 1) {
    // checksum is built on operation date, amount and payee 
    $checkSum = $this->operationDate.$this->amount.$this->payee;
    // sequence is necessary to cover for duplicate items in the same import process (rare but possible)
    // sequence is maintained by the import process
    if($sequence == 1) $this->checkSum = md5($checkSum);
    else $this->checkSum = md5($checkSum).'-'.$sequence;
    // return the checkSum value
    return $this->checkSum;
    }
    
  /**
	* class constructor 
	*   	
	* @return void
	* @access public
	*/
  public function __construct($id = null) {
    class_debug::addMsg(__FILE__, 'Creating Transaction object id='.$id, DEBUGINFO);
    $this->db = class_db::getInstance();
    // get the current logged in user
    $userSettings = class_settings::getInstance();
    $userSettings->getSettings();
    $this->currentUser = $userSettings->getUsername();
    // set the operation date to its default
    $this->operationDate = date("Y-m-d");
    // if an id is given, load the object with data from DB
    if($id != null) {
      $this->id = $id;
      $this->load();
      }
    class_debug::addMsg(__FILE__, 'Transaction object instantiated with id='.$id.' user='.$userSettings->id, DEBUGDEBUG);
    } // __construct()
  }
?>