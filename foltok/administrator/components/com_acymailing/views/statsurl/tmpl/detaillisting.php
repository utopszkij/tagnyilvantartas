<?php
  $user = JFactory::getUser();
  echo '<div style="padding:20px;">
  ';
  $mailId = JRequest::getVar('filter_mail');
  $urlId = JRequest::getVar('filter_url');
  $db = JFactory::getDBO();
  
  $db->setQuery('select subject, senddate
  from #__acymailing_mail
  where mailid = '.$db->quote($mailId));
  $mailRec = $db->loadObject();
  echo '<h2>'.$mailRec->subject.'</h2>
  ';
  
  $db->setQuery('select name, url
  from #__acymailing_url
  where urlid = '.$db->quote($urlId));
  $urlRec = $db->loadObject();
  echo '<p><a href="'.$urlRec->url.'">'.$urlRec->name.'</a><br />
  kattintások</p>
  ';
  
  $db->setQuery('select t.nev, count(*) cc
  from #__acymailing_urlclick c
  left outer join #__acymailing_subscriber s on c.subid = s.subid
  LEFT OUTER JOIN 
    (SELECT email, 
          MAX(terszerv_id)  terszerv_id, 
          MAX(kapcs_id) kapcs_id 
          FROM #__tny_kapcsolatok 
          GROUP BY email
    ) k ON k.email = s.email 
  left outer join #__tny_teruletiszervezetek t on t.terszerv_id = k.terszerv_id
  where  c.urlid='.$db->quote($urlId).' and c.mailid='.$db->quote($mailId).'
  group by t.nev
  order by t.nev
  ');
  $urlTerszervs = $db->loadObjectList();
  echo '<table border="0" cellpadding="5">
  ';
  foreach ($urlTerszervs as $urlTerszerv) {
	if ($urlTerszerv->nev == '')  $urlTerszerv->nev = '?';
	echo '<tr><td>'.$urlTerszerv->nev.'</td><td>'.$urlTerszerv->cc.' darab kattintás</td></tr>
	';  
  }
  echo '</table>
  <br />
  <br />
  ';
    
  $db->setQuery('
SELECT t.nev, k.kapcsnev, s.email
FROM #__acymailing_urlclick c 
LEFT OUTER JOIN 
  #__acymailing_subscriber s ON c.subid = s.subid 
LEFT OUTER JOIN 
  (SELECT email, 
          MAX(terszerv_id)  terszerv_id, 
          MAX(CONCAT(nev1," ",nev2," ",nev3)) kapcsnev 
          FROM #__tny_kapcsolatok 
          GROUP BY email
  ) k ON k.email = s.email 
LEFT OUTER JOIN 
  #__tny_teruletiszervezetek t ON t.terszerv_id = k.terszerv_id 
WHERE c.urlid='.$urlId.' AND c.mailid='.$mailId.' 
ORDER BY t.nev, k.kapcsnev
');  
  $urlClicks = $db->loadObjectList();
  
  $fp=fopen('../tmp/'.$user->username.'.csv','w+');
  fwrite($fp,'Hírlevél:'.$mailRec->subject."\n");
  fwrite($fp,'link:'.$urlRec->name."\n");
  foreach ($urlClicks as $urlclick) {
    fwrite($fp,'"'.$urlclick->nev.'";"'.$urlclick->kapcsnev.'";"'.$urlclick->email.'"'."\n");
  }
  fclose($fp);
  
  echo '<table cellpadding="5">
  ';
  foreach ($urlClicks as $urlclick) {
	if ($urlclick->nev=='') $urlclick->nev='?';  
	if ($urlclick->kapcsnev=='') $urlclick->kapcsnev='?';  
	echo '<tr><td>'.$urlclick->nev.'</td><td>'.$urlclick->kapcsnev.'</td><td><a href="mailto:'.$urlclick->email.'">'.$urlclick->email.'</a></td></tr>
	';  
  }
  echo '</table>
  <br />
  <br />
  <p><a href="'.JURI::root().'tmp/'.$user->username.'.csv">CSV fájl letöltése</a></p>
  <br />
  Megjegyzés: Ahol kérdőjel szerepel a listán, azoknál a hírlevél küldés e-mail címe jelenleg már nem található meg a kapcsolat 
  adatbázisban (törlve lett vagy megváltozott az e-mail címe), így a címzett neve,a területi szervezet nem megállapítható.
  <br />
  </div>
  ';
  
  
?>