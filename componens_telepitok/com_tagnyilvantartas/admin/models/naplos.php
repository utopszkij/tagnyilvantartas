 <?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_tagnyilvantartas/tables');

class TagnyilvantartasModelnaplos extends JModelList
{
	public function __construct($config = array())
	{
		parent::__construct($config);		
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
			parent::populateState();
			$app = JFactory::getApplication();
			$session = JFactory::getSession();
            $config = JFactory::getConfig();
			$id = $app->input->getInt('id', null);
			$this->setState('naplolist.id', $id);			
			
			// Load the filter state.
			$filter_date1 = $this->getUserStateFromRequest($this->context . '.filter.date1', 'filter_date1');
			$this->setState('filter.date1', $filter_date1);

			$filter_date2 = $this->getUserStateFromRequest($this->context . '.filter.date2', 'filter_date2');
			$this->setState('filter.date2', $filter_date2);

			$filter_user_id = $this->getUserStateFromRequest($this->context . '.filter.user_id', 'filter_user_id');
			$this->setState('filter.user_id', $filter_user_id);

			$filter_kapcs_id = $this->getUserStateFromRequest($this->context . '.filter.kapcs_id', 'filter_kapcs_id');
			if ($filter_kapcs_id == 0) $filter_kapcs_id = '';
			$this->setState('filter.kapcs_id', $filter_kapcs_id);
			
			$filter_lastaction = $this->getUserStateFromRequest($this->context . '.filter.lastaction', 'filter_lastaction');
			$this->setState('filter.lastaction', $filter_lastaction);

			$filter_nev = $this->getUserStateFromRequest($this->context . '.filter.nev', 'filter_nev');
			$this->setState('filter.nev', $filter_nev);

			$app = JFactory::getApplication();
			$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit'));
			$limit = $value;
			$this->setState('list.limit', $limit);
			
			$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
			if (($value == 0) & ($_POST['boxchecked']=='')) {
				// másik taskból jött ide, ilenkor jó lehet a sessionban tárolt limitstart
				$value = $session->get($this->context.'lapstart',0);
			}
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);
			$session->set($this->context.'lapstart', $limitstart);

