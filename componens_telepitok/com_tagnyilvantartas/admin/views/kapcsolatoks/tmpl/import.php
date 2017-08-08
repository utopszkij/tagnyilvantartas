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
JToolBarHelper::title( JText::_( 'COM_TAGNYILVANTARTAS_IMPORT' ));
JToolBarHelper::save('kapcsolatoks.doimport','RENDBEN');
JToolBarHelper::cancel( 'kapcsolatoks.megsem', 'MEGSEM' );
?> 
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'kapcsolatoks.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		document.getElementById("turelem").style.display="block";
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
<h2>Kapcsolat adatok beolvasása CSV fájlból</h2>
<div id="turelem" class="turelem" style="display:none;">
  <div class="turelemSzoveg">Türelmet kérek.....</div>
</div>
<p style="color:red">
A gyakorlati tapasztalatok alapján nem vállt be, az hogy a felhasználók olvasnak be csv fájlokat.
Túl sok a hibalehetőség, jobb ha ezt informatikusok csinálják.<br />
Tehát ezt a funkciót csak a rendszergazda használja, a lokális (fejlesztői) szoftver példányon!
</p>
<p>Az lmp_tny_wkapcsolatok tábla struktúrának megfelelő csv fájl szükséges, az ország és a kategória szövegesen megadva. </p>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->item->kapcs_id);  ?>" enctype="multipart/form-data" id="adminForm" name="adminForm">
    <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft lmpForm" id="import">
        <fieldset class="importalapadatok">
			<div class="control-group">
			  <label>CSV file (max:<?php echo ini_get("upload_max_filesize"); ?>):</label>
			  <input type="file" name="csvfile" class="inputcsvfile" />
			   
			</div>		
			<div class="control-group">
			   <label>Karakterkészlet:</label>
			   <select name="charset" class="selectkarkod">
			      <option value="utf-8" checked="checked">utf-8</option>
			      <option value="latin2">iso-8859-2</option>
			   </select>
			</div>		
			<div class="control-group">
			  <label>Első sort kihagyni</label>
			  <input type="checkbox" name="firstignore" checked="checked" value="1" />
			</div>	
			
			<div class="control-group">
			   <label>Mezőhatároló:</label>
			   <select name="fieldterminator" class="selectcsvterm">
			      <option value="tab" checked="checked">Tabulátor</option>
			      <option value="coma">vessző</option>
			      <option value="semicolon">pontosvessző</option>
			   </select>
			</div>	

<!--
			<fieldset class="importtipus"> 

				<table>
					<tr>
						<td class="px20">
							<div class="control-group">
								<input type="radio" name="importType" checked="checked" value="0"/>
							</div>
						</td>
						<td><span class="input_magyarazat_fej">Hozzáfűzés.</span> A CSV-ben lévő azonosító számot figyelmen kívül hagyva, az adatok új számot kapnak. Ütközés ellenőrzése lehet e-mail és telefonszám alapján.
						<p><strong>Ajánlott beállítás.</strong></p>
						<div class="control-group">
						<label>E-mail és telefon ütközés esetén is beolvasni</label>
						<input type="checkbox" name="utkozestis" value="1" checked="checked" />
						<p style="margin-left:40px; line-height:110%">Javasolt, bejelölni és inkább a beolvasás után a hibaszűrő segítségével rendezni a dupla email és telefon adatokat.</p>
						</div>	

						</td>
					</tr>
					<tr>
						<td class="px20">
							<div class="control-group">
								<input type="radio" name="importType" value="1" />
							</div>
						</td>
						<td><span class="input_magyarazat_fej">Frissítés.</span> A CSV-ben lévő azonosítók alapján felülírja a tárolt adatot (ha még nincs, újnak felveszi).
						  <p><strong>FIGYELEM FONTOS!</strong>
						  Ez az üzemmód a CSV -ben lévő azonosító számok alapján működik. Ha a CSV fájlt korábban a "hozzáfűzés"
						  funkcióval betöltötte, akkor az adatok az adatbázisban más számmal szerepelnek, ilyenkor az adott CSV fájlt ezzel az opcióval soha többet nem szabad beolvasni!
						  </p>
						  <p>Gyakorlatilag ez a funkció leginkább csak a program saját CSV exporttal készült fájlok esetleges visszatöltéséhez használható.</p>
						</td>
					</tr>					
				</table>

			</fieldset>  
-->			
        </fieldset>                   
        <div class="leiras">
<!--		
		  <h4>A CSV fájl oszlopainak a következőket kell tartalmaznia:</h4>
		  <ol>
			<li>Azonosító szám</li>
			<li>Vezetéknév</li>
			<li>Keresztnév</li>
			<li>E-mail</li>
			<li>Telefon</li>
			<li>Város</li>
			<li>Irányítószám</li>
			<li>Kerület</li>
			<li>Neme ("Férfi" vagy "Nő")</li>
			<li>Utca, házszám</li>
			<li>Megjegyzés (komment)</li>
			<li>Státusz (kategória)</li>
			<li>Ter. szerv.</li>
			<li>Címkék</li>
		  </ol>
		  <p> </p>
		  <p><a href="<?php echo JURI::root(); ?>media/csvsablon_old.csv">Üres CSV sablon letöltése</a></p>
		  <p> </p>
		  <hr />
		  <p>Ezen kivül beolvasható az a CSV is, amit ez a program készít a CSV export menüpontban. 
		  Az ilyen CSV -t a program automatikusan felismeri.</p>
		  <p> </p>
		  <p><a href="<?php echo JURI::root(); ?>media/csvsablon_uj.csv">Üres CSV sablon letöltése</a></p>
előállítása</a></p>
		  <p> </p>
-->		  
		</div>		
       	<input type="hidden" name="option" value="com_tagnyilvantartas" />
		<input type="hidden" name="task" value="kapcsolatoks.doimport" />
		<input type="hidden" name="view" value="kapcsolatoks" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>
