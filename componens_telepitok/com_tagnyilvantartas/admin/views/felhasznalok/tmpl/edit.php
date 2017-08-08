<?php
/**
* @version		$Id:edit.php 1 2015-05-30 06:28:16Z  $
* @copyright	Copyright (C) 2015, . All rights reserved.
* @license
*/

// 2017.01.24 "SM" felhasználó is használhatja, de csak lekérdezhet, nem módosíthat.

// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
include_once JPATH_ADMINISTRATOR.'/components/com_tagnyilvantartas/models/teruletiszervezeteks.php';

// Set toolbar items for the page
$edit		= JFactory::getApplication()->input->get('edit', true);
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Cimkek' ).': <small><small>[ ' . $text.' ]</small></small>' );
$session = JFactory::getSession();
$userCsoport = $session->get('userCsoport');		
if ($userCsoport->kod != 'SM') {
  JToolBarHelper::apply('felhasznalok.apply', 'MENTES');
  JToolBarHelper::save('felhasznalok.save', 'RENDBEN');
}  
if (!$edit) {
	JToolBarHelper::cancel('felhasznalok.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'felhasznalok.cancel', 'MEGSEM' );
}
?>

<script language="javascript" type="text/javascript">


Joomla.submitbutton = function(task)
{
	if (task == 'cimkek.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
<?php if ($userCsoport->kod == 'SM') : ?>
  jQuery(function() {
    jQuery('input').attr('readonly','readonly');
  });
<?php endif; ?>

</script>

<?php

  echo '<h2>'.JText::_('COM_TAGNYILVANTARTAS_FELHASZNALOK_EDIT').'</h2>';
 
  $session = JFactory::getSession();
  $errorFields = $session->get('errorFields');
  if (is_array($errorFields))
  foreach ($errorFields as $errorField) {
     $this->form->setFieldAttribute($errorField,'class','error'); 
  }
  $session->set('errorFields',array());

  // $this->item felhasználásával adat képernyő
  $db = JFactory::getDBO();
  
  //DBG echo '<pre>'; print_r($this->item); echo '</pre>';        
 
?>



<form method="post" action="<?php echo JRoute::_('index.php?option=com_tagnyilvantartas&layout=edit&id='.(int) $this->user_id);  ?>" 
      id="adminForm" name="adminForm">
  <div id="felhasznalok">   
    <div class="fej">Felhasználó neve: <strong><?php echo $this->item->name.'</strong> ('.$this->item->username.')'; ?></div>
    <div class="info">Ha egy "felsőbb szintű" területi szervezethez adsz jogot, az egyúttal a hozzá tartozó alsóbb szintűek kezeléséhez is megadja a hozzáférést.</div>
      <div class="bal" style="float:left;">
        <table border="0" width="100%" cllpadding="0" cellspacing="0">
        <?php
          // felhasználói csoportok
          $db->setQuery('select * from #__tny_felhcsoportok order by kod');
          $fcsoportok = $db->loadObjectList();
          $fcsi = 1;
          foreach ($fcsoportok as $fcsoport) {
            if (isset($this->item->terhats[$fcsoport->fcsop_id]))
              echo '<tr class="item"><td>
              <input name="fcsop_'.$fcsi.'" id="fcsop_'.$fcsi.'" type="checkbox" value="'.$fcsoport->fcsop_id.'" checked="checked" onclick="jobble()" />
              <strong>'.$fcsoport->kod.'</strong> '.$fcsoport->nev.'</td><td width="30">
              <button type="button" onclick="fcsopClick('.$fcsoport->fcsop_id.','.$fcsi.')" title="területi hatáskör">&nbsp;</button>
              </td>
              </tr>
              ';                
            else
              echo '<tr class="item"><td>
              <input name="fcsop_'.$fcsi.'" id="fcsop_'.$fcsi.'" type="checkbox" value="'.$fcsoport->fcsop_id.'" onclick="jobble()" />
              <strong>'.$fcsoport->kod.'</strong> '.$fcsoport->nev.'</td><td width="30">
              <button type="button" onclick="fcsopClick('.$fcsoport->fcsop_id.','.$fcsi.')" title="területi hatáskör">&nbsp;</button>
              </td>
              </tr>
              ';                
            $fcsi++;                
          }
        ?>
        </table>
      </div>
      <div class="kozep" style="float:left;">
        <?php
        // összekötő vonal
        $fcsi = 1;
        foreach ($fcsoportok as $res1) {
		  if ($res1->fcsop_id == JRequest::getVar('fcsop'))
		    echo '<div class="item"><div id="kozep_'.$fcsi.'" style="display:block">&nbsp;</div></div>';	
		  else	
		    echo '<div class="item"><div id="kozep_'.$fcsi.'" style="display:none">&nbsp;</div></div>';	
		  $fcsi++;	
		}
        ?>
      </div>
 	 <?php
	    $terszervModel = new TagnyilvantartasModelteruletiszervezeteks();
	    JRequest::setVar('filter_search','tree');
	    $res = $terszervModel->getItems();
		foreach ($fcsoportok as $fcsoport) { 
		  // területi szervezetek
		  $fcsop = $fcsoport->fcsop_id;
	      echo '<div class="jobb" style="float:left; display:none;" id="jobb_'.$fcsop.'">
          ';
		  $fhi = 1;
		  foreach ($res as $res1) {
             $c = 1000 + (20* $res1->level); 
			 if (!is_array($this->item->terhats[$fcsop])) {
				 $this->item->terhats[$fcsop] = array();
			 }	 
			 if (in_array($res1->terszerv_id, $this->item->terhats[$fcsop]))
			   echo '<div class="ter_item" lang="'.$c.'">
			   <div style="display:inline-block; width:'.($res1->level * 20).'px">&nbsp;</div>
			   <input id="terszerv_'.$fcsop.'_'.$fhi.'" name="terszerv_'.$fcsop.'_'.$fhi.'" type="checkbox" checked="checked" value="'.$res1->terszerv_id.'" onclick="terszervClick(event)" />
			   '.$res1->nev.'
			   </div>
			   ';
			 else 
			   echo '<div class="ter_item" lang="'.$c.'">
			   <div style="display:inline-block; width:'.($res1->level * 20).'px">&nbsp;</div>
			   <input id="terszerv_'.$fcsop.'_'.$fhi.'" name="terszerv_'.$fcsop.'_'.$fhi.'" type="checkbox" value="'.$res1->terszerv_id.'"  onclick="terszervClick(event)" />
			   '.$res1->nev.'
			   </div>
			   ';
			 $fhi++;
		  }
          if ($this->item->commentemails[$fcsop] == 1)
             $checked = ' checked="checked"';
          else
             $checked = '';              
          echo '<div><input type="checkbox" name="commentemail_'.$fcsop.'" value="1"'.$checked.'>&nbsp;Kommentekről e-mail értesítést kap</div>
          '; 
	      echo '</div>
          ';
		}  
	 ?>
        <div class="clear"></div>
		<input type="hidden" name="option" value="com_tagnyilvantartas" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->user_id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="felhasznalok" />
		<input type="hidden" name="user_id" value="<?php echo $this->item->user_id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
   </div>     
</form>

<script type="text/javascript">
    // stativ vars
    selected_i = 0;
    selected_fcsop = '';
	function fcsopClick(fcsop_id,fcsi) {
		var f = document.forms.adminForm;
        var i = 0;
        var cb = document.getElementById("fcsop_"+fcsi);
		if (cb) {
			if (cb.checked) {
               // összekötöl vonal megjelenitése
               if (selected_i != 0) {
                 var c = document.getElementById("kozep_"+selected_i);
                 if (c) c.style.display="none";
               }
               var c = document.getElementById("kozep_"+fcsi);
               if (c) c.style.display="block";
               selected_i = fcsi; 
               // jobb oldal megjelenitése
               if (selected_fcsop != "") {
                 var c = document.getElementById("jobb_"+selected_fcsop);
                 if (c) c.style.display="none";
               }
               var c = document.getElementById("jobb_"+fcsop_id);
               if (c) c.style.display="block";
               if (c) c.style.marginTop=(20 * fcsi) + "px";
               selected_fcsop = fcsop_id;
               
			} else {
			  alert("Csak bejelölt csoportnál használható.");
			}
		} else {
		   alert("Fatal error fcsopClick");	
		}	  
		return true;
	}
    function jobble() {
        if (selected_i != 0) {
           var c = document.getElementById("kozep_"+selected_i);
           if (c) c.style.display="none";
        }
        if (selected_fcsop != "") {
           var c = document.getElementById("jobb_"+selected_fcsop);
           if (c) c.style.display="none";
        }
        selected_fcsop = '';
        selected_i = 0;
    }
    /**
      * a terszerv alatti összest azonosan jelöl 
    */
    function terszervClick(event) {
       var chk = false;
       var item = false;
       var item1 = false;
       var chk1 = false;
       if (event.source)
          chk = event.source;
       else
          chk = event.target;
       if (chk) {
          item = chk.parentNode; 
          item1 = item.nextSibling;
          while (item1) {
             if (item1.nodeName == 'DIV') {
                if (item1.lang > item.lang) {
                    chk1 = item1.firstChild;
                    while (chk1) {
                        if (chk1.nodeName == 'INPUT') chk1.checked = chk.checked;
                        chk1 = chk1.nextSibling;
                    }
                } 
             } 
             item1 = item1.nextSibling; 
             if (item1) {
                 if (item1.lang == item.lang) item1 = false;
             }
          }
       }
    }
</script>