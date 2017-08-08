<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
* this->Items alapján lista megjelenités
* akciok: elfogad, elvet
*
* 2016.03.07 jogosutság kezelés
* * "A"  usercsoport mindent kezelhet, "SM" usercsoport párttagokat és segitőket nem kezelheti
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');

$db = JFactory::getDBO();
$mezoNevek = array();

$mezoNevek["delete"] = "KAPCSOLAT TÖRLÉS";
$mezoNevek["nev1"] = "Vezetéknév";
$mezoNevek["nev2"] = "Középső név";
$mezoNevek["nev3"] = "Utónév";
$mezoNevek["titulus"] = "Titulus";
$mezoNevek["nem"] = "Nem";
$mezoNevek["szev"] = "Születési év";
$mezoNevek["oevk"] = "OEVK";
$mezoNevek["irsz"] = "Áll. lc. Irányítószám";
$mezoNevek["telepules"] = "Áll. lc. Település";
$mezoNevek["kerulet"] = "Áll. lc. Kerület";
$mezoNevek["utca"] = "Áll. lc. Közterület neve";
$mezoNevek["hazszam"] = "Áll. lc. Házszám";
$mezoNevek["tirsz"] = "Tart. h. Irányítószám";
$mezoNevek["ttelepules"] = "Tart. h. Település";
$mezoNevek["tkerulet"] = "Tart. h. Kerület";
$mezoNevek["tutca"] = "Tart. h. Közterület neve";
$mezoNevek["thazszam"] = "Tart. h. Házszám";
$mezoNevek["kategoria_id"] = "Kapcsolat kategória";
$mezoNevek["terszerv_id"] = "Területi szervezet";
$mezoNevek["email"] = "E-mail";
$mezoNevek["email2"] = "E-mail 2";
$mezoNevek["belsoemail"] = "Belső E-mail";
$mezoNevek["telefon"] = "Telefon";
$mezoNevek["telszammegj"] = "Telefon megjegyzés";
$mezoNevek["kapcsnev"] = "Kacsolatfelvevő";
$mezoNevek["kapcsdatum"] = "Kapcsolatfelvétel éve";
$mezoNevek["cimkek"] = "Cimkék";
$mezoNevek["megjegyzes"] = "Megjegyzés";
$db->setQuery('select * from #__tny_extrafields order by field_id');
			   $res = $db->loadObjectList();
foreach ($res as $res1) {
	   $mezoNevek[$res1->field_name] = $res1->field_label;
}
?>

<div class="javaslatoks">
<h2>Területi hatáskörbe tartozó, elbirálásra váró javaslatok</h2>
<table border="0" width="100%">
  <thead>
    <tr>
	  <th width="150">Javasló</th>
	  <th>Javaslat</th>
	  <th width="150">Müvelet</th>
	</tr>
  </thead>
  <tbody>
    <?php foreach ($this->Items as $item) : ?>
	   <?php $db->setQuery('select * from #__tny_kapcsolatok 
	                        where kapcs_id = '.$item->kapcs_id);
			$old = $db->loadObject();
			$fn = $item->mezo;
			if ($rowclass == 'row0') $rowclass='row1'; else $rowclass='row0';	
			if ($old->$fn == '') $old->$fn = 'üres';
			$link = JURI::base().'index.php?option=com_tagnyilvantartas&kapcs_id='.$item->kapcs_id.
			 '&javaslo_id='.$item->javaslo_id.
			 '&mezo='.$item->mezo.
			 '&time='.$item->idopont;
			$klink = JURI::base().'index.php?option=com_tagnyilvantartas&task=kapcsolatok.show&cid='.$item->kapcs_id; 
			
			// kodolt mezőknél az $item->ertek és $old->$fn modositása a megnevezésére
			if ($fn == 'kategoria_id') {
				$db->setQuery('select * from #__tny_kategoriak where kategoria_id ='.$db->quote($item->ertek));
				$res = $db->loadObject();
				if ($res) {
					$item->ertek = $res->szoveg;
				}
			}
			if ($fn == 'kategoria_id') {
				$db->setQuery('select * from #__tny_kategoriak where kategoria_id ='.$db->quote($old->$fn));
				$res = $db->loadObject();
				if ($res) {
					$old->$fn = $res->szoveg;
				}
			}
			if ($fn == 'terszerv_id') {
				$db->setQuery('select * from #__tny_teruletiszervezetek where terszerv_id ='.$db->quote($item->ertek));
				$res = $db->loadObject();
				if ($res) {
					$item->ertek = $res->nev;
				}
			}
			if ($fn == 'terszerv_id') {
				$db->setQuery('select * from #__tny_teruletiszervezetek where terszerv_id ='.$db->quote($old->$fn));
				$res = $db->loadObject();
				if ($res) {
					$old->$fn = $res->nev;
				}
			}
	   ?>
	   <tr class="<?php echo $rowclass; ?>">
	     <td>
		   <?php echo $item->name; ?><br />
		   <?php echo $item->idopont; ?>
		 </td>
		 <td>
		    <a href="<?php echo $klink; ?>" target="new">
		    <?php echo $item->knev; ?> (<?php echo $item->szoveg; ?>)</a><br />
			<?php echo $mezoNevek[$item->mezo]; ?><br />
			<span style="text-decoration: line-through;"><?php echo $old->$fn; ?></span>&nbsp;&gt;&gt;&gt;&nbsp;
			<?php echo $item->ertek; ?>
			<?php if ($item->megjegyzes != '') echo '<br />Üzenet:<span style="background-color:#F7D076">'.$item->megjegyzes.'</span>'; ?>
		 </td>
		 <td>
			<?php if (($userCsoport->kod == 'A') | 
			          (($item->kategoria_id == 3) | ($item->kategoria_id == 9))
					  ) : ?>
		     
			  <a href="<?php echo $link; ?>&task=kapcsolatok.javaslatElfogadva">Elfogad</a>&nbsp;
		      <a href="<?php echo $link; ?>&task=kapcsolatok.javaslatCancel">Elvet</a>&nbsp;
			  
		    <?php endif; ?>
		 </td>
	   </tr>
	<?php endforeach; ?>
  </tbody>
  <tfoot>
    <?php if (count($this->Items) == 0) : ?>
	  <tr>
	    <td cols="3">Nincs elbírálásra váró adat.</td>
	  </tr>
	<?php endif; ?>
  </tfoot>
</table>
</div>
