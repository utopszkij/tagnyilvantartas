<?php
/**
* univerzal browser abstract object
* Joomla framwork style
* 
* author: Fogler Tibor   tibor.fogler@gmail.com
* Licence: GNU/GPL
*
* requesd name/namemodel.php
*         name/nameview.php
*         name/namehelper.php  optional
*         name/tmpl/filterform.php  optional
*         name/tmpl/orderform.php  optional
*         name/tmpl/grid.php
*         name/tmpl/paginator.php  optional
*         name/tmpl/buttons.php  optional  
*         name/tmpl/insertform.php  optional
*         name/tmpl/edittform.php  optional
*         name/tmpl/showform.php  optional
*         name/tmpl/deleteform.php  optional
*         name/tmpl/emptyform.php  optional
*         name/tmpl/exportform.php  optional
*         name/tmpl/importform.php  optional
*         default/tmpl/..... default templates
*/
class browserController {
   public $name = '';
   public $title = '';
   public $footer = '';
   public $filterForm = true;
   public $orderForn = true;
   public $colClickOrder = true;
   public $insertEnabled = true;
   public $editEnabled = true;
   public $deleteEnabled = true;
   public $showEnabled = true;
   public $exportEnabled = true;
   public $importEnabled = true;
   public $emptyEnabled = true;
   protected $key = 0; // minden task új key értéket generál és tárol sessionba.
                       // a browse ha limitstart nem érkezik akkor müködik key nélkül is
                       // a többi taszk csak helyes key érték érkezése esetén müködik
   protected $errorMsg = '';                    
   protected $model = null; //  Jmodel
   protected $view = null;  //  Jview
   protected $helper = null; // JHelper

   protected $requests = array();

