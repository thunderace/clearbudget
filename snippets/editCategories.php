<?php
/**
* Display the category edition screen
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
// get the currency
$currencySymbol = class_currency::getCurrencySymbol();
// function drawing table lines
function makeCategoryTableRow($id, $isParent, $categoryName, $categoryMaxAmount, $categoryColor, $keys, $currencySymbol) {
  $formId = 'categoryForm-'.$id;
  $loadingImage = 'categoryLoading-'.$id;
  $str = '<form id="'.$formId.'" onSubmit="categoryEditForm(\''.$formId.'\', \''.$loadingImage.'\', \''.$id.'\'); return false"><table class="tableReport">';
  $str .= '<tr class="hoverHighlight';
  if($isParent) $str .= ' parentCategory';
  //else $str .= ' hidden';
  $str .= '">';
  $str .= '<td>';
  $str .= '<input type="hidden" value="'.$id.'" name="id">';
  $str .= '<input type="text" value="'.$categoryName.'" name="name"></td>';
  $str .= '<td>';
  $str .= '<select name="color">';
  foreach((array)unserialize(COLORS) as $color) {
    $comp = explode(',', $color);
    $str .= '<option value="'.$comp[0].'" style="background-color: '.$comp[1].';"';
    if($categoryColor == $comp[0]) $str .= ' selected';
    $str .= '>'.$comp[2].'</option>';
    }
  $str .= '</select></td>';
  $str .= '<td>'.$currencySymbol.' <input type="text" value="'.$categoryMaxAmount.'" name="maxAmountPerMonth"></td>';
  $str .= '<td width="30"><img class="hidden" id="'.$loadingImage.'" src="style/icons/icon_Loading.gif"></td>';
  $str .= '<td><input type="image" src="style/icons/disk.png" value="'.$keys->link_save.'"></td>';
  $str .= '</tr></table>';
  $str .= '</form>';
  return $str;
  }
if($context->backLink != '') {
  $backLink = '- <a href="#'.$context->backLink.'" >'.$keys->link_back.'</a>';
  }
else $backLink = "";

?>
<h2><img src="style/icons/tag_blue_edit.png"/> <?php echo $keys->text_categorySettingsSetup; ?> <?php echo $backLink; ?></h2>
<?php
$totalMaxAmount = 0;
//foreach($parentCategories as $key=>$parentCategory) {
//  echo makeCategoryTableRow($parentCategory['id'], true, $parentCategory['name'], $parentCategory['maxAmountPerMonth'], $parentCategory['color'], $keys, $currencySymbol);
//  foreach($categories[$parentCategory['id']] as $category) {
  foreach($categories as $category) {
    echo makeCategoryTableRow($category['id'], false, $category['name'], $category['maxAmountPerMonth'], $category['color'], $keys, $currencySymbol);
    $totalMaxAmount += $category['maxAmountPerMonth'];
    }

  if($numCategories == 0) $additionnalRows = 10;
  else $additionnalRows = 2;
  $i=0;
  while($i<$additionnalRows) {
    echo makeCategoryTableRow('-'.++$i, false, '', '', '', $keys, $currencySymbol);
    }
  
//  }
?>
<table class="tableReport">
   <tr>
     <td style="text-align: right;"><?php echo $keys->text_Total?>: <?php echo $currencySymbol; ?> <input type="text" value="<?php echo $totalMaxAmount;?>" disabled id="totalMaxAmount"> / <?php echo $keys->text_Month; ?></td>
   </tr>
</table>
