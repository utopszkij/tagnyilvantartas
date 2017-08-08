 <?php
/**
 * @version 1.00
 * @package    joomla
 * @subpackage tagnyilvantartas
 * @author	   Fogler Tibor  tibor.fogler@gmail.com	
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
 
class tagnyilvantartasViewKommentek extends JViewLegacy {
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