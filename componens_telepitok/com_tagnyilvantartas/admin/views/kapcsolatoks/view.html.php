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

 
class TagnyilvantartasViewkapcsolatoks  extends JViewLegacy {


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

		TagnyilvantartasHelper::addSubmenu('kapcsolatoks');

		$this->addToolbar();
		if(!version_compare(JVERSION,'3','<')){
			$this->sidebar = JHtmlSidebar::render();
		}
		
		if(version_compare(JVERSION,'3','<')){
			$tpl = "25";
		}
		
		parent::display($tpl);

		$this->addTemplatePath(JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/views/kapcsolatoks/tmpl');
		echo $this->loadTemplate('telszampopup');
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
		
		//DBG foreach ($userCsoport as $fn => $fv) echo 'userCsoport '.$fn.'='.$fv.'<br />';
		
		JToolBarHelper::title( JText::_( 'Kapcsolatok' ), 'generic.png' );
        if ($task == 'szures') {
            JToolbarHelper::custom('doszures.start','','','Szűrés start',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'groupedit') {
            JToolbarHelper::custom('doszures.start','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'export') {
            JToolbarHelper::custom('doszures.start','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'szurtexport') {
            JToolbarHelper::custom('doszures.start','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'hirlevel') {
            JToolbarHelper::custom('doszures.start','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'kampany') {
            JToolbarHelper::custom('doszures.start','','','Tovább',false);
            JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem',false);
        } else if ($task == 'import') {
			; // a form tmpl definiálja
        } else if ($task == 'doszures') {
            if (JRequest::getVar('funkcio')=='szures') {
			  if ($userCsoport->jog_nev == 'RW') {	
                JToolBarHelper::editList('kapcsolatok.edit');
                JToolbarHelper::deleteList('COM_TAGNYILVANTARTAS_SURE_DELETE', 'kapcsolatoks.delete', 'JTOOLBAR_DELETE');
			  }
              JToolbarHelper::custom('kommentek.browser','','','Kommentek');
              JToolbarHelper::custom('kapcsolatoks.megsem','','','Szürés kikapcsolása');
            } else if (JRequest::getVar('funkcio')=='groupedit') {
              JToolbarHelper::custom('kapcsolatoks.groupedit2','','','Tovább a csoportos módosításhoz');
              JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem');
            } else if (JRequest::getVar('funkcio')=='export') {
              JToolbarHelper::custom('kapcsolatoks.groupedit2','','','Tovább a CSV exporthoz');
              JToolbarHelper::custom('kapcsolatoks.megsem','','','Mégsem');
            }    
        } else {
		    if ($userCsoport->jog_nev == 'RW') {	
              JToolBarHelper::addNew('kapcsolatok.add');
              JToolBarHelper::editList('kapcsolatok.edit');
              JToolbarHelper::deleteList('COM_TAGNYILVANTARTAS_SURE_DELETE', 'kapcsolatoks.delete', 'JTOOLBAR_DELETE');
			}  
            //2016.05.25. JToolbarHelper::custom('kapcsolatok.show','','','Megnéz',true);
            JToolbarHelper::custom('kapcsolatoks.szures','','','Szűrés',false);
		    if ($userCsoport->jog_csoportos == 1) {	
  			  JToolbarHelper::custom('kapcsolatoks.groupedit','','','Csoportos módosítás',false);
			}  
		    if ($userCsoport->jog_csv == 'RW') {	
              JToolbarHelper::custom('kapcsolatoks.export','','','Export CSV-be',false);
              JToolbarHelper::custom('kapcsolatoks.import','','','Import CSV-ből',false);
			}  
            JToolbarHelper::custom('kommentek.browser','','','Kommentek');
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
