<?php
/**
* @version		$Id$ $Revision$ $Date$ $Author$ $
* @package		Tagnyilvantartas
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, .
* @license 		
*/

// 

defined('_JEXEC') or die;


jimport('joomla.application.component.controlleradmin');
/**
 * Extra fields list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Tagnyilvantartas
 */
class TagnyilvantartasControllerExtrafields extends JControllerAdmin {
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  TagnyilvantartasControllerkapcsolatoks
	 * @see     JController
	 */
	public function __construct($config = array()) 	{
		$this->view_list = 'extrafields';
		parent::__construct($config);
		
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
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
	public function getModel($name = 'Extrafields', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
    
    /**
      * mégsem gombot nyomtak az edit, add, delete képernyõn
      * ugrás az extrafields böngészõ képernyõre
      */
    public function megsem() {
        $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
        $this->redirect();
    }

	/**
	  * egy adott extrafield rekord megjelenitése editálható formban
	  * @JRequest array  cid   cid[0] = extrafield_id
	  * @return void
	*/  
	public function edit() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
		  $this->setMessage(JText::_('ACCESS_DENIED'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
          $this->redirect();		  
		}
		$model = $this->getModel('extrafields');
		$view = $this->getView('extrafields','html');
		$cids = JRequest::getVar('cid');
		$item = $model->getItem($cids[0]);
		//DBG foreach ($item as $fn => $fv) echo $fn.'='.$fv.'<br />';
        $view->set('Item',$item);
		if ($item) {
		  $view->setLayout('form');
		  $view->display();
		} else {
		  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_READ_ERROR'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
          $this->redirect();		  
		}  
	}

	/**
	  * új extrafield rekord editálható formban
	  * @return void
	*/  
	public function add() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
		  $this->setMessage(JText::_('ACCESS_DENIED'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
          $this->redirect();		  
		}
		$model = $this->getModel('extrafields');
		$view = $this->getView('extrafields','html');
		$cids = JRequest::getVar('cid');
		$item = $model->getItem(0);
        //itemAccess($item, $userCsoport);	   
		//DBG foreach ($item as $fn => $fv) echo $fn.'='.$fv.'<br />';
        $view->set('Item',$item);
		if ($item) {
		  $view->setLayout('form');
		  $view->display();
		} else {
		  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_READ_ERROR'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
          $this->redirect();		  
		}  
	}
		
	/**
      * adatform tárolása
	  * JRequest form fields
	  * @return void
	*/	
	public function save() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
		  $this->setMessage(JText::_('ACCESS_DENIED'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
          $this->redirect();		  
		}
		$model = $this->getModel('extrafields');
		$view = $this->getView('extrafields','html');
		$jform = JRequest::getVar('jform');
		$item = $model->bind($jform);
		if ($model->check($item)) {
		  if ($model->save($item)) {
		    $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_SAVED'));
		    $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
            $this->redirect();		  
		  }	else {
		    $this->setMessage($model->getError());
		    $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
            $this->redirect();		  
		  }
		} else {
		  $this->setMessage($model->getError());
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
          $this->redirect();		  
		}
		return;	
	}

	public function delete() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
		  $this->setMessage(JText::_('ACCESS_DENIED'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
          $this->redirect();		  
		}
		$model = $this->getModel('extrafields');
		$view = $this->getView('extrafields','html');
		$cids = JRequest::getVar('cid');
		$id = $cids[0];
		if ($model->candelete($id)) {
			if ($model->delete($id)) {
		      $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELD_DELETED'));
		      $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
              $this->redirect();		  
			} else {
		       $this->setMessage($model->getError());
		       $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
               $this->redirect();		  
			}
		} else {
		  $this->setMessage($model->getError());
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=extrafields');
          $this->redirect();		  
		}
	}
}
