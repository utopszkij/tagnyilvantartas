<?php
/**
  * Az LMP tagynilvántartás rendszer admin funkciók
  *
  * EZ A SCRIPT SZERVER SPECIFIKUS KODOT IS TARTALMAZ!
  *
  * search_str: SERVER SPECIFIKUS KOD
  *
  * 2015.09.09 packagelimit miatti modositás  Fogler Tibor
  */
//+ FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  
//2017.07.01 nem lehet programból visszaállítani mentést
//2017.07.01 mentés két részletben páros napokon első 114 rekord, páratlanokon a többi

// no direct access
defined('_JEXEC') or die('Restricted access');

define('PACKAGESIZE',10);  // record / Bulk insert package 

/* **************************************************************** *
 *                       MYSQL DUMP                                 *
 * **************************************************************** */  
class MySQLDump {
        var $database = null;
        var $compress = false;
        var $hexValue = false;
        var $filename = null;
        var $file = null;
        var $isWritten = false;
        function MYSQLDump($db = null, $filepath = 'dump.sql', $compress = false, $hexValue = false) {
          $this->compress = $compress;
          if (!$this->setOutputFile($filepath))
            return false;
          return $this->setDatabase($db);
        }
        function setDatabase($db) {
          $this->database = $db;
          if (!@mysql_select_db($this->database))
            return false;
          return true;
        }
        function getDatabase() {
          return $this->database;
        }
        function setCompress($compress) {
          if ($this->isWritten)
            return false;
          $this->compress = $compress;
          $this->openFile($this->filename);
          return true;
        }
        function getCompress() {
          return $this->compress;
        }
        function setOutputFile($filepath) {
          if ($this->isWritten)
            return false;
          $this->filename = $filepath;
          $this->file = $this->openFile($this->filename);
          return $this->file;
        }
        function getOutputFile() {
          return $this->filename;
        }
        function getTableStructure($table) {
          if (!$this->setDatabase($this->database))
            return false;
          $structure = "-- \n";
          $structure .= "-- Table structure for table `{$table}` \n";
          $structure .= "-- \n\n";
          $structure .= 'DROP TABLE IF EXISTS `'.$table.'`;'."\n";
          $structure .= "CREATE TABLE `".$table."` (\n";
          $records = @mysql_query('SHOW FIELDS FROM `'.$table.'`');
          if (@mysql_num_rows($records) == 0)
            return false;
          while ($record = mysql_fetch_assoc($records)) {
            $structure .= '`'.$record['Field'].'` '.$record['Type'];
            if (!empty($record['Default'])) {
              if (($record['Default']=='CURRENT_TIMESTAMP') |
			      (is_numeric($record['Default'])))
			    $structure .= ' DEFAULT '.$record['Default'];
			  else 	  
			    $structure .= ' DEFAULT \''.$record['Default'].'\'';
            } if (@strcmp($record['Null'],'YES') != 0)
              $structure .= ' NOT NULL';
            if (!empty($record['Extra']))
              $structure .= ' '.$record['Extra'];
            $structure .= ",\n";
          }
          $structure = @ereg_replace(",\n$", null, $structure);
          $structure .= $this->getSqlKeysTable($table);
          $structure .= "\n)";
          $records = @mysql_query("SHOW TABLE STATUS LIKE '".$table."'");
          //DBG echo $query;
          if ($record = @mysql_fetch_assoc($records)) {
            if (!empty($record['Engine']))
              $structure .= ' ENGINE='.$record['Engine'];
            if (!empty($record['Auto_increment']))
              $structure .= ' AUTO_INCREMENT='.$record['Auto_increment'];
          }
          $structure .= ";\n\n-- --------------------------------------------------------\n\n";
          $this->saveToFile($this->file,$structure);
        }
		
