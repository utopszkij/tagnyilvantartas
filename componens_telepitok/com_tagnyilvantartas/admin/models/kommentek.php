<?php
/**
 * @version 1.00
 * @package    joomla
 * @subpackage tagnyilvantartas
 * @author	   Fogler Tibor  tibor.fogler@gmail.com	
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 */

defined('_JEXEC') or die('Restricted access');

if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
} 
 
class tagnyilvantartasModelKommentek  extends JModelLegacy { 
	
	protected $tableName = '#__tny_kommentek';
	protected $tableId = 'id';
	
	/**
	  * SQL query összeállítás a state alapján
	  * @param object state
	  * @returb object
	*/
	protected function getListQuery($state)	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		$query->select('a.*, u.name');
		$query->from($this->tableName.' as a');
		$query->join('INNER','#__users AS u ON u.id = a.user_id');             
		$orderCol = $state->orderCol;
		$orderDir = $state->orderDir;
		if(empty($orderCol)) $orderCol = 'a.idopont';
		if(empty($orderDir)) $orderDir = 'desc'; 
		$search = $state->filterStr;
		$kapcs_id = $state->filterKapcs_id;
		$whereStr = 'a.kapcs_id = '.$db->quote($kapcs_id);
		if ($search != '') {
		  $search = $db->quote('%'.$search.'%');	
		  $queryStr .= ' and (u.name LIKE ' .$search.'
  		    OR a.kommentszoveg LIKE '.$search.')'; 
		}
		$query->where($whereStr);
		$query->order($db->escape($orderCol . ' ' . $orderDir));
		//DBG echo $query;
		return $query;
	}	

	/**
	  * rekord sorozat olvasása state alapján
	  * @param object state
	  * @return array of record object
	*/  
	public function getItems($state) {
		$db = JFactory::getDBO();
		$query = $this->getListQuery($state);
		$db->setQuery($query, $state->limitstart, $state->limit);
		$result = $db->loadObjectList();
		if ($db->setErrorNum() > 0) {
			$result = false;
			$this->setError($db->getErrorNum().' '.$db->getErrorMsg());		
		}
		return $result;
	}
	
	/**
	  * egy rekord olvasása id alapján, id='' esetén felvitelhez inicializált rekord
	  * @param string id
	  * @return record object
	*/  
	public function getItem($id) {
		$db = JFactory::getDBO();
		if ($id == '') {
			$result = new stdClass();
			// felvitelhez inicializálás
			$result->id = 0;
			$result->idopont = "'.date('Y-m-d H:i:s').'";
			$result->user_id = JFactory::getUser()->id;
			$result->kommentszoveg = '';	
		} else {
		  $db->setQuery('select * 
		  from '.$this->tableName.'
		  where '.$this->tableId.'='.$db->quote($id));
		  $result = $db->loadObject();
		  if ($db->setErrorNum() > 0) {
			$result = false;
			$this->setError($db->getErrorNum().' '.$db->getErrorMsg());		
		 }
		} 
		return $result;
	}
	
	/**
	  * összes rekordszám a state alapján (limit és limitstart figyelmen kivül hagyásával)
	  * @param object state
	  * @return integer
	*/  
	public function getTotal($state) {
		$db = JFactory::getDBO();
		$query = $this->getListQuery($state);
		$query->clear('select');
		$query->select('count(*) as cc');
		$db->setQuery($query);
		$result = $db->loadObject();
		if ($db->setErrorNum() > 0) {
			$result = 0;
			$this->setError($db->getErrorNum().' '.$db->getErrorMsg());		
		}
		return $result->cc;
	}
	
	/**
	  * ellenörzés (tárolható ?)
	  * @param record object
	  * @return bool 
	*/
	public function check($item) {
		$session = JFactory::getSession();
		$errorFields = array();
		$result = true;
		// hiba esetén $this->setError() és result false
		// valamint $errorFields tömbbe beirni hibás mező neveket
		if ($item->kommentszoveg == '') {
			$this->setError(JText::_('KOMMENT_IS_EMPTY'));
			$errorFields[] = 'kommentszoveg';
			$result = false;
		}
		$session->set('errorFields',$errorFields);
		return $result;
	}
	
	/**
	  * ellenörzés (törölhető?)
	  * @param string id
	  * @return bool 
	*/
	public function candelete($id) {
		// hiba esetén $this->setError() és result false
		return true;
	}
	
	/**
	  * $data array -> $item
	*/  
	public function bind($data) {
	  $result = new stdClass();
	  foreach ($data as $fn => $fv) {
		  $result->$fn = $fv;
	  }
      return $result;	  
	}
	
	public function save($item) {
		
		// ismételt küldés szürése
		$session = JFactory::getSession();
		$lastItem = $session->get('lastKommentItem','');
		if ($lastKommentItem == JSON_encode($item)) {
			// ugyanaz jön mint az utolsó feltehetőleg browser refresh
			$this->setError('ismételt adat küldés');	
			return false;
		}
		$session->set('lastKommentItem', JSON_encode($item));
		
		$item->idopont = date('Y-m-d H:i:s');
		$item->user_id = JFactory::getUser()->id;
		$db = JFactory::getDBO();
	    $iName = $this->tableId;
		// insert vagy update?
		if ($item->$iName != '') 
		  $w = $this->getItem($item->$iName);
		else 
		  $w = false;  // ez jelzi, hogy insert kell	
		if ($w) {
			// update
			$query = '';
			foreach ($item as $fn => $fv) {
				if ($query != '') $query .= ',';
				$query .= $fn.'='.$db->quote($fv);
			}
			$query = 'update '.$this->tableName.'
			set '.$query.' 
			where '.$this->tableId.'='.$db->quote($item->$iName);
		} else {
			// insert
			$s1 = '';
			$s2 = '';
			foreach ($item as $fn => $fv) {
				if ($s1 != '') $s1 .= ',';
				$s1 .= $fn;
				if ($s2 != '') $s2 .= ',';
				$s2 .= $db->quote($fv);
			}
			$query = 'insert into '.$this->tableName.' ('.$s1.') values ('.$s2.')';
		}
		$db->setQuery($query);
		$result = $db->query();
		if ($db->getErrorNum() > 0)
			$this->setError($db->getErrorNum().' '.$db->getErrorMsg());	
		//DBG echo $db->getQuery().'<br>';
		return $result;
	}
	
	public function delete($id) {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		//$db->setQuery('delete from '.$this->tableName.'
		//where '.$this->tableId.'='.$db->quote($id));
		$db->setQuery('update '.$this->tableName.'
		set kommentszoveg="A kommentet '.$user->name.' törölte '.date('Y.m.d H:i').'"
		where '.$this->tableId.'='.$db->quote($id));
		
		$result = $db->query();
		if ($db->getErrorNum() > 0)
			$this->setError($db->getErrorNum().' '.$db->getErrorMsg());		
		return $result;
	} 
}
?>