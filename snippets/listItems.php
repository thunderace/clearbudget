<?php
/**
* Display the transactions in a table fashion
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
// build the back link URL to enable to be back to this list from a sub page (i.e.: the break transaction page)
$backLink = urlencode($context->action.'&ajax=1&monthSelected='.$context->monthSelected.'&debit='.$context->debit.'&categorySelected='.$context->categorySelected.'&keyWord='.$context->keyWord);
?>
<a name="editTransactionManualy"></a>
<div id="editTransactionManualy" class="hidden"></div>
<h2><?php echo $keys->tableTitle_transactions; ?></h2>
<table id="listItemsTable" class="tableReport tablesorter">
<thead><tr><th><?php echo $keys->text_categoryNameLong;?></th><th><?php echo $keys->text_payee;?></th><th><?php echo $keys->text_amountLong.' 
('.class_currency::getCurrencySymbol().')'; ?></th><th><?php echo $keys->text_operationDate; ?></th><td colspan="4" style="text-align: center;width: 
140px;"><div id="pager" class="pager" ><form><img src="style/icons/first.png" class="first"/><img src="style/icons/prev.png" class="prev"/><input type="text" 
size="3" class="pagedisplay"/><img src="style/icons/next.png" class="next"/><img src="style/icons/last.png" class="last"/><select class="pagesize"><option  
value="20">20</option><option value="40">40</option><option selected="selected" value="60">60</option></select></form></div></td></tr></thead>
<tbody>
<?php
  $total = 0;
  foreach($results as $result) {
    $categoryFormId = 'transactionEditCategoryForm-'.$result['id'];
    $categoryLoadingImage = 'catLoading-'.$result['id'];
    if($result['debit'] == "1") {
      $amountStyle = 'color:red;';
      $amountSign = '-';
      $total -= $result['amount'];
      }
    else {
      $amountStyle = 'color:green;';
      $amountSign = '+';
      $total += $result['amount'];
      }
    if($result['importType'] == IMPORTMANUAL) $editable = true;
    else $editable = false;
  ?>
  <tr class="hoverHighlight">
  <!--<td style="width:120px"><?php echo $result['categoryName']?></td>-->

  <td style="width:200px"><form id="<?php echo $categoryFormId; ?>" onSubmit="transactionEditForm('<?php echo $categoryFormId;?>', '<?php echo $categoryLoadingImage;?>', '<?php echo $result['id'];?>'); return false">
  <input type="hidden" name="id" value="<?php echo $result['id'];?>"/>
  <select style="width:150px" onChange="transactionEditForm('<?php echo $categoryFormId;?>', '<?php echo $categoryLoadingImage;?>', '<?php echo $result['id'];?>'); return false" name="category" class="categorySelector">
  <?php // list all possible categories
  foreach($categories as $id=>$name) {
    // show the uncategorized option only if item is already uncategorized
    if($id == 1 && $result['category'] != 1) continue;
    echo '<option value="'.$id.'"';
    if($result['category'] == $id) echo ' selected="selected"';
    echo '>'.$name.'</option>';
    }
  ?>
  </select><!--<input type="image" src="style/icons/disk.png" title="<?php echo $keys->link_save;?>"/>-->
  <span><img class="hidden" id="<?php echo $categoryLoadingImage;?>" src="style/icons/icon_Loading.gif"/></span></form>
  </td>

  <td><?php echo $result['payee']; if(strlen($result['memo']) > 0) echo '<br/>'.$result['memo']; if(strlen($result['comments']) > 0) echo '<br/>'.$keys->text_commentsLong.': '.$result['comments']; ?></td>
  <td style="width:110px;<?php echo $amountStyle; ?>"><?php echo $amountSign.$report->formatNumber($result['amount']);?></td>
  <td style="width:170px"><?php echo utf8_encode(strftime('%d-%B-%y (%A)', strtotime($result['operationDate']))); ?></td>
  <td style="width:35px"><?php
  if($result['parentId'] == 0) {  echo '<a href="#addTransactionsManualy:editTransactionManualy&id='.$result['id'].'" title="'.$keys->linkText_taskEditLink.'"><img src="style/icons/table_edit.png"/></a></td>'; }
  else { echo '<img src="style/icons/delete.png"/>'; }
  ?></td>
  <td style="width:35px"><?php
  if($result['parentId'] == 0) {  echo '<a href="javascript:deleteTransaction(\''.$result['id'].'\')" title="'.$keys->link_delete.'"><img id="buttonImg-'.$result['id'].'" src="style/icons/bin_empty.png"/></a></td>'; }
  else { echo '<img src="style/icons/delete.png"/>'; }
  ?></td>
  <td style="width:35px"><?php
  echo '<a href="#breakTransaction&backLink='.$backLink.'&id='.$result['id'].'" title="'.$keys->linkText_breakTransactionLink.'">';
  if($result['parentId'] > 0) echo '<img src="style/icons/coins.png"/>';
  else echo '<img src="style/icons/coins_add.png"/>';
  echo '</a>'
  ?></td>
  <td style="width:35px"><?php
  echo '<a href="#taskScheduler&backLink='.$backLink.'&id='.$result['id'].'" title="'.$keys->linkText_taskReminderLink.'"><img src="style/icons/clock_add.png"/></a>';
  ?></td>
  </tr>
  <?php
  }
  ?>
</tbody>
</table>

<br/><br/><br/><br/>
