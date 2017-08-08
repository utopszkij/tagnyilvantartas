<?php
/**
 * @version V1.00
 * @package    joomla
 * @subpackage tagnyilvantartas
 * @author	   Fogler Tibor   tibor.fogler@gmail.com	
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 * MVC editor (M V C H .php, templates.php, form.xml, hu-HU.comName.ini )
 */

defined('_JEXEC') or die('Restricted access');

if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
} 
 
class tagnyilvantartasModelMvceditor  extends JModelLegacy { 
	
	protected $tableName = '#__{tableName}';
	protected $tableId = 'id';
	
	/**
	  * SQL query összeállítás a state alapján
	  * @param object state
	  * @returb object
	*/
	protected function getListQuery($state)	{
		// ================== példa jelentősen átirandó ==============================
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		

		$query->select('a.*, k.szoveg, t.nev as tnev');
		$query->from($this->tableName.' as a');
		$query->join('INNER','#__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id');             
		$query->join('INNER','#__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id');             
		$orderCol = $state->orderCol;
		$orderDir = $state->orderDir;
		if(empty($orderCol)) $orderCol = 'a.nev1';
		if(empty($orderDir)) $orderDir = 'asc'; 
		$search = $state->filterStr;
		if ($search != '') {
		  $search = $db->quote('%'.$search.'%');	
		  $query->where('a.nev1 LIKE ' . $search . 
                   '  OR a.nev2 LIKE ' . $search . 
                   '  OR a.nev3 LIKE ' . $search . 
                   '  OR a.email LIKE ' . $search . 
                   '  OR a.telepules LIKE ' . $search);
		}
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
		// hiba esetén $this->setError() és result false
		// valamint $errorFields tömbbe beirni hibás mező neveket
		
		$session->set('errorFields',$errorFields);
		return true;
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
		$db = JFactory::getDBO();
		// insert vagy update?
		$iName = $this->tableId;
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
			where '.$this->tableId.'='.$db->quote($item->$iName).' limit 1';
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
		return $result;
	}
	
	public function delete($id) {
		$db = JFactory::getDBO();
		$db->setQuery('delete from '.$this->tableName.'
		where '.$this->tableId.'='.$db->quote($id).' limit 1');
		
		$result = $db->query();
		if ($db->getErrorNum() > 0)
			$this->setError($db->getErrorNum().' '.$db->getErrorMsg());		
		return $result;
	} 
}
?>