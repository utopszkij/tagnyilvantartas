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

 
class TagnyilvantartasViewdoszures  extends JViewLegacy {


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

		TagnyilvantartasHelper::addSubmenu('kapcsolatoks');

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
        $task = JRequest::getVar('task');
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		JToolBarHelper::title( JText::_( 'Kapcsolatok' ), 'generic.png' );
		
		//DBG echo '<br><br><br>doszures wiew.html task='.$task.' funkcio='.JRequest::getVar('funkcio').'<br />';

        if ($task == 'szures') {
            JToolbarHelper::custom('doszures.start','','','Szűrés start',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'groupdel1') {
			JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
            JToolbarHelper::custom('doszures.groupdel2','','','Csoportos törlés végrehajtásáa',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'groupedit') {
			JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
            JToolbarHelper::custom('doszures.start','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'export') {
			JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
            JToolbarHelper::custom('doszures.export','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'start') {
            if (JRequest::getVar('funkcio','szures')=='szures') {
              //2016.05.25. JToolbarHelper::custom('doszures.show','','','Megnéz',True);
			  if ($userCsoport->jog_nev == 'RW') {	
                JToolBarHelper::editList('doszures.edit');
                JToolbarHelper::deleteList('COM_TAGNYILVANTARTAS_SURE_DELETE', 'doszures.delete', 'JTOOLBAR_DELETE');
			  }	
              JToolbarHelper::custom('kommentek.browser','','','Kommentek');
			  JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
              JToolbarHelper::custom('kapcsolatoks.szures','','','Új szűrés',false);
              JToolbarHelper::custom('kapcsolatoks.megsem','','','Szürés kikapcsolása',false);
            } else if (JRequest::getVar('funkcio')=='groupedit') {
		      if ($userCsoport->jog_csoportos == 1) {	
			    JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
                JToolbarHelper::custom('doszures.groupedit2','','','Tovább a csoportos módosításhoz',false);
                JToolbarHelper::custom('doszures.groupdel1','','','Tovább a csoportos törléshez',false);
              }            
			  JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
            } else if (JRequest::getVar('funkcio')=='export') {
			  JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
		      if ($userCsoport->jog_csv == 1) {	
			    JToolbarHelper::custom('doszures.export','','','Tovább a CSV exporthoz',false);
              }
			  JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
            } else if (JRequest::getVar('funkcio')=='hirlevel') {
			    JToolbarHelper::custom('kapcsolatoks.tovabb','','','Vissza a szűréshez',false);
                JToolbarHelper::custom('doszures.hirlevelselect','','','Tovább a hirlevél kiválasztáshoz',false);
		    }
        }
		
		JToolBarHelper::preferences('com_tagnyilvantartas', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
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
		 	          'a.lastaction' => JText::_('Lastaction'),
	     	          'a.lastact_info' => JText::_('Lastact_info'),
	     	          'a.kapcs_id' => JText::_('Kapcs_id'),
	     		);
	}	
}
?>