		//+ 2015.09.09 package limit miatti modositás
        function getTableData($table,$hexValue = true) {
          if (!$this->setDatabase($this->database))
            return false;
          $data = "-- \n";
          $data .= "-- Dumping data for table `$table` \n";
          $data .= "-- \n\n";
          $records = mysql_query('SHOW FIELDS FROM `'.$table.'`');
          $num_fields = @mysql_num_rows($records);
          if ($num_fields == 0)
            return false;
          $selectStatement = "SELECT ";
          $insertStatement = "INSERT INTO `$table` (";
          $hexField = array();
		  //+ FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  
		  $numField = array();
          for ($x = 0; $x < $num_fields; $x++) {
            $record = @mysql_fetch_assoc($records);
            if (($hexValue) && ($this->isTextValue($record['Type']))) {
              $selectStatement .= 'HEX(`'.$record['Field'].'`)';
              $hexField [$x] = true;
            } else {
              $selectStatement .= '`'.$record['Field'].'`';
              $hexField [$x] = false;
			}  
			if ($this->isNumValue($record['Type'])) {
              $numField [$x] = true;
			} else {
              $numField [$x] = false;
			}
            $insertStatement .= '`'.$record['Field'].'`';
            $insertStatement .= ", ";
            $selectStatement .= ", ";
          }
		  //- FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  
          $insertStatement = @substr($insertStatement,0,-2).') VALUES';
          $selectStatement = @substr($selectStatement,0,-2).' FROM `'.$table.'`';
          $records = @mysql_query($selectStatement);
          $num_rows = @mysql_num_rows($records);
          $num_fields = @mysql_num_fields($records);
          if ($num_rows > 0) {
            $data .= $insertStatement."\n";
			$k = 0;
            for ($i = 0; $i < $num_rows; $i++) {
              $record = @mysql_fetch_assoc($records);
              $data .= ' (';
              for ($j = 0; $j < $num_fields; $j++) {
                $field_name = @mysql_field_name($records, $j);
				  
				//+ FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  
				if ($numField[$j] && (trim($record[$field_name]) == '')) {
				   $record[$field_name] = 0;	
				}  
				//- FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  

                if ($hexField[$j] && (@strlen($record[$field_name]) > 0))
                  $data .= "0x".$record[$field_name];
                else
                  $data .= "'".@str_replace('\"','"',@mysql_escape_string($record[$field_name]))."'";
                $data .= ',';
              }
              $data = @substr($data,0,-1).")";
			  
			  if ($i == ($num_rows - 1)) {
				  // end of datas
                  $data .=  ';';
			  } else  if ($k < (PACKAGESIZE - 1)) {
				  // in the package
                  $data .= ',';
              } else {
				  // end of package
				  $data .= ';'."\n".$insertStatement;
				  $k = -1;
			  }
			  $data .= "\n";
              if (strlen($data) > 1048576) {
                $this->saveToFile($this->file,$data);
                $data = '';
              }
			  $k++;
            }
            $data .= "\n-- --------------------------------------------------------\n\n";
            $this->saveToFile($this->file,$data);
          }
        }
		//- 2015.09.09 package limit miatti modositás

