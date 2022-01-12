<?php
/**
 * Plugin Name:         Rimarx Top Commentators Widget
 * Description:         Add WordPress user top comments widget
 * Author:              Rimarx
 * Version:             1.0
 * Requires at least:   5
 * Requires PHP:        5.4
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/class-mynew-widget.php';

function register_widget_rmx() {
    register_widget( 'Mynew_widget' );
}
add_action( 'widgets_init', 'register_widget_rmx' );