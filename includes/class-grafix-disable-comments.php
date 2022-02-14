<?php

class Grafix_Disable_Comments {
	public function __construct() {
		add_filter( 'comments_open', '__return_false' );
		add_filter( 'pings_open', '__return_false' );
		add_filter( 'comments_array', '__return_empty_array' );
		add_action( 'admin_init', array( $this, 'redirect_comments_pages' ) );
		add_action( 'admin_menu', array( $this, 'remove_comments_menu_items' ) );
		add_action( 'admin_init', array( $this, 'remove_recent_comments_widget' ) );
		add_action( 'widgets_init', array( $this, 'remove_recent_comments_styles' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_comments_admin_bar' ) );
		add_action( 'admin_init', array( $this, 'remove_comments_support' ) );
		add_action( 'admin_init', array( $this, 'update_comment_options' ) );
		add_action( 'admin_init', array( $this, 'delete_all_comments' ) );
	}

	// Redirect all pages related to comments to the dashboard.
	public function redirect_comments_pages() {
		global $pagenow;

		if ( $pagenow === 'edit-comments.php' || $pagenow === 'comment.php' ||  $pagenow === 'options-discussion.php' ) {
			wp_redirect( admin_url() );
		exit;
		}
	}

	// Remove comments menu items from admin
	public function remove_comments_menu_items() {
		remove_menu_page( 'edit-comments.php' );
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	}

	// Remove Recent Comments Widget from Dashboard
	public function remove_recent_comments_widget() {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}

	// Remove Recent Comments Widget Styles from wp_head()
	public function remove_recent_comments_styles() {
		global $wp_widget_factory;
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}

	// Remove Comments from WordPress Admin Bar
	public function remove_comments_admin_bar() {
		global $wp_admin_bar;
        $wp_admin_bar->remove_menu( 'comments' );
	}

	// Remove Comments Support from all Post Types
	public function remove_comments_support() {
		$post_types = get_post_types();

		foreach ( $post_types as $post_type ) {
			// Remove the comments and comments status meta box.
			remove_meta_box( 'commentstatusdiv', $post_type, 'normal' );
			remove_meta_box( 'commentsdiv', $post_type, 'normal' );

			// Remove the trackbacks meta box.
			remove_meta_box( 'trackbacksdiv', $post_type, 'normal' );

			// Remove all comments/trackbacks from tables.
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );

			// Remove Comments Column from Admin for all Post Types
			add_filter( 'manage_{$post_type}_columns', function($defaults) {
				unset($defaults['comments']);
				return $defaults;
			} );
		}
	}

	// Update Options for Comments
	public function update_comment_options() {
		$comment_options = array(
			'close_comments_days_old'      => 5,
			'close_comments_for_old_posts' => 1,
			'comment_moderation'           => 1,
			'comment_registration'         => 1,
			'default_comment_status'       => 'closed',
			'default_ping_status'          => 'closed',
			'default_pingback_flag'        => 0,
		);

		foreach ( $comment_options as $key => $value ) {
			update_option( $key, $value );
		}
	}

	// Delete All Comments and Pings
	public function delete_all_comments() {
		$comments_count = wp_count_comments()->total_comments;

		if ( $comments_count > 0 ) {
			global $wpdb;
			$wpdb->query("TRUNCATE $wpdb->comments");
			$wpdb->query("TRUNCATE $wpdb->commentmeta");
		}
	}
}
