<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 	    GNU/GPL
* Telefonszám popup ablak kezelése 	
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');


include_once JPATH_COMPONENT.'/models/telpopup.php';

/**
 * TagnyilvantartasKategoriak Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerTelpopup extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'telpopup';
		$this->view_list = 'telpopups';
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
	public function getModel($name = 'Telpopup', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		//$model = parent::getModel($name, $prefix, $config);
		$model = new TagnyilvantartasModelTelpopup();
		return $model;
	}	
	
	/**
	* form kirajzolása (iframe -ben fut)
	* @JRequest kapcs_id, kampany_id
	*/
	public function popupform() {
		$session = JFactory::getSession();
		$input = JFactory::getApplication()->input;
		$kampany_id = $input->get('kampany_id');
		$kapcs_id = $input->get('kapcs_id');
		$token = $session->get('telpopup');
		if (JRequest::getVar($token) != 1) die('invalid token');
		$session->set('token',$token);
		
		$model = $this->getModel();
		$view = $this->getView('telpopup','html');
		$item = $model->getItem($kampany_id, $kapcs_id);
		$view->set('item',$item);
		
		// $_GET -ben érkező CSRtoken megkeresése
		$csrToken = '123';
		foreach ($_GET as $fn => $fv) {
			if ((strlen($fn) > 20) & ($fv == 1)) $csrToken = $fv;
		}
		$view->set('token',$csrToken);
		$view->setLayout('telpopup');
		$view->display();
	} 
	
	/**
	* kitöltött form adatok tárolása
	* @JRequest: form fields
	*/
	public function save() {
		$session = JFactory::getSession();
		$input = JFactory::getApplication()->input;
		$kampany_id = $input->get('kampany_id');
		$kapcs_id = $input->get('kapcs_id');
		$token = $session->get('telpopup');
		if (JRequest::getVar($token) != 1) die('invalid token');
		$session->set('token',$token);
		$model = $this->getModel();
		$model->save();
		echo '<script type="text/javascript">
			/* böngésző ablak frissitése */
			jQuery(function() {
				var op = window.parent.opener;
				if (op.document.forms.adminForm.task.value == "doszures.start") {
				  var t = op.document.getElementById("turelem2");
				  t.style.position="fixed";
				  t.style.zIndex = 99;
				  t.style.top="200px";
				  t.style.left="400px";
				  t.style.display="block";	
				  op.document.forms.adminForm.submit();
				} 
				window.parent.close();
				if (op.telszamPopupWin) {
					op.document.telszamPopupWin = false;
				}
			});
			</script>
			';
	}
	
	/**
	* Form adatok tárolása és Hírlevél újraküldés
	* párttag adatoknál szükség esetén modositási javaslat generálása
	* szimpatizánsoknál szükség esetén email cím modositása
	* @JRequest formfields (email)
	*/
	public function hirlevelsend() {
		$session = JFactory::getSession();
		$input = JFactory::getApplication()->input;
		$kampany_id = $input->get('kampany_id');
		$kapcs_id = $input->get('kapcs_id');
		$token = $session->get('telpopup');
		if (JRequest::getVar($token) != 1) die('invalid token');
		$session->set('token',$token);
		$model = $this->getModel();
		$model->sendMail();
		echo '<script type="text/javascript">
		alert("Hírlevél küldés betéve a levél küldési feladatok közé. A tényleges kiküldés 1-2 órán belül várható.")
		</script>
		';
	}
}// class
?>