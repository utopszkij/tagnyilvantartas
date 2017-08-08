<?php
   
   // itt most rejtett divisionokba létre kell hozni a lehetséges popup listboxokat
   // div.id="helpMezonev", select.id=""
   //  - területi szervezetek
   //  - kategoriák
   //  - cimkék
   //  - nem

   //2017.04.01 kampany kezelésnél a filerKampany szürő feltétel beállítása, Call centeres nem modosithat
   
   // főmenüből is van inditva  JSession::checkToken() or die( 'Invalid Token' );
   $db = Jfactory::getDBO();
   echo '<br />';	
   
   if (JRequest::getVar('task') == 'szures') {
       echo '<h2>Kapcsolatok szűrés</h2>';
   }
   if (JRequest::getVar('task') == 'groupedit') {
       echo '<h2>Csoportos módosítás - szűrés</h2>
   	   <p>Alakitsa ki azt a listát amit modósítani akar!</p>';
   }
   if (JRequest::getVar('task') == 'szurtexport') {
       echo '<h2>Exportálás CSV fájlba - szűrés</h2>
	   <p>Alakitsa ki azt a listát amit exportálni akar!</p>';
   }
   if (JRequest::getVar('task') == 'hirlevel') {
       echo '<h2>Hírlevél küldés - szűrés</h2>
	   <p>Alakitsa ki azt a listát akinek hírlevelet akar küldeni!</p>';
   }
   if (JRequest::getVar('task') == 'kampany') {
	   $funkcio = $this->funkcio;
	   $kampany_id = substr($funkcio,8,10);
	   $db->setQuery('select * from #__tny_kampany where id='.$db->quote($kampany_id));
	   $res = $db->loadObject();
       echo '<h2>Kampány szervezés</h2>
	   <h3>'.$res->megnev.' '.$res->idopont.'</h3>
	   <p>Alakítsa ki azt a listát ami a kampányban elérendő neveket tartalmazza!</p>
	   <p style="color:red">FIGYELEM! Ha ezen a képernyőn felülbírálja a kampány felvitelénél megadott területi szervezetek beállítást, 
	   akkor a továbbiakban - ennél a kampánynál - az itt megadottak lesznek használva (nem pedig a kampány képernyőn megadott).</p>
	   ';
   }
   //DBG echo  'task='-JRequest::getVar('task');
?>
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task) {
	document.getElementById("turelem").style.display="block";
	Joomla.submitform(task, document.getElementById('adminForm'));
}
</script>

<div id="helpNem" style="display:none">
      <select size="5" onchange="sHelpChange()">
         <option value=""></option>
         <option value="no">Nő</option>
         <option value="ffi">Férfi</option>
      </select>
</div>

<div id="helpKategoria" style="display:none">
      <select size="10" onchange="sHelpChange()">
	  <option value=""></option>
	  <?php
	  $db->setQuery('select * from #__tny_kategoriak order by szoveg');
	  $res = $db->loadObjectList();
	  foreach ($res as $res1) {
		  echo '<option value="'.$res1->szoveg.'">'.$res1->szoveg.'</option>';
	  }
	  ?>
      </select>
</div>

<div id="helpSzervezet" style="display:none">
      <select size="12" onchange="sHelpChange()">
	  <option value=""></option>
	  <?php
	  $db->setQuery('select * from #__tny_teruletiszervezetek order by nev');
	  $res = $db->loadObjectList();
	  foreach ($res as $res1) {
		  echo '<option value="'.$res1->nev.'">'.$res1->nev.'</option>';
	  }
	  ?>
      </select>
</div>

<div id="helpOEVK" style="display:none">
      <select size="12" onchange="sHelpChange()">
	  <option value=""></option>
	  <?php
	  $db->setQuery('select distinct oevk from #__tny_oevk_torzs order by oevk');
	  $res = $db->loadObjectList();
	  foreach ($res as $res1) {
		  echo '<option value="'.$res1->oevk.'">'.$res1->oevk.'</option>';
	  }
	  ?>
      </select>
</div>

<div id="helpOrszag" style="display:none">
      <select size="12" onchange="sHelpChange()">
	  <option value=""></option>
	  <?php
	  $db->setQuery('select distinct orszkod,megn from #__tny_orszkod order by 2');
	  $res = $db->loadObjectList();
	  foreach ($res as $res1) {
		  echo '<option value="'.$res1->orszkod.'">'.$res1->megn.'</option>';
	  }
	  ?>
      </select>
</div>


<div id="helpKerhirlevelet" style="display:none">
      <select size="12" onchange="sHelpChange()">
	  <option value="1">Igen</option>
	  <option value="0">Nem</option>
	  </select>
