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

$listOrder	= $this->escape($this->state->orderCol);
$listDirn	= $this->escape($this->state->orderDir);
if ($listOrder == '') $listOrder = 'a.nev1';
if ($listOrderDirn == '') $listOrderDirn = 'asc';

?>
<script type="text/javascript">
	
	/* thClick rutin */
	Joomla.tableOrdering = function(fieldName, orderDirn, orderCol) {
		document.adminForm.limitstart.value = 0;
		document.forms.adminForm.orderCol.value = fieldName;
		document.forms.adminForm.orderDir.value = orderDirn;
		document.forms.adminForm.submit();
	}

	/* selekted sor kiemelés */
    selectedTr = false;
    selectedTrClass = '';
    /* radio group click */
	function cbClick(cb) {
       if (selectedTr) {
          selectedTr.className = selectedTrClass; 
       } 
       if (cb.checked) {
         selectedTr = cb.parentNode.parentNode;
         selectedTrClass = selectedTr.className;       
         selectedTr.className = selectedTr.className + ' selected';
       }  
       Joomla.isChecked(cb.checked); 
    }
	
	/* button click rutin */
	function btnClick(task) {
		if (task == 'alap.add') {
			document.forms.adminForm.task.value = task;
			document.forms.adminForm.submit();
		}
		if (task == 'alap.edit') {
			if (document.forms.adminForm.boxchecked.value >= 1) {
			  document.forms.adminForm.task.value = task;
			  document.forms.adminForm.submit();
			} else {
			  alert('<?php echo JText::_("SELECT_ONE_ITEM_PLEAS"); ?>');	
			}
		}
		if (task == 'alap.show') {
			if (document.forms.adminForm.boxchecked.value >= 1) {
			  document.forms.adminForm.task.value = task;
			  document.forms.adminForm.submit();
			} else {
			  alert('<?php echo JText::_("SELECT_ONE_ITEM_PLEAS"); ?>');	
			}
		}
		if (task == 'alap.delete') {
			if (document.forms.adminForm.boxchecked.value >= 1) {
			  if (confirm('<?php echo JText::_("SURE_DELETE"); ?>') == true) {	
			    document.forms.adminForm.task.value = task;
			    document.forms.adminForm.submit();
			  }	
			} else {
			  alert('<?php echo JText::_("SELECT_ONE_ITEM_PLEAS"); ?>');	
			}
		}
	}

</script>

<div class="divAlap">
<h2><?php echo $this->title; ?></h2>
<form action="index.php?option=com_tagnyilvantartas&view=alap&task=alap.browser" method="post" name="adminForm" id="adminForm">

<!-- filter box -->
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">				
		<input type="text" name="filterStr" id="filter_search" 
			  title="Név, email vagy település (részlet is lehet)" value="<?php echo $this->escape($this->state->filterStr); ?>" />
	</div>
	<div class="btn-group pull-left">
		<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"
			onclick="document.adminForm.limitstart.value = 0; document.forms.adminForm.submit();">
			<i class="icon-search"></i>
		</button>
		<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" 
			onclick="document.adminForm.limitstart.value = 0; this.form.filter_search.value='';this.form.submit();">
			<i class="icon-remove"></i>
		</button>
	</div>
     <div class="clear"></div>
</div>
<div class="clearfix"> </div>
	
<!-- datatable -->	
<div id="editcell">
	<table class="listatabla" id="articleList">
		<thead>
			<tr>
				<th class="px20">
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('KAPCSOLAT_NEV'), 'a.nev1', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', $this->form->getLabel('telepules'), 'a.telepules', $listDirn, $listOrder ); ?>
				</th>
				<th class="terszerv_th">
					<?php echo JHTML::_('grid.sort', $this->form->getLabel('terszerv_id'), 't.nev', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', $this->form->getLabel('kategoria_id'), 'k.szoveg', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', $this->form->getLabel('ellenorzott'), 'a.ellenorzott', $listDirn, $listOrder ); ?>
				</th>
				<th class="px20">
					<?php echo JHTML::_('grid.sort', 'Kommentek', 'a.komment_db', $listDirn, $listOrder ); ?>
				</th>
				<th class="px20">
					<?php echo JHTML::_('grid.sort', 'ID', 'a.kapcs_id', $listDirn, $listOrder ); ?>
				</th>
			</tr> 			
		</thead>
		<tfoot>
		<tr id="lapozosor">
            <td colspan="2">
              Összesen: <span class="talalatok_szama"><?php  echo $this->total; ?></span> adat
            </td>
			<td colspan="7">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		if (count($this->items)) : 
  		foreach ($this->items as $i => $item) :
					
				$disableClassName = '';
				$disabledLabel	  = '';
 				$onclick = "";
 	 	
				?>
				<tr class="row<?php echo $i % 2; ?>">
        			<td><INPUT TYPE="radio" id="cb<?php echo $item->kapcs_id; ?>" name="id" 
					   onclick="cbClick(this)" value="<?php echo $item->kapcs_id; ?>" />
					</td>
			        <td class="nowrap has-context">
						<?php  echo $this->escape($item->nev1.' '.$item->nev2.' '.$item->nev3); ?>
					</td>
					<td><?php echo $item->telepules; ?></td>
					<td><?php echo $item->tnev; ?></td>
					<td><?php echo $item->szoveg; ?></td>
					<td><?php if ($item->ellenorzott == 1) echo 'Igen'; else echo 'Nem'; ?></td>
					<td class="jobbra"><?php echo (int)$item->komment_db; ?></td>
					<td class="jobbra"><?php echo $item->kapcs_id; ?></td>
				</tr>
				<?php
		endforeach;
		else:
		?>
			<tr>
				<td colspan="9">
				<?php echo JText::_( 'NO_DATA' ); ?>
				</td>
		</tr>
		<?php
		endif;
?>
</tbody>
</table>
</div>
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="task" value="alap.browser" />
<input type="hidden" name="view" value="kapcsolatok" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="orderCol" value="<?php echo $this->state->orderCol; ?>" />
<input type="hidden" name="orderDir" value="<?php echo $this->state->orderDir; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>  	

<!-- manipuláló gombok --> 
<div class="buttons">
<?php foreach ($this->buttons as $button) : ?>
  <button type="button" class="<?php echo $button[1]; ?>" onclick="btnClick('<?php echo $button[0]; ?>')">
    <?php echo $button[2]; ?>
  </button>
<?php endforeach; ?> 
</div>

<div>

