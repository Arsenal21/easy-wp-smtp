<?php

class EasyWPSMTP_AddonsHelper {

    var $addon	 = null;
    var $opts	 = array();

    function __construct( $addon ) {
	$this->addon	 = $addon;
	$default_opts	 = array();
	if ( isset( $this->addon->default_opts ) ) {
	    $default_opts = $this->addon->default_opts;
	}
	$this->opts_name = 'ewpsmtp_addon_' . $this->addon->SETTINGS_TAB_NAME;
	$this->opts	 = get_option( $this->opts_name, $default_opts );
    }

    function init_tasks() {
	$this->load_text_domain();
	if ( is_admin() ) {
	    $this->add_settings_link();
	    if ( ! isset( $this->addon->no_settings_tab ) ) {
		add_action( 'easy_wp_smtp_admin_settings_tabs_menu', array( $this, 'add_settings_tab' ) );
	    }
	    $this->check_updates();
	    add_action( 'admin_init', array( $this, 'admin_init' ) );
	}
    }

    function admin_init() {
	$settings_saved = filter_input( INPUT_POST, 'ewpsmtp_submit_' . $this->addon->SETTINGS_TAB_NAME, FILTER_SANITIZE_STRING );
	if ( ! empty( $settings_saved ) ) {
	    $post_opts	 = filter_input( INPUT_POST, 'ewpsmtp_opts_' . $this->addon->SETTINGS_TAB_NAME, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	    $this->post_opts = $post_opts;
	    do_action( 'easy_wp_smtp_' . $this->addon->SETTINGS_TAB_NAME . '_settings_saved' );
	}
    }

    function log( $msg, $success = true ) {
//	if ( method_exists( 'ASP_Debug_Logger', 'log' ) ) {
//	    ASP_Debug_Logger::log( $msg, $success, $this->addon->ADDON_SHORT_NAME );
//	}
    }

    function check_updates() {
//	$lib_path = plugin_dir_path( $this->addon->file ) . 'lib/plugin-update-checker/plugin-update-checker.php';
//	if ( file_exists( $lib_path ) ) {
//	    include_once($lib_path);
//	    $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
//	    'https://s-plugins.com/updates/?action=get_metadata&slug=' . $this->addon->SLUG, $this->addon->file, $this->addon->SLUG );
//	}
    }

    function check_ver() {
//	if ( version_compare( WP_ASP_PLUGIN_VERSION, $this->addon->MIN_ASP_VER ) < 0 ) {
//	    add_action( 'admin_notices', array( $this, 'display_min_version_error' ) );
//	    return false;
//	}
	return true;
    }

    function add_settings_link() {
	add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );
    }

    function add_settings_tab() {
	if ( isset( $this->addon->tab_notify ) ) {
	    $notify = sprintf( '<span class="ewpsmtp-tab-notify">%d</span>', $this->addon->tab_notify );
	}
	printf( '<a href = "#%1$s" data-tab-name = "%1$s" class = "nav-tab">%2$s %3$s</a>', $this->addon->SETTINGS_TAB_NAME, $this->addon->ADDON_SHORT_NAME, $notify );
    }

    function load_text_domain() {
	if ( ! empty( $this->addon->file ) && ! empty( $this->addon->textdomain ) ) {
	    load_plugin_textdomain( $this->addon->textdomain, FALSE, dirname( plugin_basename( $this->addon->file ) ) . '/languages/' );
	}
    }

    function settings_link( $links, $file ) {
	if ( $file === plugin_basename( $this->addon->file ) ) {
	    $settings_link = sprintf( '<a href="options-general.php?page=swpsmtp_settings#%s">%s</a>', $this->addon->SETTINGS_TAB_NAME, __( 'Settings', 'easy-wp-smtp' ) );
	    array_unshift( $links, $settings_link );
	}
	return $links;
    }

    function display_min_version_error() {
	$class	 = 'notice notice-error';
	$message = sprintf( __( '%s requires Easy WP SMTP plugin minimum version to be %s (you have version %s installed). Please update Easy WP SMTP plugin.', 'easy-wp-smtp' ), $this->addon->ADDON_FULL_NAME, $this->addon->MIN_ASP_VER, WP_ASP_PLUGIN_VERSION );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    function get_option( $opt, $default = false ) {
	if ( isset( $this->opts[ $opt ] ) ) {
	    return $this->opts[ $opt ];
	}
	return $default;
    }

    function get_post_option( $opt, $filter = FILTER_UNSAFE_RAW ) {
	$val = filter_var( $this->post_opts[ $opt ], $filter );
	return $val;
    }

    function set_option( $opt, $value ) {
	$this->opts[ $opt ] = $value;
	update_option( $this->opts_name, $this->opts );
	return true;
    }

    function is_checked( $opt ) {
	if ( $this->get_option( $opt ) ) {
	    echo ' checked';
	}
    }

    function field_name( $opt ) {
	echo 'ewpsmtp_opts_' . $this->addon->SETTINGS_TAB_NAME . '[' . $opt . ']';
    }

    function submit_btn() {
	submit_button( __( 'Save Settings', 'easy-wp-smtp' ), 'primary', 'ewpsmtp_submit_' . $this->addon->SETTINGS_TAB_NAME );
    }

}
