<?php
/**
* Display the search results screen
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
<h1><img src="style/icons/zoom.png"/> <?php echo $keys->text_Search; ?> "<?php echo $keyWord; ?>"</h1>
<?php
if($error) {
  echo '<div class="error"><blockquote>'.$error.'</blockquote></div><br/>';
  }
else {
  echo '<div class="tableDiv">';
  //echo '<label>'.$keys->text_SearchForString.' <b>"'.$keyWord.'"</b> ('.$count.' '.$keys->text_SearchResultCountString.')<br/></label>';
  $title = $keys->text_SearchForString.' <b>"'.$keyWord.'"</b> ('.$count.' '.$keys->text_SearchResultCountString.')'; 
  if($results != null) {
    // include the display
    include('listItems.php');
    }
  else {
    echo $keys->text_noResult;
    }
  echo '</div>';
  }
?>
<br/>