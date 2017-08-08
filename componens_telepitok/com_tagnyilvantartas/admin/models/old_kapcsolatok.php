   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:kapcsolatok.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
 defined('_JEXEC') or die('Restricted access');
/**
 * TagnyilvantartasModelKapcsolatok 
 * @author 
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 

include_once 'components/com_tagnyilvantartas/models/felh_terhat.php';
 
class TagnyilvantartasModelKapcsolatok  extends JModelAdmin { 

	protected function chkMail($email) {
		if ($email == '') return true;
		$isValid = true;
		$atIndex = strrpos($email, '@');
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex + 1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
			   $isValid = false;
			} elseif ($domainLen < 1 || $domainLen > 255) {
				$isValid = false;
			} elseif ($local[0] == '.' || $local[$localLen-1] == '.') {
				$isValid = false;
			} elseif (preg_match('/\\.\\./', $local)) {
				$isValid = false;
			} elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				$isValid = false;
			} elseif (preg_match('/\\.\\./', $domain)) {
				$isValid = false;
			} elseif (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace('\\\\','',$local))) {
				if (!preg_match('/^”(\\\\”|[^"])+”$/', str_replace('\\\\','',$local))) {
					$isValid = false;
				}
			}
			/*
			if ($isValid && !(checkdnsrr($domain, ”MX”) || checkdnsrr($domain, 'A'))) {
				$isValid = false;
			}
			*/
		}
		return $isValid;
	}
	
	protected function chkNev ($nev,$maxSzo) {
		if ($nev=='') return true;
		$jo = true; 
        $nevek = explode(' ',$nev);
		foreach ($nevek as $nev1) {
			if (!preg_match("/^[a-zA-ZöüóőúéáűíÖÜÓŐÚÉÁŰÍ\-.\']{2,60}$/i",$nev1)){
                 $jo = false;
            }
		}
		if (count($nevek) > $maxSzo) {
			$jo = false;
		}
		return $jo;
    }
	
	protected function chkUtca ($nev,$maxSzo) {
		if ($nev=='') return true;
		$jo = true; 
        $nevek = explode(' ',$nev);
		foreach ($nevek as $nev1) {
			if (!preg_match("/^[a-zA-ZöüóőúéáűíÖÜÓŐÚÉÁŰÍ0123456789\-.\']{2,60}$/i",$nev1)){
				$jo = false;
			}
		}
		if (count($nevek) > $maxSzo) {
			$jo = false;
		}
		return $jo;
    }
		
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
		$form = $this->loadForm('com_tagnyilvantartas.kapcsolatok', 'kapcsolatok', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = $app->getUserState('com_tagnyilvantartas.edit.kapcsolatok.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_tagnyilvantartas.kapcsolatok', $data);
		}
		

		return $data;
	}
	
    /**
      * rekord elérés területi illetékesség ellenörzéssel
	  * figyelem az edit paraméter nélkül hivja! valahonnan máshonnan
	  * tudja meg mit kell meolvasni
      */
    public function	getItem($id=0) {
        $result = parent::getItem($id);
		if ($id == 0) {
			$result->kapcs_id = 0; 
			$result->email = ''; 
			$result->nev1 = ''; 
			$result->nev2 = ''; 
			$result->nev3 = ''; 
			$result->titulus = ''; 
			$result->nem = ''; 
			$result->email2 = ''; 
			$result->telefon = ''; 
			$result->irsz = ''; 
			$result->telepules = ''; 
			$result->kerulet = 0; 
			$result->utca = ''; 
			$result->kjelleg = ''; 
			$result->hazszam = 0; 
			$result->cimkieg = ''; 
			$result->tirsz = ''; 
			$result->ttelepules = ''; 
			$result->tkerulet = 0; 
			$result->tutca = ''; 
			$result->tkjelleg = ''; 
			$result->thazszam = 0; 
			$result->tcimkieg = ''; 
			$result->oevk = ''; 
			$result->szev = 0; 
			$result->kapcsnev = ''; 
			$result->kapcsid = 0; 
			$result->kapcsdatum = ''; 
			$result->kategoria_id = 3;  // szimpatizáns 
			$result->terszerv_id = 1;   // LMP párt 
			$result->cimkek = ''; 
			$result->belsoemail = ''; 
			$result->hirlevel = 0; 
			$result->ellenorzott = 0; 
			$result->zarol_user_id = 0; 
			$result->zarol_time = 0; 
			$result->lastaction = ''; 
			$result->lastact_user_id = 0; 
			$result->lastact_time = 0; 
			$result->lastact_info = ''; 
			$result->megjegyzes = '';
		}
        // területi hatáskör ellenörzés
        $session = JFactory::getSession();
        $userTerhats = $session->get('userTerhats');
        $jo = false;
        foreach ($userTerhats as $userTerhat) {
            if (($userTerhat->terszerv_id = $result->terszerv_id) | ($result->terszerv_id == 0)) $jo = true;
        }
        if ($jo == false) {
           // $this->setError(JText::_('COM_TAGNYILVANTARTAS_NO_TERHAT')); Valamiért duplán írja ki ?????
           // return false;
           return new stdClass();        
        }
		if ($result->kapcs_id == 0) {
			$result->ellenorzott = 1;
			$result->hirlevel = 1;
		}
        return $result;
    }

	/**
      * telefonszám ellenörző
      * @param string  telefonszám
      * @param string  hibaüzenet
	  * @return boolean
	*/	
	protected function telefonValidator($tel, &$errorMsg, $mobilKorzetek, $tiltottKorzetek) {  
		$tel = trim($tel);
		$result = true;
		if ($tel == '') return $result;
		if (substr($tel,0,2)=='06') $tel = '36'.substr($tel,2,20);
		  $s = substr($tel,0,4).' '.$tel;
		  if (substr($tel,0,3)=='361') {
		  if (strlen($tel) != 10) {
            $errorMsg .= 'Budapesti szám nem jó a hossza<br />';
			$result = false;
		  }
		} else if (in_array(substr($tel,0,4), $mobilKorzetek)) {
		  if (strlen($tel) != 11) {
            $errorMsg .= 'Mobil szám, nem jó a hossza '.$tel.'<br />';
			$result = false;
		  }
		} else if (in_array(substr($tel,0,4), $tiltottKorzetek)) {
            $errorMsg .= 'Tiltott körzetszám '.$tel.'<br />';
			$result = false;
		} else if (substr($tel,0,2)=='36') {
		  if (strlen($tel) != 10) {
            $errorMsg .= 'Vidéki szám, nem jó a hossza '.$tel.'<br />';
			$result = false;
		  }
		} else {
		  if (strlen($tel) < 8) {
            $errorMsg .= 'Külföldi szám, túl rövid<br />';
			$result = false;
		  }
		}
		return $result;
	} 

	/**
      * @return array|false
      * @param JForm
      * @param array
      * @param string
     */   
	public function validate(& $form, &$data, $group='') {
      $session = JFactory::getSession();
      saveAccess($session->get('userCsoport'),$data);
	  $aktEv = (int)date('Y');
	  $db = JFactory::getDBO();
	  $db->setQuery('select * from #__tny_extrafields order by field_id');
	  $extraFields = $db->loadObjectList();
	  
	  $mobilKorzetek = array(3620,3621,3630,3631,3670,);
	  $tiltottKorzetek = array(3640,3650,3651,3655,3660,3680,3690,3691);
	  
	  //DBG foreach ($data as $fn => $fv) echo 'validate data '.$fn.'='.$fv.'<br>';
	  	  
	  $id = 0;
      $cid = JRequest::getVar('cid');
      if (is_array($cid)) $id = $cid[0];
	  if ($id == '') $id = 0;
      
	  foreach ($data as $fn => $fv) {
		  $data[$fn] = trim($fv);
	  }
	  $orig = $this->getItem($id);
	  foreach ($orig as $fn => $fv) {
		  if (!isset($data[$fn])) $data[$fn] = $fv;
	  }
	  
      $errorMsg = '';      
      $this->errorFields = array();
      if ($data['nev1']=='') {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_NO_EMPTY').'<br />';
          $this->errorFields[] = 'nev1';
      }
      if ($data['terszerv_id']<=0) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_TERSZERV_NO_EMPTY').'<br />';
          $this->errorFields[] = 'nev1';
      }
      if ($data['kategoria_id']<=0) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KATEGORIA_NO_EMPTY').'<br />';
          $this->errorFields[] = 'nev1';
      }
	  
	  
	  if (!$this->chkMail($data['email'])) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EMAIL_WRONG').'<br />';
          $this->errorFields[] = 'email';
	  }   
	  if (!$this->chkMail($data['email2'])) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EMAIL_WRONG').' (email2)<br />';
          $this->errorFields[] = 'email2';
	  }   
	  if (!$this->chkMail($data['belsoemail'])) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EMAIL_WRONG').' (belső email)<br />';
          $this->errorFields[] = 'belsoemail';
	  }   
	  if (!$this->chkNev($data['nev1'],1)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - vezetéknév<br />';
          $this->errorFields[] = 'nev1';
	  }
	  if (!$this->chkNev($data['nev2'],1)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - középső név<br />';
          $this->errorFields[] = 'nev2';
	  }
	  if (!$this->chkNev($data['nev3'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - utónév<br />';
          $this->errorFields[] = 'nev3';
	  }
	  if (!$this->chkNev($data['telepules'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - település<br />';
          $this->errorFields[] = 'telepules';
	  }
	  if (!$this->chkNev($data['ttelepules'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - település<br />';
          $this->errorFields[] = 'ttelepules';
	  }
	  if (!$this->chkUtca($data['utca'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - közterület neve<br />';
          $this->errorFields[] = 'utca';
	  }
	  if (!$this->chkUtca($data['tutca'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - közterület neve<br />';
          $this->errorFields[] = 'utca';
	  }
      if (($data['szev'] != '') & ($data['szev'] != 0) &
  	      (($data['szev'] < ($aktEv - 110)) | ($data['szev'] > ($aktEv - 15)))) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_SZEV_WRONG').'<br />';
          $this->errorFields[] = 'szev';
	  } 	
      if (($data['kapcsdatum'] != '') & (($data['kapcsdatum'] < '2000') | ($data['kapcsdatum'] > '2100'))) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KAPCSDATUM_WRONG').'<br />';
          $this->errorFields[] = 'szev';
	  } 
	  if (($data['telepules'] != 'Budapest') & ($data['kerulet'] != 0)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_NOBP').'<br />';
          $this->errorFields[] = 'kerulet';
	  }	
	  if (($data['ttelepules'] != 'Budapest') & ($data['tkerulet'] != 0)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_NOBP').'<br />';
          $this->errorFields[] = 'tkerulet';
	  }	
	  if ((int)$data['kerulet'] > 22) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_BIG').'<br />';
          $this->errorFields[] = 'kerulet';
	  }	
	  if ((int)$data['tkerulet'] > 22) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_BIG').'<br />';
          $this->errorFields[] = 'kerulet';
	  }	
	  if (($data['irsz'] != '') & ($data['kerulet'] != 0) & 
	      (substr($data['irsz'],1,2) != $data['kerulet'])) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_WRONG').'<br />';
          $this->errorFields[] = 'kerulet';
	  } 
	  if (($data['tirsz'] != '') & ($data['tkerulet'] != 0) & 
	      (substr($data['tirsz'],1,2) != $data['tkerulet'])) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_WRONG').'<br />';
          $this->errorFields[] = 'tkerulet';
	  } 
	  if (($data['irsz']!='') & ($data['kerulet'] != 0) & (substr($data['irsz'],0,1)!='1')) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_WRONG').'<br />';
          $this->errorFields[] = 'tkerulet';
	  }
	  if (($data['tirsz']!='') & ($data['tkerulet'] != 0) & (substr($data['tirsz'],0,1)!='1')) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_WRONG').'<br />';
          $this->errorFields[] = 'tkerulet';
	  }
	  
	  // dátum ellenörzés
	  $s = $data['kapcsdatum'];
	  $s = str_replace('.','-',$s);
	  $s = str_replace('/','-',$s);
	  $s = str_replace(' ','-',$s);
	  $data['kapcsdatum'] = $s;
	  if ($s != '') {
        $d = DateTime::createFromFormat('Y-m-d', $s);
        if (($d) && ($d->format('Y-m-d') == $s)) {
		  $joDate = true;	
		} else {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_WRONG_DATE').' '.$s.'<br />';
          $this->errorFields[] = 'kapcsdatum';
		  $data['kapcsdatum'] = '';
		  $joDate = false;
		}
	  }

	  if ($joDate == false) {
		  $data['kapcsdatum'] = '';
	  }
	  
	  // telefonszám ellenörzés
	  if ($data['telefon'] != '') {
		  $data['telefon'] =stdTelefonszam($data['telefon']);
		  $w = explode(',', $data['telefon']);
		  foreach ($w as $tel) {
			if (!$this->telefonValidator($tel, $errorMsg, $mobilKorzetek, $tiltottKorzetek))
              $this->errorFields[] = 'telefon';
		  }
	  }
	  
	  // extrafields -ek ellenörzése
	  foreach ($extraFields as $extraField) {
		  if ($extraField->field_type == 'phone') {
		    $data[$extraField->field_name] =stdTelefonszam($data[$extraField->field_name]);
			if (!$this->telefonValidator($data[$extraField->field_name], $errorMsg, $mobilKorzetek, $tiltottKorzetek))
              $this->errorFields[] = $extraField->field_name;
		  }
		  if ($extraField->field_type == 'email') {
			  if (!$this->chkMail($data[$extraField->field_name])) {
				  $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_EMAIL_WRONG').'<br />';
				  $this->errorFields[] = $extraField->field_name;
			  }   
		  }
		  if ($extraField->field_type == 'integer') {
			  if (0 + $data[$extraField->field_name] != $data[$extraField->field_name]) {
				  $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_INTEGER_WRONG').'<br />';
				  $this->errorFields[] = $extraField->field_name;
			  }
		  }
	  }
	  
      if ($errorMsg != '') {
          $session->set('errorFields',$this->errorFields);     
          // $this->setError($errorMsg);
          JFactory::getApplication()->enqueueMessage($errorMsg, 'error');		  
          return false;
      } else {
          $session->set('errorFields',array());     
      }
      return $data;  
    }


  
    /**
      * Adat tárolás területi hatáskör ellenörzéssel
      */
    public function save($data) {
        $session = JFactory::getSession();
		$user = JFactory::getUser();
        $userTerhats = $session->get('userTerhats');
        $userCsoport = $session->get('userCsoport');
        $felh_terhatModel = new tagnyilvantartasModelFelh_terhat();
        $userTerhats = $felh_terhatModel->getItems($user->id, $userCsoport->fcsop_id);
		$session->set('userTerhats',$userTerhats);
		$db = JFactory::getDBO();
		$ujRekord = false;
		if (!isset($data['lastact_info'])) $data['lastact_info'] = '';
 
		// 2015.07.27 megbeszélés alapján
		if (($data['tirsz'] == '') &
		    ($data['ttelepules'] == '') &
		    ($data['tkerulet'] == 0) &
		    ($data['tutca'] == '') &
		    ($data['thazszam'] == 0) &
		    ($data['tkjelleg'] == '') &
		    ($data['tcimkieg'] == '')) {
		  $data['tirsz'] = $data['irsz'];
		  $data['ttelepules'] = $data['telepules'];
		  $data['tkerulet'] = $data['kerulet'];
		  $data['tutca'] = $data['utca'];
		  $data['thazszam'] = $data['hazszam'];
		  $data['tkjelleg'] = $data['kjelleg'];
		  $data['tcimkieg'] = $data['cimkieg'];
		}
		
		
        // modositás esetén el kell érni a meglévő rekordot is,
		// hatáskör ellenörzéshez és a letiltott mező értékek megörzéséhez
		$orig = false;
		if ($data['kapcs_id'] > 0) {
		  $db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$data['kapcs_id']);	 
          $orig = $db->loadObject();
		  if ($orig == false) {
			 // üres rekord felvitele
			 $db->setQuery('insert into #__tny_kapcsolatok (kapcs_id) values ('.$data['kapcs_id'].')');	
			 $db->query();
			 $ujRekord = true;
		  }
        } else {
			$ujRekord = true;
		}
 
		// amelyik mező adat nem jött a $data -ban az maradjon az $origban lévő
		if ($orig) {
			foreach ($orig as $fn => $fv) {
				if (!isset($data[$fn])) $data[$fn] = $fv;
			}
		}
		//DBG foreach ( $data as $fn => $fv) echo $fn.'='.$fv.'; ';
		//DBG echo '<br><br>';
		
		foreach ($data as $fn => $fv) {
		  if (is_string($fv))  $data[$fn] = trim($fv);
		}

        // adat telefonszám standardizálás
		$data['telefon'] =stdTelefonszam($data['telefon']);
		
		/* 2015.07.31 megbeszélés, nem változtatunk a kis/nagy betükön
		$data['nev1'] = mb_ucfirst(mb_strtolower($data['nev1'],'utf8'));
		$data['nev2'] = mb_ucfirst(mb_strtolower($data['nev2'],'utf8'));
		$data['nev3'] = mb_ucfirst(mb_strtolower($data['nev3'],'utf8'));
		$data['telepules'] = mb_ucfirst(mb_strtolower($data['telepules'],'utf8'));
		$data['ttelepules'] = mb_ucfirst(mb_strtolower($data['ttelepules'],'utf8'));
		*/
		
        // uj terszerv a területi hatáskörben ?
        $jo = false;
        foreach ($userTerhats as $userTerhat) {
			echo 'ellenörzö ciklus '.$userTerhat->terszerv_id.' ? '.$data['terszerv_id'].'<br>';
			
            if ($userTerhat->terszerv_id == $data['terszerv_id']) $jo = true;
        }
        
				
		/*
		// orig terszerv a területi hatáskörben ?
		if (($jo) & ($orig)) {
          $jo = false;
          foreach ($userTerhats as $userTerhat) {
            if ($userTerhat->terszerv_id == $orig->terszerv_id) $jo = true;
          }
        }
		*/
		
        // modosithatja a nevet?
        if ($userCsoport->jog_kapcsolat != 1) $jo = false;
        if ($userCsoport->jog_nev != 'RW') $jo = false;
		
        
        if ($jo) {
          $data['lastact_user_id'] = $user->id;
          $data['lastact_time'] = date('Y-m-d H:i');
		  
		  if ($ujRekord) 
			  $data['lastaction'] = 'INSERT';
		  else if ($data['kapcs_id'] > 0)
			  $data['lastaction'] = 'UPDATE';
		  else
			  $data['lastaction'] = 'INSERT';
		  
		  $result = parent::save($data);
		  //DBG echo 'save 3 (parent::save után) <br>';
		  if ($result) {
              // naplózás
			  $id = $data['kapcs_id'];
			  if ($id <= 0) {
				 $id = $this->getDbo()->insertid(); 
			  }
              $db->setQuery('insert into #__tny_naplo
			  select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($id));
			  $db->query();
          }
          return $result;
        } else {
          $this->setError(JText::_('COM_TAGNYILVANTARTAS_NO_TERHAT').' terszerv_id='.$data['terszerv_id']);
          return false;  
        }
    }
    
    /**
     *    adat törlés területi hatáskör ellenörzéssel
     */
    public function delete($pks) {
      if (count($pks) > 1) {
         $this->setError(JText::_('COM_TAGNYILVANTARTAS_SELECT_ONLY_ONE')); 
         return false; 
      }
      $user = JFactory::getUser();
      $result = false;
      $session = JFactory::getSession();
      $userTerhats = $session->get('userTerhats');
      $userCsoport = $session->get('userCsoport');
      $felh_terhatModel = new tagnyilvantartasModelFelh_terhat();
      $userTerhats = $felh_terhatModel->getItems($user->id, $userCsoport->fcsop_id);
	  $session->set('userTerhats',$userTerhats);
      $userCsoport = $session->get('userCsoport');
	  $db = Jfactory::getDBO();
	  $user = JFactory::getUser();
      foreach ($pks as $pk) {  
        // orig rekord elérése
        $orig = $this->getItem($pk);
       
        // területi hatáskör ?
        $jo = false;
        foreach ($userTerhats as $userTerhat) {
			if ($userTerhat->terszerv_id == $orig->terszerv_id) $jo = true; 
        }
		
        //+ 2010.10.29 SM csak szimpatizánst törölhet
		// modosithatja a nevet?
        // if ($userCsoport->jog_felhasznalok != 1) $jo = false;
        // if ($userCsoport->jog_nev != 'RW') $jo = false;
		if (($orig->kategoria_id != 3) & ($userCsoport->kod == 'SM')) {
          $this->setError(JText::_('COM_TAGNYILVANTARTAS_ACCESS_VIOLATION'));
          return false;          
		}
        //+ 2010.10.29 SM csak szimpatizánst törölhet
		
        if ($jo) {        
          
		  // módositás a naplózás érdekében  
	      $db->setQuery('update #__tny_kapcsolatok
		  set lastaction="DELETE",
		  lastact_user_id="'.$user->id.'",
		  lastact_time="'.date('Y-m-d H:i').'",
		  lastact_info=""
		  where kapcs_id='.$db->quote($pk));
		  $db->query();
		  // naplózás
          $db->setQuery('insert into #__tny_naplo
		  select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($pk));
		  $db->query();
	  
	      // tényleges törlés
          $result = parent::delete($pks);
        } else {
          $this->setError(JText::_('COM_TAGNYILVANTARTAS_NO_TERHAT'));
          return false;          
        }
      }
      return $result;
    } 
}
?>