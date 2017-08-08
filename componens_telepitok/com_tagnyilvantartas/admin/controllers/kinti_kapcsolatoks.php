<?php
/**
* @version		$Id$ $Revision$ $Date$ $Author$ $
* @package		Tagnyilvantartas
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, .
* @license 		
*/

// 

defined('_JEXEC') or die;

// ====== tartalmát kikommentezni ha már jó a program ============
function dbg($info) {
		$fp = fopen(JPATH_ROOT.'/dbg.txt','a+');
		fwrite($fp,$info."\n");
		fclose($fp);
}


jimport('joomla.application.component.controlleradmin');
/**
 * Kapcsolatok list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Tagnyilvantartas
 */
class TagnyilvantartasControllerKapcsolatoks extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  TagnyilvantartasControllerkapcsolatoks
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		$this->view_list = 'kapcsolatoks';
		parent::__construct($config);
	}
	
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Kapcsolatok', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	  * Belső rutin a CSV importhoz ez a régi prg CSV -ét dolgozza fel
	  * egy csomó javitást is végez az adatokon
	  * csvRow -ból $item objektumot állít elő
	  * @return Item object ha formailag jó az adat, false ha formailag nem jó
	  */
	protected function getItemFromCSV_old(&$csvRow, &$kapcsolatModel, &$errorInfo) {
		$item = new stdClass();
		/* DBG
		foreach ($csvRow as $fn => $fv)
		  echo $fn.'='.$fv.'; ';
		echo '<br>';
		*/
		
		
	    $db = JFactory::getDBO();
		$jo = true;
		// üres rekord létrehozása
		// $item = $kapcsolatModel->getItem(0);
		$item->kjelleg = '';
		$item->hazszam = 0;
		$item->cimkieg = '';
		$item->terszerv_id = 0;
				
		// adat olvasás a CSV rekordból az $item -be
		$item->kapcs_id = (int)$csvRow[0];
		$item->nev1 =  trim($csvRow[1]);
		$item->nev3 = trim($csvRow[2]);
		$item->email = trim($csvRow[3]);
		$item->telefon = trim($csvRow[4]);
		$item->telepules = trim($csvRow[5]);
		$item->irsz = trim($csvRow[6]);
		$item->utca = trim($csvRow[7]);
		$item->megjegyzes = trim($csvRow[8]);
		$item->kategoria_id = trim($csvRow[9]);
		$item->terszerv_id = trim($csvRow[10]);
		$item->cimkek = trim($csvRow[11]);
				
		$item->kerulet = trim($csvRow[7]);
		$item->nem = trim($csvRow[8]);
		   if ($item->nem == 'Férfi') $item->nem = 'ffi';
		   if ($item->nem == 'Nő') $item->nem = 'no';
		$item->utca = trim($csvRow[9]);
		$item->megjegyzes = trim($csvRow[10]);
		$item->kategoria_id = trim($csvRow[11]);
		$item->terszerv_id = trim($csvRow[12]);
		$item->cimkek = trim($csvRow[11]);

		
		// adat standartizálás az $item -ben
		$item->telefon = stdTelefonszam($item->telefon); 
		//$item->nev1 = mb_ucfirst(mb_strtolower($item->nev1,'utf8'));
		//$item->nev2 = mb_ucfirst(mb_strtolower($item->nev2,'utf8'));
		//$item->nev3 = mb_ucfirst(mb_strtolower($item->nev3,'utf8'));
		$item->nev1 = $item->nev1;
		$item->nev2 = $item->nev2;
		$item->nev3 = $item->nev3;
		
		//$item->telepules = mb_ucfirst(mb_strtolower($item->telepules,'utf8'));
		//$item->kategoria_id = mb_ucfirst(mb_strtolower($item->kategoria_id,'utf8'));
		//$item->terszerv_id = mb_ucfirst(mb_strtolower($item->terszerv_id,'utf8'));
		$item->telepules = $item->telepules;
		$item->kategoria_id = $item->kategoria_id;
		$item->terszerv_id = $item->terszerv_id;
		
        if ($item->nev1 == $item->nev2) $item->nev2 = ''; 
        if ($item->nev1 == $item->nev3) $item->nev3 = ''; 
			
		// név rendbetétel
		$this->stdNev($item->nev1, $item->nev2, $item->nev3, $item->titulus);
			
		// utca értelmezése, szétszedése
		
		//DBG
		//$item->megjegyzes .= '('.$item->utca.')';
		
		$this->stdCim($item->utca, $item->kjelleg, $item->hazszam, $item->cimkieg);
		
		// kategoria kodolása
		$item->kategoria_id= mb_strtoupper($item->kategoria_id, 'utf8');
		if ($item->kategoria_id=='SEGÍTŐ TAG')
			$item->kategoria_id = 2;
		else if ($item->kategoria_id=='SEGÍTŐ')
			$item->kategoria_id = 2;
		else if ($item->kategoria_id=='PÁRTTAG') 
			$item->kategoria_id = 1;
		else if ($item->kategoria_id=='SZIMPATIZÁNS') 
			$item->kategoria_id = 3;
		else if ($item->kategoria_id=='PÁRTOLÓ TAG') 
			$item->kategoria_id = 4;
		else if ($item->kategoria_id=='SZÜNETELTETETT TAG') 
			$item->kategoria_id = 5;
		else if ($item->kategoria_id=='FELFÜGGESZTETT TAG') 
			$item->kategoria_id = 6;
		else { 
		    $jo = false;
			$errorInfo .= $item->nev1.' '.$item->nev2.' '.$item->nev3.'; hibás státusz adat:"'.$item->kategoria_id.'"<br />';
			return false;
        }
			
		// település irsz-ből
		if (($item->telepules == '') & (substr($item->irsz,0,1)=='1')) $item->telepules = 'Budapest';
			
		// település területi szervezetből
		if (($item->telepules == '') & (substr($item->terszerv_id,0,8)=='Budapest')) $item->telepuless = 'Budapest';

		if ($item->ellenorzott == '') $item->ellenorzott = 1; // ha nincs ilyen adat a csv -ben akkor ""ellenörzött" legyen
			
		// területi szervezet kodolása
		$db->setQuery('select * from #__tny_teruletiszervezetek where nev='.$db->quote($item->terszerv_id).' limit 1');
		$res = $db->loadObject();
		if ($res) {
			$item->terszerv_id = $res->terszerv_id;
		} else {
			$jo = false;  
			$errorInfo .= $item->nev1.' '.$item->nev2.' '.$item->nev3.'; hibás ter.szervezet adat:"'.$item->terszerv_id.'"<br />';
		}
		if ($jo == false) $item = false;	
		
	    //dbg('getItemFromCSV_old stop csvRow[0]='.$csvRow[0].' $item->nev1='.$item->nev1.' / '.$item->nev2.' / '.$item->nev3);

		return $item;
	} //getItemFromCSV_old
	
	/**
	  * Belső rutin a CSV importhoz  az új fajta (ezzel a programmal készült) CSV beolvasásához
	  * ilyenkor semmi javitás nincs a csv adatokon
	  * csvRow -ból $item objektumot állít elő
	  * @return Item object a formailag jó az adat, false ha formailag nem jó
	  */
	protected function getItemFromCSV_uj($csvRow, $kapcsolatModel, &$errorInfo, $fieldNames) {
	    $db = JFactory::getDBO();
		$jo = true;
		// üres rekord létrehozása
		$item = $kapcsolatModel->getItem(0);
		for ($i=0; $i < count($fieldNames); $i++) {
			$fieldName = $fieldNames[$i];
			if (($fieldName != 'tnev') & ($fieldName != 'szoveg') & ($fieldName != ''))
			  $item->$fieldName = $csvRow[$i];
		}
		return $item;
	}	
	
	/** 
	  * csv file teljes végig olvasása és feldolgozása
	  * @JRequest term, importType, charset
	  * @param filePointer OPENED csvFile megnyitva az első rekordon áll
	  * @param integer átlépendő sorok száma
	  * @param integer feldolgozandó sorok száma
	  * @param Juser object
	  * @param array of userTerhat records
	  * @param Jmodel kapcsolatokModel
	  * @param integer betöltött rekordok száma
	  * @param integer formailag hibás vagy terhat.on kivüli rekordok száma
	  * @param integer e-mail, telefon ütközés miatt nem betöltött rekordok száma
	  * @return true ha EOF, false ha nem
	  */
	protected function csvImport(&$fpCSV, $skip, $limit,
	  $user, $userTerhats, $kapcsolatModel,
	  &$joDarab, &$hibasDarab, &$utkozoDarab, &$errorInfo) {
	  $importType = JRequest::getVar('importType',0);
      $term = JRequest::getVar('fieldterminator','tab');
	  $karset = JRequest::getVar('charset','utf-8');
	  $db = JFactory::getDBO();
	  $user = JFactory::getUser();
	  $session = JFactory::getSession();
	  $kapcsolatModel = $this->getModel('kapcsolatok');	  
	  $szamlalo = 0;
	  if ($term=='tab') $term = "\t";
	  if ($term=='coma') $term = ",";
	  if ($term=='semicolon') $term = ";";
      $w1 = fgetcsv($fpCSV, 0, $term, '"');
	  $fieldNames = $w1;
      while (($w1 !== false) & ($szamlalo < ($skip + $limit))) {
		if ($szamlalo >= $skip) {
		  // karakter átkodolás
		  if ($karset != 'utf-8') {
			for ($i=0; $i<count($w1); $i++) {
				   $w1[$i] = iconv('ISO-8859-2', 'UTF-8', $w1[$i]);
			}	
		  }
	      // csvRow feldolgozása --> $item -be	
		$item = new stdClass();
		if ($fieldNames[0]=='kapcs_id') {
			// saját csv új rekordszerkezettel  
		    $item = $this->getItemFromCSV_uj($w1, $kapcsolatModel, $errorInfo, $fieldNames); 
		} else {
			// régi csv  
		    $item = $this->getItemFromCSV_old($w1, $kapcsolatModel, $errorInfo); 
		}
		  if ($item) {
			//DBG echo 'ciklusban 1  $item->ttelepules:'.$item->ttelepules.'<br>';
			// csv imput mod szerint id beállítás
			if ($importType == 0) {
			   $item->kapcs_id = 0;	
			}
			$jo = true;
			
			// területi hatáskör jó?
			$joTerszerv = false;
			foreach ($userTerhats as $userTerhat) {
				if ($userTerhat->terszerv_id == $item->terszerv_id) $joTerszerv = true;
			}
			if ($joTerszerv == false) {
				$jo = false;
				$hibasDarab++;
				$errorInfo .= $item->nev1.' '.$item->nev2.' '.$item->nev3.' Nem a hatáskörébe tartozó területi szervezet:"'.$item->terszerv_id.'"<br />';
				} else {
 			   // ütközés vizsgálat
				if ($importType == 0) {
					if (JRequest::getVar('utkozestis')!='1')
						$utkozes_info = $this->chkUtkozes($db, $item->email, $item->telefon);
					else 
						$utkozes_info = '';
					if ($utkozes_info != '') {
						$jo = false;
						$errorInfo .= $item->nev1.' '.$item->nev2.' '.$item->nev3.'; '.$utkozes_info.' ütközés '.$item->email.' '.$item->telefon.'<br />'; 
						$utkozoDarab++;
					} // ütközés van
				}		
		    }
			if ($jo) {
				// tárolás
				$itemArray = array();
				foreach ($item as $fn => $fv) {
					$itemArray[$fn] = $fv;
				}
				$itemArray['lastaction']='CSV IMPORT';
				$itemArray['lastact_info']= 'CSV IMPORT '.$session->get('csvfilename');
			    //DBG2 echo 'ciklusban 2  $itemArray->ttelepules:'.$itemArray->ttelepules.'<br><br>';
				if ($kapcsolatModel->save($itemArray)) {
				  $joDarab++;
				  // naplózás
				  if ($item->kapcs_id == 0) {
					$item->kapcs_id = $kapcsolatModel->getDBO()->insertid();
				  }
				  $db->setQuery('insert into #__tny_naplo
				  select * from #__tny_kapcsolatok
				  where kapcs_id='.$item->kapcs_id);
				  $db->query();
				} else {
			      // hiba az adat tárolás közben 
				  $hibasDarab++;
				  //$errorInfo .= $kapcsolatModel->getError().'<br>'.JSON_encode($item).'<br><br>';
				  $errorInfo .= $kapcsolatModel->getError();
				}  
			}
		  }	else {
			// hiba a csvRow --> $item feldolgozás közben  
			$hibasDarab++;  
		  }
		}
        $szamlalo++;
		$w1 = fgetcsv($fpCSV, 0, $term, '"');
	  }	// file olvasó ciklus
	  
	  // kerület javitás Budapestieknél az irsz -ből
	  $db->setQuery(' 
UPDATE #__tny_kapcsolatok 
SET tkerulet = substring(tirsz,2,2)
where tkerulet = "" and ttelepules = "Budapest" and
      SUBSTR(tirsz,1,1) = "1" AND SUBSTR(tirsz,2,2) BETWEEN "01" AND "23"');
	  $db->query();
	  
	  $db->setQuery(' 
UPDATE #__tny_kapcsolatok 
SET kerulet = substring(irsz,2,2)
where kerulet = "" and telepules = "Budapest" and
      SUBSTR(irsz,1,1) = "1" AND SUBSTR(irsz,2,2) BETWEEN "01" AND "23";
      ');
	  $db->query();
	  
	  return ($w1 == false);
	  exit();
	} //csvImport rutin
	
	
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}

    /**
      * szürés képernyõ
      * @return void
      */
    public function szures() {
          $view = $this->getView('kapcsolatoks','html');
          $view->set('funkcio','szures');
          $view->setLayout('szures');
          $view->display();         
    }
    /**
      * szürés képernyõ CSV exportjhoz
      * @return void
      */
    public function szurtexport() {
          $view = $this->getView('kapcsolatoks','html');
		  $view->set('funkcio','export');
          $view->setLayout('szures');
          $view->display();         
    }

    /**
      * szürés képernyõ Hírlevélhez exportjhoz
      * @return void
      */
    public function hirlevel() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->jog_hirlevel != '1') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').' '.$userCsoport->kod,'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
		}
        $view = $this->getView('kapcsolatoks','html');
	    $view->set('funkcio','hirlevel');
        $view->setLayout('szures');
        $view->display();         
    }

    /**
      * csoportos modositás képernyõ
      * @return void
      */
    public function groupedit() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').' '.$userCsoport->kod,'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
		}
		
        $view = $this->getView('kapcsolatoks','html');
	    $view->set('funkcio','groupedit');
        $view->setLayout('szures');
        $view->display();         
    }

	/**
	  * delete rekord
	*/  
	public function delete() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
	    parent::delete();	
	}
	
    /**
      * csoportos modositás képernyõ
      * @return void
      */
    public function comments() {
		$view = $this->getView('kapcsolatoks','html');
        $view->setLayout('comments');
        $view->display();         
    }

    /**
      * autó backup konfig képernyő
      * @return void
      */
    public function autobackup() {
		echo '<h3>A rendszerben lehetőség van ütemezett, automatikus adatbázis mentések készítésére.</h3>
<p>Az ütemezett mentés beállításához  LINUX ismeretekre van szükség és a web szerveren működnie kell az un. "cron" időzített futtató szolgáltatásnak.</p>
<p>Az ütemezett mentés beállítása LINUX rendszergazdai jogokkal, LINUX terminálon végezhető el.</p>
<p>A legtöbb rendszerben az "/etc/crontab" tartalmazza az ütemezett feladatokat ebben a "http://aktuális_domain/cronbackup.php" futtatását kell beütemezni. Példának a jelenlegi (teszt környezeti) beállítás:</p>
<code>10 20 * * * root /usr/bin/wget --speeder "http://lmp-tny.tk/cronbackup.php"</code>
<p>Ez a beállítás minden nap, 20:10 -kor inditja az ütemezett mentést.</p>
';
    }
	
    /**
      * hibaszűrés
      * @return void
      */
    public function hibaszures() {
		$view = $this->getView('kapcsolatoks','html');
        $view->setLayout('comments');
        $view->display();         
    }
	
    /**
      * csv export képernyõ
      * @return void
      */
    public function export() {
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',10000000);
		$model = $this->getModel('kapcsolatoks');
		$user = JFactory::getUser();
		
		$items = $model->getItems();
		$fp = fopen(JPATH_ROOT.'/tmp/export_'.$user->id.'.csv','w+');
		$darab = 0;
		$s = '';
		foreach ($items[0] as $fn => $fv) {
			$s .= $fn."\t";
		}
		fwrite($fp,$s."\n");
		for ($i=0; $i<count($items); $i++) {
			$s = '';
			foreach ($items[$i] as $fn => $fv) {
				$fv = str_replace("\t",' ',$fv);
				$fv = str_replace('"','\"',$fv);
				$fv = str_replace("\n",' ',$fv);
				if (is_numeric($fv))
					$s .= $fv."\t";
				else	
					$s .= '"'.$fv.'"'."\t";
			}
			fwrite($fp,$s."\n");
			$darab++;
		}
		fclose($fp);
		echo '
		<h2>Adat export CSV fájlba - eredmény</h2>
		<div class="lmpForm">
		<br /><br />
		<center>
		  <a href="'.JURI::root().'tmp/export_'.$user->id.'.csv">CSV file letöltése</a>
		  <br /><br />
		  <p>A CSV fájl '.$darab.' darab adatsort tartalmaz.</p>
		  <a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks">Vissza a kapcsolatok böngészéséhez</a>
		</center>
		<br /><br />
		</div>
		';
    }


    /**
      * csv import képernyõ
      * @return void
      */
    public function import() {
		$db = JFactory::getDBO();
    	$user = JFactory::getUser();
		$session = JFactory::getSession();
		$userTerhats = $session->get('userTerhats');
		
		// van idegen zárolás a területi hatáskörében?
		$zarolt = false;
		foreach ($userTerhats as $terhat) {
			$db->setQuery('select zarol_user_id
			from #__tny_kapcsolatok
			where terszerv_id = '.$db->quote($terhat->terszerv_id).' and zarol_time > 0 and zarol_user_id <> '.$user->id);
			$res = $db->loadObject();
			if ($res) $zarolt = true;
        }		  
		if ($zarolt) {
			$this->setMessage('Jelenleg nem inditható. Mások dolgoznak a területi hatáskörébe tartozó adatokkal.');
			$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
			$this->redirect();
			return;
		}
		  
		// területi hatáskörében minden rekord zárolása
        foreach ($userTerhats as $terhat) {
          $db->setQuery('update #__tny_kapcsolatok
		  set zarol_time='.time().',
		  zarol_user_id = '.$user->id.'
		  where terszerv_id='.$db->quote($terhat->terszerv_id));
		  $db->query();
		  //DBG echo $db->getQuery().'<br>';
		}		  
		  
        $view = $this->getView('kapcsolatoks','html');
        $view->set('funkcio','import');
        $view->setLayout('import');
        $view->display();         
    }

	/* Név adatok rendbetétele, standertizálása */
	protected function stdNev(&$nev1, &$nev2, &$nev3, &$titulus) {
		$w = explode(' ',str_replace('.','. ',$nev1.' '.$nev3));
		// szavak normalizálása és a titulus kiszedése
		for ($i=0; $i<count($w); $i++) {
		  $w[$i] = trim($w[$i]);
		  //$w[$i] = mb_ucfirst(mb_strtolower($w[$i],'utf8'));
		  if (($w[$i] == 'Dr') | ($w[$i] == 'Dr.') | ($w[$i] == 'Ifj.' )) {
			  $titulus = $w[$i];
			  array_splice ($w, $i, 1); // törli ezt az elemet
		  }	  
		}  
		// a szavak sorban nev1,nev2,nev3 -ba
		$nev1 = '';
		$nev2 = '';
		$nev3 = '';
		for ($i=0; ($i<count($w) & ($i < 10)); $i++) {
		  if ($w[$i] != '') {
			  if ($nev1 == '')
				  $nev1 = $w[$i];
			  else if (($nev2 == '') & ($w[$i] != $nev1))
				  $nev2 = $w[$i];
			  else if (($nev3 == '') & ($w[$i] != $nev1) & ($w[$i] != $nev2))
				  $nev3 = $w[$i];
			  else if (($w[$i] != $nev1) & ($w[$i] != $nev2) & ($w[$i] != $nev3))
				  $nev3 .= ' '.$w[$i];
		  } 
		}
		// ha csak két név vana kkor az nev1 nav3 és a nev2 legyen üres
		if ($nev3 == '') {
		   $nev3 = $nev2;
		   $nev2 = '';			   
		}			
	}
	
	/* cim rendbetétele, szétbontása, stabdartizálása */
	public function stdCim(&$utca, &$kjelleg, &$hazszam, &$cimkieg) {
		$origUtca = $utca;
		$utca = str_replace("\t",' ',$utca);
		$utca = str_replace('/',' / ',$utca);
		$utca = str_replace('.',' ',$utca);
		$utca = str_replace('-',' - ',$utca);
		$utca = preg_replace( '/([0-9])([a-z])/', "$1 $2", $utca);
		$utca = preg_replace( '/([0-9])([A-Z])/', "$1 $2", $utca);
		$w = explode(' ',$utca);
		for ($i=1; $i<count($w); $i++) {
			$w[$i] = trim($w[$i]);
			if (is_numeric($w[$i]) & ($hazszam == 0)) {
				$hazszam = $w[$i];
				$w[$i] = '';
				$cimkieg = '';
				$cimkieg .= $w[$i+1].' '; $w[$i+1]='';
				$cimkieg .= $w[$i+2].' '; $w[$i+2]='';
				$cimkieg .= $w[$i+3].' '; $w[$i+3]='';
				$cimkieg .= $w[$i+4].' '; $w[$i+4]='';
				$cimkieg .= $w[$i+5].' '; $w[$i+5]='';
				$cimkieg = trim($cimkieg);
			}
			$worig = $w[$i];
			$w[$i] = mb_strtolower($w[$i],'utf8');
			if (($w[$i]=='út') |
				($w[$i]=='út') |
				($w[$i]=='útja') |
				($w[$i]=='ut') |
				($w[$i]=='utca') |
				($w[$i]=='sor') |
				($w[$i]=='tér') |
				($w[$i]=='körtér') |
				($w[$i]=='kőrtér') |
				($w[$i]=='körönd') |
				($w[$i]=='kőrút') |
				($w[$i]=='krt') |
				($w[$i]=='köz') |
				($w[$i]=='dűlő') |
				($w[$i]=='tanya') |
				($w[$i]=='major') |
				($w[$i]=='telep') |
				($w[$i]=='sugárút') |
				($w[$i]=='park') |
				($w[$i]=='liget') |
				($w[$i]=='rakpart') |
				($w[$i]=='fasor') |
				($w[$i]=='u')) {
				$kjelleg = $w[$i];		
				$w[$i] = '';
			} else {
			  $w[$i] = $worig;
			}  
		}
		$utca = trim(implode(' ',$w));
		if ($utca == 'Pf') {
			$utca = $origUtca;
			$hazszam = 0;
			$cimkieg = '';
		}	
		
	}

	/* ütközés vizsgálat */
	protected function chkUtkozes($db, $email, $telefon) {
        $utkozes_info = '';
		if ($email != '') {
			$db->setQuery('select kapcs_id
			from #__tny_kapcsolatok
			where email='.$db->quote($email).' or
			email2='.$db->quote($email).' or
			belsoemail = '.$db->quote($email).' limit 1');
			$res = $db->loadObject();
			if ($res) $utkozes_info = 'email ';
		}
		if ($telefon != '') {
			$db->setQuery('select kapcs_id
			from #__tny_kapcsolatok
			where telefon='.$db->quote($telefon).' limit 1');
			$res = $db->loadObject();
			if ($res) $utkozes_info .= 'telefon ';
		}
		return $utkozes_info;
	}

	/**
	  * CSV import végrehajtása
	  * @JRequest csv import paraméter képernyő adatai + opcionálisan skip, jo, hibas, utkozes
	  */
	public function doimport() {
	  $user = JFactory::getUser();
	  $session = JFactory::getSession();	
	  $userTerhats = $session->get('userTerhats');
	  $skip = JRequest::getVar('skip',0);
	  $db = JFactory::getDBO();
	  
	  $LIMIT = 200;

// ============== kivenni ha már jó a program ==============
//$fp = fopen(JPATH_ROOT.'/dbg.txt','w+');
//fwrite($fp,date('Y-m-d H:i:s')."\n");
//fclose($fp);
// ============== kivenni ha már jó a program ==============

	  
	  if ($skip == 0) {
		  // ez az első aktivizálódás, most kell az uploaded file-t átmozgatni a tmpl dir -be
          if (is_uploaded_file($_FILES['csvfile']['tmp_name'])) {
            $name = $_FILES['csvfile']['name'];
		    $dirname=JPATH_ROOT.'/tmp';
            move_uploaded_file($_FILES['csvfile']['tmp_name'],$dirname.'/csvimport'.$user->id.'.csv');
			// totaRows meghatározása
			$rows = file($dirname.'/csvimport'.$user->id.'.csv'); 
            $csvTotal = count($rows);
			$session->set('csvfilename',$_FILES['csvfile']['name']);
		  } else {
			$this->setMessage('Hiba lépett fel a fájl feltöltés közben, vagy nem adott meg feltöltendő fájlt');
			$this->setRedirect('index.php?optio=com_tagnyilvantartas&view=kapcsolatoks');
			$this->redirect();
		  }
		  if (JRequest::getVar('firstignore')==1) $skip++;		 
		  $joDarab = 0;
		  $hibasDarab = 0;
		  $utkozoDarab = 0;
		  $errorIndo = '';
	  } else {
		  $csvTotal = JRequest::getVar('csvTotal',0);
		  $joDarab = JRequest::getVar('jo',0);
		  $hibasDarab = JRequest::getVar('hibas',0);
		  $utkozoDarab = JRequest::getVar('utkozes',0);
		  $errorInfo = $session->get('errorInfo');
	  }
	  $kapcsolatModel = $this->getModel('kapcsolatok');
	  $fpCSV = fopen(JPATH_ROOT.'/tmp/csvimport'.$user->id.'.csv','r');
	  $eof = $this->csvImport($fpCSV, $skip, $LIMIT,
	                          $user, $userTerhats, $kapcsolatModel,
	                          $joDarab, $hibasDarab, $utkozoDarab, $errorInfo);
	  fclose($fpCSV);
	  if ($eof) {
		  // készvagyunk
		  // csv file és erroribfo session változó  törlése
		  unlink(JPATH_ROOT.'/tmp/csvimport'.$user->id.'.csv');
		  $session->set('errorInfo','');
		  // $errorInfo tárolása file-ba
		  if (($hibasDarab > 0) | ($utkozoDarab > 0)) {
	        $dirname=JPATH_ROOT.'/tmp';
		    $fp = fopen($dirname.'/errorinfo'.$user->id.'.txt','w+');
		    fwrite($fp,str_replace('<br />',"\n",$errorInfo));
		    fclose($fp);
		    $errorInfo= '<br /><a href="'.JURI::root().'tmp/errorinfo'.$user->id.'.txt" target="new" style="color:blue">Kattints ide ennek a hibalistának a mentéséhez</a><br />'.$errorInfo;
		  }
		  // kerület irszből
		  $db->setQuery('UPDATE #__tny_kapcsolatok 
			SET  kerulet = SUBSTRING(irsz,2,2)
			WHERE kerulet = 0 and
				  SUBSTRING(irsz,1,1) = 1 AND 
				  (SUBSTRING(irsz,2,2) >= 01 AND SUBSTRING(irsz,2,2) <= 23);   
			');
		  $db->query();
		  
		  // 2015.07.27 megbeszélés alapján a tart.hely default értéke az állandó lakcím
		  $db->setQuery('UPDATE #__tny_kapcsolatok 
						SET tirsz = irsz, 
							  ttelepules = telepules,
							  tkerulet = kerulet,
							  tutca = utca,
							  tkjelleg = kjelleg,
							  thazszam = hazszam,
							  tcimkieg = cimkieg     
						WHERE tirsz = "" AND
							  ttelepules = "" AND
							  tkerulet = 0 AND
							  tutca = "" AND
							  tkjelleg = "" AND
							  thazszam = 0 AND
							  tcimkieg = "";
						');
		  $db->query();
		  
		  
		  $this->setMessage('CSV file-ból  betöltve '.
		    $joDarab.' sor, Hibás: '.$hibasDarab.' sor Ütközés:'.$utkozoDarab.' esetben<br />'.
		    '<div style="background-color:white; color:black">'.$errorInfo.'</div>'
		  );
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
	  } else {
		  // nem vagyunk készen, önmaga visszahívása
		  // linkbe: (skip, karakter készlet, terminátor, ütközésVizsgálat, csvType, jo, hibas, utkozes)
		  $skip = $skip + $LIMIT;
		  $session->set('errorInfo',$errorInfo);
		  echo '<div class="lmpForm">
		        <h3>CSV feldolgozás...</h3>
				<p><strong>NE ZÁRD BE A BÖNGÉSZŐT, NE NAVIGÁLJ MÁS OLDALRA!</strong></p>
				<p>eddig feldolgozva:'.$skip.' sor Összesen feldolgozandó:'.$csvTotal.' sor</p>
				<div id="turelem" class="turelem">
				  <div class="turelemSzoveg">Türelmet kérek.....</div>
				</d				
				<br /><br />
		  <form method="post" name="adminForm" action="index.php">
		    <input type="hidden" name="option" value="com_tagnyilvantartas" />
		    <input type="hidden" name="view" value="kapcsolatoks" />
		    <input type="hidden" name="task" value="kapcsolatoks.doimport" />
		    <input type="hidden" name="skip" value="'.$skip.'" />
		    <input type="hidden" name="charset" value="'.JRequest::getVar('charset').'" />
		    <input type="hidden" name="fieldterminator" value="'.JRequest::getVar('fieldterminator').'" />
		    <input type="hidden" name="importType" value="'.JRequest::getVar('importType').'" />
		    <input type="hidden" name="utkozestis" value="'.JRequest::getVar('utkozestis').'" />
		    <input type="hidden" name="jo" value="'.$joDarab.'" />
		    <input type="hidden" name="hibas" value="'.$hibasDarab.'" />
		    <input type="hidden" name="utkozes" value="'.$utkozoDarab.'" />
		    <input type="hidden" name="csvTotal" value="'.$csvTotal.'" />
			<!-- p><button type="submit">Tovább</button></p -->
		  </form>
		  </div>
		  <script type="text/javascript">
		    function redirect() {
			  document.adminForm.submit();	
			}
			setTimeout("redirect();",1000);
		  </script>
		  ';
	  }
	}
    
    /**
      * mégsem gombot nyomtak a szürés vagy groupedit vagy export képernyõn
      * ugrás a kapcsolatok böngészõ képernyõre
      */
    public function megsem() {
        $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
        $this->redirect();
    }
    public function cancel() {
        $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
        $this->redirect();
    }
	
	/**
	  * Hibás email cimek kigyüjtése (szürés like -al a csillagos email cimekre)
	*/  
    public function hibasemail() {
		?>
		<div class="hibasemail">
		   <br />
		   <h2>Hírlevél küldésnél hibásnak talált e-mail címek</h2>
		   <div>
		   A program mindennap egyszer (este 20:00 -kor) ellenörzi, hogy az elmult 48 órában történt-e hírlevél kiküldés, és
		   abban voltak-e olyan címek ahová nem sikerült a levelet elküldeni.
		   <br />
		   Ha voltak ilyenek, akkor ezeket az e-mail címeket a kapcsolatok adatbázisban csilaggal megjelöli. Az így megjelőlt címekre
		   a továbbiakban nem történik hírlevél küldés.
		   <br />
		   Ha sikerül a helyes email címet megtudni akkor a normál modosítás funkcióval a csillagot távolitsuk el és írjuk be a helyes címet.
		   Ha úgy tünik mégis az eredeti cím a helyes, akkor csak a csillagot távolitsuk el.
		   <br />
		   <br />
		   <strong>Az adatbázisban csilaggal megjeleölt (hibásnak tartott) e-mail címek a program szürési funkciójával tekinthetőek meg a</strong>;
		   <ul style="font-size:16px;">
		   <li>email tartalmazza * illetve</li>
		   <li>email 2 tartalmazza * illetve</li>
		   <li>belső email tartalmazza *</li>
		   </ul>
		   <strong>feltételek megadásával.</strong>
		   <br />
		   <br />
		   <br />
		   <p><a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks&task=kapcsolatoks.szures">Tovább a szüréshez</a></p>
		   <br />
		   </div>
		</div>
		<?php
	}	

	/**
	* a kezelő a szürés utáni browser képernyőn a "tovább szűkít" gombra kattintott.
	* Ilyenkor a sessionban lévő "elozoSzures" adatot hasznlva kell a szürő képernyőt kirajzolni
	* ez a funkció benne van a tmpl/szures.php -ban csak a tovab="I" JRequest paramétert 
	* kell beállítani és a "funkcio" -nak megfelelő taskot aktivizálni
	*/
	public function tovabb() {
		JRequest::setVar('tovabb','I');
		if (JRequest::getVar('funkcio') == 'hirlevel') {
			JRequest::setVar('task','hirlevel');
			$this->hirlevel();
		} else if (JRequest::getVar('funkcio') == 'groupedit') {
			JRequest::setVar('task','groupedit');
			$this->groupedit();
		} else if (JRequest::getVar('funkcio') == 'export') {
			JRequest::setVar('task','szurtexport');
			$this->szurtexport();
		} else {
			JRequest::setVar('task','szures');
		    $this->szures();
		}	
	}
    
	/**
	  * telefonálási pupop ablak feldolgozója, rejtett iframe-be van irányitva
	  * @JRequests: kapcs_id, telstatus, opcionálisan: nemszimpatizans, nemkerhirlevelet, nemkerhivast
	  * @return void
	*/  
	public function telpopup() {
		$db = JFactory::getDBO();
		$kapcs_id = JRequest::getVar('kapcs_id');
		$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($kapcs_id));
		echo $db->getQuery().'<br /><br />';
		$kapcs = $db->loadObject();
		if ($kapcs) {
			$telszammegj = $kapcs->telszammegj;
			$telstatus = JRequest::getVar('telstatus');
			$nemszimpatizans = (JRequest::getVar('nemszimpatizans')==1);
			$nemkerhirlevelet = (JRequest::getVar('nemkerhirlevelet')==1);
			$nemkerhivast = (JRequest::getVar('nemkerhivast')==1);
			
			// régi utolsó hivás státusz eltávolitása
			$i = mb_strpos(' '.$telszammegj,'<br />[');
			if ($i > 0)
				$telszammegj = substr($telszammegj,0,$i-1);
			

			if ($nemszimpatizans) {
				if (mb_strpos($telszammegj,'MÁR NEM SZIMPATIZÁNS')===false) 
					$telszammegj = 'MÁR NEM SZIMPATIZÁNS '.$telszammegj;
			}
			

			if ($nemkerhivast) {
				if (mb_strpos($telszammegj,'NEM KÉR HÍVÁST')===false) 
					$telszammegj = 'NEM KÉR HÍVÁST '.$telszammegj;
			}


			if ($nemkerhirlevelet) {
				if (mb_strpos($telszammegj,'NEM KÉR HÍRLEVELET')===false) 
					$telszammegj = $telszammegj.' NEM KÉR HÍRLEVELET';
				// email cím csillagozás a kapcsolat rekordban
				$db->setQuery('update #__tny_kapcsolatok
				set email = concat("*",email)
				where kapcs_id='.$db->quote($kapcs_id).' and email <> "" and substr(email,1,1) <> "*"');
				$db->query();
				$db->setQuery('update #__tny_kapcsolatok
				set email2 = concat("*",email2)
				where kapcs_id='.$db->quote($kapcs_id).' and email2 <> "" and substr(email2,1,1) <> "*"');
				$db->query();
				echo $db->getQuery().'<br /><br />';
			}
			
			
			// ha rossz telefonszám akkor telszám csillagozása a kapcsolat rekordban
			if ($telstatus == 'rossz szám')  {
				$db->setQuery('update #__tny_kapcsolatok
				set telefon = concat("*",telefon)
				where kapcs_id='.$db->quote($kapcs_id).' and telefon <> "" and substr(telefon,1,1) <> "*"');
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
				if (substr($kapcs->telefon,0,1) != '*') $kapcs->telefon = '*'.$kapcs->telefon;
			}
			
			// új utolsó hivás státusz beirása
			$telszammegj .= '<br />['.date('Y-m-d').' '.$telstatus.']';

			
			// telszammegj modositása a kapcsolat rekordban
			$db->setQuery('update #__tny_kapcsolatok
			set telszammegj ='.$db->quote($telszammegj).'
			where kapcs_id='.$db->quote($kapcs_id));
			$db->query();
			//DBG echo $db->getQuery().'<br /><br />';
			
			// hivó képernyőn a telefonszám és a megjegyzés updatelése
			echo '
			<script type="text/javascript">
			  parent.document.getElementById("tsz'.$kapcs->kapcs_id.'").innerHTML = "'.$kapcs->telefon.'";
			  parent.document.getElementById("tm'.$kapcs->kapcs_id.'").innerHTML = "'.$telszammegj.'";
			</script>
			';
			
		}
	}
}
