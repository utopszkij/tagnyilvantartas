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

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder = JRequest::getVar('filter_order','a.nev1');
$listDirn = JRequest::getVar('filter_order_Dir','asc');
$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');
$db = Jfactory::getDBO();

// szürési feltételek tárolása munkafileba
$fp = fopen(JPATH_SITE.'/tmp/filter'.$user->id.'.ini','w+');
fwrite($fp,'onlychecked='.JRequest::getVar('onlychecked','')."\n");
for ($i=0; $i<20; $i++) {
   if (JRequest::getVar('mezo'.$i,'') != '') {
      fwrite($fp,'mezo'.$i.'='.JRequest::getVar('mezo'.$i,'')."\n");
      fwrite($fp,'relacio'.$i.'='.JRequest::getVar('relacio'.$i,'')."\n");
      fwrite($fp,'ertek'.$i.'='.JRequest::getVar('ertek'.$i,'')."\n");
   }
}   
fclose($fp);

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


//$archived	= $this->state->get('filter.published') == 2 ? true : false;
//$trashed	= $this->state->get('filter.published') == -2 ? true : false;
//$params		= (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tagnyilvantartas&task=kapcsolatoks.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

    selectedTr = false;
    selectedTrClass = '';
	task = '';
    function cbClick(cb) {
       if (selectedTr) {
          selectedTr.className = selectedTrClass; 
       } 
       if (cb.checked) {
         selectedTr = cb.parentNode.parentNode;
         selectedTrClass = selectedTr.className;       
         selectedTr.className = selectedTr.className + ' kijelolt_sor';
       }  
       Joomla.isChecked(cb.checked); 
    }
	
	function nevClick(id) {
		task = document.forms.adminForm.task.value;
		document.getElementById('cb'+id).checked = true;
		document.forms.adminForm.task.value = 'kapcsolatok.show';
		document.forms.adminForm.target='_new';
		setTimeout('document.forms.adminForm.target="_self"; document.forms.adminForm.task.value = task;',1000);
		document.forms.adminForm.submit();
	}
	
	function kommentClick(id) {
		task = document.forms.adminForm.task.value;
		document.getElementById('cb'+id).checked = true;
		document.forms.adminForm.task.value = 'kommentek.browser';
		document.forms.adminForm.target='_new';
		setTimeout('document.forms.adminForm.target="_self"; document.forms.adminForm.task.value = task;',1000);
		document.forms.adminForm.submit();
	}
	
	function filterSave() {
		document.forms.adminForm.task.value="doszures.filtersave";
		document.forms.adminForm.target='';
		document.forms.adminForm.submit();
	}
</script>
<h2><?php  echo JText::_('COM_TAGNYILVANTARTAS_KAPCSOLATOK'); ?> szűrés eredménye</h2>
<?php 
  if (JRequest::getVar('funkcio')=='hirlevel') echo '<h3>Hírlevél küldés</h3>';
  if (JRequest::getVar('funkcio')=='groupedit') echo '<h3>Csoportos módosítás</h3>';
  if (JRequest::getVar('funkcio')=='export') echo '<h3>Adat export CSV-be</h3>';
?>
<form action="index.php?option=com_tagnyilvantartas&view=kapcsolatok" 
  method="post" name="adminForm" id="adminForm" target="_self">

        <div class="szuresInfo"><?php echo $filterStr; ?></div>
		<div>
		  <a class="btn" style="text-decoration:none"
		     href="<?php echo JURI::root().'tmp/filter'.$user->id.'.ini'; ?>" 
			 download="filter01.ini" type="application/octet-stream">
		    Szürő feltételek mentése a saját gépére
		  </a>
		</div>
        <div class="clear"></div>
	
<div id="editcell">
	<table class="listatabla" id="articleList">
		<thead>
			<tr id="osszesen">
				<td colspan="8">
				  Összesen: <span class="talalatok_szama"><?php  echo (int)$this->model->getTotal(); ?></span> adat
				</td>
			</tr>
			<tr>
					
				<th>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Név', 'a.nev1', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Település', 'a.telepules', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Területi szervezet', 't.nev', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Kategória', 'k.szoveg', $listDirn, $listOrder ); ?>
				</th>
				<th >
					<?php echo JHTML::_('grid.sort', 'E-mail', 'a.email', $listDirn, $listOrder ); ?>
				</th>
				<th >
					<?php echo JHTML::_('grid.sort', 'Telefonszám', 'a.telefon', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Komment', 'a.komment_db', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Megjegyzés', 'a.megjegyzes', $listDirn, $listOrder ); ?>
				</th>
			</tr> 			
		</thead>
		<tfoot>
		<tr id="lapozosor">
            <td colspan="4">
              Összesen: <span class="talalatok_szama"><?php  echo (int)$this->model->getTotal(); ?></span> adat
			  &nbsp;&nbsp;adat / oldal: 
			  <?php echo JRequest::getVar('limit'); ?>
			  <?php echo $this->pagination->getLimitBox(); ?>
            </td>
			<td colspan="4">
				<?php echo $this->pagination->getListFooter(); 
                ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
