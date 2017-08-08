<?php
/**
  * Az LMP tagynilvántartás rendszer minden task elején futtatandó init része
  * user csoport választás, usercsoport, terhats elérés tárolás sessionba
  * rekord tipus szintű hozzáférés ellenörzés
  * oldal struktúra kirajzolás
  */
// no direct access
defined('_JEXEC') or die('Restricted access');

include_once 'components/com_tagnyilvantartas/models/felh_terhat.php';
include_once 'components/com_tagnyilvantartas/models/felhcsoportok.php';

   
class tagnyilvantartasControllerFejlec extends JControllerLegacy {
    /**
      * user terhats rekordok elérése, tárolása sessionba  és stringgé lakaitása
      */
    protected function getTerhats($userCsoport,$user) {
        $session = JFactory::getSession();
        if (is_object($userCsoport)) { 
            $felh_terhatModel = new tagnyilvantartasModelFelh_terhat();
            $userTerhats = $felh_terhatModel->getItems($user->id, $userCsoport->fcsop_id);
            $terhatStr = '';
            foreach ($userTerhats as $terhat) {
                // ha a tulajdonosa is checked akkor ez a rekord nem kell a stringbe
                $kell = true;
                foreach ($userTerhats as $w) {
                    if ($terhat->tulaj_id == $w->terszerv_id) $kell = false;
                }
                if ($kell) {
                  if ($terhatStr == '')
                    $terhatStr .= $terhat->nev;
                  else
                    $terhatStr .= ', '.$terhat->nev;
                }
            }
        }
        $session->set('userTerhats',$userTerhats);
		return $terhatStr;
    }
    
