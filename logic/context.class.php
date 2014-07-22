<?php
/**
* File holding the context class
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
* Class handling the context of each request. It maintains the context of the request by
* keeping all related variables to their original values and by letting other classes and files
* accessing them
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_context {
  /**
	* @var array array of all variables coming from the user request (GET or POST)
	* @access private	
	*/
  private $vars = null;
	/**
	* @var object Hold the instance of the current class
	* @access private
	* @static	
	*/
  private static $_instance = null;
  /**
   * @var int the current logged in user id (if any)
   * @access private
   */
  private $userId = false;
  /**
   * @var boolean a flag true/false indicating if a user is logged in
   * @access private
   */
  private $loggedIn = false;

  /**
   * return the currently logged in user id (as encoded in the cookie)
   * @return int|null the user id or null
   */
  public function  getLoggedInUserId() {
    return $this->userId;
    }
  
  /**
  * redirect to a different action 
	*	
	* @return void
	* @access public
	*/
  public function redirect($action) {
    $this->vars['action'] = $action;
    $this->vars['snippet'] = $action;
    }
  
  /**
	* magic method to access the private $vars variable 
	*	
	* This adds a layer between the user input and what the application is working with
	*     		
	* @return mixed the value if it exists or null
	* @access public
	*/
  public function __get($field) {
    if(isset($this->vars[$field])) {
      $return = $this->vars[$field];
      return $return;
      }
    return null;
    }

  /**
	* Save the action as the back action as it would come from the browser 
	*	
	* @param string the action to set     		
	* @return void
	* @access public	
	*/
  public function setBackAction($action) {
    $this->vars['backAction'] = $action;
    }
  
  /**
	* Do some clean up on the var from the user
	* if it is a string, trim it and strip tags out of it
	* if it is an array, same on its items
	* if none, then it is strange and set to null for safety 
	*	
	* @param string the var from the browser	
	* @return string the var trimmed and cleanned up
	* @access private	
	*/
  private function cleanVar($var) {
    if(is_string($var)) $var = trim(strip_tags($var));
    elseif(is_array($var)) {
    	foreach($var as $key=>$value) {
    	  $var[$key] = trim(strip_tags($value));
    		}
    	}
    else {
      $var = null;
    }
    return $var;
    }
  
  /**
	* Gets all vars from the user request 
	*	
	* @return void
	* @access private	
	*/
  private function getVars() {
    // preset some default value
    $this->vars['action'] = 'loading';
    $this->vars['ajax'] = false;
    $this->vars['nextAction'] = false;
    
    // loop through the input array
    foreach($_REQUEST as $field=>$value) {
      $field = $this->cleanVar($field);
      $value = $this->cleanVar($value);
      $this->vars[$field]=$value;
      class_debug::addMsg(__FILE__, $field.'='.$value, DEBUGDEBUG);
      }

    foreach($_COOKIE as $field=>$value) {
      $field = $this->cleanVar($field);
      $value = $this->cleanVar($value);
      // do not overwrite the values from the query (GET or POST)
      if(!isset($this->vars[$field])) $this->vars[$field]=$value;
      }

    // by default the snippet = the action
    $this->vars['snippet'] = $this->vars['action'];
  
    }
  
  /**
	* Add a value to the context 
	*	
	* @return boolean true if successful, false if the name already exist
	* @access private	
	*/
  public function addToContext($name, $value) {
    if(isset($this->vars[$name])) return false;
    $this->vars[$name] = $value;
    return true;
    }
    
   
  /**
	* Initialize the context object by loading all the different values 
	*	
	*	
	* @return void
	* @access private	
	*/
  private function init() {
    // get all the vars from the browser
    $this->getVars();

    // detect if user is logged in
    $this->loggedIn = class_userCookie::isCookieValid();
    // if user is logged in, gets his user id
    if($this->loggedIn) $this->userId = class_userCookie::$userId;

    // gets the user preferences
    $userSettings = class_settings::getInstance($this->userId);
    // get the Language from the settings object
    $this->vars['userLanguage'] = $userSettings->getLanguage();
    
    // get the Currency from the settings object
    $this->vars['userCurrency'] = $userSettings->getCurrency();
    
    // if user did not select a prefered language, set the language to the prefered user agent language
    if($this->vars['userLanguage'] == null || $this->vars['userLanguage'] == '') {
      // set the context var
      $this->vars['userLanguage'] = getUserAgentLanguage();
      } 
    // set the time zone for the date/time functions
    date_default_timezone_set('America/Los_Angeles');
    class_debug::addMsg(__FILE__, 'Request context loaded', DEBUGINFO);
    }
  
  /**
	* Get the login status of the current user
	*	
	* Reads the login cookie in order to determine the user login status	
	*	
	* @return boolean a flag set to true or false depending on the login status
	* @access public	
	*/
  public function isUserLoggedIn() {
    return $this->loggedIn;
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
    // initialize the object
    $this->init();
    } // __construct()
  }
?>