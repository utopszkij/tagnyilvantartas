<?php
/**
 * @version V1.00
 * @package    joomla
 * @subpackage mvceditor
 * @author	   Fogler Tibor  tibor.fogler@gmail.com
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 *
 * Konstansok: comName, author, copyYear
 * 1.szint view -ek kezel�se.   
 *    Adatok viewname, tableName, lngPre, description
 *    Taskok:  browser, edit, add, delete, save 
 *    Rutinok: bool = headerChanged() setHeader() 
 * 2/a szint php f�jlok kezel�se.  
 *    $session->mvcedit.viewname, tableName, lngPre, description
 *    $session->mvcedit.phpfile  (model, controller, viewer, helper, tmplname1, tmplname2,...)
 *    Taskok: getTmpls, phpedit, phpdelete, phpsave, addtmpl, deltmpl
 * 2/b szint  xml f�jl kezel�se
 *    $session->mvcedit.viewname, tableName, lngPre, description
 *    Taskok: xmledit, xmlsave, xmlCreate(tableName)
 * 2/c szint  lng ini f�jl kezel�se
 *    $session->mvcedit.viewname, tableName, lngPre, description
 *    Taskok: lngedit, lngsave
 *
 * comInfo
 *     comName
 *     comDescription
 *     author
 *     licence
 * item (view info)
 *     viewName
 *     viewDescription
 *     tableName
 *     lngPre
 *     description
 *     version
 *     copyRight 
 *     tmpls  array of string (tmpl name)
 * file_id = MviewName   model 
 *           VviewName   viewer 
 *           CviewName   controller 
 *           HviewName   helper 
 *           FviewName   form (cml)
 *           L           language file
 *           TviewName_tmplName   template file 
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');
  
class tagnyilvantartasControllerMvceditor extends JControllerLegacy {
	/**
	  * model,browser neve �s Lng prefix
	*/  
	protected $modelName = 'mvceditor';
	protected $browserName = 'mvceditor'; 
	protected $formName = 'mvceditor'; 
	protected $lngPre = 'MVC';

	/**
	  *  browser st�tusz
	*/  
	protected $state = null;
	
	/**
	  * �zenet
	*/
	protected $message = null;
	
	/**
	  * objektum gener�l�s
	*/  
	function __construct() {
		parent::__construct();
	    // ============================== FIGYELEM Ha t�bb sz�ro mezo van akkor �t kell �rni! ===================
	    $this->state = JSON_decode('{
		"orderCol":"1", 
		"orderDir":"asc",
		"filterStr":"",
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
	
	//================== Jogosults�g kezel�s start =============
	
	/**
	  * abstarct function jogosults�g kezel�s, sz�ks�g szerint
	  * a form egyes mezoit readonlyv� vagy l�thatatlann� teszi
	*/
	protected function formAccess(& $form) {
		
	}
	
	/**
	  * abstract function, a jform t�mb elemeibol t�rli azokat
	  * aminek a modosit�sa nem megengedett
	*/
	protected function jformAccess(& $jform)	{
		
	}

	/**
	  * abstract function, a $item objektumban
	  * le�resezi azon mezoket aminek a megn�z�se nem megengedett
	*/
	protected function itemAccess(& $item)	{
		
	}

	/**
	  * action jogosults�g ellen�rz�s
	  * @param string action 'INSERT' | 'UPDATE' | 'DELETE' | 'SHOW'
	  * @param recordObject item
	  * @return bool
	*/  
	protected function actionAccess($action, $item) {
		return true;
	}
	
	/**
	  * Browser buttons gener�l�s (jogosults�g kezel�s is elk�pzelheto itt)
	*/  
	protected function browserButtons() {
		$buttons = array( 
		  array('{viewName}.add','addbtn',JText::_('MVC_ADD')),
		  array('{viewName}.edit','editbtn',JText::_('MVC_EDIT')),
		  array('{viewName}.show','showbtn',JText::_('MVC_OPEN')),
		  array('{viewName}.delete','deletebtn',JText::_('MVC_DELETE'))
		);
		return $buttons;	
	}

	//================== Jogosults�g kezel�s v�ge ===========
	
	/**
	  * $this->state felt�lt�se a session �s JRequest  alapj�nn
	  * Ha JRequest �rkezik az irja fel�l a session -t
	*/
	protected function getState() {
		$session = JFactory::getSession();
		$storedState = JSON_decode($session->get($this->browserName.'State'));
		if (is_object($storedState)) $this->state = $storedState;
		foreach ($this->state as $fn => $fv) {
			$this->state->$fn = JRequest::getVar($fn, $fv);
		}
	}
	
	/**
	  * $this->state t�rol�sa session -ba
	*/  
	protected function saveState() {
		$session = JFactory::getSession();
		$session->set($this->browserName.'State',JSON_encode($this->state));
	}

	/**
	  * browser k�pernyo kirajzol�sa
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
		$this->saveState();
		$model = $this->getModel($this->modelName);
		$items = $model->getItems($this->state);	
        $form = &JForm::getInstance($this->browserName,               
            JPATH_COMPONENT.'/models/forms/'.$this->formName.'.xml',
            array('control' => 'jform')); 
		for ($i=0; $i<count($items); $i++) {
			$this->itemAccess($items[$i]);
		}
		$total = $model->getTotal($this->state);
		$pagination = new JPagination( $total, $this->state->limitstart, $this->state->limit );
		// $pagination->setAdditionalUrlParam('n�v','�rt�k');
		
		$view = $this->getView($this->browserName,'html');
		$view->set('title',JText::_($this->lngPre.'_BROWSER'));
		$view->set('state',$this->state);
		$view->set('items',$items);
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
		if ($this->actionAccess('EDIT',$item) == false) {
			$this->message->txt = JText::_('ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
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
			$view->set('form',$form);
			$view->set('message',$this->message);
			$view->setLayout('form');
			$buttons = array( 
			  array('{viewName}.save','okbtn',JText::_('OK'),false),
			  array('{viewName}.browser','cancelbtn',JText::_('CANCEL'),true),
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
			  array('{viewName}.save','okbtn',JText::_('OK'),false),
			  array('{viewName}.browser','cancelbtn',JText::_('CANCEL'),true),
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
			  array('{viewName}.browser','closebtn',JText::_('CLOSE'),true),
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
	  * adat t�rol�s a k�pernyon kit�lt�tt adatokb�l
	  * @JRequest form mezok jform[] -ban  �s rejtett mezoben az id
	*/  
	public function save() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$this->getState();
		$model = $this->getModel($this->modelName);
		$jform = JRequest::getVar('jform');
		$this->jformAccess($jform);
		$item = $model->bind($jform);	
		$item->id = JRequest::getVar('id',"");
		$oldItem = $model->getItem($id);
		if (($oldItem == false) & ($this->actionAccess('INSERT',$item) == false)) {
			$this->message->txt = JText::_('ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
		}
		if (($oldItem == true) & ($this->actionAccess('UPDATE',$item) == false)) {
			$this->message->txt = JText::_('ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
		}
		
		if ($model->check($item)) {
			if ($model->save($item)) {
				$this->message->txt = JText::_($this->lngPre.'_SAVED');
				$this->message->class = 'alert-success';
				//$this->state->id = $model->getInsertedId();
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
	  * rekord t�rl�s
	  * JRequest string id
	*/  
	public function delete() {
		JSession::checkToken() or die( 'Invalid Token' );         
		$this->getState();
		$model = $this->getModel($this->modelName);
		$id = JRequest::getVar('id',"");
		$item = $model->getItem($id);
		if ($this->actionAccess('DELETE',$item) == false) {
			$this->message->txt = JText::_('ACCES_DENIED');
			$this->message->class='alert-error';
			$this->browser();
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