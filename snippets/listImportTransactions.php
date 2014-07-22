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
<h2><?php echo $importTypeKey; ?>: <?php echo $importInfo['originalFileName'];?> (<?php echo $importInfo['importDate']; ?>)</h2>
<?php
echo '<table class="tableReport" style="width:98%">';
// the currency symbol
$currencySymbol = class_currency::getCurrencySymbol();
// a counter
$count = 0;
if(!$transactions) {
  echo '<tr><td>'.$keys->error_GenericError.'</td></tr>';
  }
else {
  echo '<tr><th></th><th>'.$keys->text_operationDate.'</th><th>'.$keys->text_amount.'</th>';
  echo '<th>'.$keys->text_memo.'</th><th>'.$keys->text_commentsLong.'</th><th>'.$keys->text_importedby.'</th><th>'.$keys->text_modifiedby.'</th><th></th></tr>';
  foreach($transactions as $id=>$transaction) {
    $count++;
    // add the +/- sign on the transaction
    if($transaction->debit) $transaction->amount = '-'.$transaction->amount;
    else $transaction->amount = '+'.$transaction->amount;
    // switch between deleted and not deleted transactions
    if($transaction->deleteFlag == "1") {
      $class = 'class="deletedRow"';
      $buttonImg = 'style/icons/arrow_undo.png';
      }
    else {
      $class = '';
      $buttonImg = 'style/icons/bin_empty.png';
      }
    // build the row
    echo "<tr {$class} id=\"transaction-{$id}\">";
    echo '<td style="width:20px">'.$count.'</td>';
    echo '<td style="width:100px" >'.$transaction->operationDate.'&nbsp;</td>';
    echo '<td style="width:80px">'.$currencySymbol.' '.$transaction->amount.'&nbsp;</td>';
    echo '<td style="width:400px">'.$transaction->payee.'&nbsp;</td>';
    echo '<td style="width:200px">'.$transaction->memo.'&nbsp;</td>';
    echo '<td style="width:200px">'.$transaction->importedBy.'&nbsp;</td>';
    echo '<td style="width:200px">'.$transaction->modifiedBy.'&nbsp;</td>';
    if($listOnly != 'true') {
      echo "<td style=\"width:20px\"><a href=\"javascript:deleteTransaction('{$id}')\"><img id=\"buttonImg-{$id}\" src=\"{$buttonImg}\"/></a></td>";
      }
    echo '</tr>';
    // if it is a parent, display its child
    if($transaction->isParent == 1) {
      $childs = $transactionFactory->getChildTransactions($id);
      foreach($childs as $child) {
        echo '<tr ><td><img src="style/icons/arrow_merge.png"></td>';
        echo '<td style="width:100px" >'.$child->operationDate.'&nbsp;</td>';
        echo '<td style="width:80px">'.$child->amount.'&nbsp;</td>';
        echo '<td style="width:400px">'.$child->payee.'&nbsp;</td>';
        echo '<td colspan="2" style="width:200px">'.$child->memo.'&nbsp;</td>';
        echo '</tr>';
        }
      }
    }
  }
echo '</table>';
?>