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

//+ 2017.01.04 SM is használhatja a duplaszürést, de párttagokat nem modosithat
$vanParttagKoztuk = false;
foreach ($this->item as $i => $item1) {
	if ($item1->kategoria_id == 1) $vanParttagKoztuk = true;
	if ($item1->kategoria_id == 6) $vanParttagKoztuk = true;
	if ($item1->kategoria_id == 7) $vanParttagKoztuk = true;
}

// Set toolbar items for the page
JToolBarHelper::title(  'Dupla e-mail vagy Telefonszám');
if (($userCsoport->kod == 'A') | ($vanParttagKoztuk == false))
   JToolBarHelper::save('duplak.feldolgozas', 'Összefésül');
JToolbarHelper::custom('duplak.browser','','','Marad így (következő duplikáció)',false);
JToolbarHelper::custom('duplak.megsem','','','Mégsem (megszakítás)',false);

//-+ 2017.01.04 SM is használhatja a duplaszürést, de párttagokat nem modosithat


function echoMezo($name, $label, $item, $i) {
	if ($i == 0) 
		$ch = ' checked="checked" ';
	else
		$ch = '';
	$value = $item->$name;
	if ($name == 'terszerv_id') 
		$echoValue = $item->terszerNev;
	else if ($name == 'kategoria_id') 
		$echoValue = $item->kategoriaNev;
	else if (($name == 'hirlevel') and ($item->$name == 1))
        $echoValue = 'Igen';		
	else if (($name == 'hirlevel') and ($item->$name == 0))
        $echoValue = 'Nem';		
	else if (($name == 'ellenorzott') and ($item->$name == 1))
        $echoValue = 'Igen';		
	else if (($name == 'ellenorzott') and ($item->$name == 0))
        $echoValue = 'Nem';		
	else {
		$echoValue = $item->$name;
		//+ 2016.01.18 módosítási lehetőség
		if ($name == 'cimkek')
		  $echoValue = '<input type="text" name="inp_'.$name.'" value="'.$value.'" onchange="valueChange(this)" style="width:600px" />';
		else if ($name=='megjegyzes')
		  $echoValue = '<textarea name="inp_'.$name.'" cols="80" rows="3" style="width:600px" onchange="valueChange(this)">'.$value.'</textarea>';
		else 
		  $echoValue = '<input type="text" name="inp_'.$name.'" value="'.$value.'" onchange="valueChange(this)" style="width:100%" />';
		//- 2016.01.18 módosítási lehetőség
	}
	
	
	if ($label != '')
	  echo '<label class="'.$name.'">'.$label.':</label>';
	echo '&nbsp;<input type="radio" name="'.$name.'" value="'.base64_encode($value).'"'.$ch.' />
    &nbsp;<var class="'.$name.'">'.$echoValue.'</var>'; 
}

// $this->items kapcs_id -k listáját képezzük a késöbbi rekord feldolgozáshoz kell
$ids = '';
foreach ($this->item as $item1) {
	if ($ids != '') $ids .=',';
	$ids .= $item1->kapcs_id;
}

?>


