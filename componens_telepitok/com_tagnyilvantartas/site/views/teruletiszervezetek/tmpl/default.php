<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><h2><?php echo $this->params->get('page_title');  ?></h2></div>
<h3><?php echo $this->item->nev; ?></h3>
<div class="contentpane">
	<div><h4>Some interesting informations</h4></div>
		<div>
		Terszerv_id: <?php echo $this->item->terszerv_id; ?>
	</div>
		
		<div>
		Nev: <?php echo $this->item->nev; ?>
	</div>
		
		<div>
		Nev: <?php echo $this->item->nev; ?>
	</div>
		
		<div>
		Leiras: <?php echo $this->item->leiras; ?>
	</div>
		
		<div>
		Tulaj_id: <?php echo $this->item->tulaj_id; ?>
	</div>
		
		<div>
		Terszerv_id: <?php echo $this->item->terszerv_id; ?>
	</div>
		
	</div>
 