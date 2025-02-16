<?php

/**
 * 
 * Plugin Name: CRM Ticketing
 * Plugin URI: https://github.com/atta3554
 * Description: a comprehensive plugin for integrated ticket management
 * Version: 1.0.0
 * author: ata ashrafi
 * author URI: mailto:ata.ashrafi3554@gmail.com
 * Text Domain: crm-ticket-manager
 * Domain Path: /languages
 * 
*/

if(!defined('ABSPATH')) exit; // exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// include main plugin classes
require_once plugin_dir_path(__FILE__) . 'inc/crm-ticket-manager.php';

function CTM_init() {
    return CRMTicketManager::instance();
}

CTM_init();

register_activation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});