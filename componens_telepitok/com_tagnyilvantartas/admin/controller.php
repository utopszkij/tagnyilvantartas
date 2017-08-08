<?php
/**
 * @version		$Id:controller.php 1 2015-05-30Z  $
 * @author	   	
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 * @copyright  	Copyright (C) 2015, . All rights reserved.
 * @license 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Tagnyilvantartas Standard Controller
 *
 * @package Tagnyilvantartas   
 * @subpackage Controllers
 */
 class TagnyilvantartasController extends JControllerLegacy {

 public function display($cachable = false, $urlparams = false)
	{
        $input = JFactory::getApplication()->input;
		$view   = $input->get('view', 'kapcsolatoks');
		$layout = $input->get('layout', 'default');
		$id     = $input->get('id');
 
        parent::display();
 		return;
	}
}// class
  
?>