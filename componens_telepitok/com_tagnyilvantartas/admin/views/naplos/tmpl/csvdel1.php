<?php
/**
* @version		$Id:edit.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');
$this->item = $this->Item;
$naplo_id = JRequest::getVar('naplo_id');
itemAccess($this->item, $userCsoport);
//formAccess($this->form, $userCsoport); 
echo '<div>
<h2>CSV IMPORT törlése</h2>
<p>Ezzel a funkcióval törölheti az összes olyan kapcsolat adatot amik az alábbi jelzéssel vannak ellátva a változásnaplóban:</p>
<h3>'.$this->Item->lastact_info.'</h3>
<p>Az ilyen jelzéssel ellátott adatok akkor is törlődnek, ha a beolvasás után már módosítás történt rajtuk.</p>
<p> </p>
<p>
  <button type="button" onclick="okClick()" style="background-color:red">Indulhat a törlés</button>&nbsp;
  <button type="button" onclick="cancelClick()">Mégsem</button>&nbsp;
</p>
</div>
<script type="text/javascript">
  function okClick() {
	  document.location="index.php?option=com_tagnyilvantartas&task=naplos.csvdelete2&naplo_id='.$naplo_id.'";
  }
  function cancelClick() {
	  document.location = "index.php";
  }
</script>
';
?>
