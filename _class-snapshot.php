<?php  
  declare(strict_types=1);

#=============================================================
class jb_interrogator # extends MyPdo
{
PRIVATE $PDO = NULL;

#===========================================
PUBLIC function fileDetails($status, $path)
:string 
{
  $result   = ''; # <tr><td> ... </td><.tr>

  $subPath  = substr($path, strlen($_POST['SRC_PATH']) );
  $access   = fileatime($path); 
  $modify   = filemtime($path);
  $differ   = $modify - $access;

  $dAccess = new DateTime( date('Y-M-d H:i:s', $access) );
  $dModify = new DateTime( date('Y-M-d H:i:s', $modify) );
  $dDiffer = $dAccess->diff($dModify);

  $result .= '<tr>'; 
  if( 'NEW_FILE'===trim($status) ) :       
    $result .=  '<td class="fwb fgg">' .$status   .'</td>';
  else:  
    $result .=  '<td class="fwb fgb">' .$status   .'</td>';
  endif;  
  $result .=  '<td>'    .$subPath  .'</td>';
  $result .=  '<td>'    .$this->fnDate($access) .'</td>';
  $result .=  '<td class="warn"> ' .$this->fnDate($modify) .'</td>'; 

  $result .= '<td> '
          .     $dDiffer->format('%Y-%M-%d <br> %H:%i:%s') 
          .     ' </dd>'
          .  '</td>'
          . '</tr>';

  return $result;          
}

#=============================================================
# 
# fix path table length and remove prohibited characters
#
#=============================================================
PUBLIC function tableNameFix( $srcPath=NULL )
:string
{
  $result = '';

  $srcPath = $_POST['SRC_PATH']; // ?? $srcPath;
  # $addDate  = $addDate ?? NULL;

  # PROBLEMS
  # $srcPath = crypt($srcPath, 'SALTY_BACON_FLAVOUR');
  # $srcPath = password_hash($srcPath, PASSWORD_BCRYPT );
  $srcPath = md5($srcPath);
  

  # REPLACE PROHIBITED CHARACTERS
    $bad      = str_split('\/-.`');
    $replace  = '_(_^_)_';
    $srcPath  = str_replace($bad, $replace, $srcPath);
    $srcPath  = trim($srcPath);

  return $srcPath; // $this->tableNameFix;
} 

#=============================================================
PUBLIC function getDiscrepancies( & $byRefHash, & $byRefFiles )
:array // of discrepancies
{
  $pdo         = $this->getDb();
  $diffsResult = array(); # returned default

  $allFiles   = $this->getAllFiles();
  # [
  #   /home/john/www/detect-file-changes/yyy/_class-pdo.php] 
  #   => 
  #   f81afa38d6de3f88ca002883acb1fc14255b8ee9 
  # ]
  $byRefFiles = count($allFiles);

  # INSERT FILES IF AND ONLY IF CREATE TABLE
    if( $this->tableExists() ):
      # USE EXISTING TABLE
    else:
      $ok = $this->tableCreate();
      if($ok):
        $rows       = $this->insertFilesIntoTable();
        $byRefHash  = $rows;
      endif;  
    endif;
    $byRefHash = $this->tableRowCount();

# NEW FILE CHECK
  $hashs = $this->getAllHashs();
  /*
    $hashs => Array[0] ... [9999]
    (
      [file_path] => /home/john/www/detect-file-changes/yyy/ERROR_LOG.php
      [file_hash] => 5f4d33e18f015d5f6c56f22b959b38bdffae717b
    )
  */  
  if (! empty($hashs)):
    $tmp   = [];
    foreach ($hashs as $fileName => $hash):
      /*
      (
        [file_path] => /home/john/www/detect-file-changes/yyy/ERROR_LOG.php
        [file_hash] => 5f4d33e18f015d5f6c56f22b959b38bdffae717b
      )
      */      
      $tmp[] = $hash['file_path'];
    endforeach;

    $len = strlen($_POST['SRC_PATH']);
    foreach ($allFiles as $fileName => $hash):
      if( in_array($fileName, $tmp) ):
        # EXISITNG FILE
      else:
        $diffsResult["NEW_FILE"][$fileName] = substr($fileName, $len);
      endif;    
    endforeach;
    unset($tmp);
  endif;// (!empty($hashs)):

# NON-SPECIFIC CHECK FOR DISCREPANCIES
    # EXTRAPOLATE files against HASHES       
    if( ! $byRefHash ):
      # echo jt .'<b>NO HASHES ???</b>';

    else:  
      # [/home/john/www/detect-file-changes/yyy/new-003.php] 
      #   => 
      # da39a3ee5e6b4b0d3255bfef95601890afd80709
      $tmp = array();
      foreach ($hashs as $id => $aHash):
        if ( array_key_exists($aHash["file_path"], $allFiles) ):
          # HASHED NO CHANGE
          if ( isset($allFiles[$aHash["file_path"]]) ):
            if( $allFiles[$aHash["file_path"]] != $aHash["file_hash"] ):
              # MODIFIED
                $tmp = $allFiles[$aHash["file_path"]];
                $diffsResult["MODIFIED"][$aHash["file_path"]] = $tmp;
            endif;  
          endif;                      

        else:  
          $tmp = 'DELETED';
          $diffsResult["DELETED"][$aHash["file_path"]] = $tmp;
        endif;    
      endforeach;
      unset($tmp);
    endif;

  return $diffsResult;
}#endfunc

#=============================================================
PUBLIC function tableDelete()
:bool
{
  $result = FALSE;

  $tableName =  $this->tableNameFix($_POST['SRC_PATH']);
  if( $this->tableExists() ):
    try
    {
      $pdo = $this->getDb();
      # $sql = "TRUNCATE TABLE " .$tableName;
      $sql = "DROP TABLE `" .$tableName ."`";
      $result = $pdo->prepare($sql) 
             -> execute();
      $result = TRUE;       
    }catch (Exception $e) {
      echo($e .' ==> ' . __line__);
    } 
  endif;  

  return $result;
}


#=============================================================
PRIVATE function setAllFiles()
:array
{
  $this->aFiles = array(); # returned
  # EXTENSIONS to fetch, an 
  # EMPTY array will return all extensions
  # Veresion: 003 ??? 
    $aExt = []; // array("php"); # , 'ico', 'jpg', 'css');

  # DIRECTORIES to ignore, an empty array will check all directories
    $skip = array("logs", "logs/traffic");

  # build profile
    $dir  = new RecursiveDirectoryIterator( $_POST['SRC_PATH'] );
    $iter = new RecursiveIteratorIterator($dir);
    $cnt = 0;
    while ($iter->valid()): # sadf
      # SKIP unwanted directories
      if (!$iter->isDot() && !in_array($iter->getSubPath(), $skip)):
        # GET FILE EXTENSIONS
        if ( empty($aExt) ):
          # IGNORE file extensions
          $this->aFiles[$iter->key()] = hash_file("sha1", $iter->key());
          $cnt++;
        else: # IF AND ONLY IF in $aExt
          if (in_array(pathinfo($iter->key(), PATHINFO_EXTENSION), $aExt)):
              $this->files[$iter->key()] = hash_file("sha1", $iter->key());
            $cnt++;
          endif;
        endif; 
      endif;
      $iter->next();
    endwhile;
    $this->allFiles = $cnt;

  return $this->aFiles;
}

#=============================================================
PRIVATE function getAllFiles()
:array
{
  if( empty($this->aFiles) ):
    $this->setAllfiles();  
  endif;  

  return $this->aFiles;
}


#=============================================================
PRIVATE function getAllHashs()
:array
{
  $result = NULL;

  $sql    = 'SELECT * FROM `' .$this->tableNameFix() .'`';
  $result = $this->getDb()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  # PDO::FETCH_ASSOC       
    /*
      [0] => Array
      (
        [file_path] => /home/...../detect-file-changes/yyy/DEBUG-created.php
        [file_hash] => da39a3ee5e6b4b0d3255bfef95601890afd80709
      )    
    */

  return $result;
}


#===========================================
PRIVATE function fnDate($val)
:string 
{
  return date('Y-m-d', $val) .jj .date('H:i:s', $val);;
}

#=============================================================
PRIVATE function getDb()
:PDO
{
  if( ! $this->PDO ):
    try
    {
      $this->PDO = new PDO
      (
        "mysql:host=" .DB_HOST . "; 
         dbname="     .DB_NAME,  DB_USER,  DB_PWRD
      );
    }catch (Exception $e) {
      echo($e. ' ==> '. __line__);
    }
  endif;  

  return $this->PDO;
}  

 

#=============================================================
PRIVATE function tableExists()
:bool
{
  $result = TRUE;

  try
  {
    $pdo       = $this->getDb();
    $tableName = $this->tableNameFix();
    $sql = 'SELECT 1 FROM `' .$tableName .'` LIMIT 1';

    $result = $pdo->prepare($sql) -> execute();
  } catch (Exception $e) {
    echo($e .' ==> ' . __line__);
  }

  return $result;
}  

#=============================================================
PRIVATE function tableRowCount()
:int
{
  $result = NULL; #rows

  $tableName = $this->tableNameFix();
  $sql = 'SELECT 
            COUNT(*) 
          FROM 
            `' .$tableName .'`';
  $result =  $this->getDb()
                  ->query($sql)
                  ->fetchColumn(); # string         

  return (int) $result; #
}

#=============================================================
PRIVATE function tableCreate()
:bool
{
  $result = FALSE;

  try
  {
    $pdo = $this->getDb();
    $tableName = $this->tableNameFix();
    $sql = "
      CREATE TABLE IF NOT EXISTS `" .$tableName ."`
      (
          file_path VARCHAR(200) NOT NULL,
          file_hash CHAR(40) NOT NULL,
          PRIMARY KEY (file_path)
      );
    ";
     $result = $pdo->prepare($sql) -> execute();
  }
  catch (Exception $e)
  {
    echo($e .' ==> ' . __line__);
  }

  return $result;
}

#=============================================================
PRIVATE function insertFilesIntoTable()
:int
{
  $result = 0; // 'NOT REQUIRED';

  $tableName = $this->tableNameFix();
  $pdo = $this->getDb();
  $ok  = $this->tableCreate();
  if($ok):
    $allFiles = $this->getAllFiles();
    $sql      =  "INSERT INTO `" .$tableName ."`"
              .  " (file_path, file_hash) VALUES (:path, :hash)";
    $sth =  $pdo->prepare($sql);
    $sth -> bindParam(":path", $path);
    $sth -> bindParam(":hash", $hash);
    foreach($allFiles as $path => $hash):
      $sth->execute();
      ++$result;
    endforeach;
  endif;  

  return $result; # 
}   


}//= END CLASS ==========================================
