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
$userCsoport = $session->get('userCsoport');
itemAccess($this->item, $userCsoport);
formAccess($this->form, $userCsoport); 


// Set toolbar items for the page
$edit		= JFactory::getApplication()->input->get('edit', true);
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Kapcsolatok' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::cancel('doszures.megsem','Rendben');
if ($userCsoport->jog_nev == 'RW')
  JToolBarHelper::editList('doszures.edit');
JToolBarHelper::custom('kommentek.browser','','','Kommentek',true);
JToolBarHelper::custom('kapcsolatok.naplo','','','Változás történet',true);

// kodolt mezők rendbetétele
$db = JFactory::getDBO();
$db->setQuery('select * from #__tny_kategoriak 
where kategoria_id='.$this->item->kategoria_id);
$res = $db->loadObject();
if ($res) 
  $this->item->kategoria_nev = $res->szoveg; 
else  
  $this->item->kategoria_nev = $this->item->kategoria_id;
$db->setQuery('select * from #__tny_teruletiszervezetek 
where terszerv_id='.$this->item->terszerv_id);
$res = $db->loadObject();
if ($res) 
  $this->item->terszerv_nev = $res->nev; 
else  
  $this->item->terszerv_nev = $this->item->terszerv_id;

echo '<h2>'.JText::_('COM_TAGNYILVANTARTAS_KAPCSOLATOK_SHOW').'</h2>';
$errorFields = $session->get('errorFields');
foreach ($errorFields as $errorField) {
     $this->form->setFieldAttribute($errorField,'class','error'); 
}
$session->set('errorFields',array());
?> 
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'kapcsolatok.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->kapcs_id);  ?>" id="adminForm" name="adminForm">
<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm" id="kapcsolatok">
	<div class="show">	  
          <fieldset class="adminform bal">

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('nev1'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('nev1');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('nev2'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('nev2');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('nev3'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('nev3');  ?>
					</div>
				</div>		

                <div class="allandolakcim">
                    <h3><?php echo JText::_('ALLANDOLAKCIM'); ?></h3> 
                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('telepules'); ?>
                        </div>
                        
                        <div class="controls">	
                            <?php echo $this->form->getValue('telepules');  ?>
                        </div>
                    </div>		

                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('utca'); ?>
                        </div>
                        
                        <div class="controls">	
                            <?php echo $this->form->getValue('utca');  ?>
                        </div>
                    </div>		

                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('kjelleg'); ?>
                        </div>
                        
                        <div class="controls">	
                            <?php echo $this->form->getValue('kjelleg');  ?>
                        </div>
                    </div>		

                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('hazszam'); ?>
                        </div>
                        
                        <div class="controls">	
                            <?php echo $this->form->getValue('hazszam');  ?>
                            &nbsp;<?php echo $this->form->getValue('cimkieg');  ?>
                        </div>
                    </div>		

                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('irsz'); ?>
                        </div>
                        <div class="controls">	
                            <?php echo $this->form->getValue('irsz');  ?>
                            <span class="label2"><?php echo JText::_('kerulet'); ?></span>
                            <?php echo $this->form->getValue('kerulet');  ?>
                            
                        </div>
                    </div>		
                </div> 
                <h3><?php echo JText::_('TARTOZKODAS'); ?></h3> 
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('ttelepules'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('ttelepules');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('tutca'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('tutca');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('tkjelleg'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('tkjelleg');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('thazszam'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getValue('thazszam');  ?>
						&nbsp;
						<?php echo $this->form->getValue('tcimkieg');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('tirsz'); ?>
                    </div>
					<div class="controls">	
						<?php echo $this->form->getValue('tirsz');  ?>
                        <span class="label2"><?php echo JText::_('kerulet'); ?></span>
						<?php echo $this->form->getValue('tkerulet');  ?>
					</div>
				</div>		

            </fieldset>
            
            <fieldset class="adminform jobb">
	
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('titulus'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('titulus');  ?>
					</div>
				</div>		

               <div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('email'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('email');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('email2'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('email2');  ?>
					</div>
				</div>		

                <div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('nem'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('nem');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('telefon'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('telefon');  ?><br />
						<?php echo $this->form->getValue('telefon2');  ?><br />
						<?php echo $this->form->getValue('telszammegj');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('oevk'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('oevk');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('szev'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('szev');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kapcsnev'); ?>
					</div>
					<div class="controls">	
						<?php echo $this->form->getValue('kapcsnev');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kapcsdatum'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('kapcsdatum');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('kategoria_id'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->item->kategoria_nev;  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('terszerv_id'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->item->terszerv_nev;  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('belsoemail'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getValue('belsoemail');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('hirlevel'); ?>
					</div>
					
					<div class="controls">	
						<?php if ($this->item->hirlevel==1) echo 'Igen'; else echo 'Nem';  ?>
						<?php echo $this->form->getLabel('ellenorzott');  ?>
						<?php if ($this->item->ellenorzott==1) echo 'Igen'; else echo 'Nem';  ?>
					</div>
				</div>		
                <div class="control-group"><?php echo $this->form->getLabel('cimkek');  ?>
					         <?php 
                   $cimkek = explode(',',$this->form->getValue('cimkek'));
                   foreach ($cimkek as $cimke) {
                      $cimke = trim($cimke);
                      $klink = 'index.php?option=com_tagnyilvantartas&view=doszures'.
                      '&task=doszures.start&mezo1=cimkek&relacio1=like&ertek1='.$cimke;
                      echo '<a href="'.$klink.'">'.$cimke.'</a>&nbsp;';
                   }   
                   ?>
                </div>
				
								<?php
				// Most következik az extra fieldek megjelenitése.
				// Ha egy extra fieldet elhelyeztünk a fenti (disajnolt) területen akkor az alábbi részen már nem kell
				$db->setQuery('select * from #__tny_extrafields order by field_id');
				$extraFields = $db->loadObjectList();
				foreach ($extraFields as $extraField) {
					if (($extraField->field_name != '123') &
						($extraField->field_name != 'telefon2')) {
						$fieldName = $extraField->field_name;	
						echo '<div class="control-group">
						<div class="control-label">'.$extraField->field_label.'</div>
						<div class="controls">
						';		
						if ($this->form->getField($fieldName)) 
							echo $this->form->getValue($fieldName);
						else	
							echo $this->item->$fieldName;
						echo '	
						</div>
						</div>
						';
					} 
					
				}
				?>

				
          </fieldset>   
          <div class="clear"></div>
		  <div class="control-group">
				<div class="control-label">					
					<?php echo $this->form->getLabel('megjegyzes'); ?>
				</div>
				<div class="controls">	
					<?php echo $this->form->getValue('megjegyzes');  ?>
				</div>
		  </div>		

          
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt">
        </div>                   
		</div>
       	<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->kapcs_id ?>" />
	    <input type="hidden" name="boxchecked" value="1" />
	    <input type="hidden" name="kapcs_id" value="<?php echo $this->item->kapcs_id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="doszures" />
		<input type="hidden" name="backtask" value="doszures.show" />
		<input type="hidden" name="onlychecked" value="<?php echo JRequest::getVar('onlychecked'); ?>" />
		<?php
		   for ($i=1; $i<20; $i++) {
			  if (JRequest::getVar('mezo'.$i) != '') {
				 echo '<input type="hidden" name="mezo'.$i.'" value="'.JRequest::getVar('mezo'.$i).'" />'."\n";
				 echo '<input type="hidden" name="relacio'.$i.'" value="'.JRequest::getVar('relacio'.$i).'" />'."\n";
				 echo '<input type="hidden" name="ertek'.$i.'" value="'.JRequest::getVar('ertek'.$i).'" />'."\n";
			  } 
		   }
		?>
		<?php echo JHTML::_( 'form.token' ); ?>
	
</form>
    
    <script type="text/javascript">
       function cimkeClick() {
          var d = document.getElementById('cimkekPopup');
          d.style.display="block";          
       }
       function popupClose() {
          var d = document.getElementById('cimkekPopup');
          d.style.display="none";          
       }
       function cimkeChange() {
          var cs = document.forms.adminForm.cimkekselect;
          var input = document.forms.adminForm.jform_cimkek;
          var ci = cs.selectedIndex;
          var s = cs.options[ci].value;
          if (input.value == '')
              input.value = s;
          else
              input.value = input.value + ', '+s;
       }
      setTimeout("window.scrollTo(0,190); document.getElementById('jform_nev1').focus();",500);
    </script>