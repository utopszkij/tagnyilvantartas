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
<h3><?php echo $this->item->szoveg; ?></h3>
<div class="contentpane">
	<div><h4>Some interesting informations</h4></div>
		<div>
		Kategoria_id: <?php echo $this->item->kategoria_id; ?>
	</div>
		
		<div>
		Szoveg: <?php echo $this->item->szoveg; ?>
	</div>
		
		<div>
		Szoveg: <?php echo $this->item->szoveg; ?>
	</div>
		
		<div>
		Kategoria_id: <?php echo $this->item->kategoria_id; ?>
	</div>
		
	</div>
 