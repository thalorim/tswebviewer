<?php

/**
 *  This file is part of devMX TeamSpeak3 Webviewer.
 *  Copyright (C) 2011 - 2012 Max Rath and Maximilian Narr
 *
 *  devMX TeamSpeak3 Webviewer is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  TeamSpeak3 Webviewer is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with devMX TeamSpeak3 Webviewer.  If not, see <http://www.gnu.org/licenses/>.
 */
session_name("tswv");
session_start();

error_reporting(E_STRICT);

/* Author    : Maximilian Narr
 * Homepage  : http://maxesstuff.de
 * Email     : maxe@maxesstuff.de
 */

define("PROJECTPATH", realpath("../") . "/l10n");
define("ENCODING", "UTF-8");

require_once 'core/utils.php';
require_once 'core/htmlbuilder.php';
require_once 'core/xml.php';
require_once '../libraries/php-gettext/gettext.inc';
require_once '../core/tsv/tsv.class.php';
require_once '../core/i18n/i18n.func.php';
require_once '../core/utils/utils.func.php';

$utils = new tsvUtils("../");

// Outputs the header
require_once ("html/header.php");


// START NON OUTPUT FUCTIONS \\
// destroys the session. Loggs out of the installation script
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "logout")
{
    session_destroy();
    require_once 'html/select_language.php';
    require_once 'html/footer.php';
    exit;
}

// Unsets $_SESSION['config'] and $_SESSION['config_xml'] to return to the config file selection
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "return" && isset($_SESSION['validated']) && $_SESSION['validated'] == true)
{
    unset($_SESSION['config']);
    unset($_SESSION['config_xml']);
}

// Sets Locale
if (isset($_SESSION['lang']) && $_SESSION['lang'] != "")
{
    $lang = $_SESSION['lang'];
    setL10n($lang, "teamspeak3-webviewer", PROJECTPATH);
}

// Sets Language
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "setlang" && isset($_GET['lang']))
{
    $lang = $_GET['lang'];
    setL10n($lang, "teamspeak3-webviewer", PROJECTPATH);
    $_SESSION['lang'] = $_GET['lang'];
}

// Sets password
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'setpw' && isset($_POST['password']))
{
    // Set Password
    $result = setPassword($_POST['password']);

    if ($result == FALSE)
    {
        echo(throwAlert(__('The password could not be saved. Please make "pw.xml" in the install directory writable.'), 21));
        require_once 'html/set_password.php';
        require_once 'html/footer.php';
        exit;
    }

    $_SESSION['validated'] = true;
}

// Creates new config-file
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "new_config")
{
    $_SESSION['config'] = $_REQUEST['configname'];
    $_SESSION['config_xml'] = addModuleConfigParameter(simplexml_load_file("../config/template.xml"))->asXML();
}

// Sets Configfile
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "set_config" && isset($_GET['configname']))
{
    $_SESSION['config'] = $_GET['configname'];
    $_SESSION['config_xml'] = addModuleConfigParameter(simplexml_load_file("../config/" . $_SESSION['config'] . ".xml"))->asXML();
}

// Flushes caches
if (isset($_SESSION['validated']) && $_SESSION['validated'] == true && isset($_REQUEST['action']) && $_REQUEST['action'] == 'fc' && isset($_REQUEST['config']))
{
    $data = createConfigHtml();

    echo(flushCache($_REQUEST['config']));

    require_once 'html/select_config.php';
    require_once 'html/footer.php';
    exit;
}

// Deletes caches
if (isset($_SESSION['validated']) && $_SESSION['validated'] == true && isset($_REQUEST['action']) && $_REQUEST['action'] == "delete" && isset($_REQUEST['config']))
{
    echo(deleteConfigfile($_REQUEST['config']));

    $data = createConfigHtml();

    require_once 'html/select_config.php';
    require_once 'html/footer.php';
    exit;
}

// If password needs to be validated
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'validate' && isset($_POST['password']) && passwordSetted())
{

    $pw = file_get_contents("pw.xml");

    if (sha1(md5($_POST['password'])) == $pw)
    {
        // If password is right
        $_SESSION['validated'] = true;
    }
    else
    {
        // If password is wrong
        $_SESSION['validated'] = false;

        echo(throwAlert(__("The password you provided was not correct. Please try again.")));
    }
}

// STOPS NON OUTPUT FUNCTIONS \\
// If the language has not been setted
if (!isset($_SESSION['lang']))
{
    require_once 'html/select_language.php';
    require_once 'html/footer.php';

    exit;
}

// If the password has not been setted
if (!passwordSetted())
{
    require_once 'html/set_password.php';
    require_once 'html/footer.php';
    exit;
}