   /**
   * object constructor, create model, view, helper too
   * @param string object and model and view name
   * @param array config to public propertys key='title','footer'......'emptyEnabled'
   */
   public function __construct($name, $config=array()) {
       if (is_array($config)) {
         foreach ($config as $fn => $fv)
           $this->$fn = $fv;
       }  
       $this->name = $name;
       foreach ($_GET as $fn => me $fv)
         $this->requests[$fn] = urldecode($fv);
       foreach ($_POST as $fn => $fv)
         $this->requests[$fn] = urldecode($fv);
       if (file_exists(JPATH_BASE.'/'.$name.'/'.$name.'model.php')) {  
         include_once JPATH_BASE.'/'.$name.'/'.$name.'model.php';  
         $this->model = new $name.'Model' ();
       }
       if (file_exists(JPATH_BASE.'/'.$name.'/'.$name.'view.php')) { 
         include_once JPATH_BASE.'/'.$name.'/'.$name.'view.php';  
         $this->view = new $name.'View' ();
         $this->view->controller = $this;
         $this->view->model = $this->model;
       }
       if (file_exists(JPATH_BASE.'/'.$name.'/'.$name.'helper.php')) { 
         include_once JPATH_BASE.'/'.$name.'/'.$name.'helper.php';  
         $this->helper = new $name.'Helper' ();
       }
   }
   /**
    * get my url
    * @param boolean include jrequest - exlude task param
    * @return string
    */
   protected function getMyURL($requests) {
     $session = JFactory::getSession();
     $newKey = $session->get('key');     
     $result = JURI::base().'/index.php?option=com_'.$name.'&key='.$newkey;
     if ($requests) {
		 foreach ($this->requests as $fn => $fv) {
           if (()$fn != 'task') & ($fn != 'key')) 
		      $result .= '&'.$fn.'='.urlencode($fv);
         }  
	 }
	 return $result;
   }
   /**
    * get jrequest in hidden field
    * @return string html code
    */
   public function getHiddenFields() {
      $result = '';
      foreach ($this->requests as $fn => $fv) {
		  $result .= '<input type="hidden" name=".$fn." value="'.$fv.'" />'."\n";
	  }
	  return $result;
   }     
   /** check $this->key 
   * @return boolean
   */
   protected function  checkKey() {
     $newKey = time() + date('s');
     $session = JFactory::getSession();
     $sKey = $session->get('key');
     $task = JRequest::getVar('task','');
     if ((($task=='browse') & (JRequest::getVar('limitstart','')=='')) |
         ($this->key == $sKey)) {
          $session->save('key',$newKey);
          $this->key = $newKey;          
          return true;   
     } else {
       $this->errorMsg = 'WRONG_KEY';  
       return false;
     }       
   }

   /**
   * Check user access right
   * @param JUser user adatok
   * @param string 'insert'|'edit'|'delete'|'show'|'export'|'import'|'empty'
   * @return boolean
   */
   protected function access($user, $task,$id) {
     // if access denied then set $this->errorMsg and return false
     return true;
   }

   /**
   * @return string
   */
   public function getErrorMsg() {
       return $this->errorMsg;
   }

   /**
   * task browser form display
   * @JRequest [limit,limitstart,order,filterfields,selected]
   * @return void
   */
   public function browse() {
	   if ($this->checkKey() & $this->access(JFactory::getUser(),
                                             JRequest::getVar('task'), 
                                             0)) {
         // send propertys to view
         $this->view->name = $this->name;
         $this->view->title = $this->title;
         $this->view->footer = $this->footer;
         $this->view->task = JRequest::getVar('task','browse');
         $this->view->browseURL = $this->getMyURL(true).'&task=browse';
         $this->view->colClickOrder = $this->colClickOrder;
         if ($this->insertEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=add';
         if ($this->editEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=edit';
         if ($this->deleteEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=deleteForm';
         if ($this->showEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=show';
         if ($this->exportEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=export';
         if ($this->importEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=import';
         if ($this->emptyEnabled)
           $this->view->insertURL = $this->getMyURL(true).'&task=empty';
         $this->view->items = $this->model->getItems(); // load items from database
         $this->view->total = $this->model->getTotal();
         // echo form
         if ($this->errorMsg != '') {
     	     echo '<div class="errorMsg">'.$this->errorMsg.'</div>'."\n";
         }
         echo '<div class="'.$this->name.'Browser">'."\n";
         echo '<h3>'.$this->title.'</h3>'."\n";
         if ($this->filterForm)
           $this->view->display('filterform'); 
         if ($this->orderForm)
           $this->view->display('orderform');
         $this->view->display('grid');
         $this->view->display('paginator');
         $this->view->display('buttons');
         echo '<p class="footer">'.$this->footer.'</hp>'."\n";
         echo '</div>'."\n";
       } else {
	     echo '<div class="errorMsg">'.$this->getErrorMsg().'</div>'."\n";
       }
   }

   /**
   * task edit form display
   * @JRequest [limit,limitstart,order,filterfields,selected,key]
   * @param Object $data  képernyõinit adatok
   * @param Array $errorFields  hibás mezõk felsorolása
   * @return void
   * model->check error esetén érkezett képernyõ adatok is lehetségesek
   */
   public function edit($data = null; $errorFields = null) {
     // $this->view->setFocus('fieldName');
     if ($this->checkKey() & $thJFactory::getUser(),
                                             JRequest::getVar('task'), 
                                             0)) {
         // send propertys to view
         $this->view->name = $this->name;
         $this->view->title = $this->title.' '.JText::_('EDIT');
         $this->view->footer = $this->footer;
         $this->view->task = JRequest::getVar('task','browse');
         $this->view->browseURL = $this->getMyURL(true).'&task=browse';
         $this->view->saveURL = $this->getMyURL(true).'&task=save';
         $this->view->item = $this->model->load(JRequest::getVar('selected',0)); // load item from database
         if ($data != null) 
             $this->view = $this->model->bind($data);
         // echo form
         if ($this->errorMsg != '')
             echo '<div class="errormsg">'.$this->errortMsg.'</div>'."\n";
         echo '<div class="'.$this->name.'Edit">'."\n";
         echo '<h3>'.$this->title.'</h3>'."\n";
         $this->view->setReadonly($this->idName);
         foreach ($errorFields as $fn)
           $this->view->setErrorMarker($fn);
         $this->view->display('form');
         echo '<p class="footer">'.$this->footer.'</hp>'."\n";
         echo '</div>'."\n";
     } else {
	     echo '<div class="errorMsg">'.$this->getErrorMsg().'</div>'."\n";
     }
   }

   /**
   * task add new form display
   * @JRequest [limit,limitstart,order,filterfields],selected,key
   * @param Object $data  képernyõinit adatok
   * @param Array $errorFields  hibás mezõk felsorolása
   * @return void
   * model->check error esetén érkezett képernyõ adatok is lehetségesek
   */
   public function add($data = null; $errorFields = null) {
     // $this->view->setFocus('fieldName');
     if ($this->checkKey() & $thJFactory::getUser(),
                                             JRequest::getVar('task'), 
                                             0)) {
         // send propertys to view
         $this->view->name = $this->name;
         $this->view->title = $this->title.' '.JText::_('ADD');;
         $this->view->footer = $this->footer;
         $this->view->task = JRequest::getVar('task','browse');
         $this->view->browseURL = $this->getMyURL(true).'&task=browse';
         $this->view->saveURL = $this->getMyURL(true).'&task=save';
         $this->view->item = $this->model->load(0); // init item 
         if ($data != null) 
             $this->view = $this->model->bind($data);
         foreach ($errorFields as $fn)
           $this->view->setErrorMarker($fn);
         // echo form
         if ($this->errorMsg != '')
             echo '<div class="errormsg">'.$this->errortMsg.'</div>'."\n";
         echo '<div class="'.$this->name.'Add">'."\n";
         echo '<h3>'.$this->title.'</h3>'."\n";
         $this->view->setReadonly($this->idName);
         $this->view->display('form');
         echo '<p class="footer">'.$this->footer.'</hp>'."\n";
         echo '</div>'."\n";
     } else {
	     echo '<div class="errorMsg">'.$this->getErrorMsg().'</div>'."\n";
     }
   }

   /**
   * task show form display
   * @JRequest [limit,limitstart,order,filterfields,selected],key
   * @return void
   */
   public function show() {
     if ($this->checkKey() & $thJFactory::getUser(),
                                             JRequest::getVar('task'), 
                                             0)) {
         // send propertys to view
         $this->view->name = $this->name;
         $this->view->title = $this->title.' '.JText::_('SHOW');;
         $this->view->footer = $this->footer;
         $this->view->task = JRequest::getVar('task','browse');
         $this->view->browseURL = $this->getMyURL(true).'&task=browse';
         $this->view->item = $this->model->load(JRequest::getVar('selected',0)); // load item from database
         // echo form
         echo '<div class="'.$this->name.'Show">'."\n";
         echo '<h3>'.$this->title.'</h3>'."\n";
         foreach ($this->fields as $field) { 
          $w = explode(' ',$fieldf);          
          $this->view->setReadonly($w[0]);
         }  
         $this->view->display('form');
         echo '<p class="footer">'.$this->footer.'</hp>'."\n";
         echo '</div>'."\n";
     } else {
	     echo '<div class="errorMsg">'.$this->getErrorMsg().'</div>'."\n";
     }
   }

   /**
   * task delete form display
   * @JRequest [limit,limitstart,order,filterfields],selected,key
   * @return void
   */
   public function deleteForm() {
     if ($this->checkKey() & $thJFactory::getUser(),
                                             JRequest::getVar('task'), 
                                             0)) {
         // send propertys to view
         $this->view->name = $this->name;
         $this->view->title = $this->title.' '.JText::_('DELETE');;
         $this->view->footer = $this->footer;
         $this->view->task = JRequest::getVar('task','browse');
         $this->view->browseURL = $this->getMyURL(true).'&task=browse';
         $this->view->deleteURL = $this->getMyURL(true).'&task=delete';
         $this->view->item = $this->model->load(JRequest::getVar('selected',0)); // load item from database
         // echo form
         echo '<div class="'.$this->name.'Delete">'."\n";
         echo '<h3>'.$this->title.'</h3>'."\n";
         foreach ($this->fields as $field) { 
          $w = explode(' ',$fieldf);          
          $this->view->setReadonly($w[0]);
         }  
         $this->view->display('deleteForm');
         echo '<p class="footer">'.$this->footer.'</hp>'."\n";
         echo '</div>'."\n";
     } else {
	     echo '<div class="errorMsg">'.$this->getErrorMsg().'</div>'."\n";
     }
   }

   /**
   * task do save to database
   * @JRequest [limit,limitstart,order,filterfields,selected],key
   * @return void
   */
   public function save() {
       // adat tárolás, új limitstart és selected beállítás
     if ($this->checkKey() & $thJFactory::getUser(),
                                             JRequest::getVar('task'), 
                                             0)) {
        $data = $this->model->load(JRequest::getVar('selected',0)); // load item from database
        $data = $this->model->bind($_POST);
        if ($this->model->check($data)) {
          if ($this->model->save($data)) {
            // OK --> browse
          } else {
            // save error --> browse
          }
        } else {
            // check error --> edit or add     key és task beállításra ügyelni!
            if ($data->id == 0)
               $this->add($data, $this->model->getErrorFields());
            else
               $this->add($data, $this->model->getErrorFields());
        }
                                             // send propertys to view
         $this->view->name = $this->name;
         $this->view->title = $this->title.' '.JText::_('SHOW');;
         $this->view->footer = $this->footer;
         $this->view->task = JRequest::getVar('task','browse');
         $this->view->browseURL = $this->getMyURL(true).'&task=browse';
         $this->view->item = $this->model->load(JRequest::getVar('selected',0)); // load item from database
         // echo form
         echo '<div class="'.$this->name.'Show">'."\n";
         echo '<h3>'.$this->title.'</h3>'."\n";
         foreach ($this->fields as $field) { 
          $w = explode(' ',$fieldf);          
          $this->view->setReadonly($w[0]);
         }  
         $this->view->display('form');
         echo '<p class="footer">'.$this->footer.'</hp>'."\n";
         echo '</div>'."\n";
     } else {
	     echo '<div class="errorMsg">'.$this->getErrorMsg().'</div>'."\n";
     }
       
   }

   /**
   * task do delete from database
   * @JRequest [limit,limitstart,order,filterfields],selected,key
   * @return void
   */
   public function delete() {
       
   }

   /**
   * task export fporm display
   * @JRequest [limit,limitstart,order,filterfields,selected],key
   * @return void
   */
   public function exportForm() {
       
   }

   /**
   * task import form display
   * @JRequest [limit,limitstart,order,filterfields,selected],key
   * @return void
   */
   public function importForm() {
       
   }

   /**
   * task
   * @JRequest [limit,limitstart,order,filterfields,selected],key
   * @return void
   */
   public function emptyForm() {
   }

   /**
   * task do export from database
   * @JRequest [limit,limitstart,order,filterfields,selected],key,exportform' fields
   * @return void
   */
   public function export() {
       
   }

   /**
   * task do import to database
   * @JRequest [limit,limitstart,order,filterfields,selected],key, importform' fields
   * @return void
   */
   public function import() {
       // limitstart=0, selected=0
   }

   /**
   * task do empty table
   * JRequest [limit,limitstart,order,filterfields,selected],key
   * @return void
   */
   public function emptyTable() {
       
       // limitstart=0, selected=0
   }

   /**
   * object main
   * @JRequest task + Jrequests for task 
   * @return void
   */
   procedure execute() {
       $task = JRequest('task','browse');
       $this->$task ();
   }
}