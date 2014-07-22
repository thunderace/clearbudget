<?php
/**
* File holding the reporting and statistic class
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
* Main class to get transation data from the DB. This class handles the creation
* of pre-calculated data based on the DB raw data. It is optimized to reduce
* the round-trips to the DB.
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_report {
  /**
	* @var object Holds the instance of the current class
	* @access private
	* @static	
	*/
  private static $_instance = null;
  /**
	* @var object Holds an instance of the DB class
	* @access private	
	*/
  private $db = null;
  /**
	* @var object Holds an instance of the property keys class
	* @access private	
	*/
  private $keys = null;
  // processing fields  
  /**
	* @var array Holds all the data pre-formatted from the DB
	* @access private
	*/
  private $stats = null;
  /**
	* @var array Associative array of category ID-Name pair
	* @access private
	*/
  private $categoryNames = null;
  /**
	* @var boolean flag to tell if data were loaded already
	* @access private
	*/
  private $gotData = false;
  /**
	* @var boolean flag to tell if valid data were loaded already
	* @access private
	*/
  public $validData = false;
  /**
	* @var object the report settings
	* @access public
	*/
  public $reportSettings = null;
  
  /**
	* Magic function to return any data from the internal array of statistics 
	*	
	* 	
	* @param string the data name	
	* @return mixed the data value (array, string...)
	* @access public
	*/
  public function __get($name) {
    // set the return value to null by default
    $data = null;
    
    // check if the data is present and not null
    if(isset($this->stats[$name])) $data = $this->stats[$name];
    
    // filter the output as to get a clean value
    if(is_numeric($data)) $data = $this->formatNumber($data);
    return $data;
    }

  /**
	* Gets the month-year from the month range selected by the user     
	*	
	* 	
	* @param integer the number of month to go back to
	* @return string the month-year string
	* @access public
	*/
  public function getLimitMonthForRange($range) {
    class_debug::addMsg(__FILE__, 'getting the month-day for range: '.$range, DEBUGDEBUG);
    $this->db->addFields('month');
    $this->db->addLimits(0, $range);
    $this->db->select('v_distinct_month');
    $month = $this->db->fetchAllRows();
    if($month !== false && is_array($month) && count($month) > 0) {
       $month = $month[count($month)-1]['month'];
       class_debug::addMsg(__FILE__, 'Date range is '.$month.' for range '.$range, DEBUGINFO);
       }
    else {
      class_debug::addMsg(__FILE__, 'Got no date range for '.$range, DEBUGINFO);
      }
    return $month;
    }
  
  /**
	* Gets all available month-year     
	*	
	* 	
	* @return array of month-year strings
	* @access public
	*/
  public function getAllMonthYear() {
    class_debug::addMsg(__FILE__, 'getting the list of month-day ', DEBUGDEBUG);
    $this->db->addFields('month');
    $this->db->select('v_distinct_month');
    $months = $this->db->fetchAllRows();
    return $months;
    }
  
  /**
	* format a given string to be a valid number with 2 digits 
	* 	
	* @param string the data to be formated
	* @return string the formated data
	* @access public
	*/
  public function formatNumber($string) {
    return sprintf("%.2f", $string);
    } // formatNumber()
  
  /**
	* Return the category ID from its name 
	* 	
	* @param string the category name
	* @return integer the category ID
	* @access public
	*/
  public function getCategoryId($name) {
    return $this->categoryNames[$name];
    }
  
  /**
	* Search all transaction for a given keyword 
	* 	
	* @param string the keyword to look for
	* @return array An associative array with the transactions details
	* @access public	
	*/
  public function search($keyWord) {
    $results = null;
    
    // set the fields to retreive
    $this->db->addFields('id');
    $this->db->addFields('amount');
    $this->db->addFields('debit');
    $this->db->addFields('payee');
    $this->db->addFields('memo');
    $this->db->addFields('operationDate');
    $this->db->addFields('category');
    $this->db->addFields('categoryName');
    $this->db->addFields('comments');
    $this->db->addFields('parentId');
    $this->db->addFields('importType');
    
    $this->db->addWhere('memo', 'like', '%'.$keyWord.'%');
    $this->db->addWhere('comments', 'like', '%'.$keyWord.'%', 'OR');
    $this->db->addWhere('payee', 'like', '%'.$keyWord.'%', 'OR');
    $this->db->addWhere('amount', 'like', '%'.$keyWord.'%', 'OR');
    
    // there are no limits yet on this...we lookup the entire DB which is not great
    // TODO: let's think abouts putting some limits there
    
    // run the query
    $this->db->select('v_items');
        
    while(($item = $this->db->fetchRow()) !== false) {
        $item['amount'] = (float)sprintf("%.2f", $item['amount']);
        $results[] = $item;
        }
    return $results;
    }
  
  /**
	* Build the query used to generate the report 
	* 	
	* @param boolean 	a flag to tell if the query is in test mode or not
	* @param object a reportSetting_class object	
	* @access private	
	*/
  private function buildReportQuery($test = false, &$reportSettings = null) {
    // if report setting aren't given, use the default one
    if($reportSettings === null) {
      $reportSettings = $this->reportSettings;
      }
    
    // add date constraints
    switch($reportSettings->type) {
      case 'range':
        $month = $this->getLimitMonthForRange($reportSettings->range);
        $this->db->addWhere('operationDate', '>=', $month);
      break;
      case 'custom':
        $this->db->addWhereBetween('operationDate', $reportSettings->minDate, $reportSettings->maxDate);
      break;
      }
    // add transaction type constraints
    if($reportSettings->credit == 1 && $reportSettings->debit != 1) {
      $this->db->addWhere('debit', '=', '0');
      }
    elseif($reportSettings->credit == 0 && $reportSettings->debit != 0) {
      $this->db->addWhere('debit', '=', '1');
      }
    // add category constraints
    $categories = $reportSettings->categories;
    if($categories != '') {
      $testCategory = explode(',', $reportSettings->categories);
      // add the categories only is All is not selected
      if($testCategory != false && !in_array("0", $testCategory)) $this->db->addWhereDataRange('category', $categories);
      }
    
    if(!$test) {
      // set the fields to retreive
      $this->db->addFields('total');
      $this->db->addFields('count');
      $this->db->addFields('month');
      $this->db->addFields('year');
      $this->db->addFields('debit');
      $this->db->addFields('category');
      $this->db->addFields('name');
      $this->db->addFields('color');
      $this->db->addFields('maxAmountPerMonth');
      }
    else {
      $this->db->addFields('count(total)');
      }
    }
  
  /**
	* Execute the query to count the number of returned rows 
	* 	
	* @param object a reportSettings_class object	
	* @return integer the number of rows returned
	* @access public	
	*/
  public function testQuery(&$reportSettings = null) {
    // build the query
    $this->buildReportQuery(true, $reportSettings);
    // run the query
    $this->db->select('v_total_amount');
    $res = $this->db->fetchRow();
    $count = $res['count(total)'];
    class_debug::addMsg(__FILE__, 'Report query returns '.$count.' rows', DEBUGDEBUG);
    return $count;
    }
  
  /**
	* Execute the report query and gather all data oragnized and sorted 
	* 	
	* @access public	
	*/
  public function getData() {
    global $context;
    if($this->gotData == true) return;
    if($context->totalItemCount < 1) return;
    $dataCounter = 0;
    
    // build the query
    $this->buildReportQuery();
    // run the query
    $this->db->select('v_total_amount');
    
    // get the data and build the internal arrays
    $this->stats['unfiledAmount'] = 0;
    $this->stats['unfiledCount'] = 0;
    
    while(($item = $this->db->fetchRow()) !== false) {
      $dataCounter ++;
      // retreive unfiled amount and item count
      if(true) {
        if($item['category'] == '1') {
          $this->stats['unfiledAmount'] += (float)sprintf("%.2f", $item['total']);
          $this->stats['unfiledCount'] += $item['count'];
          }
        // all available month/year
        $this->stats['dates'][$item['month'].'-'.$item['year']] = $item['month'].'-'.$item['year'];
        // all the categories
        $this->stats['categories'][$item['category']] = $item['name'];
        $this->stats['categoryColor'][$item['category']] = $item['color'];
        $this->stats['categoryMaxAmountPerMonth'][$item['category']] = $item['maxAmountPerMonth'];
        $this->categoryNames[$item['name']] = $item['category'];
        // preset the arrays
        if($item['debit'] == '1') {
          // total debits amount
          @$this->stats['totalDebits'] += $item['total'];
          // debit per dates and categories
          $this->stats['debitPerMonthPerCategory'][$item['month'].'-'.$item['year']][$item['category']] = $item['total'];
          // total debit per dates
          @$this->stats['debitTotalPerMonth'][$item['month'].'-'.$item['year']] += $item['total'];
          // total debit per category
          @$this->stats['debitTotalPerCategory'][$item['category']] += $item['total'];
          // total per categories per dates
          $this->stats['debitPerCategoryPerMonth'][$item['category']][$item['month'].'-'.$item['year']] = $item['total'];
          }
        else {
          // total credits amount
          @$this->stats['totalCredits'] += $item['total'];
          // debit per dates and categories
          $this->stats['creditPerMonthPerCategory'][$item['month'].'-'.$item['year']][$item['category']] = $item['total'];
          // total debit per dates
          @$this->stats['creditTotalPerMonth'][$item['month'].'-'.$item['year']] += $item['total'];
          // total debit per category
          @$this->stats['creditTotalPerCategory'][$item['category']] += $item['total'];
          // total per categories per dates
          $this->stats['creditPerCategoryPerMonth'][$item['category']][$item['month'].'-'.$item['year']] = $item['total'];
          }
        }
      }
    
    if($dataCounter>0) {
      // compute the percentages
      if(isset($this->stats['debitTotalPerCategory'])) {
        foreach($this->stats['debitTotalPerCategory'] as $category=>$total) {
          $this->stats['debitPctPerCategory'][$category] = $this->formatNumber($total / $this->stats['totalDebits'] * 100);
          }
        }
      
      if(isset($this->stats['creditTotalPerCategory'])) {
        foreach($this->stats['creditTotalPerCategory'] as $category=>$total) {
          $this->stats['creditPctPerCategory'][$category] = $this->formatNumber($total / $this->stats['totalCredits'] * 100);
          }
        }
    
      if(isset($this->stats['debitTotalPerMonth'])) {
        foreach($this->stats['debitTotalPerMonth'] as $month=>$total) {
          $this->stats['debitPctPerMonth'][$month] = $this->formatNumber($total / $this->stats['totalDebits'] * 100);
          }
        }
      
      if(isset($this->stats['creditTotalPerMonth'])) {
        foreach($this->stats['creditTotalPerMonth'] as $month=>$total) {
          $this->stats['creditPctPerMonth'][$month] = $this->formatNumber($total / $this->stats['totalCredits'] * 100);
          }
        }
      
      // do some ordering
      if(isset($this->stats['categories']) && is_array($this->stats['categories'])) {
        asort($this->stats['categories'], SORT_STRING);
        }
      }
    
    class_debug::addMsg(__FILE__, 'Got '.$dataCounter.' transactions', DEBUGINFO);
    // set the internal flag to tell if data are loaded or not
    $this->gotData = true;
    // set false if no data
    if($dataCounter == 0) $this->validData = false;
    // else set the number of transation returned
    else $this->validData = $dataCounter;
    } // getData()
  
  /**
	* Gives the category name from the category Id 
	*	
  * @param integer the category id
	* @return String the category name
	* @access public
	*/
  public function getCategoryName($id=null) {
    if($id == null) return 'null';
    if($id != 1 && !isset($this->stats['categories'][$id])) {
      $this->db->addFields('name');
      $this->db->addWhere('id', '=', $id);
      $this->db->select('v_categories');
      $res = $this->db->fetchRow();
      $this->stats['categories'][$id] = $res['name'];
      return $res['name'];
      }
    if($id == 1) return $this->keys->text_Uncategorized;
    else return $this->stats['categories'][$id];
    }
  
  /**
	* Get all category names 
	*	
  * @access private
	*/
  private function getAllCategoryNames() {
    class_debug::addMsg(__FILE__, 'Getting all category names', DEBUGINFO);
    if(!isset($this->stats['categories'])) {
      $this->db->addFields('name');
      $this->db->addFields('id');
      $this->db->select('v_categories');
      while(($res = $this->db->fetchRow()) !=false) {
        $this->stats['categories'][$res['id']] = $res['name'];
        class_debug::addMsg(__FILE__, 'category '.$res['id'].' is '.$res['name'], DEBUGDEBUG);
        }
      }
    }
  
  /**
	* Get the total balance: overall, debit and credit 
	*	
	* @return array an associative array with the values	
  * @access public
	*/
  public function getTotalBalance() {
    $userSettings = class_settings::getInstance();
    $initialBalance = $userSettings->getInitialBalance();
    $balance['total'] = $initialBalance;
    $balance['debit'] = 0;
    $balance['credit'] = 0;
    class_debug::addMsg(__FILE__, 'Getting the total balance', DEBUGINFO);
    $this->db->addFields('total');
    $this->db->addFields('debit');
    $this->db->select('v_total_balance');
    while(($res = $this->db->fetchRow()) != false) {
      if($res['debit'] == '1') $balance['debit'] = $this->formatNumber($res['total']);
      else $balance['credit'] = $this->formatNumber($res['total']);
      }
    $balance['total'] = $this->formatNumber($initialBalance + $balance['credit'] - $balance['debit'] );
    return $balance;
    }
  
  
  /**
	* Get the total number of transactions 
	*	
  * @access private
	*/
  private function getTotalItemCount() {
    class_debug::addMsg(__FILE__, 'Getting the total item count', DEBUGDEBUG);
    $totalItemCount = 0;
    $this->db->addFields('count(id)');
    $this->db->select('v_items');
    $result = $this->db->fetchRow();
    if($result != false) $totalItemCount = $result['count(id)'];
    else $totalItemCount = -1;
    class_debug::addMsg(__FILE__, 'Total number of items: '.$totalItemCount, DEBUGINFO);
    // adding this to the context 
    global $context;
    $context->addToContext('totalItemCount', $totalItemCount);
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
	*	
	* access to this class is private as to implement the singleton pattern
	*   	
	* @return void
	* @access private
	*/
  private function __construct() {
    global $context;
    // get a DB object
    $this->db = class_db::getInstance();
    // get the current active report settings
    $this->reportSettings = new class_reportsettings();
    $this->reportSettings->getReportSettings();
    // get the total number of transations
    $this->getTotalItemCount();
    // get all category names
    $this->getAllCategoryNames();
    // get the property keys object
    $this->keys = class_propertyKey::getInstance();
    } // __construct()
  }
?>