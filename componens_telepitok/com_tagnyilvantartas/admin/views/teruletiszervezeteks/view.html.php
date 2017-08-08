<?php
/**
* @version		$Id:teruletiszervezetek.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Views
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license #
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class TagnyilvantartasViewteruletiszervezeteks  extends JViewLegacy {


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

		TagnyilvantartasHelper::addSubmenu('teruletiszervezeteks');

		$this->addToolbar();
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
		JToolBarHelper::title( JText::_( 'Teruletiszervezetek' ), 'generic.png' );
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('teruletiszervezetek.add');
		}	
		
		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('teruletiszervezetek.edit');
		}
		
				
				

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('COM_TAGNYILVANTARTAS_SURE_DELETE', 'teruletiszervezeteks.delete', 'JTOOLBAR_DELETE');
		}
				
		
		JToolBarHelper::preferences('com_tagnyilvantartas', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_tagnyilvantartas&view=teruletiszervezeteks');
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
		 	          'a.nev' => JText::_('Nev'),
	     	          'a.terszerv_id' => JText::_('Terszerv_id'),
	     		);
	}	
}
?>
