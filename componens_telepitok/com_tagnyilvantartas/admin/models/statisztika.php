 <?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/

// 2017.06.29 a v�g d�tumokhoz bet�ve az 23:59:59 is.

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_tagnyilvantartas/tables');

class TagnyilvantartasModelstatisztika extends JModelList
{
	public function __construct($config = array())
	{
		parent::__construct($config);		
	}
	
	/**
	  * statisztika lek�r�se terszervre �sszesitve
	  * @JRequest string datumtol (opcion�lis)
	  * @JRequest string datumig (opcion�lis)
	  * @session userTerhats
	  * @return array of records
	*/  
	public function getItems() {
		$datumtol = JRequest::getVar('datumtol','');
		$datumig = JRequest::getVar('datumig','');
		$aktEv = 0 + date('Y');
		$aktHo = 0 + date('m');
		$db = JFactory::getDBO();
		if ($datumtol == '') {
			//if ($aktHo > 1) 
			//	$datumtol = $aktEv.'-'.($aktHo - 1).'-01';
		    //else 
			//	$datumtol = ($aktEv - 1).'-12-01';
			$datumtol = $aktEv.'-'.$aktHo.'-01';
		}
		if ($datumig == '') {
			//$db->setQuery('select LAST_DAY('.$db->quote($datumtol).') hoveg');
			//$res = $db->loadObject();
			//$datumig = $res->hoveg;
			$datumig = date('Y-m-d');
		}
		
		// adatb�zis hiba javit�s (ha vannak olyan napl� rekordok amikhez a kapcsolat rekord naploz�s n�lk�l lett kit�r�lve)
		$db->query('			
		update lmp_tny_naplo n1,
		(
		  SELECT DISTINCT w.kapcs_id
		  FROM (SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
			  FROM lmp_tny_naplo n
			  GROUP BY kapcs_id
			 ) w
		  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = w.kapcs_id AND n.lastact_time = w.max_lastact_time      
		  LEFT OUTER JOIN lmp_tny_kapcsolatok k ON k.kapcs_id = w.kapcs_id
		  WHERE k.kapcs_id IS NULL AND		
			n.lastaction <> "DELETE" AND n.lastaction <> "GROUP DELETE" AND n.lastaction <> "export CSV-be"
		) w1
		set n1.kapcs_id = 0
		where n1.kapcs_id = w1.kapcs_id
		');
		$db->query();
		$db->setQuery('
		delete from lmp_tny_naplo where kapcs_id = 0;		
		');
		$db->query();
		
		JRequest::setVar('datumtol',$datumtol);
		JRequest::setVar('datumig',$datumig);


		// ha datumig egy kor�bbi d�tum.... akkor a "v�g" adatok napl�b�l j�nnek
		if ($datumig < date('Y-m-d')) 
			$join =  'INNER JOIN lmp_tny_naplo n ON n.kapcs_id = vdat.kapcs_id AND n.lastact_time = vdat.max_lastact_time';
		// ha datumig a mai vagy egy j�v�beli d�tum akor akapcsolatok t�bl�b�l 
		if ($datumig >= date('Y-m-d')) 
			$join =  'INNER JOIN lmp_tny_kapcsolatok n ON n.kapcs_id = vdat.kapcs_id';
		
		$db->setQuery('
			/* v�ltoz�s darabsz�mok  ter�leti szervezetenk�nt */  
			SELECT tter.nev tulaj_nev, 
				   veg.terszerv_id, 
				   ter.nev, 
				   veg.kategoria_id, 
				   kat.szoveg, 
			   (veg.darab - IF(kezdo.darab IS NULL,0,kezdo.darab)) valtozas,
			   veg.darab vegdarab
			FROM (
				/* v�g �llapot darabsz�mok */
				SELECT va.terszerv_id, va.kategoria_id, COUNT(va.kapcs_id) darab
				FROM
				( /* v�g �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.kategoria_id) kategoria_id, max(n.terszerv_id) terszerv_id
				  FROM 
				  (/* az egyes kapcsolat adaok v�g d�tum el�tti utols� modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
					FROM lmp_tny_naplo n
					LEFT OUTER JOIN lmp_tny_kapcsolatok k on k.kapcs_id = n.kapcs_id
					WHERE n.lastact_time <= '.$db->quote($datumig.' 23:59:59').' AND k.kapcs_id IS NOT NULL 
					GROUP BY kapcs_id
				  ) vdat
				  '.$join.'
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  group by n.kapcs_id		
				) va  
				GROUP BY va.terszerv_id, va.kategoria_id
			) veg
			LEFT OUTER JOIN (
				/* elozo(kezd�) allapot darabsz�mok */
				SELECT ea.terszerv_id, ea.kategoria_id, COUNT(ea.kapcs_id) darab
				FROM
				(  /* el�z�(kezd�) �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.kategoria_id) kategoria_id, max(n.terszerv_id) terszerv_id
				  FROM 
				  ( /* Az egyes kapcsolat adatok utols� kezd� d�tum el�tti modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time 
					FROM lmp_tny_naplo n
					WHERE n.lastact_time < '.$db->quote($datumtol).' 
					GROUP BY kapcs_id
				  ) edat
				  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = edat.kapcs_id AND n.lastact_time = edat.max_lastact_time
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  GROUP BY n.kapcs_id		
				) ea  
				GROUP BY ea.terszerv_id, ea.kategoria_id
			) kezdo ON kezdo.kategoria_id = veg.kategoria_id AND veg.terszerv_id = kezdo.terszerv_id
			LEFT OUTER JOIN lmp_tny_teruletiszervezetek ter ON ter.terszerv_id = veg.terszerv_id
			LEFT OUTER JOIN lmp_tny_kategoriak kat ON kat.kategoria_id = veg.kategoria_id
			LEFT OUTER JOIN lmp_tny_teruletiszervezetek tter ON tter.terszerv_id = ter.tulaj_id
			where tter.nev <> "" and veg.terszerv_id <> ""
			ORDER BY 1, 3,5;
		');
		
		// DBG echo $db->getQuery().'<br>';
		
		$result = $db->loadObjectList();
		
		// DEBG echo count($result).'<br>';
				
		return $result;
	}
	/**
	  * statisztika lek�r�se OEVK -ra �sszes�tve
	  * @JRequest string datumtol (opcion�lis)
	  * @JRequest string datumig (opcion�lis)
	  * @session userTerhats
	  * @return array of records
	*/  
	public function getItemsOevk() {
		$datumtol = JRequest::getVar('datumtol','');
		$datumig = JRequest::getVar('datumig','');
		$aktEv = 0 + date('Y');
		$aktHo = 0 + date('m');
		$db = JFactory::getDBO();
		if ($datumtol == '') {
			//if ($aktHo > 1) 
			//	$datumtol = $aktEv.'-'.($aktHo - 1).'-01';
		    //else 
			//	$datumtol = ($aktEv - 1).'-12-01';
			$datumtol = $aktEv.'-'.$aktHo.'-01';
		}
		if ($datumig == '') {
			//$db->setQuery('select LAST_DAY('.$db->quote($datumtol).') hoveg');
			//$res = $db->loadObject();
			//$datumig = $res->hoveg;
			$datumig = date('Y-m-d');
		}
		
		// adatb�zis hiba javit�s (ha vannak olyan napl� rekordok amikhez a kapcsolat rekord naploz�s n�lk�l lett kit�r�lve)
		$db->query('			
		update lmp_tny_naplo n1,
		(
		  SELECT DISTINCT w.kapcs_id
		  FROM (SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
			  FROM lmp_tny_naplo n
			  GROUP BY kapcs_id
			 ) w
		  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = w.kapcs_id AND n.lastact_time = w.max_lastact_time      
		  LEFT OUTER JOIN lmp_tny_kapcsolatok k ON k.kapcs_id = w.kapcs_id
		  WHERE k.kapcs_id IS NULL AND		
			n.lastaction <> "DELETE" AND n.lastaction <> "GROUP DELETE" AND n.lastaction <> "export CSV-be"
		) w1
		set n1.kapcs_id = 0
		where n1.kapcs_id = w1.kapcs_id
		');
		$db->query();
		$db->setQuery('
		delete from lmp_tny_naplo where kapcs_id = 0;		
		');
		$db->query();

		JRequest::setVar('datumtol',$datumtol);
		JRequest::setVar('datumig',$datumig);

		// ha datumig egy kor�bbi d�tum.... akkor a "v�g" adatok napl�b�l j�nnek
		if ($datumig < date('Y-m-d')) 
			$join =  'INNER JOIN lmp_tny_naplo n ON n.kapcs_id = vdat.kapcs_id AND n.lastact_time = vdat.max_lastact_time
		    ';
		// ha datumig a mai vagy egy j�v�beli d�tum akor akapcsolatok t�bl�b�l 
		if ($datumig >= date('Y-m-d')) 
			$join =  'INNER JOIN lmp_tny_kapcsolatok n ON n.kapcs_id = vdat.kapcs_id
			';
			
		$db->setQuery('
			/* v�ltoz�s darabsz�mok  OEVK bont�sban */  
			SELECT "" tulaj_nev, 
				   veg.oevk, 
				   "" nev, 
				   veg.kategoria_id, 
				   kat.szoveg, 
			   (veg.darab - IF(kezdo.darab IS NULL,0,kezdo.darab)) valtozas,
			   veg.darab vegdarab
			FROM (
				/* v�g �llapot darabsz�mok */
				SELECT va.oevk, va.kategoria_id, if(va.kategoria_id=1,COUNT(va.kapcs_id) - 1,COUNT(va.kapcs_id)) darab
				FROM
				( /* v�g �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.kategoria_id) kategoria_id, max(n.oevk) oevk
				  FROM 
				  (/* az egyes kapcsolat adaok v�g d�tum el�tti utols� modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
					FROM lmp_tny_naplo n
					WHERE n.lastact_time <= '.$db->quote($datumig.' 23:59:59').'  
					GROUP BY kapcs_id
				  ) vdat
				  '.$join.'
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  group by n.kapcs_id		
				  union all
				  select distinct 1, 1, oevk 
				  from lmp_tny_oevk_torzs
				) va  
				GROUP BY va.oevk, va.kategoria_id
			) veg
			LEFT OUTER JOIN (
				/* elozo(kezd�) allapot darabsz�mok */
				SELECT ea.oevk, ea.kategoria_id, COUNT(ea.kapcs_id) darab
				FROM
				(  /* el�z�(kezd�) �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.kategoria_id) kategoria_id, max(n.oevk) oevk
				  FROM 
				  ( /* Az egyes kapcsolat adatok utols� kezd� d�tum el�tti modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time 
					FROM lmp_tny_naplo n
					WHERE n.lastact_time < '.$db->quote($datumtol).' 
					GROUP BY kapcs_id
				  ) edat
				  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = edat.kapcs_id AND n.lastact_time = edat.max_lastact_time
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  GROUP BY n.kapcs_id		
				) ea  
				GROUP BY ea.oevk, ea.kategoria_id
			) kezdo ON kezdo.kategoria_id = veg.kategoria_id AND veg.oevk = kezdo.oevk
			LEFT OUTER JOIN lmp_tny_kategoriak kat ON kat.kategoria_id = veg.kategoria_id
			where veg.oevk <> ""
			ORDER BY 1, 3,5;
		');
		
		// DBG echo $db->getQuery().'<br>';
		
		$result = $db->loadObjectList();
		//DBG echo count($result).'<br>';
		return $result;
	}

	/**
	  * Email statisztika lek�r�se OEVK -ra �sszes�tve
	  * @JRequest string datumtol (opcion�lis)
	  * @JRequest string datumig (opcion�lis)
	  * @session userTerhats
	  * @return array of records
	*/  
	public function getItemsOevkEmail() {
		$datumtol = JRequest::getVar('datumtol','');
		$datumig = JRequest::getVar('datumig','');
		$aktEv = 0 + date('Y');
		$aktHo = 0 + date('m');
		$db = JFactory::getDBO();
		if ($datumtol == '') {
			//if ($aktHo > 1) 
			//	$datumtol = $aktEv.'-'.($aktHo - 1).'-01';
		    //else 
			//	$datumtol = ($aktEv - 1).'-12-01';
			$datumtol = $aktEv.'-'.$aktHo.'-01';
		}
		if ($datumig == '') {
			//$db->setQuery('select LAST_DAY('.$db->quote($datumtol).') hoveg');
			//$res = $db->loadObject();
			//$datumig = $res->hoveg;
			$datumig = date('Y-m-d');
		}
		
		// adatb�zis hiba javit�s (ha vannak olyan napl� rekordok amikhez a kapcsolat rekord naploz�s n�lk�l lett kit�r�lve)
		$db->query('			
		update lmp_tny_naplo n1,
		(
		  SELECT DISTINCT w.kapcs_id
		  FROM (SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
			  FROM lmp_tny_naplo n
			  GROUP BY kapcs_id
			 ) w
		  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = w.kapcs_id AND n.lastact_time = w.max_lastact_time      
		  LEFT OUTER JOIN lmp_tny_kapcsolatok k ON k.kapcs_id = w.kapcs_id
		  WHERE k.kapcs_id IS NULL AND		
			n.lastaction <> "DELETE" AND n.lastaction <> "GROUP DELETE" AND n.lastaction <> "export CSV-be"
		) w1
		set n1.kapcs_id = 0
		where n1.kapcs_id = w1.kapcs_id
		');
		$db->query();
		$db->setQuery('
		delete from lmp_tny_naplo where kapcs_id = 0;		
		');
		$db->query();

		JRequest::setVar('datumtol',$datumtol);
		JRequest::setVar('datumig',$datumig);

		// ha datumig egy kor�bbi d�tum.... akkor a "v�g" adatok napl�b�l j�nnek
		if ($datumig < date('Y-m-d 00:00')) 
			$join =  'INNER JOIN lmp_tny_naplo n ON n.kapcs_id = vdat.kapcs_id AND n.lastact_time = vdat.max_lastact_time
		    ';
		// ha datumig a mai vagy egy j�v�beli d�tum akor akapcsolatok t�bl�b�l 
		if ($datumig >= date('Y-m-d 00:00')) 
			$join =  'INNER JOIN lmp_tny_kapcsolatok n ON n.kapcs_id = vdat.kapcs_id
			';

		$db->setQuery('
			/* v�ltoz�s Email darabsz�mok  OEVK bont�sban */  
			SELECT "" tulaj_nev, 
				   veg.oevk, 
				   "" nev, 
				   veg.kategoria_id, 
				   kat.szoveg, 
			   (veg.darab - IF(kezdo.darab IS NULL,0,kezdo.darab)) valtozas,
			   veg.darab vegdarab
			FROM (
				/* v�g �llapot darabsz�mok */
				SELECT va.oevk, va.kategoria_id, if(va.kategoria_id=1,COUNT(va.email) - 1,COUNT(va.email)) darab
				FROM
				( /* v�g �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.email) email, max(n.kategoria_id) kategoria_id, max(n.oevk) oevk
				  FROM 
				  (/* az egyes kapcsolat adaok v�g d�tum el�tti utols� modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
					FROM lmp_tny_naplo n
					WHERE n.lastact_time <= '.$db->quote($datumig).' and n.email <> ""  
					GROUP BY kapcs_id
				  ) vdat
				  '.$join.'
				  WHERE n.kapcs_id is not null and 
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  group by n.kapcs_id		
				  union all
				  select distinct 1, "", 1, oevk 
				  from lmp_tny_oevk_torzs
				) va  
				GROUP BY va.oevk, va.kategoria_id
			) veg
			LEFT OUTER JOIN (
				/* elozo(kezd�) allapot darabsz�mok */
				SELECT ea.oevk, ea.kategoria_id, COUNT(ea.email) darab
				FROM
				(  /* el�z�(kezd�) �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.email) email, max(n.kategoria_id) kategoria_id, max(n.oevk) oevk
				  FROM 
				  ( /* Az egyes kapcsolat adatok utols� kezd� d�tum el�tti modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time 
					FROM lmp_tny_naplo n
					WHERE n.lastact_time < '.$db->quote($datumtol).' and n.email <> ""
					GROUP BY kapcs_id
				  ) edat
				  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = edat.kapcs_id AND n.lastact_time = edat.max_lastact_time
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  GROUP BY n.kapcs_id		
				) ea  
				GROUP BY ea.oevk, ea.kategoria_id
			) kezdo ON kezdo.kategoria_id = veg.kategoria_id AND veg.oevk = kezdo.oevk
			LEFT OUTER JOIN lmp_tny_kategoriak kat ON kat.kategoria_id = veg.kategoria_id
			where veg.oevk <> ""
			ORDER BY 1, 3,5;
		');
		//DBG echo $db->getQuery().'<br>';
		$result = $db->loadObjectList();
		//DBG echo count($result).'<br>';
		return $result;
	}
	
	/**
	  * Telefonsz�m statisztika lek�r�se OEVK -ra �sszes�tve
	  * @JRequest string datumtol (opcion�lis)
	  * @JRequest string datumig (opcion�lis)
	  * @session userTerhats
	  * @return array of records
	*/  
	public function getItemsOevkTelszam() {
		$datumtol = JRequest::getVar('datumtol','');
		$datumig = JRequest::getVar('datumig','');
		$aktEv = 0 + date('Y');
		$aktHo = 0 + date('m');
		$db = JFactory::getDBO();
		if ($datumtol == '') {
			//if ($aktHo > 1) 
			//	$datumtol = $aktEv.'-'.($aktHo - 1).'-01';
		    //else 
			//	$datumtol = ($aktEv - 1).'-12-01';
			$datumtol = $aktEv.'-'.$aktHo.'-01';
		}
		if ($datumig == '') {
			//$db->setQuery('select LAST_DAY('.$db->quote($datumtol).') hoveg');
			//$res = $db->loadObject();
			//$datumig = $res->hoveg;
			$datumig = date('Y-m-d');
		}
		
		// adatb�zis hiba javit�s (ha vannak olyan napl� rekordok amikhez a kapcsolat rekord naploz�s n�lk�l lett kit�r�lve)
		$db->query('			
		update lmp_tny_naplo n1,
		(
		  SELECT DISTINCT w.kapcs_id
		  FROM (SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
			  FROM lmp_tny_naplo n
			  GROUP BY kapcs_id
			 ) w
		  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = w.kapcs_id AND n.lastact_time = w.max_lastact_time      
		  LEFT OUTER JOIN lmp_tny_kapcsolatok k ON k.kapcs_id = w.kapcs_id
		  WHERE k.kapcs_id IS NULL AND		
			n.lastaction <> "DELETE" AND n.lastaction <> "GROUP DELETE" AND n.lastaction <> "export CSV-be"
		) w1
		set n1.kapcs_id = 0
		where n1.kapcs_id = w1.kapcs_id
		');
		$db->query();
		$db->setQuery('
		delete from lmp_tny_naplo where kapcs_id = 0;		
		');
		$db->query();

		JRequest::setVar('datumtol',$datumtol);
		JRequest::setVar('datumig',$datumig);

		// ha datumig egy kor�bbi d�tum.... akkor a "v�g" adatok napl�b�l j�nnek
		if ($datumig < date('Y-m-d 00:00')) 
			$join =  'INNER JOIN lmp_tny_naplo n ON n.kapcs_id = vdat.kapcs_id AND n.lastact_time = vdat.max_lastact_time
		    ';
		// ha datumig a mai vagy egy j�v�beli d�tum akor akapcsolatok t�bl�b�l 
		if ($datumig >= date('Y-m-d 00:00')) 
			$join =  'INNER JOIN lmp_tny_kapcsolatok n ON n.kapcs_id = vdat.kapcs_id
			';

		$db->setQuery('
			/* v�ltoz�s Email darabsz�mok  OEVK bont�sban */  
			SELECT "" tulaj_nev, 
				   veg.oevk, 
				   "" nev, 
				   veg.kategoria_id, 
				   kat.szoveg, 
			   (veg.darab - IF(kezdo.darab IS NULL,0,kezdo.darab)) valtozas,
			   veg.darab vegdarab
			FROM (
				/* v�g �llapot darabsz�mok */
				SELECT va.oevk, va.kategoria_id, if(va.kategoria_id=1,COUNT(va.telefon) - 1,COUNT(va.telefon)) darab
				FROM
				( /* v�g �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.telefon) telefon, max(n.kategoria_id) kategoria_id, max(n.oevk) oevk
				  FROM 
				  (/* az egyes kapcsolat adaok v�g d�tum el�tti utols� modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
					FROM lmp_tny_naplo n
					WHERE n.lastact_time <= '.$db->quote($datumig).' and n.telefon <> ""  
					GROUP BY kapcs_id
				  ) vdat
				  '.$join.'
				  WHERE n.kapcs_id is not null and 
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  group by n.kapcs_id		
				  union all
				  select distinct 1, "", 1, oevk 
				  from lmp_tny_oevk_torzs
				) va  
				GROUP BY va.oevk, va.kategoria_id
			) veg
			LEFT OUTER JOIN (
				/* elozo(kezd�) allapot darabsz�mok */
				SELECT ea.oevk, ea.kategoria_id, COUNT(ea.telefon) darab
				FROM
				(  /* el�z�(kezd�) �llapot r�szletes adatok */
				  SELECT n.kapcs_id, max(n.telefon) telefon, max(n.kategoria_id) kategoria_id, max(n.oevk) oevk
				  FROM 
				  ( /* Az egyes kapcsolat adatok utols� kezd� d�tum el�tti modosit�si d�tuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time 
					FROM lmp_tny_naplo n
					WHERE n.lastact_time < '.$db->quote($datumtol).' and n.telefon <> ""
					GROUP BY kapcs_id
				  ) edat
				  INNER JOIN lmp_tny_naplo n ON n.kapcs_id = edat.kapcs_id AND n.lastact_time = edat.max_lastact_time
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				  GROUP BY n.kapcs_id		
				) ea  
				GROUP BY ea.oevk, ea.kategoria_id
			) kezdo ON kezdo.kategoria_id = veg.kategoria_id AND veg.oevk = kezdo.oevk
			LEFT OUTER JOIN lmp_tny_kategoriak kat ON kat.kategoria_id = veg.kategoria_id
			where veg.oevk <> ""
			ORDER BY 1, 3,5;
		');
		//DBG echo $db->getQuery().'<br>';
		$result = $db->loadObjectList();
		//DBG echo count($result).'<br>';
		return $result;
	}
	
	
	
}