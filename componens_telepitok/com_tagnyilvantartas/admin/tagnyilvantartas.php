<?php
/**
 * @version 1.0
 * @package    joomla
 * @subpackage Tagnyilvantartas
 * @author	   	
 *  @copyright  	Copyright (C) 2015, . All rights reserved.
 *  @license 
 */

//--No direct access
defined('_JEXEC') or die('Resrtricted Access');
define('PUSTART','2016-01-01'); // pénzügyi rendszer indulási időpontja
include_once 'components/com_tagnyilvantartas/models/felh_terhat.php';
include_once 'components/com_tagnyilvantartas/models/felhcsoportok.php';

if (JRequest::getVar('limit') == '') JRequest::setVar('limit',100); 

// lmp tagnyilvántartás több helyen használt rutinjai

function mb_ucfirst($string, $encoding='utf8') {
	/* 2015.07.27 személyes megbeszélés : ne legyen formai standartizálás
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
	*/
	return $string;
}


/**
  *  telefonszám(ok) standart formára hozása
  *  @param string
  *  @return string
  */
function stdTelefonszam($be) {
   if ($be == '') {
	   return $be;
   }	
   $w = explode(',',$be);
   for ($i=0; $i<count($w); $i++) {
     $w1 = trim($w[$i]);   
     $w1 = str_replace(['+', '-'], '', filter_var($w1, FILTER_SANITIZE_NUMBER_INT));
	 if (substr($w1,0,2)=='06') $w1 = '36'.substr($w1,2,20);
     $length = strlen($w1);
     if ($length < 10) {
       if (substr($w1,0,2)!='36') $w1 = '36'.$w1;
     }
     /* nem kell + jel és semmilyen tagolás 2015.07.27 megbeszélés
	 if (substr($w1,2,1)=='1')
       $w1 = substr($w1,0,2).'('.substr($w1,2,1).')'.
         substr($w1,3,2).' '.substr($w1,5,2).' '.substr($w1,7,10);
     else
       $w1 = substr($w1,0,2).'('.substr($w1,2,2).')'.
         substr($w1,4,2).' '.substr($w1,6,2).' '.substr($w1,8,10);
     $w[$i] ='+'.$w1; 
	 */
     $w[$i] = $w1; 
   }
   return implode(', ',$w);
}

/**
  * az adott felhasználói csoport által nem látható mezõ tartalmak elrejtése
  * @param record_object
  * @param record_object  
  * @return void
  */
function itemAccess(& $item, $felhcsop) {
  if ($felhcsop->jog_kapcsolat == 0) {
	 foreach ($item as $fn => $fv) {
		$item->$fn = '';
	 } 
  }
  if ($felhcsop->jog_email == 'X') {
	  $item->email = '';
	  $item->email2 = '';
	  $item->belsoemail = '';
  }
  if ($felhcsop->jog_nev == 'X') {
	  $item->nev1 = '';
	  $item->nev2 = '';
	  $item->nev3 = '';
	  $item->titulus = '';
  }
  if ($felhcsop->jog_telefonszam == 'X') {
	  $item->telefon = '';
  }
  if ($felhcsop->jog_lakcim == 'X') {
	  $item->telepules = '';
	  $item->irsz = '';
	  $item->kerulet = 0;
	  $item->utca = '';
	  $item->kjelleg = '';
	  $item->hazszam = 0;
	  $item->cimkieg = '';
  }
  if ($felhcsop->jog_tarthely == 'X') {
	  $item->ttelepules = '';
	  $item->tirsz = '';
	  $item->tkerulet = 0;
	  $item->tutca = '';
	  $item->tkjelleg = '';
	  $item->thazszam = 0;
	  $item->tcimkieg = '';
  }
  if ($felhcsop->jog_oevk == 'X') {
	  $item->oevk = '';
  }
  if ($felhcsop->jog_szev == 'X') {
	  $item->szev = '';
  }
  if ($felhcsop->jog_kapcskat == 'X') {
	  $item->kategoria_id = 0;
	  $item->szoveg = '';
  }
  if ($felhcsop->jog_kapcster == 'X') {
	  $item->terszerv_id = 0;
	  $item->tnev = '';
  }
  if ($felhcsop->jog_kapcscimkek == 'X') {
	  $item->cimkek = '';
  }
  if ($felhcsop->jog_kapcshirlevel == 'X') {
	  $item->hirlevel = 0;
  }
  
  if ($felhcsop->jog_ellenorzott == 'X') {
	  $item->ellenorzott = 0;
  }
}

/**
  * az adott felhasználói csoport által nem módosítható mezõk értékeinek törlése a $data -ból
  * @param record_object  
  * @return void
  */
