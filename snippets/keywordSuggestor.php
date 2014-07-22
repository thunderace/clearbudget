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

?>
<h2><img src="style/icons/link_break.png"/> <?php echo $keys->link_keywordSuggestor; ?></h2>
<?php
if(count($cloud) == 0) {
  $msg = sprintf($keys->error_noSuggestionFound, KEYWORDSUGGESTIONTHRESHOLD);
  echo "<div class=\"warning\"><blockquote>{$msg}</blockquote></div><br/>";
  return;
  }
?>
<div id="cloud">
<?php
$countWord = 0;
foreach($cloud as $name=>$tag) {
  $countWord++;
  echo '<a title="'.$tag['msg'].'" class="tag'.$tag['tag'].'">&#147;'.$name.'&#148;</a>';
  echo '<a href="#editKeywords:subAction2&addKeyWord='.$name.'" title="'.$keys->link_addKeyword.'"><img src="style/icons/link_add.png"/></a>';
  echo '<a href="#search:subAction2&keyWord='.$name.'" title="'.$keys->text_Search.'"><img src="style/icons/zoom.png"/></a>';
  if($countWord == 10) {
    echo '<br/>';
    $countWord = 0;
    }
  }
?>
</div>
<br/>
<div id="subAction2"></div>