<?php
/**
 * viewer abstract object Joomla framework style
 * author:  Fogler Tibor   tibor.fogler@gmail.com
 * licence: GNU/GPL
 * 
 * JPATHBASE/default/tmpl/... default templates
 * JPATHBASE/name/tmpl/... current templates
 */ 
 class view {
     protected $readOnlys = array();
     protected $focused = '';
     protected $errorFields = Array();
	 public $controller = null;
	 public $model = null;
     public $jsValidator = 'return true;';
     public $pre = 'frm_';
     
	 public function display($tmpl) {
       if (file_exists(JPATH_BASE.'/'.$this->controller->name.'/tmpl/'.$tmpl.'.php') {
		   include JPATH_BASE.'/'.$this->controller->name.'/tmpl/'.$tmpl.'.php';
       } else if (file_exists(JPATH_BASE.'/default/tmpl/'.$tmpl.'.php') { 	   
		   include JPATH_BASE.'/default/tmpl/'.$tmpl.'.php';
	   } else {
		 echo '<div class="errorMsg">'.$tmpl.' tmpl not found</div>';
	   }
       echo '<script type="text/javascript">
       function jsValidator() {
         '.$this->jsValidator.'  
       }
       function OKClick() {
          // végig nézzük a formfeldeket ahol a class tartalmazza a field_requed stringrt az nem lehet üres
          
          if (jsValidator()) { 
            location="'.$this->saveURL.'"; 
          }
       }
       function CancelClick() {
          location="'.$this->browseURL.'"; 
       }
       function integerKeypress(e) {
         var key = e.which || e.keyCode;
         if ((!(key >= 48 && key <= 57) && // Interval of values (0-9)
            (key !== 8) &&              // Backspace
            (key !== 9) &&              // Horizontal tab
            (key !== 37) &&             // Percentage
            (key !== 39) &&             // Single quotes 
            (key !== 46))               // Dot
         {
          e.preventDefault();
          return false;
         }
       }
       function floatKeypress(e) {
         var key = e.which || e.keyCode;
         if ((!(key >= 48 && key <= 57) && // Interval of values (0-9)
            (key !== 8) &&              // Backspace
            (key !== 9) &&              // Horizontal tab
            (key !== 37) &&             // Percentage
            (key !== 39) &&             // Single quotes 
            (key !== 46))               // Dot
         {
          e.preventDefault();
          return false;
         }
       }
       function emailKeypress(e) {
         var key = e.which || e.keyCode;
         if ((!(key >= 48 && key <= 57) && // Interval of values (0-9)
            (key !== 8) &&              // Backspace
            (key !== 9) &&              // Horizontal tab
            (key !== 37) &&             // Percentage
            (key !== 39) &&             // Single quotes 
            (key !== 46))               // Dot
         {
          e.preventDefault();
          return false;
         }
       }
       ';
       if ($this->focused != '') {
          echo 'documents.forms.adminForm.form_'.$this->focused.'.focus();
          ';
       }
       echo '</script>
       ';
	 }  
     /**
     * set readOnlys protected propertys (use it the form display js code)
     */
     public function setReadOnly($fieldName) {
       $this->readOnlys[] = $fieldName;
     }
     /**
     * set focused protected propertys (use it the form display js code)
     */
     public function setFucus($fieldName) {
       $this->focused[] = $fieldName;
     }
     /** set form field is error marker (example red border)
       * @param string fieldName
       * @return void
       */       
     public function setErrorMarker($fn) {
       $this->errorFields[] = $fn;  
     }
     /** 
      * @param string fieldName
      * @return string ' disabled="disabled" ' or ''
      */ 
     protected function disabled($fn) {
         if (find($this->readOnlys($fn)))
             $result = ' disabled="disabled" ';
         else
             $result = '';
         return $result;
     }
     /** 
      * @param string fieldName
      * @return string ' errorField' or ''
      */ 
     protected function errorClass($fn) {
         if (find($this->errorFields($fn)))
             $result = ' errorField';
         else
             $result = '';
         return $result;
     }
     /*
      * functions from tmpl 
      * options array :  array[value] = label
      * they are use $this->item, $this->errorClass($fn)
      * @return string html code
      */
    define('frmREQUED',true);
    define('frmOPTINAL',false);
    define('frmDISABLED',true);
    define('frmENEABLED',false);
    
    public function textField($fName, $size=40, $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      return '<input type="text" name="'.$this->pre.$fName'" class="field_'.$fName.$this->errorClass($fName).$req.'" 
              value="'.$this->item->$fName.'" maxsize="'.$size.'"'.$dis.' />
              ';
    } 
    public function integerField($fName, $size=40, $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      return '<input type="text" name="'.$this->pre.$fName'" class="field_'.$fName.$this->errorClass($fName).$req.'" 
              value="'.$this->item->$fName.'" maxsize="'.$size.'"'.$dis.' onkeypress="integerKeypress(event)" />
              ';
    } 
    public function floatField($fName, $size=40, $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      return '<input type="text" name="'.$this->pre.$fName.'" class="field_'.$fName.$this->errorClass($fName).$req.'" 
              value="'.$this->item->$fName.'" maxsize="'.$size.'"'.$dis.' onkeypress="floatKeypress(event)" />
              ';
    } 
    public function emailField($fName, $size=40, $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      return '<input type="text" name="'.$this->pre.$fName.'" class="field_'.$fName.$this->errorClass($fName).$req.'" 
              value="'.$this->item->$fName.'" maxsize="'.$size.'"'.$dis.' onkeypress="emailKeypress(event)" />
              ';
    } 
    public function textareaField($fName, $rows=4, $cols=80, $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      return '<textarea name="'.$this->pre.$fName.'" class="field_'.$fName.$this->errorClass($fName).$req.'" 
              cols="'.$cols.'" rows="'.$rows.'"'.$dis.'>'.$this->item->$fName.'</textarea>
              ';
    } 
    public function editorField($fName, $rows=4, $cols=60, $requed=false, $disabled=false) {
    
    } 
    public function selectField($fName, $options=array(), $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      $s = '';
      foreach ($options as $fn => $fv) {
          if ($fn == $this->item->$fName) 
            $s .= '<option value="'.$fn.'" selected="selected">'.$fv.'</option>';  
          else
            $s .= '<option value="'.$fn.'">'.$fv.'</option>';  
      }
      return '<select name="'.$this->pre.$fName.'" class="field_'.$fName.$this->errorClass($fName).$req.'"'.$dis.'>
      '.$s.'
      </elect>
      ';
    } 
    public function radioField($fName, $options=araay(), $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      $s = '';
      foreach ($options as $fn => $fv) {
          if ($fn == $this->item->$fName) 
            $s .= '<div class="radioItem"><input type="radio" name="'.$this->pre.$fName.'" selected="selected"'.$dis.' />'.$fv.'</div>';  
          else
            $s .= '<div class="radioItem"><input type="radio" name="'.$this->pre.$fName.'"'.$dis.' />'.$fv.'</div>';  
      }
      return '<fieldset id="'.$this->pre.$fName.'" class="field_'.$fName.$this->errorClass($fName).$req.'">
      '.$s.'
      </fieldset>
      ';      
    } 
    public function checkBoxField($fName, $requed=false, $disabled=false) {
      if ($disabled) $dis=' disabled="disabled"'; else $dis='';
      if ($requed) $req=' field_requed'; else $req='';      
      return '<input type="checkbox" name="'.$this->pre.$fName'" class="field_'.$fName.$this->errorClass($fName).$req.'" 
              value="'.$this->item->$fname.'" maxsize="'.$size.'"'.$dis.' />
              ';
    } 
 }    
?>