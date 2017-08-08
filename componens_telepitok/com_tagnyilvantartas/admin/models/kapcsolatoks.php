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

class TagnyilvantartasModelkapcsolatoks extends JModelList
{
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
        }
		parent::__construct($config);		
	}
	
	protected function populateState($ordering = null, $direction = null) {
			parent::populateState();
			$app = JFactory::getApplication();
			$session = JFactory::getSession();
            $config = JFactory::getConfig();
			$id = $app->input->getInt('id', null);
			$this->setState('felhcsoportoklist.id', $id);			
			
			// Load the filter state.
			$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
			$this->setState('filter.search', $search);
			$onlychecked = $this->getUserStateFromRequest($this->context . '.filter.onlychecked', 'filter_onlychecked');
			$this->setState('filter.onlychecked', $onlychecked);
			
			$app = JFactory::getApplication();
			$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $config->get('list_limit'));
			$limit = $value;
			$this->setState('list.limit', $limit);
			
			// a limitstart ot speciálisan (rosszul) kezeli astate objektum
			// ezért én sessionban is tárolom
			$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
			if (($value == 0) & ($_POST['boxchecked']=='')) {
				// másik taskból jött ide, ilenkor jó lehet a sessionban tárolt limitstart
				$value = $session->get($this->context.'lapstart',0);
			}
			
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);
			$session->set($this->context.'lapstart', $limitstart);
			
			
			// a state objektum a list.ordering list.orderdirn adatot speciálisan (és nem jól) kezeli
			// ezért én 'rendezes' és 'irany' neveket használok a státusz tárolására
			
			//$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
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
		$id	.= ':'.$this->getState('kapcsolatoklist.id');
						return parent::getStoreId($id);
	}	
	
	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 * session->userTerhats területi hatáskört is kezelni!
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery()	{
		$session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
        $userCsoport = $session->get('userCsoport');
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		$query->select('a.*, k.szoveg, t.nev as tnev');
		$query->from('#__tny_kapcsolatok as a');
		$query->join('INNER','#__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id');             
		$query->join('INNER','#__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id');             

	    // get filter from JRequest or sstate
		$orderCol = $this->state->get('list.ordering', 'a.nev');
		$orderDirn = $this->state->get('list.direction', 'asc');
	   
        // onlychecked filter
		$filter_onlychecked = $this->state->get('filter.onlychecked');
        if ($filter_onlychecked==1) {
           $cf = 'a.ellenorzott=1';
        } else {
           $cf = 'a.ellenorzott >= 0'; 
        }
        $cf .= ' and a.terszerv_id in (0';
		if (is_array($userTerhats)) {
			foreach ($userTerhats as $userTerhat) {
				$cf .= ','.$userTerhat->terszerv_id;
			}
		}
        $cf .= ')';
        
        // Filter by search in title
		$filter_search = $this->state->get('filter.search');
		if (!empty($filter_search)) {
				/*
				$search = $db->quote('%' . $db->escape($filter_search, true) . '%');
				$query->where('(a.nev1 LIKE ' . $search . 
                   '  OR a.nev2 LIKE ' . $search . 
                   '  OR a.nev3 LIKE ' . $search . 
                   '  OR a.email LIKE ' . $search . 
                   '  OR a.telepules LIKE ' . $search . 
                   ' ) AND '.$cf);
				*/
				$s = $db->quote('.*'.$db->escape($filter_search, true).'.*');
				$s = str_replace(' ',' *',$s);
				$search = $db->quote('%' . $db->escape($filter_search, true) . '%');
				$query->where('((CONCAT(a.nev1," ",a.nev2," ",a.nev3) REGEXP '.$s.' ) OR '.
				              ' (a.telepules like '.$search.') OR '.
				              ' (a.email like '.$search.') OR '.
							  ' (a.telefon like '.$search.') OR '.
							  ' (a.telefon2 like '.$search.') OR '.
				              ' (a.megjegyzes like '.$search.')) AND '.$cf);
				
		} else {
            $query->where($cf);
        }

		//+ 2016.12.19 "PO" usercsoport csak párttagokat olvashat
		if ($userCsoport->kod == 'PO') $query->where('a.kategoria_id = 1');

		
		// Add the list ordering clause.
        $orderCol = JRequest::getVar('filter_order', $this->state->get('list.ordering'));
        $orderDirn = JRequest::getVar('filter_order_Dir', $this->state->get('list.direction'));
		if(empty($orderCol)) $orderCol = 'a.nev1';
		if(empty($orderDirn)) $orderDirn = 'asc'; 
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

        // save filter and order to state and session
		$this->state->set('list.ordering', $orderCol);
		$this->state->set('list.direction', $orderDirn);
		$this->state->set('filter.search', $filter_search);
		$this->state->set('filter.onlychecked', $filter_onlychecked);
		
		//DBG echo $query;
		
		return $query;
	}	
	
	public function getItems() {
		$result = parent::getItems();
		$db = JFactory::getDBO();
		for ($i= 0; $i < count($result); $i++) {
			$res1 = $result[$i];
			$db->setQuery('select count(*) as cc
			from #__tny_kommentek
			where kapcs_id = "'.$res1->kapcs_id.'"');
			$res2 = $db->loadObject();
			if ($res2) $result[$i]->komment_db = $res2->cc;
		}
		return $result;
	}
}