<?php
/**
* telefonszám popup képernyő
* $this->input : kapcsolat rekord + ->subid, ->hirlevel ->kampany ->hirlevel_id ->hirlevelStatus
* requed fields: kapcs_id, kampany_id, subid, hirlevel_id, email, stb.
*/
$db1 = JFactory::getDBO();
$session = JFactory::getSession();
$token = $session->get('telpopup');
$session->set('telpopup',$token);
?>
<script type="text/javascript">
	function hirlevelSendClick() {
		document.forms.telPopupForm.task.value = 'telpopup.hirlevelsend';
		document.forms.telPopupForm.submit();
	}
</script>

<div style="padding:10px">
<h2><?php echo $this->item->nev1.' '.$this->item->nev2.' '.$this->item->nev3; ?></h2>
<form method="post" name="telPopupForm" target="rejtettifrm"
        action="index.php?option=com_tagnyilvantartas&tmpl=component">
  <?php
  if ($this->item->kampany) {
	// Kampány fej adatok kiirása
	echo '<div style="display:block; background-color: #E3E9D5;">
	<h3>'.$this->item->kampany->megnev.'</h3>
	<p>'.$this->item->kampany->leiras.'</p>
	<p>Időpont:'.$this->item->kampany->idopont.'</p>
	<p>Helyszin:'.$this->item->kampany->helyszin.'</p>
	<p>Előadó:'.$this->item->kampany->eloado.'</p>
	</div>
	<div class="kampanyHirlevel" style="border-style:solid; border-width:1px; padding:10px">
		 Hírlevél címe: '.$this->item->hirlevel->subject.'<br />
	     Hírlevél státusza: <var>'.$this->item->hirlevelStatus.'</var>&nbsp;&nbsp;&nbsp;&nbsp;Hírlevél újra küldés:
		 <input type="text" name="email" value="'.$this->item->email.'"  style="width:300px" />címre&nbsp;
		 <button name="hirlevelSend" type="button" onclick="hirlevelSendClick()" />Küldés</button>
	 </div>
	';
  }
  ?> 
    <br />  
    <input type="hidden" name="task" value="telpopup.save" />
    <input type="hidden" name="kapcs_id" value="<?php echo $this->item->kapcs_id; ?>" />
    <input type="hidden" name="subid" value="<?php echo $this->item->subid; ?>" />
    <input type="hidden" name="hirlevel_id" value="<?php echo $this->item->hirlevel->mailid; ?>" />
    <input type="hidden" name="<?php echo $token; ?>" value="1" />
	<?php if ($this->item->kampany) : ?>
    <input type="hidden" name="kampany_id" value="<?php echo $this->item->kampany->id; ?>" />
	<?php endif; ?>
	<div style="display:inline-block; width:420px; valign:top">
    <input type="radio" id="telstatusFelvette" name="telstatus"  value="felvette" />&nbsp;Felvette<br />
    <input type="radio" id="telstatusHangposta" name="telstatus"  value="hangposta" />&nbsp;Hangposta<br />
    <input type="radio" id="telstatusNemkapcsolhato" name="telstatus" value="nem kapcsolhato" />&nbsp;Előfizető nem kapcsolható<br />
    <input type="radio" id="telstatusNemvetteFel" name="telstatus"  value="nem vette fel" />&nbsp;Nem vette fel<br />
    <input type="radio" id="telstatusFoglalt" name="telstatus" value="foglalt" />&nbsp;Foglalt<br />
    <input type="radio" id="telstatusRosszSzam" name="telstatus" value="rossz szám" />&nbsp;Rossz telefonszám<br />
    <input type="radio" id="telstatusKesobb" name="telstatus" value="kesobb" />&nbsp;Később hívni, ekkor:
	   <input type="text" name="kesobb" style="width:200px"/><br />
	</div>
	<div id="telszamPopup2" style="display:inline-block; width:300px">
	<table border="0">
		<tr><td>Továbbiakban szimpatizáns </td>
		    <td><input type="radio" name="telSzimp" id="telSzimp1" value="Igen" />&nbsp;Igen&nbsp;
		        <input type="radio" name="telSzimp" id="telSzimp0" value="Nem"  />&nbsp;Nem</td></tr>
		<tr><td>Továbbiakban kér hírlevelet </td>
		    <td><input type="radio" name="telHirlevel" id="telHirlevel1" value="Igen" />&nbsp;Igen&nbsp;
		        <input type="radio" name="telHirlevel" id="telHirlevel0" value="Nem" />&nbsp;Nem</td></tr>
		<tr><td>Továbbiakban is hivhatjuk </td>
		    <td><input type="radio" name="telHivhato" id="telHivhato1" value="Igen"  />&nbsp;Igen&nbsp;
		        <input type="radio" name="telHivhato" id="telHivhato0" value="Nem" />&nbsp;Nem</td></tr>
	</table>
	</div>
	<br />
	<br />
	<table id="kerdesek" border="0" width="100%">
	<tbody>
	<tr style="height:180px;">
	<?php 
	if ($this->item->kampany) {
	    // kapmpány kérdés megjelenitése
			$w = explode("\n",$this->item->kampany->valaszok);
			if ($this->item->kampany->kerdestipus == 1) {
				$opciok = '';
				for ($i=0; $i<10; $i++) {
				  $w1 = str_replace("\n",'',$w[$i]);	
				  $w1 = str_replace("\r",'',$w1);	
				  $w1 = str_replace('"','',$w1);	
				  if (trim($w1) != '') $opciok .= '<input type="checkbox" name="kampanyValasz_'.$i.'" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			} else {
				$opciok = '';
				foreach ($w as $i => $w1) {
				  if (trim($w1) != '') $opciok .= '<input type="radio" name="kampanyValasz" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			}
			if ($this->item->kampany->kerdes != '')
			echo '<td style="border-style:solid; border-width:1px; padding:3px; min-width:225px; vertical-align:top">
			<p>'.$this->item->kampany->kerdes.'</p>
			'.$opciok.'
			</td>
			';
			
			$w = explode("\n",$this->item->kampany->valaszok1);
			if ($this->item->kampany->kerdestipus1 == 1) {
				$opciok = '';
				for ($i=0; $i<10; $i++) {
				  $w1 = str_replace("\n",'',$w[$i]);	
				  $w1 = str_replace("\r",'',$w1);	
				  $w1 = str_replace('"','',$w1);	
				  if (trim($w1) != '') $opciok .= '<input type="checkbox" name="kampanyValasz1_'.$i.'" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			} else {
				$opciok = '';
				foreach ($w as $i => $w1) {
				  if (trim($w1) != '') $opciok .= '<input type="radio" name="kampanyValasz1" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			}
			if ($this->item->kampany->kerdes1 != '')
			echo '<td style="border-style:solid; border-width:1px; padding:3px; min-width:225px; vertical-align:top">
			<p>'.$this->item->kampany->kerdes1.'</p>
			'.$opciok.'
			</td>
			';
			
			$w = explode("\n",$this->item->kampany->valaszok2);
			if ($this->item->ampany->kerdestipus2 == 1) {
				$opciok = '';
				for ($i=0; $i<10; $i++) {
				  $w1 = str_replace("\n",'',$w[$i]);	
				  $w1 = str_replace("\r",'',$w1);	
				  $w1 = str_replace('"','',$w1);	
				  if (trim($w1) != '') $opciok .= '<input type="checkbox" name="kampanyValasz2_'.$i.'" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			} else {
				$opciok = '';
				foreach ($w as $i => $w1) {
				  if (trim($w1) != '') $opciok .= '<input type="radio" name="kampanyValasz2" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			}
			if ($this->item->kampany->kerdes2 != '')
			echo '<td style="border-style:solid; border-width:1px; padding:3px; min-width:225px; vertical-align:top">
			<p>'.$this->item->kampany->kerdes2.'</p>
			'.$opciok.'
			</td>
			';
			
			$w = explode("\n",$this->item->kampany->valaszok3);
			if ($this->item->kampany->kerdestipus3 == 1) {
				$opciok = '';
				for ($i=0; $i<10; $i++) {
				  $w1 = str_replace("\n",'',$w[$i]);	
				  $w1 = str_replace("\r",'',$w1);	
				  $w1 = str_replace('"','',$w1);	
				  if (trim($w1) != '') $opciok .= '<input type="checkbox" name="kampanyValasz3_'.$i.'" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			} else {
				$opciok = '';
				foreach ($w as $i => $w1) {
				  if (trim($w1) != '') $opciok .= '<input type="radio" name="kampanyValasz3" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			}
			if ($this->item->kampany->kerdes3 != '')
			echo '<td style="border-style:solid; border-width:1px; padding:3px; min-width:225px; vertical-align:top">
			<p>'.$this->item->kampany->kerdes3.'</p>
			'.$opciok.'
			</td>
			';
			
			$w = explode("\n",$this->item->kampany->valaszok4);
			if ($this->item->kampany->kerdestipus4 == 1) {
				$opciok = '';
				for ($i=0; $i<10; $i++) {
				  $w1 = str_replace("\n",'',$w[$i]);	
				  $w1 = str_replace("\r",'',$w1);	
				  $w1 = str_replace('"','',$w1);	
				  if (trim($w1) != '') $opciok .= '<input type="checkbox" name="kampanyValasz4_'.$i.'" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			} else {
				$opciok = '';
				foreach ($w as $i => $w1) {
				  if (trim($w1) != '') $opciok .= '<input type="radio" name="kampanyValasz4" value="'.$w1.'" />&nbsp;'.$w1.'<br />';
				}  
			}
			if ($this->item->kampany->kerdes4 != '')
			echo '<td style="border-style:solid; border-width:1px; padding:3px; min-width:225px; vertical-align:top">
			<p>'.$this->item->kampany->kerdes4.'</p>
			'.$opciok.'
			</td>
			';
	}
	?>
	</tr>
	</tbody>
	</table>
	<h2>A válaszok csak akkor kerülnek tárolásra ha a "Felvette" opció be van jelölve. </h2>
    <center>
	  <br />
	  <button type="submit">Rendben</button>&nbsp;
	</center>
  </form>
  <div style="display:none">
	  <iframe name="rejtettifrm" width="1000" height="800"></iframe>
  </div>
</div>