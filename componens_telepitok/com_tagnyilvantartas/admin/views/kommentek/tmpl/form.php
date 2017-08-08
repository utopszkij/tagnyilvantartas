<?php
/**
 * @version V1.00
 * @package    joomla
 * @subpackage tagnyilvantartas
 * @author	   Fogler Tibor{author}
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

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
	if (task == '{viewName}.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>

<div id="form_{viewName}">

<h2><?php echo $this->title; ?></h2>
<form method="post" action="<?php echo JRoute::_('index.php'); ?>"
  id="adminForm" name="adminForm">
		  
        <fieldset class="adminform bal">

				<div id="komment-control-group" class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kommentszoveg'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('kommentszoveg');  ?>
					</div>
				</div>		
		</fieldset>
	    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	    <input type="hidden" name="jform[idopont]" value="<?php echo $this->item->idopont; ?>" />
	    <input type="hidden" name="jform[kapcs_id]" value="<?php echo $this->item->kapcs_id; ?>" />
	    <input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id; ?>" />

	    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
		<input type="hidden" name="view" value="kommentek" />
		<input type="hidden" name="task" value="kommentek.save" />
		<input type="hidden" name="backtask" value="<?php echo JRequest::getVar('backtask'); ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
</form>
<!-- manipuláló gombok --> 
<div class="buttons">
<?php foreach ($this->buttons as $button) : ?>
  <button type="button" class="<?php echo $button[1]; ?>" onclick="Joomla.submitbutton('<?php echo $button[0]; ?>')">
    <?php echo $button[2]; ?>
  </button -->
<?php endforeach; ?> 
</div>

</div>   
