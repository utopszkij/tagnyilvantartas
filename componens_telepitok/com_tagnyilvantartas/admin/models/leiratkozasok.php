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

class TagnyilvantartasModelleiratkozasok extends JModelList
{
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                            'szoveg', 'a.szoveg',
                            'cimke_id', 'a.cimke_id',
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
			$this->setState('leiratkozasok.id', $id);			
			
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
		$id	.= ':'.$this->getState('leiratkozasok.id');
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
		$query->select('DATE_FORMAT(FROM_UNIXTIME(a.`date`),"%Y-%m-%d %h:%i:%s") as `date`, 
		                s.name, 
						s.email, 
						m.subject, 
						a.`data`, 
						IF(k1.kapcs_id IS NULL,k2.kapcs_id, k1.kapcs_id) kapcs_id');
		$query->from('#__acymailing_history AS a');
		$query->leftjoin('#__acymailing_subscriber AS s ON s.subid = a.subid');
		$query->leftjoin('#__acymailing_mail AS m ON m.mailid = a.mailid');
		$query->leftjoin('#__tny_kapcsolatok AS k1 ON k1.email = s.email');
		$query->leftjoin('#__tny_kapcsolatok AS k2 ON k2.email2 = s.email');
		$query->where('a.action = "unsubscribed"');
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))	{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(s.name LIKE ' . $search . ' or s.email LIKE '.$search.')');
		}
				
		// Add the list ordering clause.
		// $orderCol = $this->state->get('list.ordering', 'a.szoveg');
		// $orderDirn = $this->state->get('list.direction', 'asc');
        
		$orderCol = JRequest::getVar('filter_order', 'a.date');
        $orderDirn = JRequest::getVar('filter_order_Dir','desc');
		if(empty($orderCol)) $orderCol = 'a.date';
		if(empty($orderDirn)) $orderDirn = 'desc'; 
		JRequest::setVar('filter_order',$orderCol);
		JRequest::setVar('filter_order_Dir',$orderDirn);
		
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
		// echo $query;
		
		return $query;
	}	
}