    public function start() {
        $session = JFactory::getSession();
        $user = JFactory::getUser();
		$config = JFactory::getConfig();
		$db = JFactory::getDBO();
        
        $felh_terhatModel = new tagnyilvantartasModelFelh_terhat();
        $felhCsoportokModel = new tagnyilvantartasModelFelhcsoportok();
        $userCsoport = '';
        $userTerhats = array();
        $csoportok = array();
        $terhatStr = '';
        JRequest::setVar('limit',100);
        
        // Normál esetben üzemszerüen a sessionban van az aktuális
        // userCsoport rekord és az aktuális userTerhats rekord tömb.
        $userCsoport = $session->get('userCsoport');
        if (!is_object($userCsoport)) {
           if (JRequest::getVar('view')!='fejlec') {
             JRequest::setVar('view','fejlec'); 
             JRequest::setVar('task','fejlec.login'); 
             $this->setRedirect('index.php?option=com_tagnyilvantartas&view=fejlec&task=fejlec.login');
             $this->redirect();
           }
        } else {
		  // 2016.02.03. a $userCsoport rekordban a "kod" adat idönként hibás, ezért biztos ami biztos ujra beolvasom.
		  $userCsoport = $felhCsoportokModel->getItem($userCsoport->fcsop_id);
          // echo 'DBG3 '.JSON_encode($userCsoport); 		  
          $terhatStr = $this->getTerhats($userCsoport,$user); // sessionba is tárol
          $session->set('userCsoport',$userCsoport);
	  
        }
        // adat átadás a template index.php számára
        $app = JFactory::getApplication();
        $app->fejlecView = $this->getView('fejlec','html');
        $app->fejlecView->set('userCsoport',$userCsoport);
        $app->fejlecView->set('terhatStr',$terhatStr);
		//2016.05.27. jog váltás comboboxban
        $csoportok = $felh_terhatModel->getCsoportok($user->id); 
        $app->fejlecView->set('csoportok',$csoportok);
        
         // rekord tipus szintű hozzáférés ellenörzés $userCsoport és
         // JRequest view alapján
         $jo = false;
         $view = JRequest::getVar('view');
		 $task = JRequest::getVar('task');

		 //+2016.09.14 hírlevél leiratkozás sbrowser nyilvános
		 if ($view=='leiratkozasok') $jo = true;
		 //+2016.09.14 hírlevél leiratkozás sbrowser nyilvános
		 
		//+ 2017.04.05 kampány kezelés
		if (($view=='kampanys') & ($userCsoport->kod == 'A')) $jo = true;
		if (($view=='kampanys') & ($userCsoport->kod == 'SM')) $jo = true;
		if (($view=='kampanys') & ($userCsoport->kod == 'MM')) $jo = true;
		if (($view=='kampanys') & ($userCsoport->kod == 'CC')) $jo = true;
		if (($view=='kampanys') & ($userCsoport->kod == 'CB')) $jo = true;
		if (($view=='kampany') & ($userCsoport->kod == 'A')) $jo = true;
		if (($view=='kampany') & ($userCsoport->kod == 'SM')) $jo = true;
		if (($view=='kampany') & ($userCsoport->kod == 'MM')) $jo = true;
		if (($view=='kampany') & ($userCsoport->kod == 'CC')) $jo = true;
		if (($view=='kampany') & ($userCsoport->kod == 'CB')) $jo = true;
		//+ 2017.04.05 kampány kezelés
		 
		 
		 //+ 2016.06.21.
         if (($view=='oevk') & ($userCsoport->kod == 'A')) $jo = true;
         if (($view=='oevk') & ($userCsoport->kod == 'SM')) $jo = true;
         if (($view=='oevks') & ($userCsoport->kod == 'A')) $jo = true;
         if (($view=='oevks') & ($userCsoport->kod == 'SM')) $jo = true;
		 //- 2016.06.21.


         if (($view=='cimkek') & ($userCsoport->jog_cimkek == 1)) $jo = true;
         if (($view=='cimkeks') & ($userCsoport->jog_cimkek == 1)) $jo = true;
         if (($view=='kategoriak') & ($userCsoport->jog_kategoriak == 1)) $jo = true;
         if (($view=='kategoriaks') & ($userCsoport->jog_kategoriak == 1)) $jo = true;
         if (($view=='felhcsoportok') & ($userCsoport->jog_felhasznalok == 1)) $jo = true;
         if (($view=='felhcsoportoks') & ($userCsoport->jog_felhasznalok == 1)) $jo = true;
         if (($view=='kapcsolatok') & ($userCsoport->jog_kapcsolat == 1)) $jo = true;
         if (($view=='kapcsolatoks') & ($userCsoport->jog_kapcsolat == 1)) $jo = true;
         if (($view=='doszures') & ($userCsoport->jog_kapcsolat == 1)) $jo = true;
         if (($view=='teruletiszervezetek') & ($userCsoport->jog_terszerv == 1)) $jo = true;
         if (($view=='teruletiszervezeteks') & ($userCsoport->jog_terszerv == 1)) $jo = true;
         if (($view=='felhasznalok') & ($userCsoport->jog_felhasznalok == 1)) $jo = true;
         if (($view=='felhasznaloks') & ($userCsoport->jog_felhasznalok == 1)) $jo = true;
         if (($view=='admin') & ($userCsoport->kod == 'A')) $jo = true;
         if (($view=='admin') & ($userCsoport->kod == 'SM')) $jo = true;
         
		 //+ FT 2016-08-24 naplót mindenki láthatja 
		 // if (($view=='naplos') & ($userCsoport->kod == 'A')) $jo = true;
		 if ($view=='naplos')  $jo = true;
		 //- FT 2016-08-24 naplót mindenki láthatja 
         
		 //+ FT 2017.03.30 csatlakozok listáját mindenki nézheti
		 if ($view=='csatlakozok')  $jo = true;
		 //- FT 2017.03.30 csatlakozok listáját mindenki nézheti
		 
		 
		 if (($view=='duplak') & ($userCsoport->kod == 'A')) $jo = true;
		 //+ FT 2017-01-04 duplaszürés "SM" -nek is megengedett
		 if (($view=='duplak') & ($userCsoport->kod == 'SM')) $jo = true;
		 //-FT 2017-01-04 duplaszürés "SM" -nek is megengedett


		//+ FT 2017.01.24 SM a felhasználók adatait nézegetheti
		if (($view=='felhasznaloks') & ($userCsoport->kod == 'SM')) $jo = true;
		if (($view=='felhasznalok') & ($userCsoport->kod == 'SM') & ($task != 'add')) $jo = true;
		//- FT 2017.01.24 SM a felhasználók adatait nézegetheti
		 
		 
         if (($view=='extrafields') & ($userCsoport->kod == 'A')) $jo = true;
         if ($view=='kommentek') $jo = true;
         if ($view=='fejlec') $jo = true;
         if ($view=='') $jo = true;
         if ($view=='statisztika') $jo = true;
		 if ($view=='kapcsolatoks' ) {
			if (($task == 'export') & ($userCsoport->jog_csv == 0)) $jo = false;
			if (($task == 'szurtexport') & ($userCsoport->jog_csv == 0)) $jo = false;
			if (($task == 'import') & ($userCsoport->jog_csv == 0)) $jo = false;
			if (($task == 'doimport') & ($userCsoport->jog_csv == 0)) $jo = false;
		 }
		 if ($view=='doszures' ) {
			if (($task == 'export') & ($userCsoport->jog_csv == 0)) $jo = false;
		 }
		 if (JRequest::getVar('cron','noCron')==$config->get('password')) $jo = true;
         if ($jo == false) {
            $this->setRedirect('index.php?option=com_tagnyilvantartas&view=fejlec&task=fejlec.accessdenied');
            $this->redirect();
         } 
		 if ($task != 'login') {
		    $this->hirlevelLeiratkozasJelzo();	
		 }	
		 
		 if ($view == 'telpopup') $jo=true;
    }

