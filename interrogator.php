<?php /* http://phpmaster.com/monitoring-file-integrity/ */
 declare(strict_types=1); 
 ini_set('display_errors', '1');
 error_reporting(-1); 
 # $_POST = []; // 

# DATABASE LOGIN
  include '_config.php';
  include '_class-snapshot.php';
  $jb = new jb_interrogator($paths);

  # SET DEFAULT PATH
    $_POST['SRC_PATH'] = $_POST['SRC_PATH'] ?? $paths[0];
    # $_POST = [];

  $TITLE = 'John_Betong\'s File Integrity Check';
  $FORUM = 'https://www.sitepoint.com/forums/showthread.php'
         . '?867381-Detect-file-changes';
  $DESC  = 'Simple PHP Class to monitor online file changes.'
         . 'Hacked servers can now be easily checked to prevent problems.';
  $dLong = date("l, \\t\h\\e jS \of F, Y - H:i:s");
  $SHOW  = $_POST['submit'] ?? NULL;
  $CSS   = file_get_contents('_style.css');
  $SITE  = NULL;
  if(LOCALHOST):
    $SITE = 'https://johns-jokes.com/downloads/sp-a/detect-file-changes/ver-002/';
    $SITE = '<a class="flr ooo btn bga"><a href="' .$SITE .'">ONLINE</a>';
  endif;

$hdr = <<< __TMP
<!DOCTYPE HTML>
<html lang="en-GB">
<head>
<title> $TITLE </title> 
<meta name="viewport"     content="width=device-width, initial-scale=1">
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<meta name="description"  content="$DESC">
<meta name="author"       content="John_Betong | admin@johns-jokes.com">
<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
<style> $CSS </style>
</head>
__TMP;
# CRUNCH
  $hdr = str_replace('  ', ' ', $hdr);
  echo str_replace(["\n","\t", "\r"], "", $hdr) ."\n";
?> 

<body>
  <h5 class='flr ooo tdn'> <?= $SITE ?> &nbsp; </h5>

  <h1 class="ooo"> <a class="tdn" href="index.php"> <?= $TITLE ?> </a> </h1>
  <h5 class="ooo tac">
    Inspired by
    <a href='http://phpmaster.com/monitoring-file-integrity/'>
      Martin Psinas
    </a> 
  </h5>  
  <h5 class='hhh flr ooo'>
    <a class="tdn" href="$FORUM">SitePoint Forum</a> 
  </h5>
  <hr>





<form class='' action='?' method='post'>
  <fieldset class='w88 mga p42 bgs clb bdr'>
    <legend class="fwb fsl">Please select a path &nbsp;</legend> 
    <dl class="dib lh2">
    <dt class="ooo">File Paths: &nbsp; <b><?= count($paths) ?> </b></dt>
    <dd>
      <?php 
        $lastSrcPath = $_POST['SRC_PATH'];
        echo '<select class="fss" name="SRC_PATH">';
        foreach($paths as $id => $path):
          if( $path === $lastSrcPath):
            echo '<option value="' .$path .'" selected>' .$path .'</option>';
          else:  
            echo '<option value="' .$path .'">' .$path .'</option>';
          endif;  
          echo "\n";
        endforeach;   
        echo '</select>';
    echo '</dd><dd> &nbsp; </dd>';

    echo <<< __________TMP
      <dt class="fwb">
        <input type="submit" name="submit" value="INTERROGATE" />
        &nbsp;&nbsp;
        <input type="submit" name="submit" value="DELETE TABLE" />
      </dt>  
__________TMP;
      ?>
    </dl>
  </fieldset>

</form>     
<?php ##################################################### ?>



