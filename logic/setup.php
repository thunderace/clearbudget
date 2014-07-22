<?php
/**
* check if the application is setup correctly
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
// default before 0.7.1
$previousVersion = '061';

// clear the stat cache
clearstatcache();

// if action is required then we do not process the setup page 
if(isset($_REQUEST['action'])) {
  return;
  }

// check php version
if(version_compare(PHP_VERSION, MIN_PHP_VERSION, '<')) {
  die('Running PHP version is '.PHP_VERSION.' - Clearbudget requires PHP '.MIN_PHP_VERSION.' at minimum');
  }

// check for PDO existence
if(!class_exists('PDO')) {
  die('Clearbudget requires the PDO PHP extension.<br/>This should be default in PHP but you might have to enable it in your php.ini file.');
  }

// check if PDO is install with sqlite support
if(!in_array('sqlite', PDO::getAvailableDrivers())) {
  die('Clearbudget requires PDO with SQLITE supports.<br/>This should be default in PHP but you might have to enable it in your php.ini file.');
  }

// check if JSON extension is present
if(!function_exists('json_encode')) {
  die('Clearbudget requires JSON supports.<br/>This is default in PHP 5.2.x. If you run PHP below 5.2.0, then you need to install the <a href="http://pecl.php.net/package/json">JSON extension</a> following directives at <a href="http://www.php.net/manual/en/install.pecl.php">http://www.php.net/manual/en/install.pecl.php</a>');
  }
  
// check if the DB folder is writable
clearstatcache();
if(!is_writable(SQLITEDBROOTFOLDER)) {
  die('The database folder ('.SQLITEDBROOTFOLDER.') must be writable by your webserver and by PHP');
  }
if(file_exists(SQLITEDB) && !is_writable(SQLITEDB)) {
  die('The database ('.SQLITEDB.') must be writable by your webserver and by PHP');
  }

function pdoError(&$conn, $query) {
  $msg = 'Error while performing '.$query.' ';
  $err = $conn->errorInfo();
  if(isset($err[0])) $msg .= ' - '.$err[0];
  if(isset($err[1])) $msg .= ' - '.$err[1];
  if(isset($err[2])) $msg .= ' - '.$err[2];
  die($msg);
  }
  
function to71(&$conn) {
    // update to 0.7.1
    include('db'.DIRECTORY_SEPARATOR.'budget_061_sql.php');
    foreach($sql as $query) {
      $stmt = $conn->query($query);
      if($stmt === false) {
        die('error while updating the database in: '.$query);
        }
      $stmt->closeCursor();
      }
    // now we have to update the user password to the hash version of it
    $password = '';
    $stmt = $conn->query('SELECT password FROM t_settings WHERE 1=1');
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $password = $result['password'];
    $stmt->closeCursor();
    
    // hash the password
    $password = md5($password);
    // update the password
    if($password) {
      $stmt = $conn->query('UPDATE t_settings SET password  = "'.$password.'";');
      if($stmt === false) {
        pdoError($conn, $query);
        }
      $stmt->closeCursor();
      }
    }

function to72(&$conn) {
    // upgrade the DB
    include('db'.DIRECTORY_SEPARATOR.'budget_071_sql.php');
    foreach($sql as $query) {
      $stmt = $conn->query($query);
      if($stmt === false) {
        pdoError($conn, $query);
        }
      $stmt->closeCursor();
      }
    // some logic to 'fake' the imports of existing transations
    // check if previously imported records are present
    $stmt = $conn->query('SELECT count(id) AS counter, createDate FROM t_items WHERE 1=1 GROUP BY createDate');
    if($stmt === false) {
      pdoError($conn, $query);
      }
    while(($data = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
      // create an import for that date
      $query = 'INSERT INTO t_imports (`originalFileName`,`importCount`, `importDuplicate`, `importDate`)';
      $query .= ' VALUES ';
      $query .= '("n/a", "'.$data['counter'].'", "0", "'.$data['createDate'].'");';
      $stmt2 = $conn->query($query);
      if($stmt2 === false) {
        pdoError($conn, $query);
        }
      $stmt2->closeCursor();
      // update all related records with this import ID
      $query = 'UPDATE t_items SET `importId` = "'.$conn->lastInsertId().'" WHERE createDate = "'.$data['createDate'].'";';
      $stmt3 = $conn->query($query);
      if($stmt3 === false) {
        pdoError($conn, $query);
        }
      $stmt3->closeCursor();
      }
    $stmt->closeCursor();
    }

function to81(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_072_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  // update all previous import with QIF type (as it can only come from QIF files)
  $query = 'UPDATE t_imports SET `importType` = "0"';
  $stmt = $conn->query($query);
  if($stmt === false) {
    pdoError($conn, $query);
    }
  $stmt->closeCursor();
  }

function to82(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_081_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to85(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_082_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to91(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_085_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to92(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_091_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to93(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_092_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  
  // languages before 0.9.3 were euro,dollar,BritishPound,turkishLira,SwissFrancs
  // now it is following ISO 4217
  $stmt = $conn->query('SELECT currency FROM t_settings WHERE id=1');
  if($stmt === false) {
      pdoError($conn, $query);
      }
  $currency = $stmt->fetch();
  $stmt->closeCursor();
  switch($currency) {
    case 'euro': $currency = "EUR";
    break;
    case 'dollar': $currency = "USD";
    break;
    case 'BritishPound': $currency = "GBP";
    break;
    case 'turkishLira': $currency = "TRY";
    break;
    default: $currency = "USD";
    break;
    }
  // update the currency with the new ISO code
  $query = 'UPDATE t_settings SET `currency` = "'.$currency.'" WHERE id=1;';
  $stmt = $conn->query($query);
  if($stmt === false) {
    pdoError($conn, $query);
    }
  }

function to95(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_093_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to96(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_095_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to97(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_096_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  // convert users from settings table to users table
  // insert the username, password, language and set the type to admin by default
  $query = 'SELECT username, password, language FROM t_settings WHERE id=1;';
  $stmt = $conn->query($query);
  if($stmt === false) {
    pdoError($conn, $query);
    }
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $username = $result['username'];
  $password = $result['password'];
  $language = $result['language'];
  $stmt->closeCursor();
  $query = 'INSERT INTO t_users (`username`,`password`, `language`, `type`, `enabled`, `createDate`)';
  $query .= ' VALUES ';
  $query .= '("'.$username.'","'.$password.'", "'.$language.'", "admin", "1", "'.date("Y-m-d").'");';
  $stmt = $conn->query($query);
  if($stmt === false) {
    pdoError($conn, $query);
    }
  $stmt->closeCursor();
  }

function to98(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_097_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  }

function to99(&$conn) {
  // upgrade the DB
  include('db'.DIRECTORY_SEPARATOR.'budget_097_sql.php');
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }

  // make sure all existing categories are top parent
  $stmt = $conn->query("UPDATE t_categories SET `parentId` = 0;");
  if($stmt === false) {
    pdoError($conn, $query);
    }
  $stmt->closeCursor();
  }

function makeBackup($previousVersion) {
  // make a copy of the existing DB file before doing any modification to it
  copy(SQLITEDB, SQLITEDB.'.'.$previousVersion.'.bak');
  class_debug::addMsg(__FILE__, 'Made DB file backup', DEBUGINFO);
  }
  
// if the DB data file is still in the old folder, move it to the new one with the new name
if(file_exists(OLDSQLITEDB)) {
  rename(OLDSQLITEDB, SQLITEDB);
  class_debug::addMsg(__FILE__, 'Moved DB file to new folder', DEBUGINFO);
  }
  
// check if DB exists, if not we need to create a blank one
if(!file_exists(SQLITEDB)) {
  class_debug::addMsg(__FILE__, 'DB does not exist - new install', DEBUGDEBUG);
  // get a new DB handler ( create the db if does not exist)
  try { 
    $conn = new PDO('sqlite:'.SQLITEDB);
    }
  catch(PDOException $e) {
    die('can\'t create DB - check that the web server has write permission to the "db" folder.');
    }
  // load the DB structure
  include('db'.DIRECTORY_SEPARATOR.'budget_sql.php');
  // create all the DB structure
  foreach($sql as $query) {
    $stmt = $conn->query($query);
    if($stmt === false) {
      pdoError($conn, $query);
      }
    $stmt->closeCursor();
    }
  // set the language to the user browser setting or the default one
  $lang = getUserAgentLanguage();
  $stmt = $conn->query("UPDATE t_settings SET `language` = '$lang' WHERE id=1;");
  if($stmt === false) {
    pdoError($conn, $query);
    }
  $stmt->closeCursor();
  class_debug::addMsg(__FILE__, 'DB created successfully', DEBUGINFO);
  }
else { // DB exists here
  class_debug::addMsg(__FILE__, 'Getting current version from DB', DEBUGDEBUG);
  // try to get the version from the DB - it did not exist before 0.7.1
  $conn = new PDO('sqlite:'.SQLITEDB);
  $stmt = $conn->query('SELECT cb_version FROM t_version WHERE 1=1');
  if($stmt === false) {
    pdoError($conn, $query);
    }
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $previousVersion = $result['cb_version'];
  $stmt->closeCursor();
  class_debug::addMsg(__FILE__, 'Current version is '.$previousVersion, DEBUGINFO);
  
  // switch depending on the version
  switch($previousVersion) {
    case '061':
      makeBackup($previousVersion);
      to71($conn);
      to72($conn);
      to81($conn);
      to82($conn);
      to85($conn);
      to91($conn);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.7.1':
      makeBackup($previousVersion);
      to72($conn);
      to81($conn);
      to82($conn);
      to85($conn);
      to91($conn);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.7.2':
      makeBackup($previousVersion);
      to81($conn);
      to82($conn);
      to85($conn);
      to91($conn);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.8.1':
      makeBackup($previousVersion);
      to82($conn);
      to85($conn);
      to91($conn);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.8.2':
      makeBackup($previousVersion);
      to85($conn);
      to91($conn);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.8.5':
      makeBackup($previousVersion);
      to91($conn);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.9.1':
      makeBackup($previousVersion);
      to92($conn);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.9.2':
      makeBackup($previousVersion);
      to93($conn);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.9.3':
      makeBackup($previousVersion);
      to95($conn);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.9.5':
      makeBackup($previousVersion);
      to96($conn);
      to97($conn);
      to98($conn);
    break;
    case '0.9.6':
      makeBackup($previousVersion);
      to97($conn);
      to98($conn);
    break;
    case '0.9.7':
      makeBackup($previousVersion);
      to98($conn);
    break;
    default:
      class_debug::addMsg(__FILE__, 'Nothing done as DB is up to date', DEBUGINFO);
    break;
    }
  }
$conn = null;
class_debug::addMsg(__FILE__, 'Setup Done', DEBUGDEBUG);
?>