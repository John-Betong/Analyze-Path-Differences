<?php 
  declare(strict_types=1);
    
# SET MORE CONSTNATs
  define('LOCALHOST', 'localhost'===$_SERVER['SERVER_NAME']); 
  define("DELETE_TABLE", LOCALHOST);
  define('jj', "\n<br>");
  define('jt', "\n\t<br>");

# SET DBASE PARAMETERS ONLINE OR LOCALHOST
  $fileAboveRoot = '/var/www/html/above_root/_database_params.php';
  if(file_exists( $fileAboveRoot) ): 
    # PREVENT HACKING
      include $fileAboveRoot;
  else: // LOCALHOST: 
      define('DB_HOST', 'localhost'); 
      define('DB_USER', 'QfiR03E36R14G62R50p35');
      define('DB_PWRD', 'zNa0t33y61j46k25k035!@#');
      define('DB_NAME', 'analyze_paths');
  endif;  

# SET DEFAULT PATHS - CAN ADD MANY MORE 
  $paths = [
    __DIR__,
    dirname(__DIR__),
  ]; 
 
#=============================================================
# DEBUG
#==========================================================
function fred ($val=NULL, $title=NULL)
{
  echo '<pre class="mg1 w88 bge warn bd1">';
    echo $title .' ==> ';
      print_r($val);
  echo '</pre>';
}

#==========================================================
function vd($val=NULL, $title=NULL)
{
  echo '<pre class="mg1 w88 bge warn bd1">';
    echo $title .' ==> ' .$title;
    var_dump($val);
  echo '</pre>';
}
