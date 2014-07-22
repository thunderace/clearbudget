<?php
/**
* Display the login screen
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
$userSettings = class_settings::getInstance();
$userSettings->getSettings();
if($logoutSuccess) { ?>
<div id="successLogout" class="success"><blockquote><?php echo $keys->error_successLogout; ?></blockquote></div><br/>
<script>document.title="clearBudget";</script>
<?php } ?>
<div id="error" style="display:none;"></div><br/>
<form id="loginForm" onSubmit="doLogin(); return false">
<input type="hidden" name="backAction" value="<?php echo $backAction ?>">
<table class="tableReport">
<tr><td><?php echo $keys->text_login; ?></td>
<td><input type="text" value="" name="username" id="login"></td></tr>
<tr><td><?php echo $keys->text_password; ?></td>
<td><input type="password" value="" name="password" id="password"></td></tr>

<tr><td colspan="2"  style="text-align:center" ><input type="submit" class="submit" name="submit" value="Go"></td></tr>
</table>
</form>
