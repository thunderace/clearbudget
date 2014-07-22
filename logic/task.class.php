<?php
/**
* File holding the task object
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
* Class define a task object with its properties and methods
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_task {
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
	* @var float transaction amount 
	* @access private	
	*/
  private $amount = '0';
  /**
	* @var integer task reminder day 
	* @access private	
	*/
  private $reminderDay = '';
  /**
	* @var string task memo 
	* @access private	
	*/
  private $memo = '';
  /**
	* @var type task reminder type (monthly or specific date) - date is not supported in the free version 
	* @access private	
	*/
  private $type = "monthly";
  /**
	* @var date transaction record creation date 
	* @access private	
	*/
  private $createDate = "";
  
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
          class_debug::addMsg(__FILE__, 'amount = '.$value, DEBUGDEBUG);
          }
      break;
      case 'reminderDay':
        $this->reminderDay = $value;
        class_debug::addMsg(__FILE__, 'reminderDay = '.$value, DEBUGDEBUG);
      break;
      case 'memo': 
        if(strlen($value) > 0) {
          $this->memo = trim(strtolower($value));
          class_debug::addMsg(__FILE__, 'memo = '.$value, DEBUGDEBUG);
          }
      break;
      case 'type': 
        if($value == 'monthly'|| $value == 'date') {
          $this->type = $value;
          class_debug::addMsg(__FILE__, 'type = '.$value, DEBUGDEBUG);
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
	* Saves the task 
	*
	* @return void
	* @access public	
	*/		
  public function save() {
    // the return flag, by default it is a failure
    $return = 0;
    
    // adds the fields to the query
    $this->db->addFields('amount', $this->amount);
    $this->db->addFields('reminderDay', $this->reminderDay);
    $this->db->addFields('memo', $this->memo);
    $this->db->addFields('type', $this->type);
      
    // do the update or insert
    if($this->id > 0) {
      $this->db->addWhere('id', '=', $this->id);
      $result = $this->db->update('t_tasks_reminder');
      if(!$result) {
        $return = -1;
        class_debug::addMsg(__FILE__, 'task not updated', DEBUGDEBUG);
        }
      else { 
        $return = 1;
        class_debug::addMsg(__FILE__, 'task updated', DEBUGDEBUG);
        }
      }
    else {
      // set the create date to now
      $this->createDate = date("Y-m-d");
      $this->db->addFields('createDate', $this->createDate);
      $id = $this->db->insert('t_tasks_reminder');
      if(!$id) {
        $return = -1;
        class_debug::addMsg(__FILE__, 'task not inserted', DEBUGDEBUG);
        }
      else {
        $this->id = $id;
        $return = 1;
        class_debug::addMsg(__FILE__, 'task inserted', DEBUGDEBUG);
        }
      }
    class_debug::addMsg(__FILE__, 'task saved', DEBUGINFO);
    return $return;
    }

    
  /**
	* Loads this task information 
	* 	
	* @return void
	* @access public	
	*/
  public function load() {
    if(!($this->id > 0)) return false;
    
    // set the fields to retreive
    $this->db->addFields('amount');
    $this->db->addFields('reminderDay');
    $this->db->addFields('memo');
    $this->db->addFields('type');
    $this->db->addFields('createDate');
    $this->db->addWhere('id', '=', $this->id);
    
    // run the query
    $this->db->select('v_tasks_reminder');
    $item = $this->db->fetchRow();
    if($item == false) {
      class_debug::addMsg(__FILE__, 'Unable to retreive task '.$this->id, DEBUGINFO);
      return false;
      }
    
    // load the data
    $this->amount = (string)sprintf("%.2f", $item['amount']);
    $this->reminderDay = (string)$item['reminderDay'];
    $this->memo = (string)$item['memo'];
    $this->type = (string)$item['type'];
    $this->createDate = (string)$item['createDate'];
    class_debug::addMsg(__FILE__, 'task '.$this->id.' loaded', DEBUGINFO);
    return true;
    }

  /**
	* Loads all task reminders
	* 
	* TODO: move this to a factory class     
	* 	
	* @return array all tasks info
	* @access public	
	*/
   public function getAllReminders() {
   
    }
  
  /**
	* retreive all reminders from a start and for a given limit 
	*	
	* @param integer the limit in number of days from today	
	* @return array the reminders
	*/
  public function getReminders($start, $limit) {
    class_debug::addMsg(__FILE__, 'retreiving reminders from day #'.$start.' up to day #'.$limit, DEBUGDEBUG);
    // fetch daily tasks
    $this->db->addFields('id');
    $this->db->addFields('amount');
    $this->db->addFields('memo');
    $this->db->addFields('reminderDay');
    $this->db->addFields('type');
    $this->db->addWhere('type', '=', 'monthly');
    $this->db->addWhereBetween('reminderDay', $start, $limit);
    $this->db->select('v_tasks_reminder');
    $tasks1 = $this->db->fetchAllRows();
    // fetch date specific taks
    $this->db->addFields('id');
    $this->db->addFields('amount');
    $this->db->addFields('memo');
    $this->db->addFields('reminderDay');
    $this->db->addFields('type');
    $this->db->addWhere('type', '=', 'date');
    $this->db->addWhereBetween('reminderDay', date('Y-m').'-'.$start, date('Y-m').'-'.$limit);
    $this->db->select('v_tasks_reminder');
    $tasks2 = $this->db->fetchAllRows();
    // merge the results
    $tasks = array_merge($tasks1, $tasks2);
    return $tasks;
    }
    
     
   /**
	* macro function to retreive all reminders for today's day 
	*	
	* @return array the reminders
	*/
  public function getRemindersForToday() {
    // empty return array
    $tasks = array();
    // today's day
    $today = date('d');
    // number of days in the current month
    $daysInMonth = date('t');
    // get the limit for the query
    $limit = $today+REMINDERSLIMIT-1;
    if($limit > $daysInMonth) {
      // gets all from today to max number of days in a month
      $tasks1 = $this->getReminders($today, $daysInMonth);
      // gets all from beginning of next month
      $tasks2 = $this->getReminders(1, $limit-$daysInMonth+1);
      $tmp_tasks = array_merge($tasks1, $tasks2);
      }
    else {
      $tmp_tasks = $this->getReminders($today, $limit);
      }
    // add the days count to the event
    foreach($tmp_tasks as $id=>$task) {
      if($task['type'] == 'monthly') $reminderDay = $task['reminderDay'];
      else {
        $tmp = explode('-', $task['reminderDay']);
        $reminderDay = $tmp[2];
        }
      $when = $reminderDay - date('j');
      if($when < 0) {
        $when = date('t') - date('j') + $reminderDay;
        }
      // set the value in the task array
      $task['when'] = $when;
      // prevent double tasks on same day to overwrite
      while(isset($tasks[$when])) $when ++;
      $tasks[$when] = $task;
      }
    ksort($tasks);
    return $tasks;
    }
  
  /**
	* Compute the total amount from all reminders for a given month
	* 
	* TODO: move this to a factory class     
	* 	
	* @return integer the total amount for the given month
	* @access public	
	*/
   public function getMonthlyRemindersTotalAmount($month) {
    $this->db->addFields('total(amount)');
    $this->db->addWhere('type','=', 'monthly');
    $this->db->addWhere('reminderDay', 'like', $month.'%', 'OR');
    $this->db->select('v_tasks_reminder');
    $total = $this->db->fetchRow();
    return $total['total(amount)'];
    }
    
  /**
	* class constructor 
	*   	
	* @return void
	* @access public
	*/
  public function __construct($id = null) {
    $this->db = class_db::getInstance();
    // if an id is given, load the object with data from DB
    if($id != null) {
      $this->id = $id;
      $this->load();
      }
    class_debug::addMsg(__FILE__, 'task object instantiated with id='.$id, DEBUGDEBUG);
    } // __construct()
  }
?>