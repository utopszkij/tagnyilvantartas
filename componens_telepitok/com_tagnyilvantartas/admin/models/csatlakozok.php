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

class TagnyilvantartasModelcsatlakozok extends JModelList
{
	public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                            'nev123', 'a.nev123',
                            'hirlevel', 'h.subject',
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
			$this->setState('csatlakozok.id', $id);			
			
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
		$id	.= ':'.$this->getState('csatlakozok.id');
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
		$query->select('distinct a.kapcs_id, replace(concat(a.nev1," ",a.nev2," ",a.nev3),"  "," ") nev123, 
					   t.nev,
					   k.szoveg,
					   h.subject,
					   hcs.hirlevel_csatlakozas');
		$query->from('#__tny_kapcsolatok AS a');
		$query->join('INNER','#__tny_kategoriak AS k ON k.kategoria_id = a.kategoria_id');             
		$query->join('INNER','#__tny_teruletiszervezetek AS t ON t.terszerv_id = a.terszerv_id');             
		$query->join('INNER','#__tny_hirlevel_csatlakozas AS hcs ON hcs.kapcs_id = a.kapcs_id');             
		
		$query->join('INNER','#__acymailing_subscriber AS s ON s.email = a.email');             
		$query->join('INNER','#__acymailing_urlclick AS uc ON uc.subid = s.subid and DATE_FORMAT(FROM_UNIXTIME(`uc`.`date`),"%Y-%m-%d") = hcs.hirlevel_csatlakozas');             
		$query->join('INNER','#__acymailing_mail AS h ON h.mailid = uc.mailid');             
		
		
		$query->where('a.kapcs_id > 0');
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))	{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(replace("  "," ",concat(a.nev1," ",a.nev2," ",a.nev3)) LIKE ' . $search . ' or h.subject LIKE '.$search.')');
		}
				
		// Add the list ordering clause.
		// $orderCol = $this->state->get('list.ordering', 'a.szoveg');
		// $orderDirn = $this->state->get('list.direction', 'asc');
        
		$orderCol = JRequest::getVar('filter_order', 'hcs.hirlevel_csatlakozas');
        $orderDirn = JRequest::getVar('filter_order_Dir','desc');
		if(empty($orderCol)) $orderCol = 'hcs.hirlevel_csatlakozas';
		if(empty($orderDirn)) $orderDirn = 'desc'; 
		JRequest::setVar('filter_order',$orderCol);
		JRequest::setVar('filter_order_Dir',$orderDirn);
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
		//DBG echo $query;
		
		return $query;
	}	
}