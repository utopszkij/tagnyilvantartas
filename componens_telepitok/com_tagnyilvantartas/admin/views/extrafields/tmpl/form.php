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
$session = JFactory::getSession();

JToolBarHelper::title(   JText::_( 'Extrafields' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::custom('extrafields.save','','','Rendben',False);
JToolBarHelper::cancel('extrafields.megsem','Mégsem',False);


echo '<h2>Új extra mező felvitele</h2>
';
$errorFields = $session->get('errorFields');
foreach ($errorFields as $errorField) {
     $this->form->setFieldAttribute($errorField,'class','error'); 
}
$session->set('errorFields',array());
?> 
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'extrafields.megsem' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->field_id);  ?>" id="adminForm" name="adminForm">
<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm" id="extrafields">
	<div class="show">	  
          <fieldset class="adminform bal">

				<div class="control-group">
					<div class="control-label">					
						Extra mező kódneve:
					</div>
					<div class="controls">	
					    <?php
						if ($this->item->field_id == 0) {
						  echo '<input type="text" name="jform[field_name]" 
						        value="'.$this->item->field_name.'" 
								title="Csak az angol ABC kisbetüit, számokat és aláhuzás jelet tartalmazzon" />';
						} else {
						  echo $this->item->field_name;	
						  echo '<input type="hidden" name="jform[field_name]" 
						        value="'.$this->item->field_name.'" />';
						}
						?>
					</div>
				</div>		
				<div class="control-group">
					<div class="control-label">					
						Extra mező cimkéje (olvasmányos neve):
					</div>
					<div class="controls">	
						<input type="text" name="jform[field_label]"
						  value="<?php echo $this->item->field_label; ?>" /> 
					</div>
				</div>		
				<div class="control-group">
					<div class="control-label">					
						Extra mező adat tipusa:
					</div>
					<div class="controls">	
						<select type="text" name="jform[field_type]">
						  <option value="string">szöveg</option>
						  <option value="integer">szám</option>
						  <option value="phone">telefon</option>
						  <option value="email">email</option>
						</select>
					</div>
				</div>		

          </fieldset>   
          <div class="clear"></div>
          
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt">
        </div>                   
		</div>
       	<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="jform[field_id]" 
		  value="<?php echo $this->item->field_id; ?>" />
	    <input type="hidden" name="field_id" 
		  value="<?php echo $this->item->field_id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="extrafields" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="filter_kapcs_id" value="<?php echo JRequest::getVar('filter_kapcs_id') ?>" />
		<input type="hidden" name="filter_date1" value="<?php echo JRequest::getVar('filter_date1') ?>" />
		<input type="hidden" name="filter_date2" value="<?php echo JRequest::getVar('filter_date2') ?>" />
		<input type="hidden" name="limitstart" value="<?php echo JRequest::getVar('limitstart') ?>" />
		<input type="hidden" name="filter_order" value="<?php echo JRequest::getVar('filter_order') ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo JRequest::getVar('filter_order_Dir') ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	
</form>
    
    <script type="text/javascript">
    </script>
	<div class="clear"></div>
	<p>Ennek a müveletnek a végrehajtása hosszabb ideig (5-10 perc) is eltarthat.</p>          
