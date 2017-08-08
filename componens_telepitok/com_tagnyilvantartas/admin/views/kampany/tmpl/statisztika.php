<?php 
// statisztika képernyő

$trClass = 'row0';
$elozoKerdes = '';
$felvette = 0;
$megnyitotta = 0;
$szazalek = '';

?>
<div class="kampanyStatisztika">
	<h2><?php echo $this->statisztika->megnev?></h2>
	<blockquote>
	  <p><?php echo $this->statisztika->idopont.' '. $this->statisztika->helyszin; ?></p>
	  <p>Előadó: <?php echo $this->statisztika->eloado; ?></p>
	</blockquote>
	<h3>Statisztika</h3>
	<blockquote>
		<p><?php echo date('Y.m.d H:i')?> állapot</p>
		<center>
		<table>
		<tbody>
		<?php foreach($this->statisztika->lines as $line) : ?>
		  <tr class="<?php echo $trClass; ?>">
			<?php 
				$link = JURI::base().'index.php?option=com_tagnyilvantartas&task=kampany.nevek'.
				   '&kampany_id='.$this->statisztika->id.
				   '&darab='.$line->darab.
				   '&data='.str2hex($line->info.'_'.$line->kerdes.'_'.$line->valasz);
			   if (($line->info == 'kerdes') & ($line->valasz == '')) 
				 $line->valasz = 'Nem válaszolt'; 
			   if (($line->info == 'kerdes') & ($line->kerdes == $elozoKerdes)) 
				   $line->kerdes = '';
			   else
				   $elozoKerdes = $line->kerdes;
			   if (substr($line->info,0,3) == '(7)') $felvette = $line->darab;
			   if (substr($line->info,0,3) == '(3)') $megnyitotta = $line->darab;
			   if ($line->info == 'kerdes') {
				   $line->info = '';
				   if ($felvette > 0) {
					   $szazalek = round($line->darab * 100 / $felvette).' % (7)-hez viszonyítva';
				   }
			   } else if (substr($line->info,0,3) == '(4)') {	   
				   if ($megnyitotta > 0) {
					   $szazalek = round($line->darab * 100 / $megnyitotta).' % (3)-hez viszonyítva';
				   }
			   } else if (substr($line->info,0,3) == '(5)') {	   
				   if ($megnyitotta > 0) {
					   $szazalek = round($line->darab * 100 / $megnyitotta).' % (3)-hez viszonyítva';
				   }
			   } else {
				   $szazalek = '';
			   }
			?>
			<td><?php echo $line->info; ?>&nbsp;
			    <?php echo $line->kerdes; ?></td>
			<td><?php echo $line->valasz; ?></td>
			</td>	
			<td align="right">
			   <a href="<?php echo $link; ?>"><?php echo $line->darab; ?></a>
			</td>
			<td>
			  <?php echo $szazalek; ?>
			</td>
			<?php 
			   if ($trClass == 'row1') $trClass = 'row0'; else $trClass = 'row1'; 
			?>
		  </tr>
		<?php endforeach; ?>
		</tbody>
		</table>
		<p>A darabszámokra kattintva részletesebb információkat kaphatunk.</p>
		<a class="btn" href="<?php echo JURI::base(); ?>index.php?option=com_tagnyilvantartas&task=kampany.statexport&kampany_id=<?php echo $this->statisztika->id; ?>">
		  Exportálás CSV fájlba
		</a>
		</center>
	</blockquote>
</div>
