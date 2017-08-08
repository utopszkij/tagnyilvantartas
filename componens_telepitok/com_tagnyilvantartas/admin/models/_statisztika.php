 <?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @package		Tagnyilvantartas
* @subpackage 	Models
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/

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
	  * statisztika lekérése
	  * @JRequest string datumtol (opcionális)
	  * @JRequest string datumig (opcionális)
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
			if ($aktHo > 1) 
				$datumtol = $aktEv.'-'.($aktHo - 1).'-01';
		    else 
				$datumtol = ($aktEv - 1).'-12-01';
		}
		if ($datumig == '') {
			$db->setQuery('select LAST_DAY('.$db->quote($datumtol).') hoveg');
			$res = $db->loadObject();
			$datumig = $res->hoveg;
		}
		
		// adatbázis hiba javítás
		$db->setQuery('update lmp_tny_naplo n1,
			(
			  SELECT DISTINCT w.kapcs_id
			  FROM (SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
				  FROM #__tny_naplo n
				  GROUP BY kapcs_id
				 ) w
			  INNER JOIN #__tny_naplo n ON n.kapcs_id = w.kapcs_id AND n.lastact_time = w.max_lastact_time      
			  LEFT OUTER JOIN #__tny_kapcsolatok k ON k.kapcs_id = w.kapcs_id
			  WHERE k.kapcs_id IS NULL AND		
				n.lastaction <> "DELETE" AND n.lastaction <> "GROUP DELETE" AND n.lastaction <> "export CSV-be"
			) w1
			set n1.kapcs_id = 0
			where n1.kapcs_id = w1.kapcs_id
		');
		$db->query();
		$db->setQuery('delete from #__tny_naplo where kapcs_id = 0;');
		$db->query();
		
		JRequest::setVar('datumtol',$datumtol);
		JRequest::setVar('datumig',$datumig);
		$db->setQuery('
			/* változás darabszámok  területi szervezetenként */  
			SELECT tter.nev tulaj_nev, 
				   veg.terszerv_id, 
				   ter.nev, 
				   veg.kategoria_id, 
				   kat.szoveg, 
			   (veg.darab - IF(kezdo.darab IS NULL,0,kezdo.darab)) valtozas,
			   veg.darab vegdarab
			FROM (
				/* vég állapot darabszámok */
				SELECT va.terszerv_id, va.kategoria_id, COUNT(va.kapcs_id) darab
				FROM
				( /* vég állapot részletes adatok */
				  SELECT DISTINCT n.kapcs_id, n.nev1, n.nev2, n.kategoria_id, n.terszerv_id
				  FROM 
				  (/* az egyes kapcsolat adaok vég dátum elötti utolsó modositási dátuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time
					FROM #__tny_naplo n
					WHERE n.lastact_time <= '.$db->quote($datumig).'  
					GROUP BY kapcs_id
				  ) vdat
				  INNER JOIN #__tny_naplo n ON n.kapcs_id = vdat.kapcs_id AND n.lastact_time = vdat.max_lastact_time
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				) va  
				GROUP BY va.terszerv_id, va.kategoria_id
			) veg
			LEFT OUTER JOIN (
				/* elozo(kezdõ) allapot darabszámok */
				SELECT ea.terszerv_id, ea.kategoria_id, COUNT(ea.kapcs_id) darab
				FROM
				(  /* elözõ(kezdõ) állapot részletes adatok */
				  SELECT DISTINCT n.kapcs_id, n.nev1, n.nev2, n.kategoria_id, n.terszerv_id
				  FROM 
				  ( /* Az egyes kapcsolat adatok utolsó kezdõ dátum elõtti modositási dátuma */
					SELECT n.kapcs_id, MAX(n.lastact_time) max_lastact_time 
					FROM #__tny_naplo n
					WHERE n.lastact_time < '.$db->quote($datumtol).' 
					GROUP BY kapcs_id
				  ) edat
				  INNER JOIN #__tny_naplo n ON n.kapcs_id = edat.kapcs_id AND n.lastact_time = edat.max_lastact_time
				  WHERE n.kapcs_id is not null and
					    n.lastaction <> "DELETE" and n.lastaction <> "GROUP DELETE" and n.lastaction <> "export CSV-be"
				) ea  
				GROUP BY ea.terszerv_id, ea.kategoria_id
			) kezdo ON kezdo.kategoria_id = veg.kategoria_id AND veg.terszerv_id = kezdo.terszerv_id
			LEFT OUTER JOIN #__tny_teruletiszervezetek ter ON ter.terszerv_id = veg.terszerv_id
			LEFT OUTER JOIN #__tny_kategoriak kat ON kat.kategoria_id = veg.kategoria_id
			LEFT OUTER JOIN #__tny_teruletiszervezetek tter ON tter.terszerv_id = ter.tulaj_id
			where tter.nev <> "" and veg.terszerv_id <> ""
			ORDER BY 1, 3,5;
		');
		
		//DBG echo $db->getQuery().'<br>';
		
		$result = $db->loadObjectList();
		
		//DBG echo count($result).'<br>';
				
		return $result;
	}
}