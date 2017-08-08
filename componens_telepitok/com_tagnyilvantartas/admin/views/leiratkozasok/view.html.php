<?php
/**
* @version		$Id:cimkek.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Views
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license #
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class TagnyilvantartasViewleiratkozasok  extends JViewLegacy {


	protected $items;

	protected $pagination;

	protected $state;
	
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null)
	{
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		TagnyilvantartasHelper::addSubmenu('leiratkozasok');

		// $this->addToolbar();
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
	protected function addToolbar()
	{
		
		$canDo = TagnyilvantartasHelper::getActions();
		$user = JFactory::getUser();
		JToolBarHelper::title( JText::_( 'Cimkek' ), 'generic.png' );
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('cimkek.add');
		}	
		
		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('cimkek.edit');
		}
		
				
				

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('COM_TAGNYILVANTARTAS_SURE_DELETE', 'cimkeks.delete', 'JACTION_DELETE');
		}
				
		
		JToolBarHelper::preferences('com_tagnyilvantartas', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_tagnyilvantartas&view=cimkeks');
		}
				
					
	}	
	

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
		 	          'a.szoveg' => JText::_('Szoveg'),
	     	          'a.cimke_id' => JText::_('Cimke_id'),
	     		);
	}	
}
?>
