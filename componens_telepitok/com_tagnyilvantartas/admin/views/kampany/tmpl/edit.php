<?php
/**
* @version		$Id:edit.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Set toolbar items for the page
$edit		= JFactory::getApplication()->input->get('edit', true);
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Kampany' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('kampany.apply', 'MENTES');
JToolBarHelper::save('kampany.save', 'Tárol és bezár');
if (!$edit) {
	JToolBarHelper::cancel('kampany.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'kampany.cancel', 'MEGSEM' );
}
$db = JFactory::getDBO();
?>

<style type="text/css">
 .kampanyForm .inputbox {width:300px;}
 .kampanyForm .control-label {display:inline-block; width:170px; text-align:right; }
 .kampanyForm .controls {display:inline-block; width:auto; margin-bottom:5px}
 
</style>

<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task)
{
	if (task == 'kampany.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>
<br />

<div class="kampanyForm">
<?php
  if ($this->item->id == 0) 
     echo '<h2>Új kampány felvitele</h2>';
  else {
     echo '<h2>Kampány módosítás</h2>';
	 // nézzük van-e hozzá kampany_kapcs rekord?
	 $db = JFactory::getDBO();
	 $db->setQuery('select count(kapcs_id) cc from #__tny_kampany_kapcs where kampany_id = '.$this->item->id);
	 $res = $db->loadObject();
	 if ($res->cc > 0) {
		echo '<div style="background-color:red; color:white; padding:5px;">
		Figyelem! A kampánnyal kapcsolatban már történtek telefon hívások, vannak rögzített válaszok! 
		A kérdés szövegek meggondolatlan megváltoztatása ezeket értékelhetetlenné teheti! 
		</div>
		'; 
	 }
  }
 
  // hirlevél beolvasása
  $db->setQuery('SELECT * FROM #__acymailing_mail WHERE mailid='.$db->quote($this->item->hirlevel_id));
  $hirlevel = $db->loadObject();
 
  $session = JFactory::getSession();
  $errorFields = $session->get('errorFields');
  if (is_array($errorFields)) {
    foreach ($errorFields as $errorField) {
     $this->form->setFieldAttribute($errorField,'class','error'); 
    }
  }	
  $session->set('errorFields',array());
?>

	<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->id);  ?>" 
       id="adminForm" name="adminForm">
	 	<div >
		  <fieldset class="adminform" style="float:left; width:45%">
				<div class="control-group">

					<div class="control-label">					
						<?php echo $this->form->getLabel('megnev'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('megnev');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('leiras'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('leiras');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('helyszin'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('helyszin');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('idopont'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('idopont');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('eloado'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('eloado');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('meghivott'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('meghivott');  ?>
					</div>
					
					<div class="control-label">					
						Call centeres kérdés 1:
					</div>
					<div class="controls">	
						<textarea name="jform[kerdes]" cols="80" rows="3" style="width:300px"><?php echo $this->item->kerdes; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Válaszok <br>(soronként egy válasz lehetőség)
					</div>
					<div class="controls">	
						<textarea name="jform[valaszok]" cols="80" rows="5" style="width:300px"><?php echo $this->item->valaszok; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Kérdés tipus
					</div>
					<div class="controls">	
						<select name="jform[kerdestipus]">
						  <option value="0"<?php if ($this->item->kerdestipus != 1) echo ' selected="selected"'; ?>>Csak egy válasz adható meg</option> 
						  <option value="1"<?php if ($this->item->kerdestipus == 1) echo ' selected="selected"'; ?>>Több válasz is adható</option> 
						</select>
					</div>
          <br />
					
					
					<div class="control-label">					
						Call centeres kérdés 2:
					</div>
					<div class="controls">	
						<textarea name="jform[kerdes1]" cols="80" rows="3" style="width:300px"><?php echo $this->item->kerdes1; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Válaszok <br>(soronként egy válasz lehetőség)
					</div>
					<div class="controls">	
						<textarea name="jform[valaszok1]" cols="80" rows="5" style="width:300px"><?php echo $this->item->valaszok1; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Kérdés tipus
					</div>
					<div class="controls">	
						<select name="jform[kerdestipus1]">
						  <option value="0"<?php if ($this->item->kerdestipus1 != 1) echo ' selected="selected"'; ?>>Csak egy válasz adható meg</option> 
						  <option value="1"<?php if ($this->item->kerdestipus1 == 1) echo ' selected="selected"'; ?>>Több válasz is adható</option> 
						</select>
					</div>
				</div>
			</fieldset>
				
			<fieldset class="adminform" style="float:left; width:45%">
				<div class="control-group">

					<div class="control-label">					
						<?php echo $this->form->getLabel('kezdet'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('kezdet');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('vege'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('vege');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('allapot'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('allapot');  ?>
					</div>
					<br />

					<div class="control-label">					
						<?php echo $this->form->getLabel('megjegyzes'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('megjegyzes');  ?>
					</div>
					
					<div class="control-label">					
						Call centeres kérdés 3:
					</div>
					<div class="controls">	
						<textarea name="jform[kerdes2]" cols="80" rows="3" style="width:300px"><?php echo $this->item->kerdes2; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Válaszok <br>(soronként egy válasz lehetőség)
					</div>
					<div class="controls">	
						<textarea name="jform[valaszok2]" cols="80" rows="5" style="width:300px"><?php echo $this->item->valaszok2; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Kérdés tipus
					</div>
					<div class="controls">	
						<select name="jform[kerdestipus2]">
						  <option value="0"<?php if ($this->item->kerdestipus2 != 1) echo ' selected="selected"'; ?>>Csak egy válasz adható meg</option> 
						  <option value="1"<?php if ($this->item->kerdestipus2 == 1) echo ' selected="selected"'; ?>>Több válasz is adható</option> 
						</select>
					</div>
          <br />
				
					<div class="control-label">					
						Call centeres kérdés 4:
					</div>
					<div class="controls">	
						<textarea name="jform[kerdes3]" cols="80" rows="3" style="width:300px"><?php echo $this->item->kerdes3; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Válaszok <br>(soronként egy válasz lehetőség)
					</div>
					<div class="controls">	
						<textarea name="jform[valaszok3]" cols="80" rows="5" style="width:300px"><?php echo $this->item->valaszok3; ?></textarea>
					</div>
					<br />
					<div class="control-label">					
						Kérdés tipus
					</div>
					<div class="controls">	
						<select name="jform[kerdestipus3]">
						  <option value="0"<?php if ($this->item->kerdestipus3 != 1) echo ' selected="selected"'; ?>>Csak egy válasz adható meg</option> 
						  <option value="1"<?php if ($this->item->kerdestipus3 == 1) echo ' selected="selected"'; ?>>Több válasz is adható</option> 
						</select>
					</div>
				
				</div>		
          </fieldset>                      
        </div>
		<div style="clear:both"></div>	

		<?php if ($hirlevel) : ?>
		<div>
		<h4>Hírlevél:</h4>
		<p><?php echo $hirlevel->subject; ?></p>
		</div>
		<?php endif; ?>
		
		<div class="kampanyTerszervek">
		  <h3>Érintett területi szervezetek:</h3>
		  <?php foreach($this->item->terszervek as $i => $terszerv) : ?>
		    <div style="display:inline-block; width:250px;">
		    <input type="checkbox" name="terszerv_<?php echo $i; ?>" 
		         value="<?php echo $terszerv->terszerv_id; ?>" <?php if ($terszerv->id > 0) echo ' checked="checked" '; ?> />
		    <?php echo $terszerv->nev; ?>
			</div>
		  <?php endforeach; ?>
		</div>
		
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt"></div>                   
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="kampany" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	</div>