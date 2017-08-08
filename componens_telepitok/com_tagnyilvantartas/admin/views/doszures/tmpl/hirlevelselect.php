<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

// filterStr kialakitása a JRequest-ben érkezett adatokból.
$filterStr = '';
if (JRequest::getVar('onlychecked')==1) $filterStr = 'Csak az ellenőrzött adatok';
for ($i=1; $i<20; $i++) {
    if (JRequest::getVar('mezo'.$i) != '') {
       if ($filterStr != '') 
           $filterStr .= ' és <br />';           
       $rel = JRequest::getVar('relacio'.$i);
       if ($rel == 'lt') $rel = '&lt;';
       if ($rel == 'lte') $rel = '&lt;=';
       if ($rel == 'gt') $rel = '&gt;';
       if ($rel == 'gte') $rel = '&gt;=';
       if ($rel == 'ne') $rel = '&lt;&gt;';
       if ($rel == 'like') $rel = 'benne;';
       if ($rel == 'between') $rel = 'tól-ig;';
       $filterStr .= JText::_(JRequest::getVar('mezo'.$i)).' '.
                     $rel.' '.
                     JRequest::getVar('ertek'.$i);
    }    
}
$jsLink = "location='';";
?>
<script type="text/javascript">
  function regiekClick() {
	  document.getElementById("regebbiekLink").style.display='none';
	  document.getElementById("regihirlevelek").style.display='block';
	  return;
  }
  function validateDate(isoDate) {

		if (isNaN(Date.parse(isoDate))) {
			return false;
		} else {
			if (isoDate != (new Date(isoDate)).toISOString().substr(0,10)) {
				return false;
			}
		}
		return true;
  }  
  function validator() {
	var result = false;  
	if (document.getElementById('startDatum').value == '') {
		document.forms.adminForm.submit();
		result = true;
	} else {  
	    if (validateDate(document.getElementById('startDatum').value)) {
		  var mainap = new Date();
		  var startDatum = new Date(document.getElementById('startDatum').value);
		  if (startDatum <= mainap)	{
			alert('Csak a mai napnál késöbbi ütemezést adhatsz meg!');
			result = false;
		  } else {
			document.getElementById('startDatum').value = startDatum.toISOString().substring(0,10);
			document.forms.adminForm.submit();
			result = true;
		  }	  
		} else {
			alert('Hibás ütemezési dátum forma');
			result = false;
		}
	}
	return result;
  }
  function kampanyKapcsolas() {
	document.forms.adminForm.task.value = 'doszures.kampanykapcsolas';
	document.forms.adminForm.submit();
	result = true;
  } 
  function infoClick(mailid) {
	  link="<?php echo JURI::base(); ?>index.php?option=com_tagnyilvantartas&view=doszures&task=doszures.hirlevelinfo&tmpl=component&mailid="+mailid;
	  window.open(link,"Hírlevél infó","left=100,top=100,width=800,height=700");
	  return false;
  }
</script>
<div class="lmpForm">
<form action="index.php?option=com_tagnyilvantartas&view=doszures" method="post" name="adminForm" id="adminForm">
    <div class="szuresInfo"><?php echo $filterStr; ?></div>
    <div class="clear"></div>
	<p>Válasaza ki az előre megszerkesztett hírlevel közül amit küldeni akar
	  vagy 
	  <a href="index.php?option=com_acymailing&ctrl=newsletter" target="_new" class="button">
	  hozzon létre egy új hírlevelet</a>
	  (új böngészőlap nyilik) 
	</p>
	
	<p>Prioritás:
	  <select name="prioritas" style="width:100px;">
		<option value="1">Sürgős</option>
		<option value="2" selected="selected">Közepes</option>
		<option value="3">Ráér</option>
	  </select>
	  &nbsp;&nbsp;&nbsp;Küldés ütemezése (éééé-hh-nn üres:azonnal):
	  <input type="text" name="startDatum" id="startDatum" value="" size="10" />
	</p>
	  <input type="radio" name="emailmezo" value="email" checked="checked" />&nbsp;Elsődleges E-mail címre<br />
	  <input type="radio" name="emailmezo" value="belsoemail"  />&nbsp;Belső E-mail címre&nbsp;&nbsp;
	</p>
	<p>
	  <button type="button" onclick="validator()">Hírlevél elküldése</button>&nbsp;&nbsp;
	  <?php if(substr(JRequest::getVar('funkcio'),0,7) == 'kampany') : ?>
	  <button type="button" onclick="kampanyKapcsolas()">Hírlevél hozzá kapcsolása kampányhoz küldés nélkül</button>
	  <?php endif; ?>
	</p>		
	
	
	<div class="hirlevelek">
	<?php
	$i = 0;
	$j = 0;
	foreach ($this->Hirlevelek as $hirlevel) {
	  if ($hirlevel->utemezve > 0) 
			$utemezve = '<var style="color:red">(ütemezve:'.$hirlevel->utemezve.')</var>';
	  else
			$utemezve = '';
	  if ($i==0) $s = ' checked="checked"'; else $s='';	
	  echo '<p><input type="radio" name="mailid" value="'.$hirlevel->mailid.'"'.$s.' />&nbsp;'.
	        $hirlevel->subject.' '.$utemezve.'
			&nbsp;
			<a target="hirlevelinfo"
			   href="'.JURI::base().'index.php?option=com_tagnyilvantartas&view=doszures&task=doszures.hirlevelinfo&mailid='.$hirlevel->mailid.'">
			   (i) infó
			</a>
			</p>
			';			
	  $i++;
	  $j++;
	  if ($j == 20) {
		  echo '<div id="regebbiekLink" style="text-decoration:underline; cursor:pointer"  onclick="regiekClick()">[+] Régebbiek mutatása</div>
		  <div id="regihirlevelek" style="display:none">
		  ';
	  }
	}
	if ($j >= 20) 
		echo '</div>
	    ';
		?>
	</div>
	<p>
	  <button type="submit">Hírlevél elküldése</button>
	  <?php if(substr(JRequest::getVar('funkcio'),0,7) == 'kampany') : ?>
	  <button type="button" onclick="kampanyKapcsolas()">Hírlevél hozzá kapcsolása kampányhoz küldés nélkül</button>
	  <?php endif; ?>
	</p>		
		
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="task" value="doszures.hirlevelsend" />
<input type="hidden" name="view" value="doszures" />
<input type="hidden" name="backtask" value="kapcsolatoks.megsem" />
<input type="hidden" name="onlychecked" value="<?php echo JRequest::getVar('onlychecked') ?>" />
<input type="hidden" name="funkcio" value="<?php echo JRequest::getVar('funkcio') ?>" />
<?php
for ($i=1; $i<20; $i++) {
    if (JRequest::getVar('mezo'.$i) != '') {
      echo '<input type="hidden" name="mezo'.$i.'" value="'.JRequest::getVar('mezo'.$i).'" />'."\n";
      echo '<input type="hidden" name="relacio'.$i.'" value="'.JRequest::getVar('relacio'.$i).'" />'."\n";
      echo '<input type="hidden" name="ertek'.$i.'" value="'.JRequest::getVar('ertek'.$i).'" />'."\n";
    }
}    
?>
<?php echo JHTML::_( 'form.token' ); ?>
</form>  	
</div>
