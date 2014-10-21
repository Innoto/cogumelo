<?php
// We check that the conexion comes from localhost
if ($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
    define('SITE_PATH', getcwd().'/../c_app/');
    $par = $_GET['q'];
    switch ($par){
        case 'rotate_logs':
            $dir = SITE_PATH.'log/';
            $handle = opendir($dir);
            while ($file = readdir($handle)){
                if (is_file($dir.$file)){
                    $file = $dir.$file;
                    $pos = strpos($file, 'gz');
                    if ($pos==false){
                        $gzfile = $file.'-'.date('Ymd-Hms').'.gz';
                        $fp = gzopen($gzfile, 'w9');
                        gzwrite ($fp, file_get_contents($file));
                        gzclose($fp);
                    }
                }
            }
            break;
        case 'flush':
            $dir = SITE_PATH.'tmp/templates_c/';
            $handle = opendir($dir);
            while ($file = readdir($handle)){
                if (is_file($dir.$file)){
                    unlink($dir.$file);
                }
            }
        break;
    }
}
else{
exit;
}
?>