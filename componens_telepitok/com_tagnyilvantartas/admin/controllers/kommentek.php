<?php
/**
 * @version 1.00
 * @package    joomla
 * @subpackage tagnyilvantartas
 * @author	   Fogler Tibor  tibor.fogler@gmail.com	
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');
  
class tagnyilvantartasControllerKommentek extends JControllerLegacy {
	/**
	  * model,browser neve és Lng prefix
	*/  
	protected $modelName = 'kommentek';
	protected $browserName = 'kommentek'; 
	protected $formName = 'kommentek'; 
	protected $lngPre = 'KOMMENTEK';

	/**
	  *  browser státusz
	*/  
	protected $state = null;
	
	/**
	  * üzenet
	*/
	protected $message = null;
	
	/**
	  * objektum generálás
	*/  
	function __construct() {
		parent::__construct();
	    // ============================== FIGYELEM Ha több szüro mezo van akkor át kell írni! ===================
	    $this->state = JSON_decode('{
		"orderCol":"1", 
		"orderDir":"asc",
		"filterStr":"",
		"filterKapcs_id":0,
		"limitstart":0,
		"limit":10,
		"id":""
		}
		');
	    $this->message = JSON_decode('{
		"txt":"",
		"class":"msg"
		}
	    ');
	}
	
	//================== Jogosultság kezelés start =============
	
	/**
	  * abstarct function jogosultság kezelés, szükség szerint
	  * a form egyes mezoit readonlyvá vagy láthatatlanná teszi
	*/
	protected function formAccess(& $form) {
		
	}
	
	/**
	  * abstract function, a jform tömb elemeibol törli azokat
	  * aminek a modositása nem megengedett
	*/
	protected function jformAccess(& $jform)	{
		
	}

	/**
	  * abstract function, a $item objektumban
	  * leüresezi azon mezoket aminek a megnézése nem megengedett
	*/
	protected function itemAccess(& $item)	{
		
	}

	/**
	  * action jogosultság ellenörzés
	  * @param string action 'INSERT' | 'UPDATE' | 'DELETE' | 'SHOW'
	  * @param recordObject item
	  * @return bool
	*/  
	protected function actionAccess($action, $item) {
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		$userCsoport = $session->get('userCsoport');
		$result = true;
		if (($action == 'UPDATE') & 
		    ($userCsoport->kod != 'A') &
			($user->id != $item->user_id)) $result = false;
		if (($action == 'DELETE') & 
		    ($userCsoport->kod != 'A') &
			($user->id != $item->user_id)) $result = false;
		//DBG echo 'action Access action='.$action.' item->user_id='.$item->user_id.' user->id='.$user->id.' usercsoport='.$userCsoport->kod.' result='.$result.'<br />';	
		return $result;
	}
	
	/**
	  * Browser buttons generálás 
	*/  
	protected function browserButtons() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		$buttons = array( 
			  array('kommentek.edit','editbtn',JText::_('EDIT')),
			  array('kommentek.delete','deletebtn',JText::_('DELETE')),
			  array(JRequest::getVar('backtask','kapcsolatoks'),'backbtn',JText::_('BACK'))
		);
		return $buttons;	
	}

	
	//================== Jogosultság kezelés vége ===========
	
	/**
	  * $this->state feltöltése a session és JRequest alapján
	  * Ha JRequest érkezik az irja felül a session -t, az id cid[0] ban is érkezhet
	*/
	protected function getState() {
		$session = JFactory::getSession();
		$storedState = JSON_decode($session->get($this->browserName.'State'));
		if (is_object($storedState)) $this->state = $storedState;
		foreach ($this->state as $fn => $fv) {
			$this->state->$fn = JRequest::getVar($fn, $fv);
		}
		$cids = JRequest::getVar('cid');
		if (is_array($cids)) {
		  if (count($cids) > 0) $this->state->id = $cids[0];	
		}
	}
	
	/**
	  * $this->state tárolása session -ba
	*/  
	protected function saveState() {
		$session = JFactory::getSession();
		$session->set($this->browserName.'State',JSON_encode($this->state));
	}

	/**
	  * browser képernyo kirajzolása
	  * @JRequest string orderCol optional
	  * @JRequest string orderDir optional
	  * @JRequest string filterStr optional
	  * @JRequest integer limitstart optional
	  * @JRequest integer limit optional
	  * @JRequest string id optional
	  * @session object optional browser status {orderCol, orderDir, filterStr, limitstart, limit, id}
	  */
	public function browser() {
		$this->getState();
		
		// ha kapcsolatok vagy szures browser hivta akkor filterKapcs_id nem érkezik,
		// viszont cid[] ben érkezik a kapcs_id
		if (JRequest::getVar('filterKapcs_id')=='') {
			$cids = JRequest::getVar('cid');
			if (is_array($cids)) {
				$this->state->filterKapcs_id = $cids[0];
				$this->state->orderCol = 'a.idopont';
				$this->state->orderDir = 'desc';
				$this->state->limitstart = 0;
				$this->state->filterStr = '';
			}
		}
		
		$this->saveState();
		
		// kapcsolat adat hozzá olvasás
		$db = JFactory::getDBO();
		$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$this->state->filterKapcs_id);
		$kapcsolatInfo = $db->loadObject();
		if ($kapcsolatInfo->hazszam == 0) $kapcsolatInfo->hazszam = '';
		
		$model = $this->getModel($this->modelName);
		$items = $model->getItems($this->state);	
		//DBG echo $model->getDBO()->getQuery();
        $form = &JForm::getInstance($this->browserName,               
            JPATH_COMPONENT.'/models/forms/'.$this->formName.'.xml',
            array('control' => 'jform')); 
		for ($i=0; $i<count($items); $i++) {
			$this->itemAccess($items[$i]);
		}
		$total = $model->getTotal($this->state);
		$pagination = new JPagination( $total, $this->state->limitstart, $this->state->limit );
		// $pagination->setAdditionalUrlParam('név','érték');
		
		$view = $this->getView($this->browserName,'html');
		$view->set('title',JText::_($this->lngPre.'_BROWSER'));
		$view->set('state',$this->state);
		$view->set('items',$items);
		$view->set('kapcsolatInfo',$kapcsolatInfo);
		$view->set('form',$form);
		$view->set('total',$total);
		$view->set('pagination',$pagination);
		$view->set('message',$this->message);
		$buttons = $this->browserButtons();
		$view->set('buttons',$buttons);
		$view->setLayout('list');
		$view->display();
	}
	
	/**
	  * editor form
	  * @JRequest string id
	*/  
	public function edit() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$this->getState();
		$this->saveState();
		$model = $this->getModel($this->modelName);
		$item = $model->getItem($this->state->id);	

		// kapcsolat adat hozzá olvasás
		$db = JFactory::getDBO();
		$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$this->state->filterKapcs_id);
		$kapcsolatInfo = $db->loadObject();
		if ($kapcsolatInfo->hazszam == 0) $kapcsolatInfo->hazszam = '';

		if ($this->actionAccess('UPDATE',$item) == false) {
			$this->message->txt = JText::_('KOMMENTEK_ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
			return;
		}
		$this->itemAccess($item);
        $form = &JForm::getInstance($this->browserName,               
            JPATH_COMPONENT.'/models/forms/'.$this->formName.'.xml',
            array('control' => 'jform')); 
		$form->bind($item);
		$this->formAccess($form);
		if ($item) {
			$view = $this->getView($this->browserName,'html');
		    $view->set('title',JText::_($this->lngPre.'_EDIT'));
			$view->set('state',$this->state);
			$view->set('item',$item);
			$view->set('kapcsolatInfo',$kapcsolatInfo);
			$view->set('form',$form);
			$view->set('message',$this->message);
			$view->setLayout('form');
			$buttons = array( 
			  array('kommentek.save','okbtn',JText::_('OK'),false),
			  array('kommentek.browser','cancelbtn',JText::_('CANCEL'),true),
			);
			$view->set('buttons',$buttons);
			$view->display();
		} else {
			$this->message->txt = JText::_($this->lngPre.'_DATA_NOT_FOUND');
			$this->message->class = 'alert-error';
			$this->browser();
		}
	}

	/**
	  * add form
	*/  
	public function add() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$this->getState();
		$this->saveState();
		$model = $this->getModel($this->modelName);
		$item = $model->getItem("");	
		if ($this->actionAccess('INSERT',$item) == false) {
			$this->message->txt = JText::_('ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
		}
        $form = &JForm::getInstance($this->browserName,               
            JPATH_COMPONENT.'/models/forms/'.$this->formName.'.xml',
            array('control' => 'jform')); 
		$form->bind($item);
		$this->formAccess($form);
		$this->itemAccess($item);
		if ($item) {
			$view = $this->getView($this->browserName,'html');
		    $view->set('title',JText::_($this->lngPre.'_EDIT'));
			$view->set('state',$this->state);
			$view->set('item',$item);
			$view->set('form',$form);
			$view->set('message',$this->message);
			$buttons = array( 
			  array('kommentek.save','okbtn',JText::_('OK'),false),
			  array('kommentek.browser','cancelbtn',JText::_('CANCEL'),true),
			);
			$view->set('buttons',$buttons);
			$view->setLayout('form');
			$view->display();
		} else {
			$this->message->txt = JText::_($this->lngPre.'_DATA_NOT_FOUND');
			$this->message->class = 'alert-error';
			$this->browser();
		}
	}

	/**
	  * show form
	  * @JRequest string id
	*/  
	public function show() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$this->getState();
		$this->saveState();
		$model = $this->getModel($this->modelName);
		$item = $model->getItem($this->state->id);	
		if ($this->actionAccess('SHOW',$item) == false) {
			$this->message->txt = JText::_('ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
		}
        $form = &JForm::getInstance($this->browserName,               
            JPATH_COMPONENT.'/models/forms/'.$this->formName.'.xml',
            array('control' => 'jform')); 
		$form->bind($item);
		$this->formAccess($form);
		$this->itemAccess($item);
		if ($item) {
			$view = $this->getView($this->browserName,'html');
		    $view->set('title',JText::_($this->lngPre.'_SHOW'));
			$view->set('state',$this->state);
			$view->set('item',$item);
			$view->set('form',$form);
			$view->set('message',$this->message);
			$buttons = array( 
			  array('kommentek.browser','closebtn',JText::_('CLOSE'),true),
			);
			$view->set('buttons',$buttons);
			$view->setLayout('show');
			$view->display();
		} else {
			$this->message->txt = JText::_($this->lngPre.'_DATA_NOT_FOUND');
			$this->message->class = 'alert-error';
			$this->browser();
		}
	}
	
	/**
	  * adat tárolás a képernyon kitöltött adatokból
	  * @JRequest form mezok jform[] -ban  és rejtett mezoben az id
	*/  
	public function save() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$user = JFactory::getUser();
		$this->getState();
		$model = $this->getModel($this->modelName);
		$jform = JRequest::getVar('jform');
		$this->jformAccess($jform);
		$item = $model->bind($jform);	
		$item->id = JRequest::getVar('id',"");
		$oldItem = $model->getItem($id);
		
		if (($oldItem == false) & ($this->actionAccess('INSERT',$item) == false)) {
			$this->message->txt = JText::_('KOMMENTEK_ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
		}
		if (($oldItem == true) & ($this->actionAccess('UPDATE',$item) == false)) {
			$this->message->txt = JText::_('KOMMENTEK_ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
		}
		
		if ($model->check($item)) {
			if ($model->save($item)) {
				$this->message->txt = JText::_($this->lngPre.'_SAVED');
				$this->message->class = 'alert-success';
				// $this->state->id = $model->getInsertedId();
				$this->saveState();
				$this->browser();
			} else {
				$this->message->txt = $model->getError();
				$this->message->class = 'alert-error';
				$view = $this->getView($this->browserName,'html');
				if ($this->state->id == '')
				  $view->set('title',JText::_($this->lngPre.'_ADD'));
				else
				  $view->set('title',JText::_($this->lngPre.'_EDIT'));
				$view->set('state',$this->state);
				$view->set('item',$item);
				$view->set('message',$this->message);
				$view->setLayout('form');
				$view->display();
			}
		} else {
			$this->message->txt = $model->getError();
			$this->message->class = 'alert-error';
			$this->browser();
		}
	}
	
	/**
	  * rekord törlés
	  * JRequest string id
	*/  
	public function delete() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$this->getState();
		$model = $this->getModel($this->modelName);
		$id = JRequest::getVar('id',"");
		$item = $model->getItem($id);
		if ($this->actionAccess('DELETE',$item) == false) {
			$this->message->txt = JText::_('DELETE_ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
			return;
		}
		if ($model->candelete($id)) {
			if ($model->delete($id)) {
			  $this->message->txt = JText::_($this->lngPre.'_DELETED');
			  $this->message->class = 'alert-success';
			  $this->browser();
			} else {
			  $this->message->txt = $model->getError();
			  $this->message->class = 'alert-error';
			  $this->browser();
			}	
		} else {
			$this->message->txt = $model->getError();
			$this->message->class = 'alert-error';
			$this->browser();
		}
	}
} // class
?>