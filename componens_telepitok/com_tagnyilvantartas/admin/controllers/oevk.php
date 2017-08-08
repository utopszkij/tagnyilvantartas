<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');

/**
 * TagnyilvantartasOevk Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerOevk extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'oevk';
		$this->view_list = 'oevks';
		parent::__construct($config);
	}	
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Oevk', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);
	
		return $model;
	}
	
	public function delete() {
		$model = $this->getModel();
		$model->delete(JRequest::getVar('cid'));
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=oevks');
		$this->setMessage('Adat törölve');
		$this->redirect();
	}
	
}// class
?>