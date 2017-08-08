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
function submitbutton(task) {
	if (task != 'kapcsolatok.cancel') {
		// javaslat form ellenörzés
		var jo = true;
		var i = 0;
		for (i=0; i<20; i++) {
			if (document.forms.javaslatForm.elements.namedItem("javaslatMezo"+i)) {
				if (document.forms.javaslatForm.elements.namedItem("javaslatMezo"+i).value=="delete") {
					if (document.forms.javaslatForm.elements.namedItem("uzenet"+i).value=="") {
					  jo = false;
					  alert("Törlési javaslathoz indoklást meg kell adni!");
					}	
				}
			}
		}
		if (jo) document.forms.javaslatForm.submit();
	}
}
</script>
<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm" id="kapcsolatok">
	<div class="show" style="float:left; width:500px;">	  
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
						<?php 
						   $s1 = $this->form->getValue('telefon');
						   if (substr($s1,0,2) == '36') $s1 = '06'.substr($s1,2,20);
						   $s2 = $this->form->getValue('telefon2');
						   if (substr($s2,0,2) == '36') $s2 = '06'.substr($s2,2,20);
						   if ($this->form->getValue('telefon') != '')
						      echo '<a href="tel:'.$s1.'">'.$this->form->getValue('telefon').'</a><br />';  
						   if ($this->form->getValue('telefon2') != '')
						      echo '<a href="tel:'.$s2.'">'.$this->form->getValue('telefon2').'</a>';  
						?>
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
    </div><!-- show -->                  
	<div class="javaslat" style="float:left; width:550px">
	    <?php 
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$cancelJS = "location='index.php?option=com_tagnyilvantartas&task=kapcsolatok.show&cid=".JRequest::getVar('kapcs_id')."';";
		?>
		<h3>Adat módosítási javaslat</h3>
		<p>Az ezen az oldalon megadott módosítási javaslatok nem kerülnek azonnal átvezetésre az adatbázisban. 
		Egy üzenetet kapnak az adatmódosításra jogosult felhasználók az ön javaslatáról. </p>
		<form name="javaslatForm" method="post" action="index.php?option=com_tagnyilvantartas&task=kapcsolatok.javaslatSave">
		<input type="hidden" name="javaslo" value="<?php echo $user->id; ?>" />
		<input type="hidden" name="kapcsolat_id" value="<?php echo JRequest::getVar('kapcs_id'); ?>" />
		<div id="javaslatSorok">
			<div id="javaslatSor1">
			   Módosítandó adat:
			   <select name="javaslatMezo1" onchange="javaslatMezoChange(event);">
				 <option value="delete">KAPCSOLAT TÖRLÉSE</option>
				 <option value="nev1" selected="selected">Vezetéknév</option>
				 <option value="nev2">Középső név</option>
				 <option value="nev3">Utónév</option>
				 <option value="titulus">Titulus</option>
				 <option value="nem">Nem</option>
				 <option value="szev">Születési év</option>
				 <option value="oevk">OEVK</option>
				 <option value="irsz">Áll. lc. Irányítószám</option>
				 <option value="telepules">Áll. lc. Település</option>
				 <option value="kerulet">Áll. lc. Kerület</option>
				 <option value="utca">Áll. lc. Közterület neve</option>
				 <option value="hazszam">Áll. lc. Házszám</option>
				 <option value="tirsz">Tart. h. Irányítószám</option>
				 <option value="ttelepules">Tart. h. Település</option>
				 <option value="tkerulet">Tart. h. Kerület</option>
				 <option value="tutca">Tart. h. Közterület neve</option>
				 <option value="thazszam">Tart. h. Házszám</option>
				 <option value="kategoria_id">Kapcsolat kategória</option>
				 <option value="terszerv_id">Területi szervezet</option>
				 <option value="email">E-mail</option>
				 <option value="email2">E-mail 2</option>
				 <option value="belsoemail">Belső E-mail</option>
				 <option value="telefon">Telefon</option>
				 <option value="telszammegj">Telefon megjegyzés</option>
				 <option value="megjegyzes">Megjegyzés</option>
				 <?php
				   $db->setQuery('select * from #__tny_extrafields order by field_id');
				   $res = $db->loadObjectList();
				   foreach ($res as $res1) {
					   echo '<option value="'.$res1->field_name.'">'.$res1->field_label.'</option>';
				   }
				 ?>
			   </select>
			   <br />Javasolt új tartalom:
			   <span id="javaslatSpan1"><input type="text" name="javaslatErtek1" value="" /></span>
			   <br />Indoklás:
			   <br /><textarea name="uzenet1" cols="60" rows="4"></textarea>

			   <button type="button" onclick="javaslatDelClick(event)" title="Javaslat sor törlése" style="display:none">Del</button>
			</div>
		</div>
		<?php echo JHTML::_( 'form.token' ); ?>
		<p><button type="button" onclick="javaslatPluszClick()" title="Új javaslat sor">+</button></p>
		<p><button type="button" onclick="submitbutton('save')">Rendben</button>
           &nbsp;
		   <button type="button" onclick="<?php echo $cancelJS; ?>">Mégsem</button>
		</p>
		</form>
		<script type="text/javascript">
		  var javaslatDb = 1;
		  var nemSelect = '<select name ="javaslatErtek1">'+
		                  '<option value="ffi">Férfi</option>'+
		                  '<option value="no">Nő</option>'+
						  '</select>';
		  var terszervSelect = '<select name ="javaslatErtek1">'+
						  <?php
						    $db->setQuery('select * from #__tny_teruletiszervezetek order by nev');
							$res = $db->loadObjectList();
							foreach ($res as $res1) {
								echo "'".'<option value="'.$res1->terszerv_id.'">'.$res1->nev.'</option>'."'+";
							}
						  ?>
						  '</select>';
						  
		  var kategoriaSelect = '<select name ="javaslatErtek1">'+
						  <?php
						    $db->setQuery('select * from #__tny_kategoriak order by szoveg');
							$res = $db->loadObjectList();
							foreach ($res as $res1) {
								echo "'".'<option value="'.$res1->kategoria_id.'">'.$res1->szoveg.'</option>'."'+";
							}
						  ?>
						  '</select>';
		  var egyebInput = '<input type="text" name="javaslatErtek1" />';				  
		  
		  function javaslatDelClick(event) {
            var b = event.target;
            var sor = b.parentNode;
			if (sor.id == "javaslatSor1") {
				 alert("Az első sor nem törölhető");
			} else {
				var parent = sor.parentNode;
				parent.removeChild(sor);
		    } 
		  }
		  function javaslatPluszClick() {
			 javaslatDb = javaslatDb + 1;
			 var sorok = document.getElementById("javaslatSorok");
			 var ujSor = document.getElementById("javaslatSor1").clone();
			 ujSor.id = "javaslatSor"+javaslatDb;
			 var c = ujSor.firstChild; 
			 c = c.nextSibling;
			 c.name = "javaslatMezo"+javaslatDb;
			 c = c.nextSibling;
			 c = c.nextSibling;
			 c = c.nextSibling;
			 c = c.nextSibling;
			 c.id = "javaslatSpan"+javaslatDb;
			 c.firstChild.name = "javaslatErtek"+javaslatDb; 
			 c = c.nextSibling; 
			 c = c.nextSibling; 
			 c.name = "javaslatErtek"+javaslatDb; 
			 c = c.nextSibling; 
			 c = c.nextSibling; 
			 c = c.nextSibling; 
			 c.name="uzenet"+javaslatDb;
			 c.nextSibling;	
			 c.style.display = "inline-block";
			 sorok.appendChild(ujSor);
		  }
		  function javaslatMezoChange(event) {
			 var mezoSelect = event.target;
			 var mezo = mezoSelect.options[mezoSelect.selectedIndex].value;
			 var c = mezoSelect.nextSibling;
			 c = c.nextSibling;
			 c = c.nextSibling;
			 c = c.nextSibling;
			 var n = c.firstChild.name;
			 if (mezo == "nem") 
				 c.innerHTML = nemSelect;
			 else if (mezo == "terszerv_id") 
				 c.innerHTML = terszervSelect;
			 else if (mezo == "kategoria_id") 
				 c.innerHTML = kategoriaSelect;
			 else if (mezo == "delete") 
				 c.innerHTML = "---";
			 else  
				 c.innerHTML = egyebInput;
			 c.firstChild.name = n;
		  }
		</script>
	</div> <!-- javaslat -->
</div>


	
    