</div>

<div id="turelem" class="turelem" style="display:none;">
  <div class="turelemSzoveg">Türelmet kérek.....</div>
</div>

<form id="adminForm" class="lmpForm" method="post" action="index.php" enctype="multipart/form-data">
<div class="szures">
   <div>
      <br />
      Tárolt szűrési feltétel használata:
	  <input type="file" name="savedfilter" />
   </div>
   <div class="onlychecked" style="text-align:right">
      <input type="radio" name="onlychecked"  
	    value="1" <?php if (JRequest::getVar('onlychecked')==1) echo ' checked="checked"' ?> /> 
	  Csak ellenőrzött 
	  &nbsp;
      <input type="radio" name="onlychecked"  
	    value="0" <?php if (JRequest::getVar('onlychecked')!=1) echo ' checked="checked"' ?> /> 
	  Minden adat 
   </div>
   <div id="sorok">
    <div id="sor1" class="control-group">
       <select id="mezo1" name="mezo1">
         <option value="" selected="selected"></option>
         <option value="nev1">Vezetéknév</option>
         <option value="nev2">Középső név</option>
         <option value="nev3">Utónév</option>
         <option value="titulus">Titulus</option>
         <option value="nem">Nem</option>
         <option value="szev">Születési év</option>
         <option value="oevk">OEVK</option>
         <option value="orszag">Áll. lc. Ország</option>
         <option value="irsz">Áll. lc. Irányítószám</option>
         <option value="telepules">Áll. lc. Település</option>
         <option value="kerulet">Áll. lc. Kerület</option>
         <option value="utca">Áll. lc. Közterület neve</option>
         <option value="hazszam">Áll. lc. Házszám</option>
         <option value="tirsz">Tart. h. Irányítószám</option>
         <option value="torszag">Tart. h. Ország</option>
         <option value="ttelepules">Tart. h. Település</option>
         <option value="tkerulet">Tart. h. Kerület</option>
         <option value="tutca">Tart. h. Közterület neve</option>
         <option value="thazszam">Tart. h. Házszám</option>
         <option value="kategoria">Kapcsolat kategória</option>
         <option value="terszerv">Területi szervezet</option>
         <option value="email">E-mail</option>
         <option value="email2">E-mail 2</option>
         <option value="hirlevel">Kér hírlevelet</option>
		 <option value="hirlevel_csatlakozas">Hírlevél csatlakozás klik (éééé-hh-nn)</option>
         <option value="belsoemail">Belső E-mail</option>
         <option value="telefon">Telefon</option>
         <option value="telszammegj">Telefon megjegyzés</option>
         <option value="kapcsnev">Kacsolatfelvevő</option>
         <option value="kapcsdatum">Kapcsolatfelvétel éve</option>
         <option value="cimkek">Cimkék</option>
         <option value="megjegyzes">Megjegyzés</option>
		 <?php
		   $db->setQuery('select * from #__tny_extrafields order by field_id');
		   $res = $db->loadObjectList();
		   foreach ($res as $res1) {
			   echo '<option value="'.$res1->field_name.'">'.$res1->field_label.'</option>';
		   }
		 ?>
       </select>
       <select id="relacio1" name="relacio1" style="width:100px" onchange="relacioChange(event)">
         <option value="lt" title="kisebb mint">&lt;</option>
         <option value="lte" title="kisebb vagy egyenlő">&lt;=</option>
         <option value="=" title="egyenlő" selected="selected">=</option>
         <option value="like" title="a megadott karektersorozat megtalálható a mező értékében">tartalmazza</option>
         <option value="gt" title="nagyobb">&gt;</option>
         <option value="gte" title="nagyobb egyenlő">&gt;=</option>
         <option value="ne" title="nem egyenlő">&lt;&gt;</option>
         <option value="between" title="határértékek között (a két szélső értéket is beleértve)">tól-ig</option>
         <option value="in" title="a felsorolt értékek egyike">lista</option>
         <option value="add">páros</option>
         <option value="even">páratlan</option>
       </select>
       <input id="ertek1" name="ertek1" value="" class="ertek" />
       <button id="help1" type="button" onclick="helpClick(event)" title="segítség az érték megadásához">?</button>
       <button id="del1" type="button" onclick="torolClick(event)" title="sor törlése" disabled="disabled" style="display:none">Del</button>
	   <span style="margin-left:20px;"id="txt1"></span>
    </div><!-- sor1 -->
   </div>
  <button id="plus1" type="button" onclick="plusClick(event)" title="Új sor hozzáadása">+</button>
