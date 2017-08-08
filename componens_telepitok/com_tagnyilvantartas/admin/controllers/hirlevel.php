<?php


  FONTOS  unsubsribe plugin készitése !!!! kapcsolatok -ba jelezze, hogy nem kér hírlevelet

  administrator/components/acymailing foltozva
    subscribers és stats csak a területi hatáskörbe tartozó adatokat mutatja.
  
  hirlevél küldés müködése
  ------------------------
  
  1. amelyik kapcsolat rekord még nincs meg aaz acymailing subscriber táblába azt felveszi oda
         key képezve, source = terszerv_id 
	     listára nem iratkozik fel.	 
		 
  2. szürés
  3. hírlevél választás
  4. sql -el betölti a #__hirlevel_que -ba a küldési feladatokat, innen a cron task tölti át a megfelelõ idõben azt
        acymailing_que ba.  
  

		//The purpose of this code is to let AcyMailing send an e-mail to a specific user at a specific time.
		//You just have to add an entry in the queue and AcyMailing will take care of the rest.
		 
		$memberid = '23'; //ID of the Joomla User or user e-mail (this code supposes that the user is already inserted in AcyMailing!)
		 
		$mailid = '45'; //ID of the Newsletter you want to add in the queue
		 
		$senddate = time(); //When do you want the e-mail to be sent to this user? you should specify a timestamp here (time() is the current time)
		 
		if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_acymailing'.DS.'helpers'.DS.'helper.php')){
		 echo 'This code can not work without the AcyMailing Component';
		 return false;
		 }
		 
		$userClass = acymailing_get('class.subscriber');
		 
		$subid = $userClass->subid($memberid); //this function returns the ID of the user stored in the AcyMailing table from a Joomla User ID or an e-mail address
		 if(empty($subid)) return false; //we didn't find the user in the AcyMailing tables
		 
		$db= JFactory::getDBO();
		 
		$db->setQuery('INSERT IGNORE INTO #__acymailing_queue (`subid`,`mailid`,`senddate`,`priority`) VALUES ('.$db->Quote($subid).','.$db->Quote($mailid).','.$db->Quote($senddate).',1)');
		 
		$db->query();

?>

/* kapcsolatok --> acymailing_subscriber, source = terszerv_id */
INSERT INTO lmp_acymailing_subscriber
SELECT 0 subid, 
    hianyzok.email email, 
    0 userid, 
    CONCAT(hianyzok.nev1,' ',hianyzok.nev2,' ',hianyzok.nev3) `name`, 
	UNIX_TIMESTAMP() created, 
	1 confirmed, 
	1 enabled, 
	1 accept, 
	"" ip, 
	1 html, 
	HEX(12345 * hianyzok.kapcs_id) `key`, 
	UNIX_TIMESTAMP() confirmed_date, 
	"" confirmed_ip, 
	"" lastopen_date, 
	"" lastclick_date, 
	"" lastopen_ip, 
	"" lastsent_date, 
	hianyzok.terszerv_id source
FROM
(SELECT idemail.kapcs_id, idemail.email, k.nev1, k.nev2, k.nev3, k.terszerv_id, s.subid
 FROM
   (SELECT MIN(kapcs_id) kapcs_id, email
    FROM
      (SELECT kapcs_id, email
       FROM lmp_tny_kapcsolatok
       WHERE email <> ""
       UNION
       SELECT kapcs_id, email2
       FROM lmp_tny_kapcsolatok
       WHERE email2 <> ""
       UNION
       SELECT kapcs_id, belsoemail
       FROM lmp_tny_kapcsolatok
       WHERE belsoemail <> ""
       ORDER BY 1
      ) emails
    GROUP BY email
   ) idemail
   LEFT OUTER JOIN lmp_tny_kapcsolatok k
     ON k.kapcs_id = idemail.kapcs_id
   LEFT OUTER JOIN lmp_acymailing_subscriber s
     ON s.email = idemail.email
   WHERE idemail.email <> "" AND s.subid IS NULL 
) hianyzok  

  