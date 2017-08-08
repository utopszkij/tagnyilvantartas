 <?php
/**
* @version		$Id:view.html.php 1 2015-05-30 06:28:16Z  $
* @package		
* @subpackage 	Views
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license #
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
 
class TagnyilvantartasViewAlap  extends JViewLegacy {
	public $form;
	public $item;
	public $items;
	public $state;
	public $title;
	public $buttons;
	public $message;
	public $total;
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null) {
		JFactory::getApplication()->input->set('hidemainmenu', true);
		if ($this->message->txt != '') {
			echo '<div class="'.$this->message->class.'">'.$this->message->txt.'</div>
			';			
		}
		parent::display($tpl);	
	}	
}
?>