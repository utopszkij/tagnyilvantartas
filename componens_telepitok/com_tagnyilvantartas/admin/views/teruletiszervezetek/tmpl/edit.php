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
JToolBarHelper::title(   JText::_( 'Teruletiszervezetek' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('teruletiszervezetek.apply', 'MENTES');
JToolBarHelper::save('teruletiszervezetek.save', 'RENDBEN');
if (!$edit) {
	JToolBarHelper::cancel('teruletiszervezetek.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'teruletiszervezetek.cancel', 'MEGSEM' );
}
if ($this->item->terszerv_id == 0)
  echo '<h2>'.JText::_('COM_TAGNYILVANTARTAS_TERULETISZERVEZETEK_ADD').'</h2>';
else
  echo '<h2>'.JText::_('COM_TAGNYILVANTARTAS_TERULETISZERVEZETEK_EDIT').'</h2>';

$session = JFactory::getSession();
$errorFields = $session->get('errorFields');
foreach ($errorFields as $errorField) {
  $this->form->setFieldAttribute($errorField,'class','error'); 
}
$session->set('errorFields',array());

?>

<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task)
{
	if (task == 'teruletiszervezetek.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>

	 	<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->terszerv_id);  ?>" id="adminForm" name="adminForm">
	 	<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm" id="teruletiszervezetek">
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('nev'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('nev');  ?>
					</div>
				</div>		
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('leiras'); ?>
					</div>
				<?php if(version_compare(JVERSION,'3.0','lt')): ?>
				<div class="clr"></div>
				<?php  endif; ?>						
				<div class="controls">	
					<?php echo $this->form->getInput('leiras');  ?>
				</div>
				</div>		
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('tulaj_id'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('tulaj_id');  ?>
					</div>
				</div>		
          </fieldset>                      
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt">
        </div>                   
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->terszerv_id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="teruletiszervezetek" />
        <input type="hidden" name="terszerv_id" value="<?php echo $this->item->terszerv_id; ?>" />    
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>