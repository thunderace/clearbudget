<?php
/**
* File holding the database class
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
* Class acting as a wrapper around PDO objects. It handles database connections
* and let you build nicely formated SQL queries without the need to know SQL.
* 
* Currently supports: Select, Update, Insert with Where and Limit clauses.
* 
* It handles data fetching using 2 modes: single row or all rows.
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @version      $Revision: $
* @access       public
*/
class class_db {
	/**
	* @var object Holds the instance of the current class
	* @access private
	* @static	
	*/
  private static $_instance = null;
  /**
	* @var boolean holds a flag to tell if we are connected or not
	* @access public	
	*/
  public $connected = false;
  /**
	* @var object holds the PDO object connecting to a DB
	* @access private	
	*/
  private $dbConn = null;
  /**
	* @var object Holds a prepared statement
	* @access private	
	*/
  public $statement = null;
  /**
	* @var integer counter of query performed
	* @access public
	* @static	
	*/
  public static $queryCounter = 0;
  /**
	* @var string Holds the Where part of a query
	* @access private	
	*/
  private $whereString = '';
  /**
	* @var array Holds the Where values to be binded to the query
	* @access private
	*/
  private $whereValues = '';
  /**
	* @var string Holds the Limit part of a query
	* @access private
	*/
  private $limitString = '';
  /**
	* @var array Holds the fields part of a query
	* @access private	
	*/
  private $fields = array();
  
  /**
	* Resets the internal values to start fresh for a new query
	* 
	* Called after each query execution.     
	*		
	* @return void
	* @access private
	*/
  private function resetParameters() {
    $this->whereString = '';
    $this->whereValues = '';
    $this->limitString = '';
    $this->fields = array();
    }
  
  /**
	* Save the field name and its value in the internal array 
	*	
	* @param string the field name	
	* @param mixed the field value	
	* @return void
	* @access public
	*/
  public function addFields($field, $value=null) {
    $this->fields[$field] = $value;
    }
    
  /**
	* Save the limit to query data 
	*	
	* @param integer the start point of the query
	* @param integer the number of items to retreive  	
	* @return void
	* @access public
	*/
  public function addLimits($limitStart, $limitEnd) {
    $this->limitString = 'LIMIT '.$limitStart.','.$limitEnd;
    }
  
  /**
	* Save the field name, operator to use, field value and concatenation type 
	*	
	* Fill the internal array as to build a clean Where clause.
	*  	
	* @param string the field name
	* @param string the operator to use to compare the value in DB and the value given
	* @param string the field value
	* @param string Optional - the concatenation string to use (can be AND or OR)      	
	* @return void
	* @access public
	*/
  public function addWhere($field, $operator, $value, $type = 'AND') {
    $this->whereString .= ' '.$type.' ('.$field.' '.$operator.' :'.$field.')';
    $this->whereValues[$field] = $value;
    }
  
  /**
	* Add a where clause with data range 
	*	
	*  	
	* @param string the field name
	* @param string the value range
	* @return void
	* @access public
	*/
  public function addWhereDataRange($field, $value) {
    $this->whereString .= ' AND ('.$field.' IN ('.$value.'))';
    // PDO does not seem to support binding got range values!!!
    //$this->whereValues['dbdataRange'] = $value;
    }
    
  /**
	* Add a where clause for date range 
	*	
	*  	
	* @param string the field name
	* @param string the first field value
	* @param string the second field value	
	* @param string Optional - the concatenation string to use (can be AND or OR)      	
	* @return void
	* @access public
	*/
  public function addWhereBetween($field, $value1, $value2, $type = 'AND') {
    $this->whereString .= " $type ($field between :b1 AND :b2)";
    $this->whereValues['b1'] = $value1;
    $this->whereValues['b2'] = $value2;
    }
  
  /**
	* Build a clean query string from the internal array or from the given array 
	*	
	* @param string the query type (select, insert, update, delete)
	* @param string the table to query
	* @param array Optional - an array of fields name-value pair    	
	* @return void
	* @access public
	*/
  private function buildQuery($type, $table, $fields=null, $distinct = '') {
    $query = '';
    $fieldList = '';
    $bindList = '';
    
    if($fields == null) $fields = $this->fields;
    
    // build the fields and bind string
    foreach($fields as $field=>$val) {
      if($type != 'update') {
        $fieldList .= $field.',';
        }
      else {
        $fieldList .= $field.'=:'.$field.',';
        }
      $bindList .= ':'.$field.',';
      }
    // remove the extra comma
    $fieldList = substr($fieldList, 0 , strlen($fieldList)-1);
    $bindList = substr($bindList, 0 , strlen($bindList)-1);
    $whereList = $this->whereString;
    $limit = $this->limitString;
    
    switch($type) {
      case 'select':
        $query = "SELECT $distinct $fieldList FROM $table WHERE 1=1 $whereList $limit";
      break;
      case 'update':
        $query = "UPDATE $table SET $fieldList WHERE 1=1 $whereList";
      break;
      case 'insert':
        $query = "INSERT INTO $table ($fieldList) VALUES ($bindList)";
      break;
      case 'delete':
        $query = "DELETE FROM $table WHERE 1=1 $whereList";
      break;
      }
    $this->lastQuery = $query;
    return $query;
    }
  
