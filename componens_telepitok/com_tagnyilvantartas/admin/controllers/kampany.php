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


function hex2str( $hex ) {
  return pack('H*', $hex);
}

function str2hex( $str ) {
  return array_shift( unpack('H*', $str) );
}

/**
 * TagnyilvantartasKampany Controller
 *
 * @package    Tagnyilvantartas
 * @subpackage Controllers
 */
class TagnyilvantartasControllerKampany extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'kampany';
		$this->view_list = 'kampanys';
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
	public function getModel($name = 'Kampany', $prefix = 'TagnyilvantartasModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function delete() {
		$input = JFactory::getApplication()->input;
		$cid = $input->get('cid','','array');
		$model = $this->getModel();
		$result = $model->delete($cid);
		if ($result == false)
			$this->setMessage($model->getError(),'error');
		else
			$this->setmessage('Adat törölve');
		$this->setRedirect(JURI::base().'index.php?option=com_tagnyilvantartas&view=kampanys');
		$this->redirect();
	}
	
	public function statisztika() {
		$input = JFactory::getApplication()->input;
		$kampany_id = $input->get('kampany_id');
		$model = $this->getModel();
		$statisztika = $model->getStatisztika($kampany_id);
		$view = $this->getView('kampany','html');
		$view->set('statisztika',$statisztika);
		$view->setLayout('statisztika');
		$view->display();
	}
	
	public function statexport() {
		$user = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$kampany_id = $input->get('kampany_id');
		$model = $this->getModel();
		$statisztika = $model->getStatisztika($kampany_id);
		$fp = fopen('../work/kampany_statisztika_'.$user->id.'.csv','w+');
		fwrite($fp,'"'.$statisztika->megnev.'";"";"";""'."\n");
		fwrite($fp,'"'.$statisztika->idopont.'";"";"";""'."\n");
		fwrite($fp,'"'.$statisztika->helyszin.'";"";"";""'."\n");
		foreach($statisztika->lines as $line) {
		  fwrite($fp,'"'.$line->info.'";"'.$line->kerdes.'";"'.$line->valasz.'";"'.$line->darab.'"'."\n");	
		}
		fclose($fp);
		?>
		<center>
		<h2><?php echo $statisztika->megnev; ?></h2>
		<h3>statisztika</h3>
		<br />
		<a href="<?php echo JURI::root().'work/kampany_statisztika_'.$user->id.'.csv'; ?>">CSV fájl letöltése</a>
		<br />
		</center>
		<?php
	}
	
	/**
	* kampany statisztika név lista
	* @request int kampany_id
	* @request base64_encoded info_kerdes_valasz data
	*/
	public function nevek() {
		$user = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$kampany_id = $input->get('kampany_id');
		$darab = $input->get('darab');
		$data = hex2str($input->get('data'));
		$w = explode('_',$data);
		$info = $w[0];
		$kerdes = $w[1];
		$valasz = $w[2];
		$model = $this->getModel();
		$kampany = $model->getItem($kampany_id);
		$items = $model->getNevek($kampany_id, $info, $kerdes, $valasz);
		$view = $this->getView('kampany','html');
		$view->set('kampany',$kampany);
		$view->set('info',$info);
		$view->set('kerdes',$kerdes);
		$view->set('valasz',$valasz);
		$view->set('items',$items);
		$view->setLayout('nevek');
		$view->display();
		
	}
}// class
?>