<?php
/**
 * @version 1.00
 * @package    joomla
 * @subpackage tagnyilvantartas
 * @author	   Fogler Tibor  tibor.fogler@gmail.com	
 * @copyright  Copyright (C) 2015, . All rights reserved.
 * @license    GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');


$listOrder	= $this->escape($this->state->orderCol);
$listDirn	= $this->escape($this->state->orderDir);
if ($listOrder == '') $listOrder = 'a.idopont';
if ($listOrderDirn == '') $listOrderDirn = 'desc';

//+ 2017.02.16 komment olvasások nyilvántartása
$db = JFactory::getDBO();
$user = JFactory::getUser();
$db->setQuery('create table if not exists #__tny_kommentolvasasok (
kapcs_id int(11),
komment_id int(11),
olvaso_id int(11),
primary key kommentolvasas1 (kapcs_id, komment_id, olvaso_id)
)');
$db->query();
//+ 2017.02.16 komment olvasások nyilvántartása

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
		if (task == 'kommentek.add') {
			document.forms.adminForm.task.value = task;
			document.forms.adminForm.submit();
		} else if (task == 'kommentek.save') {	
			if (document.forms.adminForm.id) {
			  if (document.forms.adminForm.id.length > 1) {	
			    document.forms.adminForm.id[0].value = '';
			    document.forms.adminForm.id[0].checked = true;
			  } else {
				document.forms.adminForm.id.value = 0;  
			  }
			}  
			document.forms.adminForm.task.value = task;
			document.forms.adminForm.submit();
		} else if (task == 'kommentek.edit') {
			if (document.forms.adminForm.boxchecked.value >= 1) {
			  document.forms.adminForm.task.value = task;
			  document.forms.adminForm.submit();
			} else {
			  alert('<?php echo JText::_("KOMMENTEK_SELECT_ONE_ITEM_PLEAS"); ?>');	
			}
		} else if (task == 'kommentek.show') {
			if (document.forms.adminForm.boxchecked.value >= 1) {
			  document.forms.adminForm.task.value = task;
			  document.forms.adminForm.submit();
			} else {
			  alert('<?php echo JText::_("KOMMENTEK_SELECT_ONE_ITEM_PLEAS"); ?>');	
			}
		} else if (task == 'kommentek.delete') {
			if (document.forms.adminForm.boxchecked.value >= 1) {
			  if (confirm('<?php echo JText::_("KOMMENTEK_SURE_DELETE"); ?>') == true) {	
			    document.forms.adminForm.task.value = task;
			    document.forms.adminForm.submit();
			  }	
			} else {
			  alert('<?php echo JText::_("KOMMENTEK_SELECT_ONE_ITEM_PLEAS"); ?>');	
			}
		} else if (task == 'kapcsolatok.show') {
			var id = <?php echo $this->state->filterKapcs_id; ?>;
			document.forms.adminForm.action = 'index.php?cid[]='+id;
			document.forms.adminForm.view='kapcsolatok';
			document.forms.adminForm.task.value = task;
		    document.forms.adminForm.submit();
		} else if (task == 'doszures.show') {
			var id = <?php echo $this->state->filterKapcs_id; ?>;
			document.forms.adminForm.action = 'index.php?cid[]='+id;
			document.forms.adminForm.view='doszures';
			document.forms.adminForm.task.value = task;
		    document.forms.adminForm.submit();
		} else if (task == 'kapcsolatoks.megsem') {
			document.forms.adminForm.view='kapcsolatoks';
		    document.forms.adminForm.task.value = task;
		    document.forms.adminForm.submit();
		} else if (task == 'doszures.cancel') {
			document.forms.adminForm.view='doszures';
		    document.forms.adminForm.task.value = task;
		    document.forms.adminForm.submit();
		} else {
		    document.forms.adminForm.task.value = task;
		    document.forms.adminForm.submit();
		}
	}

</script>

<h2><?php echo $this->title; ?></h2>

<div class="list_kommentek">
<center><big>Ezen a képernyőn a böngésző frissítés funkicóját semmiképen ne használd!</big></center>
<div class="kommentkapcsolatinfo">
  <?php echo '<strong>'.$this->kapcsolatInfo->nev1.' '.
             $this->kapcsolatInfo->nev2.' '.
			 $this->kapcsolatInfo->nev3.'</strong><br />'.
			 $this->kapcsolatInfo->telepules.' '.
			 $this->kapcsolatInfo->utca.' '.
			 $this->kapcsolatInfo->kjelleg.' '.
			 $this->kapcsolatInfo->hazszam; ?>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<!-- filter box -->
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">				
		<input type="text" name="filterStr" id="filter_search" 
			  title="Kommentelő Név, időpont vagy szöveg (részlet is lehet)" 
			  value="<?php echo $this->escape($this->state->filterStr); ?>" />
		<input type="hidden" name="filterKapcs_id" value="<?php echo $this->state->filterKapcs_id ?>" />	  
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
				<th width="60">
				  <?php echo JText::_('KOMMENTEK_ORDERING');?>:
				</th>
				<th width="120">
					<?php echo JHTML::_('grid.sort', $this->form->getLabel('user_id'), 'u.name', $listDirn, $listOrder ); ?>
				</th>
				<th width="120">
					<?php echo JHTML::_('grid.sort', $this->form->getLabel('idopont'), 'a.idopont', $listDirn, $listOrder ); ?>
				</th>
				<th> </th>
			</tr> 			
		</thead>
		<tfoot>
		<tr id="lapozosor">
            <td colspan="2">
              <?php echo JText::_('KOMMENTEK_SUMMA') ?>: 
			  <span class="talalatok_szama"> <?php  echo $this->total; ?></span> 
			  <?php echo JText::_('KOMMENT') ?>
            </td>
			<td colspan="2">
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
				<tr class="row<?php echo $i % 2; ?>" class="kommentElem">
        			<td><INPUT TYPE="radio" id="cb<?php echo $item->_id; ?>" name="id" 
					   onclick="cbClick(this)" value="<?php echo $item->id; ?>" />
					</td>
					<td colspan="3"> 
					  <strong><?php echo $item->name; ?></strong>&nbsp;
					  <i><?php  echo $this->escape($item->idopont); ?></i><br/>
					  <?php echo $item->kommentszoveg;
							//+ olvasás tényének ltárolása
							$db->setQuery('insert ignore into #__tny_kommentolvasasok
							value ('.$item->kapcs_id.','.$item->id.','.$user->id.')
							');
							$db->query();
							//- olvasás tényének ltárolása

					  ?>
					</td>
				</tr>
				<?php
		endforeach;
		else:
		?>
			<tr>
				<td colspan="4">
				<?php echo JText::_( 'KOMMENTEK_NO_DATA' ); ?>
				</td>
		</tr>
		<?php
		endif;
?>
</tbody>
</table>
</div>
<input type="hidden" name="option" value="com_tagnyilvantartas" />
<input type="hidden" name="task" value="kommentek.browser" />
<input type="hidden" name="view" value="kommentek" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="orderCol" value="<?php echo $this->state->orderCol; ?>" />
<input type="hidden" name="orderDir" value="<?php echo $this->state->orderDir; ?>" />
<input type="hidden" name="backtask" value="<?php echo JRequest::getVar('backtask'); ?>" />

<!-- manipuláló gombok --> 
<div class="buttons">
<?php foreach ($this->buttons as $button) : ?>
  <button type="button" class="<?php echo $button[1]; ?>" onclick="btnClick('<?php echo $button[0]; ?>')">
    <?php echo $button[2]; ?>
  </button>
<?php endforeach; ?> 
</div>

<div class="newComment">
<h3><?php echo JText::_('KOMMENTEK_NEW')?></h3>
<div style="width:600px;"><?php echo $this->form->getInput('kommentszoveg'); ?></div>
<input type="hidden" name="jform[id]" value="" />
<input type="hidden" name="jform[kapcs_id]" value="<?php echo $this->state->filterKapcs_id; ?>" />
<input type="hidden" name="jform[user_id]" value="<?php echo $user->id; ?>" />
<input type="hidden" name="jform[idopont]" value="<?php echo date('Y-m-d H:i:s'); ?>" />
<button type="button" onclick="btnClick('kommentek.save')" style="padding:5px; font-size:16px;"><?php echo JText::_('KOMMENTEK_SEND'); ?>
</button>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
</form>  	

<div>