<?php  # RESULTS ##########################################
  $mode = $_POST['submit'] ?? NULL;
  if($mode):
    echo '<fieldset class="w88 mg1 bgs bd1 XXXp42">';
      switch($mode):
        case 'INTERROGATE' : #======================================
          $leg = 'Following discrepancies were found:';
          echo '<legend class="fwb fsl warn">' .$leg .' </legend>';
            echo '<dl class="ooo">';
              $diffs = $jb->getDiscrepancies($byRefHash, $byRefFiles);

              echo '<dt class="bge">Totals:</dt>';
                echo '<dd>';  
                  echo '<b class="dib w06">Files: </b>' .$byRefFiles .jj;
                  echo '<b class="dib w06">Hashs: </b>' .$byRefHash;
                echo '</dd>';  

              echo '<dt class="bge">Path: </dt>';  
                echo '<dd class="fwb">'. $_POST['SRC_PATH'] .'</dd>';

                echo '<dt>TableName:</dt>';
                echo '<dd>';
                  echo '<b>' .$jb->tableNameFix() .'</b>';
                echo '<dd>';    

              #================================================  
              echo '<dt class="bge warn"> Discrepancies: </dt>';  
              echo '<dd>';
                $result = '<b>Success</b> - there are no discrepancies :)';
                if( empty($diffs) ):
                  # 'Hurray - there are no discrepancies';
                else:  
                  echo '<table class="fss">'
                       . '<tr>'
                       .    '<th> Status        </th>'
                       .    '<th> FileName        </th>'
                       .    '<th> Last accessed </th>'
                       .    '<th> Last modified </th>'
                       .    '<th> difference    </th>'
                       .  '</tr>'; 
                  $result = '';

                  foreach ($diffs as $status => $affected):
                    $result = '';
                    if (is_array($affected) && !empty($affected)):
                      foreach($affected as $path => $hash):
                        clearstatcache(TRUE, $path );
                        if( file_exists($path)):
                          # NEW_FILE and MODIFIED files
                          echo $jb->fileDetails($status, $path);
                        else: 
                          # DELETED files      
                          $subPath  = substr($path, strlen($_POST['SRC_PATH']) );
                          $result .= '<tr>';        
                          $result .=  '<td class="fwb warn">' .$status  .'</td>';
                          $result .=  '<td>' .$subPath .'</td>';
                          $result .=  '<td>not known</td>'; 
                          $result .=  '<td>not known</td>'; 
                          $result .=  '<td>not known</td>'; 
                          $result .= '</tr>';
                        endif;  
                      endforeach;
                    endif;
                  endforeach;
                endif; // if( empty($diffs) ):
                echo $result;
              echo '</dd>';  
                echo '</table>';
              echo '</dd>';
            echo '</dl>';
        break;

        case 'DELETE TABLE' : #======================================
          $leg = 'Delete Table:';
          echo '<legend class="fwb fsl">' .$leg .' </legend>';

          echo '<dl>';
            $msg  = 'Sorry, not allowed ONLINE :(';
            # echo '<dt>' .vd(DELETE_TABLE) .'</dt>';
            if(DELETE_TABLE):
              $msg  = '<b class="warn"> Table does not exist??? </b>';
              if( $ok = $jb->tableDelete() ):
                $msg  = 'Success - Deleted table';
              endif;  
              # echo '<dt>' .$msg .'</dt>';
            endif;  
            echo '<dt>' .$msg .'</dt>';
            echo '<dd> &nbsp; </dd>';
            echo '<dd>Table: ' .$jb->tableNameFix .'</dd>';
          echo '</dl>';
        break;
      endswitch;
    echo '</fieldset>';      
  endif;
  ?>  

  <p> &nbsp; </p>
  <div class="POF tac bga w99 p42">
    <a class="fll tdn" href="https://validator.w3.org/nu/">HTML check</a>
    <a class="flr tdn" href="https://jigsaw.w3.org/css-validator/">CSS check</a>
    Wonderful place for a footer
  </div>   
</body>
</html><?php 

#===========================================
function fileDetails($status, $path)
:string 
{
  $result = '';

  $subPath  = substr($path, strlen($_POST['SRC_PATH']) );
  $access   = fileatime($path); 
  $modify   = filemtime($path);
  $differ   = $modify - $access;

  $result .= '<dt>' .$status .'<dt>';
  $result .= '<dd>file: ==> ' .$subPath . '</dd>';
  $result .= '<dd> last accessed: &nbsp;' .date('Y-M-d  -  H:i:s', $access);
  $result .= '<dd class="warn"> last modified: &nbsp;' .date('Y-M-d  -  H:i:s', $modify); 

  $dAccess = new DateTime( date('Y-M-d H:i:s', $access) );
  $dModify = new DateTime( date('Y-M-d H:i:s', $modify) );
  $dDiffer = $dAccess->diff($dModify);
  $result .= '<dd> difference: &nbsp;&nbsp;&nbsp;'
          .     $dDiffer->format('%Y-%M-%d %H:%i:%s') 
          . '&nbsp; (yyyy-mm-dd - H:i:s) </dd>';

  return $result;          
}
