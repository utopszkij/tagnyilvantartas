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
 * TagnyilvantartasTerszerv Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerTerszervmap extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'terszervmap';
		$this->view_list = 'terszervmaps';
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
	public function getModel($name = 'Terszervmap', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);
	
		return $model;
	}
	
	/**
	* rekord modositás 
	* (mivel nincs id ez nem megy, - nincs hivva)
	*/
	public function edit() {
		$ids = JRequest::getVar('cid',array());
		$id = $ids[0];
		$model = $this->getModel();
		$view = $this->getView('terszervmap','html');
		$item = $model->getItem($id);
		$form = &JForm::getInstance('terszervmap', 
                             JPATH_COMPONENT.'/models/forms/terszervmap.xml',
                             array('control' => 'jform')); 
		$form->bind($item);
		$view->set('Item',$item);
		$view->set('Form',$form);
		$view->setLayout('edit');
		$view->display();
	}
	
	/**
	* rekord(ok) törlése
	* @JRequest array cid
	*/
	public function delete() {
		$ids = JRequest::getVar('cid', array());
		$id = $ids[0];
		$model = $this->getModel();
		if ($model->delete($id)) {
			$this->setMessage('Adat törölve','info');
			$this->setRedirect(JURI::base().'index.php?option=com_tagnyilvantartas&view=terszervmaps');
			$this->redirect();
		} else {
			$this->setMessage($model->getError(),'error');
			$this->setRedirect(JURI::base().'index.php?option=com_tagnyilvantartas&view=terszervmaps');
			$this->redirect();
		}	
	}
	
}// class
?>