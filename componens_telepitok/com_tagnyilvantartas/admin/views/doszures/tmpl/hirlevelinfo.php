<div class="hirlevelinfo"style="padding:10px;">
<h2>Hírlevél kiküldési információk</h2>
<?php if (count($this->hirlevelinfok) > 0) : ?>
	<h3><?php echo $this->hirlevelinfok[0]->subject; ?></h3>
	<?php foreach ($this->hirlevelinfok as $item) : ?>
		<div class="hirlevelinfoitem">
			<h4><?php echo $item->datum; ?></h4>
			<p>Kiküldte: <?php echo $item->name; ?></p>
			<?php 
				// $item->filter tartalmazza a filtert. \n\n -el vannak a sorok elválasztva, 3 sor ad egy feltételt
				$sorok = explode("\n\n",$item->filter);
				if ($sorok[0] == 'onlychecked=1') echo 'Csak ellenörzött kapcsolatok<br />';
				$i = 1;
				while ($i < count($sorok)) {
					$mezo = substr($sorok[$i],6,100);
					$rel = substr($sorok[$i+1],9,100);
					$ertek = substr($sorok[$i+2],7,100);
				    if ($rel == 'lt') $rel = '&lt;';
				    if ($rel == 'lte') $rel = '&lt;=';
				    if ($rel == 'gt') $rel = '&gt;';
				    if ($rel == 'gte') $rel = '&gt;=';
				    if ($rel == 'ne') $rel = '&lt;&gt;';
				    if ($rel == 'like') $rel = 'benne;';
				    if ($rel == 'between') $rel = 'tól-ig;';
				    if ($mezo == 'orszag') {
					   $mezo = 'Áll. lc. ország';
					   $db->setQuery('select megn from #__tny_orszkod where orszkod="'.$ertek.'"');
					   $res = $db->loadObject();
					   if ($res) $ertek = $res->megn;
				    } 	   
				    if ($mezo == 'torszag') {
					   $mezoLabel = 'Tart. h. ország';
					   $db->setQuery('select megn from #__tny_orszkod where orszkod="'.$ertek.'"');
					   $res = $db->loadObject();
					   if ($res) $ertek = $res->megn;
				    }	   
				    if ($mezo == 'kategoria_id') {
					   $mezoLabel = 'Kategória';
					   $db->setQuery('select szoveg from #__tny_kategoriak where kategoria_id="'.$ertek.'"');
					   $res = $db->loadObject();
					   if ($res) $ertek = $res->szoveg;
				    }	   
					$mezo = JText::_($mezo);
					echo $mezo.' '.$rel.' '.$ertek.'<br />';
					$i = $i + 3;
				}
			?>
		</div>
	<?php endforeach; ?>
<?php else : ?>
	<h3>Erről a hírlevélről nincs letárolt küldési információ.</h3>
<?php endif; ?>
</div>