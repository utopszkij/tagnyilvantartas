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
 
class TagnyilvantartasModelFelh_terhat  extends JModelAdmin { 

		
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
      * területi hatókör rekordok elérése
      * @param integer user_id
      * @param integer fcsop_id
      * @return array of terszerv rekordok vagy false  
    */  
	public function getItems($user_id, $felcsop_id) {
        $result = array();
        $db = JFactory::getDBO();
        $db->setQuery('select distinct ter.*
        from #__tny_felh_terhat t
        inner join #__tny_felhcsoportok fcs on fcs.fcsop_id = t.fcsoport_id
        inner join #__tny_teruletiszervezetek ter on ter.terszerv_id = t.terszerv_id
        where t.felh_id ='.$db->quote($user_id).' and t.fcsoport_id='.$db->quote($felcsop_id).'
        order by ter.nev');
        $result = $db->loadObjectList();
        if (count($result)==0) $result = false;
        return $result;
    }
    
    /** user felhasználói csoportjainak elérése
      * @param integer user_id
      * @return array of felhcsoport rekokordok vagy false
      */
    public function getCsoportok($user_id) {
      $result = array(); 
      $db = JFactory::getDBO();
      $db->setQuery('select distinct fcs.*
      from #__tny_felh_terhat t
      inner join #__tny_felhcsoportok fcs on fcs.fcsop_id = t.fcsoport_id
      where t.felh_id ='.$db->quote($user_id).'
      order by fcs.kod');
      $result = $db->loadObjectList();
      if (count($result)==0) $result = false;
      return $result;
    }
}
?>