   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:kampany.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas    
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
// 2017.07.05 jogosultság ellenörzés

 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelCimkek 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelKampany  extends JModelAdmin { 

    public $errorFields = array();
	
	/**
	* egy rekord beolvasása
	* (hozzá olvassa a #__tny_kampany_terszerv adatokat is) 
	*/
	public function getItem($id=0) {
		$db = JFactory::getDBO();
		$result = parent::getItem($id);
		$db->setQuery('select tsz.terszerv_id, tsz.nev, kt.id
		from #__tny_teruletiszervezetek tsz
		left outer join #__tny_kampany_terszerv kt on kt.terszerv_id = tsz.terszerv_id and kt.kampany_id = '.$db->quote($result->id).'
		where tsz.terszerv_id not in (1,2,37)
		order by nev
		');
		$result->terszervek = $db->loadObjectList();
		return $result;
	}
	
	/**
	* adat tárolása a képernyöről érkező adatok alapján
	* (beleértve a tesrületi szervezeteket is)
	*/
	// 2017.07.05 jogosultság ellenörzés
	public function save(&$data) {
		//DBG  echo json_encode($data);
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if (($userCsoport->kod == 'A') |
			($userCsoport->kod == 'SM') |		
			($userCsoport->kod == 'CB') 		
		    ) {
			$db = JFactory::getDBO();
			$input = JFactory::getApplication()->input;
			$result = parent::save($data);
			if ($result) {
				if ($data['id'] <= 0) {
					$db->setQuery('select max(id) cc from #__tny_kampany');
					$res = $db->loadObject();
					$data['id'] = $res->cc;
				} 
				$db->setQuery('delete from #__tny_kampany_terszerv where kampany_id='.$db->quote($data['id']));
				$db->query();
				for ($i=0; $i < 50; $i++) {
					$w = $input->get('terszerv_'.$i,'');
					if ($w != '') {
						$db->setQuery('insert into #__tny_kampany_terszerv
						values (0,'.$db->quote($data['id']).','.$db->quote($w).')');
						$db->query();
					}
				}
			}
		} else {
			$result = false;
			$this->setError('Access violation');
		}
		return $result;
	}
		
/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form. [optional]
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure

	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_tagnyilvantartas.kampany', 'kampany', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_tagnyilvantartas.edit.kampany.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_tagnyilvantartas.kampany', $data);
		}
		

		return $data;
	}
	/**
      * @return array|false
      * @param JForm
      * @param array
      * @param string
     */   
	public function validate(& $form, $data, $group=0) {
      //DBG foreach ($data as $fn => $fv) echo 'data '.$fn.'='.$fv.'<br>'; exit();
      $id = 0;
      $cid = JRequest::getVar('cid');
      if (is_array($cid)) $id = $cid[0];
      
      $errorMsg = '';      
      $this->errorFields = array();
      if ($data['megnev']=='') {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_NO_EMPTY').'<br />';
          $this->errorFields[] = 'megnev';
      }
      $db = JFactory::getDBO();
      $db->setQuery('select * 
                     from #__tny_kampany 
                     where megnev = '.$db->quote($data['megnev']).' and 
                           id<>'.$db->quote($id));
      $res = $db->loadObject();
      if ($res) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_IS_DOUBLE').'<br />';
          $this->errorFields[] = 'megnev';
          // DBG .$db->getQuery().'<br />';
      }      
      $session = JFactory::getSession();
      if ($errorMsg != '') {
          $session->set('errorFields',$this->errorFields);     
          $this->setError($errorMsg);
          return false;
      } else {
          $session->set('errorFields',array());     
      }
      return $data;  
    }
    
    /**
      * @return boolean
      * @param array of primary_keys
    */  
	// 2017.04.05 jogosultás ellenörzés
    public function delete($pks) {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if (($userCsoport->kod == 'A') |
			($userCsoport->kod == 'SM') |		
			($userCsoport->kod == 'CB') 		
		    ) {
		   $errorMsg = '';
		   $db = JFactory::getDBO();
		   if (count($pks) > 1) {
			 $this->setError(JText::_('COM_TAGNYILVANTARTAS_SELECT_ONLY_ONE')); 
			 return false; 
		   }
		   foreach ($pks as $pk) {
			   // ellenörzi, hogy a "$pk" rekord törölhető-?
			   // ha nem ir az errorMessagbe
			   // $errorMsg .= $pk.' '.JText::_('COM_TAGNYILVANTARTAS_CANNOT_DELETE').'<br />';
		   }
		   if ($errorMsg == '') {
			   $result = parent::delete($pks);
			   foreach ($pks as $pk) {
				   $db->setQuery('delete from #__tny_kampany_terszerv where kampany_id='.$db->quote($pk));
				   $db->query();
			   }
		   } else {      
			 $this->setError($errorMsg); 
			 return false; 
		   }  
		} else {
				$result = false;
				$this->setError('Access violation');
		}
		 return $result;
	}	 
	
	/**
	* kampámy statisztika lekérése
	* @param integer kampany_id
	* @return object
	*/
	public function getStatisztika($kampany_id) {
		$db = JFactory::getDBO();
		$db->setQuery('select * from #__tny_kampany where id='.$db->quote($kampany_id));
		$result = $db->loadObject();
		$db->setQuery('
		SELECT "(1) e-mail cím összesen" AS `info`,
			   "" AS `kerdes`,
			   "" AS `valasz`,
			   (s.senthtml+s.senttext+s.fail) AS `darab`
		FROM lmp_acymailing_stats AS s
		INNER JOIN lmp_tny_kampany AS k ON k.hirlevel_id = s.mailid
		WHERE k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(2) sikeresen elküldve",
			   "",
			   "",
			   (s.senthtml+s.senttext) 
		FROM lmp_acymailing_stats AS s
		INNER JOIN lmp_tny_kampany AS k ON k.hirlevel_id = s.mailid
		WHERE k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(3) hírlevelet megnyitotta",
			   "",
			   "",
			   (s.openunique) 
		FROM lmp_acymailing_stats AS s
		INNER JOIN lmp_tny_kampany AS k ON k.hirlevel_id = s.mailid
		WHERE k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(4) Hírlevél csatlakozok linkre kattintott",
			   "",
			   "",COUNT(auc.subid)
		FROM lmp_acymailing_urlclick auc
		INNER JOIN lmp_acymailing_url au ON au.urlid = auc.urlid
		INNER JOIN lmp_tny_kampany AS k ON k.hirlevel_id = auc.mailid
		WHERE au.url = "http://lehetmas.hu/csatlakozom" AND k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(5) hírlevélről leiratkozott",
			   "",
			   "",
			   (s.unsub) 
		FROM lmp_acymailing_stats AS s
		INNER JOIN lmp_tny_kampany AS k ON k.hirlevel_id = s.mailid
		WHERE k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(6) Telefonon hívott kontaktok összesen",
			   "",
			   "",
			   COUNT(DISTINCT kk.kapcs_id)
		FROM lmp_tny_kampany_kapcs AS kk
		INNER JOIN lmp_tny_kampany AS k ON k.id = kk.kampany_id
		WHERE k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(7) Telefont felvette",
			   "",
			   "",
			   COUNT(DISTINCT kk.kapcs_id)
		FROM lmp_tny_kampany_kapcs AS kk
		INNER JOIN lmp_tny_kampany AS k ON k.id = kk.kampany_id
		INNER JOIN lmp_tny_kapcsolatok AS ka ON ka.kapcs_id = kk.kapcs_id
		WHERE kk.valasz <> "nem vette fel" AND k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(8) Már nem szimpatizál az LMP -vel",
			   "",
			   "",
			   COUNT(DISTINCT kk.kapcs_id)
		FROM lmp_tny_kampany_kapcs AS kk
		INNER JOIN lmp_tny_kampany AS k ON k.id = kk.kampany_id
		INNER JOIN lmp_tny_kapcsolatok AS ka ON ka.kapcs_id = kk.kapcs_id
		WHERE ka.telmegj2 LIKE "%szimpatizáns:Nem%" AND k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "(9) Továbbra is szimpatizál az LMP -vel",
			   "",
			   "",
			   COUNT(DISTINCT kk.kapcs_id)
		FROM lmp_tny_kampany_kapcs AS kk
		INNER JOIN lmp_tny_kampany AS k ON k.id = kk.kampany_id
		INNER JOIN lmp_tny_kapcsolatok AS ka ON ka.kapcs_id = kk.kapcs_id
		WHERE ka.telmegj2 LIKE "%szimpatizáns:Igen%" AND k.id = '.$db->quote($kampany_id).'

		UNION ALL
		SELECT "kerdes", k.kerdes, kk.valasz, COUNT(DISTINCT kapcs_id)
		FROM lmp_tny_kampany AS k
		INNER JOIN lmp_tny_kampany_kapcs AS kk ON kk.kampany_id = k.id 
		WHERE k.kerdes <> "" AND k.id = '.$db->quote($kampany_id).' and kk.valasz <> "nem vette fel"
		GROUP BY k.kerdes, kk.valasz

		UNION ALL
		SELECT "kerdes", k.kerdes1, kk.valasz1, COUNT(DISTINCT kapcs_id)
		FROM lmp_tny_kampany AS k
		INNER JOIN lmp_tny_kampany_kapcs AS kk ON kk.kampany_id = k.id 
		WHERE k.kerdes1 <> "" AND k.id = '.$db->quote($kampany_id).' and kk.valasz <> "nem vette fel"
		GROUP BY k.kerdes1, kk.valasz1

		UNION ALL
		SELECT "kerdes", k.kerdes2, kk.valasz2, COUNT(DISTINCT kapcs_id)
		FROM lmp_tny_kampany AS k
		INNER JOIN lmp_tny_kampany_kapcs AS kk ON kk.kampany_id = k.id 
		WHERE k.kerdes2 <> "" AND k.id = '.$db->quote($kampany_id).' and kk.valasz <> "nem vette fel"
		GROUP BY k.kerdes2, kk.valasz2

		UNION ALL
		SELECT "kerdes", k.kerdes3, kk.valasz3, COUNT(DISTINCT kapcs_id)
		FROM lmp_tny_kampany AS k
		INNER JOIN lmp_tny_kampany_kapcs AS kk ON kk.kampany_id = k.id 
		WHERE k.kerdes3 <> "" AND k.id = '.$db->quote($kampany_id).' and kk.valasz <> "nem vette fel"
		GROUP BY k.kerdes3, kk.valasz3
		');
		$result->lines = $db->loadObjectList();
		return $result;
	}
	
	/**
	* statisztika részletezés név lista elérése
	* @param int kampany_id
	* @param str info`
	* @param str kerdes
	* @param str valasz
	* @return [{"kapcs_id":###, "nev":"xxxx", "terszerv":"xxxx","status":"xxxx"},...]
	*
	*/
	public function getNevek($kampany_id, $info, $kerdes, $valasz) {
		$session = JFactory::getSession();
		$db = JFactory::getDBO();
		
		$terhatStr = '';
		foreach ($session->get('userTerhats') as $terhat) {
			if ($terhatStr == '')
				$terhatStr = $terhat->terszerv_id;
			else
				$terhatStr .= ','.$terhat->terszerv_id;
		}
		$terhatStr = '('.$terhatStr.')';
		
		// alap sql a hírleveles lekérdezésekhez
		$sql = 'SELECT DISTINCT k.kapcs_id, concat(k.nev1," ",k.nev2," ",k.nev3) AS nev, t.nev AS terszerv, kat.szoveg AS `status`,
  		  k.terszerv_id," " AS hivasido
		FROM #__tny_kampany AS kamp 
		LEFT OUTER JOIN #__acymailing_userstats AS us ON us.mailid = kamp.hirlevel_id
		LEFT OUTER JOIN #__acymailing_subscriber AS s ON s.subid = us.subid
		LEFT OUTER JOIN #__tny_kapcsolatok k ON k.email = s.email OR k.email2 = s.email
		LEFT OUTER JOIN #__tny_teruletiszervezetek t ON t.terszerv_id = k.terszerv_id
		LEFT OUTER JOIN #__tny_kategoriak AS kat ON kat.kategoria_id = k.kategoria_id';
		
		// alap sql a teleonos lekérdezésekhez
		$sql2 = 'SELECT DISTINCT k.kapcs_id, concat(k.nev1," ",k.nev2," ",k.nev3) AS nev, t.nev AS terszerv, 
		   kat.szoveg AS `status`, kk.hivasido
		FROM #__tny_kampany AS kamp 
		LEFT OUTER JOIN #__tny_kampany_kapcs AS kk ON kk.kampany_id = kamp.id
		LEFT OUTER JOIN #__tny_kapcsolatok k ON k.kapcs_id = kk.kapcs_id
		LEFT OUTER JOIN #__tny_teruletiszervezetek t ON t.terszerv_id = k.terszerv_id
		LEFT OUTER JOIN #__tny_kategoriak AS kat ON kat.kategoria_id = k.kategoria_id';
		
		if ($info == '(1) e-mail cím összesen') {
		  $db->setQuery('select *
		  FROM ('.$sql.'
		  WHERE kamp.id = '.$db->quote($kampany_id).'
		  ) w	
		  WHERE w.terszerv_id in '.$terhatStr);
		  $result = $db->loadObjectList();
		} else if ($info == '(2) sikeresen elküldve') {
		  $db->setQuery('SELECT *
		  FROM ('.$sql.'
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND us.sent = 1
		  ) w
		  WHERE w.terszerv_id in '.$terhatStr);	
		  $result = $db->loadObjectList();
		} else if ($info == '(3) hírlevelet megnyitotta') {
		  $db->setQuery('SELECT *
		  FROM ('.$sql.'
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND us.open = 1
		  ) w
		  WHERE w.terszerv_id in '.$terhatStr);	
  		  $result = $db->loadObjectList();
		} else if ($info == '(4) Hírlevél csatlakozok linkre kattintott') {
		  $db->setQuery('SELECT *
		  FROM ('.$sql.'
		  INNER JOIN #__acymailing_urlclick auc ON auc.subid = us.subid AND auc.mailid = us.mailid
		  INNER JOIN lmp_acymailing_url au ON au.urlid = auc.urlid
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND us.sent = 1 AND  au.url = "http://lehetmas.hu/csatlakozom" 
		  ) w
		  WHERE w.terszerv_id in '.$terhatStr.'
		  ');	
		  $result = $db->loadObjectList();
		} else if ($info == '(5) hírlevélről leiratkozott') {
			echo '<br /><br /><br />
			<p><strong>A leiratkozásokról a program az adatkezelési tv.-nek megfelelően nem vezet részletes, (névreszóló) nyilvántartást.</strong></p>
			<p> </p>
			';
			$result = array();
		} else if ($info == '(6) Telefonon hívott kontaktok összesen') {
		  $db->setQuery($sql2.'
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND (k.terszerv_id in '.$terhatStr.')
		  ');
		  $result = $db->loadObjectList();
		} else if ($info == '(7) Telefont felvette') {
		  $db->setQuery($sql2.'
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND k.telszammegj LIKE "%felvette%" AND (k.terszerv_id in '.$terhatStr.')
		  ');
		  $result = $db->loadObjectList();
		} else if ($info == '(8) Már nem szimpatizál az LMP -vel') {
		  $db->setQuery($sql2.'
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND k.telmegj2 LIKE "%szimpatizáns:Nem%" AND (k.terszerv_id in '.$terhatStr.')
		  ');
		  $result = $db->loadObjectList();
		} else if ($info == '(9) Továbbra is szimpatizál az LMP -vel') {
		  $db->setQuery($sql2.'
		  WHERE kamp.id = '.$db->quote($kampany_id).' AND k.telmegj2 LIKE "%szimpatizáns:Igen%" AND (k.terszerv_id in '.$terhatStr.')
		  ');
		  $result = $db->loadObjectList();
		} else if ($info == 'kerdes') {
			// kérdések, válaszok
			$db->setQuery($sql2.'
			WHERE kamp.id = '.$db->quote($kampany_id).' AND kamp.kerdes = "'.$kerdes.'" AND kk.valasz = "'.$valasz.'" AND (k.terszerv_id in '.$terhatStr.')
			UNION
			'.$sql2.'
			WHERE kamp.id = '.$db->quote($kampany_id).' AND kamp.kerdes1 = "'.$kerdes.'" AND kk.valasz1 = "'.$valasz.'" AND (k.terszerv_id in '.$terhatStr.')
			UNION
			'.$sql2.'
			WHERE kamp.id = '.$db->quote($kampany_id).' AND kamp.kerdes2 = "'.$kerdes.'" AND kk.valasz2 = "'.$valasz.'" AND (k.terszerv_id in '.$terhatStr.')
			UNION
			'.$sql2.'
			WHERE kamp.id = '.$db->quote($kampany_id).' AND kamp.kerdes3 = "'.$kerdes.'" AND kk.valasz3 = "'.$valasz.'" AND (k.terszerv_id in '.$terhatStr.')
			');
			$result = $db->loadObjectList();
		} else {
			echo '<p class="error">Nem megfelelő paraméter</p>';
			$result = array();
		}
//DBG echo $db->getQuery();
		return $result;	
	}
}
?>