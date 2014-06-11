#!/usr/bin/php -q
<?php
  //
  // CARGO COGUMELO, É PRECISO FACER UNS CANTOS APAÑOS 
  //

  // Project location
  define('SITE_PATH', getcwd().'/c_app/');
  $_SERVER['HTTPS'] = false;
  $_SERVER['HTTP_HOST'] = '';
  $_SERVER['REQUEST_URI'] = 'cogumelo shell script';
  $_SERVER['REMOTE_ADDR'] = "local_shell";

  // cogumelo core Location
  set_include_path('.:'.SITE_PATH);

  require_once("conf/setup.dev.php"); 

  require_once(COGUMELO_LOCATION."/c_classes/CogumeloClass.php");
  require_once(SITE_PATH."/Cogumelo.php");

  global $_C;
  $_C =Cogumelo::get();

  //
//  CARGO MÓDULO DEVEL E O DEVELDBCONTROLLER, QUE É ONDE ESTÁN OS MÉTODOS QUE NOS INTERESAN
//
require_once(COGUMELO_LOCATION."/c_modules/devel/devel.php");
require_once(COGUMELO_LOCATION."/c_modules/devel/classes/controller/DevelDBController.php");

//
//  UTILIZO A CLASE COMO FARÍA DENDE UN VIEW NORMAL
//
$develdbcontrol = new DevelDBController();
//var_dump( $develdbcontrol->getTablesSQL() ); // este método, por exemplo, devolvenos todo o  SQL xerado para os VO's do proxecto


 
 if ($argc>1){ //parameters handler
    switch($argv[1]){

      case 'flush': // delete temporary files
	       exec('rm tmp/');
	       echo "archivos temporales borrados\n";
         break; 

      case 'create_db': // create database
          $db = ReadStdin('Please enter the database: ', '');
          $user =  ReadStdin('Enter the username: ', '');
          //echo $db."\n";
          // Get the password
          fwrite(STDOUT, "Password: ");
          $password = getPassword(true);
          // Output the password
          //echo "\nYour password: " . $password . "\n";
          createDB();
          echo "database '.$db.' created\n";
        break; 

      case 'generate_tables': // create database tables
        createTables();
        echo 'generate db tables';
        break;  

      case 'bck': // do the backup of the given db
      	if ($argc>2){
      	  $bd = $argv[2];
      	  if ($argc>3){ //nome do ficheiro de bck
              $file = $argv[3];
          }
          else{ //crearase un bck.sql co nome da bbdd
              $file = $bd.'.sql';
          }
          doBackup($bd, $file);
      	}
      	else{
      	  echo "Not enough parameters\n usage: bck DB file\n";
      	}
        break;  

      case 'restore': // restore the backup of a given db
        if ($argc>2){
	       $bd = $argv[2];
         retoreDB($bd);
        }
        else
          echo "You must especify the database to retore\n"; 
        break;  

      default:
        echo "invalid parameter;try: 
        flush           to remove temporary files
        crete_db        to create a database
        restore_db      to restore a database";
        exit;     
    }//end switch 
}//end parameters handler
else{
  echo "Not enough arguments;try: 
        flush           to remove temporary files
        crete_db        to create a database
        restore_db      to restore a database";
}

function createDB(){
  $develdbcontrol = new DevelDBController();
  $develdbcontrol->createSchemaDB();
}

function createTables(){
  $develdbcontrol = new DevelDBController();
  $develdbcontrol->createTables();
}

function doBackup($DB, $file){
   echo "fai o bck...";
}

/**
 * Get data from the shell.
 */
function ReadStdin($prompt, $valid_inputs, $default = '') { 
    while(!isset($input) || (is_array($valid_inputs) && !in_array($input, $valid_inputs)) || ($valid_inputs == 'is_file' && !is_file($input))) { 
        echo $prompt; 
        $input = strtolower(trim(fgets(STDIN))); 
        if(empty($input) && !empty($default)) { 
            $input = $default; 
        } 
    } 
    return $input; 
} 

/**
 * Get a password from the shell.
 * This function works on *nix systems only and requires shell_exec and stty.
 *
 * @param  boolean $stars Wether or not to output stars for given characters
 * @return string
 */
function getPassword($stars = false)
{
    // Get current style
    $oldStyle = shell_exec('stty -g');

    if ($stars === false) {
        shell_exec('stty -echo');
        $password = rtrim(fgets(STDIN), "\n");
    } else {
        shell_exec('stty -icanon -echo min 1 time 0');

        $password = '';
        while (true) {
            $char = fgetc(STDIN);

            if ($char === "\n") {
                break;
            } else if (ord($char) === 127) {
                if (strlen($password) > 0) {
                    fwrite(STDOUT, "\x08 \x08");
                    $password = substr($password, 0, -1);
                }
            } else {
                fwrite(STDOUT, "*");
                $password .= $char;
            }
        }
    }

    // Reset old style
    shell_exec('stty ' . $oldStyle);

    // Return the password
    return $password;
}

?>
