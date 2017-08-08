<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

JToolBarHelper::title(   JText::_( 'Groupedit' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::save('doszures.groupedit3','RENDBEN');
JToolBarHelper::cancel('kapcsolatoks.megsem','MEGSEM');


$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder = JRequest::getVar('filter_order','a.nev1');
$listDirn = JRequest::getVar('filter_order_Dir','asc');
$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');
$db = Jfactory::getDBO();

// filterStr kialakitása a JRequest-ben érkezett adatokból.
$filterStr = '';
if (JRequest::getVar('onlychecked')==1) $filterStr = 'Csak az ellenőrzött adatok';
for ($i=1; $i<20; $i++) {
    if (JRequest::getVar('mezo'.$i) != '') {
       if ($filterStr != '') 
           $filterStr .= ' és <br />';           
       $rel = JRequest::getVar('relacio'.$i);
       if ($rel == 'lt') $rel = '&lt;';
       if ($rel == 'lte') $rel = '&lt;=';
       if ($rel == 'gt') $rel = '&gt;';
       if ($rel == 'gte') $rel = '&gt;=';
       if ($rel == 'ne') $rel = '&lt;&gt;';
       if ($rel == 'like') $rel = 'benne;';
       if ($rel == 'between') $rel = 'tól-ig;';
	   $mezoNev = JRequest::getVar('mezo'.$i,'');
	   $mezoLabel = JText::_($mezoNev);
	   if ($mezoLabel == $mezoNev) {
		   // nem talált hozzá forditást, lehet, hogy extrafield....
		   $db->setQuery('select * from #__tny_extrafields where field_name="'.$mezoNev.'"');
		   $res = $db->loadObject();
		   if ($res) $mezoLabel = $res->field_label;
	   }
       $filterStr .= $mezoLabel.' '.
                     $rel.' '.
                     JRequest::getVar('ertek'.$i);
    }    
}



?>
<script type="text/javascript">

</script>
<h2><?php  echo JText::_('COM_TAGNYILVANTARTAS_GROUPEDIT'); ?> </h2>
<h3><?php echo $this->Darab;?>.db adat</h3>
<form action="index.php?option=com_tagnyilvantartas&view=kapcsolatok" 
  method="post" name="adminForm" id="adminForm" target="_self">

        <div class="szuresInfo"><?php echo $filterStr; ?></div>
        <div class="clear"></div>
<div id="groupedit" class="lmpForm">	
<div class="info">A beirt adatok a szürésnek megfelelő ÖSSZES adatban át lesznek írva. 
Ha üresen hagy egy mezőt akkor annak értéke változatlan marad. Ha üresre akarja módosítani valamelyik mező értékét akkor irja a mezőbe az "ÜRES" szöveget (csupa nagybetüvel, idézőjel nélkül).</div>	
          <fieldset>
                <div class="allandolakcim">
                    <h3><?php echo JText::_('ALLANDOLAKCIM'); ?></h3> 

                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('utca'); ?>
                        </div>
                        
                        <div class="controls">	
                            <?php echo $this->form->getInput('utca');  ?>
                        </div>
                    </div>		

                    <div class="control-group">
                        <div class="control-label">					
                            <?php echo $this->form->getLabel('kjelleg'); ?>
                        </div>
                        
                        <div class="controls">	
                            <?php echo $this->form->getInput('kjelleg');  ?>
                        </div>
                    </div>		

                </div> 
				
				<div class="tartozkodas">
					<h3><?php echo JText::_('TARTOZKODAS'); ?></h3> 
					<div class="control-group">
						<div class="control-label">					
							<?php echo $this->form->getLabel('tutca'); ?>
						</div>
						
						<div class="controls">	
							<?php echo $this->form->getInput('tutca');  ?>
						</div>
					</div>		

					<div class="control-group">
						<div class="control-label">					
							<?php echo $this->form->getLabel('tkjelleg'); ?>
						</div>
						
						<div class="controls">	
							<?php echo $this->form->getInput('tkjelleg');  ?>
						</div>
					</div>		
				</div>	

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('oevk'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('oevk');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('terszerv_id'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('terszerv_id');  ?>
					</div>
				</div>		

          </fieldset>   

          <div class="clear"></div>
	
</form>

<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="task" value="doszures.groupedit3" />
<input type="hidden" name="view" value="kapcsolatok" />
<input type="hidden" name="backtask" value="doszures.start" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="onlychecked" value="<?php echo JRequest::getVar('onlychecked') ?>" />
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
</div>
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