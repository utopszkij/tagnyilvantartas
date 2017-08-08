<?php
defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:telpopup.php  1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		GNU/GPL
* telefonszám popup kezelés
*/

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}

 
class TagnyilvantartasModelTelpopup { 
   /*
   function __construct($config = array()) {
	   echo ' model construct start'; exit();
	   parent::__construct($config);
   }
	*/

	/**
	* get kapcsolat, kampany, hirlevel, hirlevelStatus
	* @param int kampany_id
	* @param int kapcs_id
	* @return object kapcsolat rekord + ->kampany  ->hirlevel  ->herlevelStatus ->subid
	*/
	public function getItem($kampany_id, $kapcs_id) {
		$db = JFactory::getDBO();
		$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($kapcs_id));
		$result = $db->loadObject();
		// subid keresés
		if ($result) {
		  $result->subid = '';
		  $db->setQuery('select * from #__acymailing_subscriber where email="'.$result->email.'"');
		  $res = $db->loadObject();
		  if ($res) $result->subid = $res->subid;
		}
		$db->setQuery('select * from #__tny_kampany where id='.$db->quote($kampany_id));
		$result->hirlevelStatus = 'Nem kapta meg';
		$result->kampany = $db->loadObject();
		if ($result->kampany) {
			$db->setQuery('select * from #__acymailing_mail where mailid='.$db->quote($result->kampany->hirlevel_id));
			$result->hirlevel = $db->loadObject();
			// subid beolvasása (lentebb még lehet, hogy átirjuk)
			$db->setQuery('select subid from #__acymailing_subscriber where email='.$db->quote($result->email));
			$res = $db->loadObject();
			if ($res) $result->subid = $res->subid;
			if ($result->hirlevel) {
				// hirlevél küldés állapotának beolvasása
				$db->setQuery('select us.* from #__acymailing_userstats us
				left outer join #__acymailing_subscriber s on s.subid = us.subid
				left outer join #__tny_kapcsolatok k on k.email = s.email or k.email2 = s.email
				where us.mailid = '.$db->quote($result->hirlevel->mailid).' and 
				      k.kapcs_id = '.$db->quote($result->kapcs_id));
				$res = $db->loadObjectList();
//dbg echo $db->getQuery().'<br>';				
				if (count($res) > 0) {
					$result->subid = $res[0]->subid;
					if ($res[0]->senddate > 0) $result->hirlevelStatus = 'elküldve '.date('Y.m.d H:i', $res[0]->senddate);
					if ($res[0]->opendate > 0) $result->hirlevelStatus = 'megnyitotta '.date('Y.m.d H:i', $res[0]->opendate);
				}
			}
		}
		return $result;
	}
	
