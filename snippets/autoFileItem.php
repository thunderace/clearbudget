<?php
/**
* Display the results of the auto filling
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
<h2><img src="style/icons/database_gear.png"/> <?php echo $keys->pageTitle_AutoFileItems; ?></h2>
<?php
  if($updateCount > 0) {
    echo '<div class="success"><blockquote>'.$updateCount.' '.$keys->text_autoFileResult.'</blockquote></div>';
    echo '<br/><br/><br/><br/><br/><br/><br/>';
    }
  else {
    echo '<div class="warning"><blockquote>'.$updateCount.' '.$keys->text_autoFileResult.'</blockquote></div>';
    echo '<br/><br/><br/><br/><br/><br/><br/>';
    }
?>