function saveAccess($felhcsop, &$data) {
  //DBG echo 'saveAccess eleje '.$data['email'].'<br />';
  if ($felhcsop->jog_email != 'RW') {
	  unset($data['email']);
	  unset($data['email2']);
	  unset($data['belsoemail']);
  }
  
  if ($felhcsop->jog_nev != 'RW') {
	  unset($data['nev1']);
	  unset($data['nev2']);
	  unset($data['nev3']);
	  unset($data['titulus']);
  }
  if ($felhcsop->jog_telefonszam != 'RW') {
	  unset($data['telefon']);
  }
  if ($felhcsop->jog_lakcim != 'RW') {
	  unset($data['telepules']);
	  unset($data['irsz']);
	  unset($data['utca']);
	  unset($data['hazszam']);
	  unset($data['kjelleg']);
	  unset($data['cimkieg']);
  }
  if ($felhcsop->jog_tarthely != 'RW') {
	  unset($data['ttelepules']);
	  unset($data['tirsz']);
	  unset($data['tutca']);
	  unset($data['thazszam']);
	  unset($data['tkjelleg']);
	  unset($data['tcimkieg']);
  }
  if ($felhcsop->jog_oevk != 'RW') {
	  unset($data['oevk']);
  }
  if ($felhcsop->jog_szev != 'RW') {
	  unset($data['szev']);
  }
  if ($felhcsop->jog_kapcskat != 'RW') {
	  unset($data['kategoria_id']);
  }
  if ($felhcsop->jog_kapcster != 'RW') {
	  unset($data['terszerv_id']);
  }
  if ($felhcsop->jog_kapcscimkek != 'RW') {
	  unset($data['cimkek']);
  }
  if ($felhcsop->jog_kapcshirlevel != 'RW') {
	  unset($data['hirlevel']);
  }
  if ($felhcsop->jog_ellenorzott != 'RW') {
	  unset($data['ellenorzott']);
  }
  //DBG echo 'saveAccess vége '.$data['email'].'<br />';
}

/**
  * form field attribute és value modositás
  * @return void
  * @param JForm
  * @param string
  * @param string  'R' | 'X'
  * @param mixed 0 | ''
  */
function setFieldAttr(&$form, $fieldName, $attr, $nullValue) {
	if ($form == false) return;
	if ($attr == 'X') {
      $form->setFieldAttribute($fieldName, 'class', 'readonly hidden');
      $form->setFieldAttribute($fieldName, 'readonly', 'true');
	  $form->setValue($fieldName, $nullValue);
	}
	if ($attr == 'R') {
      $form->setFieldAttribute($fieldName, 'class', 'readonly');
      $form->setFieldAttribute($fieldName, 'readonly', 'true');
	}
}

/**
  * az adott felhasználói csoport által nem módosítható mezõk readonly-vá tétele
  * @param record_object
  * @param record_object  
  * @return void
  */
