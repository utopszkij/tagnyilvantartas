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

class TagnyilvantartasModelextrafields extends JModelList
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
			$id = $app->input->getInt('field_id', null);
			$this->setState('extrafield.field_id', $id);			
			
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
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);		
		$query->select('a.*');
		$query->from('#__tny_extrafields as a');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.field_id');
		$orderDirn = $this->state->get('list.direction', 'asc');
		if(empty($orderCol)) $orderCol = 'a.field_id';
		if(empty($orderDirn)) $orderDirn = 'asc'; 
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$this->state->set('list.ordering', $orderCol);
		$this->state->set('list.direction', $orderDirn);
		//DBG echo $query;
		return $query;
	}	

	/**
      * Egy adott extrafield rekord olvasása
	  * @return record object
	  */
	public function getItem($id = 0) {
	  if ($id == 0) {
		  $result = new stdclass();
		  $result->field_id = 0;
		  $result->field_name = '';
		  $result->field_label = '';
		  $result->field_type = 'string';
	  }	else {
		  $db = JFactory::getDBO();
		  $db->setQuery('select a.*
			FROM #__tny_extrafields as a
			WHERE a.field_id = '.$db->quote($id)
		  );
		  //DBG echo $db->getQuery().'<br />';
		  $result = $db->loadObject();	
	  }
	  return $result;
	}

	public function save($item) {
		$db = JFactory::getDBO();
		if ($item->field_type=='string') $colType = 'varchar(128)';
		if ($item->field_type=='integer') $colType = 'int(11)';
		if ($item->field_type=='phone') $colType = 'varchar(40)';
		if ($item->field_type=='email') $colType = 'varchar(40)';

		if ($item->field_type=='string') $defValue = '""';
		if ($item->field_type=='integer') $defvalue = '0';
		if ($item->field_type=='phone') $defValue = '""';
		if ($item->field_type=='email') $defValue = '""';

		if ($item->field_id == 0) {
			// új extrafield
			$result = true;		
				
			// felvitel az #__tny_extrafields táblába
			$db->setQuery('INSERT INTO #__tny_extrafields 
			(`field_id`, 
			`field_name`, 
			`field_label`, 
			`field_type`
			)
			VALUES
			(0, 
			'.$db->quote($item->field_name).', 
			'.$db->quote($item->field_label).', 
			'.$db->quote($item->field_type).' 
			);
			');
			if ($db->query() == false) {
				$his->setError($db->getErrorMsg());
				$result = false;
			} else {
				$result = true;
			}
			$colName = $item->field_name;
			
			// alter #__tny_kapcsolatok tábla
			if ($result) {
				$db->setQuery('alter table #__tny_kapcsolatok add '.$colName.' '.$colType.' not null default '.$defValue);
				if ($db->query() == false) {
					$his->setError($db->getErrorMsg());
					$result = false;
				} else {
					$result = true;
				}
			}
			
			// alter #__tny_naplok tábla
			if ($result) {
				$db->setQuery('alter table #__tny_naplo add '.$colName.' '.$colType.' not null default '.$defValue);
				if ($db->query() == false) {
					$his->setError($db->getErrorMsg());
					$result = false;
				} else {
					$result = true;
				}
			}
			
			// forms/kapcsolatok.xml bovítése
			if ($result) {
				$lines = file(JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml');
				for ($i=0; $i < count($lines); $i++) {
					if (trim($lines[$i]) == '</fields>') {
						$lines[$i] = '		<field
			id="'.$item->field_name.'"
			name="'.$item->field_name.'"
			type="text"
			required="false"
			label="'.$item->field_label.'"
			description=""
			class="inputbox"
			size="40"/>						
'.$lines[$i];
					}
				}
				$fp = fopen(JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml','w+');
				fwrite($fp, implode('',$lines));
				fclose($fp);
				
			}
		} else {
			// módosítás
			$db->setQuery('update #__tny_extrafields
			set field_label = '.$db->quote($item->field_label).',
			    field_name = '.$db->quote($item->field_name).',
			    field_type = '.$db->quote($item->field_type).'
			where field_id = '.$db->quote($item->field_id));
			if ($db->query() == false) {
				$his->setError($db->getErrorMsg());
				$result = false;
			} else {
				$result = true;
			}
		}
		return $result;
	}	
	
	public function delete($id) {
		$db = JFactory::getDBO();
		$db->setQuery('select field_name from #__tny_extrafields where field_id='.$db->quote($id));
		$res = $db->loadObject();
		if ($res) 
			$colName = $res->field_name;
		else
			$colName = '';
		$db->setQuery('delete from #__tny_extrafields where field_id='.$db->quote($id));
		if ($db->query() == false) {
			$his->setError($db->getErrorMsg());
			$result = false;
		} else {
			$result = true;
		}
		if ($result) {
			$db->setQuery('alter table #__tny_kapcsolatok drop '.$colName);
			if ($db->query() == false) {
				$his->setError($db->getErrorMsg());
				$result = false;
			} else {
				$result = true;
			}
			$db->setQuery('alter table #__tny_naplo drop '.$colName);
			if ($db->query() == false) {
				$his->setError($db->getErrorMsg());
				$result = false;
			} else {
				$result = true;
			}
		}
		return $result;
	
	}

	public function check($item) {
		$result = true;
		$errorMsg = '';
		$db = JFactory::getDBO();
		if ($item->field_name == '') {
			$errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_EMPTY_NAME').'<br />';
			$result = false;
		}
		if ($item->field_label == '') {
			$errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_EMPTY_LABEL').'<br />';
			$result = false;
		}
		if ($item->field_type == '') {
			$errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_EMPTY_TYPE').'<br />';
			$result = false;
		}
		$db->setQuery('select * from #__tny_extrafields where field_name='.$db->quote($item->field_name));
		$res = $db->loadObject();
		if ($res) {
			if ($res->field_id != $item->field_id) {
			  $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_DOBLE_NAME').'<br />';
			  $result = false;
			}
		}
		$this->setError($errorMsg);
		return $result;
	}
	
	public function candelete($id) {
		return true;
	}
	
	public function bind($source) {
		$item = $this->getItem(0);
		foreach ($item as $fn => $fv) {
			if (isset($source[$fn]))
			  $item->$fn = $source[$fn];
		}
		return $item;
	}
	
}