<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task) {
	if (task == 'duplak.browser') {
		document.forms.adminForm.limitstart.value = 1 + Number(document.forms.adminForm.limitstart.value);
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else if (task == 'duplak.feldolgozas') {
		document.forms.adminForm.limitstart.value = 1 + Number(document.forms.adminForm.limitstart.value);
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

//+ 2016.01.18 módosítási lehetőség
function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}

function valueChange(c) {
	var v = c.parentNode; // var
	v = v.previousSibling;
    while (v.nodeName != 'INPUT') {
	  v = v.previousSibling;
	}	
	v.value = b64EncodeUnicode(c.value);
}
//+ 2016.01.18 módosítási lehetőség

</script>

<div id="duplak">
<br />
<h2><?php echo $this->title; ?></h2>
<p>Összesen <?php echo $this->total; ?> -ból ez a <?php echo (1 + JRequest::getVar('limitstart')); ?> . duplikáció.</p>
<div class="help">
Ha a kapcsolat rekordokat össze kívánja vonni, egyetlen adatba, akkor minden egyes mezőnél jelölje be a megtartandó adatot végül kattintson a "Összefésül" gombra. A megmaradó egyetlen kapcsolat adat a legkisebb azonosító számon lesz megtalálható, a többi azonosítójú adat sor törlődik.
Ha azt akarja, hogy úgy maradjanak az adatok a kapcsolat adatbázisban ahogy most vannak kattintson az "Így marad" gombra. Mindkét kattintás után, az újonnan megjelenő képernyőn a következő <br />
duplikáció fog megjelenni. Ha a duplikáció feldolgozást meg akarja szakítani, akkor a "Mégsem" gombra kell kattintania.
</div>
if (($userCsoport->kod != 'A') & ($vanParttagKoztuk == false))
   echo '<h2>A duplikáció párttag adatot érint ezért ÖN nem tudja ezzel a funkcióval javítani</h2>
        ';

<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas'); ?>" 
  name="adminForm" id="adminForm"> 
    <?php
		foreach ($this->item as $i => $item1) {
			echo '<div class="item'.($i % 2).'">';
			$j = 0;
			if ($i == 0) 
				$ch = ' checked="checked" ';
			else
				$ch = '';
			echo '<label>Azonosító:</label><var>'.$item1->kapcs_id.'</var><br />';
	        echoMezo('titulus','Titulus', $item1,$i);
	        echoMezo('nev1','Vezeték név', $item1,$i);
	        echoMezo('nev2','Középső név', $item1,$i);
	        echoMezo('nev3','Utónév', $item1,$i);
			echo '<br />';
            echoMezo('nem','Neme',  $item1, $i);
            echoMezo('szev','Szül.év',  $item1, $i); 
            echoMezo('telefon','Telefon', $item1, $i); 
            echoMezo('kategoria_id','Kategória', $item1, $i); 
			echo '<br />';
	        echoMezo('email','E-mail', $item1, $i); 
	        echoMezo('email2','E-mail2', $item1, $i); 
	        echoMezo('belsoemail','Belső E-mail', $item1, $i); 
			echo '<br />';
			echoMezo('oevk','OEVK', $item1, $i);
			echoMezo('hirlevel','Hírlevél', $item1, $i);
			echoMezo('ellenorzott','Ellenőrzött', $item1, $i);
			echoMezo('terszerv_id','Ter.szerv.', $item1, $i);
			echo '<table border="1" cellspacing="0" cellpadding="0" width="100%">
			  <tr>
			    <th width="60"></th>
			    <th><label>irsz / település</label></th>
			    <th><label>közterület neve / jellege</label></th>
			    <th><label>házszám / kieg.adatok</label></th>
			    <th width="100"><label>kerület</label></th>
			  </tr>
			  <tr>
				<td>Áll. lakcím</td>
				<td>'; 
				echoMezo('irsz','', $item1, $i); 
				echo '<br />';
				echoMezo('telepules','', $item1, $i);
				echo '</td><td>';
				echoMezo('utca','', $item1, $i);
				echo '<br />';
				echoMezo('kjelleg','', $item1, $i);
				echo '</td><td>';
				echoMezo('hazszam','', $item1, $i);
				echo '<br />';
				echoMezo('cimkieg','', $item1, $i);
				echo '</td><td>';
				echoMezo('kerulet','', $item1, $i);
				echo '</td>
			  </tr>
			  <tr>
				<td>Tart. hely</td>
				<td>'; 
				echoMezo('tirsz','', $item1, $i); 
				echo '<br />';
				echoMezo('ttelepules','', $item1, $i);
				echo '</td><td>';
				echoMezo('tutca','', $item1, $i);
				echo '<br />';
				echoMezo('tkjelleg','', $item1, $i);
				echo '</td><td>';
				echoMezo('thazszam','', $item1, $i);
				echo '<br />';
				echoMezo('tcimkieg','', $item1, $i);
				echo '</td><td>';
				echoMezo('tkerulet','', $item1, $i);
				echo '</td>
			  </tr>
			  
			</table>
			';
			echoMezo('kapcsnev','kapcs.felvevő', $item1, $i);
			echoMezo('kapcsdatum','kapcs.felvétel', $item1, $i);
			echo '<br />';
			echoMezo('cimkek','Cimkék', $item1, $i);
			echo '<br />';
			echoMezo('megjegyzes','Megjegyzés', $item1, $i);
			echo '</div>';
		}
	?>
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="duplak" />
		<input type="hidden" name="limitstart" value="<?php echo JRequest::getVar('limitstart',0); ?>" />
		<input type="hidden" name="ids" value="<?php echo $ids; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>