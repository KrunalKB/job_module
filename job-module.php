<?php
/**
 * Plugin Name:       Job module
 * Description:       This is job module for client and contractor.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Author:            Krunal Bhimajiyani
 * Author URI:        https://github.com/KrunalKB
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

 if (!defined('ABSPATH')) {
     exit;
 }

class jbm_controller
{
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this,'jbm_activate'));
    }

    public function jbm_activate()
    {
    }
}

$jbm_controller = new jbm_controller();
