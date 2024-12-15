<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( get_parent_theme_file_uri( 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

function enqueue_ajax_scripts() {
    wp_enqueue_script('your-script-handle', get_template_directory_uri() . '/js/app.js', ['jquery'], null, true);
    wp_localize_script('your-script-handle', 'ajaxData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('video_timestamp_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_scripts');


function create_video_timestamps_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'video_timestamps';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        userid varchar(36) NOT NULL,
        event varchar(50) NOT NULL,
        urlref varchar(255) NOT NULL,
        browser varchar(255) NOT NULL,
		device VARCHAR(50),
		ip_address VARCHAR(50),
        timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_switch_theme', 'create_video_timestamps_table');


function delete_video_timestamps_table() {
    global $wpdb;

    // Specify the table name (use the appropriate table name you want to delete)
    $table_name = $wpdb->prefix . 'video_timestamps';

    // SQL query to drop the table
    $sql = "DROP TABLE IF EXISTS $table_name";

    // Execute the SQL query to delete the table
    $wpdb->query($sql);

    // Optional: log or output success message
    if ($wpdb->last_error) {
        error_log('Error deleting table: ' . $wpdb->last_error);
    } else {
        error_log('Table deleted successfully.');
    }
}

function handle_video_timestamp_request() {
	// delete_video_timestamps_table();
	// create_video_timestamps_table();

	global $wpdb;

    // Validate nonce
    check_ajax_referer('video_timestamp_nonce', 'nonce');

	// Get POST data
	$user_id = sanitize_text_field($_POST['user_id']); // Should be UUID
	$event = sanitize_text_field($_POST['event']);
	$url = sanitize_text_field($_POST['url']);
	$timestamp = current_time('mysql');

	// Check if the UUID is present
	if (!$user_id) {
		wp_send_json_error(array('message' => 'User ID is missing'));
		return;
	}

	// Get browser info from HTTP_USER_AGENT
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$browser_info = 'Unknown Browser';
	$device_info = 'Unknown Device';

	// Detect Browser
	if (strpos($user_agent, 'Firefox') !== false) {
		$browser_info = 'Firefox';
	} elseif (strpos($user_agent, 'Chrome') !== false && strpos($user_agent, 'Edg') === false) {
		$browser_info = 'Chrome';
	} elseif (strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false) {
		$browser_info = 'Safari';
	} elseif (strpos($user_agent, 'Edg') !== false) {
		$browser_info = 'Edge';
	} elseif (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
		$browser_info = 'Internet Explorer';
	}

	// Detect Device
	if (strpos($user_agent, 'Mobile') !== false) {
		$device_info = 'Mobile';
	} elseif (strpos($user_agent, 'Tablet') !== false) {
		$device_info = 'Tablet';
	} elseif (strpos($user_agent, 'Windows') !== false || strpos($user_agent, 'Macintosh') !== false) {
		$device_info = 'Desktop';
	}

	// Get User IP
	$user_ip = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$user_ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$user_ip = $_SERVER['REMOTE_ADDR'];
	}

	// Insert into database
	$table_name = $wpdb->prefix . 'video_timestamps'; // Replace with your table name
	$wpdb->insert($table_name, [
		'userid' => $user_id,
		'event' => $event,
		'timestamp' => $timestamp,
		'urlref' => $url,
		'browser' => $browser_info,
		'device' => $device_info,
		'ip_address' => $user_ip,
	]);

     // Fetch the last inserted record
	$last_inserted = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE userid = %d ORDER BY timestamp DESC LIMIT 1",
            $user_id
        )
    );

    // Check if the data is retrieved successfully
    if ($last_inserted) {
        // Send success response with the message from the database
        wp_send_json_success( array( 'message' => 'Timestamp saved successfully', 'data' => $last_inserted,
		'userId' => $user_id,
		'browser info' => $browser_info ) );
    } else {
        // Send error if no data is found
        wp_send_json_error( array( 'message' => 'Failed to save timestamp' ) );
    }
}

add_action('wp_ajax_save_video_timestamp', 'handle_video_timestamp_request');
add_action('wp_ajax_nopriv_save_video_timestamp', 'handle_video_timestamp_request');

function handle_get_video_timestamps_request() {
    global $wpdb;

    // Validate nonce (optional but recommended for security)
    // check_ajax_referer('video_timestamp_nonce', 'nonce');

    // Query to get all records from the video_timestamps table
    $table_name = $wpdb->prefix . 'video_timestamps';
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");

    // Check if any records exist
    if ($results) {
        wp_send_json_success(array('message' => 'Records retrieved successfully', 'data' => $results));
    } else {
        wp_send_json_error(array('message' => 'No records found'));
    }
}

add_action('wp_ajax_get_video_timestamps', 'handle_get_video_timestamps_request'); // For logged-in users
add_action('wp_ajax_nopriv_get_video_timestamps', 'handle_get_video_timestamps_request'); // For logged-out users
