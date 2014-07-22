<?php
/**
* factory class for User objects
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
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
* factory class for user objects
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_userFactory {
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
  public $users = null;

  /**
   * Delete a user
   * @param int $id the user id to be deleted
   * @return void
   * @access public
   */
  public function deleteUser($id) {
    // get a db handler
    $db = class_db::getInstance();
    // add the where clause
    $db->addWhere('id', '=', $id);
    // delete the record
    $db->delete('t_users');
    }

  /**
   * Count the number of Admin users
   * @return integer the number of admin users
   * @access public
   */
  public function adminUserCount() {
    $db = class_db::getInstance();
    $db->addFields('count(id)');
    $db->addWhere('type', '=', 'admin');
    $db->select('v_users');
    $result = $db->fetchRow();
    return $result['count(id)'];
    }

  /**
   * Returns an array containing all User Ids
   * @param int $type the user type - optionnal
   * @return array the list of user Ids
   * @access public
   */
  public function getAllUsersId($type = null) {
    $ids = array();
    // get a DB object
    $db = class_db::getInstance();
    $db->addFields('id');
    if($type != null) $db->addWhere('type', '=', $type);
    $db->select('v_users');
    $results = $db->fetchAllRows();
    if($results != false) {
      foreach($results as $result) {
        $ids[] = $result['id'];
        }
      }
    return $ids;
    }


  /**
   * Load all users
   * @return void
   * @access public
   */
  public function loadAllUsers() {
    $ids = $this->getAllUsersId();
    foreach($ids as $id) {
      $this->getUser($id);
      }
    }

  /**
   * gets a user id from a username/password combination
   * @param string $username the user username
   * @param string $password the user password
   * @return int|boolean the user id or false if user does not exists
   */
   public function getUserId($username, $password) {
    $id = false;
    $pwd = md5($password);
    // get a DB object
    $db = class_db::getInstance();
    $db->addFields('id');
    $db->addWhere('username', '=', $username);
    $db->addWhere('password', '=', $pwd);
    $db->addWhere('enabled', '=', '1');
    $db->select('v_users');
    $result = $db->fetchRow();
    if($result != false) {
      $id = $result['id'];
      }
    return $id;
    }

  /**
   * Returns a user object
   * @param int $id the user id
   * @return object a user object
   * @access public
   */
  public function &getUser($id = null) {
    $user = null;
    // if an object already exist, return it
    if(isset($this->users[$id])) {
        $user = $this->users[$id];
        }
    // else instantiate it
    else {
        $user = new class_user($id);
        class_debug::addMsg(__FILE__, 'Created new user object for id: '.$id, DEBUGINFO);
        // add this object to the internal storage
        $this->users[$id] = $user;
        }
    return $user;
    }

  /**
   * Provide an instance of the current class
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