// If password is setted but has not been entered yet
if (passwordSetted() && !isset($_SESSION['validated']) || $_SESSION['validated'] != true)
{
    require_once 'html/enter_pw.php';
    require_once 'html/footer.php';
    exit;
}

// If no configfile is selected
if (!isset($_SESSION['config']) || $_SESSION['config'] == "")
{
    $data = createConfigHtml();

    require_once 'html/select_config.php';
    require_once 'html/footer.php';
    exit;
}

// If password is setted and has been entered and Configfile and Language is setted and Configfile should be written
if (passwordSetted() && $_SESSION['validated'] == true && isset($_SESSION['config']) && isset($_SESSION['lang']) && isset($_REQUEST['action']) && $_REQUEST['action'] == "submit")
{
    require_once '../core/config/configComparer.php';

    str_replace(".xml", "", $_SESSION['config_xml']);

    $xml = simplexml_load_string($_SESSION['config_xml']);

    // Check for configuration update
    $template = simplexml_load_file("../config/template.xml");

    $configComparer = new configComparer($xml, $template);
    $xml = $configComparer->updateOldFile();

    $necessary_vars = array("serveradress", "queryport", "serverport", "login_needed", "style", "arrows", "caching", "language", "display-filter");
    $vars_unavailable = false;

    // START VAR CHECKING \\
    // Check if necessary vars are full
    foreach ($necessary_vars as $var)
    {
        if (isNullOrEmtpy($_POST[$var]))
        {
            $vars_unavailable = true;
            $_POST[$var] = "";
            echo throwAlert($var . " " . __('is not set. Please check if you filled out all blanks.'));
        }
    }

    // Check servericons and imagepack
    if ((isNullOrEmtpy($_POST['servericons']) || $_POST['servericons'] == "false") && isNullOrEmtpy($_POST['imagepack']))
    {
        $vars_unavailable = true;
        $_POST['servericons'] = "";
        $_POST['imagepack'] = "";
        echo throwAlert("Servericons or imagepack" . __('is not set. Please check if you filled out all blanks.'));
    }

    // END VAR CHECKING \\

    $xml->host = $_POST['serveradress'];
    $xml->queryport = $_POST['queryport'];
    $xml->vserverport = $_POST['serverport'];
    $xml->login_needed = $_POST['login_needed'];
    $xml->username = $_POST['username'];
    $xml->password = $_POST['password'];

    // Modules
    if (empty($_POST['module']) || $_POST['module'][0] == "")
    {
        $pre = "htmlframe,style";
        $xml->modules = $pre;
    }
    else
    {
        $pre = "htmlframe,style,";
        $xml->modules = $pre . implode(",", $_POST['module']);
    }


    $xml->use_serverimages = $_POST['servericons'];
    $xml->imagepack = $_POST['imagepack'];
    $xml->style = $_POST['style'];
    $xml->show_arrows = $_POST['arrows'];
    $xml->enable_caching = $_POST['caching'];

    // Add caching-module to loaded modules
    if ($_POST['caching'] == "true") $xml->modules = (string) $xml->modules . ",fullCache";

    $xml->standard_cachetime = $_POST['standard_caching'];
    $xml->cache_method = $_POST['caching-method'];

    $xml->language = $_POST['language'];
    $xml->usage_stats = $_POST['usage-statistics'];

    $xml->filter = $_POST['display-filter'];
    $xml->show_icons = $_POST['show_icons'];

    $xml->show_country_icons = $_POST['show_country_icons'];
    $xml->date_format = $_POST['date-format'];

    // Not all necessary values were entered.
    if ($vars_unavailable)
    {
        $_SESSION['config_xml'] = $xml->asXML();
        $data = createEditHtml();

        require_once 'html/config.php';
        require_once 'html/footer.php';
        exit;
    }

    saveXmlFile("../config/" . $_SESSION['config'] . ".xml", $xml);

    if (!file_exists("../config/" . $_SESSION['config'] . ".xml"))
    {
        echo (throwAlert(__('The configfile is not writable. Please check if the required permissions are given to write the file.'), 24));
        exit;
    }

    $data = createEditHtml();

    echo(throwWarning(__('Configfile successfully saved.')));

    require_once 'html/config.php';
    require_once 'html/footer.php';

    echo('<script type="text/javascript">
            var ret = true;
          </script>');

    exit;
}

// If password is setted and has been entered and Configfile and Language is setted
if (passwordSetted() && $_SESSION['validated'] == true && isset($_SESSION['config']) && isset($_SESSION['lang']))
{
    $data = createEditHtml();

    require_once 'html/config.php';
    require_once 'html/footer.php';
    exit;
}
?>
