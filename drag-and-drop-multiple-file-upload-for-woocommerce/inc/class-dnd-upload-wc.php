<?php
	/**
	* @Description : Plugin main core
	* @Package : Drag & Drop Multiple File Upload - WooCommerce
	* @Author : CodeDropz
	*/

	if ( ! defined( 'ABSPATH' ) || ! defined('DNDMFU_WC') ) {
		exit;
	}

	/**
	* Begin : begin plugin initialization
	*/
	class DNDMFU_WC_MAIN {

		private static $instance = null;

		// Default upload options
		public $_options = array(
			'save_to_media'				=>	false,
			'automatic_file_deletion'	=>	false,
			'folder_option'				=>	null,
			'tmp_folder'				=>	'tmp_uploads',
			'upload_dir'				=>	null,
			'preview_image'				=>	'',
			'zip_files'					=>	false
		);

		// default error message
		public $error_message = array();

		// Upload dir - default from wp
		public $wp_upload_dir = array();

		/**
		* Creates or returns an instance of this class.
		*
		* @return  Init A single instance of this class.
		*/
		public static function get_instance() {
			if( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		* Load and initialize plugin
		*/
		private function __construct() {
			$this->init_actions();
			$this->init();
			$this->hooks();
			$this->filters();
		}

		/**
		 * Plugin init actions (text domain and default error message)
		 */
		public function init_actions() {
			add_action( 'init', array( $this, 'text_domain' ), 10 );
			add_action( 'init', array( $this, 'setErrorMessages' ), 20 );
		}

		/**
		* Plugin init
		*/
		public function init() {

			// Includes functions / helpers
			$this->includes();

            // Temporary fix
            $this->prefix_option();

			// Wordpress upload directory
			$this->wp_upload_dir = apply_filters( 'dndmfu_wc_upload_dir', wp_upload_dir() );

			// Base upload URL
			$base_url = $this->wp_upload_dir['baseurl'];

			// Create upload folder where files being stored.
			if( defined('DNDMFU_WC_PATH') ) {

				// concat path and defined folder dir
				$wp_dnd_wc_folder = trailingslashit( wp_normalize_path( $this->wp_upload_dir['basedir'] ) ) . DNDMFU_WC_PATH;

				// Format correct slashes
				$base_url = preg_replace( '/\\\\/', '/', $base_url );

				// Create dir
				if( ! is_dir( $wp_dnd_wc_folder ) ) {
					wp_mkdir_p( $wp_dnd_wc_folder );

					// Generate .htaccess file`
					$htaccess_file = path_join( $wp_dnd_wc_folder, '.htaccess' );

					if ( ! file_exists( $htaccess_file ) ) {
						if ( $handle = fopen( $htaccess_file, 'w' ) ) {
							fwrite( $handle, "Options -Indexes \n <Files *.php> \n deny from all \n </Files>" );
							fclose( $handle );
						}
					}
				}

				// override default wordpress basedir and baseurl
				$this->wp_upload_dir['basedir'] = apply_filters( 'dndmfu_wc_base_dir', $wp_dnd_wc_folder );
				$this->wp_upload_dir['baseurl'] = apply_filters( 'dndmfu_wc_base_url', path_join( $base_url , DNDMFU_WC_PATH ) );
			}

			// Upload DIR
			$this->_options['upload_dir'] = $this->wp_upload_dir['basedir'];

		}

		/**
		* Includes custom files
		*/
		public function includes() {
			include( DNDMFU_WC_DIR .'/inc/functions/functions-dnd-upload-wc.php' );
			include( DNDMFU_WC_DIR .'/inc/functions/functions-dnd-upload-custom.php' );
		}

		/**
		* Begin : begin plugin hooks
		*/
		public function hooks() {

			// List of available hooks ( Mostly Woo )
			$hooks = array(

                // nonce
                'wp_ajax_wc_upload_nonce'				        =>	array( 'cb' => [ $this, 'check_nonce'], 10),
                'wp_ajax_nopriv_wc_upload_nonce'			    =>	array( 'cb' => [ $this, 'check_nonce'], 10),

				// woo commerce hooks - admin
				'woocommerce_product_data_tabs'					=>	array( 'cb' => 'dndmfu_wc_product_tabs', 10, 1),
				'woocommerce_product_data_panels'				=>	array( 'cb' => 'dndmfu_wc_product_panels', 10, 1 ),
				'woocommerce_process_product_meta'				=>	array( 'cb' => 'dndmfu_wc_save_fields', 10, 1 ),

				// cart
				'woocommerce_add_cart_item_data'				=>	array( 'cb' => 'dndmfu_wc_add_cart_data', 10, 3 ),
				'woocommerce_get_item_data'						=>	array( 'cb' => 'dndmfu_wc_get_cart_item', 10, 2 ),

				// Order
				'woocommerce_checkout_create_order_line_item'	=>	array( 'cb' => 'dndmfu_wc_order_item', 10,4 ),
				'woocommerce_order_item_name'					=>	array( 'cb' => 'dndmfu_wc_order_item_name', 10, 2),

				// ajax upload
				'wp_ajax_dnd_codedropz_upload_wc'					=>	array( 'cb' => [ $this,'upload' ] ),
				'wp_ajax_nopriv_dnd_codedropz_upload_wc'			=>	array( 'cb' => [ $this,'upload' ] ),

				// ajax delete
				'wp_ajax_nopriv_dnd_codedropz_upload_delete_wc'	=>	array( 'cb' => [ $this,'delete_file' ] ),
				'wp_ajax_dnd_codedropz_upload_delete_wc'		=>	array( 'cb' => [ $this,'delete_file' ] ),

				// Remove files - from deleted cart contents
				'template_redirect'								=>	array( 'cb' => 'dndmfu_wc_remove_files_from_contents' ),

				// Cron - remove files inside /tmp_uploads dir
				'wp_dnd_wc_daily_cron'							=>	array( 'cb'	=> 'dndmfu_wc_auto_remove_files', 20, 3 ),
			);

			// Plugin Hooks
			$hooks['wp_enqueue_scripts'] = array( 'cb' => [$this, 'enqueue'] );

			// Get uploader option
			$show_uploader_in = get_option('wcf_show_in_dnd_file_uploader_in', true);

			// Get which to display file upload ( default: Single Page, Before Add to Cart )
			if( $show_uploader_in == 'single-page' ) {
				$file_upload = get_option('wcf_show_in_dnd_file_upload_after') ? trim( get_option('wcf_show_in_dnd_file_upload_after') ) : 'woocommerce_before_add_to_cart_button';
				$hooks[ $file_upload ] = 'dndmfu_wc_display_file_upload';
			}

			// Loop all hooks & excecute
			$this->process_hook_filters( $hooks );
		}

		/**
		* Begin : Custom filters
		*/
		public function filters() {

			// Get plugin basename
			$plugin = plugin_basename( DNDMFU_WC_DIR ) .'/drag-and-drop-file-uploads-wc.php';

			// Array - custom filters
			$filters = array(
				'woocommerce_add_to_cart_validation' 	=>	array( 'cb' => 'dndmfu_wc_cart_validation', 10,4 ),
				'woocommerce_update_cart_validation'	=>	array( 'cb' => 'dndmfu_wc_update_cart_validation', 10,4 ),
				'plugin_action_links_'. $plugin         =>  array( 'cb' => [ $this, 'dndmfu_wc_settings'], 10, 1 ),
			);

			// Loop all filters
			$this->process_hook_filters( $filters, true );
		}

		/**
		* Run - hooks & filters
		*/
		protected function process_hook_filters( $hooks, $filter = false ) {

			if( ! $hooks ) {
				return false;
			}

			// Loop all hooks excecute
			foreach( $hooks as $hook_name => $callback ) {

				$prio 		= ( is_array( $callback ) && isset( $callback[0] ) ) ? $callback[0] : 10;
				$param 		= ( is_array( $callback ) && isset( $callback[1] ) ) ? $callback[1] : null;
				$callable 	= ( is_array( $callback ) && isset( $callback['cb'] ) ) ? $callback['cb'] : $callback;

				if( $filter ) {
					add_filter( $hook_name, $callable, $prio, $param );
				}else {
					add_action( $hook_name, $callable, $prio, $param );
				}

			}
		}

		/**
		* Security helpers - token ownership & throttling
		*/
		private function get_token() {
			if( empty( $_POST['token'] ) ) {
				return '';
			}
			$token = sanitize_text_field( wp_unslash( $_POST['token'] ) );
			return preg_match( '/^[A-Za-z0-9]{32}$/', $token ) ? $token : '';
		}

		// Get user IP address
		private function get_ip() {
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
			return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
		}

		// Check if nonce use 60 times in a span of 1minute (Per user IP address)
		private function is_nonce_throttled( $key, $max = 60, $window = 60 ) {
			$transient_key = 'dnd_wc_rl_' . md5( $key );
			$hits          = (int) get_transient( $transient_key );

			if( $hits >= $max ) {
				return true;
			}

			set_transient( $transient_key, $hits + 1, $window );
			return false;
		}

		/**
		* Every upload session gets its own tmp subfolder, named after its token.
		* A file only belongs to a token if it lives inside that token's folder -
		* no per-file transient row needed, and the filename itself is never touched.
		*/
		private function get_token_upload_dir( $token ) {
			return trailingslashit( $this->_options['tmp_folder'] ) . md5( $token );
		}

		/**
		* One state row per token - upload count + throttle windows, instead of separate transients.
		*/
		private function get_token_state( $token ) {
			$defaults = array(
				'file_count'           => 0,
				'upload_hits'          => 0,
				'upload_window_start'  => 0,
				'delete_hits'          => 0,
				'delete_window_start'  => 0,
			);
			$state = get_transient( 'dnd_wc_s_' . md5( $token ) );
			return is_array( $state ) ? wp_parse_args( $state, $defaults ) : $defaults;
		}

		/**
		 * Save new transient by token with new state data.
		 */
		private function save_token_state( $token, $state ) {
			set_transient( 'dnd_wc_s_' . md5( $token ), $state, DAY_IN_SECONDS );
		}

		/**
		* Fixed-window throttle against a shared token state array.
		*/
		private function token_throttled( &$state, $hits_field, $window_field, $max, $window ) {
			$now = time();
			if( empty( $state[ $window_field ] ) || ( $now - $state[ $window_field ] ) >= $window ) {
				$state[ $window_field ] = $now;
				$state[ $hits_field ]   = 0;
			}
			if( $state[ $hits_field ] >= $max ) {
				return true;
			}
			$state[ $hits_field ]++;
			return false;
		}

        /**
        * Check for nonce
        */
        public function check_nonce(){

            // Throttle so bots can't farm tokens and bloat wp_options.
            if( $this->is_nonce_throttled( 'nonce_' . $this->get_ip(), 30, MINUTE_IN_SECONDS ) ) {
                wp_send_json_error( 'Too many requests.', 429 );
            }

            wp_send_json_success( array(
                'nonce' =>  wp_create_nonce( 'dnd_wc_ajax_upload' ),
                'token' =>  wp_generate_password( 32, false, false ),
            ) );
        }

		/**
		* Load plugin text-domain
		*/

		public function text_domain() {
			load_plugin_textdomain( 'dnd-file-upload-wc', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
		}

		/**
		 * Set default error message
		 */
		public function setErrorMessages() {
			$this->error_message = array(
				'server_limit'		=>	__('The uploaded file exceeds the maximum upload size of your server.','dnd-file-upload-wc'),
				'failed_upload'		=>	__('Uploading a file fails for any reason','dnd-file-upload-wc'),
				'large_file'		=>	__('Uploaded file is too large','dnd-file-upload-wc'),
				'invalid_type'		=>	__('Uploaded file is not allowed for file type','dnd-file-upload-wc'),
				'maxNumFiles'		=>	__('You have reached the maximum number of files ( Only %s files allowed )','dnd-file-upload-wc'),
				'maxTotalSize'		=>	__('The total file(s) size exceeding the max size limit of %s.','dnd-file-upload-wc'),
				'maxUploadLimit'	=>	__('Note : Some of the files could not be uploaded ( Only %s files allowed )','dnd-file-upload-wc'),
				'minFileUpload'		=>	__('Please upload atleast %s file(s).','dnd-file-upload-wc')
			);
		}

		/**
		* Add plugin settings
		*/
		public function dndmfu_wc_settings( $actions ) {
			$upload_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=dnd-wc-file-uploads' ) . '">Settings</a>',
				'<a href="https://www.codedropz.com/woocommerce-drag-drop-multiple-file-upload/" target="_blank" style="color: green; font-weight: 700;">Get Pro</a>',
			);
            $actions = array_merge( $upload_links, $actions );
            return $actions;
		}

        /**
		* Temporary fix for naming conflict with option.
		*/
        public function prefix_option() {

            $settings = array(
                'drag_n_drop_text','drag_n_drop_separator','drag_n_drop_browse_text','drag_n_drop_default_label','drag_n_drop_error_server_limit','drag_n_drop_error_failed_to_upload','drag_n_drop_error_files_too_large','drag_n_drop_error_invalid_file','drag_n_drop_error_max_file','drag_n_drop_error_min_file','drag_n_drop_required','drag_n_drop_disable','drag_n_drop_field_name','drag_n_drop_file_size_limit','drag_n_drop_max_file_upload','drag_n_drop_min_file_upload','drag_n_drop_support_file_upload','show_in_dnd_file_uploader_in','show_in_dnd_file_upload_after','drag_n_drop_error_max_number_of_files'
            );

            foreach( $settings as $option ) {
                $prefix_opt = 'wcf_'. $option;
                if( get_option( $option ) && ! get_option($prefix_opt) ) {
                    $old_data = get_option( $option );
                    update_option( $prefix_opt, $old_data );
                }
            }
        }

		/**
		* Begin : Load js and css
		*/
		public function enqueue() {

			// Get plugin version
			$version = DNDMFU_WC_VERSION;

			// enqueue script
			//if( is_product() ) {
				wp_enqueue_script( 'dndmfu-free-uploader', plugins_url('/assets/js/codedropz-uploader-min.js',dirname(__FILE__)), array('jquery'), $version, true );
				wp_enqueue_script( 'dndmfu-wc-free', plugins_url('/assets/js/dnd-upload-wc.js', dirname(__FILE__)), array('jquery','dndmfu-free-uploader'), $version, true);
			//}

            // Get current language
            $lang = dndmfu_wc_lang();

			//  registered script with data for a JavaScript variable.
			wp_localize_script( 'dndmfu-wc-free', 'dnd_wc_uploader',
				array(
					'ajax_url' 				=> admin_url( 'admin-ajax.php' ),
					'nonce'					=>	wp_create_nonce('dnd_wc_ajax_upload'),
					'drag_n_drop_upload' 	=> array(
						'text'				=>	( get_option('wcf_drag_n_drop_text'.$lang) ? esc_html( get_option('wcf_drag_n_drop_text'.$lang) ) : __('Drag & Drop Files Here','dnd-file-upload-wc') ),
						'or_separator'		=>	( get_option('wcf_drag_n_drop_separator'.$lang) ? esc_html( get_option('wcf_drag_n_drop_separator'.$lang) ) : __('or','dnd-file-upload-wc') ),
						'browse'			=>	( get_option('wcf_drag_n_drop_browse_text'.$lang) ? esc_html( get_option('wcf_drag_n_drop_browse_text'.$lang) ) : __('Browse Files','dnd-file-upload-wc') ),
						'server_max_error'	=>	( get_option('wcf_drag_n_drop_error_server_limit'.$lang) ? get_option('wcf_drag_n_drop_error_server_limit'.$lang) : $this->get_error_msg('server_limit') ),
						'large_file'		=>	( get_option('wcf_drag_n_drop_error_files_too_large'.$lang) ? get_option('wcf_drag_n_drop_error_files_too_large'.$lang) : $this->get_error_msg('large_file') ),
						'inavalid_type'		=>	( get_option('wcf_drag_n_drop_error_invalid_file'.$lang) ? get_option('wcf_drag_n_drop_error_invalid_file'.$lang) : $this->get_error_msg('invalid_type') ),
						'minimum_file'		=>	( get_option('wcf_drag_n_drop_error_min_file'.$lang) ? get_option('wcf_drag_n_drop_error_min_file'.$lang) : $this->get_error_msg('minFileUpload') ),
						'maxNumFiles'		=>	( get_option('wcf_drag_n_drop_error_max_number_of_files'.$lang) ? get_option('wcf_drag_n_drop_error_max_number_of_files'.$lang) : $this->get_error_msg('maxNumFiles') ),
						'maxFileLimit'		=>	( get_option('wcf_drag_n_drop_error_max_file'.$lang) ? get_option('wcf_drag_n_drop_error_max_file'.$lang) : $this->get_error_msg('maxUploadLimit') ),
					),
                    'uploader_text'        => array(
                        'of'        =>  get_option('wcf_drag_n_drop_of_text'.$lang) ? get_option('wcf_drag_n_drop_of_text'.$lang) : __('of','dnd-file-upload-wc'),
                        'delete'    =>  get_option('wcf_drag_n_drop_deleting_text'.$lang) ? get_option('wcf_drag_n_drop_deleting_text'.$lang) : __('Deleting...','dnd-file-upload-wc'),
                        'remove'    =>  get_option('wcf_drag_n_drop_remove_text'.$lang) ? get_option('wcf_drag_n_drop_remove_text'.$lang) : __('Remove','dnd-file-upload-wc'),
                    ),
				)
			);

			// enque style
			wp_enqueue_style( 'dndmfu-wc', plugins_url ('/assets/css/dnd-upload-wc.css', dirname(__FILE__) ), '', $version );
		}

		/**
		* Default error message
		*/

		public function get_error_msg( $error_key ) {
			// return error message based on $error_key request
			if( isset( $this->error_message[$error_key] ) ) {
				return $this->error_message[$error_key];
			}
			return false;
		}

		/**
		* Begin process ajax upload.
		*/
		public function upload() {

            if( ! check_ajax_referer( 'dnd_wc_ajax_upload', 'security', false ) ){
                wp_send_json_error('The security nonce is invalid or expired');
            }

			// Require ownership token
			$token = $this->get_token();
			if( ! $token ) {
				wp_send_json_error('Missing upload session. Please refresh the page.');
			}

			// Per-token file cap - @newJuly 2026
			$max_files = (int) get_option('wcf_drag_n_drop_max_file_upload');
			$max_files = $max_files > 0 ? $max_files : 10;
			$state     = $this->get_token_state( $token ); // get_transient [file_count, upload_hits]

			if( $state['file_count'] >= $max_files ) {
				wp_send_json_error( sprintf( __('You have reached the maximum number of files ( Only %s files allowed )','dnd-file-upload-wc'), $max_files ) );
			}

			// max 60 Uploads/min per token (true if hit max)
			$throttled = $this->token_throttled( $state, 'upload_hits', 'upload_window_start', 60, MINUTE_IN_SECONDS );

			// Save token in transient - initial
			$this->save_token_state( $token, $state );

			// If throttled hits max 60 uploads/minuate return an error.
			if( $throttled ) {
				wp_send_json_error('Too many uploads. Please slow down.', 429 );
			}

			// input type file 'name'
			$name = 'dnd-wc-upload-file';

			// Guard missing entry; do NOT wc_clean() the whole array (it mangles tmp_name)
			if( ! isset( $_FILES[$name] ) || ! isset( $_FILES[$name]['tmp_name'] ) ) {
				wp_send_json_error( $this->get_error_msg('failed_upload') );
			}

			$file         = $_FILES[ $name ];
			$file['name'] = sanitize_file_name( $file['name'] );

			// Tells whether the file was uploaded via HTTP POST
			if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
				$error_code = ( $file['error'] == 1 ? __('The uploaded file exceeds the upload_max_filesize limit.','dnd-file-upload-wc') : $this->get_error_msg('failed_upload') );
				wp_send_json_error( get_option('wcf_drag_n_drop_error_failed_to_upload') ? get_option('wcf_drag_n_drop_error_failed_to_upload') : $error_code  );
			}

			/* File type validation */
			$supported_options = ( get_option('wcf_drag_n_drop_support_file_upload') ? explode( ',', get_option('wcf_drag_n_drop_support_file_upload') ) : null );
			$supported_types    = 'jpg|jpeg|JPG|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv|xls';

			// Parse allowed types
			if( $supported_options && is_array( $supported_options ) ) {
				$supported_types = implode( '|', array_map('trim', $supported_options) );
			}

			// Prepare file type pattern.
			$file_type_pattern = dndmfu_wc_filetypes( $supported_types );

			// Get file extension
			$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

			// validate file type
			if ( ! preg_match( $file_type_pattern, $file['name'] ) || ! dndmfu_wc_validate_type( $extension, $supported_types ) ) {
				wp_send_json_error( get_option('wcf_drag_n_drop_error_invalid_file') ? get_option('wcf_drag_n_drop_error_invalid_file') : $this->get_error_msg('invalid_type') );
			}

			// Check file type - sniffs real content, not just the extension
			$validate = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
			if ( empty( $validate['type'] ) || empty( $validate['ext'] ) ) {
				wp_send_json_error( get_option('wcf_drag_n_drop_error_invalid_file') ? get_option('wcf_drag_n_drop_error_invalid_file') : $this->get_error_msg('invalid_type') );
			}

			// Size limit - clamp client value against the server setting
			$server_limit = (int) get_option('wcf_drag_n_drop_file_size_limit');
			$server_limit = $server_limit > 0 ? ( $server_limit * 1048576 ) : wp_max_upload_size();

			$client_limit = isset( $_POST['size_limit'] ) ? (int) $_POST['size_limit'] : $server_limit;
			$size_limit   = min( $server_limit, $client_limit > 0 ? $client_limit : $server_limit );

			if( $file['size'] > $size_limit ) {
				wp_send_json_error( get_option('wcf_drag_n_drop_error_files_too_large') ? get_option('wcf_drag_n_drop_error_files_too_large') : $this->get_error_msg('large_file') );
			}

			// Get dir setup / path - each upload session gets its own tmp subfolder ( named after its token )
			$base_dir = trailingslashit( $this->_options['upload_dir'] ) . $this->get_token_upload_dir( $token );

			// Create tmp_folder dir
			if( ! is_dir( $base_dir ) ) {
				wp_mkdir_p( $base_dir );
			}

			// Create file name
			$filename = $file['name'];
			$filename = dndmfu_wc_antiscript_file_name( $filename );

			// Add filter on upload file name
			$filename = apply_filters( 'dndmfu_wc_file_name', $filename, $file['name'] );

			// Generate new filename - kept exactly as-is, ownership lives in the folder, not the filename.
			$filename = wp_unique_filename( $base_dir, $filename );
			$new_file = path_join( $base_dir, $filename );

			// Php manual files upload
			if ( false === move_uploaded_file( $file['tmp_name'], $new_file ) ) {
				$error_upload = get_option('wcf_drag_n_drop_error_failed_to_upload') ? get_option('wcf_drag_n_drop_error_failed_to_upload') : $this->get_error_msg('failed_upload');
				wp_send_json_error( $error_upload );
			}else{

				// Bump this session's file counter (update file count in transient)
				$state['file_count']++;
				$this->save_token_state( $token, $state );

				// Setup path and file name and add it to response.
				$path = trailingslashit( '/' . $this->get_token_upload_dir( $token ) );

				// Change file permission to 0400
				chmod( $new_file, 0644 );

				// Get details of attachment from media_json_respons function
				$files = dndmfu_wc_media_json_response( $path, wp_basename( $filename ) );

				// Send files to json response
				wp_send_json_success( $files );
			}

			die;
		}

		/**
		* Delete specific files - via Ajax
		*/
		public function delete_file() {

			// Layer 1 - CSRF only. Every guest shares the same nonce,
			// so this can never prove identity.
			if( ! check_ajax_referer( 'dnd_wc_ajax_upload', 'security', false ) ){
				wp_send_json_error('The security nonce is invalid or expired');
			}

			// Layer 2 - ownership token. This is what stops the PoC.
			$token = $this->get_token();
			if( ! $token ) {
				wp_send_json_error('Missing upload session.', 403 );
			}

			// Layer 3 - throttle
			$state = $this->get_token_state( $token ); // get_transient [file_count, upload_hits]

			// Max 30 files deleted/min per token (true if hit max)
			$throttled = $this->token_throttled( $state, 'delete_hits', 'delete_window_start', 30, MINUTE_IN_SECONDS );

			// Save token in transient - initial
			$this->save_token_state( $token, $state );

			// If throttled hits max 30 deletes/minute return an error.
			if( $throttled ) {
				wp_send_json_error('Too many requests.', 429 );
			}

			// Sanitize Path
			$get_file_name = ( isset( $_POST['path'] ) ? sanitize_text_field( wp_unslash( $_POST['path'] ) ) : '' );

			// Get only the filename to avoid traversal attack..
			$file_name = basename( trim( $get_file_name ) );

			if( '' === $file_name ) {
				wp_send_json_error('Invalid file.', 400 );
			}

			// Check valid filename & extensions
			if( preg_match_all('/wp-|(\.php|\.exe|\.js|\.phtml|\.cgi|\.aspx|\.asp|\.bat)/', $file_name ) ) {
				wp_send_json_error('File not safe', 400 );
			}

			// Layer 4 - OWNERSHIP CHECK - only this token can resolve a path into its own tmp subfolder.
			$dir = trailingslashit( $this->get_token_upload_dir( $token ) ) . $file_name;

			// Layer 5 - path containment (original logic)
			$file_path = realpath( trailingslashit( $this->wp_upload_dir['basedir'] ) . $dir );

			if( ! $file_path ) {
				wp_send_json_error('File not found.', 404 );
			}

			$is_path_in_content_dir = strpos( $file_path, realpath( wp_normalize_path( $this->wp_upload_dir['basedir'] ) ) );

			if( ! preg_match("/". DNDMFU_WC_PATH ."/i", $file_path ) || ( 0 !== $is_path_in_content_dir ) ) {
				wp_send_json_error('It\'s not a valid upload directory', 400 );
			}

			// Check if file exists
			if( file_exists( $file_path ) ){
				dndmfu_wc_delete_file( $file_path );
				if( ! file_exists( $file_path ) ) {

					// Reduce this session's file counter now that the delete succeeded. (transient)
					if( $state['file_count'] > 0 ) {
						$state['file_count']--;
						$this->save_token_state( $token, $state );
					}

					wp_send_json_success('File Deleted!');
				}
			}

			wp_send_json_error('Could not delete file.', 500 );
		}

	}

	/**
	* Initialize using singleton pattern
	*/

	// declare function assign return instance
	function DNDMFU_WC_INIT() {
		return DNDMFU_WC_MAIN::get_instance();
	}

	// Launch the whole plugin.
	add_action( 'woocommerce_loaded', 'DNDMFU_WC_INIT' );
