<?php
/**
 * @package Block Management Online
 * @author Esscotti Ltd
 * @version 1.0.2
 */
/*
Plugin Name: Block Management Online
Plugin URI: http://www.blockmanagementonline.com/
Description: Block Management Online
Author: Esscotti Ltd
Version: 1.0.2
Author URI: http://www.blockmanagementonline.com/

Copyright (c) 2013 Esscotti Ltd, All Rights Reserved
*/
require_once('class.bmo.php');
require_once('class.bmo_pluginmanager.php');
require_once('class.bmo_shortcodes.php');
require_once('class.bmo_options.php');
require_once('class.bmo_session.php');

// bmo does the main work including rendering...
$bmo 				= new bmo();

// the plugin manager deals with header and footer inserts, and with issues around installing, activating and de-activating the plugin..
$bmo_pluginmanager = new bmo_pluginmanager();

// the shortcodes class deals with shortcodes...
$bmo_shortcodes 	= new bmo_shortcodes();

// deals with options and admin option pages...
$bmo_options 		= new bmo_options();

// deals with login, logout, sessions and authentication...
$bmo_session 		= new bmo_session();


register_activation_hook(__FILE__, array('bmo_pluginmanager', 'install_plugin'));


?>