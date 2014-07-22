<?php
/**
 * User class handling all action around user data
 * @author Fabrice Douteaud <admin@clearbudget.net>
 * @package framework
 * @access public
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


class class_user {
  /**
   * @var object
   * @access private
   */
  private $db = null;
  /**
   * @var integer
   * @access protected
   */
  protected $id = 0;
  /**
   * @var string
   * @access protected
   */
  protected $userLanguage = DEFAULTLANGUAGE;
  /**
   * @var string
   * @access protected
   */
  protected $username = "";
  /**
   * @var string
   * @access protected
   */
  protected $password = "";
  /**
   * @var string
   * @access protected
   */
  protected $type = "user";
  /**
   * @var boolean
   * @access protected
   */
  protected $enabled = true;
 /**
   * @var string
   * @access protected
   */
  protected $createDate = "";

  /**
   * magic method to set the protected variables
   * @param string $field the field name to be set
   * @param mixed $value the value to be assigned to the field
   * @return void
   * @access public
   */
  public function __set($field, $value) {
    class_debug::addMsg(__FILE__, 'Setting '.$field.' = '.$value, DEBUGDEBUG);
    switch($field) {
      case 'userLanguage':
         $this->userLanguage = trim($value);
         class_debug::addMsg(__FILE__, 'language = '.$value, DEBUGDEBUG);
      break;
      case 'username':
         $this->username = trim($value);
         class_debug::addMsg(__FILE__, 'username = '.$value, DEBUGDEBUG);
      break;
      case 'password':
          if($value == "") $this->password = "";
          else $this->password = md5(trim($value));
         class_debug::addMsg(__FILE__, 'password = '.$this->password, DEBUGDEBUG);
      break;
      case 'type':
         $this->type = trim($value);
         class_debug::addMsg(__FILE__, 'type = '.$value, DEBUGDEBUG);
      break;
      case 'enabled':
         $this->enabled = $value;
         class_debug::addMsg(__FILE__, 'enabled = '.(string)$value, DEBUGDEBUG);
      break;
      default:
          class_debug::addMsg(__FILE__, "$field is not a property of the user class", DEBUGERROR);
      break;
      }
    }
    
  /**
   * magic method to access the private variables in read-only
   * @param string $field the class property name
   * @return mixed the value of the class property
   * @access public
   */
  public function __get($field) {
    if(isset($this->$field)) {
      return $this->$field;
      }
    return null;
    }
   
  /**
   * Save the user data back to the database
   *
   * @return void
   * @access public
   */
  public function save() {
    // the return flag, by default it is a failure
    $return = 0;
    
    
    // check if mandatory fields are present
    if(strlen($this->username) < 6 || strlen($this->password) < 6) {
      class_debug::addMsg(__FILE__, 'Can\'t save user - missing mandatory fields', DEBUGDEBUG);
      return -1;
      }
      
    // adds the fields to the query
    $this->db->addFields('language', $this->userLanguage);
    $this->db->addFields('username', $this->username);
    $this->db->addFields('password', $this->password);
    $this->db->addFields('type', $this->type);
    $this->db->addFields('enabled', $this->enabled);
    // do the update or insert
    if($this->id > 0) {
      $this->db->addWhere('id', '=', $this->id);
      $result = $this->db->update('t_users');
      if(!$result) {
        $return = -2;
        class_debug::addMsg(__FILE__, 'User data not saved', DEBUGDEBUG);
        }
      else { 
        $return = 1;
        class_debug::addMsg(__FILE__, 'User data saved', DEBUGDEBUG);
        }
      }
    else {
      // set the create date to now
      $this->createDate = date("Y-m-d");
      $this->db->addFields('createDate', $this->createDate);
      $id = $this->db->insert('t_users');
      if(!$id) {
        $return = -1;
        class_debug::addMsg(__FILE__, 'User not inserted', DEBUGDEBUG);
        }
      else {
        $this->id = $id;
        $return = 1;
        class_debug::addMsg(__FILE__, 'User inserted', DEBUGDEBUG);
        }
      }
    class_debug::addMsg(__FILE__, 'User saved', DEBUGINFO);
    return $return;
    }

    
  /**
   * Load the user data
   * @return void
   * @access public
   */
  public function load() {
    if(!($this->id > 0)) return false;
    
    // set the fields to retreive
    $this->db->addFields('language');
    $this->db->addFields('username');
    $this->db->addFields('password');
    $this->db->addFields('type');
    $this->db->addFields('enabled');
    $this->db->addFields('createDate');
    $this->db->addWhere('id', '=', $this->id);
    
    // run the query
    $this->db->select('v_users');
    $user = $this->db->fetchRow();
    if($user == false) {
      class_debug::addMsg(__FILE__, 'Unable to retreive user '.$this->id, DEBUGINFO);
      return false;
      }
    
    // load the data
    $this->userLanguage = (string)$user['language'];
    $this->username = (string)$user['username'];
    $this->password = (string)$user['password'];
    $this->type = (string)$user['type'];
    $this->enabled = (string)$user['enabled'];
    $this->createDate = (string)$user['createDate'];
    
    class_debug::addMsg(__FILE__, 'User '.$this->id.' loaded', DEBUGINFO);
    return true;
    }

  /**
   * Return true if the user is an admin, false otherwise
   * @return boolean True if the user is an admin, false otherwise
   * @access public
   */
  public function isAdmin() {
    if($this->type=="admin") return true;
    return false;
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
    class_debug::addMsg(__FILE__, 'User object instantiated with id='.$id, DEBUGDEBUG);
    } // __construct()
  }
?>