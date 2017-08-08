   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:oevk.php  1 2016-06-21
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelOevk 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelOevk  extends JModelAdmin { 

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
		$form = $this->loadForm('com_tagnyilvantartas.oevk', 'oevk', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = $app->getUserState('com_tagnyilvantartas.edit.oevk.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_tagnyilvantartas.oevk', $data);
		}
		

		return $data;
	}
	/**
      * @return array|false
      * @param JForm
      * @param array
      * @param string
     */   
	public function validate(& $form, $data, $group) {
      //DBG foreach ($data as $fn => $fv) echo 'data '.$fn.'='.$fv.'<br>'; exit();
      $id = 0;
      $cid = JRequest::getVar('cid');
      if (is_array($cid)) $id = $cid[0];
      
      $errorMsg = '';      
      $this->errorFields = array();
      if ($data['OEVK']=='') {
          $errorMsg .= 'OEVK nem lehet üres<br />';
          $this->errorFields[] = 'OEVK';
      }
      if ($data['telepules']=='') {
          $errorMsg .= 'Település név nem lehet üres<br />';
          $this->errorFields[] = 'telepules';
      }
      if ($data['kozterulet']=='') {
          $errorMsg .= 'Közterület név nem lehet üres<br />';
          $this->errorFields[] = 'kozterulet';
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
      * @return boolean
      * @param array of primary_keys
    */  
    public function delete($pks) {
       $errorMsg = '';
       if (count($pks) > 1) {
         $this->setError(JText::_('COM_TAGNYILVANTARTAS_SELECT_ONLY_ONE')); 
         return false; 
       }
       foreach ($pks as $pk) {
           // ellenörzi, hogy a "$pk" rekord törölhetõ-?
           // ha nem ir az errorMessagbe
           // $errorMsg .= $pk.' '.JText::_('COM_TAGNYILVANTARTAS_CANNOT_DELETE').'<br />';
       }
       if ($errorMsg == '') {
           return parent::delete($pks);
       } else {      
         $this->setError($errorMsg); 
         return false; 
       }  
    }
    
}
?>