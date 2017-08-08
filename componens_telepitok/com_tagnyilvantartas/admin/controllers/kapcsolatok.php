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
 * TagnyilvantartasKapcsolatok Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerKapcsolatok extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'kapcsolatok';
		$this->view_list = 'kapcsolatoks';
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
	public function getModel($name = 'Kapcsolatok', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);
	
		return $model;
	}	
    
    public function szures() {
        $view = $this->getView('kapcsolatok,html');
        $view->setLayout('szures');
        $view->display();
    }

	/**
	  * edit form kirajzolása zárolás kezeléssel
	  * JRequest cids
	  * @return void
	  */
    public function edit() {
	  $session = JFactory::getSession();
      $userCsoport = $session->get('userCsoport');
      $cids = JRequest::getVar('cid');
      $id = $cids[0];
      $user = JFactory::getUser();
	  $db = JFactory::getDBO();

	  // nem zárolta másik user?
	  $db->setQuery('select zarol_user_id 
	  from #__tny_kapcsolatok
	  where zarol_user_id > 0 and zarol_user_id <> '.$user->id.' and kapcs_id = '.$id);
      $res = $db->loadObject();
	  if ($res) {
		  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_RECORD_LOCKED'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
		  return;
	  }
	  
	  //+2015.10.29 SM csoport csak szimpatizánsokat modosíthat
	  $db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$id);
	  $rekord = $db->loadObject();
	  if (($userCsoport->kod == 'SM') & ($rekord->kategoria_id != 3)) {
		  $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCESS_VIOLATION'));
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
		  return;
	  }
	  //-2015.10.29 SM csoport csak szimpatizánsokat modosíthat
	  
	  
	  // rekord zárolás
	  $db->setQuery('update #__tny_kapcsolatok
	  set zarol_user_id='.$user->id.',
	  zarol_time='.time().'
	  where kapcs_id = '.$id);
	  if ($db->query()) {
       $model = $this->getModel('kapcsolatok');
       $view = $this->getView('kapcsolatok','html');
	   $session = JFactory::getSession();
       $item = $model->getItem($id);
       itemAccess($item, $userCsoport);	   
       $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => 'jform'));
       $form->bind($item);                               
       $view->set('Item',$item);
       $view->set('Form',$form);
       $view->setLayout('edit');
       $view->display();
	  } else
         $db->stderr();	
	 
	}

    
	/**
	  * show form kirajzolása
	  * JRequest cids (array or id) vagy integer id
	  * @return void
	  */
    public function show() {
       $model = $this->getModel('kapcsolatok');
       $view = $this->getView('kapcsolatok','html');
	   $session = JFactory::getSession();
	   $userCsoport = $session->get('userCsoport');
       $cids = JRequest::getVar('cid');
	   if (is_array($cids))
          $id = $cids[0];
	   else
	   	  $id = $cids;  


		//+ 2016.07.23 pénzügyi rendszer miatt
	   if (($id < 0) | ($id == '')) $id = JRequest::getVar('id',0);
		//- 2016.07.23 pénzügyi rendszer miatt
	   
       $item = $model->getItem($id);
       itemAccess($item, $userCsoport);	   
       $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => ''));
       $form->bind($item);                               
       $view->set('Item',$item);
       $view->set('Form',$form);
       $view->setLayout('show');
       $view->display();
    } 
	
	/**
	  * minden zárolás feloldása
	  */
	public function unlock() {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
	    $userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod == 'A') {
		    $db->setQuery('update #__TNY_kapcsolatok
			set zarol_user_id = 0,
			zarol_time = 0');	
			if ($db->query())
			  $this->setMessage('Minden zárolás feloldva.');
		    else 
			  $this->setMessage('Hiba lépett fel a müvelet közben '.$db->getErrorMsg());
		} else {
  		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=fejlec&task=fejlec.accessdenied');
		  $this->redirect();
		}
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		$this->redirect();
	}
    /**
      * mégsem gombot nyomtak a szürés vagy groupedit vagy export képernyõn
      * ugrás a kapcsolatok böngészõ képernyõre
    */
	
	/**
	 * adott kapcsolat rekord változásnapló lekérdezés
	*/
	public function naplo() {
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=naplos&filter_kapcs_id='.JRequest::getVar('kapcs_id'));
		$this->redirect();
	}
	
	//=======================================================
	/**
	  * Módosításra nem jogosult user, javítási javaslat az adminok számára
	*/  
	public function javaslat() {
	   $model = $this->getModel('kapcsolatok');
       $view = $this->getView('kapcsolatok','html');
	   $session = JFactory::getSession();
	   $userCsoport = $session->get('userCsoport');
       $cids = JRequest::getVar('cid');
	   if (is_array($cids))
          $id = $cids[0];
	   else
		  $id = $cids;  
       $item = $model->getItem($id);
       itemAccess($item, $userCsoport);	   
       $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => ''));
       $form->bind($item);                               
       $view->set('Item',$item);
       $view->set('Form',$form);
       $view->setLayout('javaslat');
       $view->display();
	}
	
	/**
	  * javaslat form adatainak tárolása
	  *
	*/  
	public function javaslatSave() {
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$kapcs_id = JRequest::getVar('kapcsolat_id');
		$time = time();
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__tny_javaslat (
		  `kapcs_id` int(11) NOT NULL DEFAULT "0",
		  `javaslo_id` int(11) NOT NULL DEFAULT "0",
		  `idopont` time NOT NULL DEFAULT "00:00:00",
		  `mezo` varchar(32) COLLATE utf8_hungarian_ci NOT NULL default "",
		  `ertek` varchar(128) COLLATE utf8_hungarian_ci NOT NULL default "",
		  `allapot` varchar(10) COLLATE utf8_hungarian_ci NOT NULL DEFAULT "javaslat",
		  `megjegyzes` varchar(80) COLLATE utf8_hungarian_ci NOT NULL default ""
		  ) DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci
		');
		//$db->query();
		for ($i = 0; $i < 20; $i++) {
			if (JRequest::getVar('javaslatMezo'.$i) != '') {
				$db->setQuery('insert into #__tny_javaslat values (
				'.$db->quote($kapcs_id).',
				'.$db->quote($user->id).',
				"'.date('Y-m-d H:i:s').'",
				'.$db->quote(JRequest::getVar('javaslatMezo'.$i)).',
				'.$db->quote(JRequest::getVar('javaslatErtek'.$i)).',
				"javaslat",
				'.$db->quote(JRequest::getVar('uzenet'.$i)).')');
				$db->query();
			}
		}
		$this->setMessage('Javaslat tárolva.');
		$this->setRedirect('index.php?option=com_tagnyilvantartas&task=kapcsolatok.show&cid='.$kapcs_id);
		$this->redirect();
	}
	
	/**
	  * a bejelentkezett user területi hatáskörébe eső javaslatok böngészése
	  * elöl a "javaslat" státuszuak, utána az "átvezetett" majd a "elutasitott" -ak
	*/
	public function javaslatok() {
	   // jogosultság ellenörzés
	   $session = JFactory::getSession();
       $userCsoport = $session->get('userCsoport');
	   if (($userCsoport->kod != 'A') & ($userCsoport->kod != 'SM')) {
		  $this->setMessage('Ezt a funkciót csak az adminisztrátorok használhatják !!! /'.$userCsoport->kod.'/');
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
	   } 
	   $model = $this->getModel('javaslatoks');
	   $items = $model->getItems();
       $view = $this->getView('javaslatoks','html');
	   $view->set('Items',$items);	
	   $view->setLayout('default');
	   $view->display();
	}
	
	/**
	  * javaslat elfogadása, átvezetése, naplozás, javaslat rekord státusz modositás
	  * JRequest:  kapcs_id, javaslo_id, mezo, time
	*/
	public function javaslatElfogadva() {
	   // jogosultság ellenörzés
	   $session = JFactory::getSession();
       $userCsoport = $session->get('userCsoport');
	   if (($userCsoport->kod != 'A') & ($userCsoport->kod != 'SM')) {
		  $this->setMessage('Ezt a funkciót csak az adminisztrátorok használhatják /'.$userCsoport->kod.'/');
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
	   } 
	   $model = $this->getModel('javaslatoks');
	   $model->elfogad(JRequest::getVar('kapcs_id'),
	                   JRequest::getVar('javaslo_id'),
	                   JRequest::getVar('mezo'),
	                   JRequest::getVar('time'));
		$this->setMessage('Javaslat átvezetve az adatbázisban.');
		$this->setRedirect('index.php?option=com_tagnyilvantartas&task=kapcsolatok.javaslatok');
		$this->redirect();
	}
	
	/**
	  * javaslat elvetése, javaslat rekord státusz modositás
	  * JRequest:  kapcs_id, javaslo_id, mezo, time
	*/
	public function javaslatCancel() {
	   // jogosultság ellenörzés
	   $session = JFactory::getSession();
       $userCsoport = $session->get('userCsoport');
	   if (($userCsoport->kod != 'A') & ($userCsoport->kod != 'SM')) {
		  $this->setMessage('Ezt a funkciót csak az adminisztrátorok használhatják /'.$userCsoport->kod.'/');
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
	   } 
	   $model = $this->getModel('javaslatoks');
	   $model->elvet(JRequest::getVar('kapcs_id'),
	                 JRequest::getVar('javaslo_id'),
	                 JRequest::getVar('mezo'),
	                 JRequest::getVar('time'));
		$this->setMessage('Javaslat elvetve.');
		$this->setRedirect('index.php?option=com_tagnyilvantartas&task=kapcsolatok.javaslatok');
		$this->redirect();
	}
	
}// class
?>