			$value = $this->getUserStateFromRequest($this->context.'.rendezes', 'filter_order');
			$this->setState('list.ordering', $value);			
			$this->setState('list.rendezes', $value);			
			//$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
			$value = $app->getUserStateFromRequest($this->context.'.irany', 'filter_order_Dir');
			$this->setState('list.direction', $value);
			$this->setState('list.irany', $value);
					
	}
    		
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('naplolist.id');
						return parent::getStoreId($id);
	}	
	
	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 * @session userTerhats
	 * @JRequest  szüro feltételek: filter_date1, filter_dat2, filter_user_id, filter_kapcs_id
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery() {
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
        $userCsoport = $session->get('userCsoport');
		//DBG echo 'userCsoport:'.JSON_ENCODE($userCsoport).'<br />';
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		$query->select('a.*, k.szoveg, t.nev as tnev, u.name');
		$query->from('#__tny_naplo as a');
		$query->join('LEFT OUTER','#__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id');             
		$query->join('LEFT OUTER','#__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id');             
		$query->join('LEFT OUTER','#__users AS u ON u.id = a.lastact_user_id');             
       
	    // Területi hatáskör feltétel
        $whereStr = 'a.terszerv_id in (0';
        foreach ($userTerhats as $userTerhat) {
            $whereStr .= ','.$userTerhat->terszerv_id;
        }
        $whereStr .= ')';
        
        // Filter by search in title
		$filter_date1 = $this->state->get('filter.date1');
		$filter_date2 = $this->state->get('filter.date2');
		$filter_user_id = $this->state->get('filter.user_id');
		$filter_kapcs_id = $this->state->get('filter.kapcs_id');
		$filter_lastaction = $this->state->get('filter.lastaction');
		$filter_nev = $this->state->get('filter.nev');
		
		if (($filter_date1 != '') | ($filter_date2 != '')) {
			$whereStr .= ' and a.lastact_time >= '.$db->quote($filter_date1.' 00:00').
			             ' and a.lastact_time <= '.$db->quote($filter_date2.' 23:59');
		}		
		if ($filter_user_id != '') {
		   $whereStr .=  ' and a.lastact_user_id = '.$db->quote($filter_user_id);
		}
		if ($filter_kapcs_id != '') {
		   $whereStr .=  ' and a.kapcs_id = '.$db->quote($filter_kapcs_id);
		}
		if ($filter_lastaction != '') {
		   $whereStr .=  ' and a.lastaction like '.$db->quote($filter_lastaction.'%');
		}
		if ($filter_nev != '') {
		   $s = $db->quote('.*'.$filter_nev.'.*');
		   $s = str_replace(' ',' *',$s);	
		   $whereStr .=  ' and concat(a.nev1," ",a.nev2," ",a.nev3) REGEXP '.$s;
		}
			
		//+ FT 2016-08-24  a nem "A" csoport csak a login eseményeket láthatja 
		// (korábban az egész menüpont tiltott volt a számukra)
		if ($userCsoport->kod != 'A') {
			$whereStr .= ' and lastaction like "%LOGIN"';
			//JRequest::setVar('filter_lastaction','sikeres');
		}	
		//- FT 2016-08-24  a nem "A" csoport csak a login eseményeket láthatja	
        $query->where($whereStr);

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.lastact_time');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if(empty($orderCol)) $orderCol = 'a.lastact_time';
		if(empty($orderDirn)) $orderDirn = 'desc'; 
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$this->state->set('list.ordering', $orderCol);
		$this->state->set('list.direction', $orderDirn);
		
		//DBG echo $query;
		
		return $query;
	}	

	/**
      * Egy adott napló rekord olvasása
	  * @param string naplo_id  "####,#####,####"  kapcsolat_id,lastact_time, lastact_user_id
	  * @return record object
	  */
	public function getItem($id = '') {
	  $w = explode(',',$id);
	  if (count($w) == 3) {
		  $db = JFactory::getDBO();
		  $db->setQuery('select a.*, t.nev, k.szoveg, u.name
			FROM #__tny_naplo as a
			LEFT OUTER JOIN #__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id             
			LEFT OUTER JOIN #__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id             
			LEFT OUTER JOIN #__users AS u ON u.id = a.lastact_user_id
			WHERE a.kapcs_id = '.$db->quote($w[0]).'
			  AND a.lastact_time = '.$db->quote($w[1]).'
			  AND a.lastact_user_id = '.$db->quote($w[2])	
		  );
		  //DBG echo $db->getQuery().'<br />';
	      return $db->loadObject();	
	  } else {
		  return array();
	  }
	}
	
	/**
	  *  adott változatot megelözo változat beolvasása
	  */
	public function getLastItem($kapcs_id, $lastact_time) {
	  $db = JFactory::getDBO();
	  $db->setQuery('select a.*, t.nev, k.szoveg, u.name
		FROM #__tny_naplo as a
		INNER JOIN #__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id             
		INNER JOIN #__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id             
		INNER JOIN #__users AS u ON u.id = a.lastact_user_id
		WHERE a.kapcs_id = '.$db->quote($kapcs_id).'
		  AND a.lastact_time < '.$db->quote($lastact_time).'
		ORDER BY a.lastact_time DESC 
		LIMIT 1');
	  return $db->loadObject();
	}
	
	public function purge($date) {
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
	    // Területi hatáskör feltétel
        $whereStr = 'terszerv_id in (0';
        foreach ($userTerhats as $userTerhat) {
            $whereStr .= ','.$userTerhat->terszerv_id;
        }
        $whereStr .= ')';
		$db = JFactory::getDBO();
		$db->setQuery('delete from #__tny_naplo 
		where lastact_time < '.$db->quote($date.' 00:00').' and lastaction <> "INSERT" and '.$whereStr);
		if (!$db->query())
			$result = true;
		else {
			$this->setError($db->getErrorMsg());
			$result = false;
		}
		//DBG echo $db->getQuery(); 
		return $result;
	}
}