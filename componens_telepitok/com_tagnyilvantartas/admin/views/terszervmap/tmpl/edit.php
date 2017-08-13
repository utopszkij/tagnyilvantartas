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
JToolBarHelper::title(   JText::_( 'Települések' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('terszervmap.apply', 'MENTES');
JToolBarHelper::save('terszervmap.save', 'RENDBEN');
if (!$edit) {
	JToolBarHelper::cancel('cimkek.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'terszervmap.cancel', 'MEGSEM' );
}
?>

<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task)
{
	if (task == 'terszervmap.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>

<?php
  echo '<h2>Település - területi szervezet</h2>';
 
 
  $session = JFactory::getSession();
  $errorFields = $session->get('errorFields');
  if (is_array($errorFields)) {
	foreach ($errorFields as $errorField) {
		$this->form->setFieldAttribute($errorField,'class','error'); 
	}
  }
  $session->set('errorFields',array());
?>

	<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->cimke_id);  ?>" 
       id="adminForm" name="adminForm">
	 	<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm">
		  <fieldset class="adminform">
				<div class="control-group">
					<div class="control-label">					
						Település
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('telepules');  ?>
					</div>
				</div>		
				<div class="control-group">
					<div class="control-label">					
						Kerület (csak budapestnél)
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('kerulet');  ?>
					</div>
				</div>		
				<div class="control-group">
					<div class="control-label">					
						Területi szervezet
					</div>
					<div class="controls">	
						<?php echo $this->form->getInput('terszerv_id');  ?>
					</div>
				</div>		
				
          </fieldset>                      
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt"></div>                   
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->cimke_id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="terszervmap" />
		<input type="hidden" name="cimke_id" value="<?php echo $this->item->cimke_id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>