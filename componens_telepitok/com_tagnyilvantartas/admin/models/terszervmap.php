<?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:cimkek.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelCimkek 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelTerszervmap  extends JModelAdmin { 

    public $errorFields = array();
		
/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form. [optional]
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure

	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_tagnyilvantartas.terszervmap', 'terszervmap', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_tagnyilvantartas.edit.terszervmap.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_tagnyilvantartas.terszervmap', $data);
		}
		

		return $data;
	}
	/**
      * @return array|false
      * @param JForm
      * @param array
      * @param string
     */   
	public function validate(& $form, $data, $group = 'jform') {
	  $data = JRequest::getVar('jform');	
      $db = JFactory::getDBO();
	  if (($data['telepules'] != 'Budapest') & ($data['telepules'] != 'budapest')) $data['kerulet'] = '0';
	  $db->setQuery('select nev from #__tny_teruletiszervezetek where terszerv_id="'.$data['terszerv_id'].'"');
	  $res = $db->loadObject();
	  if ($res) $data['terszerv_nev'] = $res->nev;
	  
	  $id = 0;
      $cid = JRequest::getVar('cid');
      if (is_array($cid)) $id = $cid[0];
      
      $errorMsg = '';      
      $this->errorFields = array();
      if ($data['telepules']=='') {
          $errorMsg .= 'Település nem lehet üres';
          $this->errorFields[] = 'telepules';
      }
      $session = JFactory::getSession();
      if ($errorMsg != '') {
          $session->set('errorFields',$this->errorFields);     
          $this->setError($errorMsg);
          return false;
      } else {
          $session->set('errorFields',array());     
      }
      return $data;  
    }
    
	/**
	* egy rekord olvasása
	* @params string település|kerület
	*/	
	public function getItem($id='') {
		$w = explode('|',$id); 
		if ($w[0] != '') {
			$db = JFactory::getDBO();
			$db->setQuery('select * 
			from #__tny_terszerv_map 
			where telepules='.$db->quote($w[0]).' and
			kerulet = '.$db->quote($w[1]));
			return $db->loadObject();
		} else {
			return false;
		}
	}
	
	/**
	* egy rekord törlése
	* @params string település|kerület
	*/	
	public function remove($id='') {
		$w = explode('|',$id); 
		if ($w[0] != '') {
			$db = JFactory::getDBO();
			$db->setQuery('delete from #__tny_terszerv_map 
			where telepules='.$db->quote($w[0]).' and
			kerulet = '.$db->quote($w[1]));
			return $db->query();
		} else {
			return false;
		}
	}

	/**
	* egy rekord tárolása (mivel nincs id a rekordban csak insert lehet, update nem)
	* @params array
	*/	
	public function save($data) {
		if (($data['telepules'] != 'Budapest') & ($data['telepules'] != 'budapest'))  {
			$data['kerulet'] = '';
		}
		$db = JFactory::getDBO();
		$db->setQuery('insert into #__tny_terszerv_map 
		value('.$db->quote($data['telepules']).',
		'.$db->quote($data['kerulet']).',
		'.$db->quote($data['terszerv_nev']).',
		'.$db->quote($data['terszerv_id']).')');
		return $db->query();
	}
	
    /**
      * @return boolean
      * @param array of primary_keys
    */  
    public function delete($pks) {
		return $this->remove($pks);
    }
    
}
?>