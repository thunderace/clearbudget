<?php
/**
* File holding the user settings class
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
* Class to retreive and set the user preferences. It is used in the context.
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_settings extends class_user {
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
	* @var boolean flag telling if the site should be secured 
	* @access private	
	*/
  private  $secureAccess = null;
  /**
	* @var string the user currency 
	* @access private	
	*/
  private $currency = null;
  /**
	* @var string the application language
	* @access private
	*/
  protected $language = null;
  /**
	* @var string the default language
	* @access private
	*/
  protected $defaultLanguage = null;
  /**
	* @var string the user language
	* @access private
	*/
  protected $userLanguage = null;
  /**
	* @var integer the initial balance 
	* @access private	
	*/
  private $initialBalance = null;
  /**
	* @var boolean flag telling if the user preferences have been loaded already 
	* @access private	
	*/
  private $settingsLoaded = false;
  /**
   * @var int the currently logged in user id
   * @access private
   */
   private $userId = false;

  /**
	* Saves all the user preferences at once 
	*
	*  	
	* @param boolean flag telling if the settings are sufficient to say that the application is fully setup
	* @return void
	* @access public	
	*/		
  public function updateUserSettings($isNewUser = false) {
    $doUpdate = false;
    if($this->secureAccess!=null) {
      $this->db->addFields('secureAccess', $this->secureAccess);
      $doUpdate = true;
      }
    if($this->language!=null) {
      $this->db->addFields('language', $this->language);
      $doUpdate = true;
      }
    if($this->currency!=null) {
      $this->db->addFields('currency', $this->currency);
      $doUpdate = true;
      }
    if($this->initialBalance!=null) {
      $this->db->addFields('initialBalance', $this->initialBalance);
      $doUpdate = true;
      }
    
    if($doUpdate) {
      $this->db->addWhere('id', '=', '1');
      // do the update
      if($isNewUser) $this->db->insert('t_settings');
      else $this->db->update('t_settings');
      // reset the internal flag
      $this->settingsLoaded = false;
      // load the new user settings
      $this->getSettings();
      }
    }
    
  /**
	* Gets all the user preferences 
	* 	
	* @return void
	* @access public	
	*/		
  public function getSettings() {
    class_debug::addMsg(__FILE__, 'Getting user settings (user id:'.$this->userId.')', DEBUGDEBUG);
    if($this->settingsLoaded) {
      class_debug::addMsg(__FILE__, 'User settings already loaded', DEBUGDEBUG);
      return;
      }
    
    // set the fields to retreive
    $this->db->addFields('language');
    $this->db->addFields('secureAccess');
    $this->db->addFields('currency');
    $this->db->addFields('initialBalance');
    $this->db->addWhere('id', '=', '1');
    // run the query
    $this->db->select('v_settings');
    // since there is only one row and since the view can return only one row, we fetch only one row
    $item = $this->db->fetchRow();
    if($item == false) {
      class_debug::addMsg(__FILE__, 'No user setting yet - application is not setup', DEBUGINFO);
      return false;
      }
    $this->defaultLanguage = (string)$item['language'];
    $this->secureAccess = (string)$item['secureAccess'];
    $this->currency = (string)$item['currency'];
    $this->initialBalance = (float)$item['initialBalance'];

    // if a user is logged in, its settings overwrite the global settings
    if($this->userId) {
      $this->language = $this->userLanguage;
      class_debug::addMsg(__FILE__, 'Got user specific language for user '.$this->userId.'', DEBUGDEBUG);
      }
    else {
      $this->language = $this->defaultLanguage;
      class_debug::addMsg(__FILE__, 'Defaulting language to '.$this->defaultLanguage.'', DEBUGDEBUG);
    }

    // fix language to move to ISO standard - just being backward compatible with older version
    switch($this->language) {
      case 'en': $this->language = 'en-US'; break;
      case 'tr': $this->language = 'tr-TR'; break;
      case 'fr': $this->language = 'fr-FR'; break;
      case 'es': $this->language = 'es-ES'; break;
      }

    $this->settingsLoaded = true;
    class_debug::addMsg(__FILE__, 'User settings loaded', DEBUGINFO);
    return true;
    }

  /**
	* Gets the username 
	*  		
	* @return void
	* @access public	
	*/		
  public function getUsername() {
    return $this->username;
    }

  /**
	* Gets the password 
	*  		
	* @return void
	* @access public	
	*/		
  public function getPassword() {
    return $this->password;
    }
  
  /**
	* Returns the currency 
	*  	
	*
	* @return string currency
	* @access public	
	*/		
  public function getCurrency() {
    return $this->currency;
    }
  
  /**
	* Returns the language 
	*  	
	*
	* @return string language
	* @access public	
	*/		
  public function getLanguage() {
    return $this->language;
    }

  /**
	* Returns the default language
	*
	*
	* @return string language
	* @access public
	*/
  public function getDefaultLanguage() {
    return $this->defaultLanguage;
    }

  /**
	* Returns the initial Balance 
	*  	
	*
	* @return integer initial Balance
	* @access public	
	*/		
  public function getInitialBalance() {
    return $this->initialBalance;
    }
  
  /**
	* tells if access to the application needs to be secure 
	*  	
	*
	* @return boolean secure access flag
	* @access public	
	*/		
  public function secureAccess() {
    return $this->secureAccess;
    }
  
  /**
	* magic method to access the private class variable 
	*     		
	* @return void
	* @access public
	*/
  public function __set($field, $value) {
    switch($field) {
       case 'language': $this->language = trim($value);
         break;
       case 'initialBalance':  $this->initialBalance = (float)trim($value);
         break;
       case 'currency':  $this->currency = trim($value);
         break;
       case 'secureAccess':  $this->secureAccess = trim($value);
         break;
      }
    }
  /**
   * Reload this class with a given user id
   * @param int $userId the user id
   * @return void
   * @access public
   */
  public function reload($userId) {
    $this->userId = $userId;
    $this->id = $userId;
    parent::load();
    $this->getSettings();
    }
    
  /**
	* Provide an instance of the current class 
	*	
	* Implementation of the singleton pattern
	*   		
	* @return object An Instance of this class
	* @access public	
	*/  
  public static function getInstance($userId=false) {
    if(is_null(self::$_instance)) {
      self::$_instance = new self($userId);
      }
    return self::$_instance;
    } // getInstance()

  /**
	* class constructor 
	*	
	* access is public since it extends the user_class
	*   	
	* @return void
	* @access public
	*/
  public function __construct($userId) {
    $this->db = class_db::getInstance();
    $this->userId = $userId;
    parent::__construct($userId);
    $this->getSettings();
    } // __construct()
  }
?>