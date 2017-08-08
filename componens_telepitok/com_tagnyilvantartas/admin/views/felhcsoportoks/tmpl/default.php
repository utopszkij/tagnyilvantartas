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
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tagnyilvantartas&task=felhcsoportoks.saveOrderAjax&tmpl=component';
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
<h2><?php echo JText::_('COM_TAGNYILVANTARTAS_FELHCSOPORTOK'); ?></h2>
<form action="index.php?option=com_tagnyilvantartas&view=felhcsoportok" method="post" name="adminForm" id="adminForm">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">				
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="this.form.filter_search.value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
<div class="clearfix"> </div>
	
<div id="editcell">
	<table class="adminlist" id="articleList">
		<thead>
			<tr>
					
				<th class="px20">
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'Fcsop_nev', 'a.nev', $listDirn, $listOrder ); ?>
				</th>
				<th class="px20">
					<?php echo JHTML::_('grid.sort', 'Kod', 'a.kod', $listDirn, $listOrder ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', 'Fcsop_id', 'a.fcsop_id', $listDirn, $listOrder ); ?>
				</th>
			</tr> 			
		</thead>
		<tfoot>
		<tr>
			<td colspan="4">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
<?php
  if (count($this->items)) : 
  		foreach ($this->items as $i => $item) :
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
    				$onclick= "onclick=\"window.parent.jSelectFelhcsoportok_id('".$item->id."', '".$this->escape($item->nev)."', '','fcsop_id')\" ";
    			}  	
    
 				$link = JRoute::_( 'index.php?option=com_tagnyilvantartas&view=felhcsoportok&task=felhcsoportok.edit&id='. $item->fcsop_id );
 	
 				
 	
 				$checked = JHTML::_('grid.id', $i, $item->fcsop_id);
 	 	
  		?>
				<tr class="row<?php echo $i % 2; ?>"">
                    <td>
                    <?php  
                    echo '<input type="radio" id="cb'.$item->kapcs_id.'" name="cid[]"
                       onclick="cbClick(this)" value="'.$item->fcsop_id.'"/>
                    ';
                    ?></td>
			        <td class="nowrap has-context">
						<?php  echo $this->escape($item->nev); ?>
					</td>
			        <td class="nowrap has-context">
						<?php  echo $this->escape($item->kod); ?>
					</td>
					<td class="jobbra"><?php echo $item->fcsop_id; ?></td>
				</tr>
<?php

  endforeach;
  else:
  ?>
	<tr>
		<td colspan="12">
			<?php echo JText::_('COM_TAGNYILVANTARTAS_NO_DATA'); ?>
		</td>
	</tr>
	<?php
  endif;
  ?>
</tbody>
</table>
</div>
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="task" value="felhcsoportok" />
<input type="hidden" name="view" value="felhcsoportoks" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>  	