	/**
	* save data from form into database
	* @JRequest form datas (include kapcs_id, kampany_id)
	* @return boolean
	*/
	public function save() {
		$result = true;
		$db = JFactory::getDBO();
		$kapcs_id = JRequest::getVar('kapcs_id');
		$telSzimp = JRequest::getVar('telSzimp','Igen');
		$telHirlevel = JRequest::getVar('telHirlevel','Igen');
		$telHivhato = JRequest::getVar('telHivhato','Igen');
		$kampany_id = JRequest::getVar('kampany_id','');
		$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($kapcs_id));
		//DBG echo $db->getQuery().'<br /><br />';
		$kapcs = $db->loadObject();
		if ($kapcs) {
			$telszammegj = $kapcs->telszammegj;
			$telstatus = JRequest::getVar('telstatus');
			
			// régi utolsó hivás státusz eltávolitása
			$i = mb_strpos(' '.$telszammegj,'<br />[');
			if ($i > 0)
				$telszammegj = substr($telszammegj,0,$i-1);
			
			if (($telHirlevel == 'Nem') & ($telstatus == 'felvette')) {
				if (mb_strpos($telszammegj,'NEM KÉR HÍRLEVELET')===false) 
					$telszammegj = $telszammegj.' NEM KÉR HÍRLEVELET';
				// email cím csillagozás a kapcsolat rekordban
				$db->setQuery('update #__tny_kapcsolatok
				set email = concat("*",email), hirlevel=0
				where kapcs_id='.$db->quote($kapcs_id).' and email <> "" and substr(email,1,1) <> "*"');
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
				$db->setQuery('update #__tny_kapcsolatok
				set email2 = concat("*",email2)
				where kapcs_id='.$db->quote($kapcs_id).' and email2 <> "" and substr(email2,1,1) <> "*"');
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
			} else if (($telHirlevel == 'Igen') & ($telstatus == 'felvette')) {
				$telszammegj = str_replace('NEM KÉR HÍRLEVELET','', $telszammegj);
				$db->setQuery('update #__tny_kapcsolatok
				set email = replace(email, "*", ""), email2 = replace(email2, "*", ""), hirlevel=1
				where kapcs_id='.$db->quote($kapcs_id));
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
			}
			
			//DBG $fp = fopen('debug.txt','w+');
			//DBG fwrite($fp, $db->getQuery()."\ntelHirlevel=".$telHirlevel."\ntelStatus=".$telstatus."\n");
			//DBG fclose($fp);
			
			
			// ha rossz telefonszám akkor telszám csillagozása a kapcsolat rekordban
			if ($telstatus == 'rossz szám')  {
				$db->setQuery('update #__tny_kapcsolatok
				set telefon = concat("*",telefon)
				where kapcs_id='.$db->quote($kapcs_id).' and telefon <> "" and substr(telefon,1,1) <> "*"');
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
				//DBG echo $db->getQuery().'<br /><br />';
				if (substr($kapcs->telefon,0,1) != '*') $kapcs->telefon = '*'.$kapcs->telefon;
			}
			
			// új utolsó hivás státusz beirása
			if ($telHivhato == 'Nem')
			   $telszammegj .= '<br />['.date('Y-m-d').' '.$telstatus.' NEM KÉR TÖBB HÍVÁST]';
			else	
			   $telszammegj .= '<br />['.date('Y-m-d').' '.$telstatus.']';
			if ($telstatus == 'kesobb') $telszammegj .= ' '.JRequest::getVar('kesobb');	  

			
			// telszammegj modositása a kapcsolat rekordban
			$db->setQuery('update #__tny_kapcsolatok
			set telszammegj ='.$db->quote($telszammegj).'
			where kapcs_id='.$db->quote($kapcs_id));
			$db->query();
			//DBG echo $db->getQuery().'<br /><br />';
			
			// telmegj2 modositása a kapcsolat rekordban
			if ($telstatus == 'felvette') {
				$telmegj2 = '&nbsp;&nbsp;&nbsp;'.date('Y.m.d H:i').
				  ',<br />- szimpatizáns:'.$telSzimp.
				  ',<br />- kér hírlevelet:'.$telHirlevel.
				  ',<br />- hívhatjuk:'.$telHivhato;
				$db->setQuery('update #__tny_kapcsolatok
				set telmegj2 ='.$db->quote($telmegj2).'	
				where kapcs_id='.$db->quote($kapcs_id));
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
			}
			// kampannyal kapcsolatos dolgok tárolása
			
			$kampany_id = JRequest::getVar('kampany_id',0);
			if (($kampany_id > 0) & ($telstatus == 'felvette')) {
				$valasz = JRequest::getVar('kampanyValasz');
				$valasz1 = JRequest::getVar('kampanyValasz1');
				$valasz2 = JRequest::getVar('kampanyValasz2');
				$valasz3 = JRequest::getVar('kampanyValasz3');
				$valasz4 = JRequest::getVar('kampanyValasz4');
				for ($i = 0; $i<10; $i++) {
					$s = JRequest::getVar('kampanyValasz_'.$i);
					if ($s != '') {
						if ($valasz != '')
							$valasz .= ",".$s;
						else
							$valasz = $s;
					} 
					
					$s = JRequest::getVar('kampanyValasz1_'.$i);
					if ($s != '') {
						if ($valasz1 != '')
							$valasz1 .= ",".$s;
						else
							$valasz1 = $s;
					} 
					
					$s = JRequest::getVar('kampanyValasz2_'.$i);
					if ($s != '') {
						if ($valasz2 != '')
							$valasz2 .= ",".$s;
						else
							$valasz2 = $s;
					} 
					
					$s = JRequest::getVar('kampanyValasz3_'.$i);
					if ($s != '') {
						if ($valasz3 != '')
							$valasz3 .= ",".$s;
						else
							$valasz3 = $s;
					} 
					
					$s = JRequest::getVar('kampanyValasz4_'.$i);
					if ($s != '') {
						if ($valasz4 != '')
							$valasz4 .= ",".$s;
						else
							$valasz4 = $s;
					} 
				}
				
				$db->setQuery('select * from #__tny_kampany_kapcs where kampany_id='.$kampany_id.' and kapcs_id='.$kapcs_id);
				$kk = $db->loadOnject();
				if ($kk) {
					$db->setQuery('update #__tny_kampany_kapcs
					set hivasido="'.date('Y-m-d H:i:s').'", valasz="'.$valasz.'",
						valasz1="'.$valasz1.'",
						valasz2="'.$valasz2.'",
						valasz3="'.$valasz3.'",
						valasz4="'.$valasz4.'"
					where kampany_id='.$kampany_id.' and kapcs_id='.$kapcs_id);
				} else {
					$db->setQuery('insert into #__tny_kampany_kapcs (kampany_id, kapcs_id, hivasido, valasz,valasz1, valasz2, valasz3, valasz4)
					values ('.$kampany_id.','.$kapcs_id.',"'.date('Y-m-d H:i:s').'","'.$valasz.'","'.$valasz1.'","'.$valasz2.'","'.$valasz3.'","'.$valasz4.'")
					');
				}
				$db->query();
				//DBG echo $db->getQuery().'<br /><br />';
			} // kampany_id > 0 és felvette
			if (($kampany_id > 0) & ($telstatus != 'felvette')) {
			   // ha még nincs hozzá kampany_kapcs rekord akkor most létrehozzuk
			   $db->setQuery('select count(*) cc from #__tny_kampany_kapcs where kampany_id='.$db->quote($kampany_id).' and kapcs_id='.$db->quote($kapcs_id));
			   $res = $db->loadObject();
			   if ($res->cc == 0) {
					$db->setQuery('insert into #__tny_kampany_kapcs (kampany_id, kapcs_id, hivasido, valasz,valasz1, valasz2, valasz3, valasz4)
					values ('.$kampany_id.','.$kapcs_id.',"'.date('Y-m-d H:i:s').'","nem vette fel","","","","")
					');
					$db->query();
			   }
			   
			}
		} // megvan a kapcsolat rekord
		return $result;	
	}
	
	/**
	* hírlevél újra küldése, email cím változás tárolása szimpatizánsnál a kapcsolat rekordba,
	* másoknál modositási javaslatként
	* @JRequest kapcs_id, hirlevel_id, email, subid
	* @return void
	*/
	public function sendMail() {
		$db = JFactory::getDBO();
		$kapcs_id = JRequest::getVar('kapcs_id');
		$email = JRequest::getVar('email');
		$mailid = JRequest::getVar('hirlevel_id');
		$subid = JRequest::getVar('subid');

		$db->setQuery('select * from #__tny_kapcsolatok where kapcs_id='.$db->quote($kapcs_id));
//dbg echo $db->getQuery().'<br /><br />';
		$kapcsolat = $db->loadObject();

//dbg echo $db->getQuery().' (1)<br>';		
		if ($kapcsolat) {

			// ha subid nem érkezet a formrpl akkor keresése az adatbázisban
		    if (($subid <= 0) | ($subid == '')) {
		      $db->setQuery('select * from #__acymailing_subscriber where email="'.$kapcsolat->email.'"');
		      $res = $db->loadObject();
		      if ($res) $subid = $res->subid;
//dbg echo $db->getQuery().' (2) $subid='.$subid.'<br>';		
		    }
			
			// ha subid <= 0 akkor eddig nem volt subscriber rekordja, most létrehozzuk
			if (($subid <= 0) | ($subid == '')) {
				
			$key = dechex(1212 * $kapcsolat->kapcs_id);
			  $db->setQuery('INSERT INTO #__acymailing_subscriber 
                (`email`,
				 `userid`,
				 `name`,
				 `created`,
				 `confirmed`,
				 `enabled`,
				 `accept`,
				 `ip`,
				 `html`,
				 `key`,
				 `confirmed_date`,
				 `confirmed_ip`,
				 `lastopen_date`,
				 `lastclick_date`,
				 `lastopen_ip`,
				 `lastsent_date`,
				 `source`
				)
				values
				("'.$kapcsolat->email.'",
	           	 0,
				 '.$db->quote($kapcsolat->nev1.' '.$kapcsolat->nev2.' '.$kapcsolat->nev3).',
				 '.time().',
				 0,
				 1,
				 1,
				 "",
				 1,
				 "'.$key.'",
				 0,
				 "",
				 0,
				 0,
				 "",
				 '.time().',
				 ""
				)
				');
				$db->query();

				$db->setQuery('select max(subid) cc from #__acymailing_subscriber');
				$res = $db->loadObject();
				$subid = $res->cc;
			} // subscriber létrehozás (ha eddig nem volt)

			
			if ($kapcsolat->email != $email) {

				  // Új email létezik az acymailing_subscriber -ben?
				  $db->setQuery('select * from #__acymailing_subscriber where email='.$db->quote($email));
				  $res = $db->loadObject();
				  if (!$res) {
					// nem létezik, a meglévő acymailing_subscriber rekordot modositjuk  
					$db->setQuery('update #__acymailing_subscriber
					set email='.$db->quote($email).'
					where subid='.$db->quote($subid));
					$db->query();
				  } else {
					// létezik, ez esetben ezt a subid -t használjuk  
					$subid = $res->subid;  
				  }	
			
				  if (($kapcsolat->kategoria_id == 3) & ($kapcsolat->email != $email)) {
					  // szimpatizáns; kapcsolat rekord modositás
					  $db->setQuery('update #__tny_kapcsolatok
					  set email='.$db->quote($email).',
					  megjegyzes=concat(megjegyzes," email módositva telefonos egyeztetés alapján '.date('Y-m-d H:i').' '.JFactory::getUser()->name.'")
					  where kapcs_id='.$db->quote($kapcs_id));
					  $db->query();
				  }	else if ($kapcsolat->email != $email) {
					 // másoknál modositási javaslat felvitele 	
					 $db->setQuery('INSERT INTO #__tny_javaslat 
						(`kapcs_id`, 
						`javaslo_id`, 
						`idopont`, 
						`mezo`, 
						`ertek`, 
						`allapot`, 
						`megjegyzes`
						)
						VALUES
						('.$db->quote($kapcs_id).', 
						'.JFactory::getUser()->id.', 
						"'.date('Y-m-d H:i:s').'", 
						"email", 
						'.$db->quote($email).', 
						"javaslat", 
						"telefonos egyeztetés"
						);
					');
					$db->query();
				  } // szimpatizáns vagy páttag ?	 
			} // változott az email
			
			// ellenörzés az acymailing_que -ban nincs-e már ilyen rekord?
			$db->setQuery('select * from #__acymailing_queue where mailid='.$db->quote($mailid).' and subid='.$db->quote($subid));
			$res = $db->loadObject();
			if (!$res) {
				// még nem szerepel, most beirjuk.
				$db->setQuery('INSERT INTO #__acymailing_queue 
					(`senddate`, 
					`subid`, 
					`mailid`, 
					`priority`, 
					`try`, 
					`paramqueue`
					)
					VALUES
					(0, 
					'.$db->quote($subid).', 
					'.$db->quote($mailid).', 
					2, 
					0, 
					""
					);
				');
				$db->query();
			} // már van ilyen az acymailing_que -ban?
		} // megvan a kapcsolat rekord
	} // sendMail method
} // class	
?>