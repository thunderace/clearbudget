<?php
/**
* Display the upload transaction file screen
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
<h2><img src="style/icons/table_gear.png"/> <?php echo $keys->pageTitle_UploadQfx; ?></h2>
<form id="QIFUploadForm" enctype="multipart/form-data" action="" method="POST">
<div id="qifUploadError" style="display: none" class="error"><blockquote><?php echo $keys->error_UploadQif; ?>: <span id="qifUploadErrorText"></span></blockquote></div>
<table class="tableReport">
  <tr>
    <td colspan="3">
      <span id="qifUploadLoading" style="display:none;"><?php echo $keys->text_pageUploadQifLoading; ?></span>
      <span id="qifUploadResultCount" style="display:none; color: #3A7;"></span>
    </td>
  </tr>
  <tr>
  <td style="width: 350px">
    <input name="MAX_FILE_SIZE" value="300000" type="hidden" />
    <input name="action" id="action" value="uploadQfxSubmit" type="hidden" />
    <input name="datafile"  id="fileToUpload"  type="file" size="60"/>
  </td>
  <td style="width: 300px">
    &nbsp;
  </td>
  <td style="width: 150px">
    <span class="jqUploader" id="jqUploader">
    <button class="button" id="buttonUpload" onclick="return ajaxFileUpload();"><?php echo $keys->text_SendButton;?></button>
    </span>
  </td>
</td>
</tr>
</table>
</form>
<div id="importDetails"></div>
<br/>