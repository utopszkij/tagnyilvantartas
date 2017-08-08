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
		$nev = str_replace('  ',' ',$nev);
		$nev = str_replace('  ',' ',$nev);
		$nev = str_replace('  ',' ',$nev);
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
		$nev = str_replace('  ',' ',$nev);
		$nev = str_replace('  ',' ',$nev);
		$nev = str_replace('  ',' ',$nev);
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
			$result->orszag = 'HU';
			$result->torszag = 'HU';
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
	  if (!$this->chkNev($data['nev1'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - vezetéknév<br />';
          $this->errorFields[] = 'nev1';
	  }
	  if (!$this->chkNev($data['nev2'],3)) {
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
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - tart.hely település<br />';
          $this->errorFields[] = 'ttelepules';
	  }
	  if (!$this->chkUtca($data['utca'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - közterület neve<br />';
          $this->errorFields[] = 'utca';
	  }
	  
	  if (!$this->chkUtca($data['tutca'],3)) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_NAME_WRONG').' - tart.hely közterület neve<br />';
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
	  if ((int)$data['kerulet'] > 23) {
          $errorMsg .= JText::_('COM_TAGNYILVANTARTAS_KERULET_BIG').'<br />';
          $this->errorFields[] = 'kerulet';
	  }	
	  if ((int)$data['tkerulet'] > 23) {
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
			//DBG echo 'ellenörzö ciklus '.$userTerhat->terszerv_id.' ? '.$data['terszerv_id'].'<br>';
			
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

		  // kerület javitás Budapestieknél az irsz -ből
		  $db->setQuery(' 
			UPDATE #__tny_kapcsolatok 
			SET tkerulet = substring(tirsz,2,2)
			where tkerulet = "" and ttelepules = "Budapest" and
				  SUBSTR(tirsz,1,1) = "1" AND SUBSTR(tirsz,2,2) BETWEEN "01" AND "23"');
				  $db->query();
				  
				  $db->setQuery(' 
			UPDATE #__tny_kapcsolatok 
			SET kerulet = substring(irsz,2,2)
			where kerulet = "" and telepules = "Budapest" and
				  SUBSTR(irsz,1,1) = "1" AND SUBSTR(irsz,2,2) BETWEEN "01" AND "23";
		  ');
		  $db->query();

	      //+ 2016.06.20  OEVK kitöltési kisérlet 
		  if ($data['oevk'] == '') {
			$db->setQuery('UPDATE #__tny_kapcsolatok k, #__tny_oevk_torzs t
				SET k.oevk = t.oevk
				WHERE k.kapcs_id = '.$id.' AND k.oevk = "" AND 
				t.ev = 2016 AND 
				((t.kozterulet = "teljes" AND t.telepules = k.telepules AND t.kerulet="") OR
				 (t.kozterulet = "teljes" AND t.telepules = k.telepules AND t.kerulet = k.kerulet) OR
				 (t.telepules = k.telepules AND t.kerulet = k.kerulet AND 
				  t.kozterulet = k.utca AND
				  t.kozterjellege = k.kjelleg AND
				  t.hazszamtol <= k.hazszam AND t.hazszamig >= k.hazszam AND
				  ((MOD(k.hazszam,2) = 0 AND t.paros = "paros") OR 
				   (MOD(k.hazszam,2) = 1 AND t.paros = "paratlan") OR 
				   (t.paros = "")
				  )
				 )
				) 
			');
			$dbgStr = $db->getQuery();
			$db->query();
		  }
		  if ($data['oevk'] == '') {
			$db->setQuery('UPDATE #__tny_kapcsolatok k, #__tny_oevk_torzs t
				SET k.oevk = t.oevk
				WHERE k.kapcs_id = '.$id.' AND k.oevk = "" AND 
				t.ev = 2016 AND 
				((t.kozterulet = "teljes" AND k.telepules >= t.telepules AND k.telepules <= CONCAT(t.telepules,"z") AND t.kerulet="") OR
				 (t.kozterulet = "teljes" AND k.telepules >= t.telepules AND k.telepules <= CONCAT(t.telepules,"z") AND t.kerulet = k.kerulet) OR
				 (k.telepules >= t.telepules AND k.telepules <= CONCAT(t.telepules,"z") AND t.kerulet = k.kerulet AND 
				  k.utca >= t.kozterulet AND k.utca <= CONCAT(t.kozterulet,"z") AND
				  t.hazszamtol <= k.hazszam AND t.hazszamig >= k.hazszam AND
				  ((MOD(k.hazszam,2) = 0 AND t.paros = "paros") OR 
				   (MOD(k.hazszam,2) = 1 AND t.paros = "paratlan") OR 
				   (t.paros = "")
				   )
				 )
				)
			');
			$dbgStr .= '<br />'."\n".$db->getQuery();
			$db->query();
		  }
		  $w = $this->getItem($id);
		  if ($w->oevk == '') {
            $session->set('saveWarning','<p style="color:red">OEVK -t nem sikerült automatikusan kitölteni!</p>');
		  } else if ($data['oevk'] == '')  {
            $session->set('saveWarning','<p>OEVK automatikusan kitöltve: '.$w->oevk.'</p>');
		  } else {
			$session->set('saveWarning','');  
		  }	  
		  //echo $dbgStr; 
		  //- 2016.06.20  OEVK kitöltési kisérlet 

		  // 2016.12.09 acymailing_subscriber name szinkronizálás
		  if ($data['email'] != '') {
			  if (trim($data['nev2']) != '')
				 $snev =  trim($data['nev1']).' '.trim($data['nev2']).' '.trim($data['nev3']);
			  else
				 $snev =  trim($data['nev1']).' '.trim($data['nev3']);
			  $db->setQuery('update #__acymailing_subscriber
			  set name = '.$db->quote(trim($snev)).'
			  where email = '.$db->quote($data['email']));
			  $db->query();
		  }
		  
		  
		  // Kapcsolat a pénzügyi modullal (használható változók: $ujRekord, $orig, $id, $data)
		  
		  $this->puKapcsolat($ujRekord, $orig, $id, $data);
	
          return $result;
        } else {
          $this->setError(JText::_('COM_TAGNYILVANTARTAS_NO_TERHAT').' terszerv_id='.$data['terszerv_id']);
          return false;  
        }
		
    }
    
	/**
	 * Kapcsolat a pénzügyi modullal
	 * @param boolean $ujRekord
	 * @param object|false $orig
	 * @param integer $id
	 * @param array $data
	*/
	protected function puKapcsolat($ujRekord, $orig, $id, $data) {	  
		  $db = JFactory::getDBO();
		  if (file_exists(JPATH_ADMINISTRATOR.'/components/com_penzugy/penzugy.php')) {
			  $puLevelSubject = '';
			  $puLevelBody = '';
			  $db->setQuery('select * from #__tny_teruletiszervezetek
			  where terszerv_id = '.$db->quote($data['terszerv_id']));
			  $teruletiSzervezet = $db->loadObject();
			  
			  // kizárásnál a felszolítási folyamat lezárása, levél küldése
			  if (($data['kategoria_id']==7) | ($data['kategoria_id']==6)) {
			    $db->setQuery('update #__pu_felszolitasok
			    set status=12
			    where kapcs_id = '.$db->quote($id));
			    $db->query();
				$puLevelSubject = 'Párttag kizárása, felfuggesztese';
				$puLevelBody = 'Párttag neve:'.$data['nev1'].' '.$data['nev2'].' '.$data['nev3'].'<br />'.
				'<br />Területi szervezet:'.$teruletiSzervezet->nev.
				'<br />További információkhoz, beállításokhoz <a href="'.JURI::root().'">jelentkezzen be a pénzügyi programba</a>, ezután '.
				'<a href="'.JURI::root().'administrator/index.php?limit=20&limitstart=0&option=com_penzugy&view=tny_kapcsolatoks&task=adatlap&id='.$id.'">kattintson ide</a>'.
				'<p> </p><p>Ez egy automatikusan küldött levél, ne küldjön rá választ!</p>';
			  }
			  
			  // új párttag felvitele esetén levél küldése
			  if (($ujRekord == true) & ($data['kategoria_id'] == 1)) {
				  $puLevelSubject = 'Uj párttag lett a tagnyilvantartasba felveve';
				  $puLevelBody = 'Új párttag neve:'.$data['nev1'].' '.$data['nev2'].' '.$data['nev3'].'<br />'.
				  '<br />Területi szervezet:'.$teruletiSzervezet->nev.
				  '<br />További információkhoz, beállításokhoz <a href="'.JURI::root().'">jelentkezzen be a pénzügyi programba</a>, ezután '.
				  '<a href="'.JURI::root().'administrator/index.php?limit=20&limitstart=0&option=com_penzugy&view=tny_kapcsolatoks&task=adatlap&id='.$id.'">kattintson ide</a>'.
				  '<p> </p><p>Ez egy automatikusan küldött levél, ne küldjön rá választ!</p>';
			  }
			  
			  // párttagá modosítás esetén levél küldése
			  if (($ujRekord == false) & ($data['kategoria_id'] == 1) & ($orig->kategoria_id != 1)) {
				  $puLevelSubject = 'Korábbiban is meglevp kapcsolat párttagra lett módositva a tagnyilvantartasban';
				  $puLevelBody = 'Új párttag neve:'.$data['nev1'].' '.$data['nev2'].' '.$data['nev3'].'<br />'.
				  '<br />Területi szervezet:'.$teruletiSzervezet->nev.
				  '<br />További információkhoz, beállításokhoz <a href="'.JURI::root().'">jelentkezzen be a pénzügyi programba</a>, ezután '.
				  '<a href="'.JURI::root().'administrator/index.php?limit=20&limitstart=0&option=com_penzugy&view=tny_kapcsolatoks&task=adatlap&id='.$id.'">kattintson ide</a>'.
				  '<p> </p><p>Ez egy automatikusan küldött levél, ne küldjön rá választ!</p>';
			  }
			  
			  if ($puLevelSubject != '') {
					$mail = JFactory::getmailer();
					$mail->addRecipient('penzugy@lehetmas.hu');
					$mail->isHTML(true | false);
					$mail->setBody($puLevelBody);
					$mail->setSubject($puLevelSubject);
					$mail->setSender(array( [0] => 'penzugy@lehetmas.hu', [1] => 'LMP CRM program'));
					$bool = $mail->send();  
			  }
		  
			  $this->puKapcsolatEloirasok($orig,$data, $id);
		  } // van pénzügy component
	}	  	
	
	/**
	  * kapcsolat a pénzügyi modullal, előírások kezelése
	  * @param object|false $orig
	  * @param array $data
	  * @param integer $id
	*/  
	protected function puKapcsolatEloirasok($orig,$data, $id) {
			$db = JFactory::getDBO();
			
			// rekords lock
			$db->setQuery('lock tables
			#__tny_kapcsolatok WRITE,
			#__pu_tagdij_beallitasok WRITE,
			#__pu_eloirasok WRITE
			');
			$db->query();
		  
			// párttagoknál, ha modositás akkor a régihez tartozó előírások törlése
			if (($id > 0) & ($data['kategoria_id'] == 1)) {
				if ($orig) {
					$db->setQuery('delete 
					from #__pu_eloirasok
					where kapcs_id = '.$orig->kapcs_id.' and tagdij > 0 and nyito = 0 and 
					      datum >= "'.$orig->parttagstart.'" and 
						  (datum <= "'.$orig->parttagend.'" or "'.$orig->parttagend.'" < "2000-01-01")');
					$db->query();	  
				}
			}
			
			// párttagoknál új beállítások szerinti meglévő előírások törlése
			if ($data['kategoria_id'] == 1) {
				$db->setQuery('delete 
					from #__pu_eloirasok
					where kapcs_id = '.$data['kapcs_id'].' and tagdij > 0 and nyito = 0 and 
					  datum >= "'.$data['parttagstart'].'" and 
					  (datum <= "'.$data['parttagend'].'" or "'.$data['parttagend'].'" < "2000-01-01")');
				$db->query();	  
			}

			// érvényes tagdij beállítás rekord elérése
			if ($data['parttagstart'] > "2000-01-01")
			   $dmin = new DateTime($data['parttagstart']);
		    else
			   $dmin = new DateTime(PUSTART);
			$db->setQuery('SELECT *
			FROM #__pu_tagdij_beallitasok
			WHERE datumtol <= "'.$dmin->format('Y-m-d').'" and 
			      (datumig > "'.$dmin->format('Y-m-d').'" or datumig < "2000-01-01")');
			$beallitas = $db->loadObject();	  
			if ($beallitas == false) {
				$beallitas = new stdClass();
				$beallitas->eloirasnap = 5;
			}
			
			// párttagoknál új beállítás szerinti előírások generálása

			if ($dmin->format('d') == 1) {
				$dmin = $dmin = $dmin->add(new DateInterval('P'.$beallitas->eloirasnap.'D'));
				$dmin = $dmin->sub(new DateInterval('P1D'));
			} else {
				$dmin = $dmin->modify('last day of this month');
				$dmin = $dmin->add(new DateInterval('P5D'));
			}
			if ($dmin->format('Y-m-d') < PUSTART) {
				$dmin = new dateTime(PUSTART);
				$dmin = $dmin->add(new DateInterval('P'.$beallitas->eloirasnap.'D'));
				$dmin = $dmin->sub(new DateInterval('P1D'));
			}
			
			if (($data['parttagend'] > "2000-01-01") & ($data['parttagend'] <= date('Y-m-d'))) {
			  $dmax = new DateTime($data['parttagend']);
			  $w = $dmax->modify('last day of this month');
			  if ($w != $dmax) {
				  $s = $dmax->format('Y-m').'-01';
				  $dmax = new DateTime($s);
				  $dmax = $dmax->sub(new DateInterval('P1D'));
			  }  
			} else {
			  $dmax = new DateTime('now');	
			}  
			
			while (($dmin < $dmax) & ($data['kategoria_id'] == 1)) {
				$db->setQuery('INSERT INTO `lmp_pu_eloirasok` 
					(`id`, 
					`kapcs_id`, 
					`datum`, 
					`nyito`, 
					`leiras`, 
					`tagdij`, 
					`uvegzseb`, 
					`modusr`, 
					`moddat`
					)
					SELECT
					0, 
					'.$db->quote($data['kapcs_id']).', 
					"'.$dmin->format('Y-m-d').'", 
					0, 
					"", 
					osszeg, 
					0,
					0, 
					"'.date('Y-m-d').'"
					FROM #__pu_tagdij_beallitasok
					WHERE datumtol <= "'.$dmin->format('Y-m-d').'" and 
					      (datumig > "'.$dmin->format('Y-m-d').'" or datumig < "2000-01-01")
					;
				');
				$db->query();
				$db->setQuery('SELECT *
				FROM #__pu_tagdij_beallitasok
				WHERE datumtol <= "'.$dmin->format('Y-m-d').'" and 
					  (datumig > "'.$dmin->format('Y-m-d').'" or datumig < "2000-01-01")');
				$beallitas = $db->loadObject();	  
				if ($beallitas == false) {
					$beallitas = new stdClass();
					$beallitas->eloirasnap = 5;
				}
				$dmin = $dmin->modify('last day of this month');
				$dmin = $dmin->add(new DateInterval('P'.$beallitas->eloirasnap.'D'));
			}	

			$db->setQuery('unlock tables');
			$db->query();
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
       
		//csak akkor törölhető ha nincs hozzá befizetés
	    if ($this->penzugyilegTorolheto($orig)) {
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
		} else {
			  $this->setError('Nem törölhető mert befizetés van hozzá a pénzügyi rendszerben.');
			  return false;          
	    }
      }
      return $result;
    }
	/**
      *	pénzügyi szempontból törölhető ez a rekord?
	  * @param record object  kapcsolat rekord
	  * @return boolean
	*/  
    protected function penzugyilegTorolheto($record) {
		$db = JFactory::getDBO();
		$result = true;
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_penzugy/penzugy.php')) {
			$db->setQuery('select datum 
			from #__pu_befizetesek
			where kapcs_id='.$db->quote($record->kapcs_id).' or megbizo='.$db->quote($record->kapcs_id));
			$res = $db->loadObjectList();
			if (count($res) > 0) $result = false;
		}
		return $result;
	}	
}
?>