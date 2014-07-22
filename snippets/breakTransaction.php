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
// helper function to draw the category selection box
function drawCategorySelect($categories, $id, $category) {
  echo '<select name="trncategory'.$id.'" class="categorySelector">';
  foreach($categories as $catId=>$catName) {
    if($catId == 1) continue;
    echo '<option value="'.$catId.'" ';
    if($category == $catId) echo 'selected';
    echo '>'.$catName.'</option>';
    }
  echo '</select>';
  }
// get the currency
$currencySymbol = class_currency::getCurrencySymbol();
?>
<h2><img src="style/icons/coins_add.png"> <?php echo $keys->tableTitle_breakTransaction; ?> - <a href="#<?php echo $context->backLink;?>" ><?php echo $keys->link_back;?></a></h2>
<form id="transactionBreaker" onSubmit="breakTransactionSubmit(); return false">
<table class="tableReport">
<tr>
  <th colspan="2"><u><?php echo $keys->text_payee; ?></u></th>
  <th><u><?php echo $keys->text_memo; ?></u></th>
  <th><u><?php echo $keys->text_amountLong; ?></u></th>
  <th><u><?php echo $keys->text_operationDate; ?></u></th>
  <th><u><?php echo $keys->text_categoryNameLong; ?></u></th>
</tr>
<tr><td colspan="6" style="display:none;" id="resultDiv"></td></tr>
<tr>
    <th  colspan="2"><input type="text" size="90" value="<?php echo $transaction->payee; ?>" disabled></th>
    <th><input type="text" size="20" value="<?php echo $transaction->memo; ?>" disabled></th>
    <th><?php echo $currencySymbol;?> <input type="text" size="5" value="<?php echo $transaction->amount; ?>" disabled></th>
    <th><input type="text" size="10" value="<?php echo $transaction->operationDate; ?>" disabled></th>
    <th><input type="text" size="5" value="<?php echo $report->getCategoryName($transaction->category); ?>" disabled></th>
</tr>
<tr><td>1</td>
    <td><input type="text" name="trnpayee0" size="50" value="<?php echo $children[0]->payee; ?>"></td>
    <td><input type="text" name="trnmemo0" size="20" value="<?php echo $children[0]->memo; ?>"></td>
    <td><?php echo $currencySymbol;?> <input type="text" name="trnamount0" size="5" value="<?php echo $children[0]->amount; ?>"></td>
    <td><input type="text" size="10" value="<?php echo $transaction->operationDate; ?>" disabled></td>
    <td><?php drawCategorySelect($categories, 0, $children[0]->category); ?></td>
</tr>
<tr><td>2</td>
    <td><input type="text" name="trnpayee1" size="50" value="<?php echo $children[1]->payee; ?>"></td>
    <td><input type="text" name="trnmemo1" size="20" value="<?php echo $children[1]->memo; ?>"></td>
    <td><?php echo $currencySymbol;?> <input type="text" name="trnamount1" size="5" value="<?php echo $children[1]->amount; ?>"></td>
    <td><input type="text" size="10" value="<?php echo $transaction->operationDate; ?>" disabled></td>
    <td><?php drawCategorySelect($categories, 1, $children[1]->category); ?></td>
</tr>
<tr><td>3</td>
    <td><input type="text" name="trnpayee2" size="50" value="<?php echo $children[2]->payee; ?>"></td>
    <td><input type="text" name="trnmemo2" size="20" value="<?php echo $children[2]->memo; ?>"></td>
    <td><?php echo $currencySymbol;?> <input type="text" name="trnamount2" size="5" value="<?php echo $children[2]->amount; ?>"></td>
    <td><input type="text" size="10" value="<?php echo $transaction->operationDate; ?>" disabled></td>
    <td><?php drawCategorySelect($categories, 2, $children[2]->category); ?></td>
</tr>
<tr><td>4</td>
    <td><input type="text" name="trnpayee3" size="50" value="<?php echo $children[3]->payee; ?>"></td>
    <td><input type="text" name="trnmemo3" size="20" value="<?php echo $children[3]->memo; ?>"></td>
    <td><?php echo $currencySymbol;?> <input type="text" name="trnamount3" size="5" value="<?php echo $children[3]->amount; ?>"></td>
    <td><input type="text" size="10" value="<?php echo $transaction->operationDate; ?>" disabled></td>
    <td><?php drawCategorySelect($categories, 3, $children[3]->category); ?></td>
</tr>
<tr><td>5</td>
    <td><input type="text" name="trnpayee4" size="50" value="<?php echo $children[4]->payee; ?>"></td>
    <td><input type="text" name="trnmemo4" size="20" value="<?php echo $children[4]->memo; ?>"></td>
    <td><?php echo $currencySymbol;?> <input type="text" name="trnamount4" size="5" value="<?php echo $children[4]->amount; ?>"></td>
    <td><input type="text" size="10" value="<?php echo $transaction->operationDate; ?>" disabled></td>
    <td><?php drawCategorySelect($categories, 4, $children[4]->category); ?></td>
</tr>
<tr>
    <td colspan="2">
      <input type="hidden" value="<?php echo $children[0]->id; ?>" name="trnid0">
      <input type="hidden" value="<?php echo $children[1]->id; ?>" name="trnid1">
      <input type="hidden" value="<?php echo $children[2]->id; ?>" name="trnid2">
      <input type="hidden" value="<?php echo $children[3]->id; ?>" name="trnid3">
      <input type="hidden" value="<?php echo $children[4]->id; ?>" name="trnid4">
      <input type="hidden" value="<?php echo $transaction->id; ?>" name="parentTransactionId">
    </td>
    <td><?php echo $keys->text_Total;?></td>
    <td><?php echo $currencySymbol;?> <span id="sum"><?php echo $transaction->amount; ?></span></td>
    <td colspan="2"></td>
</tr>
<tr>
    <td colspan="6" style="text-align:center"><input type="submit" value="<?php echo $keys->link_save;?>"></td>
</tr>
</table>
</form>