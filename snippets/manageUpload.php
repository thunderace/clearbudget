<?php
/**
* Display the list of uploaded files
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      snippets
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
//prevent direct access
if(!defined('ENGINEON')) die('Direct access forbidden');

?>
<h2><img src="style/icons/table_edit.png"/> <?php echo $keys->pageTitle_uploadsManagement; ?></h2>
<input id="confirmMessage" type="hidden" value="<?php echo $keys->text_deleteAllRecordsinImport; ?>">
<?php
if(count($results) == 0) {
  echo '<div class="warning"><blockquote>'.$keys->error_NoTransactionData.'</blockquote></div>';
  echo '<br/><br/><br/><br/><br/><br/><br/>';
  return;
  }
?>
<table class="tableReport" style="width:98%">
<?php
$anchor = '';
$alt = '';
$count = 0;
foreach($results as $result) {
  // data cleanup
  if(!$result['importCount']) $result['importCount'] = 0;
  if(!$result['importDuplicate']) $result['importDuplicate'] = 0;
  if($result['importCount'] > 0) {
    $viewLink = '<a href="#listImportTransactions:importDetails&id='.$result['id'].'"><img src="style/icons/zoom.png"></a>';
    }
  else {
    $viewLink = '-';
    }
  if($result['importDate'] != 'n/a') {
    $importDate =utf8_encode(strftime('%A %d %B %y', strtotime($result['importDate'])));
    }
  else {
    $importDate = $result['importDate'];
    }  
  // presentation stuff
  if($alt == "") $alt = 'class="alt"';
  else $alt = '';
  
  // translate the import type
  switch($result['importType']) {
    case IMPORTQFXFILE:
      $importTypeKey = $keys->text_importTypeQFX;
    break;
    case IMPORTMANUAL:
      $importTypeKey = $keys->text_importTypeManual;
      $result['originalFileName'] = 'n/a';
    break;
    default:
    case IMPORTQIFFILE:
      $importTypeKey = $keys->text_importTypeQIF;
    break;
    case IMPORTCSVFILE:
      $importTypeKey = $keys->text_importTypeCSV;
    break;
    }
  
  if($count == 0 || $count == 5) {
    echo "<tr>";
    echo "<th>".$keys->text_operationDate."</th>";
    echo "<th>".$keys->text_importType."</th>";
    echo "<th>".$keys->text_fileName."</th>";
    echo "<th>".$keys->text_importedTransactions."</th>";
    echo "<th>".$keys->text_duplicatedRecords."</th>";
    echo "<th></th><th></th>";
    echo "</tr>";
    $count = 0;
    }
  echo '<tr>';
  echo '<td '.$alt.' style="width:180px">'.$importDate.'</td>';
  echo '<td '.$alt.' style="width:70px">'.$importTypeKey.'</td>';
  echo '<td '.$alt.' style="width:100px">'.$result['originalFileName'].'</td>';
  echo '<td '.$alt.' style="width:100px">'.$result['importCount'].'</td>';
  echo '<td '.$alt.' style="width:100px">'.$result['importDuplicate'].'</td>';
  echo '<td '.$alt.' style="width:20px">'.$viewLink.'</td>';
  echo '<td '.$alt.' style="width:20px"><a href="javascript:deleteImport(\''.$result['id'].'\')"><img src="style/icons/bin_empty.png"></a></td>';
  echo '</tr>';
  $count++;
  $anchor .= "<a name=\"listImportTransactions:importDetails&id=".$result['id']."\"></a>"; 
  }
?>
</table><br/>
<?php echo $anchor; ?>
<div id="importDetails"><br/><br/><br/><br/><br/><br/><br/></div>
<br/>