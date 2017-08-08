<?php
/**
* kampány statisztika részletek
* request: $this->kampany, $this->info, $this->kerdes, $this->valasz, $this->items
*/
$trClass = "row0";
if ($this->info == 'kerdes') {
	$this->info = '';
	if ($this->valasz == '') $this->valasz = 'nem válaszolt';
}
?>
<div class="kampanyStatNevek">
<center>
<h2><?php echo $this->kampany->megnev; ?></h2>
<h3><?php echo $this->kampany->idopont.' '.$this->kampany->helyszin?></h3>
<h4><?php echo $this->info.' '.$this->kerdes; ?></h4>
<h5><?php echo $this->valasz; ?></h5>
<p>Kampány statisztika részletezés</p>
<table id="articleList">
  <thead>
    <tr>
		<th>Azonosító</th>
		<th>Név</th>
		<th>Területi szervezet</th>
		<th>Kategória</th>
		<th>Telefonálás időpontja</th>
	</tr>
  </thead>
  <tbody>
	<?php foreach ($this->items as $item) : ?>
    <tr class="<?php echo $trClass; ?>">
		<?php $link = JURI::base().'index.php?option=com_tagnyilvantartas&task=kapcsolatok.show&id='.$item->kapcs_id; ?>
		<td><?php echo $item->kapcs_id; ?></td>
		<td>
		  <a href="<?php echo $link; ?>" target="adatlap">
		   <?php echo $item->nev; ?>
		  </a> 
		</td>
		<td><?php echo $item->terszerv; ?></td>
		<td><?php echo $item->status; ?></td>
		<td><?php echo $item->hivasido; ?></td>
		<?php if ($trClass=='row0') $trClass='row1'; else $trClass='row0'; ?>
    </tr>
	<?php endforeach; ?>
  </tbody>
  <tfoot>
    <?php if (count($this->items) == 0) : ?>
	<tr colspan="4"><td>Nincs adat</td></tr>
	<?php endif; ?>
  </tfoot>
</table>
<p> </p>
<p><a href="<?php echo JURI::base(); ?>index.php?option=com_tagnyilvantartas&task=kampany.statisztika&kampany_id=<?php echo $this->kampany->id; ?>">
     Vissza a statisztika oldalra
   </a>
</p>
</center>
</div>
