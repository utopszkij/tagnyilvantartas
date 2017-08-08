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
 * Kapcsolatok list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Tagnyilvantartas
 */
class TagnyilvantartasControllerNaplos extends JControllerAdmin {
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  TagnyilvantartasControllerkapcsolatoks
	 * @see     JController
	 */
	public function __construct($config = array()) 	{
		$this->view_list = 'naplos';
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
	public function getModel($name = 'Naplo', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
    
    /**
      * mégsem gombot nyomtak a szürés vagy groupedit vagy export képernyõn
      * ugrás a kapcsolatok böngészõ képernyõre
      */
    public function megsem() {
        $this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos');
        $this->redirect();
    }

	/**
	  * egy adott napló rekord megjelenitése a képernyőn
	  * @JRequest string  naplo_id "####,#####,####"  kapcsolat_id,lastact_time, lastact_user_id
	  * @return void
	*/  
	public function show() {
		$model = $this->getModel('naplos');
		$view = $this->getView('naplos','html');
		$cids = JRequest::getVar('cid');
		$item = $model->getItem($cids[0]);
		$lastItem = $model->getLastItem($item->kapcs_id, $item->lastact_time);
		if ($lastItem == false) $lastItem = $item;
        //itemAccess($item, $userCsoport);	   
        $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => 'jform'));
		
		// eltérés keresés item és lastItem között, ha van eltérés akkor item -be beteszi
		foreach ($item as $fn => $fv) {
			if (($fn == 'hirlevel') | ($fn == 'ellenorzott')) {
				if ($item->$fn == 1) $item->$fn = 'Igen'; else $item->$fn = 'Nem';
				if ($lastItem->$fn == 1) $lastItem->$fn = 'Igen'; else $lastItem->$fn = 'Nem';
			} 
			if (($item->$fn != $lastItem->$fn) &
				($fn != 'lastaction') &
				($fn != 'lastact_time') &
				($fn != 'lastact_user_id') &
				($fn != 'lastact_info') &
				($fn != 'name')) {
				if ($lastItem->$fn == '') $lastItem->$fn = 'üres';
				$item->$fn .= '<br /><span class="elozoertek">'.$lastItem->$fn.'</span>'; 
			}
		}
							 
        $form->bind($item);                               
		//DBG foreach ($item as $fn => $fv) echo $fn.'='.$fv.'<br />';
        $view->set('Item',$item);
        $view->set('Form',$form);
		$view->set('lastItem',$lastItem);
		if ($item) {
		  $view->setLayout('show');
		  $view->display();
		} else {
		  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_NAPLO_READ_ERROR'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos');
          $this->redirect();		  
		}  
	}
		
	/**
	  * egy adott kapcsolat rekord visszaállitása a napló rekord alapján
	  * @JRequest string  naplo_id "####,#####,####"  kapcsolat_id,lastact_time, lastact_user_id
	  * @return void
	*/  
	public function restore() {
		$naploModel = $this->getModel('naplos');
		$kapcsolatModel = $this->getModel('kapcsolatok');
		$item = $naploModel->getItem(JRequest::getVar('naplo_id'));
		$item->lastact_info = 'Visszaállítás '.$item->lastact_time.' állapotra';
		
		//echo 'restore naplo_id = '.JRequest::getVar('naplo_id');
		//foreach ($item as $fn => $fv) echo 'restore jönne '.$fn.'='.$fv.'<br />';
		// exit();
		
		$itemArray = array();
		foreach ($item as $fn => $fv) $itemArray[$fn] = $fv;
		
		if ($item) {
			if ($kapcsolatModel->save($itemArray)) {
			  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_KAPCSOLAT_RESTORED'));
		      $this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos');
              $this->redirect();		  
			} else {
			  $this->setMessage($kapcsolatModel->getError());
		      $this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos');
              $this->redirect();		  
			}
		} else {
		  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_NAPLO_READ_ERROR'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=naploks');
          $this->redirect();		  
		}
	}
	/**
	  * régi törlése form
	  */
	public function purge() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
	  if ($userCsoport->kod == 'A') {
		  echo '<h2>'.JText::_('NAPLOS_PURGE').'</h2>
		  <div class="lmpForm">
		  <form name="adminForm" method="post" action="index.php">
		  <input type="hidden" name="option" value="com_tagnyilvantartas" />
		  <input type="hidden" name="view" value="naplos" />
		  <input type="hidden" name="task" value="naplos.dopurge" />
		  <p>
		  <label>Dátum (éééé-hh-nn):</label>
		  <input type="text" name="date" value="" style="width:120px"/>
		  </p>
		  <p>
		  A megadott dátumnál régebbi napló bejegyzések törlődnek.
		  </p>
		  <p><button type="submit">Rendben</button></p>
		  </form>
		  </div>
		  ';	
	  } else {
		  echo '<div class="errorMs">Önnek nincs joga ehhez a müvelethez</div>';
	  }
	}
	
    /**
	  * adott időtarztamba eső napló rekordok törlése végrehajtás
	  * @JRequest string date1
	*/
	public function dopurge() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
	    if ($userCsoport->kod == 'A') {
			$model = $this->getModel('naplos');
			if ($model->purge(JRequest::getVar('date')))
				$this->setMessage(JText::_('NAPLO_PUGED'));
			else
				$this->setMessage($model->getError());
			$this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos');
			$this->redirect();		  
		} else {
		   echo '<div class="errorMs">Önnek nincs joga ehhez a müvelethez</div>';
		}	
	}
	/**
	  * egy adott kapcsolat változás történetére szürni
	  * @JRequest cid[0]  = kapcsid,lastact_time,lastact_user_id  
	  * @return void
	  */
	public function filterkapcsid() {
		$cids = JRequest::getVar('cid');
		$cid = $cids[0];
		$w = explode(',',$cid);
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos&filter_kapcs_id='.$w[0]);
		$this->redirect();
	}
	
	/**
	  * CSV IMPORT törlés biztonsági képernyő
	  * információ kiirás és biztonsági kérdés
	  * @JREquest naplo_is = kapcs_id.lastact_time.lastact_user_id
	  */
	public function csvdelete1() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
			echo '<div class="errormsg">Ezt a funkciót csak adminisztrátorok használhatják.</div>'; exit();
		}
		
		$model = $this->getModel('naplos');
		$item = $model->getItem(JRequest::getVar('naplo_id'));
		$view = $this->getView('naplos','html');
        $view->set('Item',$item);
		$view->setLayout('csvdel1');
		$view->display();
	}
	
	/**
	  * CSV IMPORT törlés végrehajtása
	  * @JREquest naplo_is = kapcs_id.lastact_time.lastact_user_id
	  */
	public function csvdelete2() {
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
			echo '<div class="errormsg">Ezt a funkciót csak adminisztrátorok használhatják.</div>'; exit();
		}
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		// szétbontja a naplo_id adatot, beolvassa a napló rekordból a lastact_info -t
		$w = explode(',',JRequest::getVar('naplo_id'));
		if (count($w)==3) {
			$lastact_time = $w[1];
			$db->setQuery('SELECT lastact_info
			FROM #__tny_naplo
			WHERE kapcs_id="'.$w[0].'" and lastact_time="'.$w[1].'" and lastact_user_id="'.$w[2].'"');
			//DBG echo '<pre>'.$db->getQuery().'</pre>';
			$res = $db->loadObjectList();
			if (count($res) > 0)
				$lastact_info = $res[0]->lastact_info;
			else
				$lastact_info = '?@?';
			
  		    // bejelöli a "halálraitélt" kapcsolat rekordokat
			$ora = substr($lastact_time,11,2);
			if ($ora > 1) $ora1 = $ora-1; else $ora1 = '00';
			if ($ora < 22) $ora2 = $ora + 1; else $ora2 = '23';
			$timeMin = substr($lastact_time,0,10).' '.($ora1).':00';
			$timeMax = substr($lastact_time,0,10).' '.($ora2).':59';
			$db->setQuery('UPDATE #__tny_kapcsolatok k, #__tny_naplo n
			set k.lastact_info = "CSV_IMPORT_DELETE",
			    k.lastact_time = "'.date('Y-m-d H:i:s').'",
				k.lastact_user_id = "'.$user->id.'"
			where k.kapcs_id = n.kapcs_id and
			      n.lastact_info = "'.$lastact_info.'" and
				  n.lastact_time >= "'.$timeMin.'" and n.lastact_time <= "'.$timeMax.'"
			');
			//DBG echo '<pre>'.$db->getQuery().'</pre>';
			if ($db->query()) {

			  // naplóz
			  $db->setQuery('INSERT INTO #__tny_naplo
			  SELECT *
			  FROM #__tny_kapcsolatok
			  WHERE lastact_info="CSV_IMPORT_DELETE"');
			  //DBG echo '<pre>'.$db->getQuery().'</pre>';
			  if (!$db->query()) $db->sderr();
		      
			  // töröl
			  $db->setQuery('DELETE FROM #__tny_kapcsolatok where lastact_info="CSV_IMPORT_DELETE"');
			  //DBG echo '<pre>'.$db->getQuery().'</pre>';
			  if (!$db->query()) $db->sderr();
			} else {
			  $db->sderr();	
			}  
		}
	}
	
}
