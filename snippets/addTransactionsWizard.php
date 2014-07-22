<?php
/**
* Display the action screen
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

<table width="100%" class="tableReport">
  <tr>
    <td style="text-align: left; width: 150px;"><a href="#uploadQif:subAction1"><img src="style/icons/table_add.png"/> <?php echo $keys->link_uploadQif; ?></a></td>
    <td style="text-align: left; width: 150px;"><a href="#uploadQfx:subAction1"><img src="style/icons/table_gear.png"/> <?php echo $keys->link_uploadQfx; ?></a></td>
    <td style="text-align: left; width: 150px;"><a href="#uploadCSV:subAction1"><img src="style/icons/page_white_text.png"/> <?php echo $keys->link_uploadCSV; ?></a></td>
    <td style="text-align: left; width: 150px;"><a href="#addTransactionsManualy:subAction1"><img src="style/icons/table_go.png"/> <?php echo $keys->link_uploadManual; ?></a></td>
  </tr>
</table>
<br/>
<div id="subAction1"></div>
