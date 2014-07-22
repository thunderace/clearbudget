<?php
/**
* File holding the user cookie class
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
* 
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
* Class handling the logic around the user cookie. This cookie is used to identify the user
* and to manage the login/logout state.
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_userCookie {
	/**
	* @var object Hold the instance of the current class
	* @access private
	* @static	
	*/
  private static $_instance = null;
  /**
	* @var string the hash of the user identifying string
	* @access public
	* @static	
	*/
  public static $hash = null;
  /**
	* @var boolean flag true/false if cookie is valid
	* @access public
	* @static	
	*/
  public static $cookieValid = null;
  /**
   * @var int the user id from the cookie
   * @access public
   * @static
   */
   public static $userId = false;
  
  /**
	* Reads the login cookie and validate its value	
	*	
	* @return boolean a flag set to true or false
	* @access public
	* @static	
	*/
  public static function isCookieValid() {
    if(self::$cookieValid !== null) {
      class_debug::addMsg(__FILE__, 'Cookie was checked already', DEBUGINFO);
      return self::$cookieValid;
      }
    
    class_debug::addMsg(__FILE__, 'Validating user cookie', DEBUGDEBUG);
    // try to get the cookie
    if(!isset($_COOKIE['user']) || $_COOKIE['user'] === null || $_COOKIE['user'] == '') {
      // no cookie here
      class_debug::addMsg(__FILE__, 'Cookie is empty', DEBUGINFO);
      self::$cookieValid = false;
      self::deleteCookie();
      return false;
      }
    // try to break out the cookie
    $userCookie = $_COOKIE['user'];
    $parts = explode('-', $userCookie);
    if(!is_array($parts) || count($parts) != 3) {
      // cookie does not look formatted as we want
      class_debug::addMsg(__FILE__, 'Cookie is invalid', DEBUGINFO);
      self::$cookieValid = false;
      self::deleteCookie();
      return false;
      }
    // get the data from the cookie
    $userId = $parts[0];
    $key = $parts[1];
    $time = $parts[2];
    // get the username and password from the DB
    $userSettings = class_settings::getInstance($userId);
    // recreate the cookie for the given time and for the user in the DB and for all salt/pepper
    $lock = self::getSecureLoginKey($userSettings->getUsername(), $userSettings->getPassword(), $time);
    // compare the lock with the hash
    if($key != $lock) {
      // cookie does not match---might have been hacked
      class_debug::addMsg(__FILE__, 'Hash cookie do not match', DEBUGINFO);
      self::$cookieValid = false;
      self::deleteCookie();
      return false;
      }
    // check if user is still logged in
    $lastHit = time()-$time;
    if($lastHit >= LOGINCOOKIELIFETIME) {
      class_debug::addMsg(__FILE__, 'cookie is expired by '.($lastHit-LOGINCOOKIELIFETIME).'s', DEBUGINFO);
      self::$cookieValid = false;
      self::deleteCookie();
      return false;
      }
    // everything looks good here
    self::$userId = $userId;
    // refresh the user cookie to start the time counter from scratch
    class_debug::addMsg(__FILE__, 'Cookie is valid - user id '.self::$userId.' ('.$lastHit.'s ago - reseting cookie)', DEBUGINFO);
    self::setUserCookie();
    self::$cookieValid = true;
    return true;
    }

 /**
	* log the user using the secure cookie
	*
	* @param int $id the user Id
	* @return void
	* @access public

	*/
  public static function loginUser($id) {
    // get the username and password from the DB
    $userSettings = class_settings::getInstance();
    $userSettings->reload($id);
    self::$userId = $id;
    self::setUserCookie();
    }

  /**
   * set the user cookie
   * @return void
   * @access private
   * @static
	*/
  public static function setUserCookie() {
    // if no userId, return
    if(!self::$userId) {
      class_debug::addMsg(__FILE__, 'User id is invalid, cookie cannot be set', DEBUGERROR);
      return;
      }
    // get the current time
    $now = time();
    // get the username and password from the DB
    $userSettings = class_settings::getInstance(self::$userId);
    // try to set the cookie
    if(setcookie('user', self::$userId.'-'.self::getSecureLoginKey($userSettings->getUsername(), $userSettings->getPassword(), $now).'-'.$now, 0, "/")) {
      class_debug::addMsg(__FILE__, 'User cookie set', DEBUGINFO);
      }
    else {
      class_debug::addMsg(__FILE__, 'User cookie cannot be set', DEBUGERROR);
      }

    }
  
  /**
   * delete the user cookie
   * @return void
   * @access private
   * @static
   */
  public static function deleteCookie() { 
    setcookie('user', '', 0, "/");
    class_debug::addMsg(__FILE__, 'User cookie unset', DEBUGINFO);
    }
    
  /**  
	* Return a secure string from the current logged in user 
	* 	
	* @param string $timeStamp a time stamp
	* @return mixed a string or an array of string depending on $forAllSaltnPepper
	* @access private
	* @static	
	*/
  private static function getSecureLoginKey($username, $password, $timeStamp) {
    // get the remote user IP address
    $client = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER["HTTP_USER_AGENT"];
    // build a key from there
    $key = md5(LOGINCOOKIESALT.$username.$password.$client.$userAgent.$timeStamp.LOGINCOOKIEPEPPER);
    class_debug::addMsg(__FILE__, 'login key built with '.$username.' - '.$password.' - '.$timeStamp, DEBUGDEBUG);
    return $key;
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
    } // __construct()
  }
?>