function formAccess(& $form, $felhcsop) {
	
  //DBG echo 'form access '.$felhcsop->jog_kapcskat.'<br />';	
	
  if ($felhcsop->jog_email == 'X') {
	 setFieldAttr($form, 'email','X',''); 
	 setFieldAttr($form,'email2','X',''); 
	 setFieldAttr($form,'belsoemail','X',''); 
  }	 
  if ($felhcsop->jog_nev == 'X') {
	 setFieldAttr($form,'nev1','X',''); 
	 setFieldAttr($form,'nev2','X',''); 
	 setFieldAttr($form,'nev3','X',''); 
	 setFieldAttr($form,'titulus','X',''); 
	 setFieldAttr($form,'nem','X',''); 
  }
  if ($felhcsop->jog_telefonszam == 'X') {
	 setFieldAttr($form,'telefon','X',''); 
  }
  if ($felhcsop->jog_lakcim == 'X') {
	 setFieldAttr($form,'telepules','X',''); 
	 setFieldAttr($form,'utca','X',''); 
	 setFieldAttr($form,'irsz','X',''); 
	 setFieldAttr($form,'kerulet','X',0); 
	 setFieldAttr($form,'hazszam','X',0); 
	 setFieldAttr($form,'cimkieg','X',''); 
	 setFieldAttr($form,'kjelleg','X',''); 
  }
  if ($felhcsop->jog_tarthely == 'X') {
	 setFieldAttr($form,'ttelepules','X',''); 
	 setFieldAttr($form,'tutca','X',''); 
	 setFieldAttr($form,'tirsz','X',''); 
	 setFieldAttr($form,'tkerulet','X',0); 
	 setFieldAttr($form,'thazszam','X',0); 
	 setFieldAttr($form,'tcimkieg','X',''); 
	 setFieldAttr($form,'tkjelleg','X',''); 
  }
  if ($felhcsop->jog_oevk == 'X') {
	 setFieldAttr($form,'oevk','X',''); 
  }
  if ($felhcsop->jog_szev == 'X') {
	 setFieldAttr($form,'szev','X',0); 
  }
  if ($felhcsop->jog_kapcskat == 'X') {
	 setFieldAttr($form,'kategoria_id','X',0); 
  }
  if ($felhcsop->jog_kapcster == 'X') {
	 setFieldAttr($form,'terszerv_id','X',0); 
	 setFieldAttr($form,'kapcsnev','X',''); 
	 setFieldAttr($form,'kapcsdatum','X',''); 
	 setFieldAttr($form,'megjegyzes','X',''); 
  }
  if ($felhcsop->jog_kapcscimkek == 'X') {
	 setFieldAttr($form,'cimkek','X',''); 
  }
  if ($felhcsop->jog_kapcshirlevel == 'X') {
	 setFieldAttr($form,'hirlevel','X',0); 
  }
  if ($felhcsop->jog_ellenorzott == 'X') {
	 setFieldAttr($form,'ellenorzott','X','1'); 
  }

  
  if ($felhcsop->jog_email == 'R') {
	 setFieldAttr($form,'email','R',''); 
	 setFieldAttr($form,'email2','R',''); 
	 setFieldAttr($form,'belsoemail','R',''); 
  }	 
  if ($felhcsop->jog_nev == 'R') {
	 setFieldAttr($form,'nev1','R',''); 
	 setFieldAttr($form,'nev2','R',''); 
	 setFieldAttr($form,'nev3','R',''); 
	 setFieldAttr($form,'titulus','R',''); 
	 setFieldAttr($form,'nem','R',''); 
  }
  if ($felhcsop->jog_telefonszam == 'R') {
	 setFieldAttr($form, 'telefon','R',''); 
  }
  if ($felhcsop->jog_lakcim == 'R') {
	 setFieldAttr($form,'telepules','R',''); 
	 setFieldAttr($form,'utca','R',''); 
	 setFieldAttr($form,'irsz','R',''); 
	 setFieldAttr($form,'kerulet','R',0); 
	 setFieldAttr($form,'hazszam','R',0); 
	 setFieldAttr($form,'cimkieg','R',''); 
	 setFieldAttr($form,'kjelleg','R',''); 
  }
  if ($felhcsop->jog_tarthely == 'R') {
	 setFieldAttr($form,'ttelepules','R',''); 
	 setFieldAttr($form,'tutca','R',''); 
	 setFieldAttr($form,'tirsz','R',''); 
	 setFieldAttr($form,'tkerulet','R',0); 
	 setFieldAttr($form,'thazszam','R',0); 
	 setFieldAttr($form,'tcimkieg','R',''); 
	 setFieldAttr($form,'tkjelleg','R',''); 
  }
  if ($felhcsop->jog_oevk == 'R') {
	 setFieldAttr($form,'oevk','R',''); 
  }
  if ($felhcsop->jog_szev == 'R') {
	 setFieldAttr($form,'szev','R',0); 
  }
  if ($felhcsop->jog_kapcskat == 'R') {
	 setFieldAttr($form,'kategoria_id','R',0); 
  }
  if ($felhcsop->jog_kapcster == 'R') {
	 setFieldAttr($form,'terszerv_id','R',0); 
	 setFieldAttr($form,'kapcsnev','R',''); 
	 setFieldAttr($form,'kapcsdatum','R',''); 
	 setFieldAttr($form,'megjegyzes','R',''); 
  }
  if ($felhcsop->jog_kapcscimkek == 'R') {
	 setFieldAttr($form,'cimkek','R',''); 
  }
  if ($felhcsop->jog_kapcshirlevel == 'R') {
	 setFieldAttr($form,'hirlevel','R',0); 
  }
  if ($felhcsop->jog_ellenorzott == 'R') {
	 setFieldAttr($form,'ellenorzott','R','1'); 
  }
}

//+ 2015.11.05. userTerhats beolvasása sessionba
$session = JFactory::getSession();
$user = JFactory::getUser();
$userCsoport = $session->get('userCsoport');
$felh_terhatModel = new tagnyilvantartasModelFelh_terhat();
$userTerhats = $felh_terhatModel->getItems($user->id, $userCsoport->fcsop_id);
$session->set('userTerhats',$userTerhats);
//- 2015.11.05. userTerhats beolvasása sessionba

//DBG echo JSON_encode($userCsoport); 
//DBG echo JSON_encode($userTerhats); 

require_once(JPATH_COMPONENT.'/helpers/tagnyilvantartas.php');
$controller = JControllerLegacy::getInstance('tagnyilvantartas');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
