<?php
// Recurse Delete
function recursiveDelete($str) {
    if (is_file($str)) {
    return @unlink($str);
    }
    elseif (is_dir($str)) {
    $scan = glob(rtrim($str,'/').'/*');
    foreach($scan as $index=>$path) {
    recursiveDelete($path);
    }
    return @rmdir($str);
    }
    }

//Recurse Copy
function recurse_copy($src,$dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }