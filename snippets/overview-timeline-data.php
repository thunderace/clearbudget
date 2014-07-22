<?php
/**
* Display the homepage screen
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
global $context;
// get the currency
$currencySymbol = class_currency::getCurrencySymbol();
// send the XML type header - necessary for Prototype
header ("content-type: text/xml");
// the root tag
echo '<data>';
// loop through all transactions groups
foreach($items as $date=>$transactionTypes) {
  // format the date as timeline expects it
  $operationDate = utf8_encode(date('M d Y 00:00:00 \G\M\T', strtotime($date)));
  // loop through all this group transactions type (debit or credit)
  foreach($transactionTypes as $debit=>$transactions) {
    $count = count($transactions);
    $text = "";
    $total = 0;
    $categoryMarker = "";
    // loop through all the transactions
    foreach($transactions as $transaction) {
      // define the html IDs
      $categoryLoadingImage = 'categoryLoadingImage-'.$transaction["id"];
      $categoryFormId = 'categoryForm-'.$transaction["id"];
      $selectBoxId = 'selectBoxId-'.$transaction["id"];
      // sum up the amounts
      $total += $transaction["amount"];
      // if debits, add the - sign
      if($debit) $transaction["amount"] = -$transaction["amount"];
      // build the bubble html
      $text .= "<tr>\n";
      $text .= "<td style=\"width:120px\">\n";
      $text .= "<form id=\"$categoryFormId\" onSubmit=\"transactionEditForm('$categoryFormId', '$categoryLoadingImage', '{$transaction['id']}'); return false\">";
      $text .= "<input type=\"hidden\" name=\"id\" value=\"{$transaction['id']}\"/>";
      // list all possible categories
      $text .= "<select id=\"$selectBoxId\" style=\"width:90px\" onChange=\"transactionEditForm('$categoryFormId', '$categoryLoadingImage', '{$transaction['id']}'); return false\" name=\"category\" class=\"categorySelector\">";
      foreach($categories as $id=>$name) {
        // show the uncategorized option only if item is already uncategorized
        if($id == 1 && $transaction['category'] != 1) continue;
        $text .= '<option value="'.$id.'"';
        if($transaction['category'] == $id) $text .= ' selected="selected"';
        $text .= '>'.$name.'</option>';
        }
      $text .= "</select>";
      $text .= " <span><img class=\"hidden\" id=\"$categoryLoadingImage\" src=\"style/icons/icon_Loading.gif\"/></span>";
      $text .= "</form></td>\n";
      $text .= "<td>{$transaction['payee']}</td>\n";
      $text .= "<td style=\"width:50px\">{$currencySymbol}{$transaction['amount']}</td>\n";
      //$text .= "<td><a href=\"#addTransactionsManualy:editTransactionManualy&id={$transaction['id']}\"><img src=\"style/icons/table_edit.png\"/></a></td>";
      $text .= "</tr>\n";
      //$text .= "<tr><td colspan=\"3\">{$transaction['payee']}</td></tr>\n";
      }
    // if debits, add the - sign
    if($debit) $total = -$total;
    // the bubble link title
    $summary = "$currencySymbol $total ($count)";
    // xml output
    if($debit) echo '<event color="#FF0000" start="'.$operationDate.'" title="'.$summary.'" icon="style/icons/bullet_red.png">';
    else  echo '<event color="#00FF00" start="'.$operationDate.'" title="'.$summary.'" icon="style/icons/bullet_green.png">';
    echo htmlentities("<table class=\"tableReport\">\n{$text}</table>");
    echo "</event>\n";
    }
  }
echo '</data>';
?>