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

 
class TagnyilvantartasViewstatisztika  extends JViewLegacy {
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null) {
		$this->Items = $this->get('Items');
		if(!version_compare(JVERSION,'3','<')){
			$this->sidebar = JHtmlSidebar::render();
		}
		if(version_compare(JVERSION,'3','<')){
			$tpl = "25";
		}
		parent::display($tpl);
	}

}
?>
