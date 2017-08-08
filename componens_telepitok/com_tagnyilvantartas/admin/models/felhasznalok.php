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
 * TagnyilvantartasModelFelhasznalok 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class TagnyilvantartasModelFelhasznalok  extends JModelAdmin { 

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
		return false;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		return false;
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
      * getItem  
      * @return {"user_id" : '',
      *          "user_name" : '',
      *          "user_username" : '',
      *          "user_email" : '',
      *          "terhats" :["fcsop" => ["terszerv, terszerv, ....]]}   (fcsop: id, terszerv: id)
      *          "commentemails" ["fcsop" => 1|0]
      */                        
    public function getItem($id = 0) {
        
       //echo 'felhasznalokModel getItem <br>'; 
       //foreach ($_POST as $fn => $fv) echo 'POST '.$fn.'='.$fv.'<br>';
       //foreach ($_GET as $fn => $fv) echo 'GET '.$fn.'='.$fv.'<br>';
       if ($id=='') $id = JRequest::getVar('id'); 
        
       $result = false;
       // user rekord elérése
       $result = new stdclass();
       $db = JFactory::getDBO();
       $db->setQuery('select * from #__users where id='.$db->quote($id));
       $result = $db->loadObject();
       if ($result) {
         $result->terhats = array();
         $result->commentemails = array();
         $result->user_id = $result->id;
         // terhats rekordok elérése --> $terhats
         $db->setQuery('select * from #__tny_felh_terhat where felh_id='.$db->quote($id).' order by fcsoport_id');
         $terhats = $db->loadObjectList();
         $fcsop = 0;
         foreach ($terhats as $terhat) {
           if ($terhat->fcsoport_id != $fcsop) {
             $fcsop = $terhat->fcsoport_id;
             $result->terhats[$fcsop] = array();
           }  
           $result->terhats[$fcsop][] = $terhat->terszerv_id;
           $result->commentemails[$fcsop] = $terhat->commentemail;
         }
       }  
       return $result;
    }
    
    
    /**
      * @param array (form array) CSAK MÓDOSÍTÁS felvitel nincs
      *  user_id, user_name, user_username, user_email, user_psw, user_psw2
      *  fcsop_1, terszerv_fcsop1_1, terszerv_fcsop1_2,......  
      *  fcsop_2, terszerv_fcsop2_1, terszerv_fcsop2_2,......  
      * 
      *  fcsop_1, fcsop_2 ... tartalma felhcsop_id
      *  terszerv_###_1, terszerv_###_2 ... tartalma tervesz_id  (### fcsop_id)
      *  hilrlevelemail_####   (### fcsop_id)
      * @return true vagy false
      */
    public function save($data) {
		
        //DBG echo 'model save rutin ';
        //DBG echo '<pre>'; print_r($data); echo '</pre>';       
        
	   $manager = false;
       $hirlevelKezelo = false;       
       $db = Jfactory::getDBO();
       // user_terhat rekordok törlése
       $db->setQuery('delete from #__tny_felh_terhat where felh_id='.$db->quote($data['user_id']));
       if ($db->query()) {
         // user terhat rekordok tárolása
         for ($fcsi = 1; $fcsi < 20; $fcsi++) {
           // van "fcsop_$fcsi" adat a $data -ban?
           if (isset($data["fcsop_$fcsi"])) {
			   $fcsop = $data["fcsop_$fcsi"];
               // fcsop rekord beolvasása
               $db->setQuery('select * from #__tny_felhcsoportok where fcsop_id='.$db->quote($fcsop));
               $felhCsop = $db->loadObject();
               if ($felhCsop->jog_felhasznalok == 1) $manager = true;
               if ($felhCsop->jog_hirlevel == 1) $hirlevelKezelo = true;
               //DBG echo '<p>Van fcsop '.$fcsi.' fcsop='.$fcsop.'</p>';
               for ($fthi = 1; $fthi < 200; $fthi++) {
                 // ha van "terszerv_$fcsop_$thi" adat a tömbben akkor rekord kiirás
                 if (isset($data['terszerv_'.$fcsop.'_'.$fthi])) {
                     //DBG echo '<p>van terszerv adat </p>';
                     $db->setQuery('insert into #__tny_felh_terhat
                     values ('.$db->quote($data['user_id']).',
                             '.$db->quote($fcsop).',
                             '.$db->quote($data['terszerv_'.$fcsop.'_'.$fthi]).',
                             '.$db->quote($data['commentemail_'.$fcsop]).'
                            )');
                     //DBG echo '<p>'.$db->getQuery().'</p>';       
                     if (!$db->query()) {
                        $this->setError($db->getErrorMsg());
                        return false;
                     }        
                 } 
               }    
           }    
         }
       } else {
           $this->setError($db->getErrorMsg());
           return false;
       } 

       // Joomla usergroup besorolás kezelése
       // 10 usergroupba mindenképpen tartozik   
       // ha hírlevél kezelõ akkor 11 usergroupba is tartozik
       // ha felhasználó kezelõ akkor 6 usergroupba is tartozik
       $db->setQuery('delete from #__user_usergroup_map where user_id='.$db->quote($data['user_id']).' and (group_id=6 or group_id=11 or group_id=10)');
       $db->query();
       $db->setQuery('insert into #__user_usergroup_map values ('.$db->quote($data['user_id']).',10)');
       $db->query(); 
       if ($manager) {
           $db->setQuery('insert into #__user_usergroup_map values ('.$db->quote($data['user_id']).',6)');
           $db->query(); 
       }
       if ($hirlevelKezelo) {
           $db->setQuery('insert into #__user_usergroup_map values ('.$db->quote($data['user_id']).',11)');
           $db->query(); 
       }
       return true;
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
           // felh_terhat rekordok törlése
           
           // törlés a user táblából és group map bõl
           
       } else {      
         $this->setError($errorMsg); 
         return false; 
       }  
    }
}
?>