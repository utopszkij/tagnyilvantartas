<?php
/**
 * model abstract object Joomla framework style
 * author:  Fogler Tibor   tibor.fogler@gmail.com
 * licence: GNU/GPL
 * 
 * A bint method find 'form_fieldName' names from html POST or GET (form_ = value of $formPre property)
 */ 
 class model {
   protected $tableName = '';  
   protected $idName = '';
   protected $formPre = 'form_';
   protected $errorMsg = '';
   protected $fields = '';  // sql select syntax pl:  t1.id, t1.name, t2.cyti as `település`
   protected $unions = array();  // sql csak táblanevek és összekapcsolások
                                 // pl: '#__tabla1 t1 left outer join #__tabla2 on t2.ref_id = t1.id'
   protected $filter = '';       // sql where feltétel
                                 // pl: 't1.date >= "" and t2.state = 1'
   protected $order = '';
   protected $errorFields = Array();  // fieldNames where check error
   public function getErrorMsg() {
       return $this->errorMsg;
   }
   public function __construct($tableName, $idName = 'id', $formPre = 'form_') {
      $this->tableName = $tableName; 
      $this->idName = $idName;
      $this->formPre = $formPre;
      $this->setFields();
      $this->addUnions($this->tableName);      
      $this->setFilter('1');
      $this->setOrder('1');
   }
   public function setFields($str = '') {
      if ($str != '') {
          $this->fields = $str;
      } else {
          $db = JFactory::getDBO();
          $db->setQuery('show fields from "'.$this->tableName.'"');
          $res = $db->loadObject();
          if ($db->getErrorNum() > 0) $db->stderr();
          foreach ($res as $res1) {
           if ($this->fileds != '') $fields .= ',';       
           $this->fields .= $res1->field_name];      
          }   
      }
   }
   public function setFilter($filter) {
       $this->filter = $filter;
   }
   public function setOrder($order) {
       $this->order = $order;
   }
   public function addUnion($join) {
      $this->unions[] = $join;  
   }
   public function getItems() {
      $sql = '';
      foreach ($this->unions as $union) {
        if ($sql != '') $sql .= 'union all'."\n";       
        $sql .= 'select '.$this->fields."\n".
                'from '.$union."\n";     
      }                
      $sql .= 'where '.$this->filter."\n";
      $sql .= 'order by '.$this->order."\n";
      $sql .= ' limit '.JRequest::getVar('limitstart',0).','.JRequest::getVar('limit',20);
      $db = JFactory::getDBO();
      $db->setQuery($sql);
      $result = $db->loadObjectList();
      if ($db->getErrorNum() > 0) $db->stderr();
      return $result;
   }
   public function getTotal() {
      $result = 0;
      $sql = '';
      foreach ($this->unions as $union) {
        if ($sql != '') $sql .= 'union all'."\n";       
        $sql .= 'select count(*) cc'."\n".
                'from '.$union."\n";     
      }                
      $sql .= 'where '.$this->filter."\n";
      $db = JFactory::getDBO();
      $db->setQuery($sql);
      $res = $db->loadObjectList();
      if ($db->getErrorNum() > 0) $db->stderr();
      foreach ($res as $res1)
        $result = $result + $res1->cc;
      return $result;        
   }
   /**
   * load one record, if id==0 then init record for insert
   */
   public funtlion load($id) {
      if ($id == 0) {
           $result = new stdclass();       
         } else {
          $sql = '';
          foreach ($this->unions as $union) {
            if ($sql != '') $sql .= 'union all'."\n";       
            $sql .= 'select '.$this->fields."\n".
                    'from '.$union."\n";     
          }                
          $sql .= 'where '.$this->idName.'="'.$id.'"'."\n";
          $sql .= 'order by '.$this->order;
          $db = JFactory::getDBO();
          $db->setQuery($sql);
          $result = $db->loadObject();
          if ($db->getErrorNum() > 0) $db->stderr();
      }
      return $result;
   }
   /**
   * @param array record data key = fieldName value = fieldValue
   */
   public function check($data) {
      // if error then set $this->errorMsg and 
      // set $this->errorFields and result false 
      return true; 
   }
   /**
   * @param array record data key = fieldName value = fieldValue
   */
   public function canDelete($data) {
      // if not then set $this->errorMsg and result false 
      return true; 
   }
   /**
   * @param array  $_GET or $_POST
   */
   public funtion bind($source) {
      $result = array();
      $fields = explode(',',$this->fields);
      foreach ($fields as $fn) {
        $w = explode(' ',$fn);
        $fn = $w[0];        
        if (isset($source[$this->formPre.$fn]))
           $result[$fn] = urldecode($source[$this->formPre.$fn]);            
      } 
      return $result;
   }
   
   /**
   * @param array record data key = fieldName value = fieldValue
   */
   public function save($data) {
       $sql = '';
       $fields = explode(',',$this->fields);
       if ($data[$this->idName]==0) {
           foreach ($felds as $fn) {
             $w = explode(' ',$fn);
             $fn = $w[0];        
             if ($sql != '') $sql .= ',';  
             $sql .= '"'.$db->quota($data[$fn]).'"'  ;
           }
           $sql 'insert into '.$this->tableName.' values ('.$sql.')';
       } else {
           foreach ($felds as $fn) {
             $w = explode(' ',$fn);
             $fn = $w[0];        
             if ($sql != '') $sql .= ',';  
             $sql .= '`'$fn.'`='.$db->quote($data[$fn])';
           }
           $sql = 'update '.$this->tableName.' set '.$sql.' where '.$this->idName="'.$data['id'].'"';
       }
      $db = JFactory::getDBO();
      $db->setQuery('delete from '.$this->tableName.' where '.$this->idName.'="'.$id.'"');       
      $result = $db->query();
      if (!$result) $db->stderr();
      return $result;
   }
   
   public function delete($id) {
      $db = JFactory::getDBO();
      $db->setQuery('delete from '.$this->tableName.' where '.$this->idName.'="'.$id.'"');       
      $result = $db->query();
      if (!$result) $db->stderr();
      return $result;
   }
   /** get errorFields
    *  @return array of fieldname
    */
   public function getErrorFields() {
       return $this->errorFields();
   }
 }    
?>
