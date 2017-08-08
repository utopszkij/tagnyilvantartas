<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/


// 2017.01.24 "SM" felhasználó is használhatja, de csak lekérdezhet, nem módosíthat.

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');

/**
 * TagnyilvantartasFelhasznalok Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerFelhasznalok extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'felhasznalok';
		$this->view_list = 'felhasznaloks';
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
	public function getModel($name = 'Felhasznalok', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);
	
		return $model;
	}
    
    public function save() {
        //DBG echo 'controller save rutin ';
        //DBG echo '<pre>'; print_r($_POST); echo '</pre>';   
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');		
		if ($userCsoport->kod == 'SM') {
          $this->setError('Ön nem modosíthatja ezeket az adatokat.','error');
          $this->setRedirect(JURI::base().'/index.php?option=com_tagnyilvantartas&view=felhasznaloks');
          $this->redirect();
		}
        $model = $this->getModel('felhasznalok');
        if ($model->save(JRequest::get('POST'))) 
          $this->setMessage('Adatok tárolva.');
        else    
          $this->setError($model->getError());
        $this->setRedirect(JURI::base().'/index.php?option=com_tagnyilvantartas&view=felhasznaloks');
        $this->redirect();
    }
}// class
?>