<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a bit shirty.
// Created By: Dave Williams | d4v3w

global $msg;

class library {

    const TEMPLATE_EXTENSION = '.html';
    const TEMPLATE_FOLDER = 'templates';

    public function library() {

    }

    public function makeFriendly($name, $preserve = false) {
        $filename = str_replace(' ', '', $name);
        $filename = str_replace("'", '', $name);
        $filename = str_replace("_", '-', $name);
        $filename = stripslashes(str_replace("\'", "", $filename));
        if ($preserve) {
            return strtolower($filename);
        } else {
            return urlencode(strtolower($filename));
        }
    }

    public function getRealIpAddr() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function createFriendlyURL($template, $id, $friendly) {
        $return = '';
        if (strstr($friendly, '/')) {
            $return = makeFriendly($friendly, true);
        } else {
            $return .= $template . '/';
            $return .= $id . '/';
            $return .= makeFriendly($friendly, true);
        }
        return $return;
    }

    public function compileHtml($items, $template, $root = '') {
        $html = $this->readHtml($template, false, $root);
        // Read the template into an array, then create a string.
        if (is_array($items)) {
            // Loop through all the parameters and set the variables to values.
            foreach ($items as $key => $value) {
                if (!is_array($value)) {
                    $template_name = '{' . strtoupper($key) . '}';
                    //if (is_array($value)) echo $template.'<br />';
                    $html = str_replace($template_name, $value, $html);
                }
            }
        }
        return $html;
    }

    public function readHtml($template, $cache = false, $root = '') {
        $ext = self::TEMPLATE_EXTENSION;
        if (isset($_GET['json']) && $_GET['json'] === '1') {
            $ext = '.json';
        }
        // override extension
        if (preg_match('/\.[a-z]{2,4}$/', $template)) {
            $ext = '';
        }
        // get folder
        $folder = $root . self::TEMPLATE_FOLDER;
        if ($cache) {
            $folder = 'cache';
        }
        // return file
        return implode('', (file($folder . '/' . $template . $ext)));
        // Read the template into an array, then create a string.
    }

    public function shorten($str, $num = 28) {
        $str = strip_tags($str);
        if (strlen($str) > $num)
            $str = substr($str, 0, $num) . '...';
        return $str;
    }

    public function swapQuote($str) {
        return eregi_replace('"', "'", $str);
    }

    public function setMsg($msg, $fail) {
        $msg['content'] = $msg;
        $msg['error'] = $fail;
    }

    public function drawMsg() {
        if ($msg != '') {
            if ($msg['error'])
                $content['IMG'] = 'apperror';
            else
                $content['IMG'] = 'tick';
            $content['MSG'] = $msg['content'];
            return $this->compileHtml($content, 'msg', 'msg/');
        } else {
            return '';
        }
    }

    public function createCacheFile($content, $cacheFile, $cacheFolder) {
        // if no cache file create 1
        if (!file_exists($cacheFolder . $cacheFile)) {
            // create cachefile
            $handle = fopen($cacheFolder . $cacheFile, 'w');
            // write html to file
            fwrite($handle, $content);
            // close connection to file
            fclose($handle);
        }
    }

    public function checkCacheFile($cacheFile, $cacheFolder) {
        // make sure cache file now exists
        if (file_exists($cacheFolder . $cacheFile)) {
            // print to page
            echo readHtml($cacheFile, true);
            return true;
        }
        return false;
    }

    public function clearCache($debug = false) {
        $dir = 'cache\/';

        // Open a known directory, and proceed to read its contents
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != ".." && preg_match('/.dcf/', $file)) {
                        //echo "filename: $file : filetype: " . filetype($dir .
                        // $file) . "\n";
                        deleteFile($dir . $file);
                    }
                }
                closedir($dh);
                if ($debug)
                    echo('Cache Cleared!');
            }
        }
    }

    public function deleteFile($target) {
        if (file_exists($target)) {
            unlink($target);
            return true;
        } else {
            return false;
        }
    }

    public function checkSecurity($check) {
        if ($check && (!isset($_SESSION['userID'], $_SESSION['timestamp']) || !is_numeric($_SESSION['userID']) || $_SESSION['userID'] <= 0 || !is_numeric($_SESSION['timestamp']) || ($_SESSION['timestamp'] + 14400) < date('U'))) {
            unset($_SESSION['userID']);
            unset($_SESSION['timestamp']);
            unset($_SESSION['time']);
            unset($_SESSION['date']);
            unset($_SESSION['username']);
            //if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !=
            // '') $query = '?'.$_SERVER['QUERY_STRING'];
            //else $query = '';

            //###################################
            //$login = new login();
            //echo($login->drawLogin());
            exit();
        } else {
            if (!empty($_SESSION['userID']) && $_SESSION['userID'] > 0) {
                $_SESSION['timestamp'] = date('U');
                return true;
            } else {
                return false;
            }
        }
    }

}
?>