  /**
	* Perform a select query 
	*	
	* @param string the table to query
	* @param boolean Optionnal - a flag to indicate if the function should return the number of rows selected   
	* @param string Optionnal - the distinct keyword  	
	* @return integer the row count
	* @access public
	*/
  public function select($table, $count = false, $distinct = '') {
    $resultCount = 0;
    
    // reset the internal statement
    $this->statement = null;
    
    // build the query
    $selectQuery = $this->buildQuery('select', $table, null, $distinct);
    if($count) $countQuery =  $this->buildQuery('select', $table, array('count(*)'=>''));

    // prepare the query
    if($count) $countStatement = $this->dbConn->prepare($countQuery);
    $this->statement = $this->dbConn->prepare($selectQuery);
    
    
    if($this->statement === false) {
  	  class_debug::addMsg(__FILE__, 'cannot prepare select statement', DEBUGERROR);
  	  $this->resetParameters();
  	  return false;
      }
    class_debug::addMsg(__FILE__, 'Prepared Query ('.$selectQuery.')', DEBUGDEBUG);
    
    // bind the parameters
    foreach($this->fields as $field=>$value) {
      if($value != null) {
        $$field = $value;
        $this->statement->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        class_debug::addMsg(__FILE__, 'Binding parameter :'.$field.' with value="'.$value.'"', DEBUGDEBUG);
        if($count) $countStatement->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        }
      }
  	
    if($this->whereValues!=null && count($this->whereValues)>0 ) {
      foreach($this->whereValues as $field=>$value) {
        $$field = $value;
        $this->statement->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        class_debug::addMsg(__FILE__, 'Binding parameter :'.$field.' with value="'.$value.'"', DEBUGDEBUG);
        if($count) $countStatement->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        }
      }
    
    // execute the query
    $result = $this->statement->execute();
    $this->resetParameters();
    self::$queryCounter++;
    if($result === false) {
      $error = $this->statement->errorInfo();
      class_debug::addMsg(__FILE__, 'cannot Select ('.$error[2].')', DEBUGERROR);
      return false;
			}
		// count the selected rows
    if($count) {
      $countStatement->execute();
      $resultCount = $countStatement->fetchColumn(0);
      class_debug::addMsg(__FILE__, 'Query Executed ('.$resultCount.' rows)', DEBUGDEBUG);
      }
    else {
      class_debug::addMsg(__FILE__, 'Query Executed', DEBUGDEBUG);
    }
    
    return $resultCount;
    } // select()
  
  /**
	* Fetch all rows from an executed statement 
	*	
	* @return array an associated array with all rows as field name-pair value
	* @access public
	*/
  public function fetchAllRows() {
    $result = null;
    // if statement does not exist, we return false
    if($this->statement === false) {
      class_debug::addMsg(__FILE__, 'Last query failed: '.$this->lastQuery, DEBUGERROR);
      return false;
      }
    // fetch the data
    $results = $this->statement->fetchAll(PDO::FETCH_ASSOC);
    return $results;
    } // fecthAllRows()
  
  /**
	* Fetch one row from an executed statement 
	*	
	* @return mixed an associated array of field name-value pair or false if no rows to be fetched
	* @access public
	*/
  public function fetchRow() {
    $result = null;
    
    // if statement does not exist, we return false
    if($this->statement === false) {
      class_debug::addMsg(__FILE__, 'Last query failed: '.$this->lastQuery, DEBUGERROR);
      return false;
      }
      
    // fetch the data
    try {
      $row = $this->statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
      }
    catch(Exception $e) {
      class_debug::addMsg(__FILE__, $e->getMessage(), DEBUGERROR);
      $this->connected = false;
      }
    
    if($row!=false) {
      foreach($row as $field=>$value) {
        $result[$field] = $value;
        }
      return $result;
      }
    return false;
    } // fecthRow()

  /**
	* Perform an insert query 
	*	
	* @param string the table to query
	* @return integer the rowid of the last row inserted
	* @access public
	*/
  public function insert($table) {
    $insertQuery = $this->buildQuery('insert', $table);

    // prepare the query
    $insertStmt = $this->dbConn->prepare($insertQuery);
    if($insertStmt === false) {
  	  class_debug::addMsg(__FILE__, 'cannot Prepare insert statement', DEBUGERROR);
  	  return false;
      }
    
    // bind the parameters
    foreach($this->fields as $field=>$value) {
      if($value != null) {
        $$field = $value;
        $insertStmt->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        class_debug::addMsg(__FILE__, 'Binding parameter :'.$field.' with value="'.$value.'"', DEBUGDEBUG);
        }
      }
  	
  	// execute the insert
  	$result = $insertStmt->execute();
  	$this->resetParameters();
  	class_debug::addMsg(__FILE__, 'Executed Insert query', DEBUGDEBUG);
  	self::$queryCounter++;
    if($result == false) {
      $error = $insertStmt->errorInfo();
      if($error[0] == '23000') {
        class_debug::addMsg(__FILE__, 'duplicate Insert '.$error[1], DEBUGDEBUG);
        return false;
        }
      class_debug::addMsg(__FILE__, 'cannot Insert '.$error[1], DEBUGERROR);
      
      return false;
			}
		return $this->dbConn->lastInsertId();
    } // insert()
  
