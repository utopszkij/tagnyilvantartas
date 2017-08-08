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
 * Kapcsolatok statisztika
 *
 * @package     Joomla.Administrator
 * @subpackage  Tagnyilvantartas
 */
class TagnyilvantartasControllerStatisztika extends JControllerAdmin {
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  TagnyilvantartasControllerkapcsolatoks
	 * @see     JController
	 */
	public function __construct($config = array()) 	{
		$this->view_list = 'statisztika';
		parent::__construct($config);
		
	}
	
	/**
	  * terszerv statisztika elkészítése és megjelenítése
	  * @Request string datumtol (opcionális)
	  * @Request string datumig (opcionális)
	  * @Request string tipus (opcionalis)
	  * @return void
	*/  
	public function show() {
		if (JRequest::getVar('tipus') === 'oevk') {
			$model = $this->getModel('statisztika');
			$view = $this->getView('statisztika','html');		
			$items = $model->getItemsOevk();
			$view->set('Items',$items);
			//$view->setLayout('defaultOevk');
			$view->display('oevk');
		} else {
			$model = $this->getModel('statisztika');
			$items = $model->getItems();
			$view = $this->getView('statisztika','html');		
			$items = $model->getItems();
			$view->set('Items',$items);
			$view->setLayout('default');
			$view->display();
		}
	}
	
	/**
	  * terszerv email statisztika elkészítése és megjelenítése
	  * @Request string datumtol (opcionális)
	  * @Request string datumig (opcionális)
	  * @return void
	*/  
	public function emailshow() {
			$model = $this->getModel('statisztika');
			$view = $this->getView('statisztika','html');		
			$items = $model->getItemsOevkEmail();
			$view->set('Items',$items);
			//$view->setLayout('defaultOevk');
			$view->display('oevkemail');
	}

	/**
	  * terszerv telefonszám statisztika elkészítése és megjelenítése
	  * @Request string datumtol (opcionális)
	  * @Request string datumig (opcionális)
	  * @return void
	*/  
	public function telszamshow() {
			$model = $this->getModel('statisztika');
			$view = $this->getView('statisztika','html');		
			$items = $model->getItemsOevkTelszam();
			$view->set('Items',$items);
			//$view->setLayout('defaultOevk');
			$view->display('oevktelszam');
	}

	
	/*
	public function execute($task) {
		$this->$task();
	}
	*/
}
