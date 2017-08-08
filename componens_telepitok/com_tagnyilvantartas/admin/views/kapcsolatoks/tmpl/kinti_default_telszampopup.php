<?php
// telefonszám caller popup ablak
?>
<script type="text/javascript">
	function telszamClick(kapcs_id) {
		h = document.getElementById("telszamPopup");	
		document.forms.telPopupForm.kapcs_id.value = kapcs_id;
		//document.getElementById('telPopupEditLink').href='index.php?option=com_tagnyilvantartas&task=kapcsolatok.javaslat&cid[]='+kapcs_id;
		if (h.style.display=="none") {
		  h.style.display="block";
		}   
	}
	function telszamPopupClose() {
		h = document.getElementById("telszamPopup");	
		h.style.display='none';	
	}
	
	//+ 2016.08.23 tlszám popup
</script>

<div id="telszamPopup" style="display:none; position:fixed; z-index:999; left:400px; top:200px; width:auto; height:auto; background-color:#E0E0E0; border-style:solid; border-width:1px; padding:10px;">
  <table width="100%" border="0">
    <tr><td><h3>Telefonálás</h3><td>
	    <td align="right"><button type="button" onclick="telszamPopupClose()" title="Bezárás">X</td>
	</tr>
  </table>
  <form method="post" name="telPopupForm" target="rejtettifrm"
        action="index.php?option=com_tagnyilvantartas&task=kapcsolatoks.telpopup">
    <input type="hidden" name="kapcs_id" value="" />
    <input type="radio" name="telstatus" value="felvette" checked="checked" />Felvette<br />
    <input type="radio" name="telstatus" value="hangposta" />Hangposta<br />
    <input type="radio" name="telstatus" value="nem kapcsolhato" />Előfizető nem kapcsolható<br />
    <input type="radio" name="telstatus" value="nem vettefel" />Nem vette fel<br />
    <input type="radio" name="telstatus" value="foglalt" />Foglalt<br />
    <input type="radio" name="telstatus" value="rossz szám" />Rossz telefonszám<br />
	-----------------------------------------<br />
    <input type="checkbox" name="nemszimpatizans" value="1" />Továbbiakban nem szimpatizáns<br />
    <input type="checkbox" name="nemkerhirlevelet" value="1" />Továbbiakban nem kér hírlevelet<br />
    <input type="checkbox" name="nemkerhivast" value="1" />Továbbiakban ne hívjuk telefonon<br />
	<br />
    <center>
	  <button type="submit" onclick="telszamPopupClose()">Rendben</button>&nbsp;
	</center>
  </form>
</div> 
<div style="display:none">
  <iframe name="rejtettifrm" width="400" height="300"></iframe>
</div>
