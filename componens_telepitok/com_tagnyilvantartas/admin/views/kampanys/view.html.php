<?php
/**
* @version		$Id:kampany.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Views
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license #
*/
// 2017.07.05 jogosultság ellenörzés

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class TagnyilvantartasViewkampanys  extends JViewLegacy {


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

		TagnyilvantartasHelper::addSubmenu('kampanys');

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
	// 2017.07.05 jogosultság ellenörzés
	protected function addToolbar()
	{
		$canDo = TagnyilvantartasHelper::getActions();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		
		JToolBarHelper::title( JText::_( 'Kampany' ), 'generic.png' );
		if (($userCsoport->kod == 'A') |
			($userCsoport->kod == 'SM') |		
			($userCsoport->kod == 'CB') 		
		    ) {
			JToolBarHelper::addNew('kampany.add');
		}	
		
		if (($userCsoport->kod == 'A') |
			($userCsoport->kod == 'SM') |		
			($userCsoport->kod == 'CB') 		
		    ) {
			JToolBarHelper::editList('kampany.edit');
		}
        JToolbarHelper::custom('kapcsolatoks.kampany','','','Érintett kapcsolatok',false);

		if (($userCsoport->kod == 'A') |
			($userCsoport->kod == 'SM') |		
			($userCsoport->kod == 'CB') 		
		    ) {
			JToolbarHelper::deleteList('COM_TAGNYILVANTARTAS_SURE_DELETE', 'kampany.delete', 'JACTION_DELETE');
		}
				
		
		JToolBarHelper::preferences('com_tagnyilvantartas', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_tagnyilvantartas&view=kampanys');
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
		 	          'a.megnev' => JText::_('Megnevezés'),
	     	          'a.id' => JText::_('id'),
	     	          'a.helyszin' => JText::_('Helyszín'),
	     	          'a.idopont' => JText::_('Időpont'),
	     		);
	}	
}
?>
