 <?php
/**
* @version		$Id:view.html.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Views
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license      GNU/GPL
* telesfonszám popup képernyő
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class TagnyilvantartasViewTelpopup extends JViewLegacy {
	protected $item;
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null) 
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		parent::display($tpl);	
	}	
}
?>