  /**
	* Perform an update query 
	*	
	* @param string the table to query   	
	* @return boolean true in case of success, false otherwise
	* @access public
	*/
  public function update($table) {
    $updateQuery = $this->buildQuery('update', $table);
    
    // bind the parameters in the query
    class_debug::addMsg(__FILE__, 'preparing '.$updateQuery, DEBUGDEBUG);
    $updateStmt = $this->dbConn->prepare($updateQuery);
    if($updateStmt === false) {
  	  class_debug::addMsg(__FILE__, 'cannot Prepare update statement '.$updateQuery, DEBUGERROR);
  	  return false;
      }
    class_debug::addMsg(__FILE__, 'prepared '.$updateQuery, DEBUGDEBUG);
    
    // bind the parameters
    foreach($this->fields as $field=>$value) {
      if($value != null) {
        $$field = $value;
        $updateStmt->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        class_debug::addMsg(__FILE__, 'Binding parameter :'.$field.' with value="'.$value.'"', DEBUGDEBUG);
        }
      }
  	
    if($this->whereValues!=null && count($this->whereValues)>0 ) {
      foreach($this->whereValues as $field=>$value) {
        $$field = $value;
        $updateStmt->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        class_debug::addMsg(__FILE__, 'Binding parameter :'.$field.' with value="'.$value.'"', DEBUGDEBUG);
        }
      }
    
  	// execute the insert
  	$result = $updateStmt->execute();
  	$this->resetParameters();
  	self::$queryCounter++;
    if($result === false) {
      class_debug::addMsg(__FILE__, 'cannot Update', DEBUGERROR);
      return false;
			}
		class_debug::addMsg(__FILE__, 'executed '.$updateQuery, DEBUGDEBUG);
		return true;
  } // update()
  
  /**
	* Perform a delete query 
	*	
	* @param string the table to query   	
	* @return boolean true in case of success, false otherwise
	* @access public
	*/
  public function delete($table) {
    $deleteQuery = $this->buildQuery('delete', $table);
    
    // bind the parameters in the query
    class_debug::addMsg(__FILE__, 'preparing '.$deleteQuery, DEBUGDEBUG);
    $deleteStmt = $this->dbConn->prepare($deleteQuery);
    if($deleteStmt === false) {
  	  class_debug::addMsg(__FILE__, 'cannot Prepare delete statement', DEBUGERROR);
  	  return false;
      }
    class_debug::addMsg(__FILE__, 'prepared '.$deleteQuery, DEBUGDEBUG);
    
    if($this->whereValues!=null && count($this->whereValues)>0 ) {
      foreach($this->whereValues as $field=>$value) {
        $$field = $value;
        $deleteStmt->bindParam(':'.$field, $$field, PDO::PARAM_STR);
        class_debug::addMsg(__FILE__, 'Binding parameter :'.$field.' with value="'.$value.'"', DEBUGDEBUG);
        }
      }
    
  	// execute the insert
  	$result = $deleteStmt->execute();
  	$this->resetParameters();
  	self::$queryCounter++;
    if($result === false) {
      class_debug::addMsg(__FILE__, 'cannot Delete', DEBUGERROR);
      return false;
			}
		class_debug::addMsg(__FILE__, 'executed '.$deleteQuery, DEBUGDEBUG);
		return true;
  } // delete()
  
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
	* close the active connection 
	*   	
	* @return void
	* @access private
	*/
  private function close() {
    $this->dbConn = null;
    }
  
  /**
	* class destructor 
	*   	
	* @return void
	* @access public
	*/
  public function __destruct() {
    $this->close();
    }
  
  /**
	* class constructor 
	*	
	* Opens up a database connection. Kills the application if no connection are available.	
	* access to this class is private as to implement the singleton pattern
	*   	
	* @return void
	* @access private
	*/ 
  private function __construct() {
    // if file does not exist, something is wrong somewhere so we just quit!
    if(!file_exists(SQLITEDB)) {
      die();//'DB is not accessible or missing');
      }
    // open a DB connection
    try {
      $this->dbConn = new PDO('sqlite:'.SQLITEDB);//, null, null, array(PDO::ATTR_PERSISTENT => true));
      $this->connected = true;
      }
    catch(PDOException $e) {
      class_debug::addMsg(__FILE__, $e->getMessage(), DEBUGERROR);
      $this->connected = false;
      }
    class_debug::addMsg(__FILE__, 'Connected to database', DEBUGDEBUG);
    }
  }
?>