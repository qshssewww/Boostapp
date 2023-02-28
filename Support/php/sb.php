<?php
/**
 * ======================================
 * SUPPORT BOARD - INIT
 * ======================================
 *
 * This file generate a Javascript file that can be loaded like a normal script with the code below:
 * <script id="sb-php-init" src="www.your-site.com/supportboard/php/sb.php"></script>
 * Documentation at board.support/docs/php
 */

/**
 * --------------------------------------
 * VARIABLES AND COMPONENTS LOADING
 * --------------------------------------
 */
require("gettext/autoloader.php");
use Gettext\Translations;
$atts = array("type" => "chat","lang" => "");
$translations_file;
if (isset($_GET['type'])) $atts["type"] = $_GET['type'];
if (isset($_GET['lang'])) $atts["lang"] = $_GET['lang'];
include("../include/functions.php");
include("../php/functions.php");
include_once("config.php");
define("SB_PLUGIN_URL", sb_get_plugin_url());
$agents_arr = json_decode(get_option("sb-agents-arr"), true);

/**
 * --------------------------------------
 * TRANSLATION FUNCTIONS
 * --------------------------------------
 */
function _e($string, $domain, $language = "") {
    echo __($string, $domain, $language);
    return false;
}
function __($string = "", $domain, $language = "") {
    if (isset($string)) {
        if ($language == "" || !isset($language)) {
            global $atts;
            if ($atts["lang"] != "") {
                $language = $atts["lang"];
            } else {
                global $sb_config;
                if ($sb_config["auto-multilingual"]) {
                    $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                    $language = $language . "_" . strtoupper($language);
                }
            }
        }
        if (isset($language) && $language != "" && $language != "en_EN") {
            global $translations_file;
            $result = "";
            if (isset($domain)) $domain = $domain . "-";
            if (!isset($translations_file)) {
                if (file_exists('../lang/' . $domain . $language . '.po')) {
                    $translations_file = Translations::fromPoFile('../lang/' . $domain . $language . '.po');
                }
            }
            if (isset($translations_file)) {
                $translation = $translations_file->find(null, $string);
                $result = $translation->getTranslation();
            }
            if ($result != "") {
                return $result;
            }
        }
    }
    return $string;
}


/**
 * --------------------------------------
 * JAVASCRIPT GENERATION CODE
 * --------------------------------------
 */
ob_start();
session_start();

include("config.php");

$isLogged = false;
if (isset($_SESSION['sb-login']) && !empty($_SESSION['sb-login'])) {
    $session = encryptor("decrypt", $_SESSION['sb-login']);
    if (strpos($session,"sb-logged-in") > -1) {
        $isLogged = true;
    }
}
if ($isLogged || !sb_get($sb_config, "desk-login", true) || ($atts["type"] == "chat" && (sb_get($sb_config,"chat-visibility") == "all" || sb_get($sb_config,"chat-visibility") == ""))) {
    include("../board.php");
} else {
    include("../login.php");
}

$sb_output = ob_get_contents();
$sb_output = preg_replace( "/\r|\n/", "", $sb_output);
$sb_output = str_replace( "'", "\'", $sb_output);

ob_clean();

$css = sb_set_css();
$css = preg_replace( "/\r|\n/", "", $css);

echo "'use strict';(function ($) {
    var HTML_EMBED = '" . $sb_output . "';
    var URL = '';";
if (isset($_GET['target'])) {
    echo "var sb_target = '" . $_GET['target'] . "';\n";
} else {
    echo "var sb_target = '';\n";
}
echo '$(document).ready(function () {
        //Load resources
        URL = $("#sb-php-init").attr("src");
        URL = URL.substr(0, URL.lastIndexOf("/php"));
        if (sb_target != "" && $("." + sb_target + ",#" + sb_target).length) {
          $("." + sb_target + ",#" + sb_target).first().append(HTML_EMBED);
        } else {
          $("body").append(HTML_EMBED);
        }
        $("#sb-main").addClass("sb-php");
        $("head").append(\'<link rel="stylesheet" type="text/css" href="\' + URL + \'/include/main.css" />\');';
if ($css != "") echo "$('head').append('<style>" . $css . "</style>');\n";
if (!$sb_config["font-disable"]) echo '$(\'head\').append(\'<link href="https://fonts.googleapis.com/css?family=Raleway:500,600" rel="stylesheet">\');';
echo "var imported = document.createElement('script'); imported.src = URL + '/include/main.js'; document.head.appendChild(imported);}); }(jQuery))";
?>
