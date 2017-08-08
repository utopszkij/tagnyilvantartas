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

class TagnyilvantartasModelteruletiszervezeteks extends JModelList
{
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                            'Név', 'a.nev',
							'Tulajdonos szervezet','a.tulaj_id',
                            'terszerv_id', 'a.terszerv_id',
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
			$this->setState('teruletiszervezeteklist.id', $id);			
			
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
		$id	.= ':'.$this->getState('teruletiszervezeteklist.id');
		return parent::getStoreId($id);
	}	
	
	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object	A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery()
	{
		$session = JFactory::getSession();
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		$query->select('a.*');
		$query->from('#__tny_teruletiszervezetek as a');
	
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)){
			if (stripos($search, 'id:') === 0)	{
				$query->where('a.id = ' . (int) substr($search, 3));
			} else{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.nev LIKE ' . $search . ' )');
			}
		}
		// Add the list ordering clause.
		//$orderCol = $this->state->get('list.ordering', 'a.nev');
		//$orderDirn = $this->state->get('list.direction', 'asc');
        $orderCol = JRequest::getVar('filter_order', $session->get($this->context.'.orderCol'));
        $orderDirn = JRequest::getVar('filter_order_Dir', $session->get($this->context.'.orderDirn'));
		if(empty($orderCol)) $orderCol = 'a.nev';
		if(empty($orderDirn)) $orderDirn = 'asc'; 
		$session->set($this->context.'.orderCol',$orderCol);
		$session->set($this->context.'.orderDirn',$orderDirn);
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$this->state->set('list.ordering', $orderCol);
		$this->state->set('list.direction', $orderDirn);
		return $query;
	}
    /**
      * rekurziv function a területi szervezet fa elérése adott $tulaj -hoz
      * @return void
      * @param array of records  by reference
      * @param integer tulajdonos
      * @param integer szint
    */      
    protected function getItems2(&$result, $tulaj, $level) {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		$query->select($level.' as level, a.*');
		$query->from('#__tny_teruletiszervezetek as a');
		$query->where('a.tulaj_id = '.$tulaj);
		$query->order($db->escape('nev ASC'));
        $db->setQuery($query);
        $res = $db->loadObjectList();
        foreach ($res as $res1) {
            $result[] = $res1;
            $this->getItems2($result, $res1->terszerv_id, $level+1);
        }
    }
    /**
      * getItems search="fa" esetén fa szerkezetben, egyébként a suzokásos módon
      * @return array of records
     */
     public function getItems() {
 		$search = $this->getState('filter.search');
        if ($search == 'tree') { 
           JRequest::setVar('limit',999);
           $result = array();
           $this->getItems2($result, 0, 0);         
           return $result;
        } else {
           return parent::getItems(); 
        }   
     }     
}