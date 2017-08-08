<?php
/**
* @version		$Id:default.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license 		
*
* extrafields böngészése
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

JToolBarHelper::title(   JText::_( 'EXTRAFIELDS' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::custom('extrafields.add','','','Új felvitel',False);
JToolBarHelper::custom('extrafields.edit','','','Módosítás',True);
JToolBarHelper::custom('extrafields.delete','','','Tőrlés',True);

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
<h2><?php echo JText::_('COM_TAGNYILVANTARTAS_EXTRAFIELDS'); ?></h2>
<form action="index.php?option=com_tagnyilvantartas&view=extrafields" method="post" name="adminForm" id="adminForm">
<div class="clearfix"> </div>
<div id="editcell">
	<table class="listatabla" id="articleList">
		<thead>
			<tr>
					
				<th class="px20">
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'ID', 'a.field_id', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'FIELD_NAME', 'a.field_name', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'FIELD_LABEL', 'a.field_label', $listDirn, $listOrder ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'FIELD_TYPE', 'a.field_type', $listDirn, $listOrder ); ?>
				</th>
			</tr> 			
		</thead>
		<tfoot>
		<tr id="lapozosor">
            <td colspan="2">
              Összesen: <span class="talalatok_szama"><?php  echo (int)$this->getModel('extrafields')->getTotal(); ?></span> adat
            </td>
			<td colspan="3">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
<?php
  if (count($this->items)) : 
  		foreach ($this->items as $i => $item) :
 				$id = $item->field_id;
 				$checked = JHTML::_('grid.id', $i, $item->field_id);
  		?>
				<tr class="row<?php echo $i % 2; ?>">
					      
        			<td><?php 
                        //echo $checked;  
                        echo '<input type="radio" id="cb'.$id.'" name="cid[]"
                           onclick="cbClick(this)" value="'.$id.'"/>
                        ';
                        ?>
					</td>
                    <td><?php echo $item->field_id; ?></td>
                    <td><?php echo $item->field_name; ?></td>
                    <td><?php echo $item->field_label; ?></td>
                    <td><?php echo $item->field_type; ?></td>
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
<input type="hidden" name="task" value="extrafields" />
<input type="hidden" name="view" value="extrafields" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="filter_kapcs_id" value="<?php echo $this->state->get('filter.kapcs_id',0); ?>" />

<?php echo JHTML::_( 'form.token' ); ?>
</form>  	
<h2>FIGYELEM! egy mező törlése, az összes abban tárolt információ és a vele kapcsolatos napló bejegyzés visszavonhatatlan törlését is jelenti!</h2>
