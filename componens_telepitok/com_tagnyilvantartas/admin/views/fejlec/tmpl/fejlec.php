<?php
   JHTML::_('behavior.modal'); 
    $userToken = JSession::getFormToken();
    $logoutLink = 'index.php?option=com_login&task=logout&'.$userToken.'=1';
    
	//helpLink képzés
	$hview = JRequest::getVar('view');
	$hlayout = JRequest::getVar('layout');
	// $htask = JRequest::getVar('task');  itt már Joomla FOF néha bezavart a task névbe

	$htask = $_POST['task'];
  if ($htask == '') $htask = $_GET['task']; // Jó ez igy?
  
  
	$hfunkcio = JRequest::getVar('funkcio');
	//DBG foreach ($_POST as $fn => $fv) echo 'helpLink képzés '.$fn.'='.$fv.'<br />';
	//DBG exit();
	$hw = explode('.',$htask);
	if (count($hw)==2) {
		$hview = $hw[0];
		$hlayout = $hw[1];
		$htask = '';
	}
  
  //DBG1 echo 'helplink képzés 1 hview='.$hview.' hlayout='.$hlayout.' htask='.$htask.'<br>';
  
	//DBG echo 'helplink képzés hview='.$hview.' htask='.$htask.' count(hw)='.count($hw).'<br>';
	if ($hlayout=='') $hlayout = $htask;
    if (($hview == 'doszures') & ($hlayout=='edit')) $hview = 'kapcsolatok';	
    if (($hview == 'doszures') & ($hlayout=='')) $hview = 'kapcsolatoks';	
    if (($hview == 'doszures') & ($hlayout=='start')) {
	  if ($hfunkcio=='') $hfunkcio = 'szures';
 	  $hview = 'kapcsolatoks';	
	}
  
   
  //DBG2 echo 'helplink képzés 2 hview='.$hview.' hlayout='.$hlayout.' htask='.$htask.'<br>';
  
 	
	if (($hlayout == 'edit') & ($hfunkcio == 'szures')) $hfunkcio = '';
	if (($hlayout == 'show') & ($hfunkcio == 'szures')) $hfunkcio = '';
	if ($hview == 'kommentek') $hfunkcio = '';
    if (($hview == 'kapcsolatoks') & ($hlayout=='show')) $hview = 'kapcsolatok';	
	
	$helpLink = 'lmpsugo.php?alias='.$hview.$hlayout.$hfunkcio;
	
    $helpRel = "{handler: 'iframe', size: {x: 800, y: 600}}";
    $user = JFactory::getUser();
    if ($user == false) {
        $user = new stdclass();
        $user->name = 'Nincs bejelentkezve';
        $user->username = '';
    }
    $userCsoport = $this->userCsoport;
    if ($userCsoport == false) {
        $userCsoport = new stdclass();
        $userCsoport->kod = '';
    }
    $terhatStr = $this->terhatStr;


    /* ***********************************
	   rendszer üzemen kivül 
	   ***********************************
	*/
    //if ($user->id > 0 and $user->username <> 'utopszkij') {
	//	echo '<h2>Az LMP tagnyilvántartási rendszeren karbantartási munkát végzünk.</h2><h3>Átmenetileg nem használható.</h3></body></html>'; exit;
	//}	
    
    // LMP főmenü kiirása
   echo ' 
   <div class="fejlec">
     <div class="menutarto" id="menutarto">
        <nav class="navigation" role="navigation">
		<div id="menuIcon" onclick="menuClick()" style="cursor:pointer; display:inline-block; font-size:18px;" title="Menüt kibont">&nbsp;[+]&nbsp;</div>
          <div id="menu">
			<ul class="nav menu" id="ul-fomenu">
              <li class="item-1 deeper parent"><a>Kapcsolatok</a>
                 <ul class="nav-child unstyled small">
                   <li class="item-0101"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks" >Böngészés</a></li>
                   <li class="item-0101"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.szures" >
				   Szűrés</a></li>
                   <li class="item-0102"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatok&layout=edit" >Új felvitel</a></li>
                   <li class="item-0103"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.groupedit" >Csoportos műveletek</a></li>
                   <li class="item-0104"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.szurtexport" >Export CSV-be</a></li>
                   <li class="item-0105"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.import" >Import CSV-ből</a></li>
                   <li class="item-0105"><a href="index.php?option=com_tagnyilvantartas&view=duplak&task=duplak.browser" >Hibaszűrés</a></li>
                   <li class="item-0106"><a href="index.php?option=com_tagnyilvantartas&view=statisztika&task=statisztika.show">Létszám Statisztika</a></li>
                   <li class="item-0107"><a href="index.php?option=com_tagnyilvantartas&view=statisztika&task=statisztika.emailshow">Email Statisztika</a></li>
                   <li class="item-0108"><a href="index.php?option=com_tagnyilvantartas&view=statisztika&task=statisztika.telszamshow">Telefonszám Statisztika</a></li>
                   <li class="item-0109"><a href="index.php?option=com_tagnyilvantartas&view=csatlakozok" onclick="turelmetker()">Csatlakozók klick</a></li>
                 </ul>
              </li>
              <li class="item-kampany"><a href="index.php?option=com_tagnyilvantartas&view=kampanys">Kampányok</a></li>
              <li class="item-2 deeper parent"><a>Területi szervezetek</a>
                <ul class="nav-child unstyled small">
                  <li class="item-524"><a href="index.php?option=com_tagnyilvantartas&view=teruletiszervezeteks" >Böngészés</a></li>
                  <li class="item-525"><a href="index.php?option=com_tagnyilvantartas&view=teruletiszervezetek&layout=edit" >Új felvitel</a></li>
                </ul>
              </li>
              <li class="item-3 deeper parent"><a name="kezelok" >Kezelők</a>
                <ul class="nav-child unstyled small">
                  <li class="item-524"><a href="index.php?option=com_tagnyilvantartas&view=felhasznaloks">Böngészés</a></li>
                  <li class="item-525"><a href="index.php?option=com_users&task=user.add" target="new">Új felvitel</a></li>
                </ul>      
              </li>  
              <li class="item-4 deeper parent"><a name="hirlevel">Hírlevelek</a>
                <ul class="nav-child unstyled small">
                  <li class="item-524">
				    <a href="index.php?option=com_acymailing&ctrl=newsletter" target="new">
					   Hirlevél szerkesztése
					</a>
                  </li>				  
                  <li class="item-524">
				    <a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.hirlevel">
					Hírlevél küldése
					</a>
                  </li>				  
                  <li class="item-524">
				    <a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.hibasemail">
					Híbás e-mail címek
					</a>
                  </li>	
				  <li>
				    <a href="index.php?option=com_tagnyilvantartas&view=leiratkozasok&task=browser">Hírlevél leiratkozások</a>
				  </li>	
                </ul>			  
			  </li>	
              <li class="item-5 deeper parent"><a name="szerviz" >Szerviz funkciók</a>
                <ul class="nav-child unstyled small">
                  <li class="item-524"><a href="index.php?option=com_tagnyilvantartas&view=admin&task=admin.backup">
				  Mentés</a></li>
                  <li class="item-525"><a href="index.php?option=com_tagnyilvantartas&view=admin&task=admin.restore">
				  Visszaállítás</a></li>
                  <li class="item-525"><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.autobackup">Auto. mentés beállítás</a></li>
                  <li class="item-525">
				  <a href="index.php?option=com_tagnyilvantartas&view=naplos&filter_kapcs_id=0">Esemény napló</a></li>
                  <li class="item-524"><a href="index.php?option=com_tagnyilvantartas&view=cimkeks" target="_new" >Címkék</a></li>
                  <li class="item-525"><a href="index.php?option=com_tagnyilvantartas&view=kategoriaks" target="_new" >Kategóriák</a></li>
                  <li class="item-525"><a href="index.php?option=com_tagnyilvantartas&view=felhcsoportoks" >Felhasználó csoportok</a></li>
                  <li class="item-525"><a href="index.php?option=com_tagnyilvantartas&view=felhcsoportok&task=kapcsolatok.unlock" >
				  Minden zárolás feloldása</a></li>
                  <li class="item-526"><a href="index.php?zart&option=com_tagnyilvantartas&view=extrafields"> Extra mezők</a></li>
                  <li class="item-526"><a href="index.php?zart&option=com_tagnyilvantartas&view=oevks"> OEVK törzsadatok</a></li>
                  <li class="item-525"><a href="index.php?zart&option=com_admin&view=help" target="new" >Joomla admin</a></li>
                </ul>      
              </li>  
			  <!-- li>
                  <li class="item-pu"><a href="index.php?option=com_penzugy&view=pu_befizeteseks">Pénzügy</a></li>
			  </li -->
            </ul>
          <div> 
        </nav>

     </div><!-- menutarto -->
     <div class="logotarto">
     </div>
   </div> <!-- fejlec -->
   ';
   
    //+ 2016.05.27  LMP infóblokk jogkör váltási lehetőséggel
	$profilLink = 'index.php?option=com_admin&view=profile&layout=edit&id='.$user->id;
	$csoportValtas = '';
	if ((count($this->csoportok) > 1) & ($userCsoport->kod != '')) {
		$csoportValtas = ' Váltás:';
		foreach ($this->csoportok as $csoport) {
			if ($csoport->kod != $userCsoport->kod)
			    $csoportValtas .= '<a href="index.php?option=com_tagnyilvantartas&task=fejlec.login&csoport='.$csoport->fcsop_id.'">
				'.$csoport->kod.'&nbsp;'.$csoport->nev.'
			    </a>&nbsp;&nbsp;';
		}
		
	}
	
    echo '
    <div class="uzenettarto">
         <table border="0" width="100%">
           <tr>
             <td>
             Bejelentkezve mint: <span class="bejelentkezesi_nev">'.$user->username.'</span><var>('.$userCsoport->kod.')</var>
			 &nbsp;&nbsp;'.$csoportValtas.'
             <br>Hatáskör: '.$terhatStr.'</td>
             <td width="220" valign="top">
              <a href="'.$helpLink.'" class="modal" rel="'.$helpRel.'" id="ahelp">Súgó</a>
              &nbsp;|&nbsp;<a href="'.$profilLink.'" id="aprofil">Saját adataim</a>
              &nbsp;|&nbsp;<a href="'.$logoutLink.'">Kilépés</a>
             </td>
           </tr>
         </table>               
     </div>            
     ';
    //- 2016.05.27  LMP infóblokk
	 
	echo ' 
	<div id="turelem2" class="turelem" style="display:none;">
	  <div class="turelemSzoveg">Türelmet kérek.....</div>
	</div>
	';
	
	 echo '<script type="text/javascript">
	 
	   function turelmetker() {
		   document.getElementById("turelem2").style.display="block";
		   return true;
	   }
	   
	   // ez a dolog a fejlesztés végén kivehető
	   function helpAdjust() {
		    var i = 0;
		    var w = document.getElementsByTagName("H2");
			var title = w[0].innerHTML;
		    var adatok = "";
			w = document.getElementsByTagName("INPUT");	
			for (i=0; i<w.length; i++) {
				adatok += "input mező:"+w[i].name+"[BR]";
			}
			w = document.getElementsByTagName("SELECT");	
			for (i=0; i<w.length; i++) {
				adatok += "select:"+w[i].name+"[BR]";
			}
			w = document.getElementsByTagName("TEXTAREA");	
			for (i=0; i<w.length; i++) {
				adatok += "textarea:"+w[i].name+"[BR]";
			}
			w = document.getElementsByTagName("BUTTON");	
			for (i=0; i<w.length; i++) {
				adatok += "button:"+w[i].innerHTML+"[BR]";
			}
			w = document.getElementById("ahelp");
            if (adatok.length < 255)
			  w.href = w.href+"&title="+encodeURIComponent(title)+"&adatok="+encodeURIComponent(adatok);
	   }
	   function menuClick() {
		   // menü kibontása
		   var w1 = document.getElementById("menutarto");
		   var mod = "";
		   w1 = w1.parentNode;
		   // w1 = fejlectarto
		   if (w1.style.height == "300px") {
			   mod = "bezar";
		   } else {
			   mod = "kibont"
		   }		
		   if (mod == "kibont")
		     w1.style.height="300px";
		   else
			 w1.style.height = "100px";  
		   var w1 = document.getElementById("ul-fomenu");
		   var w2 = w1.firstChild;
		   while (w2) {
			   if (w2.nodeName == "LI") {
				   // w2 = felső szintű LI
				   if (mod == "kibont") {
				     w2.style.marginRight="30px";
				   } else {
				     w2.style.marginRight="5px";
				   }	 
				   var w3 = w2.firstChild;
				   while (w3) {
					   if (w3.nodeName == "UL") {
						   // w3 második szintű UL
						   if (mod == "kibont") {
						     w3.style.width = "auto";
						     w3.style.height = "auto";
						   } else {
						     w3.style.width = "0px";
						     w3.style.height = "0px";
						   }	 
					   }	   
					   w3 = w3.nextSibling;
				   }
			   }
			   w2 = w2.nextSibling;
		   }
		 	
	   }
	   setTimeout("helpAdjust()",500);
	 </script>
	 ';
?>