<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a bit shirty.
// Created By: Dave Williams | d4v3w

// start session vars
session_start();

// Turn error reporting ON/OFF
error_reporting(E_ALL | E_STRICT);

// Date/Time
date_default_timezone_set('GMT');

// Try/Catch incase anything fails, hides sql errors etc
try {
    // Include Template class (compiles all elements to create page)
    require_once('application/library.class.php');
    $library = new library();
    // START CHECK FOR CACHE FILE
    // #####################
    $cachePage = trim(str_replace('.php', '', strtolower(basename($_SERVER['PHP_SELF']))));
    $remove = array("'#'","'$'","'%'","'&'","'\''","'='");
    $cacheQuery = trim(preg_replace($remove, '', strip_tags($_SERVER['QUERY_STRING'])));
    $cacheFile = $cachePage.$cacheQuery.'.dcf';
    $cacheFolder = 'cache/';
    $caching = false;

    if ($library->checkSecurity(false)) {
        $caching = false;
    //} else if (getRealIpAddr() == '94.192.27.178') {
    //  $caching = false;
    }

    if ($caching && $library->checkCacheFile($cacheFile, $cacheFolder)) {
        //echo('CACHED');
        // END SITE  - USE CACHE
        exit;
    } else {
        require_once('application/template.class.php');
        $friendlyUrl = '';
        if (isset($_GET['f']) && !empty($_GET['f'])) {
            $friendlyUrl = $_GET['f'];
        }
        $template = new template($friendlyUrl);

        // Compile Site
        $thesite = $template->compileTemplate();

        // is caching on - if true create cache file
        if ($caching) {
            $library->createCacheFile($thesite, $cacheFile, $cacheFolder);
        }

        // Draw page to screen
        echo $thesite;
    }
} catch (Exception $e) {
    mail('my.spam.box@hotmail.co.uk', 'Robot Error', $e->getMessage());
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
exit;

?>