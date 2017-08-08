   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:teruletiszervezetek.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelTeruletiszervezetek 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelTeruletiszervezetek  extends JModelAdmin { 

		
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
		$form = $this->loadForm('com_tagnyilvantartas.teruletiszervezetek', 'teruletiszervezetek', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = $app->getUserState('com_tagnyilvantartas.edit.teruletiszervezetek.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_tagnyilvantartas.teruletiszervezetek', $data);
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
      if ($data['nev']=='') {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_NO_EMPTY').'<br />';
          $this->errorFields[] = 'nev';
      }
      $db = JFactory::getDBO();
      $db->setQuery('select * 
                     from #__tny_teruletiszervezetek 
                     where nev = '.$db->quote($data['nev']).' and 
                           terszerv_id<>'.$db->quote($id));
      $res = $db->loadObject();
      if ($res) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_IS_DOUBLE').'<br />';
          $this->errorFields[] = 'nev';
          // DBG .$db->getQuery().'<br />';
      }  
      if (($id==$data['tulaj_id']) &
          ($data['tulaj_id']!= 0)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_TULAJ_ERROR').'<br />';
          $this->errorFields[] = 'tulaj_id';
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
       $db = JFactory::getDBO();
       foreach ($pks as $pk) {
           // ellenörzi, hogy a "$pk" rekord törölhetõ-?
           // ha nem ir az errorMessagbe
           // $errorMsg .= $pk.' '.JText::_('COM_TAGNYILVANTARTAS_CANNOT_DELETE').'<br />';
           $db->setQuery('select terszerv_id from #__tny_teruletiszervezetek where tulaj_id='.$db->quote($pk).' limit 1');
           $res = $db->loadObject();
           if ($res) {
              $errorMsg .= $pk.' '.JText::_('COM_TAGNYILVANTARTAS_CANNOT_DELETE').'<br />';
           } else {
             $db->setQuery('select kapcs_id from #__tny_kapcsolatok where terszerv_id='.$db->quote($pk).' limit 1');
             $res = $db->loadObject();
             if ($res) {
                $errorMsg .= $pk.' '.JText::_('COM_TAGNYILVANTARTAS_CANNOT_DELETE').'<br />';
             }  
           }
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