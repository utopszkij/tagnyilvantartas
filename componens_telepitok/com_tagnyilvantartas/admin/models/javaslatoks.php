 <?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*
* 2016.03.07  jogosutság ellenörzés elfogadásnál és elvetésnél
* "A"  usercsoport mindent kezelhet, "SM" usercsoport csak szimpatizánsokat kezelhet
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_tagnyilvantartas/tables');

class TagnyilvantartasModeljavaslatoks extends JModelList
{
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
        }
		parent::__construct($config);		
	}
	
	/**
	  * rekord sorozat beolvasása
	  * @session userTerhats
	  * @return array of record
	*/  
	public function getItems() {
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
		$db		= $this->getDbo();
        $cf = 'k.terszerv_id in (0';
		if (is_array($userTerhats)) {
			foreach ($userTerhats as $userTerhat) {
				$cf .= ','.$userTerhat->terszerv_id;
			}
		}
        $cf .= ')';
		$db->setQuery('select j.kapcs_id, j.javaslo_id, j.idopont, j.mezo, j.ertek,
		    concat(k.nev1," ",k.nev2," ",k.nev3) knev,
			ka.szoveg,	
		    tsz.nev,
			u.name, k.kategoria_id, j.megjegyzes
			from #__tny_javaslat j
			left outer join #__tny_kapcsolatok k on k.kapcs_id = j.kapcs_id
			left outer join #__tny_teruletiszervezetek tsz on tsz.terszerv_id = k.terszerv_id
			left outer join #__tny_kategoriak ka on ka.kategoria_id = k.kategoria_id
			left outer join #__users u on u.id = j.javaslo_id
			where '.$cf.' and j.allapot="javaslat"
		');
		$result = $db->loadObjectList();
		// echo $db->getQuery();
		return $result;
	}

	/**
	  * aktuális user terhatba tartozó javaslatok számát adja meg
	  * @return integer;
	*/  
	public function getTotal() {
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
		$db		= $this->getDbo();
        $cf = 'k.terszerv_id in (0';
		if (is_array($userTerhats)) {
			foreach ($userTerhats as $userTerhat) {
				$cf .= ','.$userTerhat->terszerv_id;
			}
		}
        $cf .= ')';
		$db->setQuery('select count(j.*) cc
			from #__tny_javaslat j
			left outer join #__tny_kapcsolatok k on k.kapcs_id = j.kapcs_id
			left outer join #__tny_teruletiszervezetek tsz on tsz.terszerv_id = k.terszerv_id
			left outer join #__tny_kategoriak ka on ka.kategoria_id = k.kategoria_id
			left outer join #__users u on u.id = j.javaslo_id
			where '.$cf.' and j.allapot="javaslat"
		');
		$res = $db->loadObject();
		// echo $db->getQuery();
		return $res->cc;
	}
	
	/**
	  * javaslat átvezetése az adatbázisban, javaslat rekord státusz modositás
	*/  
	public function elfogad($kapcs_id, $javaslo_id, $mezo, $ptime) {
	  $db = JFactory::getDBO();
	  $user = JFactory::getUser();	
	  $db->setQuery('select * 
	  from #__tny_javaslat 
	  where kapcs_id='.$db->quote($kapcs_id).' and
	        javaslo_id = '.$db->quote($javaslo_id).' and
			idopont = '.$db->quote($ptime).' and
			mezo = '.$db->quote($mezo)
	  );
	  $res = $db->loadObject();
	  
	  // jogosultság ellenörzés
	  $session = JFactory::getSession();
	  $userCsoport = $session->get('userCsoport');
      $userTerhats = $session->get('userTerhats');
      $cf = 'terszerv_id in (0';
	  if (is_array($userTerhats)) {
		foreach ($userTerhats as $userTerhat) {
			$cf .= ','.$userTerhat->terszerv_id;
		}
	  }
      $cf .= ')';
 	  $db->setQuery('select * from #__tny_kapcsolatok k where kapcs_id='.$db->quote($kapcs_id).' and '.$cf);
	  $kapcsolatRecord = $db->loadObject();
	  echo $db->getQuery().'<br>';
	  if (!$kapcsolatRecord) {
		  return; // nincs meg a kapcsolat rekord vagy terhaton kivüli
	  }
	  $jo = false;
	  if ($userCsoport->kod == 'A') $jo = true;
	  if (($userCsoport->kod == 'SM') & ($kapcsolatRecord->kategoria_id == 3)) $jo = true;
	  if (($userCsoport->kod == 'SM') & ($kapcsolatRecord->kategoria_id == 9)) $jo = true;
	  if ($jo == false) {
		  return;  // nincs ehhez joga
	  }

	  if ($res) {
		if ($mezo=="delete") {
			// kapcsolat rekord törlése
			$db->setQuery('update #__tny_kapcsolatok
			set lastaction="DELETE",
			  lastact_info = "Javaslat elfogadása",
			  lastact_user_id = '.$user->id.',
			  lastact_time = "'.date('Y-m-d H:i:s').'"
			where kapcs_id = '.$kapcs_id  
			);
			$db->query();	
			$db->setQuery('insert into #__tny_naplo
			 select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($kapcs_id));
			$db->query();
			$db->setQuery('delete from #__tny_kapcsolatok where kapcs_id='.$kapcs_id);
			$db->query();
		} else {
			// kapcsolatok rekord modositása
			$db->setQuery('update #__tny_kapcsolatok
			set '.$mezo.' = '.$db->quote($res->ertek).',
			  lastaction="UPDATE",
			  lastact_info = "Javaslat elfogadása",
			  lastact_user_id = '.$user->id.',
			  lastact_time = "'.date('Y-m-d H:i:s').'"
			where kapcs_id = '.$kapcs_id  
			);
			$db->query();	
			$db->setQuery('insert into #__tny_naplo
			 select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($kapcs_id));
			$db->query();
		}
		// javaslat rekord modosítása
		$db->setQuery('update #__tny_javaslat
		set allapot="atvezetve",
				megjegyzes="átvezette:'.$user->name.' '.date('Y-m-d H:i').'"
		where kapcs_id='.$db->quote($kapcs_id).' and
				javaslo_id = '.$db->quote($javaslo_id).' and
				idopont = '.$db->quote($ptime).' and
				mezo = '.$db->quote($mezo)
		);
		$db->query();
	  }
	}

	/**
	  * javaslat elvetése; javaslat rekord státusz modositás
	*/  
	public function elvet($kapcs_id, $javaslo_id, $mezo, $ptime) {
	  $db = JFactory::getDBO();
	  $user = JFactory::getUser();	

	  // jogosultság ellenörzés
	  $session = JFactory::getSession();
	  $userCsoport = $session->get('userCsoport');
      $userTerhats = $session->get('userTerhats');
      $cf = 'terszerv_id in (0';
		if (is_array($userTerhats)) {
			foreach ($userTerhats as $userTerhat) {
				$cf .= ','.$userTerhat->terszerv_id;
			}
		}
      $cf .= ')';
 	  $db->setQuery('select * from #__tny_kapcsolatok k where kapcs_id='.$db->quote($kapcs_id).' and '.$cf);
	  $kapcsolatRecord = $db->loadObject();
	  if (!$kapcsolatRecord) {
		  return; // nincs meg a kapcsolat rekord vagy terhaton kivüli
	  }
	  $jo = false;
	  if ($userCsoport->kod == 'A') $jo = true;
	  if (($userCsoport->kod == 'SM') & ($kapcsolatRecord->kategoria_id == 3)) $jo = true;
	  if (($userCsoport->kod == 'SM') & ($kapcsolatRecord->kategoria_id == 9)) $jo = true;
	  if ($jo == false) {
			return;  // nincs ehhez joga
	  }

	  $db->setQuery('update #__tny_javaslat
		set allapot="elvetve",
		    megjegyzes="elvetette:'.$user->name.' '.date('Y-m-d H:i').'"
	    where kapcs_id='.$db->quote($kapcs_id).' and
	        javaslo_id = '.$db->quote($javaslo_id).' and
			idopont = '.$db->quote($ptime).' and
			mezo = '.$db->quote($mezo)
	  );
	  $db->query();
	  //echo $db->getQuery(); exit();
	}
	
}
?>