<?php
/**
* @version		$Id:cimkek.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelCimkek 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelDuplak  extends JModelAdmin { 
  /**
    * dupla email cimek és telefonszámok kigyüjtése
	* @return array of object {"tipus":"", "adat":""}
	*/
  public function getItems() {
	$session = JFactory::getSession();
    $userTerhats = $session->get('userTerhats');

    // terhat feltétel
    $cf .= ' and terszerv_id in (0';
    foreach ($userTerhats as $userTerhat) {
            $cf .= ','.$userTerhat->terszerv_id;
    }
    $cf .= ')';
	
	$db = JFactory::getDBO();
	$db->setQuery('select a.tipus, a.adat
	from (select "EMAIL" tipus, concat(email,"                              ") adat
		  from #__tny_kapcsolatok
		  where email <> "" '.$cf.'
		  union all
		  select "TEL", SPLIT_STRING(",",telefon,1)
	      from #__tny_kapcsolatok
		  where length(trim(telefon)) > 6 '.$cf.'
		  union all
		  select "TEL", SPLIT_STRING(",",telefon,2)
	      from #__tny_kapcsolatok
		  where length(trim(telefon)) > 6 '.$cf.'
		  union all
		  select "EMAIL",email2
		  from #__tny_kapcsolatok
		  where email2 <> "" '.$cf.'
		  union all
		  select "EMAIL",belsoemail
		  from #__tny_kapcsolatok
		  where belsoemail <> "" '.$cf.'
		 ) a
	where trim(a.adat) <> ""	 
	group by a.tipus, a.adat
    having count(*) > 1
    order by 1,2	
	');
    return $db->loadObjectList();	
  }
  
  /**
    * dupla kapcsolat rekordok beolvasása
	* @param record {"tipus":"", "adat":""}
	* @return array of kapcsolat records
	*/
  public function getItem($data) {
	$session = JFactory::getSession();
    $userTerhats = $session->get('userTerhats');

    // terhat feltétel
    $cf .= ' and k.terszerv_id in (0';
    foreach ($userTerhats as $userTerhat) {
            $cf .= ','.$userTerhat->terszerv_id;
    }
    $cf .= ')';

	$db = JFactory::getDBO();
	if (trim($data->tipus) == "TEL")
	  $db->setQuery('select k.*
      from #__tny_kapcsolatok k
	  where telefon like'.$db->quote('%'.$data->adat.'%').$cf.'
	  order by kapcs_id');
    else
	  $db->setQuery('select k.*, t.nev terszerNev, ka.szoveg kategoriaNev
      from #__tny_kapcsolatok k
	  left outer join #__tny_teruletiszervezetek t on t.terszerv_id = k.terszerv_id
	  left outer join #__tny_kategoriak ka on ka.kategoria_id = k.kategoria_id
	  where email = '.$db->quote($data->adat).' or 
	        email2 = '.$db->quote($data->adat).' or
			belsoemail = '.$db->quote($data->adat).' '.$cf.'
			order by kapcs_id');
    return $db->loadObjectList();	  
  }
  
  /**
    * update $id rekord JRequest-ben érkezo adatokkal
	* @param integer
	* @JRequest mezo1, mezo2,.... base64_encoded
	*/
  public function update($id) {
	$session = JFactory::getSession();
    $userTerhats = $session->get('userTerhats');
	$user = JFactory::getUser();  
    $db = JFactory::getDBO();
	
	// orig rekord elérérse
	$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($id));
	$res = $db->loadObject();

    // orig rekord területi hatáskörben ?
    $jo = false;
    foreach ($userTerhats as $userTerhat) {
        if ($userTerhat->terszerv_id == $res->terszerv_id) $jo = true;
    }
	
	if ($jo) {
		// új rekord területi hatáskörben?
        $jo = false;
        foreach ($userTerhats as $userTerhat) {
           if ($userTerhat->terszerv_id == base64_decode(JRequest::getVar('terszerv_id',''))) $jo = true;
        }
		
	}
	
	if (!$jo) {
        $this->setError(JText::_('COM_TAGNYILVANTARTAS_NO_TERHAT'));
        return false;  
	}
	
	$sql = '';
	foreach ($res as $fn => $fv) {
		if ($fn != 'kapcs_id')
		   $sql .= $fn.'='.$db->quote(base64_decode(JRequest::getVar($fn,''))).',';
	}
	$sql .= 'lastaction="UPDATE",
	         lastact_time="'.date('Y-m-d H:i:s').'",
			 lastact_user_id="'.$user->id.'",
			 lastact_info="MERGE"';
	$db->setQuery('update #__tny_kapcsolatok set '.$sql.' where kapcs_id='.$db->quote($id));
	$result = $db->query();	
	
	// naplózás
	$db->setQuery('insert into #__tny_naplo
	select *
	from #__tny_kapcsolatok
	where kapcs_id='.$db->quote($id));
	$db->query();
	
	// 2016.12.09 acymailing_subscriber name szinkronizálás
	if (JRequest::getVar('email') != '') {
	  if (JRequest::getVar('nev2') != '')
		 $snev =  trim(JRequest::getVar('nev1')).' '.trim(JRequest::getVar('nev2')).' '.trim(JRequest::getVar('nev3'));
	  else
		 $snev =  trim(JRequest::getVar('nev1')).' '.trim(JRequest::getVar('nev3'));
	  $db->setQuery('update #__acymailing_subscriber
	  set name = '.$db->quote(trim($snev)).'
	  where email = '.$db->quote(JRequest::getVar('email')));
	  $db->query();
	 }
	
	

	return $result;
  }
  
  /**
    * egy rekord törlése
	* @param integer
	*/
  public function delete($id) {
	$session = JFactory::getSession();
    $userTerhats = $session->get('userTerhats');
	$user = JFactory::getUser();  
	$db = JFactory::getDBO();

	// orig rekord elérése
	$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($id));
	$orig = $db->loadObject();

    // orig rekord területi hatáskörben ?
    $jo = false;
    foreach ($userTerhats as $userTerhat) {
            if ($userTerhat->terszerv_id == $orig->terszerv_id) $jo = true;
    }

	if (!$jo) {
        $this->setError(JText::_('COM_TAGNYILVANTARTAS_NO_TERHAT'));
        return false;  
	}
	
	// naplózás
	$sql = 'update #__tny_kapcsolatok set
	         lastaction="DELETE",
	         lastact_time="'.date('Y-m-d H:i:s').'",
			 lastact_user_id="'.$user->id.'",
			 lastact_info="MERGE"
			 where kapcs_id='.$db->quote($id);
	$db->setQuery($sql);
	$db->query();
	$db->setQuery('insert into #__tny_naplo
	select *
	from #__tny_kapcsolatok
	where kapcs_id='.$db->quote($id));
	$db->query();
	
	// törlés
	$db->setQuery('delete from #__tny_kapcsolatok where kapcs_id='.$db->quote($id));
	return $db->query();
  }
  
  /**
    * kötelezo abstract form deklaráció, most nincs használva
	*/
  public function getForm($data = array(), $loaddata = true) {
	  return false;
  }
}
?>