        function getDatabaseStructure() {
		  $structure = '';	
          $records = @mysql_query('SHOW TABLES');
          if (@mysql_num_rows($records) == 0)
            return false;
//+ 2017.07.01 mentés két részletben
		  $nap = substr(basename($this->filename),16,2);
		  $paros = (($nap % 2) == 0);
		  echo 'nap='.$nap.' páros='.$paros.'<br />';
		  
		  
		  $i=1;
          while ($record = @mysql_fetch_row($records)) {
			if ((($paros) & ($i <= 114)) |
                ((!$paros) & ($i > 114))) 			
				$strkucture .= $this->getTableStructure($record[0]);
			$i++;
          }
          return true;
        }
        function getDatabaseData($hexValue = true) {
          $records = @mysql_query('SHOW TABLES');
          if (@mysql_num_rows($records) == 0)
            return false;
		  $nap = substr(basename($this->filename),16,2);
		  $paros = (($nap % 2) == 0);

		  echo 'nap='.$nap.' páros='.$paros.'<br />';

		  $i=1;
          while ($record = @mysql_fetch_row($records)) {
			if ((($paros) & ($i <= 114)) |
                ((!$paros) & ($i > 114))) 			
				$this->getTableData($record[0],$hexValue);
			$i++;
          }
//- 2017.07.01 mentés két részletben
        }
        function doDump() {
          $this->saveToFile($this->file,"<?php exit(); ?>\n");
          $this->saveToFile($this->file,"SET FOREIGN_KEY_CHECKS = 0;\n\n");
          $this->saveToFile($this->file,"SET NAMES UTF8;\n\n");
          $this->getDatabaseStructure();
          $this->getDatabaseData($this->hexValue);
          $this->saveToFile($this->file,"SET FOREIGN_KEY_CHECKS = 1;\n\n");
          $this->closeFile($this->file);
          return true;
        }
        function writeDump($filename) {
          if (!$this->setOutputFile($filename))
            return false;
          $this->doDump();
          $this->closeFile($this->file);
          return true;
        }
        function getSqlKeysTable ($table) {
          $primary = "";
          unset($unique); $unique = false;
          unset($index);  $index = false;
          unset($fulltext);  $fulltext = false;
          $results = mysql_query("SHOW KEYS FROM `{$table}`");
          if (@mysql_num_rows($results) == 0)
            return false;
          while($row = mysql_fetch_object($results)) {
            if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
              if ($primary == "")
                $primary = "  PRIMARY KEY  (`{$row->Column_name}`";
              else
                $primary .= ", `{$row->Column_name}`";
            }
            if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
              //if ((!is_array($unique)) OR ($unique[$row->Key_name]==""))
              if ((!is_array($unique)) OR (isset($unique[$row->Key_name])==false))
                $unique[$row->Key_name] = "  UNIQUE KEY `{$row->Key_name}` (`{$row->Column_name}`";
              else
                $unique[$row->Key_name] .= ", `{$row->Column_name}`";
            }
            if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
              //if ((!is_array($index)) OR ($index[$row->Key_name]=="")) {
              if ((!is_array($index)) OR (isset($index[$row->Key_name])==false)) {
                 if ($row->Sub_part != '')
			       $index[$row->Key_name] = "  KEY `{$row->Key_name}` (`{$row->Column_name}`({$row->Sub_part})";
				 else	 
			       $index[$row->Key_name] = "  KEY `{$row->Key_name}` (`{$row->Column_name}`";
              } else
                $index[$row->Key_name] .= ", `{$row->Column_name}`";
            }
            if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
              if ((!is_array($fulltext)) OR ($fulltext[$row->Key_name]==""))
                $fulltext[$row->Key_name] = "  FULLTEXT `{$row->Key_name}` (`{$row->Column_name}`";
              else
                $fulltext[$row->Key_name] .= ", `{$row->Column_name}`";
            }
          }
          $sqlKeyStatement = '';
          if ($primary != "") {
            $sqlKeyStatement .= ",\n";
            $primary .= ")";
            $sqlKeyStatement .= $primary;
          }
          if (is_array($unique)) {
            foreach ($unique as $keyName => $keyDef) {
              $sqlKeyStatement .= ",\n";
              $keyDef .= ")";
              $sqlKeyStatement .= $keyDef;
            }
          }
          if (is_array($index)) {
            foreach ($index as $keyName => $keyDef) {
              $sqlKeyStatement .= ",\n";
              $keyDef .= ")";
              $sqlKeyStatement .= $keyDef;
            }
          }
          if (is_array($fulltext)) {
            foreach ($fulltext as $keyName => $keyDef) {
              $sqlKeyStatement .= ",\n";
              $keyDef .= ")";
              $sqlKeyStatement .= $keyDef;
            }
          }
          return $sqlKeyStatement;
        }
        function isTextValue($field_type) {
          switch ($field_type) {
            case "tinytext":
            case "text":
            case "mediumtext":
            case "longtext":
            case "binary":
            case "varbinary":
            case "tinyblob":
            case "blob":
            case "mediumblob":
            case "longblob":
              return True;
              break;
            default:
              return False;
          }
        }
		
		//+ FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  
        function isNumValue($field_type) {
		  if ((substr($field_type,0,6)=='bigint') |	
		      (substr($field_type,0,7)=='decimal') |	
		      (substr($field_type,0,6)=='double') |	
		      (substr($field_type,0,5)=='float') |	
		      (substr($field_type,0,3)=='int') |	
		      (substr($field_type,0,9)=='mediumint') |	
		      (substr($field_type,0,7)=='numeric') |	
		      (substr($field_type,0,4)=='real') |	
		      (substr($field_type,0,8)=='smallint') |	
		      (substr($field_type,0,7)=='tinyint'))
			return true;
		  else
            return false;			  
		}	
		//- FT 2016-02-06 numerikus mezőkben nem lehet '' az érték !  


        function openFile($filename) {
          $file = false;
          if ($this->compress)
            $file = @gzopen($filename, "w9");
          else
            $file = @fopen($filename, "w");
          return $file;
        }
        function saveToFile($file, $data) {
          if ($this->compress)
            @gzwrite($file, $data);
          else
            @fwrite($file, $data);
          $this->isWritten = true;
        }
        function closeFile($file) {
          if ($this->compress)
            @gzclose($file);
          else
            @fclose($file);
        }
} // mysqlDump

   
class tagnyilvantartasControllerAdmin extends JControllerLegacy {
  /**
    * backup elkészitése  
	* @return void
	*/
  public function dobackup() {
	$session = JFactory::getSession();
	$userTerhats = $session->get('userTerhats');
	$jo = false;
	foreach ($userTerhats as $userTerhat) {
	  if ($userTerhat->terszerv_id == 1) $jo = true;
	}
	if (!$jo) {
	  echo '<div class="errorMs">'.JText::_('ACCESS_DENIED').'</div>';
	  return;
	}
	  
	  
	  $errormsg = ''; 
	  // config.php elérése
	  $config     = &JFactory::getConfig();
	  // mysql connection_aborted
      $connection1 = @mysql_connect($config->get('host'),
                                    $config->get('user'),
                                    $config->get('password'));
	  if (set_time_limit(120) == false) echo 'No set time limit :(<br />';
	  // dump elkészitése
      if ($connection1) {
	     $results = mysql_query("SET NAMES UTF8;");
  	     $filePath =  JPATH_ROOT."/backups/".$config->get('db')."-".date('Y-m-d-H-i')."-" .rand().".sql.php";
		 if (file_exists($filePath)) unlink($filePath);
         $dumper = new MySQLDump($config->get('db'),$filePath,false,false);
         if (!$dumper->doDump())
           $errorMsg .= "<br /> error mysql dump.";
      } else {
        $errorMsg .= "<br /> error can not connect to mysql database.";
      }
	  set_time_limit(30);
	  if ($errorMsg != '') {
         echo '<div class="errorMsg">'.$errormsg.'</div>';		
	  } else {
		echo '<div class="infoMsg">
			  <center> 
			  <br /><br />
			  <h3>'.$config->get('db')."-".date('Y-m-d-H-i').".sql".'</h3>
			  <p>Database backup is complete</p>
			  <br /><br />
			  </center>
			</div>
			<script type="text/javascript">
			  if (parent) {
				  var wait = parent.document.getElementById("waiting");
				  if (wait) {
					  wait.style.backgroundImage = "none";
					  wait.innerHTML="<h3>Az adatbázis mentés elkészült.</h3>";
				  }
			  }
			</script>
		';	
	}
  }
  
