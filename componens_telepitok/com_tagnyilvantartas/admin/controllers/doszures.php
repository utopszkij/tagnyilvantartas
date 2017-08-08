<?php
/**
* @version		$Id$ $Revision$ $Date$ $Author$ $
* @package		Tagnyilvantartas
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, .
* @license 		
*/

//2017.06.09 hirlevél küldésnél tárolja a szürésu feltételeket


defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}


/**
 * Felhcsoportok list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Tagnyilvantartas
 */
class TagnyilvantartasControllerDoszures extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  TagnyilvantartasControllerDoszures
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		$this->view_list = 'doszures';
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
	public function getModel($name = 'Doszures', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
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
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
    
    /**
      * szürés utáni browser képernyõ
      * JRequest: mezox, ralaciox, ertekx,....
      *    onlychecked, funkcio, limitstart
      * @return void
      */
    public function start() {
         if (JRequest::getVar('relacio1')=='eq')
            JRequest::setVar('relacio1','=');    // ha link hivja akkor van ez
		// 2016.05.28. elozoSzures tárolása sessionba
		$session = JFactory::getSession();
		$elozoSzures = array();
		for ($i=0; $i<20; $i++) {
			if (JRequest::getVar('mezo'.$i) != '') {
				$sz = new stdClass();
				$sz->mezoNev = JRequest::getVar('mezo'.$i);
				$sz->relacio = JRequest::getVar('relacio'.$i);
				$sz->ertek = JRequest::getVar('ertek'.$i);
				$elozoSzures[] = $sz;
			}
		}
		$session->set('elozoSzures',$elozoSzures);
		
        jimport('joomla.html.pagination');
        $model = $this->getModel('doszures');
		$whereStr = $model->makeWhereStr('');
        $items = $model->getItems();
        $pageNav = $model->getPagination();
        $view = $this->getView('doszures','html');
        $view->set('Items',$items);
        $view->set('model',$model);
        $view->set('Pagination',$pageNav);
        $view->setLayout('default');
        $view->display();
    }

	/**
	* a kezelő a szürés utáni browser képernyőn a "tovább szűkít" gombra kattintott
	* ilyenkor a sessionban lévő "elozoSzures" adatot hasznlva kell a szürő képernyőt kirajzolni
	* ez benne van a tmpl/szures.php -ban csak a tovab="I" JRequest paramétert kellbeállítani
	*/
	public function tovabb() {
		JRequest::setVar('tovabb','I');
		$this->szures();
	}
	
	/**
      *	editor form kirajzolása
	  * @return void
	  * @JRequest cid[], mezox, relaciox, ertekx......,limitstart, filer_ordering, filter_ordering_Dir
	  *           onlychecked
    */	  
	public function edit() {
       $cids = JRequest::getVar('cid');
       $id = $cids[0];
	   $user = JFactory::getUser();
	   $session = JFactory::getSession();
	   $userCsoport = $session->get('userCsoport');
	   $db = JFactory::getDBO();
		
	   // nem zárolta másik user?
		$db->setQuery('select zarol_user_id 
		from #__tny_kapcsolatok
		where zarol_user_id > 0 and zarol_user_id <> '.$user->id.' and kapcs_id = '.$id);
		$res = $db->loadObject();
		if ($res) {
           echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_RECORD_LOCKED').'</div>';
		   $this->show();
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
	    if (!$db->query()) {
           $db->stderr();		  
		}
       $model = $this->getModel('kapcsolatok');
       $view = $this->getView('doszures','html');
	   
       $item = $model->getItem($id);
	   $item->kapcs_id = $id;
	   if (JRequest::getVar('jform') != '') {
		   $jform = JRequest::getVar('jform');
           foreach ($item as $fn => $fv) {
			   if (isset($jform[$fn])) 
				   $item->$fn = $jform[$fn];
			   else 
				   $item->$fn = JRequest::getVar('jform['.$fn.']', $item->$fn);
		   }
	   }
       itemAccess($item, $userCsoport);	   
       $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => 'jform'));
       $form->bind($item);                               
       $view->set('Item',$item);
       $view->set('Form',$form);
       $view->setLayout('edit');
       $view->display();
	}

	/**
      *	show form kirajzolása
	  * @return void
	  * @JRequest cid[], mezox, relaciox, ertekx......,limitstart, filer_ordering, filter_ordering_Dir
	  *           onlychecked
    */	  
	public function show() {
       $model = $this->getModel('kapcsolatok');
       $view = $this->getView('doszures','html');
	   $session = JFactory::getSession();
	   $userCsoport = $session->get('userCsoport');
       $cids = JRequest::getVar('cid');
       $id = $cids[0];
       $item = $model->getItem($id);
	   $item->kapcs_id = $id;
       itemAccess($item, $userCsoport);	   
       $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => 'jform'));
       $form->bind($item);                               
       $view->set('Item',$item);
       $view->set('Form',$form);
       $view->setLayout('show');
       $view->display();
	}


	
	/**
	  * save edit form, redirect to doszures.start vagy hiba esetén doszures.edit
	  */
	public function save() {
      $session = JFactory::getSession();
	  JSession::checkToken( 'post' ) or die( 'Invalid Token' );		
	  $model = $this->getModel('kapcsolatok');
	  $jform = JRequest::getVar('jform');
	  $form = false; // csak szintatikailag kell, a validet fv -em nem használja
	  if ($model->validate($form, $jform)) {
		  if ($model->save($jform)) {
			  echo '<div class="alert-success">Adat tárolva</div>';
			  JRequest::setVar('funkcio','szures');
			  JRequest::setVar('task','start');
			  $this->start();
		  } else {
			  echo '<div class="alert">Adat tárolva</div>';
			  JRequest::setVar('funkcio','szures');
			  JRequest::setVar('task','start');
			  $this->start();
		  }
	  } else {
			  echo '<div class="alert">'.$model->getError().'</div>';
			  $this->setMessage($model->getError());
			  JRequest::setVar('task','edit');
			  $this->edit();
	  }
	}
	
	/**
	  * redirect to doszures.start
	  */
	public function cancel() {
		JRequest::setVar('task','start');
		JRequest::setVar('funkcio','szures');
        $this->start();
	}
	/**
	  * redirect to doszures.start
	  */
	public function megsem() {
	   JRequest::setVar('task','start');
	   JRequest::setVar('funkcio','szures');
       $this->start();
	}

    /**
	  * törlés
	  */
	public function delete() {
       $model = $this->getModel('kapcsolatok');
       $view = $this->getView('doszures','html');
	   $session = JFactory::getSession();
	   $userCsoport = $session->get('userCsoport');
       $cids = JRequest::getVar('cid');
       $id = $cids[0];
       if ($model->delete($cids)) {
		  echo '<div class="alert-success">Adat törölve ('.$id.').</div>';
		  JRequest::setVar('task','start');
		  JRequest::setVar('funkcio','szures');
		  $this->start();
	   } else {
		  echo '<div class="alert">'.$model->getError().'</div>';
		  JRequest::setVar('funkcio','szures');
		  JRequest::setVar('task','start');
		  $this->start();
	   }		
	}  
	
	/**
	  * kommentek böngésző popup megjelenités 
	  */
	public function comments() {
		echo '<div class="working">&nbsp;</div>';
		return;
	}
	
	/**
	  * CSV export végrehajtása
	  * @return void
	  */
	public function export() {
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',10000000);
		$model = $this->getModel('doszures');
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$items = $model->getItems();
		
		// naplózás
		// filterStr kialakitása a JRequest-ben érkezett adatokból.
		$filterStr = '';
		if (JRequest::getVar('onlychecked')==1) $filterStr = 'Csak az ellenőrzött adatok';
		for ($i=1; $i<20; $i++) {
			if (JRequest::getVar('mezo'.$i) != '') {
			   if ($filterStr != '') 
				   $filterStr .= ' és <br />';           
			   $rel = JRequest::getVar('relacio'.$i);
			   if ($rel == 'lt') $rel = '&lt;';
			   if ($rel == 'lte') $rel = '&lt;=';
			   if ($rel == 'gt') $rel = '&gt;';
			   if ($rel == 'gte') $rel = '&gt;=';
			   if ($rel == 'ne') $rel = '&lt;&gt;';
			   if ($rel == 'like') $rel = 'benne;';
			   if ($rel == 'between') $rel = 'tól-ig;';
			   $mezoNev = JRequest::getVar('mezo'.$i,'');
			   $mezoLabel = JText::_($mezoNev);
			   if ($mezoLabel == $mezoNev) {
				   // nem talált hozzá forditást, lehet, hogy extrafield....
				   $db->setQuery('select * from #__tny_extrafields where field_name="'.$mezoNev.'"');
				   $res = $db->loadObject();
				   if ($res) $mezoLabel = $res->field_label;
			   }
			   $filterStr .= $mezoLabel.' '.
							 $rel.' '.
							 JRequest::getVar('ertek'.$i);
			}    
		}

		$db->setQuery('insert into #__tny_naplo
		(kapcs_id, lastaction, lastact_time, lastact_user_id, lastact_info)
		values
		(0,"export CSV-be","'.date('Y-m-d H:i:s').'","'.$user->id.'","'.$filterStr.'")');
		$db->query();
		
		$fp = fopen(JPATH_ROOT.'/tmp/export_'.$user->id.'.csv','w+');
		$darab = 0;
		$s = '';
		foreach ($items[0] as $fn => $fv) {
			$s .= $fn."\t";
		}
		fwrite($fp,$s."\n");
		for ($i=0; $i<count($items); $i++) {
			$s = '';
			foreach ($items[$i] as $fn => $fv) {
				$fv = str_replace("\t",' ',$fv);
				$fv = str_replace('"','\"',$fv);
				$fv = str_replace("\n",' ',$fv);
				if (is_numeric($fv))
					$s .= $fv."\t";
				else	
					$s .= '"'.$fv.'"'."\t";
			}
			fwrite($fp,$s."\n");
			$darab++;
		}
		fclose($fp);
		echo '
		<h2>Adat export CSV fájlba - eredmény</h2>
		<div class="lmpForm">
		<br /><br />
		<center>
		  <a href="'.JURI::root().'tmp/export_'.$user->id.'.csv">CSV file letöltése</a>
		  <br /><br />
		  <p>A CSV fájl '.$darab.' darab adatsort tartalmaz.</p>
		  <a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks">Vissza a kapcsolatok böngészéséhez</a>
		</center>
		<br /><br />
		</div>
		';
	}
	
	/**
	  * Üres CSV sablon file előállítása
	*/
	public function csvsablon() {
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',10000000);
		$db = JFactory::getDBO();
		$db->setQuery('select * from #__tny_kapcsolatok limit 1');
		$user = JFactory::getUser();
		$item = $db->loadObject();
		$fp = fopen(JPATH_ROOT.'/tmp/csvsablon.csv','w+');
		$s = '';
		foreach ($item as $fn => $fv) {
			$s .= $fn."\t";
		}
		fwrite($fp,$s."\n");
		fclose($fp);
		echo '
		<h2>CSV sablon adat importhoz (teljes adat tartalom)</h2>
		<div class="lmpForm">
		<br /><br />
		<center>
		  <a href="'.JURI::root().'tmp/csvsablon.csv">CSV file letöltése</a>
		  <br /><br />
		  <a href="index.php?option=com_tagnyilvantartas&view=kapcsolatoks">Vissza a kapcsolatok böngészéséhez</a>
		</center>
		<br /><br />
		</div>
		';
	}
	  
	
	/**
	  * hirlevél küldéshez hirlevél választás
	  * @JRequest szürés képernyő mezői
	  * @return void
	  */
	public function hirlevelselect() {
		$session = JFactory::getSession();
		$userTerhats = $session->get('userTerhats');
	    $userCsoport = $session->get('userCsoport');
		$db = JFactory::getDBO();
		
		// tábla létrehozás ha szükséges
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__hirlevel_queue (
		subid int(10),
		mailid mediumint(8),
		priority tinyint(3),
		startdate varchar(10)
		)
		');
		$db->query();
		
		// jogosultság ellenörzés
		if (($userCsoport->kod != 'MM') & ($userCsoport->kod != 'A') & ($userCsoport->kod != 'SM')) {
			$this->setError(JText::_('ACCES_DENIED'));
			$this->setRedirect('index.php');
			$this->redirect();
		}

		$view = $this->getView('doszures','html');

		$view->setLayout('hirlevelselect');
		$db->setQuery('select m.mailid, m.subject, count(hq.subid) utemezve
		from #__acymailing_mail m
		left outer join #__hirlevel_queue hq on hq.mailid = m.mailid 
		where published=1 and visible=1
		group by m.mailid, m.subject 
		order by mailid DESC');
		$hirlevelek = $db->loadObjectList();
		$view->set('Hirlevelek',$hirlevelek);
		$view->display();
	}
	
	/**
	  * keresés az acymailing subscriber táblában
	  * @param object kapccsolatok rekord
	  * @return integer
	  */
	protected function getSubId($item, $email) {
		$db = JFactory::getDBO();
		$result = 0;
		$db->setQuery('select subid
		from #__acymailing_subscriber
		where email='.$db->quote($email));
		$res = $db->loadObject();
		if ($res) {
			$result = $res->subid;
		} else {
			if ($email == $item->email)
			  $key = strToHex(1234 * '.$item->kapcs_id.');
		    else
			  $key = strToHex(1212 * '.$item->kapcs_id.');
			$db->setQuery('INSERT INTO #__acymailing_subscriber 
				(`subid`, 
				`email`, 
				`userid`, 
				`name`, 
				`created`, 
				`confirmed`, 
				`enabled`, 
				`accept`, 
				`ip`, 
				`html`, 
				`key`, 
				`confirmed_date`, 
				`confirmed_ip`, 
				`lastopen_date`, 
				`lastclick_date`, 
				`lastopen_ip`, 
				`lastsent_date`, 
				`source`
				)
				VALUES
				(0, 
				'.$db->quote($email).', 
				0, 
				'.$db->quote($item->nev1.' '.$item->nev2.' '.$item->nev3).', 
				'.time().', 
				'.time().', 
				1, 
				1, 
				"", 
				1, 
				'.$key.', 
				0, 
				"", 
				0, 
				0, 
				"", 
				0, 
				""
				);
			');
			$db->query();
			$result = $db->insertid();
		}
		return $result;
	}
	
	/**
	  * hirlevél küldés (beirás az acymailng que táblába)
	  * @JRequest szürő képernyő mezői, mailid, prioritas, sttartdatum
	  * @return void
	  */
	public function hirlevelsend() {
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',10000000);
		$model = $this->getModel('doszures');
		$user = JFactory::getUser();
		//$items = $model->getItems();
		$emailmezo = JRequest::getVar('emailmezo','email');
		$mailid = JRequest::getVar('mailid',0);
		$prioritas = JRequest::getVar('prioritas',3);
		$startDatum = JRequest::getVar('startDatum','');
		$startDatum = str_replace('.','-',$startDatum);
		$startDatum = str_replace('_','-',$startDatum);
		$startDatum = str_replace(' ','',$startDatum);
		$db = JFactory::getDBO();
		
		if ($mailid > 0) {
			/* kapcsolattáblában meglévő összes nem csillagozott email cim legyen meg a hírlevél subscriber táblában is! */
			$db->setQuery('INSERT IGNORE INTO #__acymailing_subscriber 
					(`subid`, `email`, `userid`, `name`, 
					`created`,`confirmed`, `enabled`, `accept`, `ip`, `html`, 
					`key`, `confirmed_date`, `confirmed_ip`, `lastopen_date`, `lastclick_date`, 
					`lastopen_ip`, 
					`lastsent_date`, 
					`source`
					)
			select 0, k.email,0, concat(k.nev1," ",k.nev2," ",k.nev3), 
				   UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1, 1, "", 1,
				   hex(1234 * k.kapcs_id), 0, "", 0, 0, "", 0, ""
			from #__tny_kapcsolatok k	   
			left outer join #__acymailing_subscriber s on s.email = k.email
			where s.subid is null and k.email <> "" and substr(k.email,1,1) <> "*"');
			$db->query();
			if ($db->getErrorNum() > 0) echo '<div class="errormsg">'.$db->getErrorMsg().'</div>';
			$db->setQuery('INSERT IGNORE INTO #__acymailing_subscriber 
					(`subid`, `email`, `userid`, `name`, 
					`created`,`confirmed`, `enabled`, `accept`, `ip`, `html`, 
					`key`, `confirmed_date`, `confirmed_ip`, `lastopen_date`, `lastclick_date`, 
					`lastopen_ip`, 
					`lastsent_date`, 
					`source`
					)
			select 0, k.email2,0, concat(k.nev1," ",k.nev2," ",k.nev3), 
				   UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1, 1, "", 1,
				   hex(1212 * k.kapcs_id), 0, "", 0, 0, "", 0, ""
			from #__tny_kapcsolatok k	   
			left outer join #__acymailing_subscriber s on s.email = k.email2
			where s.subid is null and k.email2 <> "" and substr(k.email2,1,1)<>"*"');
			$db->query();
			if ($db->getErrorNum() > 0) echo '<div class="errormsg">'.$db->getErrorMsg().'</div>';


			/* minden hírlevél (#__acymailing_mail record) legyen tagja az "1" -es listának */
			$db->setQuery('
			INSERT INTO #__acymailing_listmail
			SELECT 1, m.mailid
			FROM (SELECT DISTINCT m1.mailid
				  FROM #__acymailing_mail AS m1
				  LEFT OUTER JOIN #__acymailing_listmail AS lm ON lm.mailid=m1.mailid AND lm.listid=1
				  WHERE lm.mailid IS NULL 
			) AS m;
			');
			$db->query();

			/* minden #__acymailing_subscriber legyen tagja az "1" -es listának */
			$db->setQuery('
			INSERT INTO #__acymailing_listsub
			SELECT 1, s.subid, UNIX_TIMESTAMP(), NULL, 1
			FROM (SELECT DISTINCT s1.subid
				  FROM #__acymailing_subscriber AS s1
				  LEFT OUTER JOIN #__acymailing_listsub AS ls ON ls.subid=s1.subid AND ls.listid=1
				  WHERE ls.subid IS NULL 
			) AS s;
			');
			$db->query();
			
			/* a liratkozott email cimek legyenek csillagozva a kapcsolat táblában */
			$db->setQuery('update #__tny_kapcsolatok k, #__acymailing_subscriber s, #__acymailing_listsub ls
			set k.email = copcat("*",k.email)
			where s.email = k.email and ls.subid = s.subid and ls.subid = s.subid and 
			ls.onsubdate > 0 and substr(k.email,1,1) <> '*'
			');
					
			/* ha inditási dátumot nem adott meg akkor lehet az acymailing  que-ba belökdösni a feladatokat
			a doszures getQuery -el kapott sql-t subquerynek használva, kivéve aki leiratkozott:
			Ha van startDatum akkor a #__hirlevel_que -ba töltjük be
			*/
			if ($startDatum == '') {			
				$subQuery = $model->getListQuery();
				$db->setQuery('INSERT INTO #__acymailing_queue 
							(`senddate`, 
							`subid`, 
							`mailid`, 
							`priority`, 
							`try`, 
							`paramqueue`
							)
				select DISTINCT 0, s.subid, '.$mailid.', '.$prioritas.', 0, ""
				from ('.$subQuery.') k
				left outer join #__acymailing_subscriber s on s.email=k.'.$emailmezo.'
				left outer join #__acymailing_listsub ls on ls.subid = s.subid and ls.unsubdate > 0 and ls.listid = 1
				where substr(k.'.$emailmezo.',1,1) <> "*" and k.'.$emailmezo.' <> "" and ls.unsubdate is null
				');
				$db->query();
				if ($db->getErrorNum() == 0) 
					$this->setMessage('Hírlevél küldés elindítva. ');
			} else {
				$subQuery = $model->getListQuery();
				$db->setQuery('INSERT INTO #__hirlevel_queue 
							(`subid`, 
							`mailid`, 
							`priority`, 
							`startdate` 
							)
				select DISTINCT s.subid, '.$mailid.', '.$prioritas.', '.$db->quote($startDatum).'
				from ('.$subQuery.') k
				left outer join #__acymailing_subscriber s on s.email=k.'.$emailmezo.'
				left outer join #__acymailing_listsub ls on ls.subid = s.subid and ls.unsubdate > 0 and ls.listid = 1
				where substr(k.'.$emailmezo.',1,1) <> "*" and k.'.$emailmezo.' <> "" and ls.unsubdate is null
				');
				
				// DBG echo $db->getQuery(); 
				$db->query();
				if ($db->getErrorNum() == 0) 
					$this->setMessage('Hírlevél küldés beütemezve. ');
			}
			
			// kampány hirlevél esetén visszairás a kampany táblába
			$funkcio = JRequest::getVar('funkcio','');
			if (substr($funkcio,0,7) == 'kampany') {
				$kampany_id = substr($funkcio, 8,10);
				$db->setQuery('UPDATE #__tny_kampany
				SET hirlevel_id = '.$mailid.'
				WHERE id='.$db->quote($kampany_id));
				$db->query();
				//DBG echo $db->getQuery(); exit();
			}
			
			//+ 2017.06.09
			// hirlevél küldési feltételek tárolása
			$this->saveHirlevelFilter($mailid);
			//- 2017.06.09
			
			
			if ($db->getErrorNum() > 0) 
				echo '<div class="errormsg">'.$db->getErrorMsg().' funkcio='.$funkcio.'</div>';
		} else {
			$this->setError('hirlevelsend mailid is empty');
		}
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		$this->redirect();
		
		/* régi eljárás
		
		if ($mailid > 0) {
			$emails = array();
			foreach ($items as $item) {
				$email = $item->$emailmezo;
				if (($email != '') & (in_array($email, $emails)==false)) {
				  $subid = $this->getSubId($item, $email);
				  try {
				  $db->setQuery('INSERT INTO #__acymailing_queue 
					(`senddate`, 
					`subid`, 
					`mailid`, 
					`priority`, 
					`try`, 
					`paramqueue`
					)
					VALUES
					(0, 
					'.$subid.', 
					'.$mailid.', 
					'.$prioritas.', 
					0, 
					""
					);
				  ');
				  $db->query();
				  } // try
				  catch(Exception $e) {
					 ; // duplay key error 
				  }	
				  $emails[] = $item->email;
				}  
			} // for
			$this->setMessage('Hírlevél küldés elindítva.');
		} else {
			$this->setError('hirlevelsend mailid is empty');
		}
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		$this->redirect();
		*/
	}
	
	/**
	* hírlevél hozzákapcsolása kampányhoz
	* @JRequest string funcio
	* @JRequest integer mailid
	*/
	public function kampanykapcsolas() {
		$mailid = JRequest::getVar('mailid',0);
		$funkcio = JRequest::getVar('funkcio','');
		$db = JFactory::getDBO();
		if (substr($funkcio,0,7) == 'kampany') {
			$kampany_id = substr($funkcio, 8,10);
			$db->setQuery('UPDATE #__tny_kampany
			SET hirlevel_id = '.$mailid.'
			WHERE id='.$db->quote($kampany_id));
			$db->query();
			//DBG echo $db->getQuery(); exit();
			$this->setMessage('Hírlevél hozzákapcsolva a kampányhoz.');
			$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kampanys');
			$this->redirect();
		}
	}
	
	/**
	  * csoportos modositás form megjelenités
	  * OK gomb --> doszures->groupedit3()
	  * @JRequest filter mezők
	  * @return void
	  */
	public function groupedit2() {
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',10000000);

		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED'),'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
		}
		
	    $db = JFactory::getDBO();
		$model = $this->getModel('doszures');
		$user = JFactory::getUser();
		$items = $model->getItems();
        $pageNav = $model->getPagination();

	    // nem zárol másik user?
		$db->setQuery('select zarol_user_id 
		from #__tny_kapcsolatok
		where zarol_user_id > 0 and zarol_user_id <> '.$user->id);
		$res = $db->loadObject();
		if ($res) {
           echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_RECORD_LOCKED').'</div>';
		   $this->show();
		   return;
		}
	  
	    // rekord zárolás (összes rekord)
	    $db->setQuery('update #__tny_kapcsolatok
	    set zarol_user_id='.$user->id.',
	    zarol_time='.time());
	    if (!$db->query()) {
           $db->stderr();		  
		}
		
        $view = $this->getView('doszures','html');
	    $session = JFactory::getSession();
	    $userCsoport = $session->get('userCsoport');
        $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => 'jform'));
        $view->set('Item',$item);
        $view->set('Form',$form);

        $view->set('Items',$items);
        $view->set('model',$model);
		$view->set('Darab', count($items));
        $view->set('Pagination',$pageNav);

        $view->setLayout('groupedit');
        $view->display();
	}
	
	/**
	  * csoportos modositás végrehajtása  --> kapcsolatoks.browser
	  * @JRequest filter mezők, form mezők (ÜRES és 0 adatok esetén ez a mező nem modosul!)
	  * @return void
	  */
	public function groupedit3() {
	  $user = JFactory::getUser();

		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED'),'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
		}


	  $db = JFactory::getDBO();
	  // modositás végrehajtása	
	  $model = $this->getModel('doszures');
	  $jform = JRequest::getVar('jform');
	  $result = $model->groupSave($jform);
	  // zárolás feloldása
      $db->setQuery('update #__tny_kapcsolatok
	  set zarol_user_id=0,
	  zarol_time=0
	  where zarol_user_id='.$user->id);
	  if (!$db->query()) {
           $db->stderr();		  
	  }
	  // redirect
	  if ($result)
	     $this->setMessage(JText::_('COM_TAGNYILVANTARTAS_GROUPEDIT_SAVED'));
	  else
		 $this->setMessage($model->getError(),'error'); 
	  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
	  $this->redirect();
	}
	
	public function groupdel1() {
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED'),'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
		}
	    $db = JFactory::getDBO();
		$model = $this->getModel('doszures');
		$user = JFactory::getUser();
		JRequest::setVar('limitstart',0);
		JRequest::setVar('limit',100000);
		$items = $model->getItems();
        $pageNav = $model->getPagination();

	    // nem zárol másik user?
		$db->setQuery('select zarol_user_id 
		from #__tny_kapcsolatok
		where zarol_user_id > 0 and zarol_user_id <> '.$user->id);
		$res = $db->loadObject();
		if ($res) {
           echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_RECORD_LOCKED').'</div>';
		   $this->show();
		   return;
		}
	  
	    // rekord zárolás (összes rekord)
	    $db->setQuery('update #__tny_kapcsolatok
	    set zarol_user_id='.$user->id.',
	    zarol_time='.time());
	    if (!$db->query()) {
           $db->stderr();		  
		}
		
        $view = $this->getView('doszures','html');
	    $session = JFactory::getSession();
	    $userCsoport = $session->get('userCsoport');
        $form = JForm::getInstance('adminForm',  
                             JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/forms/kapcsolatok.xml',
                             array('control' => 'jform'));
        $view->set('Item',$item);
        $view->set('Form',$form);

        $view->set('Items',$items);
        $view->set('model',$model);
		$view->set('Darab', count($items));
        $view->set('Pagination',$pageNav);

        $view->setLayout('groupdel1');
        $view->display();
	}
	
	public function groupdel2() {
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$userCsoport = $session->get('userCsoport');
		if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED'),'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
		}
	    $db = JFactory::getDBO();
		$model = $this->getModel('doszures');
		$user = JFactory::getUser();

	    // nem zárol másik user?
		$db->setQuery('select zarol_user_id 
		from #__tny_kapcsolatok
		where zarol_user_id > 0 and zarol_user_id <> '.$user->id);
		$res = $db->loadObject();
		if ($res) {
           echo '<div class="errorMsg">'.JText::_('COM_TAGNYILVANTARTAS_RECORD_LOCKED').'</div>';
		   $this->show();
		   return;
		}
	  
	    // rekord zárolás (összes rekord)
	    $db->setQuery('update #__tny_kapcsolatok
	    set zarol_user_id='.$user->id.',
	    zarol_time='.time());
	    if (!$db->query()) {
           $db->stderr();		  
		}
		
		// törlés végrehajtása
		if ($model->groupdel() == false) {
		  $this->setMessage($model->getError);
		  $this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		  $this->redirect();
		}
		
	    // rekord zárolás feloldása (összes rekord)
	    $db->setQuery('update #__tny_kapcsolatok
	    set zarol_user_id=0,
	    zarol_time=0');
	    if (!$db->query()) {
           $db->stderr();		  
		}

		$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_DELETED'));
		$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
		$this->redirect();
	}

	//+ 2017.06.09
	/**
	* tárolja a hirlevél küldésre használt szürési feltételeket
	* @patram integer
	* @return void
	*/
	protected function saveHirlevelFilter($mailid) {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$filter = implode("\n",file(JPATH_ROOT.'/tmp/filter'.$user->id.'.ini'));
		$db->setQuery('CREATE TABLE IF NOT EXISTS #__hirlevelfilters (
			mailid integer,
			datum date,
			user_id integer,
			filter text	
		)
		');
		$db->query();
		$db->setQuery('INSERT INTO #__hirlevelfilters
		(mailid,datum,user_id,filter)
		values
		('.$db->quote($mailid).',"'.date('Y-m-d').'",'.$user->id.','.$db->quote($filter).')
		');
		$db->query();
	}
	//- 2017.06.09

	/**
	* hirlevél elküldési infok kiirása
	* @input mailid
	*/
	public function hirlevelinfo() {
		$mailid = JRequest::getVar('mailid',0);
		$model = $this->getModel();
        $view = $this->getView('doszures','html');
		$items = $model->getHirlevelInfo($mailid);
		$view->set('hirlevelinfok',$items);
		$view->setLayout('hirlevelinfo');
		$view->display();	
	}
}
