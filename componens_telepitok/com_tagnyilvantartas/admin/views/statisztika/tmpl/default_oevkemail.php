<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*
* változásnapló böngészése
* szürési lehetőségek: filter_date - filter_date2,  (filter_kapcs_id, ) filter_user_id
* id = kapcsolat_id,lastact_time,lastact_user_id
* extra funkció : purge
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$db = JFactory::getDBO();
$rowClass = 'row0';

function cmpFun($a,$b) {
	if ($a == 'LMP PÁRT')
		$result = -1;
	else if ($b == 'LMP PÁRT')
		$result = 1;
	else if ($a == $b)
		$result = 0;
	else if ($a > $b)
		$result = 1;
	else
		$result = -1;
	return $result;
}

if (JRequest::getVar("tipus")=="oevk") $s2= ' checked="checked"';
if (JRequest::getVar("tipus")=="terszer") $s1= ' checked="checked"';
if (JRequest::getVar("tipus")=="") $s1= ' checked="checked"';
echo '<div id="articleList">
<p style="text-align:right"><a href="lmpsugo.php?alias=statisztika">Súgó</a></p>
';
echo '<h2>Email darabszám változási statisztika OEVK bontásban</h2>';
echo '<h3>Zárójelben az időszak végén meglévő darabszám</h3>';
echo '<form action="'.JURI::base().'index.php">
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="view" value="statisztika" />
<input type="hidden" name="task" value="statisztika.emailshow" />
<p><input type="text" name="datumtol" value="'.JRequest::getVar('datumtol').'" size="10" style="width:140px;" />
&nbsp;-&nbsp;
<input type="text" name="datumig" value="'.JRequest::getVar('datumig').'" size="10" style="width:140px;" />
&nbsp;<button type="submit">start</button></p>
</form>
';



$db->setQuery('select * from #__tny_kategoriak order by szoveg');
$kategoriak = $db->loadObjectList();
$terstats = array();
$terstatsVeg = array();
$nevStilus = array();
foreach ($kategoriak as $fn => $fv) $nevStilus[$fn] = '';

foreach ($this->Items as $item) {
	$terstats[$item->oevk][$item->szoveg] = $item->valtozas; 
	$terstatsVeg[$item->oevk][$item->szoveg] = $item->vegdarab; 

	/*
	// tulajdonoshoz is hozzáadni
	if (isset($terstats[$item->tulaj_nev][$item->szoveg]))
	  $terstats[$item->tulaj_nev][$item->szoveg] += $item->valtozas; 
    else
	  $terstats[$item->tulaj_nev][$item->szoveg] = $item->valtozas; 
	if (isset($terstatsVeg[$item->tulaj_nev][$item->szoveg]))
	  $terstatsVeg[$item->tulaj_nev][$item->szoveg] += $item->vegdarab; 
    else
	  $terstatsVeg[$item->tulaj_nev][$item->szoveg] = $item->vegdarab; 
    $nevStilus[$item->tulaj_nev] = ' style="font-weight:bold"';  
	
	// LMP összesenhez is
	if ($item->tulaj_nev != 'LMP PÁRT') {
	  if (isset($terstats['LMP PÁRT'][$item->szoveg]))
	    $terstats['LMP PÁRT'][$item->szoveg] += $item->valtozas; 
      else
	    $terstats['LMP PÁRT'][$item->szoveg] = $item->valtozas; 
	  if (isset($terstatsVeg['LMP PÁRT'][$item->szoveg]))
	    $terstatsVeg['LMP PÁRT'][$item->szoveg] += $item->vegdarab; 
      else
	    $terstatsVeg['LMP PÁRT'][$item->szoveg] = $item->vegdarab; 
	  $nevStilus['LMP PÁRT'] = ' style="font-weight:bold"';  
	}
	*/
	
}

uksort($terstats, "cmpFun");
uksort($terstatsVeg, "cmpFun");
$csvNev = '/tmp/statisztika_oevk_'.$user->id.'-'.JRequest::getVar('datumtol').'-'.JRequest::getVar('datumig').'.csv';
$fp = fopen(JPATH_ROOT.$csvNev,'w+');

echo '<center>
<table border="0" class="listatabla" style="width:850px">
<tr><th style="width:100px">OEVK</th>
';
$s = '"Területi szervezet";';
foreach($kategoriak as $kategoria) {
	echo '<th>'.$kategoria->szoveg.'</th>';
	$s .= '"'.str_replace('"','',$kategoria->szoveg).'";"";';
}
echo '</tr>
';
fwrite($fp,$s."\n");
foreach ($terstats as $oevk => $terstat){
	echo '<tr class="'.$rowClass.'">
	        <td>
			  <span'.$nevStilus[$oevk].'>'.$oevk.'</span>
			</td>
			';
			foreach ($kategoriak as $kategoria) {
				if (isset($terstat[$kategoria->szoveg])) {
					echo '<td align="right"><span'.$nevStilus[$oevk].'>'.$terstat[$kategoria->szoveg].'</span>';
				    echo '<span'.$nevStilus.'>&nbsp;('.$terstatsVeg[$oevk][$kategoria->szoveg].')</span>';
				    echo '</td>';
				} else {
					echo '<td>&nbsp;</td>';
				}	
			}
	echo '</tr>
		 ';
	$s = '"'.str_replace('"','',$oevk).'";';
	foreach ($kategoriak as $kategoria) {
	  $s .= $terstat[$kategoria->szoveg].';'.$terstatsVeg[$oevk][$kategoria->szoveg].';';
	}	
	fwrite($fp,$s."\n");	 
	if ($rowClass == 'row0') $rowClass = 'row1'; else $rowClass = 'row0';	 
}
fclose($fp);
echo '</table>
<p><a href="'.JURI::root().$csvNev.'" target="new">Letöltés CSV fálként</a></p>
</center>
<div class="magyarazat" style="text-align:left">
 <p>A statisztika a napló bejegyzések alapján készül. Tehát olyan időszakról aminek a napló bejegyzései törölve
 lettek nullás statisztikát kapunk.</p>
</div> 
</div>
';

?>
