<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*
* változásnapló böngészése
* szürési lehetőségek: filter_date - filter_date2,  (filter_kapcs_id, ) filter_user_id
* id = kapcsolat_id,lastact_time,lastact_user_id
* extra funkció : purge
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
	$saveOrderingUrl = 'index.php?option=com_tagnyilvantartas&task=naplos.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');

JToolBarHelper::title(   JText::_( 'Naplók' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::custom('naplos.show','','','Megnéz',True);

if ($this->state->get('filter.kapcs_id',0)==0)
  JToolBarHelper::custom('naplos.filterkapcsid','','','Csak ennek a kapcsolatnak a változás története',True);

JToolBarHelper::custom('naplos.purge','','','Régi napló bejegyzések törlése',False);

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

</script>
<h2><?php echo JText::_('COM_TAGNYILVANTARTAS_NAPLOK'); ?></h2>
<form action="index.php?option=com_tagnyilvantartas&view=naplos" method="post" name="adminForm" id="adminForm">

		<div>
			<div style="width:700px; float:left;">				
			  Dátum (ééé-hh-nn):
			  <input type="text" name="filter_date1" value="<?php echo $this->state->get('filter.date1'); ?>" size="10" style="width:120px"/>
			  &nbsp;-&nbsp;
			  <input type="text" name="filter_date2" value="<?php echo $this->state->get('filter.date2'); ?>" size="10" style="width:120px"/>
			  Név:<input type="text" name="filter_nev" value="<?php echo $this->state->get('filter.nev'); ?>" />
			  <br>Kezelő:
			  <select name="filter_user_id">
			  <option value="">&nbsp;</option>
			  <?php
			    $filter_user_id = $this->state->get('filter.user_id');
			    $db->setQuery('select id, name
				from #__users
				order by name');
				$res = $db->loadObjectList();
				foreach ($res as $res1) {
					if ($res1->id == $filter_user_id)
						echo '<option value="'.$res1->id.'" selected="selected">'.$res1->name.'</option>';
					else	
						echo '<option value="'.$res1->id.'">'.$res1->name.'</option>';
				}
			  ?>
			  </select>
			  <?php $filter_lastaction = JRequest::getVar('filter_lastaction'); ?>
			  Müvelet:<select name="filter_lastaction">
			    <option value=""<?php if ($filter_lastaction=='') echo ' selected="selected"'; ?>>Összes</option>
			    <option value="INSERT"<?php if ($filter_lastaction=='INSERT') echo ' selected="selected"'; ?>>Felvitel</option>
			    <option value="UPDATE"<?php if ($filter_lastaction=='UPDATE') echo ' selected="selected"'; ?>>Módosítás</option>
			    <option value="DELETE"<?php if ($filter_lastaction=='DELETE') echo ' selected="selected"'; ?>>Törlés</option>
			    <option value="sikeres"<?php if ($filter_lastaction=='sikeres') echo ' selected="selected"'; ?>>Sikeres bejelentkezés</option>
			    <option value="sikertelen"<?php if ($filter_lastaction=='sikertelen') echo ' selected="selected"'; ?>>Hibás bejelentkezési kisérlet</option>
			  </select>
			  <?php 
			    if ($this->state->get('filter.kapcs_id',0) > 0) {
					$db->setQuery('select nev1,nev2,nev3 
					from #__tny_kapcsolatok 
					where kapcs_id='.$db->quote($this->state->get('filter.kapcs_id',0)));
					$res = $db->loadObject();
					if ($res) 
						echo '<br />'.$res->nev1.' '.$res->nev2.' '.$res->nev3;
				}
			  ?>
			</div>

			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" 
				  onclick="var f=this.form; f.filter_date1.value=''; f.filter_date2.value=''; f.filter_user_id.selectedIndex=0; f.filter_kapcs_id.value=0; f.filter_lastaction.selectedIndex=0; f.filter_nev.value=''; f.submit();">
				  <i class="icon-remove"></i></button>
			</div>
            <div class="clear"></div>
		</div>
		<div class="clearfix"> </div>

	
<div id="editcell">
	<table class="listatabla" id="articleList">
		<thead>
			<tr>
					
				<th class="px20">
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'Időpont', 'a.lastact_time', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'Akció', 'a.lastaction', $listDirn, $listOrder ); ?>
				</th>
				<th class="terszerv_th">
					<?php echo JHTML::_('grid.sort', 'Kezelő', 'u.name', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'Kapcsolat adat', 'a.nev1', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'Területi szervezet', 't.nev', $listDirn, $listOrder ); ?>
				</th>
			</tr> 			
		</thead>
		<tfoot>
		<tr id="lapozosor">
            <td colspan="2">
              Összesen: <span class="talalatok_szama"><?php  echo (int)$this->getModel('naplos')->getTotal(); ?></span> adat
            </td>
			<td colspan="4">
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
    
 				$naplo_id = $item->kapcs_id.','.$item->lastact_time.','.$item->lastact_user_id;
 	
 				$checked = JHTML::_('grid.id', $i, $item->kapcs_id);
 	 	
  		?>
				<tr class="row<?php echo $i % 2; if ($item->ellenorzott==0) echo ' nemellenorzott'; ?>">
					      
        			<td><?php 
                        //echo $checked;  
                        echo '<input type="radio" id="cb'.$id.'" name="cid[]"
                           onclick="cbClick(this)" value="'.$naplo_id.'"/>
                        ';
                        ?>
					</td>
                    <!-- td><?php echo date('Y.m.d h:i:s',$item->lastact_time); ?>'</td -->
                    <td><?php echo $item->lastact_time; ?></td>
        			<td><?php echo JText::_($item->lastaction).'<br />'.$item->lastact_info; ?></td>	
					<td><?php echo $item->name; ?></td>
			        <td class="nowrap has-context">
						<?php  echo $this->escape($item->nev1.' '.$item->nev2.' '.$item->nev3). 
						  '<br />'.$item->irsz.' '.$item->telepules.' '.$item->utca.' '.$item->hazszam.' '.$item->cimkieg;
						?>
					</td>
					<td><?php echo $item->tnev; ?></td>
				</tr>
<?php

  endforeach;
  else:
  ?>
	<tr>
		<td colspan="7">
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
<input type="hidden" name="task" value="naplos" />
<input type="hidden" name="view" value="naplos" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="filter_kapcs_id" value="<?php echo $this->state->get('filter.kapcs_id',0); ?>" />


<?php echo JHTML::_( 'form.token' ); ?>
</form>  	

