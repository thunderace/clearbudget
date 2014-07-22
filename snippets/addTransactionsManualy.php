<?php
/**
* Add a transaction manualy
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

$debitFlag = 'checked';
$creditFlag = '';
if($id != false && $transaction->debit == 0) {
  $debitFlag = '';
  $creditFlag = 'checked';
}
?>
<script type="text/javascript">
	$(function() {
	  $.datepicker.setDefaults($.extend({showMonthAfterYear: false}, $.datepicker.regional['<?php echo $lang;?>']));
		$("#trnDate").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: 'style/icons/calendar.gif', buttonImageOnly: true});
	});
	</script>

<h2><img src="style/icons/table_go.png"/> <?php echo $keys->tableTitle_AddManualTransaction; ?></h2>
<form id="addManualTransaction" onSubmit="addManualTransaction(); return false">
<?php if($id!=false) {?><input type="hidden" name="id" value="<?php echo $id;?>"/><?php }?>
<table class="tableReport">
<tr>
  <td><?php echo $keys->text_operationDate;?><br/><input type="text" <?php if(!$transaction->editable) echo 'disabled'; ?> tabindex="1" size="10" id="trnDate" name="trnDate" value="<?php echo $transaction->operationDate;?>"/></td>
  <td><?php echo $keys->text_amountLong;?><br/><input tabindex="2" <?php if(!$transaction->editable) echo 'disabled'; ?> type="text" name="trnAmount" id="trnAmount" size="11" value="<?php echo $transaction->amount;?>"/> <?php echo class_currency::getCurrencySymbol();?></td>
  <td><?php echo $keys->text_Credit;?><br/><input tabindex="3" <?php if(!$transaction->editable) echo 'disabled'; ?> type="radio" name="trnDebit" <?php echo $creditFlag;?> id="trnDebit" value="0"/></td>
  <td><?php echo $keys->text_Debit;?><br/><input tabindex="3" type="radio" <?php if(!$transaction->editable) echo 'disabled'; ?> name="trnDebit" id="trnDebit" <?php echo $debitFlag;?> value="1"/></td>
  <td><?php echo $keys->text_importedby;?><br/><input disabled readonly type="text" size="30" value="<?php echo $transaction->importedBy;?>"/></td>
  <td><?php echo $keys->text_modifiedby;?><br/><input disabled readonly type="text" size="30" value="<?php echo $transaction->modifiedBy;?>"/></td>
  <td>&nbsp;</td>
</tr>
 <tr>
  <td colspan="4"><?php echo $keys->text_payee;?><br/><input tabindex="4" <?php if(!$transaction->editable) echo 'disabled'; ?> type="text" size="90" name="trnPayee" id="trnPayee" value="<?php echo $transaction->payee;?>"/></td>
  <td colspan="2"><?php echo $keys->text_memo;?><br/><input tabindex="5" <?php if(!$transaction->editable) echo 'disabled'; ?> type="text" size="70" name="trnMemo" id="trnMemo" value="<?php echo $transaction->memo;?>"/></td>
  <td><?php echo $keys->text_commentsLong;?><br/><input tabindex="6" type="text" size="40" name="trnComments" id="trnComments" value="<?php echo $transaction->comments;?>"/></td>
</tr>
<tr>
  <td colspan="7">Category<br/>
<?php
foreach($categories as $categoryId) {
  echo '<input name="trnCategory" tabindex="7" type="radio"';
    echo ' value="'.$categoryId.'"';
    if($id != false) {
      if($transaction->category == $categoryId) echo ' checked';
      }
    elseif($categoryId == 1) echo ' checked';
    echo '/>'.$report->getCategoryName($categoryId).'&nbsp;&nbsp;&nbsp;';
    }
?>
  </td>
</tr>
<tr>
  <td colspan="3" style="text-align: center;">
  <span class="hidden" id="loadingSpan"><img  src="style/icons/icon_Loading.gif"/></span>
  <span class="hidden" id="resultDiv"></span>
  </td>
  <td colspan="4" style="text-align: center;">
  <input type="image" src="style/icons/disk.png" tabindex="8" value="<?php echo $keys->link_save; ?>"/>
<?php if($id != false) {
  echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:clearDiv(\''.$toDiv.'\');"><img  src="style/icons/cross.png" alt="Close"/></a>';
  }
?>
  </td>
</tr>
</table>
</form>
<br/>