</div>

<div id="segitseg" style="float:left; width:auto; display:none;" class="popup">
      <select id="sHelp" size="5" onchange="sHelpChange()">
         <option value="a">Bp 1-2-12</option>
         <option value="b">Bp 3</option>
         <option value="c">Bp 4-15</option>
         <option value="d">Bp 5-13</option>
         <option value="e">Bp 6-7-8</option>
         <option value="f">Bp 6-7-8</option>
         <option value="g">Bp 6-7-8</option>
         <option value="h">Bp 6-7-8</option>
         <option value="i">Bp 6-7-8</option>
      </select>
</div>    
<div style="clear:both"></div>  
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="view" value="kapcsolatoks" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="funkcio" value="<?php echo  $this->funkcio; ?>" />
<?php if (substr($this->funkcio,0,7) == 'kampany') : ?>
<input type="hidden" name="filterKampany" value="1" />
<?php endif; ?>
<?php echo JHtml::_( 'form.token' ); ?>
</form>
<p>Bármelyik e-mail mezőre megadott feltétel esetén, a program mindhárom e-mail mezőben keres.</p>
<p>Bármelyik telefonszám mezőre megadott feltétel esetén, a program mindkét telefonszám mezőben keres.</p>
<script type="text/javascript">
  sorDb = 1;
  helpI = 0;

  // megjeleniti a help popup ot a mezox értékének megfelelően
  // a mezox tartalmának megfelelő tartalommal
  function helpClick(event) {
    var helpSelectId = '';
    var b = event.target;
    helpI = b.id.substr(4,2);
    var h = document.getElementById("segitseg");
    var mezo = document.getElementById("mezo"+helpI);
    mezo = mezo.options[mezo.selectedIndex].value;
    var relacio = document.getElementById("relacio"+helpI);
    relacio = relacio.options[relacio.selectedIndex].value;
    
    if (mezo == "nem") helpSelectId = "helpNem";
    if (mezo == "kategoria") helpSelectId = "helpKategoria";
    if (mezo == "terszerv") helpSelectId = "helpSzervezet";
    if (mezo == "oevk") helpSelectId = "helpOEVK";
    if (mezo == "orszag") helpSelectId = "helpOrszag";
    if (mezo == "torszag") helpSelectId = "helpOrszag";
    if (mezo == "hirlevel") helpSelectId = "helpKerhirlevelet";
    if (h.style.display=="block")
       h.style.display="none";
    else {
      // melyik tartalomra van szükség?
      if (helpSelectId != "") {
          h.innerHTML = document.getElementById(helpSelectId).innerHTML;
          c = h.firstChild;
		  if (c) {
            c = c.nextSibling;
            c.id = "sHelp";
            c.onchange = sHelpChange;
		  }	
      } else if (relacio == "betwen" ) {
         h.innerHTML = "Két értéket \" - \" -el elválasztva kell beirni."; 
      } else if (relacio == "in" ) {
         h.innerHTML = "Értékek listáját, vesszővel elválasztva kell beirni."; 
      } else if (relacio == "add" ) {
         h.innerHTML = "Nem kell értéket megadni"; 
      } else if (relacio == "even" ) {
         h.innerHTML = "Nem kell értéket megadni"; 
      } else {    
         h.innerHTML = "Egy adott értéket kell beirni."; 
      }
      h.style.marginTop = (35 + (helpI*35))+"px";      
      h.style.display="block";
    }   
  }

  // új feltétel sort hoz létre
  function plusClick(event) {
    document.getElementById("segitseg").style.display="none";  
    var ujDb = sorDb + 1;
    var ujSor = document.getElementById("sor1").clone();
    ujSor.id = "sor"+ujDb;
    var c = ujSor.firstChild;
    c = c.nextSibling;
    c.id = "mezo"+ujDb;
    c.name = "mezo"+ujDb;
	c.selectedIndex = 0;
    c = c.nextSibling;   
    c = c.nextSibling;   
    c.id = "relacio"+ujDb;
    c.name = "relacio"+ujDb;
	c.selectedIndex = 2;
    c = c.nextSibling;
    c = c.nextSibling;
    c.id = "ertek"+ujDb;
    c.name = "ertek"+ujDb;
	c.value = "";
    c = c.nextSibling;
    c = c.nextSibling;
    c.id = "help"+ujDb;
    c = c.nextSibling;
    c = c.nextSibling;
    c.id = "del"+ujDb;
    c.disabled = false;
    c.style.display="inline-block";
    c = c.nextSibling;
    c = c.nextSibling;
    c.id = "txt"+ujDb;
    document.getElementById('sorok').appendChild(ujSor);
    sorDb = ujDb;
  }
  
  // feltétel sort töröl
  function torolClick(event) {
      document.getElementById("segitseg").style.display="none";  
      if (sorDb > 1) {
        var b = event.target;
        var sor = b.parentNode;
        if (sor.id == "sor1") {
           alert("Az első sor nem törölhető!");   
        } else {
           var sorok = sor.parentNode;
           sorok.removeChild(sor);
        }  
        sorDb = sorDb - 1;
      } else {
         alert("Az összes sor nem törölhető."); 
      }  
  }
  
  
  // reláció változott töröljük az értékx mezőt
  function relacioChange(event) {
     document.getElementById("segitseg").style.display="none";  
     var relacio = event.target;
     var i = relacio.id.substr(7,2);
     document.getElementById("ertek"+i).value = "";     
	 var txt = document.getElementById("txt"+i);     
	 // relációnak megfelelő segéd szöveg írása txt+i -be.
    relacio = relacio.options[relacio.selectedIndex].value;
    if (relacio == "betwen" ) 
         txt.innerHTML = "Két értéket \" - \" -el elválasztva kell beirni."; 
    else if (relacio == "in" ) 
         txt.innerHTML = "Értékek listáját, vesszővel elválasztva kell beirni."; 
    else if (relacio == "add" ) 
         txt.innerHTML = "Nem kell értéket megadni"; 
    else if (relacio == "even" ) 
         txt.innerHTML = "Nem kell értéket megadni"; 
    else     
         txt.innerHTML = "Egy adott értéket kell beirni."; 
     
  }
  
  // a helpI felhasználásával az ertekx tartalmába kell irni
  // figyelembe véve a reláciox -et is
  function sHelpChange() {
     ertek = document.getElementById("ertek"+helpI);
     sHelp = document.getElementById("sHelp");
     relacio = document.getElementById("relacio"+helpI);
     rel = relacio.options[relacio.selectedIndex].value;
     if (rel == '<')
        ertek.value = sHelp.options[sHelp.selectedIndex].value;     
     else if (rel == '>')
        ertek.value = sHelp.options[sHelp.selectedIndex].value;     
     else if (rel == '<=')
        ertek.value = sHelp.options[sHelp.selectedIndex].value;     
     else if (rel == '>=')
        ertek.value = sHelp.options[sHelp.selectedIndex].value;     
     else if (rel == '<>')
        ertek.value = sHelp.options[sHelp.selectedIndex].value;     
     else if (rel == 'betwen') {
         if (ertek.value == '')
           ertek.value = sHelp.options[sHelp.selectedIndex].value+" - ";
         else
           ertek.value += sHelp.options[sHelp.selectedIndex].value;
     } else  if (rel == 'in') {
         if (ertek.value == '')
           ertek.value = sHelp.options[sHelp.selectedIndex].value;
         else
           ertek.value += ", "+sHelp.options[sHelp.selectedIndex].value;
     } else {
        ertek.value = sHelp.options[sHelp.selectedIndex].value;     
     }
	 // popup closedir
     var h = document.getElementById("segitseg");
	 h.style.display="none";
  }

  function setSelect(nev,ertek) {
	  var s = document.forms.adminForm.elements[nev];
	  var i = 0;
	  for (i=0; i<s.options.length; i++) {
		  if (s.options[i].value == ertek) {
			  s.selectedIndex = i;
		  }
	  }
  }

  function tovabbKezelo() {
    <?php
	if(JRequest::getVar('tovabb')=='I') {	
	     // elöző szürés folytatása
		 $session = JFactory::getSession();
		 $elozoSzures = $session->get('elozoSzures'); 
		 foreach ($elozoSzures as $i => $sz) {
			 $i = $i+1;
			 echo 'plusClick();';
			 echo 'setSelect("mezo'.$i.'","'.$sz->mezoNev.'");'."\n";
			 echo 'setSelect("relacio'.$i.'","'.$sz->relacio.'");'."\n";
			 echo 'document.forms.adminForm.ertek'.$i.'.value = "'.$sz->ertek.'";'."\n";
		 }
	}
	// kampany kezelésnél callcenteres nem modosithat a feltételeken
    if (substr($this->funkcio,0,7) == 'kampany') {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod == 'CC') {
			echo '
			jQuery("form").hide();
			jQuery("#turelem").show();
			document.forms.adminForm.task.value="doszures.start";
			document.forms.adminForm.submit();
			';
		}
	}
	?>	
	
  }
  
  setTimeout("tovabbKezelo()",1000);
  
</script>

