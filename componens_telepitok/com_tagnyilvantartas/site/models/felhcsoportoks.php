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
class TagnyilvantartasModelfelhcsoportoks extends JModelList
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
	          	          'fcsop_id', 'a.fcsop_id',
	          	          'nev', 'a.nev',
	          	          'kod', 'a.kod',
	          	          'nev', 'a.nev',
	          	          'jog_felhasznalok', 'a.jog_felhasznalok',
	          	          'jog_terszerv', 'a.jog_terszerv',
	          	          'jog_kategoriak', 'a.jog_kategoriak',
	          	          'jog_cimkek', 'a.jog_cimkek',
	          	          'jog_csoportos', 'a.jog_csoportos',
	          	          'jog_hirlevel', 'a.jog_hirlevel',
	          	          'jog_csv', 'a.jog_csv',
	          	          'jog_email', 'a.jog_email',
	          	          'jog_nev', 'a.jog_nev',
	          	          'jog_telefonszam', 'a.jog_telefonszam',
	          	          'jog_lakcim', 'a.jog_lakcim',
	          	          'jog_tarthely', 'a.jog_tarthely',
	          	          'jog_oevk', 'a.jog_oevk',
	          	          'jog_szev', 'a.jog_szev',
	          	          'jog_kapcsolat', 'a.jog_kapcsolat',
	          	          'jog_kapcskat', 'a.jog_kapcskat',
	          	          'jog_kapcster', 'a.jog_kapcster',
	          	          'jog_kapcscimkek', 'a.jog_kapcscimkek',
	          	          'jog_kapcshirlevel', 'a.jog_kapcshirlevel',
	          	          'jog_ellenorzott', 'a.jog_ellenorzott',
	          	          'fcsop_id', 'a.fcsop_id',
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
		parent::populateState('a.nev', 'asc');
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
		
		$query->from('#__tny_felhcsoportok AS a');

	
   
		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$query->where('LOWER(a.name) LIKE ' . $this->_db->Quote('%' . $search . '%'));		
		}


		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.nev');
		$orderDirn = $this->state->get('list.direction', 'asc');
		
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
 