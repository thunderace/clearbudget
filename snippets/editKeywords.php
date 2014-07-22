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

function makeKeywordTableRow($id=null, $keyword=null, $keywordCategory=null, $categories=null, $keys) {
  $formId = 'keywordForm-'.$id;
  $loadingImage = 'keywordLoading-'.$id;
  $str = '<form id="'.$formId.'" onSubmit="keywordEditForm(\''.$formId.'\', \''.$loadingImage.'\', \''.$id.'\'); return false"><table class="tableReport">';
  $str .= '<tr class="hoverHighlight">';
  $str .= '<td width="150">';
  $str .= '<input type="hidden" value="'.$id.'" name="id">';
  $str .= '<input type="text" size="80" value="'.$keyword.'" name="keyword" id="keyword'.$id.'">';
  $str .= '</td>';
  $str .= '<td width="150">';
  $str .= '<select name="category">';
  foreach($categories as $category) {
    $str .= '<option value="'.$category['id'].'"';
    if($category['id'] == $keywordCategory) $str .= ' selected';
    $str .= '>'.$category['name'].'</option>';
    }
  $str .= '</select></td>';
  $str .= '<td width="30"><img class="hidden" id="'.$loadingImage.'" src="style/icons/icon_Loading.gif"></td>';
  $str .= '<td width="50"><input type="image" src="style/icons/disk.png" value="'.$keys->link_save.'"></td>';
  if($id>0) {
    $str .= '<td  width="50"><a href="javascript:deleteKeyword(\''.$id.'\')" title="'.$keys->link_delete.'"><img id="buttonImg-'.$id.'" src="style/icons/bin_empty.png"/></a></td>';
    }
  else {
    $str .= '<td  width="50">&nbsp;</td>';
    }
  $str .= '<td></td></tr></table>';
  $str .= '</form>';
  return $str;
  }
  
?>
<div id="editKeywords">
<h2><img src="style/icons/link_edit.png"/> <?php echo $keys->text_keywordSettingsSetup; ?></h2>
<?php
if(count($categories) > 0) {
  $count = 0;
  $additionnalRows = 5;
  if($addKeyWord) {
    echo '<strong>'.$keys->text_newKeyword.'</strong>';
    echo makeKeywordTableRow('-11', $addKeyWord, '', $categories, $keys);
    //echo '<script language="javascript">$("#keyword-11").focus();</script>';
    echo '<br/><br/>';
    return;
    //echo '<strong>'.$keys->text_existingKeywords.'</strong>';
    }
  foreach($keywords as $keyword) {
    $count++;
    echo makeKeywordTableRow($keyword['id'], $keyword['keyword'], $keyword['category'], $categories, $keys);
    }
  if($count == 0) $additionnalRows = 10;
  $maxCount = $count + $additionnalRows;
  while($count<$maxCount) {
    $count++;
    echo makeKeywordTableRow('-'.$count, '', '', $categories, $keys);
    }
  }
else {
  echo '<div class="warning"><blockquote>'.$keys->error_noCategoriesYet.'</blockquote></div>';
  }
?>
<br/>
</div>