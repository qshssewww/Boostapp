<?php
header('Access-Control-Allow-Origin: *');

/**
 * ======================================
 * SUPPORT BOARD - CORE FILE
 * ======================================
 *
 * This file is a hub that load both admin and fron-end sides functions
 */
include("../include/functions.php");
define("SB_PLUGIN_URL", sb_get_plugin_url());

include("functions.php");
include("../include/ajax.php");

?>
