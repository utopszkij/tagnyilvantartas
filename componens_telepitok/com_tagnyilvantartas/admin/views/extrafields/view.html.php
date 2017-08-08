<?php
/**
* @version		$Id:kapcsolatok.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Views
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license #
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class TagnyilvantartasViewextrafields  extends JViewLegacy {
	protected $items;
	protected $item;
	protected $pagination;
	protected $state;
	
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null)
	{
		
		$this->items		= $this->get('Items');
        $this->item		    = $this->get('Item');		
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->form			= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//TagnyilvantartasHelper::addSubmenu('kapcsolatoks');

		//$this->addToolbar();
		if(!version_compare(JVERSION,'3','<')){
			$this->sidebar = JHtmlSidebar::render();
		}
		
		if(version_compare(JVERSION,'3','<')){
			$tpl = "25";
		}
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar() {
		/*
		$canDo = TagnyilvantartasHelper::getActions();
		$user = JFactory::getUser();
        $task = JRequest::getVar('task');
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		
		//DBG foreach ($userCsoport as $fn => $fv) echo 'userCsoport '.$fn.'='.$fv.'<br />';
		
		JToolBarHelper::title( JText::_( 'COM_TAGNYILVANTARTAS_EXTRA_FIELDS' ), 'generic.png' );
        JToolbarHelper::custom('naplos.purge','','','Régiek törlése',false);
        JToolbarHelper::custom('naplos.show','','','Megnéz',true);
		
		JToolBarHelper::preferences('com_tagnyilvantartas', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_tagnyilvantartas&view=naplos');
		}
		*/
	}	
	

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
		 	          'a.field_id' => JText::_('ID'),
	     	          'a.field_name' => JText::_('FIELD_NAME'),
	     	          'a.field_label' => JText::_('FIELD_LABEL'),
	     	          'a.field_type' => JText::_('FIELD_TYPE')
	     		);
	}	
}
?>
