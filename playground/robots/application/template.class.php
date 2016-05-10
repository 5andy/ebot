<?php
// 2016 Dave Williams. All rights reserved.
// Use of this code without permission from the owner will result in us getting a bit shirty.
// Created By: Dave Williams | d4v3w

//require_once('database.mysqli.class.php');
require_once('library.class.php');
//require_once('nav.class.php');
//require_once('login.class.php');

include_once('dBug.php');
//new dBug($myVariable);

class template {

    var $pageID = 1;
    var $data = array();
    var $secure = false;
    var $templateID = 1;
    var $sitename = 'Robot Beach';
    var $siteurl = '/';
    var $library = null;

    public function template($friendlyUrl = '') {
        $this->library = new library();
        if ($friendlyUrl != '') {
            // get Friendly URL
            $this->pageID = $this->getFriendlyUrl($friendlyUrl);
        } elseif (isset($_GET) && is_array($_GET) && count($_GET) > 0) {
            if (!empty($_GET['pageid']) && is_numeric($_GET['pageid'])) {
                $this->pageID = $_GET['pageid'];
            }
        } else {
            //header('Location: http://www.insidejobmusic.com/index.php?pageid=1');
        }
        //$login = new login();
    }

    private function getPageContent($pageID) {
        // $db = new database();
        // if (isset($this->data['content']) && is_array($this->data['content']) && count($this->data['content']) > 0) {
            // return true;
        // } elseif ($db->query('SELECT `content`.`ID` AS `PAGEID`, `content`.`Heading` AS `heading`, `content`.`IntroText` AS `intro`, `content`.`Body` AS `body`, `content`.`FriendlyURL` AS `friendly`, `content`.`Created`, `content`.`LastUpdated`, `content`.`URL` AS `URL`, `content`.`Secure` AS `secure`, `templates`.`Name` AS `template`, `templates`.`ID` AS `templateID`, `templates`.`Type` AS `type`, `templates`.`urlname` AS `templatename` FROM `content`, `templates` WHERE `content`.`ID` = '.$pageID.' AND `templates`.`ID` = `content`.`Template` LIMIT 1;') && $db->numRows() == 1) {
            // //echo('SELECT content.ID AS `PAGEID`, content.Heading AS `heading`, content.IntroText AS `intro`, content.Body AS `body`, content.FriendlyURL AS `friendly`, content.Secure AS `secure`, templates.Name AS `template`, templates.Type AS `type`'.$sql.' FROM `content`, `templates`'.$table.' WHERE content.ID = '.$pageID.' AND templates.ID = content.Template'.$where.' LIMIT 1;');
            // $this->data['content'] = $db->fetcharray();
            // $this->templateID = $this->data['content']['templateID'];
            // $this->data['content']['intro'] = stripslashes($this->data['content']['intro']);
            // $this->data['content']['heading'] = stripslashes($this->data['content']['heading']);
            // $this->data['content']['body'] = stripslashes($this->data['content']['body']);
            // $this->data['content']['friendly'] = str_replace(' ', '', strtolower($this->data['content']['friendly']));
            // $this->data['content']['friendlyURL'] = createFriendlyURL($this->data['content']['templatename'], $this->data['content']['PAGEID'], $this->data['content']['friendly']);
            // if ($this->data['content']['URL'] != '') {
                // $this->data['content']['URL'] = compileHtml($this->data['content'], 'common/url');
            // }
            // return $this->data['content'];
        // } else {
            $this->data['content'] = array();
            $this->data['content']['heading'] = 'Default Robot Heading';
            $this->data['content']['desc'] = 'Description Text...';
            $this->data['content']['type'] = 0;
            return $this->data['content'];
        //}
    }

    public function getFriendlyUrl($friendlyUrl) {
        // $db = new database();
        // if ($db->query('SELECT `ID` FROM `content` WHERE `FriendlyURL` = "'.$friendlyUrl.'" LIMIT 1;') && $db->numRows() == 1) {
            // $this->data['404'] = $db->fetcharray();
            // return $this->data['404']['ID'];
        // } elseif ($db->query('SELECT `ID` FROM `content` WHERE `ID` = "'.$friendlyUrl.'" LIMIT 1;') && $db->numRows() == 1) {
            // $this->data['404'] = $db->fetcharray();
            // return $this->data['404']['ID'];
        // }
        // send to homepage if not found
        return '';
    }

    public function siteDefaults() {
        $defaults = array();
        $defaults['YEAR'] = date('Y');
        $defaults['WEBSITENAME'] = $this->sitename;
        $defaults['COPYRIGHT'] = $this->sitename.' &copy; '.$defaults['YEAR'];
        $defaults['WEBSITEURL'] = $this->siteurl;
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            $defaults['WEBSITEURL'] = 'http://localhost'.dirname($_SERVER["PHP_SELF"]).'/';
        }
        return $defaults;
    }

    public function compileTemplate() {
        // Setup Template
        $template = array();

        // Website Defaults (copyright etc.)
        $template = $this->siteDefaults();

        // page ID
        $template['PAGEID'] = $this->pageID;

        // navigation

        // Draw Page Content
        $content = $this->getPageContent($this->pageID);
        if ($content) {
            //new dBug($_SERVER['REQUEST_URI']);
            //print_r($content);
            if (isset($content['title']) && !empty($content['title'])) {
                $template['TITLE'] = 'Blog - '.$content['title'];
                $template['DESC'] = $content['title'];
            } else {
                $template['TITLE'] = $content['heading'];
                $template['DESC'] = $content['desc'];
            }
        }

        // FRIENDLY URL
        $template['FRIENDLYURL'] = '';
        if (isset($this->data['content']['friendlyURL'])) {
            $template['FRIENDLYURL'] = $this->data['content']['friendlyURL'];
        }

        // Is the template php driven?
        $inc = false;
        if ($content['type'] > 0) $inc = true;

        if (isset($content['body']) && $content['body'] != '') {
            $content['body'] = compileHtml($content, '/default/body');
        }
        if (!isset($content['template']) || $content['template'] === '') {
            $content['template'] = 'default';
        }

        if ($inc) {
            $template['content'] = '';
            // show default template and included php template
            if ($content['type'] == 2) {
                $template['content'] = $this->library->compileHtml($content, '/default/index');
            }

            // include main template content
            $template['content'] .= include('templates/'.$content['template'].'/index.php');

        } else {
            // default
            $template['content'] = $this->library->compileHtml($content, $content['template'].'/index');
        }

        $template['created'] = '';//$content['Created'];
        $template['modified'] = '';//$content['LastUpdated'];

        // Add template sepcific includes
        // $template['TEMPLATEINCLUDES'] = '';
        // if (file_exists('templates/'.$content['template'].'/includes.html')) {
            // $template['TEMPLATEINCLUDES'] = readHtml($content['template'].'/includes');
        // }


        // Compile Page
        return $this->library->compileHtml($template, 'index');
    }
}
?>