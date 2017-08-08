 <?php
/**
* @version		$Id$ $Revision$ $Date$ $Author$ $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, .
* @license #
*/

// 

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
/**
 * Methods supporting a list of contact records.
 *
 * @package     Joomla.Site
 * @subpackage  Tagnyilvantartas
 */
class TagnyilvantartasModelkapcsolatoks extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
	          	          'kapcs_id', 'a.kapcs_id',
	          	          'lastaction', 'a.lastaction',
	          	          'email', 'a.email',
	          	          'nev1', 'a.nev1',
	          	          'nev2', 'a.nev2',
	          	          'nev3', 'a.nev3',
	          	          'titulus', 'a.titulus',
	          	          'nem', 'a.nem',
	          	          'email2', 'a.email2',
	          	          'telefon', 'a.telefon',
	          	          'irsz', 'a.irsz',
	          	          'telepules', 'a.telepules',
	          	          'kerulet', 'a.kerulet',
	          	          'utca', 'a.utca',
	          	          'kjelleg', 'a.kjelleg',
	          	          'hazszam', 'a.hazszam',
	          	          'cimkieg', 'a.cimkieg',
	          	          'tirsz', 'a.tirsz',
	          	          'ttelepules', 'a.ttelepules',
	          	          'tkerulet', 'a.tkerulet',
	          	          'tutca', 'a.tutca',
	          	          'tkjelleg', 'a.tkjelleg',
	          	          'thazszam', 'a.thazszam',
	          	          'tcimkieg', 'a.tcimkieg',
	          	          'oevk', 'a.oevk',
	          	          'szev', 'a.szev',
	          	          'kapcsnev', 'a.kapcsnev',
	          	          'kapcsid', 'a.kapcsid',
	          	          'kapcsdatum', 'a.kapcsdatum',
	          	          'kategoria_id', 'a.kategoria_id',
	          	          'terszerv_id', 'a.terszerv_id',
	          	          'cimkek', 'a.cimkek',
	          	          'belsoemail', 'a.belsoemail',
	          	          'hirlevel', 'a.hirlevel',
	          	          'ellenorzott', 'a.ellenorzott',
	          	          'zarol_user_id', 'a.zarol_user_id',
	          	          'zarol_time', 'a.zarol_time',
	          	          'lastaction', 'a.lastaction',
	          	          'lastact_user_id', 'a.lastact_user_id',
	          	          'lastact_time', 'a.lastact_time',
	          	          'lastact_info', 'a.lastact_info',
	          	          'kapcs_id', 'a.kapcs_id',
	          			);

			$app = JFactory::getApplication();

		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.lastaction', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 *
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		$select_fields = $this->getState('list.select', 'a.*'); 
		
		// Select the required fields from the table.
		$query->select( $select_fields);
		
		$query->from('#__tny_kapcsolatok AS a');

	
   
		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$query->where('LOWER(a.name) LIKE ' . $this->_db->Quote('%' . $search . '%'));		
		}


		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.lastaction');
		$orderDirn = $this->state->get('list.direction', 'asc');
		
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
 