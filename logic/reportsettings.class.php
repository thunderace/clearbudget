<?php
/**
* File holding the user report settings class
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
* Class to retreive and set the user report preferences.
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_reportsettings {
  /**
	* @var object Holds the instance of the current class
	* @access private
	* @static  	
	*/
  private static $_instance = null;
  /**
	* @var object Holds an instance of the DB object
	* @access private	
	*/
  private $db = null;
  /**
	* @var integer the report setting ID 
	* @access private	
	*/
  public $id = null;
  /**
	* @var string the report name 
	* @access private	
	*/
  public $name = null;
  /**
	* @var string the minimum date to look for data (the oldest one)
	* @access private	
	*/
  public $minDate = null;
  /**
	* @var string the maximum date to look for data (the soonest one)
	* @access private	
	*/
  public $maxDate = null;
  /**
	* @var string the report type (custom, range) 
	* @access private	
	*/
  public  $type = null;
  /**
	* @var string list of catgegories to include in the report 
	* @access private	
	*/
  public  $categories = null;
  /**
	* @var integer the range in month to compute the report on 
	* @access private	
	*/
  public $range = null;
  /**
	* @var boolean a flag to tell which
	* @access private	
	*/
  public $activeFlag = 0;
  /**
	* @var integer a flag to include credit transactions 
	* @access private	
	*/
  public $credit = null;
  /**
	* @var integer a flag to include debit transactions 
	* @access private	
	*/
  public $debit = null;

  
  /**
	* Saves the language preference 
	*  	
	* @param string the language
	* @return void
	* @access public	
	*/		
  public function getAllReportSettingNames() {
    $reports = array();
    // get all reports defined
    $this->db->addFields('id');
    $this->db->addFields('name');
    $this->db->addFields('type');
    $this->db->addFields('activeFlag');
    $this->db->select('t_report_settings');
    $results = $this->db->fetchAllRows();
    foreach($results as $result) {
      $report['id'] = $result['id'];
      $report['name'] = $this->fixName($result['name'], $result['type']);
      $report['type'] = $result['type'];
      $report['activeFlag'] = $result['activeFlag'];
      $reports[] = $report;
      }
    return $reports;
    }
  
  /**
   * fix the name of predefined report setting to use the translation kyes
   * 
   * @param string the name in the DB
   * @param string the type of the report
   * @return string the translated name
   * @access private
   */
   private function fixName($name, $type) {
    $keys = class_propertyKey::getInstance();
    $label = $name;
     if($type == 'range') {
        switch($name) {
          case '3': $label = $keys->text_last3Month; break;
          case '6': $label = $keys->text_last6Month; break;
          case '12': $label = $keys->text_last12Month; break;
          }
        }
    return $label;
    }
                       
  /**
	* Saves the settings of a given report 
	*  	
	* @param string the report id
	* @return void
	* @access public	
	*/		
  public function getReportSettings($id=null) {
    $this->db->addFields('id');
    $this->db->addFields('name');
    $this->db->addFields('minDate');
    $this->db->addFields('maxDate');
    $this->db->addFields('type');
    $this->db->addFields('categories');
    $this->db->addFields('range');
    $this->db->addFields('credit');
    $this->db->addFields('debit');
    $this->db->addFields('activeFlag');
    // if no id given, then select the active one
    if($id == null) {
      $this->db->addWhere('activeFlag', '=', "1");
      }
    else {
      $this->db->addWhere('id', '=', $id);
      }
    $this->db->select('t_report_settings');
    $result = $this->db->fetchRow();
    $this->id = $result['id'];
    $this->name = $this->fixName($result['name'], $result['type']);
    $this->minDate = $result['minDate'];
    $this->maxDate = $result['maxDate'];
    $this->type = $result['type'];
    $this->categories = $result['categories'];
    $this->range = $result['range'];
    $this->credit = $result['credit'];
    $this->debit = $result['debit'];
    $this->activeFlag = $result['activeFlag'];
    }
  
  /**
	* Delete the active report and set the first report to be the active one 
	*  	
	* @return boolean True on success and False otherwise
	* @access public	
	*/		
  public function deleteActiveReport() {
    $ids = null;
    $count = 0;
    // check if there are more than one report
    $this->db->addFields('id');
    $this->db->addWhere('activeFlag', '=', "0");
    $this->db->select('t_report_settings');
    while(($result = $this->db->fetchRow()) != false) {
      $count++;
      $ids[] = $result['id'];
      }
    // if 0 or 1, we can't delete so we return false
    if($count == 0) return false;
    
    // delete the currently active report
    $this->db->addWhere('activeFlag', '=', "1");
    $this->db->delete('t_report_settings');
    
    // make sure the object is unset
    $this->id = null;
    $this->name = null;
    $this->minDate = null;
    $this->maxDate = null;
    $this->type = null;
    $this->categories = null;
    $this->range = null;
    $this->credit = null;
    $this->debit = null;
    $this->activeFlag = null;
    
    // set the first report to be the active one
    $this->setActiveReport($ids[0]);
    return true;
    }

  /**
	* Saves all the user preferences at once 
	*
	*  	
	* @param boolean flag telling if the settings are sufficient to say that the application is fully setup
	* @return intger the last updated or inserted report id
	* @access public	
	*/		
  public function updateReportSettings() {
    if($this->name !== null) $this->db->addFields('name', $this->name);
    if($this->minDate !== null) $this->db->addFields('minDate', $this->minDate);
    if($this->maxDate !== null) $this->db->addFields('maxDate', $this->maxDate);
    if($this->type !== null) $this->db->addFields('type', $this->type);
    if($this->categories !== null) $this->db->addFields('categories', $this->categories);
    if($this->range !== null) $this->db->addFields('range', $this->range);
    if($this->activeFlag !== null) $this->db->addFields('activeFlag', $this->activeFlag);
    if($this->credit !== null) $this->db->addFields('credit', $this->credit);
    if($this->debit !== null) $this->db->addFields('debit', $this->debit);
    
    // do the update
    if($this->id != null) {
      $this->db->addWhere('id', '=', $this->id);
      $this->db->update('t_report_settings');
      }
    else {
      $this->id = $this->db->insert('t_report_settings');
      }
    return $this->id;
    }

  /**
	* set a specific report to be the active one. Only one report can be the active one at a time 
	*
	*  	
	* @param integer the report id to set active
	* @return void
	* @access public	
	*/		
  public function setActiveReport($id) {
    // reset all to be inactive
    $this->db->addFields('activeFlag', "0");
    $this->db->update('t_report_settings');
    // set the given report to be active
    $this->db->addFields('activeFlag', "1");
    $this->db->addWhere('id', '=', $id);
    $this->db->update('t_report_settings');
    }

  /**
	* class constructor 
	*	
	* access to this class is private as to implement the singleton pattern
	*   	
	* @return void
	* @access private
	*/
  public function __construct() {
    $this->db = class_db::getInstance();
    } // __construct()
  }
?>