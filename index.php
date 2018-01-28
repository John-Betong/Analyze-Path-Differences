<?php
  declare(strict_types=1);
  ini_set('display_errors', '1');
  error_reporting(-1);
  # $_POST = [];
    
# NOT REQUIED ON THIS PAGE
  include '_config.php'; 

  $TITLE = 'John_Betong\'s Website File Integrity Check';
  $DESC  = 'Simple PHP Class to monitor online file changes.'
          . 'Hacked servers can now be easily checked to prevent problems.';
  $DEMO  = 'http://johns-jokes.com/downloads/sp-a/detect-file-changes/ver-002/';
  $DEMO  = 'interrogator.php';
  $SITE  = NULL;
  if(LOCALHOST):
    $url   = 'https://johns-jokes.com/downloads/sp-a/detect-file-changes/';
    $SITE  = '<h6 class="flr ooo"><a href="' .$url .'">ONLINE</a></h6>';
  endif; 
  $IMG_1    = 'imgs/screenshot-2018-01-28-31.3kb.png';

# HIGHLIGHT FILES  #####################################
  $getParams = [
    'index.php',
    'interrogator.php',
    '_class-snapshot.php',
    '_config.php',
    '_style.css',
  ];  
  $getParam = $_GET['getParam'] ?? $getParams[0];

# STYLE SHEET INLINE ###################################
  $_CSS  = file_get_contents('_style.css');
 
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
<style> $_CSS </style>
</head>
__TMP;
# CRUNCH Header and Style sheet
  $hdr = str_replace('  ', ' ', $hdr);
  $hdr = str_replace(["\n","\t", "\r"], "", $hdr) ."\n";
echo $hdr;

?>
<body>
  <?= $SITE ?>
  <h1 class="ooo"> 
    <a class="tdn" href="?"> <?= $TITLE ?> </a> 
  </h1>
  <h5 class='flr ooo '>
    <a class='btn tdn bgl' href="$FORUM">SitePoint Forum</a> 
  </h5>
  <div class="tac">
    Inspired by:
    <a href='http://phpmaster.com/monitoring-file-integrity/'>
      Martin Psinas
    </a> 
    <sup>blog</sup>
  </div>  
  <hr>

  <div class="tac fwb">    
    <?php # PAGES =============================
      $pages = [
        'Introduction'  => 'index.php',
        'Source files'  => 'index.php?get',
      ];
      foreach($pages as $title => $url):
        echo "\n" .'<a class="btn bge tdn" href="' .$url .'">' 
                .$title 
             .'</a>';
      endforeach;  
    ?>
  </div>    

  <div class="w88 mga">
    <fieldset class="bgs tal">

    <?php if( count($_GET) ): #=========== SOURCE FILES =================?>
      <legend class="fsl fwb"> Source Files: </legend>
        <div class="tac fss">
          <?php 
            foreach( $getParams as $id => $file):
              echo "\n" .'<b class="btn bgl ">' 
                  .   '<a class="tdn" href="?getParam=' 
                  .     $file .'">' .$file .'</a>'
                  .'</b>';
            endforeach;
        ?>
        </div>

      <h2 class="w99 bge bd1"> <b>Source: &nbsp; </b> <?= $getParam ?> </h2>
      <div class="fss">
        <?php highlight_file( $getParam); ?> 
      </div>  
      <br>

    <?php else: //===================================== ?> 
      <legend class="fsl fwb w99">Introduction:</legend>
        <div class="p42">
          Have you ever wondered what files have been newly created, last modified or even deleted?
          <br>
          <h3 class="ooo"> Look no further - this is the App' for that :) </h3>
          <br>
        </div>  

        <h2 class="ooo"> File Integrity Class </h2>
        <div class="p42">
          A simple Class that interrogates all files in a site path. 
          <br>
          File contents are hashed and a "snapshot" saved to a dynamically created database table. 
          <br>
          <i class="fss"> Unfortunately folder names must be "fixed" to in order to conform :) </i>
          <br>
          Any time afterwards the "snapshot" can be compared against the current files. 
          <br>
          All discrepancies are shown which facilitates finding problematic files. 
          <br>
          A new "snapshot" can be created once all discrepances have been resolved.
          <br><br>
        </div>

        <h2 class="ooo"> Results of Interrogation: </h2>
        <ol>
          <li>Amended files </li>
          <li>Deleted files </li>
          <li>New files </li>
        </ol>  

        <h2 class="ooo">Installation: </h2>
        <ol>
          <li>
            Download and extract any of the following zip files into a new LOCALHOST "folder-name":
            <br>
            <a class="btn bge tdn" href="zips/ver-002.zip">source.zip</a>
            <a class="btn bge tdn" href="zips/ver-002.tar.xz">source.tar.xz</a>
            <a class="btn bge tdn" href="zips/ver-002.7z">source.7z</a>
          </li>
          <li>
            Use PhpMyAdmin to create a "DB_HOST" Database named "DB_NAME"
            <br>
            Set Database permissions for:
            <ul>
              <li> "DB_USER" </li>
              <li> "DB_PWD"  </li>
            </ul>
          </li>
          <li>
              Edit: &nbsp; <b> _config.php</b> 
              <br>
              <ul>
                <li> Set Database values for: DB_HOST, DB_USER, DB_PWD, DB_NAME</li>
                <li>  <i class="fss"> More paths may be added to the "folder-name" path and its parent </i></li>
                <li> Browse to "folder-name"/index.php</li>
                <li> Test thoroughly and once satisfied upload to your online site.</li>
              </ul>  
          </li>
        </ol>

        <dl>
          <dt>
            <a class="btn tdn bgl fsl" href="<?= $DEMO ?>">Online Demo</a>
          </dt>  
          <dd class="tac">
            <a href="<?= $DEMO ?>">
              <img src="<?= $IMG_1 ?>" width="420" alt="Screenshot" /> 
            </a>  
          </dd>
        </dl>  
  

    <?php endif; ?>
    </fieldset>
 </div>

  <p> &nbsp; </p>
  <div class="POF tac bga w99 p42">
    <a class="fll tdn" href="https://validator.w3.org/nu/">HTML check</a>
    <a class="flr tdn" href="https://jigsaw.w3.org/css-validator/">CSS check</a>
    Wonderful place for a footer
  </div>   
</body>
</html>
