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
JToolBarHelper::title(   JText::_( 'OEVK' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('oevk.apply', 'MENTES');
JToolBarHelper::save('oevk.save', 'RENDBEN');
if (!$edit) {
	JToolBarHelper::cancel('oevk.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'oevk.cancel', 'MEGSEM' );
}
?>

<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task)
{
	if (task == 'oevk.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>

<?php
  if ($this->item->id == 0)
     echo '<h2>Új OEVK törzsadat felvitele</h2>';
  else
     echo '<h2>OEVK törzsadat módosítása</h2>';
 
 
  $session = JFactory::getSession();
  $errorFields = $session->get('errorFields');
  foreach ($errorFields as $errorField) {
     $this->form->setFieldAttribute($errorField,'class','error'); 
  }
  $session->set('errorFields',array());
?>

	<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->id);  ?>" 
       id="adminForm" name="adminForm">
	 	<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm">
		  <fieldset class="adminform">
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('id');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('ev'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('ev');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('telepules'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('telepules');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kerulet'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('kerulet');  ?>
					</div>
				</div>		
				
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kozterulet'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('kozterulet');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kozterjellege'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('kozterjellege');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('hazszamtol'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('hazszamtol');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('hazszamig'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('hazszamig');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('paros'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('paros');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('OEVK'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('OEVK');  ?>
					</div>
				</div>		
				
          </fieldset>                      
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt"></div>                   
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="oevk" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>