<?php
  if (count($this->items)) : 
  		foreach ($this->items as $i => $item) :
                itemAccess($item, $userCsoport);
                $canCreate  = $user->authorise('core.create');
				$canEdit    = $user->authorise('core.edit');				
				$canChange  = $user->authorise('core.edit.state'); 				
				if ($item->kapcsdatum < '1900-01-01') $item->kapcsdatum = '---';	
			    // felvitel dátuma
				$db->setQuery('select min(lastact_time) as feldat from #__tny_naplo where kapcs_id='.$db->quote($item->kapcs_id));
				$res = $db->loadObject();
				if ($res) $item->feldat = $res->feldat;	
				
				$disableClassName = '';
				$disabledLabel	  = '';
				if (!$saveOrder) {
					$disabledLabel    = JText::_('JORDERINGDISABLED');
					$disableClassName = 'inactive tip-top';
				} 
	
 				$onclick = "";
  	
    			if (JFactory::getApplication()->input->get('function', null)) {
    				$onclick= "onclick=\"window.parent.jSelectKapcsolatok_id('".$item->id."', '".$this->escape($item->lastaction)."', '','kapcs_id')\" ";
    			}  	
    
 				$link = JRoute::_( 'index.php?option=com_tagnyilvantartas&view=kapcsolatok&task=kapcsolatok.edit&id='. $item->kapcs_id );
 	
 				$caller = '';
				if ($item->telefon != '') {
					$s1 = $item->telefon;
					if (substr($s1,0,2) == '36') $s1 = '06'.substr($s1,2,20);
					if ($item->telszammegj != '') 
					   $caller = '<a href="tel:'.$s1.'" style="color:red; font-weight:bold">'.$item->telefon.'</a>';
				    else
					   $caller = '<a href="tel:'.$s1.'">'.$item->telefon.'</a>';
				}
				if ($item->telefon2 != '') $caller .= '<br />'.$item->telefon2;
				if ($item->telszammegj != '') $caller .= '<br />'.$item->telszammegj;
 	
 				$checked = JHTML::_('grid.id', $i, $item->kapcs_id);
 	 	
  		?>
				<tr class="row<?php echo $i % 2; if ($item->ellenorzott==0) echo ' nemellenorzott'; ?>">
					      
        			<td><?php 
                        //echo $checked;  
                        echo '<input type="radio" id="cb'.$item->kapcs_id.'" name="cid[]"
                           onclick="cbClick(this)" value="'.$item->kapcs_id.'"/>
                        ';
                        ?></td>
                    
			        <td class="nowrap has-context" onclick="nevClick(<?php echo $item->kapcs_id; ?>)"><var style="cursor:pointer; text-decoration:underline;">
						<?php  echo $this->escape($item->nev1.' '.$item->nev2.' '.$item->nev3).
						   '</var><br />&nbsp;&nbsp;&nbsp;&nbsp;(Gépre vitel:'.$item->feldat.')';
						?>
					</td>
					<td><?php echo $item->telepules; ?></td>
					<td><?php echo $item->nev; ?></td>
					<td><?php echo $item->szoveg; ?></td>
					<td><?php echo $item->email; ?></td>
					<td><?php echo $caller; ?></td>
					<td class="jobbra" onclick="kommentClick(<?php echo $item->kapcs_id; ?>)" style="cursor:pointer; text-decoration:underline">
					   <?php echo (int)$item->komment_db; ?>
					</td>
					<td>
					  <?php echo $item->megjegyzes; ?>
					</td>
				</tr>
<?php

  endforeach;
  else:
  ?>
	<tr>
		<td colspan="12">
			<?php echo JText::_( 'COM_TAGNYILVANTARTAS_NO_DATA' ); ?>
		</td>
	</tr>
	<?php
  endif;
  ?>
</tbody>
</table>
</div>
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="task" value="doszures.start" />
<input type="hidden" name="funkcio" value="<?php echo JRequest::getVar('funkcio','szures') ?>" />
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
</form>  	