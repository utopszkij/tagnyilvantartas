<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.2.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="maincontent" style="border: 1px solid rgb(233, 233, 233);">
	<form method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" style="margin:0px;">
		<div id="folderarea" style="box-shadow: 0px 4px 4px -4px rgba(0, 0, 0, 0.3);padding:15px;">
			<button style="float: right;" class="btn" onclick="changeDisplay(event);" id="btn_change_display" title="<?php echo JText::_('ACY_DISPLAY_NOICON'); ?>"><i id="iconTypeDisplay" class="acyicon-list_view"></i></button>
			<?php
			$folders = acymailing_generateArborescence($this->uploadFolders);
			$filetreeType = acymailing_get('type.filetree');
			$filetreeType->display($folders, $this->uploadFolder, 'currentFolder', 'submitform()');
			?>
		</div>
		<script type="text/javascript">
			var clickedDel = false;
			window.addEvent('domready', function(){
				display(document.getElementById('displayType').value);
			});
			function changeDisplay(event){
				event.preventDefault();
				if(document.getElementById('displayPict').style.display == ''){
					display('list');
				}else{
					display('icons');
				}
			}
			function display(type){
				if(type == 'list'){
					document.getElementById('displayPict').style.display = 'none';
					document.getElementById('displayLine').style.display = '';
					document.getElementById('btn_change_display').title = '<?php echo JText::_('ACY_DISPLAY_ICON'); ?>';
					document.getElementById('iconTypeDisplay').className = 'acyicon-image_view';
					document.getElementById('displayType').value = 'list';
				}else{
					document.getElementById('displayPict').style.display = '';
					document.getElementById('displayLine').style.display = 'none';
					document.getElementById('btn_change_display').title = '<?php echo JText::_('ACY_DISPLAY_NOICON'); ?>';
					document.getElementById('iconTypeDisplay').className = 'acyicon-list_view';
					document.getElementById('displayType').value = 'icons';
				}
			}
			function diplayDeleteBtn(id, action){
				if(action == 'display'){
					document.getElementById('acy_attachment_delete_' + id + '').style.display = '';
				}else{
					document.getElementById('acy_attachment_delete_' + id + '').style.display = 'none';
				}
			}
			function confirmDeleteFile(event, fileName){
				event.preventDefault();
				clickedDel = true;
				var divText = document.getElementById('confirmTxtAttach');
				divText.innerHTML = '<?php echo JText::_('ACY_VALIDDELETEITEMS'); ?>' + '<br /><span class="acy_folder_name">(' + fileName + ')</span><br />';
				var divDelete = document.getElementById('confirmOkAttach');
				divDelete.onclick = function(event){
					event.preventDefault();
					deleteFile(fileName);
				};
				var divConfirm = document.getElementById('confirmBoxAttach');
				divConfirm.style.display = 'inline';
			}
			function deleteFile(fileName){
				var urlFile = window.location.href;
				if(urlFile.lastIndexOf('#') == urlFile.length - 1){
					urlFile = urlFile.substr(0, urlFile.length - 1);
				}
				var lastParam = urlFile.substring(urlFile.lastIndexOf('&') + 1);
				if(lastParam.indexOf('filename=') > -1){
					urlFile = urlFile.substring(0, urlFile.indexOf('filename=') - 1);
				}
				if(urlFile.indexOf('?') > -1){
					window.location.href = urlFile + '&filename=' + fileName;
				}else{
					window.location.href = urlFile + '?filename=' + fileName;
				}
			}
		</script>
		<div id="filesarea" style="width:100%;height:460px;overflow-x: hidden;text-align: center;">
			<?php
			if(file_exists($this->uploadPath)) $files = JFolder::files($this->uploadPath);
			$imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'ico', 'bmp');

			if(in_array($this->map, array('thumb', 'readmore'))){
				$allowedExtensions = $imageExtensions;
			}else{
				$allowedExtensions = explode(',', $this->config->get('allowedfiles'));
				$allowedExtensions = array_merge($allowedExtensions, $imageExtensions);
			}

			if(!empty($files)){
				$k = 0;
				$displayList = '<div id="displayLine" style="display: none; text-align: left;"><table class="acymailing_smalltable">';
				echo '<div id="displayPict">';
				foreach($files as $file){
					if(strrpos($file, '.') === false) continue;

					$ext = strtolower(substr($file, strrpos($file, '.') + 1));
					if(!in_array($ext, $allowedExtensions)) continue;

					$filesFound = true;

					echo '<div style="float: left; text-align: center; position: relative;">';

					$linkStart = '<a href="#" style="text-decoration:none;" onclick="if(clickedDel == false){';
					$linkStart .= "parent.document.getElementById('".$this->map."').value = '".str_replace(DS, '/', $this->uploadFolder)."/$file';";
					if(in_array($this->map, array('thumb', 'readmore'))){
						$linkStart .= "parent.document.getElementById('".$this->map."preview').src = '".JURI::root().str_replace(DS, '/', $this->uploadFolder)."/$file'; ";
					}else{
						$linkStart .= "parent.document.getElementById('".$this->map."selection').innerHTML = '$file'; ";
					}
					$linkStart .= 'window.parent.SqueezeBox.close();}">';

					echo $linkStart;

					$structPict = '<div onmouseover="diplayDeleteBtn('.$k.', \'display\');" onmouseout="diplayDeleteBtn('.$k.', \'hide\');">';
					$structPict .= '<div style="width: 160px;height: 160px;margin: 14px;border: 1px solid rgb(233, 233, 233);border-radius:4px;" onmouseover="this.style.opacity = 0.5;" onmouseout="this.style.opacity = 1;" title="'.$file.'">';
					if(strlen($file) > 20){
						$structPict .= '<span title="'.str_replace('"', '', $file).'">'.substr(rtrim($file, $ext), 0, 17).'...'.$ext.'</span>';
					}else{
						$structPict .= $file;
					}

					if(in_array($ext, $imageExtensions)){
						$imgPath = ACYMAILING_LIVE.$this->uploadFolder.'/'.$file;
					}else{
						$imgPath = ACYMAILING_LIVE.'media/com_acymailing/images/file.png';
					}
					$structPict .= '<br /><img src="'.$imgPath.'" style="margin-top:5px;max-width:150px;"/>';
					$structPict .= '</div>';
					$structPict .= '<img class="acy_attachment_delete" id="acy_attachment_delete_'.$k.'" src="'.ACYMAILING_LIVE.'media'.DS.ACYMAILING_COMPONENT.DS.'images'.DS.'editor'.DS.'delete.png" onclick="confirmDeleteFile(event, \''.$file.'\')" style="display: none;"/>';
					$structPict .= '</div>';

					echo $structPict;
					echo '</a></div>';


					$displayList .= '<tr><td width="30" style="padding-left: 10px;"><img src="'.$imgPath.'" style="max-width:24px;"/></td>';
					$displayList .= '<td>'.$linkStart.$file.'</a></td>';
					$displayList .= '<td><img class="acy_attachment_delete" src="'.ACYMAILING_LIVE.'media'.DS.ACYMAILING_COMPONENT.DS.'images'.DS.'editor'.DS.'delete.png" onclick="confirmDeleteFile(event, \''.$file.'\')"/></td></tr>';
					$k++;
				}
				echo '</div>';
				$displayList .= '</table></div>';
				echo $displayList;
			}

			if(empty($filesFound)) acymailing_display(JText::_('NO_FILE_FOUND'), 'warning');
			?>
			<div class="confirmBoxAttach" id="confirmBoxAttach" style="display: none;">
				<div id="acy_popup_content">
					<span class="confirmTxtAttach" id="confirmTxtAttach"></span><br/>
					<button class="acymailing_button" id="confirmCancelAttach" onclick="event.preventDefault(); clickedDel=false;  document.getElementById('confirmBoxAttach').style.display='none';" style="padding: 6px 15px 6px 10px;">
						<i class="acyicon-cancel" style="margin-right: 5px; font-size: 16px;top: 2px; position: relative;"></i><?php echo JText::_('ACY_CANCEL'); ?>
					</button>
					<button class="acymailing_button acymailing_button_delete" id="confirmOkAttach" style="padding: 8px 15px 6px 10px;">
						<i class="acyicon-delete" style="margin-right: 5px; font-size: 12px;"></i><?php echo JText::_('ACY_DELETE'); ?>
					</button>
				</div>
			</div>
		</div>

		<div id="uploadarea" style="text-align: center;box-shadow: 0px -4px 4px -4px rgba(0, 0, 0, 0.3);padding: 10px 0px 10px 0px;">
			<input type="file" style="width:auto;" name="uploadedFile"/><br/>
			<input type="hidden" name="task" value="select"/>
			<input type="hidden" id="displayType" name="displayType" value="<?php echo $this->displayType; ?>"/>
			<input type="hidden" name="selected_folder" value="<?php echo htmlspecialchars($this->uploadFolder, ENT_COMPAT, 'UTF-8'); ?>"/>
			<?php echo JHTML::_('form.token'); ?>
			<button class="btn btn-primary" type="button" onclick="submit();"> <?php echo JText::_('IMPORT'); ?> </button>
		</div>
	</form>
</div>