  public function backup() {
	  $session = JFactory::getSession();
	  $userTerhats = $session->get('userTerhats');
	  $userCsoport = $session->get('userCsoport');
	  if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').' '.$userCsoport->kod,'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
	  }
	  
	  $jo = false;
	  foreach ($userTerhats as $userTerhat) {
		  if ($userTerhat->terszerv_id == 1) $jo = true;
	  }
	  if (!$jo) {
		  echo '<div class="alert-error">'.JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').'</div>';
		  return;
	  }

	  echo '
	  <h2>Adatbázis mentés</h2>
	  <div id="waiting" class="turelem">&nbsp;</div>
	  <div style="display:none">
	  <iframe width="100%" height="300"  src="index.php?option=com_tagnyilvantartas&view=admin&layout=backup&task=admin.dobackup"></iframe>
	  </div>
	  ';
  }
  public function autoBackup() {
	  
  }
  public function restore() {
	  $session = JFactory::getSession();
	  $userTerhats = $session->get('userTerhats');
	  $userCsoport = $session->get('userCsoport');
	  if ($userCsoport->kod != 'A') {
				$this->setMessage(JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').' '.$userCsoport->kod,'error');
				$this->setRedirect('index.php?option=com_tagnyilvantartas&view=kapcsolatoks');
				$this->redirect();
	  }
	  $jo = false;
	  foreach ($userTerhats as $userTerhat) {
		  if ($userTerhat->terszerv_id == 1) $jo = true;
	  }
	  if (!$jo) {
		  echo '<div class="alert-error">'.JText::_('COM_TAGNYILVANTARTAS_ACCES_DENIED').'</div>';
		  return;
	  }

	  echo '<h2>Adatbázis helyreállítás korábbi mentett állapotba</h2>';

//2017.07.01 nem lehet programból visszaállítani mentést
	  echo '<p><br /><br /><br />Az adatbázis visszaállításhoz informatikusi szakértelemre van szükség.</p>';
	  echo '<p>Vegye fel a kapcsolatot a rendszer üzemetető informatikusokkal!</p>';
	  echo '<p>A mentés páros/páratlan napokon eltérő tartalommal készül</p>';
	  return;




	echo '<div class="turelem" style="display:none" id="turelem">&nbsp;</div>
	<p>Az adatbázis teljes tartalma (beleértve a felhasználókat és azok jogait is, valamint a változás naplót is) viszaáll a kiválasztott mentéskori állapotba. Minden ezután történt adatbevitel, módosítás véglegesen elveszik.
	</p>
	</p><strong>Csak nagyon megfontoltan, indokolt esetben használja!</strong></p>
	<p>Kattintson arra a mentésre amelyikre vissza akar állni!</p>
	<ul>
	';  

	// direktori listázás
	// dátum sorrend = filenév sorrend
	$files = array();
	if ($handle = opendir(JPATH_ROOT.'/backups')) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != "index.html" && file != "config.php") {
				$files[] = $file;
			}	
		}
		closedir($handle);
    }	
	asort($files);
	foreach ($files as $file) {
			  echo '<li><a href="index.php?option=com_tagnyilvantartas&view=admin&task=admin.dorestore&f='.$file.'" onclick="linkClick()">
			  '.$file.'</a></li>'."\n";
	}
	if (count($files) == 0) {
		echo '<li>Nem található mentés a szerveren.</li>';
	}

	echo '</ul>
	<p>A kattintás után azonnal elindul a visszaállítási folyamat, ami hosszabb ideig is eltarthat.</p>
	<script type="text/javascript">
	  function linkClick() {
		  document.getElementById("turelem").style.display="block";
		  return true;
	  }
	</script>
	';  
  }
  
  public function dorestore() {
	  $file = JRequest::getVar('f');
	  $db = JFactory::getDBO();
	  $user = JFactory::getUser();
	  $session = JFactory::getSession();
	  $userCsoport = $session->get('userCsoport');
	  if ($userCsoport->kod != 'A') {
		  echo '<div class="errormsg">Ezt a funkciót csak az adminisztratorok használhatják</div>';
		  return;
	  }
	  
	  $sql = file(JPATH_ROOT.'/backups/'.$file);
	  $sql[0] = '--'; // a távoli hozzáférést megakadályozó php sor törlése
	  if (count($sql) > 100) {

	    // át kell jelentkezni a mysql root accountra!
		// ========================= SERVER SPECIFIKUS KOD ===============================
        $connection2 = @mysql_connect('localhost',
                                      'root',
                                      '');
		mysql_select_db('lmp',$connection2);
		// ========================= SERVER SPECIFIKUS KOD ===============================
		
		$hibaDarab = 0;
		// Temporary variable, used to store current query
		$templine = '';
		// Loop through each line
		foreach ($sql as $line)	{
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
				continue;
			 
			// Add this line to the current segment
			$templine .= $line."\n";
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';')	{
				// Perform the query
                if (mysql_query($templine,$connection2) == false) {
					echo '<div class="errormsg">SQL error '.$templine.'</div>';
					$hibaDarab++;
				}	
				// Reset temp variable to empty
				$templine = '';
			}
		}	  
		// naplózás
		$db->setQuery('insert into #__tny_naplo 
		(kapcs_id, lastaction, lastact_time, lastact_user_id, lastact_info)
		values
		(0,"RESTORE","'.date('Y-m-d H:i:s').'","'.$user->id.'","FULL DATABASE RESTORE '.$file.'")');
		$db->query();
		if ($hibaDarab == 0)
    	  echo '<div célass="infoMsg"><br />Adatbázis visszaállítva.<br /></div>';	
	    else 
    	  echo '<div célass="errorMsg"><br />Adatbázis visszaállítás közben SQL hiba lépett fel.<br />
	      Értesitse a rendszergazdát!</div>';	
	  } else {
		  echo '<div class="errorMsg">A backup sql nem tünik jónak.</div>';
		  return;
	  }
  }
  public function setup() {
	  
  }
}
?>