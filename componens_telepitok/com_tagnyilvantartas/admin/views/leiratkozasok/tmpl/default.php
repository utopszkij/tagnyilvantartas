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
$listOrder	= JRequest::getVar('filter_order');
$listDirn	= JRequest::getVar('filter_order_Dir');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$params		= (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tagnyilvantartas&task=cimkeks.saveOrderAjax&tmpl=component';
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
<h2>Hírlevél leiratkozások</h2>
<form action="index.php?option=com_tagnyilvantartas&view=cimkek" method="post" name="adminForm" id="adminForm">

		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">				
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			</div>
            <div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="this.form.filter_search.value='';this.form.submit();"><i class="icon-remove"></i></button>
		        <span>Keresni névre, emailre vagy ezek részletére lehet.</span>
			</div>
        </div>
		<div class="clearfix"> </div>
	
<div id="editcell">
	<p>A pirossal jelzett nevek már nem szerepelnek a kapcsolatok között, a többinél a névre kattintva a kapcsolat szerkesztő űrlap jön be (új böngésző fülön).</p>
	<table class="adminlist" id="articleList">
		<thead>
			<tr>
				<th class="title" style="width:150px;"><?php echo JHTML::_('grid.sort', 'Dátum', 'a.date', $listDirn, $listOrder ); ?></th>
				<th class="title" style="width:300px;"><?php echo JHTML::_('grid.sort', 'Név', 's.name', $listDirn, $listOrder ); ?></th>
				<th class="title" style="width:200px;"><?php echo JHTML::_('grid.sort', 'E-mail', 's.email', $listDirn, $listOrder ); ?></th>
				<th class="title" style="width:300px"><?php echo JHTML::_('grid.sort', 'Hírlevél', 'm.subject', $listDirn, $listOrder ); ?></th>
				<th class="title"><?php echo JHTML::_('grid.sort', 'Indoklás', 'a.data', $listDirn, $listOrder ); ?></th>
			</tr> 			
		</thead>
		<tfoot>
		<tr>
			<td colspan="3">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
        </tfoot>
	<tbody>
<?php
  // kapcsolatok szürés link
  $klink = 'index.php?option=com_tagnyilvantartas&view=doszures'.
    '&task=doszures.start&mezo1=cimkek&relacio1=like&ertek1=';
  if (count($this->items)) : 
  		foreach ($this->items as $i => $item) : 
		   $link = JURI::root().'administrator/index.php?option=com_tagnyilvantartas&view=kapcsolatok&task=kapcsolatok.edit&cid[]=';
		   $item->data = str_replace('UNSUB_SURVEY_FREQUENT','Túl gyakran küldünk e-maileket',$item->data);
		   $item->data = str_replace('UNSUB_SURVEY_RELEVANT','Az e-mailek nem érdekesek számomra',$item->data);
		   $item->data = str_replace('REASON::',' ',$item->data);
		   
		?>
				<tr class="row<?php echo $i % 2; ?>"">
        			<td>
					     <?php echo $item->date; ?>
					</td>
        			<td>
					   <?php if ($item->kapcs_id > 0) :?>
					     <a target="new" href="<?php echo $link.$item->kapcs_id; ?>">
					     <?php echo $item->name; ?>
					     </a>
					   <?php else : ?>
					     <span style="color:red"><?php echo $item->name; ?></span>
					   <?php endif; ?>	
					</td>
        			<td><?php echo $item->email; ?></td>
        			<td><?php echo $item->subject; ?></td>
        			<td><?php echo $item->data; ?></td>
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
<input type="hidden" name="task" value="browser" />
<input type="hidden" name="view" value="leiratkozasok" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

</form>  	