    /**
      * Látta már az utolsó program változás ismertetőt?
      * ha még nem akkor most nézze meg!
	  * @return true = már látta; false = most nézi meg
      */	  
	public function valtozasjelzo() {
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		// messages tábla létrehozása ha még nincs
		$db->setQuery('create table if not exists #__tny_messages(
		  `recipient` integer,
		  `tipus`  varchar(10),
		  `txt` text,
		  `created` datetime,
		  `creator` integer,
		  `read` datetime
		)');
		$db->query();
		
		// program változásait tartalmazó cikk beolvasása
		$db->setQuery('select * from #__content where alias="valtozasok"');
		$valtozasok = $db->loadObject();
		
		// mikor olvasta utoljára a változás jelentőt? (ha még nincs meg a message rekord akkor létrehozzuk)
		$db->setQuery('select * 
		from #__tny_messages 
		where tipus="prgvalt" and recipient='.$db->quote($user->id));
		$latta = $db->loadObject();
		if ($latta == false) {
			$latta = new stdClass();
			$latta->read = 0;
			$db->setQuery('insert into #__tny_messages values (
			'.$user->id.',"prgvalt","",0,0,0)');
			$db->query();
		}
		
		// ha az utolsó cikk modosítás előtt olvasta, akkor most megjelenitjük.
		if ($latta->read < $valtozasok->modified) {
		   echo '<div class="valtozasjelzo">
		   <h2>A program változásai</h2>
		   '.$valtozasok->introtext.$valtozasok->fulltext.'
		   <p> </p>
		   </div>
		   ';
		   // és beirjuk a messages táblába, hogy most olvasta
		   $db->setQuery('update #__tny_messages
		   set `read`='.$db->quote(date('Y-m-d H:i:s')).'
		   where tipus="prgvalt" and recipient='.$db->quote($user->id));
		   $db->query();
		   $result = false;
		} else {
		   $result = true;	
		}
		return $result;
	}
    
    /**
      * Látta már az utolsó program hírlevél leiratkozásokat?
      * ha még nem akkor most nézze meg!
	  * @return true = már látta; false = most nézi meg
      */	  
	public function hirlevelLeiratkozasJelzo() {
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		// csak hirlevél szerkesztőknek és super usernek
		//DBG echo 'hirlevelLeiratkozasJelzo <br />'.JSON_encode($user).'<br />';
		if (!(isset($user->groups[8]) | isset($user->groups[11]) | isset($user->groups[15]))) return;
		
		// mikor olvasta utoljára a változás jelentőt? (ha még nincs meg a message rekord akkor létrehozzuk)
		$db->setQuery('select * 
		from #__tny_messages 
		where tipus="unsub" and recipient='.$db->quote($user->id));
		$latta = $db->loadObject();
		if ($latta == false) {
			$latta = new stdClass();
			$latta->read = '2016-06-01';
			$db->setQuery('insert into #__tny_messages values (
			'.$user->id.',"unsub","",0,0,"2016-06-01")');
			$db->query();
		}

		//TEST	$latta->read = '2016-06-01';

		
		// liratkozásokat tartalmazó tábla beolvasása
		$db->setQuery('SELECT  s.name AS usernev, 
		                       max(ls.unsubdate) AS unsubdate, 
							   max(l.name) AS listanev,
							   max(m.subject) AS subject,
							   max(h.data) AS data,
							   max(k.kapcs_id) AS kapcs_id
		from #__acymailing_listsub AS ls
		LEFT OUTER JOIN #__acymailing_subscriber AS s ON s.subid = ls.subid
		LEFT OUTER JOIN #__acymailing_list AS l ON l.listid = ls.listid
		LEFT OUTER JOIN #__acymailing_history AS h ON h.subid = s.subid AND h.action="unsubscribed"
		LEFT OUTER JOIN #__acymailing_mail AS m ON m.mailid = h.mailid
		LEFT OUTER JOIN #__tny_kapcsolatok k on k.email = s.email or k.email2 = s.email
		WHERE FROM_UNIXTIME(ls.unsubdate) > '.$db->quote($latta->read).'
		GROUP BY s.subid,s.name
		ORDER BY ls.unsubdate');
		
		$leiratkozasok = $db->loadObjectList();
		// echo $db->getQuery();
		
		
		// ha még nem látta most megjelenitjük.
		if (count($leiratkozasok) > 0) {
		   echo '<div class="valtozasjelzo">
		   <h2>Új ('.$latta->read.' utáni) hírlevél leiratkozások</h2>
		   <p><b>A névre kattintva a kapcsolat szerkezstő ürlap jelenik meg új böngésző fülön</b><p>
		   ';
		   foreach ($leiratkozasok as $leiratkozas) {
			   if ($leiratkozas->kapcs_id > 0)
			      $editLink = JURI::root().'administrator/index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatok.edit&cid[]='.$leiratkozas->kapcs_id;
			   else
				  $editLink = '#'; 
			   $leiratkozas->data = str_replace('UNSUB_SURVEY_FREQUENT','Túl gyakran küldünk e-maileket',$leiratkozas->data);
			   $leiratkozas->data = str_replace('UNSUB_SURVEY_RELEVANT','Az e-mailek nem érdekesek számomra',$leiratkozas->data);
			   $leiratkozas->data = str_replace('REASON::',' ',$leiratkozas->data);
			   echo $leiratkozas->subject. ' --> '.date('Y-m-d H:i:s',$leiratkozas->unsubdate).' <a href="'.$editLink.'" target="edit">'.$leiratkozas->usernev.
			     '</a><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$leiratkozas->data.'<br />';
		   }
		   echo '
		   <p> </p>
		   </div>
		   ';
		   // és beirjuk a messages táblába, hogy most látta
		   $db->setQuery('update #__tny_messages
		   set `read`='.$db->quote(date('Y-m-d H:i:s')).'
		   where tipus="unsub" and recipient='.$db->quote($user->id));
		   $db->query();
		   $result = false;
		} else {
		   $result = true;	
		}
		return $result;
	}


    
    /**
      * ez az LMP tagnyilvántartás default taskja.
      * Ha a user már be van jelentkezve tovább ugrik a kapcsolatoks -ra
      * ellenkező esetben usercsoport választó login képernyő
      * ez oldja meg a login képernyő adatfogadását is
      * ha a user csak egy csoport tagja akkor auto login és tovább a kapcsolatoks -ra
      */
    public function login() {
       $session = JFactory::getSession();
       $user = JFactory::getUser();
       include_once 'components/com_tagnyilvantartas/models/felh_terhat.php';
       include_once 'components/com_tagnyilvantartas/models/felhcsoportok.php';
		
       $felh_terhatModel = new tagnyilvantartasModelFelh_terhat();
       $felhCsoportokModel = new tagnyilvantartasModelFelhcsoportok();
       
       // ha a user most választott csoportot akkor POST ban érkezik 'csoport' adat
	   // ha email megerősités is kell akkor a "kulcs" adat is érkezik a POST -ban, ennek egyeznie kell
	   //    a sessionban tárolt "kulcs" adattal.
       // ezt csak akkor fogadom el ha az adott user tagja ennek a csoportnak
       if (JRequest::getVar('csoport')!='') {
            $csoportok = $felh_terhatModel->getCsoportok($user->id); 
            $cs = JRequest::getVar('csoport');
            foreach ($csoportok as $csoport) {
               if ($cs == $csoport->fcsop_id) {
                   $userCsoport = $csoport; 
               }
            }  

			echo 'DBG1'.JSON_encode($userCsoport); 
			
			$session->set('userCsoport',$userCsoport);
            $terhatStr = $this->getTerhats($userCsoport,$user); // sessionba is tárol
			
        }

		
		
        $userCsoport = $session->get('userCsoport');
        if (is_object($userCsoport)) {
           $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks&redirect=1');
           $this->redirect();
        } else { 
            $csoportok = $felh_terhatModel->getCsoportok($user->id); 
			// ha emailes azonositás van akkor 1 usercsoportnál is a második login
			// képernyőre kell ugrani
            if ((count($csoportok) == 1) & ($csoportok[0]->kod != '')) {
               $userCsoport = $csoportok[0]; 
               $session->set('userCsoport',$userCsoport);
               $terhatStr = $this->getTerhats($userCsoport,$user); // sessionba is tárol
			   if ($this->valtozasjelzo() == false) {
				   return;
			   }
			   $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks&redirect=1');
               $this->redirect();
            } else if (count($csoportok) == 0) {
                echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_ACCESS_DENIED').'</div>';
                return;
            } else if ($csoportok[0]->kod == '') {
                echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_ACCESS_DENIED').'</div>';
                return;
            }  else {
			      // a usernek ki kell választania a felhasználói csoportját
				  // ha emailes azonositásvan akkor most kell képezni a "kulcs"-ot
				  // és azt tárolni sessionba, valamint elküldeni emailben.
			      $this->valtozasjelzo();
                  $viewer = $this->getView('felhasznalok','html');
                  $viewer->set('csoportok',$csoportok);
				  $viewer->setLayout('select');
                  $viewer->display();
                  return;
            }  
        }    
    }
    /**
      * rekordtipus szintű hozzáférés letiltás üzenet
      */ 
    public function accessdenied() {
       echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').'</div>'; 
    }
}
?>