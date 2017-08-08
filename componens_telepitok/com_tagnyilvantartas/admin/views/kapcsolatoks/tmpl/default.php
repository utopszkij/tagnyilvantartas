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
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$params		= (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'ordering';
$db = JFactory::getDBO();

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tagnyilvantartas&task=kapcsolatoks.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');
$userTerhats = $session->get('userTerhats');


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
		document.getElementById('cb'+id).checked = true;
		document.forms.adminForm.task.value = 'kapcsolatok.show';
		document.forms.adminForm.submit();
	}
	
	function kommentClick(id) {
		document.getElementById('cb'+id).checked = true;
		document.forms.adminForm.task.value = 'kommentek.browser';
		document.forms.adminForm.target='_new';
		setTimeout('document.forms.adminForm.target="_self";',1000);
		document.forms.adminForm.submit();
	}
</script>

<h2><?php echo JText::_('COM_TAGNYILVANTARTAS_KAPCSOLATOK'); ?></h2>

<?php
  // aktiv javaslatok vannak?
  if (($userCsoport->kod == 'A') | ($userCsoport->kod == 'SM')) {
        $cf = 'k.terszerv_id in (0';
		if (is_array($userTerhats)) {
			foreach ($userTerhats as $userTerhat) {
				$cf .= ','.$userTerhat->terszerv_id;
			}
		}
        $cf .= ')';
		$db->setQuery('select count(*) cc
			from #__tny_javaslat j
			left outer join #__tny_kapcsolatok k on k.kapcs_id = j.kapcs_id
			where '.$cf.' and j.allapot="javaslat"
		');
		$res = $db->loadObject();
		$javaslatCC = $res->cc;
		if ($javaslatCC > 0) {
			echo '<div style="background-color:#D0F0F0; padding:10px;">
			  <a href="index.php?option=com_tagnyilvantartas&task=kapcsolatok.javaslatok">'.$javaslatCC.' db elbírálásra váró javaslat.</a>
			</div>
			';
			
		}
    echo '<h4>Javaslatok száma '.$javaslatCC.'</h4>';
  }

?>

<form action="index.php?option=com_tagnyilvantartas&view=kapcsolatok" 
  method="post" name="adminForm" id="adminForm" target="_self">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">				
				<input type="text" name="filter_search" id="filter_search" 
				  title="Név, email, telefonszám, település, megjegyzés (részlet is lehet)" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
				<span style="font-size:12px; color:black; vertical-align:bottom">&nbsp;Név, település, email, telefon, megjegyzés (részlet) -re lehet szűrni.</span>  
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="this.form.filter_search.value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
            <div class="filterEllenorzott">
                &nbsp;<input type="radio" <?php if ($this->state->get('filter.onlychecked')=='1') echo ' checked="checked"'; ?> name="filter_onlychecked" onclick="this.form.submit()" value="1" /> 
                Csak az ellenőrzött adatok
                &nbsp;<input type="radio" <?php if ($this->state->get('filter.onlychecked')!='1') echo ' checked="checked"'; ?> name="filter_onlychecked" onclick="this.form.submit()" value="0" /> 
                Minden adat
            </div>
            <div class="clear"></div>
		</div>
		<div class="clearfix"> </div>

	
<div id="editcell">
	<table class="listatabla" id="articleList">
		<thead>
			<tr id="osszes">
				<td colspan="4">
				  Összesen: <span class="talalatok_szama"><?php  echo (int)$this->getModel('kapcsolatoks')->getTotal(); ?></span> adat &nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				<td colspan="5">
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
					<?php echo JHTML::_('grid.sort', 'Komment<br /><span style="color:#ed8957">(Olvasatlan)</span>', 'a.komment_db', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Megjegyzés', 'a.megjegyzes', $listDirn, $listOrder ); ?>
				</th>
			</tr> 			
		</thead>
		<tfoot>
		<tr id="lapozosor">
            <td colspan="4">
              Összesen: <span class="talalatok_szama"><?php  echo (int)$this->getModel('kapcsolatoks')->getTotal(); ?></span> adat &nbsp;&nbsp;&nbsp;&nbsp;
			  adat / oldal: 
			  <?php echo JRequest::getVar('limit'); ?>
			  <?php echo $this->pagination->getLimitBox(); ?>
            </td>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
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

				//+ telszám popup
				if ($item->telmegj2 != '') {
				  $wt = explode(',',$item->telmegj2); // Friisitve:éééé-hh-nn HH.ii:ss, Szimpatizáns:Igen|Nem, Hírlevelet kér:Igen|Nem, Hívhatjuk:Igen|Nem
				  $telSzimp = 0;
				  $telHirlevel = 0;	
				  $telHivhato = 0;
				  if (mb_strpos($wt[1],'Igen') > 1) $telSzimp = true;	
				  if (mb_strpos($wt[2],'Igen') > 1) $telHirlevel = true;	
				  if (mb_strpos($wt[3],'Igen') > 1) $telHivhato = true;	
				} else {
				  $telSzimp = true;	
				  $telHirlevel = true;	
				  $telHivhato = true;	
				}  
 				$caller = '';
				if ($item->telefon != '') {
					$s1 = $item->telefon;
					if (substr($s1,0,2) == '36') $s1 = '06'.substr($s1,2,20);
					if ($item->telszammegj != '') 
					   $caller = '<a id="tsz'.$item->kapcs_id.'" 
				                     onclick="telszamClick('.$item->kapcs_id.','.$telSzimp.','.$telHirlevel.','.$telHivhato.')" 
									 href="tel:'.$s1.'" style="color:red; font-weight:bold">'.
				                 $item->telefon.'</a>';
				    else
					   $caller = '<a id="tsz'.$item->kapcs_id.'" 
				                     onclick="telszamClick('.$item->kapcs_id.','.$telSzimp.','.$telHirlevel.','.$telHivhato.')" 
									 href="tel:'.$s1.'">'.$item->telefon.'</a>';
				}
				if ($item->telefon2 != '')
				   $caller .= '<br />'.$item->telefon2;
				$caller .= '<br /><span id="tm'.$item->kapcs_id.'">'.$item->telszammegj.'<span>';
				//- telszám popup
 	
 				$checked = JHTML::_('grid.id', $i, $item->kapcs_id);
				
				
				//+ olvasatlan kommentek megjelenitése
				$item->olvasatlan_komment_db = 0;
				$db->setQuery('select count(k.kapcs_id) cc
				from #__tny_kommentek k
				left outer join #__tny_kommentolvasasok ko on ko.kapcs_id = k.kapcs_id and 
				                                              ko.komment_id = k.id and
				                                              olvaso_id='.$user->id.'
				where k.kapcs_id='.$db->quote($item->kapcs_id).' and ko.olvaso_id is null and
				k.user_id <> '.$user->id);
				$olvasatlanok = $db->loadObject();
				//DBG echo $db->getQuery().'<br />';
				$item->olvasatlan_komment_db = $olvasatlanok->cc;
				if ($item->olvasatlan_komment_db > 0) {
				   $komment_title = '';
				   $db->setQuery('select kommentszoveg
				   from #__tny_kommentek
				   where kapcs_id = '.$item->kapcs_id.'
				   order by idopont desc
				   limit 3
				   ');
					$komments = $db->loadObjectList();
				   foreach ($komments as $komment)
                     $komment_title .= mb_substr($komment->kommentszoveg,0,25)."...\n------\n";				   
				}
				//- olvasatlan kommentek megjelenitése
				
  		?>
				<tr class="row<?php echo $i % 2; if ($item->ellenorzott==0) echo ' nemellenorzott'; ?>">
					      
        			<td><?php 
                        //echo $checked;  
                        echo '<input type="radio" id="cb'.$item->kapcs_id.'" name="cid[]"
                           onclick="cbClick(this)" value="'.$item->kapcs_id.'"
						   title="'.$item->kapcs_id.'" />
                        ';
                        ?></td>
                    
        				
			        <td class="nowrap has-context" onclick="nevClick(<?php echo $item->kapcs_id; ?>)"><var style="cursor:pointer; text-decoration:underline;">
						<?php  echo $this->escape($item->nev1.' '.$item->nev2.' '.$item->nev3).
						   '</var><br />&nbsp;&nbsp;&nbsp;&nbsp;(Gépre vitel:'.$item->feldat.')';
						?>
					</td>
					<td><?php echo $item->telepules; ?></td>
					<td><?php echo $item->tnev; ?></td>
					<td><?php echo $item->szoveg; ?></td>
					<td><?php echo $item->email; ?></td>
					<td><?php echo $caller; ?></td>
					<td class="jobbra" onclick="kommentClick(<?php echo $item->kapcs_id; ?>)" 
					    <?php if ($item->olvasatlan_komment_db > 0) echo ' title="'.$komment_title.'"'; ?>
					    style="cursor:pointer; text-decoration:underline">
					   <?php echo (int)$item->komment_db; 
					         if ($item->olvasatlan_komment_db > 0) {
								 echo '&nbsp;<spn style="color:red">('.$item->olvasatlan_komment_db.')</span>';
							 }
					   ?>
					</td>
					<td id="megj<?php echo $item->kapcs_id; ?>">
					  <?php echo $item->megjegyzes.'<div class="telmegj2">'.$item->telmegj2.'</div>'; ?>
					</td>
				</tr>
<?php

  endforeach;
  else:
  ?>
	<tr>
		<td colspan="9">
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
<input type="hidden" name="task" value="kapcsolatok" />
<input type="hidden" name="view" value="kapcsolatoks" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="backtask" value="kapcsolatoks.megsem" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>  	
<p>Piros szinnel a "nem ellenörzött" adatok vannak megjelölve.</p>
<display style="display:none">
<iframe width="800" height="200" name="callerResult" src=""></iframe>
</display>