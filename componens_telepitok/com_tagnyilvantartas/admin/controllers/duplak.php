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
 * TagnyilvantartasDuplak Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerDuplak extends JControllerForm {
  /**
    * sessionból olvassa vagy sql -el kigyüjti a dupla emaileket és telefonszámokat 
	* figyelmen kivül hagyja azokat amiknél a "ndplTel" illetve "nodplemail" be van állítva
	* az sql result oszlopai: tipus, telszam, email
	* A limitstart -ban meghatározott rekordhoz tartozó kapcsolat rekordokat megjeleniti
	* chekform -ban
	* @JRequest limistart
	*/
  public function browser() {
	$session = JFactory::getSession();  
	$user = JFactory::getUser();
	$db = JFactory::getDBO();
	$userTerhats = $session->get('userTerhats');
	
	// van idegen zárolás a területi hatáskörében?
	$zarolt = false;
	foreach ($userTerhats as $terhat) {
		$db->setQuery('select zarol_user_id
		from #__tny_kapcsolatok
		where terszerv_id = '.$db->quote($terhat->terszerv_id).' and zarol_time > 0 and zarol_user_id <> '.$user->id);
		$res = $db->loadObject();
		if ($res) $zarolt = true;
    }		  
	if ($zarolt) {
		$this->setMessage('Jelenleg nem inditható. Mások dolgoznak a területi hatáskörébe tartozó adatokkal.');
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		$this->redirect();
		return;
	}
	
	// területi hatáskörében minden rekord zárolása
	if (JRequest::getVar('limitstart') == 0) {
		foreach ($userTerhats as $terhat) {
          $db->setQuery('update #__tny_kapcsolatok
		  set zarol_time='.time().',
		  zarol_user_id = '.$user->id.'
		  where terszerv_id='.$db->quote($terhat->terszerv_id));
		  $db->query();
		  //DBG echo $db->getQuery().'<br>';
		}		  
	}
		
	// dupla emailek, telszámok beolvasása sessionból vagy adatbázisból, majd tárolás session -ba  
	$items = false;
    $model = $this->getModel('duplak');
	if (JRequest::getVar('limistart',0) > 0)
	  $items = $session->get('duplaItems',false);
	if ($items == false)
	   $items = $model->getItems();
    $session->set('duplaItems',$items); 
    //DBG foreach ($items as $item) echo 'items: '.$item->tipus.' '.$item->adat.'<br />'; 
	
	// limistarta al meghatározott dupla rekordok beolvasása és
	// megjeleitése képernyőn
    $limitStart = JRequest::getVar('limitstart',0);
    if ($limitStart < count($items)) {
		//DBG echo 'limitStart="'.$limitStart.'"<br>';
		//DBG foreach ($items as $item)	  echo ' items '.$item->tipus.' '.$item->adat.'<br>';
		
		$item = $model->getItem($items[$limitStart]); // array of kapcsolat record
		
		if ($item) {
			if (count($item) > 1) {
			  $view = $this->getView('duplak','html');
			  $view->set('Item',$item);
			  if (trim($items[$limitStart]->tipus) == "TEL")
			    $view->set('title','Kapcsolatok Telefonszám duplikáció');
			  else	
			    $view->set('title','Kapcsolatok E-mail duplikáció');
		      $view->set('total',count($items));
			  $view->setLayout('default');
			  $view->display();
			} else {
			  // feltehetőleg már email duplaságnál le lett rz a dolog rendezve
			  $limitStart = 1 + (int)$limitStart;
			  JRequest::setVar('limitstart',$limitStart);
			  $this->browser();
			}
		} else {
  		  $this->setMessage('Hiba a kapcsolat adatok beolvasása közben');
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatok');
		  $this->redirect();
		}
	} else {
		// zárolás feloldása
		foreach ($userTerhats as $terhat) {
			  $db->setQuery('update #__tny_kapcsolatok
			  set zarol_time=0,
			  zarol_user_id = 0
			  where terszerv_id='.$db->quote($terhat->terszerv_id).'
			  and zarol_user_id = '.$user->id);
			  $db->query();
			  //DBG echo $db->getQuery().'<br>';
		}		  
		
		$session = JFactory::getSession();
		$session->set('duplaItems',false);	
		$this->setMessage('Nincs (több) duplikáció');
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		$this->redirect();
	}	
  }	
  
  /**
    * duplaság feldolgozás
    * @JRequest string	mezonev1, mezonev2, ....   a megtartandó adat base54_encoded formában
	* @JRequest ids lista a rekord kapcs_id -ékről
	*/
  public function feldolgozas() {
	$ids = JRequest::getVar('ids','');
	$w = explode(',',$ids);
	if (count($w > 1)) {
		$model = $this->getModel('duplak');
		$result = $model->update($w[0]); // update $w[0] rekord a JRequest-ben érkező értékekre
		if ($result) {
		  for ($i=1; $i<count($w); $i++) {
			if ($model->delete($w[$i]) == false) {
  			  $i = count($w); // kilép a for ciklusból
			  $this->setMessage('Hiba lépett fel az összefésülés végrehajtás során (1)');
			  $this->megsem();
			  return;
			};
		  }
		} else {
			$this->setMessage('Hiba lépett fel az összefésülés során (2)');
			$this->megsem();
			return;
		}
	}
	echo '<div class="alert-success">Kapcsolat adatok sikeresen összefésülve.</div>';
    $this->browser();	
  }
  
  public function megsem() {
	$session = JFactory::getSession();
	$user = JFactory::getUser();
	$db = JFactory::getDBO();
	$userTerhats = $session->get('userTerhats');

	// zárolás feloldása
	foreach ($userTerhats as $terhat) {
	  $db->setQuery('update #__tny_kapcsolatok
	  set zarol_time=0,
	  zarol_user_id = 0
	  where terszerv_id='.$db->quote($terhat->terszerv_id).'
	  and zarol_user_id = '.$user->id);
	  $db->query();
	  //DBG echo $db->getQuery().'<br>';
	}		  

    $session->set('duplaItems',false);	
	$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
	$this->redirect();
  }
}// class
?>