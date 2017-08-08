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
JToolBarHelper::title(   JText::_( 'Felhcsoportok' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('felhcsoportok.apply', 'MENTES');
JToolBarHelper::save('felhcsoportok.save', 'RENDBEN');
if (!$edit) {
	JToolBarHelper::cancel('felhcsoportok.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'felhcsoportok.cancel', 'MEGSEM' );
}

if ($this->item->cimke_id == 0)
    echo '<h2>'.JText::_('COM_TAGNYILVANTARTAS_FELHCSOPORTOK_ADD').'</h2>';
else
    echo '<h2>'.JText::_('COM_TAGNYILVANTARTAS_FELHCSOPORTOK_EDIT').'</h2>';

$session = JFactory::getSession();
$errorFields = $session->get('errorFields');
foreach ($errorFields as $errorField) {
   $this->form->setFieldAttribute($errorField,'class','error'); 
}
$session->set('errorFields',array());

/*
if ($this->form->getValue('jog_email')=='') 
    $this->form->setValue('jog_email',null,'X');
if ($this->form->getValue('jog_nev')=='') 
    $this->form->setValue('jog_nev',null,'X');
if ($this->form->getValue('jog_telefonszam')=='') 
    $this->form->setValue('jog_telefonszam',null,'X');
if ($this->form->getValue('jog_lakcim')=='') 
    $this->form->setValue('jog_lakcim',null,'X');
if ($this->form->getValue('jog_tarthely')=='') 
    $this->form->setValue('jog_tarthely',null,'X');
if ($this->form->getValue('jog_oevk')=='') 
    $this->form->setValue('jog_oevk',null,'X');
if ($this->form->getValue('jog_szev')=='') 
    $this->form->setValue('jog_szev',null,'X');
if ($this->form->getValue('jog_kapcskat')=='') 
    $this->form->setValue('jog_kapcskat',null,'X');
if ($this->form->getValue('jog_kapcster')=='') 
    $this->form->setValue('jog_kapcster',null,'X');
if ($this->form->getValue('jog_kapcscimkek')=='') 
    $this->form->setValue('jog_kapcscimkek',null,'X');
if ($this->form->getValue('jog_kapcshirlevel')=='') 
    $this->form->setValue('jog_kapcshirlevel',null,'X');
if ($this->form->getValue('jog_ellenorzott')=='') 
    $this->form->setValue('jog_ellenorzott',null,'X');
*/

?>

<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task)
{
	if (task == 'felhcsoportok.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->fcsop_id);  ?>" id="adminForm" name="adminForm">
  <div id="felhCsop">
	<div class="control-group">
		<div class="control-label">					
			<?php echo $this->form->getLabel('kod'); ?>
		</div>
		<div class="controls">	
			<?php echo $this->form->getInput('kod');  ?>
		</div>
	</div>		
	<div class="control-group">
		<div class="control-label">					
            <?php echo $this->form->getLabel('nev'); ?>
		</div>
		<div class="controls">	
			<?php echo $this->form->getInput('nev');  ?>
		</div>

	</div>		
    
    <div class="alul1">
		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_felhasznalok'); ?>
			</div>
					
			<div class="controls">	
				<?php echo $this->form->getInput('jog_felhasznalok');  ?>
			</div>
		</div>		

		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_terszerv'); ?>
			</div>
					
			<div class="controls">	
				<?php echo $this->form->getInput('jog_terszerv');  ?>
			</div>
		</div>		
		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_kategoriak'); ?>
			</div>
				
			<div class="controls">	
				<?php echo $this->form->getInput('jog_kategoriak');  ?>
			</div>
		</div>		
		<div class="control-group">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_kapcsolat'); ?> **
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_kapcsolat');  ?>
				</div>
		</div>		
    </div>
    <div class="alul2">
		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_cimkek'); ?>
			</div>
					
			<div class="controls">	
				<?php echo $this->form->getInput('jog_cimkek');  ?>
			</div>
		</div>		

		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_csoportos'); ?> **
			</div>
					
			<div class="controls">	
				<?php echo $this->form->getInput('jog_csoportos');  ?>
			</div>
		</div>		

		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_hirlevel'); ?> **
			</div>
					
			<div class="controls">	
				<?php echo $this->form->getInput('jog_hirlevel');  ?>
			</div>
		</div>		

		<div class="control-group">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_csv'); ?> **
			</div>
					
			<div class="controls">	
				<?php echo $this->form->getInput('jog_csv');  ?>
			</div>
		</div>		
    </div>
    <div class="clear"></div>
    <p>** A megjelölt jogokat csak a területi hatáskörében gyakorolhatja</p>
    <p> </p>
    <p>A területi hatáskörébe tartozó kapcsolat adatokon belül:</p>
    <div id="felhCsop-bal">
        <div class="felhCsop-fejlec">
           <div class="fejlec-bal">
             Mező név
           </div>
           <div class="fejlec-jobb">
             &nbsp;R&nbsp;&nbsp;&nbsp;RW&nbsp;&nbsp;&nbsp;X&nbsp;
           </div>
        </div>
        <div class="control-group row0">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_email'); ?>
			</div>
			<div class="controls">	
				<?php echo $this->form->getInput('jog_email');  ?>
			</div>
		</div>		
        <div class="control-group row1">
			<div class="control-label">					
				<?php echo $this->form->getLabel('jog_nev'); ?>
			</div>
			<div class="controls">	
				<?php echo $this->form->getInput('jog_nev');  ?>
			</div>
		</div>		
		<div class="control-group row0">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_telefonszam'); ?>
				</div>
				<div class="controls">	
					<?php echo $this->form->getInput('jog_telefonszam');  ?>
				</div>
		</div>		
		<div class="control-group row1">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_lakcim'); ?>
				</div>
				<div class="controls">	
					<?php echo $this->form->getInput('jog_lakcim');  ?>
				</div>
		</div>		
		<div class="control-group row0">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_tarthely'); ?>
				</div>
				<div class="controls">	
					<?php echo $this->form->getInput('jog_tarthely');  ?>
				</div>
		</div>		
		<div class="control-group row1">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_oevk'); ?>
				</div>
				<div class="controls">	
					<?php echo $this->form->getInput('jog_oevk');  ?>
				</div>
		</div>		
    </div><!-- bal -->
    
    <div id="felhCsop-jobb">
            <div class="felhCsop-fejlec">
               <div class="fejlec-bal">
                 Mező név
               </div>
               <div class="fejlec-jobb">
                 &nbsp;R&nbsp;&nbsp;&nbsp;RW&nbsp;&nbsp;&nbsp;X&nbsp;
               </div>
            </div>
			<div class="control-group row0">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_szev'); ?>
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_szev');  ?>
				</div>
			</div>		

			<div class="control-group row1">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_kapcskat'); ?>
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_kapcskat');  ?>
				</div>
			</div>		

			<div class="control-group row0">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_kapcster'); ?>
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_kapcster');  ?>
				</div>
			</div>		

			<div class="control-group row1">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_kapcscimkek'); ?>
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_kapcscimkek');  ?>
				</div>
			</div>		

			<div class="control-group row0">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_kapcshirlevel'); ?>
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_kapcshirlevel');  ?>
				</div>
			</div>		

			<div class="control-group row1">
				<div class="control-label">					
					<?php echo $this->form->getLabel('jog_ellenorzott'); ?>
				</div>
					
				<div class="controls">	
					<?php echo $this->form->getInput('jog_ellenorzott');  ?>
				</div>
			</div>		
    </div><!-- jobb -->
    <div class="clear"></div>
    
    <p>Jelmagyarázat: R: olvashatja, RW: Írhatja és olvashatja, X: Nem láthatja</p>
    <div class="clear"></div>
    

  </div><!-- felhcsop -->
  <input type="hidden" name="option" value="com_tagnyilvantartas" />
  <input type="hidden" name="cid[]" value="<?php echo $this->item->fcsop_id ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="view" value="felhcsoportok" />
  <input type="hidden" name="fcsop_id" value="<?php echo $this->item->fcsop_id; ?>" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>

