<?php

/**
 * Plugin Name: Grafix Disable Comments
 * Version: 1.0.0
 * Plugin URI: https://www.grafix.com.tr/
 * Description: This plugin disables WordPress comments.
 * Author: Tanju Yıldız
 * Author URI: https://www.grafix.com.tr/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: grafix-disable-comments
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Tanju Yıldız
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-grafix-disable-comments.php';

new Grafix_Disable_Comments();
