   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:felhcsoportok.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelFelhcsoportok 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelFelhcsoportok  extends JModelAdmin { 

		
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
		$form = $this->loadForm('com_tagnyilvantartas.felhcsoportok', 'felhcsoportok', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = $app->getUserState('com_tagnyilvantartas.edit.felhcsoportok.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_tagnyilvantartas.felhcsoportok', $data);
		}
		

		return $data;
	}
   	/**
      * @return array|false
      * @param JForm
      * @param array
      * @param string
     */   
	public function validate(& $form, & $data, $group) {
	  // CHECKBOXOK HIBÁS MÜKÖDÉSE MIATT	
	  if (!isset($data['jog_felhasznalok'])) $data['jog_felhasznalok']=0;
	  if (!isset($data['jog_terszerv'])) $data['jog_terszerv'] = 0;
	  if (!isset($data['jog_kategoriak'])) $data['jog_kategoriak'] = 0;
	  if (!isset($data['jog_kapcsolat'])) $data['jog_kapcsolat'] = 0;
	  if (!isset($data['jog_cimkek'])) $data['jog_cimkek'] = 0;
	  if (!isset($data['jog_csoportos'])) $data['jog_csoportos'] = 0;
	  if (!isset($data['jog_hirlevel'])) $data['jog_hirlevel'] = 0;
	  if (!isset($data['jog_csv'])) $data['jog_csv'] = 0;     
	  
	  //DBG foreach ($data as $fn => $fv) echo 'falhcsoportok validate data '.$fn.'='.$fv.'<br>'; exit();
      
	  $id = 0;
      $cid = JRequest::getVar('cid');
      if (is_array($cid)) $id = $cid[0];
      
      $errorMsg = '';      
      $this->errorFields = array();
      if ($data['nev']=='') {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_NO_EMPTY').'<br />';
          $this->errorFields[] = 'nev';
      }
      if ($data['kod']=='') {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_NO_EMPTY').'<br />';
          $this->errorFields[] = 'kod';
      }
      
      $db = JFactory::getDBO();
      $db->setQuery('select * 
                     from #__tny_felhcsoportok 
                     where kod = '.$db->quote($data['kod']).' and 
                           fcsop_id<>'.$db->quote($id));
      $res = $db->loadObject();
      if ($res) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_IS_DOUBLE').'<br />';
          $this->errorFields[] = 'kod';
          // DBG .$db->getQuery().'<br />';
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
           $db->setQuery('select kapcs_id from #__tny_felh_terhat where fcsoport_id='.$db->quote($pk).' limit 1');
           $res = $db->loadObject();
           if ($res) {
              $errorMsg .= $pk.' '.JText::_('COM_TAGNYILVANTARTAS_CANNOT_DELETE').'<br />';
           }
       }
       if ($errorMsg == '') {
           return parent::delete();
       } else {      
         $this->setError($errorMsg); 
         return false; 
       }  
    }    	
}
?>