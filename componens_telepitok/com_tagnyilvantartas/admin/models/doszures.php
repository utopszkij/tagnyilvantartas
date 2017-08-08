 <?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*
* 2016-02-05 hírlevél küldésnél a * -os email cimeket kihagyni.
* 2016-02-22 hírlevél küldésnél a hírlevél ellenöröknek hozzátétele
* 2017.04.01. kampany szervezésnél "kampanyFilter" szürőfeltétel kezelése
* 2017.04.27 call centereseknek csak a jó telefonszámosakat hozni fel
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_tagnyilvantartas/tables');

class TagnyilvantartasModeldoszures extends JModelList
{
	private $hirlevel_csatlakozas_feltetel = false;
	private $kampanySzervezes = false;
	
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                            'Név', 'a.nev1',
							'Település','a.tepeules',
							'Területi szervezet','t.nev',
							'Kategória','k.szoveg',
							'Ellenörzött','a.ellenorzott',
							'Kommentek','a.komment_db',
                            'Kapcs_id', 'a.fcsop_id',
            );
        }

		parent::__construct($config);		
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
			parent::populateState();
			$app = JFactory::getApplication();
            $config = JFactory::getConfig();
			$id = $app->input->getInt('id', null);
			$this->setState('kapcsolatoklist.id', $id);			
			
			// Load the filter state.
			$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
			$this->setState('filter.search', $search);

			$app = JFactory::getApplication();
			$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit'));
			$limit = $value;
			$this->setState('list.limit', $limit);
			
			$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);
			
			$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
			$this->setState('list.ordering', $value);			
			$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
			$this->setState('list.direction', $value);

					
	}
    		
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('kapcsolatoklist.id');
		return parent::getStoreId($id);
	}	
	
	/**
	  * az "in" -es feltételek spec rutinja
	  * @param $s ('ertek1','ertek2'.....) formátumu
	  * @return (UPPER('ertek1),UPPER('ertek2')....)
	*/  
	protected function specUpper($s) {
		$w = explode(',',$s);
		$result = '';
		foreach ($w as $i => $w1) {
			$w1 = str_replace("'",'',$w1);
			$w1 = str_replace('"','',$w1);
			$w1 = str_replace("(",'',$w1);
			$w1 = str_replace(")",'',$w1);
			  $w[$i] = 'UPPER("'.trim($w1).'")';
		}
		return '('.implode(',',$w).')';
	}

	/**
	 *
	 * szüro feltételek érkezhetnek JRequest-bol: onlychevked, mezo##, relacio##, ertek##, savedfilter
	 * vagy ha JRequestbol nem jön akkor sessionból veendo
	 * @return string
	*/ 
	public function makeWhereStr($userTerhats) {
  		$session = JFactory::getSession();
		if (!is_array($userTerhats)) {
          $userTerhats = $session->get('userTerhats');
		}  
        $userCsoport = $session->get('userCsoport');
		$user =JFactory::getUser();
			//if JRequest savedfilter érkezett akkor
			// - be kell olvasni az uploaded file-t, ebbol kell A JRequest adatokat felülirni
			$uploaddir = JPATH_SITE.'/tmp/';
            $uploadfile = $uploaddir . $user->id. 'filter.ini';
			if (move_uploaded_file($_FILES['savedfilter']['tmp_name'], $uploadfile)) {
			   $lines = file($uploadfile);
			   foreach ($lines as $line) {
				   if ($line != '') {
					   $w = explode('=',$line,2);
					   if (count($w)==2) {
						   JRequest::setVar($w[0],$w[1]);
						   //DBG echo 'setVar '.$w[0].'='.$w[1].'<br>';
						}
				   }
			   }
			   unlink($uploadfile);
			}
			
			
			$session = JFactory::getSession();
			$db = JFactory::getDBO();
			// Ha érkezett a JRequest -ben szürés adat akkor a sessionban lévok törlendoek
			$erkezett = false;
			for ($i=0; $i<20; $i++) {
			   if (JRequest::getVar('mezo'.$i,'') != '') $erkezett = true;
			}  	   
			if ($erkezett) {
			  for ($i=0; $i<20; $i++) {
				 $session->set('doszures.mezo'.$i,''); 
				 $session->set('doszures.relacio'.$i,''); 
				 $session->set('doszures.ertek'.$i,''); 
			  }	
			  $session->set('doszures.onlychecked','');
			}
		   
			// onlychecked filter
			if (JRequest::getVar('onlychecked', $session->get('doszures.onlychecked'))==1) {
			   $where = 'a.ellenorzott=1';
			   $session->set('doszures.onlychecked','1');
			   JRequest::setVar('onlychecked','1');
			} else {
			   $where = 'a.ellenorzott >= 0'; 
			   $session->set('doszures.onlychecked','0');
			   JRequest::setVar('onlychecked','0');
			}
			$where .= ' and a.terszerv_id in (0';
			foreach ($userTerhats as $userTerhat) {
				$where .= ','.$userTerhat->terszerv_id;
			}
			$where .= ')';
			
			// Filter by search in JRequest
			for ($i=0; $i<20; $i++) {
			   if (JRequest::getVar('mezo'.$i,$session->get('doszures.mezo'.$i)) != '') {
				   $mezo = JRequest::getVar('mezo'.$i,$session->get('doszures.mezo'.$i));
				   if ($mezo == 'hirlevel_csatlakozas')	
					  $this->hirlevel_csatlakozas_feltetel = true;
				   $relacio = JRequest::getVar('relacio'.$i,$session->get('doszures.relacio'.$i));
				   $ertek = JRequest::getVar('ertek'.$i,$session->get('doszures.ertek'.$i));
				   $session->set('doszures.mezo'.$i,$mezo);
				   $session->set('doszures.relacio'.$i,$relacio);
				   $session->set('doszures.ertek'.$i,$ertek);
				   JRequest::setVar('mezo'.$i,$mezo);
				   JRequest::setVar('relacio'.$i,$relacio);
				   JRequest::setVar('ertek'.$i,$ertek);
				   
				   if ($relacio == 'lt') $relacio = '<';
				   if ($relacio == 'lte') $relacio = '<=';
				   if ($relacio == 'gt') $relacio = '>';
				   if ($relacio == 'gte') $relacio = '>=';
				   if ($relacio == 'ne') $relacio = '<>';
				   
				   // telefonszám standartiozálás
				   if (($mezo == 'telefon') & ($relacio == '=')) {
				   	   $relacio = 'like';
				   	   $ertek = stdTelefonszam($ertek);
				   }
				   
				   // relációnak megfelelő értékek
				   if ($relacio == 'in') {
					   $w = explode(',',$ertek);
					   foreach ($w as $j => $w1) {
						 $w[$j] = $db->quote(trim($w1));    
					   }
					   $ertek = '('.implode(',',$w).')'; 
					   
					   /*
					   $ertek = '(';
					   foreach ($w as $w1) {
						 $ertek .= $db->quote($w1).', ';    
					   }
					   $ertek .= '"@")';
					   */
				   } else if ($relacio == 'between') {
					   if (strpos($ertek,' - ')===false) {
						   $ertek = str_replace('-',' - ',$ertek);
					   }
					   $w = explode(' - ',$ertek);
					   $ertek1 =$db->quote($w[0]);
					   $ertek2 =$db->quote($w[1]);
				   } else if ($relacio == 'like') {
					   $ertek = $db->quote('%'.$ertek.'%');
				   } else {
					   $ertek = $db->quote($ertek);
				   }
				   
				   // tábla alias hozzátétele és karekteres mezők nagybetüsitve
				   if  (($mezo == 'nev1') | ($mezo == 'nev2') | ($mezo == 'nev3') | 
						($mezo == 'telepules') | ($mezo == 'ttelepules') |
						($mezo == 'kozterület') | ($mezo == 'tkozterület') | 
						($mezo == 'kapcsnev')  |
						($mezo == 'cimke') | ($mezo == 'email') |
						($mezo == 'email2') | ($mezo == 'belsoemail') |
						($mezo == 'orszag') | ($mezo == 'torszag') 
					   ) {
						 $mezo = 'UPPER(a.'.$mezo.')';  
						 if ($relacio != 'in')
						     $ertek = 'UPPER('.$ertek.')';  
					     else
						     $ertek = $this->specUpper($ertek); 
				   } else if ($mezo == 'kategoria') {		 
					  $mezo = 'UPPER(k.szoveg)';
					  if ($relacio != 'in')
					     $ertek = 'UPPER('.$ertek.')';  
					  else
						 $ertek = $this->specUpper($ertek); 
				   } else if ($mezo == 'terszerv') {		 
					  $mezo = 'UPPER(t.nev)';
					  if ($relacio != 'in')
					    $ertek = 'UPPER('.$ertek.')';  
					  else
						 $ertek = $this->specUpper($ertek); 
				   } else if ($mezo == 'hirlevel_csatlakozas') {		 
					   $mezo = 'hcs.'.$mezo;	
				   } else {
					   $mezo = 'a.'.$mezo;  
				   }                   

				   // a $mezo -re vonatkozó egyedi feltétel  összeállítása
				   if ($relacio == 'between') {
					  $where1 = $mezo.' >= '.$ertek1.' and '.$mezo.' <= '.$ertek2; 
				   } else if ($relacio == 'add') {
					  $where1 = ' addparity('.$mezo.')'; 
				   } else if ($relacio == 'even') {
					  $where1 = ' evenparity('.$mezo.')'; 
				   } else if ($relacio == 'like') {
					  $where1 = $mezo.' like '.$ertek; 
				   } else {
					  $where1 = $mezo.' '.$relacio.' '.$ertek; 
				   }
				   
				   // email cím esetén a feltétel mindhárom mezőre vonatkozik
				   if (($mezo == "UPPER(a.email)") | ($mezo == "UPPER(a.email2)") | ($mezo == "UPPER(a.belsoemail)")) {
					   $where1 = "((UPPER(a.email) $relacio $ertek) or (UPPER(a.email2) $relacio $ertek) or (UPPER(a.belsoemail) $relacio $ertek))";
				   }

				   // telefonszám esetén a feltétel mindkét mezőre vonatkozik
				   if (($mezo == "a.telefon") | ($mezo == "a.telefon2)")) {
					   $where1 = "((a.telefon $relacio $ertek) or (a.telefon2 $relacio $ertek))";
				   }
				   
				   
				   // a teljes where feltételhez adása
				   $where .= ' and '.$where1;
				   
			   } else {
				   $session->set('doszures.mezo'.$i,'');
				   $session->set('doszures.relacio'.$i,'');
				   $session->set('doszures.ertek'.$i,'');
			   } 
			}
			//+ 2016-02-05 hírlevél küldésnél a * -os email cimeket kihagyni.
			if ((JRequest::getVar('funkcio')=='hirlevel') | 
			    (JRequest::getVar('task')=='doszures.hirlevelsend')
			   ) {
				$where .= ' and (substr(a.email,1,1)<>"*" or substr(a.email2,1,1)<>"*" or substr(a.belsoemail,1,1)<>"*")';
			}
			//- 2016-02-05 hírlevél küldésnél a * -os email cimeket kihagyni.

			
			//+ 2017.04.01. kampany szervezésnél "kampanyFilter" szürőfeltétel kezelése
			if (substr(JRequest::getVar('funkcio'),0,7) == 'kampany') {
				$this->kampanySzervezes = true;
				if (JRequest::getVar('filterKampany') == 1) {
					$where .= ' and kc.hivasido is null ';
				}
			}
			//- 2017.04.01. kampany szervezésnél "kampanyFilter" szürőfeltétel kezelése
			
			//+ 2017.04.27 call centereseknek csak a jó telefonszámosakat hozni fel
			if (($userCsoport->kod == 'CC') | ($userCsoport->kod == 'CB')) {
				$where .= ' and (a.telefon <> "" or a.telefon2 <> "") and 
				(substr(a.telefon,1,1) <> "*" or (substr(a.telefon2,1,1) <> "*" and (a.telefon2 <> "")))';
			} 
			//- 2017.04.27 call centereseknek csak a jó telefonszámosakat hozni fel
			
			//+ 2016.12.19 "PO" usercsoport csak párttagokat olvashat
			if ($userCsoport->kod == 'PO') $where .= " and UPPER(k.szoveg) = UPPER('Párttag')";
			
			
			//+ 2016-02-22 hírlevél küldésnél a hírlevél ellenöröknek hozzátétele
			if ((JRequest::getVar('funkcio')=='hirlevel') | 
			    (JRequest::getVar('task')=='doszures.hirlevelsend'))
				$where = '('.$where.') or a.cimkek like "%Hírlevél ellenör%"';
			//- 2016-02-22 hírlevél küldésnél a hírlevél ellenöröknek hozzátétele

			
			return $where;
	}
	
	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 * session->userTerhats területi hatáskört is kezelni!
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 */
	public function getListQuery()
	{
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		
		
		$query->select('distinct a.*, k.szoveg, t.nev');
		$query->from('#__tny_kapcsolatok as a');
		$query->join('INNER','#__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id');             
		$query->join('INNER','#__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id');             
        if ($this->hirlevel_csatlakozas_feltetel)
		   $query->join('INNER','#__tny_hirlevel_csatlakozas AS hcs ON hcs.kapcs_id = a.kapcs_id');             
		//+ 2017.04.01. kampany szervezésnél "kampanyFilter" szürőfeltétel kezelése
        if ($this->kampanySzervezes) {
		   $kampany_id = substr(JRequest::getVar('funkcio'),8,10);	
		   $query->join('LEFT OUTER','#__tny_kampany_kapcs AS kc ON kc.kapcs_id = a.kapcs_id and kc.kampany_id = '.$kampany_id);             
		}	
		//- 2017.04.01. kampany szervezésnél "kampanyFilter" szürőfeltétel kezelése
		$where = $this->makeWhereStr($userTerhats);
        $query->where($where);
		$session->set('doszures.onlychecked',$onlyChecked);
				
		// Add the list ordering clause.
		$orderCol = JRequest::getVar('filter_order',$session->get('doszures.orderCol','a.nev1'));
		$orderDirn = JRequest::getVar('filter_order_Dir',$session->get('doszures.orderDir'.'asc'));
		$session->set('doszures.orderCol',$orderCol);
		$session->set('doszures.orderDir',$orderDirn);
		$limitStart = JRequest::getVar('limitstart', $session->get('doszures.limitstart',0));
		$session->set('doszures.limitstart',$limitStart);
		JRequest::setVar('limitstart',$limitStart);
		
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$this->state->set('list.ordering', $orderCol);
		$this->state->set('list.direction', $orderDirn);
		
		//DBG echo '<div>query:<br /><br /><br /> '.$query.'<br /><br /><br />';
		
		return $query;
	}	
	
	public function getItems() {
		$db = JFactory::getDBO();
		$result = parent::getItems();
		//DBG echo $db->getQuery().'<br /><br />';
		for ($i= 0; $i < count($result); $i++) {
			$res1 = $result[$i];
			$db->setQuery('select count(*) as cc
			from #__tny_kommentek
			where kapcs_id = "'.$res1->kapcs_id.'"');
			$res2 = $db->loadObject();
			if ($res2) $result[$i]->komment_db = $res2->cc;
		}
		//+ 2016-02-05 hírlevél küldésnél a * -os email cimeket kihagyni.
		if ((JRequest::getVar('funkcio')=='hirlevel') | 
			    (JRequest::getVar('task')=='doszures.hirlevelsend')
			   ) {
			for ($i= 0; $i < count($result); $i++) {
				if (substr($result->email,0,1)=='*') $result->email = '';
				if (substr($result->email2,0,1)=='*') $result->email2 = '';
				if (substr($result->belsoemail,0,1)=='*') $result->belsoemail = '';
			}	
		}
		//- 2016-02-05 hírlevél küldésnél a * -os email cimeket kihagyni.
		

		return $result;
	}
	
	/**
	  * csoportos modositás végrehajtása , szoköz, '0', 'ÜRES' mezo tartalmak spec. kezerlése
	  *   szoköz, '0' : a mezo nem változik
	  *   'ÜRES' : a mezo tartalma '' -re változik
	  * @param array jform
	  * @JRequest filter fields
	  * @return true or false
	  */
	public function groupSave($jform) {
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$where = $this->makeWhereStr($userTerhats);
		$utca = $jform['utca'];
		$tutca = $jform['tutca'];
		$kjelleg = $jform['kjelleg'];
		$tkjelleg = $jform['tkjelleg'];
		$oevk = $jform['oevk'];
		$terszerv_id = $jform['terszerv_id'];

		// sql összeállítás
		$sql = 'update 
		  #__tny_kapcsolatok AS a,
		  #__tny_kategoriak AS k, 
		  #__tny_teruletiszervezetek AS t 
		  set lastaction="GROUPEDIT", lastact_time="'.date('Y-m-d H:i:s').'", lastact_user_id="'.$user->id.'"';
		$separator = ',';  
		if ($utca == 'ÜRES') {
			$sql .= $separator.' a.utca = ""';
			$separator = ',';
		} else 	if ($utca != '') {
			$sql .= $separator.' a.utca = '.$db->quote($utca);
			$separator = ',';
		}	
		if ($tutca == 'ÜRES') {
			$sql .= $separator.' a.tutca = ""';
			$separator = ',';
		} else if ($tutca != '') {
			$sql .= $separator.' a.tutca = '.$db->quote($tutca);
			$separator = ',';
		}	
		if ($kjelleg == 'ÜRES') {
			$sql .= $separator.' a.kjelleg = ""';
			$separator = ',';
		} else if ($kjelleg != '') {
			$sql .= $separator.' a.kjelleg = '.$db->quote($kjelleg);
			$separator = ',';
		}
		if ($tkjelleg == 'ÜRES') {
			$sql .= $separator.' a.tkjelleg = ""';
			$separator = ',';
		} else if ($tkjelleg != '') {
			$sql .= $separator.' a.tkjelleg = '.$db->quote($tkjelleg);
			$separator = ',';
		}	
		if ($oevk == 'ÜRES') {
			$sql .= $separator.' a.oevk = ""';
			$separator = ',';
		} else 	if ($oevk != '') {
			$sql .= $separator.' a.oevk = '.$db->quote($oevk);
			$separator = ',';
		}	
		if ($terszerv_id != 0) {
			$sql .= $separator.' a.terszerv_id = '.$db->quote($terszerv_id);
			$separator = ',';
		}	
		$sql .= ' where  t.terszerv_id = a.terszerv_id and 
		   k.kategoria_id = a.kategoria_id and '.$where;             
		
		if ($separator == '') {
			// nem volt egyetlen modositandó mezo sem
			$this->setError(JText::_('COM_TAGNYILVANTARTAS_GROUPEDIT_NOACTION'));
			return false;
		}
		// sql végrehajtás
		$db->setQuery($sql);
		$result = $db->query();
		if ($result == false) $this->setError($db->getErrorMsg());
		//DBG echo $sql; 

		// Naplózás
		if ($result) {
		  $db->setQuery('insert into #__tny_naplo
		  select a.*
		  from #__tny_kapcsolatok AS a,
		       #__tny_kategoriak AS k, 
		       #__tny_teruletiszervezetek AS t 
		  where  t.terszerv_id = a.terszerv_id and 
		   k.kategoria_id = a.kategoria_id and '.$where);
		  $result = $db->query();
		  if ($result == false) $this->setError($db->getErrorMsg());
		}
		
		return $result;
	}

	public function groupdel() {
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$where = $this->makeWhereStr($userTerhats);

		// Naplózás
		$db->setQuery('update 
		     #__tny_kapcsolatok a, 
		     #__tny_kategoriak AS k, 
		     #__tny_teruletiszervezetek AS t 
		set a.lastaction = "GROUP DELETE",
            a.lastact_user_id = "'.$user->id.'",
            a.lastact_time = "'.date('Y-m-d H:i:s').'"			
	    where t.terszerv_id = a.terszerv_id and 
		      k.kategoria_id = a.kategoria_id and '.$where);
		$result = $db->query();	  
	    if ($result == false) $this->setError($db->getErrorMsg());
		if ($result) {
		  $db->setQuery('insert into #__tny_naplo
		  select a.*
		  from #__tny_kapcsolatok AS a,
		       #__tny_kategoriak AS k, 
		       #__tny_teruletiszervezetek AS t 
		  where  t.terszerv_id = a.terszerv_id and 
		   k.kategoria_id = a.kategoria_id and '.$where);
		  $result = $db->query();
		  if ($result == false) $this->setError($db->getErrorMsg());
		}

		// törlés végrehajtás
		$db->setQuery('update 
		     #__tny_kapcsolatok a, 
		     #__tny_kategoriak AS k, 
		     #__tny_teruletiszervezetek AS t 
		set a.nev1 = "DELETE"	 
	    where t.terszerv_id = a.terszerv_id and 
		      k.kategoria_id = a.kategoria_id and '.$where);
		if ($db->query() == false) {
			$this->setError($db->getErrormsg());
			$result = false;
		} else {
			$result = true;
		}
		if ($result) {
			$db->setQuery('delete from #__tny_kapcsolatok where nev1="DELETE"');
			if ($db->query() == false) {
				$this->setError($db->getErrormsg());
				$result = false;
			} else {
				$result = true;
			}
		}
		return $result;
	}	
	
	/**
	* hirlevél küldési infok beolvasása
	* @param integer mailid
	* @return array of object
	*/
	public function getHirlevelInfo($mailid) {
		$db = JFactory::getDBO();
		$db->setQuery('select m.subject, i.datum, i.filter, u.name
		from #__hirlevelfilters i, #__acymailing_mail m, #__users u
		where i.mailid = '.$db->quote($mailid).' and
		m.mailid = i.mailid and u.id = i.user_id
		order by datum
		');
		return $db->loadObjectList();
	}
}