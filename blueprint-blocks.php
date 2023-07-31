<?php
/*
Plugin Name: Gravitate Base
Description: Base Plugin for Gravitate Blocks
Version: 1.2.5
Plugin URI: http://www.gravitatedesign.com/base
Author: Gravitate
Author URI: http://www.gravitatedesign.com/dev
*/

require_once(plugin_dir_path(__FILE__).'blueprint-blocks-css.php');
require_once(plugin_dir_path(__FILE__).'blueprint-plugin-settings.php');
require_once(plugin_dir_path(__FILE__).'blueprint-global-blocks.php');
require_once(plugin_dir_path(__FILE__).'blueprint-dependency-manager.php');
require_once(plugin_dir_path(__FILE__).'blueprint-blocks-util.php');
require_once(plugin_dir_path(__FILE__).'blueprint-blocks-backgrounds.php');
require_once(plugin_dir_path(__FILE__).'library/includes/utils.php');

register_activation_hook(__FILE__, array('GRAV_BLOCKS', 'activate'));
register_deactivation_hook(__FILE__, array('GRAV_BLOCKS', 'deactivate'));

add_action('admin_menu', array('GRAV_BLOCKS', 'admin_menu'));
add_action('admin_init', array('GRAV_BLOCKS', 'admin_init'));
add_action('wp_loaded', array('GRAV_BLOCKS', 'init'));
add_action('wp_footer', array('GRAV_BLOCKS', 'load_dependencies'));
add_action('admin_enqueue_scripts', array('GRAV_BLOCKS', 'enqueue_admin_files'));
add_filter('plugin_action_links_'.plugin_basename(__FILE__), array('GRAV_BLOCKS', 'plugin_settings_link'));

/**
 *
 * @author Gravitate
 *
 */
class GRAV_BLOCKS
{
	private static $version = '1.2.5';
	private static $page = 'admin.php?page=gravitate-blocks';
	private static $settings = array();
	private static $option_key = 'gravitate_blocks_settings';
	private static $posts_to_exclude = array('attachment', 'revision', 'nav_menu_item', 'acf-field-group', 'acf-field', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block');
	public  static $current_block_name = '';
	public  static $block_index = 0;
	public  static $block_wrapped_repeater_index = 0;
	public  static $plugin_url = '';
	public  static $plugin_path = '';
	public  static $background_groups = [];
	//private static $registered_sections = array(array());
	private static $cache = array();
	private static $registered_sections = array();


	public static function dump($var)
	{
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}

	public static function load_dependencies()
	{
		\BlueprintBlocks\DependencyManager::load_dependencies();
	}

	/**
	 * Outputs the Grav CSS to the Front End Head
	 *
	 * @return type
	 */
	public static function add_head_css()
	{
		$output_default_styles = apply_filters('grav_blocks_output_default_styles', true);

		if (!$output_default_styles) {
			return;
		}

		?>
		<style>
			/* Gravitate Block Option Classes */
			.block-options-padding-remove-top .block-inner {
				padding-top: 0;
			}

			.block-options-padding-remove-bottom .block-inner {
				padding-bottom: 0;
			}

			.block-bg-image {
				background-size: cover;
				background-position: center;
			}

			.block-bg-video {
				overflow: hidden;
			}

			.block-bg-video .block-video-container {
				position: absolute;
				top: 50%;
				left: 50%;
				-webkit-transform: translateX(-50%) translateY(-50%);
				transform: translateX(-50%) translateY(-50%);
				min-width: 100%;
				min-height: 100%;
				width: auto;
				height: auto;
				overflow: hidden;
				z-index: -1;
			}

			.block-bg-video,
			.block-bg-overlay,
			.block-bg-video .block-inner,
			.block-bg-overlay .block-inner {
				position: relative;
			}

			.block-bg-overlay::before {
				content: '';
				display: block;
				position: absolute;
				background-color: rgba(0, 0, 0, 0.5);
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
			}
		</style>
	<?php
	}

	/**
	 * Register all global JS and CSS dependencies used by the default blocks
	 *
	 * @return void
	 */
	private static function register_global_dependencies()
	{
		$plugin_dep_url = plugin_dir_url(__FILE__).'library/dependencies';

		// Google Maps API
		$google_maps_api_key = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_api_key');

		if ($google_maps_api_key) {
			wp_register_script(
				'google-maps-api',
				'https://maps.googleapis.com/maps/api/js?key='.$google_maps_api_key,
				[],
				null,
				true
			);
		}

		// Snazzy Info Window for Google Maps
		wp_register_script(
			'snazzy-info-window',
			$plugin_dep_url.'/js/snazzy-info-window.min.js',
			['google-maps-api'],
			'1.1.1',
			true
		);

		wp_register_style(
			'snazzy-info-window',
			$plugin_dep_url.'/css/snazzy-info-window.min.css',
			[],
			'1.1.1'
		);

		// Swiper
		wp_register_script(
			'swiper',
			$plugin_dep_url.'/js/swiper.min.js',
			[],
			'4.4.6',
			true
		);

		wp_register_style(
			'swiper',
			$plugin_dep_url.'/css/swiper.min.css',
			[],
			'4.4.6'
		);

		// Colorbox
		wp_register_script(
			'colorbox',
			$plugin_dep_url.'/js/jquery.colorbox-min.js',
			['jquery'],
			'1.6.4',
			true
		);
	}

	/**
	 * This is the initial setup that connects the Settings and loads the Fields from ACF
	 *
	 * @return void
	 */
	private static function setup()
	{
		global $block;

		GRAV_GLOBAL_BLOCKS::setup_global_blocks_cpt();

		new GRAV_BLOCKS_PLUGIN_SETTINGS(self::$option_key);

		self::get_settings(true);

		/**
		 * Register Google Maps API key with ACF
		 */
		add_filter('acf/fields/google_map/api', function($api) {
			$key = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_api_key');

			if (!is_string($key) || !trim($key)) {
				return $api;
			}

			$api['key'] = trim($key);

			return $api;
		});

		/**
		 *  Include Blocks in Flexible Content
		 */
		$layouts = array();

		foreach (self::get_blocks() as $block => $block_params) {
			self::$current_block_name = $block;

			if (!empty($block_params['path'])) {
				$block_backgrounds = array ();
				$block_background_image = array ();

				if (file_exists($block_params['path'].'/block_fields.php')) {
					$layouts[$block] = include($block_params['path'].'/block_fields.php');
				}
			}
		}

		// Reset Current Block
		$block = '';

		/*
		* Block Function to build Admin and Set Fields for ACF
		*/
		if (function_exists("acf_add_local_field_group") && !empty($layouts)) {
			// Filter the Link Options
			self::filter_layout_links($layouts, '', 'grav_link_fields');

			// Add Default Fields
			foreach ($layouts as $block_key => $block_layout) {
				if (!empty($block_layout['sub_fields']) && $block_layout['name'] != 'global-block') {
					$layouts[$block_key]['sub_fields'] = array_merge(self::get_default_fields($block_layout['name']), $block_layout['sub_fields']);
				}
			}

			// Filter the Fields from developers
			$layouts = apply_filters('grav_block_fields', $layouts);

			// Create Tabs
			if (!empty($layouts)) {
				foreach ($layouts as $block_key => $block_layout) {
					$tab_fields = array();
					$new_sub_fields = array();
					$tab_options_fields = array();

					$add_first_tab = true;

					if (!empty($block_layout['sub_fields'])) {
						foreach ($block_layout['sub_fields'] as $sub_field_key => $sub_field) {
							if ($sub_field_key === 0) {
								if (!empty($sub_field['type']) && $sub_field['type'] === 'tab') {
									$add_first_tab = false;
								}
							}

							if ((!empty($sub_field['block_options']) || !empty($sub_field['block_option'])) && $sub_field['type'] !== 'tab') {
								$tab_options_fields[] = $sub_field;
							} else {
								$tab_fields[] = $sub_field;
							}
						}
					}

					$tab_content = array (
						'key' => 'field_block_tab_'.$block_layout['name'].'_tab1',
						'label' => 'Content',
						'name' => 'block_tab_'.$block_layout['name'].'_tab1',
						'type' => 'tab',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'placement' => 'left',
						'endpoint' => 0,          // end tabs to start a new group
					);

					$tab_options = array (
						'key' => 'field_block_tab_'.$block_layout['name'].'_tab2',
						'label' => 'Options',
						'name' => 'block_tab_'.$block_layout['name'].'_tab2',
						'type' => 'tab',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'placement' => 'left',
						'endpoint' => 0,          // end tabs to start a new group
					);

					$guidelines_text = apply_filters('grav_blocks_'.$block_layout['name'].'_guidelines_text', '');
					$guidelines_fields = $guidelines_text ? array(
						array (
							'key' => 'field_'.$block_layout['name'].'_guidelines',
							'label' => apply_filters('grav_blocks_'.$block_layout['name'].'_guidelines_title', 'Guidelines'),
							'name' => 'guidelines',
							'type' => 'message',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array (
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => $guidelines_text,
							'new_lines' => 'wpautop',    // wpautop | br | ''
							'esc_html' => 0,             // uses the WordPress esc_html function
						)
					) : [];

					if ($add_first_tab) {
						$new_sub_fields = array($tab_content);
					}

					if (!empty($block_layout['grav_blocks_settings']['repeater'])) {
						$layouts[$block_key]['display'] = 'block';
						$background_fields = self::get_background_fields($block_layout['name'], 'Container Background', 'wrapped_repeater_background');

						$tab_fields = array(array (
						    'key' => 'field_'.$block_layout['name'].'_wrapped_repeater',
						    'label' => 'Items',
						    'name' => 'wrapped_repeater',
						    'type' => 'repeater',
						    'instructions' => '',
						    'required' => 0,
						    'conditional_logic' => 0,
						    'wrapper' => array (
						        'width' => '',
						        'class' => '',
						        'id' => '',
						    ),
						    'collapsed' => '',
						    'min' => '1',
						    'max' => '',
						    'layout' => 'row',         // table | block | row
						    'button_label' => (!empty($block_layout['grav_blocks_settings']['repeater_label']) ? $block_layout['grav_blocks_settings']['repeater_label'] : 'Add'),
						    'sub_fields' => $tab_fields,
						));

						$tab_fields = array_merge($background_fields, $tab_fields);

					}

					$new_sub_fields = array_merge($new_sub_fields, $guidelines_fields, $tab_fields, array($tab_options), $tab_options_fields);

					$layouts[$block_key]['sub_fields'] = $new_sub_fields;
				}
			}

			$placement = (GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('advanced_options', 'after_title')) ? 'acf_after_title' : 'normal';

			$sections = array (
				'key' => 'group_grav_blocks',
				'title' => 'Grav Blocks',
				'fields' => array (
					array (
						'key' => 'field_x1',
						'label' => 'Grav Blocks',
						'name' => 'grav_blocks',
						'type' => 'flexible_content',
						'layouts' => $layouts,
						'button_label' => 'Add Content',
						'min' => '',
						'max' => '',
					),
				),
				'location' => self::get_locations(),
				'menu_order' => 100,
				'position' => $placement,
				'style' => 'no_box',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => self::hide_on_screen(),
				'active' => 1,
				'description' => '',
			);

			$sections = apply_filters('grav_default_section', $sections);

			acf_add_local_field_group($sections);

			if (isset(self::$settings['option_pages'])) {
				$option_pages = self::$settings['option_pages'];

				foreach($option_pages as $option_page) {
					$sections = array (
						'key' => 'group_options_'.$option_page,
						'title' => 'Blocks',
						'fields' => array (
							array (
								'key' => 'field_options_'.$option_page,
								'label' => 'Grav Blocks',
								'name' => $option_page,
								'type' => 'clone',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array (
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'clone' => array (
									0 => 'group_grav_blocks',
								),
								'display' => 'seamless',
								'layout' => 'block',
								'prefix_label' => 0,
								'prefix_name' => 1,
							),
						),
						'location' => array (
							array (
								array (
									'param' => 'options_page',
									'operator' => '==',
									'value' => $option_page,
								),
							),
						),
						'menu_order' => 100,
						'position' => 'normal',
						'style' => 'no_box',
						'label_placement' => 'top',
						'instruction_placement' => 'label',
						'hide_on_screen' => '',
						'active' => 1,
						'description' => '',
					);

					$sections = apply_filters('grav_blocks_section', $sections, $option_page);
					acf_add_local_field_group($sections);
				}
			}

			self::$registered_sections = $sections;
		}
	}

	private static function get_block_background_allowed_video()
	{
		$block_background_video_blocks = array('banner');

		return apply_filters('grav_blocks_background_video', $block_background_video_blocks);
	}

	private static function get_background_fields($block='', $label='Background', $key='background')
	{
		/**
		 *  Set Background Colors
		 */
		$block_background_colors = array();
		$block_background_colors['block-bg-none'] = 'None';

		if (!empty(self::$settings['background_colors'])) {
			foreach (self::$settings['background_colors'] as $color_key => $color_params) {
				if (!empty($color_params['_repeater_id'])) {
					$block_background_colors['block-bg-'.$color_params['_repeater_id']] = $color_params['name'];
				}
			}
		}

		$block_background_colors['block-bg-image'] = 'Image';

		if (in_array($block, self::get_block_background_allowed_video($block))) {
			$block_background_colors['block-bg-video'] = 'Video';
		}

		$block_background_colors = apply_filters( 'grav_block_background_colors', $block_background_colors, $block );

		/**
		 *  Set Default Fields
		 */
		$background_fields = array(
			array (
				'key' => 'field_block_default_'.$block.'_'.$key,
				'label' => $label,
				'name' => 'block_background',
				'type' => 'select',
				'column_width' => '',
				'choices' => $block_background_colors,
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
				'block_options' => 1
			),
			array (
			    'key' => 'field_block_default_'.$block.'_'.$key.'_video_type',
			    'label' => 'Video Type',
			    'name' => 'block_background_video_type',
			    'type' => 'radio',
			    'instructions' => 'Using Url with Vimeo or other Provider is the better option as it will not incur additional bandwidth charges from your Hosting Provider.',
			    'required' => 0,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_block_default_'.$block.'_'.$key,
							'operator' => '==',
							'value' => 'block-bg-video',
						),
					),
				),
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'choices' => array (
			        'url' => 'Url',
					'file' => 'File'
			    ),
			    'other_choice' => 0,
			    'save_other_choice' => 0,
			    'default_value' => 'url',
			    'layout' => 'horizontal',
				'block_options' => 1
			),
			array (
				'key' => 'field_block_default_'.$block.'_'.$key.'_video_url',
				'label' => 'Background Video URL',
				'name' => 'block_background_video_url',
				'type' => 'text',
				'instructions' => 'Video must be a MP4 Format. <br><br>Use the Background Image below for a Placeholder',
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_block_default_'.$block.'_'.$key.'_video_type',
							'operator' => '==',
							'value' => 'url',
						),
						array (
							'field' => 'field_block_default_'.$block.'_'.$key,
							'operator' => '==',
							'value' => 'block-bg-video',
						),
					),
				),
				'column_width' => '',
				'save_format' => 'object',
				'preview_size' => 'medium',
				'library' => 'all',
				'block_options' => 1
			),
			array (
			    'key' => 'field_block_default_'.$block.'_'.$key.'_video_file',
			    'label' => 'Video File',
			    'name' => 'block_background_video_file',
			    'type' => 'file',
			    'instructions' => 'Uploads may not work if the file is too large.  <br><br>Use the Background Image below for a Placeholder',
			    'required' => 0,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_block_default_'.$block.'_'.$key.'_video_type',
							'operator' => '==',
							'value' => 'file',
						),
						array (
							'field' => 'field_block_default_'.$block.'_'.$key,
							'operator' => '==',
							'value' => 'block-bg-video',
						),
					),
				),
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'return_format' => 'url',      // array | url | id
			    'library' => 'all',              // all | uploadedTo
			    'min_size' => '',
			    'max_size' => '',
			    'mime_types' => '',
				'block_options' => 1
			),
			array (
				'key' => 'field_block_default_'.$block.'_'.$key.'_image',
				'label' => 'Background Image',
				'name' => 'block_background_image',
				'type' => 'image',
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_block_default_'.$block.'_'.$key,
							'operator' => '==',
							'value' => 'block-bg-image',
						),
					),
					array (
						array (
							'field' => 'field_block_default_'.$block.'_'.$key,
							'operator' => '==',
							'value' => 'block-bg-video',
						),
					),
				),
				'column_width' => '',
				'save_format' => 'object',
				'preview_size' => 'medium',
				'library' => 'all',
				'block_options' => 1
			),
			array (
			   'key' => 'field_block_default_'.$block.'_'.$key.'_overlay',
			   'label' => 'Add Background Overlay',
			   'name' => 'block_background_overlay',
			   'type' => 'true_false',
			   'instructions' => '',
			   'required' => 0,
			   'conditional_logic' => array (
				   array (
					   array (
						   'field' => 'field_block_default_'.$block.'_'.$key,
						   'operator' => '==',
						   'value' => 'block-bg-image',
					   ),
				   ),
				   array (
					   array (
						   'field' => 'field_block_default_'.$block.'_'.$key,
						   'operator' => '==',
						   'value' => 'block-bg-video',
					   ),
				   ),
			   ),
			   'wrapper' => array (
			       'width' => '',
			       'class' => '',
			       'id' => '',
			   ),
			   'message' => '',
			   'ui' => 1,
			   'ui_on_text' => 'Yes',
			   'ui_off_text' => 'No',
			   'default_value' => 0,
			   'block_options' => 1
			)
		);

		return $background_fields;
	}

	private static function get_default_fields($block='')
	{
		$background_fields = self::get_background_fields($block);

		$default_fields = array(
			array (
				'key' => 'field_block_default_'.$block.'_unique_id',
				'label' => 'Container ID',
				'name' => 'unique_id',
				'type' => 'text',
				'column_width' => '',
				'default_value' => '',
				'instructions' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none', 		// none | html
				'maxlength' => '',
				'block_options' => 1
			),
			array (
				'key' => 'field_block_default_'.$block.'_admin_title',
				'label' => 'Block Title',
				'name' => 'layout_title',
				'type' => 'text',
				'required' => 0,
				'block_options' => 1
			),
			array (
				'key' => 'field_block_default_'.$block.'_custom_class',
				'label' => 'Custom CSS Classes',
				'name' => 'block_option_custom_class',
				'type' => 'text',
				'column_width' => '',
				'default_value' => '',
				'instructions' => 'Separate with spaces',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none', 		// none | html
				'maxlength' => '',
				'block_options' => 1
			),
			array (
			   'key' => 'field_'.$block.'_block_animate',
			   'label' => 'Animate',
			   'name' => 'block_animate',
			   'type' => 'true_false',
			   'instructions' => '',
			   'required' => 0,
			   'conditional_logic' => 0,
			   'wrapper' => array (
			       'width' => '',
			       'class' => '',
			       'id' => '',
			   ),
			   'message' => '',
			   'ui' => 1,
			   'ui_on_text' => 'Yes',
			   'ui_off_text' => 'No',
			   'default_value' => 0,
			   'block_options' => 1,
			),
			array (
			    'key' => 'field_block_option_'.$block.'_padding',
			    'label' => 'Padding',
			    'name' => 'block_option_padding',
			    'type' => 'checkbox',
			    'instructions' => '',
			    'required' => 0,
			    'conditional_logic' => 0,
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'choices' => array (
			        'block-options-padding-remove-top' => 'Remove Top Padding',
			        'block-options-padding-remove-bottom' => 'Remove Bottom Padding'
			    ),
			    'default_value' => array (
			    ),
			    'layout' => 'horizontal',
			    'toggle' => 0,
				'block_options' => 1
			),
			array (
			    'key' => 'field_block_option_'.$block.'_hiding',
			    'label' => 'Hide For',
			    'name' => 'block_option_hide',
			    'type' => 'checkbox',
			    'instructions' => '',
			    'required' => 0,
			    'conditional_logic' => 0,
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'choices' => array (
			        'small' => 'Small Screens',
			        'medium' => 'Medium Screens',
			        'large' => 'Large Screens',
			        'xlarge' => 'Extra Large Screens',
			    ),
			    'default_value' => array (
			    ),
			    'layout' => 'horizontal',
			    'toggle' => 0,
				'block_options' => 1
			),
		);

		return array_merge($background_fields, $default_fields);
	}

	// Style (Keep for Older Versions) #TODO Deprecate as this is no longer needed with get_default_fields()
	public static function get_additional_fields()
	{
		return array();
	}

	public static function get_registered_sections()
	{
		return self::$registered_sections;
	}

	public static function hide_on_screen()
	{
		$hidden = array();

		if (GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('advanced_options', 'hide_content')) {
			$hidden[0] = 'the_content';
		}

		$hidden = apply_filters( 'grav_hide_on_screen', $hidden );

		return $hidden;
	}

	public static function css()
	{
		return new GRAV_BLOCKS_CSS();
	}

	/**
	 * Runs on WP init
	 *
	 * @return void
	 */
	public static function init()
	{
		self::$plugin_url = trailingslashit(plugin_dir_url(__FILE__));
		self::$plugin_path = trailingslashit(plugin_dir_path(__FILE__));

		self::setup();
		self::add_hooks();
		self::prepare_blocks();
		self::register_global_dependencies();
	}

	/**
	 * Runs on action "wp"
	 * Use this section to run code before any output has been sent to browser.
	 *
	 * @return void
	 */
	public static function prepare_blocks()
	{
		$blocks = self::get_blocks();

		if (!$blocks || !is_array($blocks)) {
			return;
		}

		foreach ($blocks as $block) {
			$block_functions_file = $block['path'].'/block_functions.php';

			if (file_exists($block_functions_file)) {
				include_once($block_functions_file);
			}
		}
	}

	/**
	 * Runs on WP init
	 *
	 * @return void
	 */
	public static function add_hooks()
	{
		if (GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('advanced_options', 'filter_content') && !is_admin()) {
			self::add_hook('filter', 'the_content', 'filter_content', 23);
		}

		if (!is_admin()) {
			self::add_hook('action', 'wp_head', 'add_head_css');
		}

		// self::add_hook('action', 'wp', 'prepare_blocks');

		if (GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('search_options', 'include_in_search') && !is_admin() && is_main_query()) {
			self::add_hook('filter', 'posts_search', 'add_search_filtering');
		}

		if (!is_admin()) {
			if ((!empty($_GET['s']) && GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('search_options', 'include_in_search')) || GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('advanced_options', 'filter_excerpt')) {
				self::add_hook('filter', 'get_the_excerpt', 'add_excerpt_filtering');
			}
		}

		// self::add_hook('action', 'wp_footer', 'add_footer_js', 100);
		self::add_hook('action', 'wp_enqueue_scripts', 'add_footer_js');

		if (!function_exists('acf_add_local_field_group') && (!isset($_GET['page']) || $_GET['page'] != 'gravitate_blocks')) {
			self::add_hook('action', 'admin_notices', 'acf_notice');
		}

		// Add Hook doesn't work here as the parameters messes up the reference
		// self::add_hook('action', 'grav_blocks_display_before' , 'get_block_background_video_markup', 10, 2);
		add_action( 'grav_blocks_display_before' , array(__CLASS__, 'get_block_background_video_markup'), 10, 2);
	}

	/**
	 * Runs on WP init
	 *
	 * @return void
	 */
	public static function add_hook($type='', $hook='', $hook_function='', $param='')
	{
		if ($type === 'action') {
			add_action( $hook , array(__CLASS__, $hook_function));
		} else {
			add_filter( $hook , array(__CLASS__, $hook_function), $param);
		}
	}

	/**
	 * Grabs the settings from the Settings class
	 *
	 * @param boolean $force
	 *
	 * @return void
	 */
	public static function get_settings($force=false)
	{
		self::$settings = GRAV_BLOCKS_PLUGIN_SETTINGS::get_settings($force);
	}

	/**
	 * Returns the default settings array
	 *
	 * @return array<string, mixed>
	 */
	public static function get_default_settings(): array
	{
		$current_settings = [
			'post_types' => array_keys(self::get_usable_post_types()),
			'templates' => '',
			'advanced_options' => ['filter_content'],
			// 'css_options' => ['enqueue_css', 'use_foundation', 'use_default'],
			'search_options' => ['include_in_search'],
			'background_colors' => [
				['name' => 'White', 'value' => '#ffffff'],
				['name' => 'Light Gray', 'value' => '#eeeeee'],
				['name' => 'Dark Gray', 'value' => '#555555']
			],
			// 'foundation' => array['f5'],
		];

		$blocks_groups = self::get_available_block_groups();

		foreach ($blocks_groups as $group_name => $group_info) {
			$current_settings['blocks_enabled_' . $group_name] = array_keys($group_info);
		}

		return $current_settings;
	}

	/**
	 * Runs on WP Plugin Activation
	 *
	 * @return void
	 */
	public static function activate($network_wide)
	{
		if ($network_wide) {
			$blog_ids = get_sites(['fields' => 'ids']);

			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);

				$active_settings = get_option(self::$option_key);

				if (!$active_settings) {
					update_option(self::$option_key, self::get_default_settings());
				}

				restore_current_blog();
			}
		} else {
			$active_settings = get_option(self::$option_key);

			if (!$active_settings) {
				update_option(self::$option_key, self::get_default_settings());
			}
		}
	}

	public static function acf_notice($dismissible=true)
	{
	    ?>
	    <div class="notice error grav-blocks-acf-notice<?php echo ($dismissible ? ' is-dismissible' : '');?>">
	        <p><?php _e( 'Gravitate Blocks - ACF Pro is required to run Gravitate Blocks<br>To download the plugin go here. <a target="_blank" href="http://www.advancedcustomfields.com/pro/">http://www.advancedcustomfields.com/pro/</a><br>To remove this message permanently either Install ACF Pro or Deactivate the Gravitate Blocks Plugin', 'GRAV_BLOCKS' ); ?></p>
	    </div>
	    <?php
	}

	/**
	 * Runs on WP Plugin Deactivation
	 *
	 * @return void
	 */
	public static function deactivate()
	{
		// Nothing for now
	}

	/**
	 * Runs on WP Admin Initiate
	 *
	 * @return void
	 */
	public static function admin_init()
	{
		// Nothing for now
	}

	/**
	 * Create the Admin Menu in that Admin Panel
	 *
	 * @return void
	 */
	public static function admin_menu()
	{
		$icon = file_get_contents(plugin_dir_path( __FILE__ ) . 'grav-blocks/columns/columns_1.svg');

		add_menu_page( 'Gravitate Blocks', 'Blocks', 'manage_options', 'gravitate-blocks', array( __CLASS__, 'admin' ), 'dashicons-gravitate', 9999);
	}

	public static function plugin_settings_link($links)
	{
		$settings_link = '<a href="options-general.php?page=gravitate_blocks">Settings</a>';
		array_unshift($links, $settings_link);

		return $links;
	}

	public static function add_excerpt_filtering($output)
	{
		if (empty($output) && !has_excerpt() && !trim(strip_tags(get_the_content()))) {
			global $wpdb;

			$search_query = \BlueprintBlocks\Util::get_search_query();

			// If is Search, then first check to see if we can find results that matches the search
			if (is_main_query() && $search_query && \BlueprintBlocks\Util::is_search() && GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('search_options', 'include_in_search')) {
				$results = $wpdb->get_var("SELECT meta_value FROM ".$wpdb->postmeta." WHERE meta_value LIKE '%" . esc_sql($search_query) . "%' AND meta_key NOT LIKE '\_%' AND post_id = ".get_the_ID()." ORDER BY CHAR_LENGTH(meta_value) DESC LIMIT 1");
			}

			// If no matches are found or if not Search then check for any fields to show data
			if (empty($results)) {
				if (GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('search_options', 'include_in_search') || !\BlueprintBlocks\Util::is_search() || !is_main_query()) {
					$results = $wpdb->get_var("SELECT meta_value FROM ".$wpdb->postmeta." WHERE meta_key NOT LIKE '\_%' AND post_id = ".get_the_ID()." ORDER BY CHAR_LENGTH(meta_value) DESC LIMIT 1");
				}
			}

			// If Results then lets format it
		    if (!empty($results)) {
	    		$output = wp_trim_excerpt(strip_shortcodes(strip_tags($results)));
	    	}
		}

		return $output;
	}

	public static function add_search_filtering($search)
	{
		if (!is_admin() && \BlueprintBlocks\Util::is_search() && is_main_query()) {
			global $wpdb;

			$post_ids = array();
			$search_query = \BlueprintBlocks\Util::get_search_query();

			if ($results = $wpdb->get_results("SELECT * FROM ".$wpdb->postmeta.", ".$wpdb->posts." WHERE meta_value LIKE '%" . esc_sql($search_query) . "%' AND post_id = ID AND post_status = 'publish' GROUP BY post_id")) {
			    foreach ($results as $result) {
			        $post_ids[] = $result->post_id;
			    }
			}

			if (!empty($post_ids)) {
				$replace = ' OR ('.$wpdb->posts.'.ID IN ('.esc_sql(implode(',',$post_ids)).')) OR ';
				$search = str_replace(' OR ', $replace, $search);
			}
		}

		return $search;
	}


	/**
	 * Outputs the Grav Blocks
	 *
	 * @param string $args - Currently the two options for the array are 'section' and 'object'
	 *
	 * @return type
	 */
	public static function display($args = array())
	{
		// Check $args array if it exists and what is set.
		$section = (!empty($args['section'])) ? $args['section'] : 'grav_blocks';
		// $object = (isset($args['object']) || (isset($args['object']) && is_null($args['object']))) ? $args['object'] : false;
		$object = (in_array('object', array_keys($args)) ? $args['object'] : false);

		$block_only = !empty($args['block']) ? $args['block'] : '';
		$block_only_id = !empty($args['block_id']) ? $args['block_id'] : '';

		$block_only_variables = isset($args['block_variables']) ? $args['block_variables'] : array();

		$block_excludes = !empty($args['exclude_blocks']) ? $args['exclude_blocks'] : array();
		$block_includes = !empty($args['include_blocks']) ? $args['include_blocks'] : array();

		$handler_file = self::get_path('handler.php');

		// Use Single Block
		if (is_null($object) && $block_only) {
			self::$current_block_name = strtolower(str_replace('_', '-', $block_only));
			self::get_block_format($block_only_variables, $handler_file);

			return;
		}

		// Use All Blocks
		$query_target = ($object) ? $object : ( ( ( $query = get_queried_object() ) && !empty($query->term_id ) ) ? $query : '');

		if (isset($query_target->ID)) {
			$query_target = $query_target->ID;
		}

		$viewable_query_target = $query_target;

		if (in_array($viewable_query_target, array('option_page', 'options_page'))) {
			$viewable_query_target = $section;
			$section.= '_grav_blocks';
			$query_target = 'option';
		}

		if (!self::is_viewable($viewable_query_target)) {
			return;
		}

		$block_fields = get_field($section, $query_target);

		$allowable_bg_groups = apply_filters('grav_blocks_allowable_background_groups', []);

		if (!$handler_file || !$block_fields || !is_array($block_fields)) {
			return;
		}

		$background_groups = \BlueprintBlocks\Backgrounds::get_background_groups(
			is_array($block_fields) ? $block_fields : [],
			$allowable_bg_groups,
			GRAV_BLOCKS::$block_index
		);

		while (the_flexible_field($section, $query_target)) {
			self::$current_block_name = strtolower(str_replace('_', '-', get_row_layout()));

			$use_single_block = ((!$block_only_id && $block_only === self::$current_block_name) || ($block_only_id && $block_only_id === get_sub_field('unique_id')));

			if (empty($block_only) || $use_single_block) {
				if (!in_array(self::$current_block_name, $block_excludes) && (empty($block_includes) || in_array(self::$current_block_name, $block_includes))) {
					self::get_block_format($block_only_variables, $handler_file, $background_groups);

					if ($use_single_block) {
						reset_rows(true);
						return;
					}
				}
			}
		}

		do_action('grav_blocks_display_complete');
	}


	private static function get_block_attributes($block_name='', $block_variables=array())
	{
		$block_attributes = array();

		if (!empty($block_variables)) {
			extract($block_variables);
		}

		if (!isset($block_unique_id)) {
			$block_unique_id = get_sub_field('unique_id');
		}

		if (!isset($block_custom_class)) {
			$block_custom_class = get_sub_field('block_option_custom_class');
		}

		if (!isset($block_padding)) {
			$block_padding = get_sub_field('block_option_padding');
		}

		if (!isset($block_hide)) {
			$block_hide = get_sub_field('block_option_hide');
		}

		if (!isset($block_background)) {
			$block_background = ($block_bg = get_sub_field('block_background')) ? $block_bg : 'block-bg-none';
		}

		if (!isset($block_background_image)) {
			$block_background_image = get_sub_field('block_background_image');
		}

		if (!isset($block_background_overlay)) {
			$block_background_overlay = get_sub_field('block_background_overlay');
		}

		if (!isset($block_animate)) {
			$block_animate = get_sub_field('block_animate');
		}

		$block_index = self::$block_index;
		$block_attributes['data-block-index'] = $block_index;

		// ID
		// $block_unique_id = get_sub_field('unique_id');

		if ($block_unique_id) {
			$block_attributes['id'] = sanitize_title($block_unique_id);
		}

		$sections = self::get_registered_sections();

		if (!empty($sections['fields'][0]['layouts'])) {
			foreach ($sections['fields'][0]['layouts'] as $layout_name => $layout) {
				if (!empty($layout['sub_fields'])) {
					foreach ($layout['sub_fields'] as $sub_field) {
						if (!empty($sub_field['block_data_attribute'])) {
							$block_attributes['data-'.str_replace('_', '-', strtolower($sub_field['name']))] = trim(get_sub_field($sub_field['name']));
						}
					}
				}
			}
		}

		// Class
		$block_attributes['class'] = array();

		if (!empty($block_custom_class)) {
			$block_attributes['class'] = array_merge($block_attributes['class'], explode(' ', $block_custom_class));
		}

		// Padding Options
		if ($block_padding) {
			$block_attributes['class'] = array_merge($block_attributes['class'], $block_padding);
		}

		// Screen Options
		if ($block_hide) {
			$small = in_array('small', $block_hide);
			$medium = in_array('medium', $block_hide);
			$large = in_array('large', $block_hide);
			$xlarge = in_array('xlarge', $block_hide);
			$block_attributes['class'] = array_merge($block_attributes['class'], self::css()->hide($small, $medium, $large, $xlarge)->class);
		}

		if ($block_animate) {
			$block_attributes['class'][] = 'block-animate';
		}

		// Background
		$block_attributes['class'][] = $block_background;

		if (!empty(self::$settings['background_colors'])) {
			foreach (self::$settings['background_colors'] as $color_key => $color_params) {
				$use_css_variable = (!empty($color_params['class']) && GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('css_options', 'add_custom_color_class'));

				if (!empty($color_params['_repeater_id']) && $block_background === 'block-bg-'.$color_params['_repeater_id'] && $use_css_variable) {
					if (!GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('css_options', 'enqueue_css')) {
						$block_background = '';
					}

					$block_attributes['class'][] = $color_params['class'];
				}
			}
		}

		// Background Image
		if ($block_background === 'block-bg-image' && $block_background_image) {
			$bg_image_attrs = [];
			$bg_image_style = '';

			if (is_string($block_background_image)) {
				$bg_image_style = "background-image: url('".esc_url($block_background_image)."'); ";
			} else {
				$image_src = self::get_prefered_image_size_src($block_background_image);

				if ($image_src) {
					$bg_image_style = "background-image: url('".$image_src."'); ";
				}
			}

			$bg_image_attrs['style'] = isset($block_attributes['style']) ?
				$block_attributes['style'].' '.$bg_image_style :
				$bg_image_style;

			$bg_image_attrs = apply_filters('grav_blocks_background_image_attributes', $bg_image_attrs, $block_attributes, $block_background_image);
			$block_attributes = array_merge($block_attributes, $bg_image_attrs);
		}

		// Background Overlay
		if (in_array($block_background, array('block-bg-image', 'block-bg-video')) && $block_background_overlay) {
			$block_attributes['class'][] = 'block-bg-overlay';
		}

		// Check for JS and CSS Files
		if ($block_path = self::get_path($block_name)) {
			\BlueprintBlocks\DependencyManager::add_block($block_name, $block_path);
		}

		// Add Aria Label
		$block_attributes['aria-label'] = ucwords(str_replace(array('-','_'), ' ', $block_name));

		// Style (Keep for Older Versions) #TODO Deprecate as this can be handled by "grav_blocks_attributes" hook
		$block_attributes['style'] = apply_filters('grav_block_background_style', (isset($block_attributes['style']) ? $block_attributes['style'] : ''));

		// Class (Keep for Older Versions) #TODO Deprecate as this can be handled by "grav_blocks_attributes" hook
		$block_attributes['class'] = GRAV_BLOCKS::css()->add($block_attributes['class'])->get();
		$block_attributes['class'] = explode(' ', $block_attributes['class']);

		// Allow filtering all attributes - remove empty values, but leave 0
		$block_attributes = apply_filters('grav_blocks_container_attributes', $block_attributes, $block_name, $block_variables);
		$block_attributes = array_filter($block_attributes, function($value) {
    		return ($value !== null && $value !== false && $value !== '');
		});

		return $block_attributes;
	}

	private static function format_block_attributes($attributes)
	{
		$block_attributes = array();

		foreach ($attributes as $key => $attribute)
		{
			$block_attributes[esc_attr($key)] = '"'.esc_attr(is_array($attribute) ? implode(' ', $attribute) : $attribute).'"';
		}

		$block_attributes = trim(urldecode(http_build_query($block_attributes, '', ' ')));

		return $block_attributes;
	}


	private static function get_block_format($block_only_variables, $handler_file, $background_groups = [])
	{
		$block_name = self::$current_block_name;

		$sections = self::get_registered_sections();

		if(!empty($sections['fields'][0]['layouts'][$block_name]['grav_blocks_settings']['repeater']))
		{
			self::$block_wrapped_repeater_index++;

			$block_attributes = self::get_block_attributes($block_name, $block_only_variables);

			$block_attributes['class'][] = 'block-'.$block_name.'-wrapped-repeater';
			$block_attributes['class'][] = 'block-wrapped-repeater';
			$block_attributes['class'][] = 'block-wrapped-repeater-index-'.self::$block_wrapped_repeater_index;
			$block_attributes['data-block-wrapped-repeater-index'] = self::$block_wrapped_repeater_index;

			$block_attributes = apply_filters('grav_block_attributes', $block_attributes, $block_name);

			$block_container_attributes = self::format_block_attributes($block_attributes);

			?>
			<div <?php echo $block_container_attributes;?>>
				<div class="block-wrapped-repeater-inner">
					<?php
					while ( have_rows('wrapped_repeater') )
					{
						self::$block_index++;
						the_row();

						self::display_block($block_name, $block_only_variables, $handler_file, $background_groups);
					}
					?>
				</div>
			</div>
			<?php
		}
		else
		{
			if ($block_name != 'global-block') {
				self::$block_index++;
			}

			self::display_block($block_name, $block_only_variables, $handler_file, $background_groups);
		}
	}


	private static function display_block($block_name = '', $block_variables = [], $handler_file = '', $background_groups = [])
	{
		self::$background_groups = $background_groups;

		if (!$handler_file) {
			$handler_file = self::get_path('handler.php');
		}

		$block_attributes = self::get_block_attributes($block_name, $block_variables);

		$block_attributes['class'][] = 'block-container';
		$block_attributes['class'][] = 'block-'.$block_name;
		$block_attributes['class'][] = 'block-index-'.self::$block_index;

		$block_attributes = apply_filters('grav_block_attributes', $block_attributes, $block_name);

		$block_container_attributes = self::format_block_attributes($block_attributes);

		do_action('grav_blocks_pre_block_output', $block_name, $block_attributes);

		include $handler_file;

		do_action('grav_blocks_post_block_output', $block_name, $block_attributes);
	}


	public static function has_block($block='', $object_id=0, $section='grav_blocks')
	{
		if(!$object_id)
		{
			$object_id = get_queried_object_id();
		}

		if(self::is_viewable())
		{
			if(get_field($section, $object_id))
			{
				while(the_flexible_field($section, $object_id))
				{
					if(strtolower(str_replace('_', '-', $block)) === strtolower(str_replace('_', '-', get_row_layout())))
					{
						reset_rows( true );
						return true;
					}
				}
			}
		}


		return false;
	}

	/**
	 * Returns the Array of locations that the blocks are attached to.
	 *
	 * Has Filter:
	 * Allows to be filtered with apply_filters( 'grav_block_locations', $locations_formatted )
	 *
	 * @return array
	 */
	public static function get_locations($format = 'acf')
	{
		self::get_settings(true);
		$locations = array();
		$locations_formatted = array();


		if($format == 'viewable')
		{
			$locations['post_types'] = isset(self::$settings['post_types']) ? self::$settings['post_types'] : '';
			$locations['templates'] = isset(self::$settings['templates']) ? self::$settings['templates'] : '';
			$locations['taxonomies'] = isset(self::$settings['taxonomies']) ? self::$settings['taxonomies'] : '';
			$locations['option_pages'] = isset(self::$settings['option_pages']) ? self::$settings['option_pages'] : '';
			return $locations;
		}


		if(!empty(self::$settings['post_types']))
		{
			foreach (self::$settings['post_types'] as $location)
			{
				$locations[] = array('key' => 'post_type', 'value' => $location);
			}
		}

		if(!empty(self::$settings['templates']))
		{
			foreach (self::$settings['templates'] as $location)
			{
				$locations[] = array('key' => 'page_template', 'value' => $location);
			}
		}

		if(!empty(self::$settings['taxonomies']))
		{
			foreach (self::$settings['taxonomies'] as $location)
			{
				$locations[] = array('key' => 'taxonomy', 'value' => $location);
			}
		}

		$group = 0;


		foreach ($locations as $location)
		{
			$locations_formatted[] = array (
					array (
						'param' => $location['key'],
						'operator' => '==',
						'value' => $location['value'],
						'order_no' => 0,
						'group_no' => $group++,
					),
				);
		}

		$locations_formatted = apply_filters( 'grav_block_locations', $locations_formatted );

		return $locations_formatted;
	}

	/**
	 * Outputs the Markup for the Block
	 *
	 * @param string $block - This is the name of the block folder to retrieve and output
	 *
	 * @return void
	 */
	public static function get_block($block = '', $block_variables = array(), $block_attributes = array())
	{
		if (!empty($block_variables)) {
			extract($block_variables);
		}

		$path = self::get_path($block);

		if (!$path || !file_exists($path.'/block.php')) {
			return;
		}

		do_action('grav_blocks_display_before', $block, $block_variables, $block_attributes);

		include($path.'/block.php');

		do_action('grav_blocks_display_after', $block, $block_variables, $block_attributes);
	}

	/**
	 * Outputs the Markup for a background Video
	 *
	 * @param string $block - This is the name of the block folder
	 *
	 * @return void
	 */
	public static function get_block_background_video_markup($block, $block_variables = array())
	{
		if (!in_array($block, self::get_block_background_allowed_video())) {
			return;
		}

		if (!empty($block_variables)) {
			extract($block_variables);
		}

		$background = isset($block_background) ? $block_background : get_sub_field('block_background');

		if ($background === 'block-bg-video') {
			$block_video_type = isset($block_video_type) ? $block_video_type : get_sub_field('block_background_video_type');
			$block_video_url = isset($block_video_url) ? $block_video_url : get_sub_field('block_background_video_'.$block_video_type);
			$block_video_poster = isset($block_video_poster) ? $block_video_poster : get_sub_field('block_background_image');
		}

		if (!empty($block_video_url)) {
			?>
			<video class="block-video-container" src="<?php echo $block_video_url;?>" autoplay loop muted <?php if(!empty($block_video_poster['sizes']['large'])){?>poster="<?php echo $block_video_poster['sizes']['large'];?>" <?php } ?>preload="auto"></video>
			<?php
		}
	}

	/**
	 * Returns the specified setting for the block or array of all settings
	 *
	 * @param string $block - This is the name of the block folder to retrieve
	 * @param string $setting - This is the setting to retrieve
	 *
	 * @return array if no setting specified, string if setting is specified
	 */
	public static function get_block_settings($block = '', $setting = '')
	{
		$block = ($block !== '') ? $block : self::$current_block_name;
		$path = self::get_path($block);

		if ($path && file_exists($path.'/block_fields.php')) {
			$fields = include($path.'/block_fields.php');

			$settings = ($setting === '') ?
				($fields['grav_blocks_settings'] ?? '') :
				($fields['grav_blocks_settings'][$setting] ?? '');

			return $settings;
		}

		// Reset Current Block
		$block = '';

		return false;
	}

	/**
	 * Returns the Enabled Blocks
	 *
	 * @return array
	 */
	public static function get_blocks()
	{
		self::get_settings(true);

		$blocks = array();
		$available_blocks = self::get_available_blocks();

		if ($available_blocks) {
			$enabled_blocks = array();

			foreach (self::$settings as $setting_key => $setting_value) {
				if (strpos($setting_key, 'blocks_enabled_') !== false && is_array($setting_value)) {
					$enabled_blocks = array_merge($enabled_blocks, $setting_value);
				}
			}

			$blocks = array_intersect_key($available_blocks, array_flip($enabled_blocks));
		}

		return $blocks;
	}

	/**
	 * Returns all the available blocks
	 *
	 * Has Filter:
	 * Allows to be filtered with apply_filters( 'grav_blocks', $blocks );
	 *
	 * @return array
	 */
	public static function get_available_blocks()
	{
		// Return Cache if exists
		if (isset(self::$cache['filtered_blocks'])) {
			return self::$cache['filtered_blocks'];
		}

		global $block;

		$blocks = array();
		$plugin_blocks = array();
		$theme_blocks = array();

		// Get blocks from the Plugin
		$plugin_blocks_dir = self::get_path();

		if ($plugin_blocks_dir) {
			$plugin_blocks = array_filter(glob($plugin_blocks_dir.'*'), 'is_dir');
		}

		// Get blocks from the Theme
		$theme_blocks_dir = get_template_directory().'/grav-blocks/';

		if ($theme_blocks_dir && is_dir($theme_blocks_dir)) {
			$theme_blocks = array_filter(glob($theme_blocks_dir.'*'), 'is_dir');
		}

		/* These are just placed to ignore any php warnings when including the fields */
		$block_backgrounds = '';
		$block_background_image = '';

		if ($plugin_blocks) {
			foreach ($plugin_blocks as $dir) {
				$block = basename($dir);

				if (file_exists($dir.'/block_fields.php')) {
					$fields = include($dir.'/block_fields.php');
					$label = (!empty($fields['label'])) ? $fields['label'] : $block;
					$blocks[$block] = array('label' => $label, 'path' => $dir, 'group' => (!empty($fields['grav_blocks_settings']['group']) ? $fields['grav_blocks_settings']['group'] : 'default'));
				}
			}
		}

		if ($theme_blocks) {
			foreach ($theme_blocks as $dir) {
				$block = basename($dir);

				if (file_exists($dir.'/block_fields.php')) {
					$fields = include($dir.'/block_fields.php');
					$label = (!empty($fields['label'])) ? $fields['label'] : $block;
					$blocks[$block] = array('label' => $label, 'path' => $dir, 'group' => (!empty($fields['grav_blocks_settings']['group']) ? $fields['grav_blocks_settings']['group'] : 'theme'));
				}
			}
		}

		// Reset Current Block
		$block = '';

		// Apply Filters to allow others to filter the blocks used.
		$filtered_blocks = apply_filters('grav_blocks', $blocks);

		self::$cache['filtered_blocks'] = $filtered_blocks;

		return $filtered_blocks;
	}

	/**
	 * Returns all the available block groups
	 *
	 * @return array
	 */
	public static function get_available_block_groups()
	{
		$block_groups = array();

		foreach (self::get_available_blocks() as $block => $block_params) {
			$block_groups[str_replace(' ', '_', strtolower($block_params['group']))][$block] = $block_params['label'];
		}

		return $block_groups;
	}

	/**
	 * Gets the correct path of a file or directory for a Block asset.
	 * Allows to be overwritten by the theme if the theme has a block asset in /grav-blocks/
	 *
	 * @param string $path
	 *
	 * @return string|false
	 */
	public static function get_path($path='')
	{
		if (!$path) {
			$plugin_path = plugin_dir_path( __FILE__ ).'grav-blocks/';

			return is_dir($plugin_path) ? $plugin_path : false;
		}

		if (is_dir(get_template_directory().'/grav-blocks/'.$path.'/')) {
			return get_template_directory().'/grav-blocks/'.$path;
		} else if (file_exists(get_template_directory().'/grav-blocks/'.$path)) {
			return get_template_directory().'/grav-blocks/'.$path;
		} else if (is_dir(plugin_dir_path( __FILE__ ).'grav-blocks/'.$path.'/')) {
			return plugin_dir_path( __FILE__ ).'grav-blocks/'.$path;
		} else if (file_exists(plugin_dir_path( __FILE__ ).'grav-blocks/'.$path)) {
			return plugin_dir_path( __FILE__ ).'grav-blocks/'.$path;
		} else if (file_exists(get_template_directory().'/grav-blocks/'.str_replace('-', '_', $path))) {
			return get_template_directory().'/grav-blocks/'.str_replace('-', '_', $path);
		}

		return false;
	}

	/**
	 * Returns the Real IP from the user
	 *
	 * @return string
	 */
	public static function get_real_ip()
    {
        foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR') as $server_ip) {
            if (!empty($_SERVER[$server_ip]) && is_string($_SERVER[$server_ip])) {
                if ($ip = trim(reset(explode(',', $_SERVER[$server_ip])))) {
	            	return $ip;
	            }
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns the Settings Fields for specifc location.
     *
     * @param string $location
     *
     * @return array
     */
	private static function get_settings_fields($location = 'general')
	{
		switch ($location)
		{
			case 'advanced':
				$advanced_options = array(
					'filter_content' => 'Gravitate Blocks will be added to the end of your content. <span class="extra-info">( Using "the_content" filter )</span>',
					'filter_excerpt' => 'Filter the_excerpt() with Block Fields (all postmeta fields) when the_excerpt() or the_content() is empty.',
					'after_title' => 'Place Gravitate Blocks directly after the title in the WordPress admin. <span class="extra-info">( changes position using acf_after_title )</span>',
					'hide_content' => 'Remove the WordPress content box from Gravitate Blocks enabled pages. <span class="extra-info">( if content has already been entered it may still show on the front end of the website. )</span>',
				);
				// $css_options = array(
				// 	// 'add_custom_color_class' => 'Allow customization of CSS class names for the background color options.',
				// 	// 'disable_colorpicker' => 'Disable color picker ( Use this to force your own css class names ).',
				// 	// 'enqueue_css' => 'Background color CSS will be added to the website\'s header. <span class="extra-info">( Needed for custom background colors, images, etc. )</span>',
				// 	'use_default' => 'Use the default Gravitate Blocks CSS. <span class="extra-info">( Affects padding and some basic styling. )</span>',
				// 	'use_foundation' => 'Use the <a target="_blank" href="http://foundation.zurb.com/sites/docs/">Foundation</a> CSS grid. <span class="extra-info">( This will add the foundation CSS file to your site. )</span>',
				// );

				// $foundation_options = array(
				// 	'f5' => '<a href="http://foundation.zurb.com/sites/docs/v/5.5.3/" target="_blank">5.5.3</a>',
				// 	'f6' => '<a href="http://foundation.zurb.com/sites/docs/grid.html" target="_blank">6.2.0</a>',
				// 	'f6flex' => '<a href="http://foundation.zurb.com/sites/docs/flex-grid.html" target="_blank">6.2.0</a> <span class="extra-info">( flex grid )</span>',
				// );

				$search_options = array(
					'include_in_search' => 'Includes Block Fields (all postmeta fields) in the search criteria.',
				);

				$fields = array();
				$fields['advanced_options'] = array('type' => 'checkbox', 'label' => 'Advanced Options', 'options' => $advanced_options, 'description' => '');
				// $fields['css_options'] = array('type' => 'checkbox', 'label' => 'CSS Settings', 'options' => $css_options, 'description' => '');
				// $fields['foundation'] = array('type' => 'radio', 'label' => 'Foundation Version', 'options' => $foundation_options, 'description' => 'If you are using the foundation grid, this will determine which version of the grid to use.');
				$fields['search_options'] = array('type' => 'checkbox', 'label' => 'Search Settings', 'options' => $search_options, 'description' => '');

			break;

			default:
			case 'general':
				$post_types = self::get_usable_post_types();
				$template_options = self::get_template_options();
				$block_groups = self::get_available_block_groups();

				$option_pages = self::get_acf_option_pages(true);

				$taxonomies = self::get_usable_taxonomies(true);

				$background_colors_repeater = array();
				$background_colors_repeater['name'] = array('type' => 'text', 'label' => 'Name', 'description' => 'Name of color');

				// if(GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('css_options', 'add_custom_color_class'))
				// {
				// 	$background_colors_repeater['class'] = array('type' => 'text', 'label' => 'CSS Class Name', 'description' => '( Optional )');
				// }
				//
				// if(!GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('css_options', 'disable_colorpicker'))
				// {
				// 	$background_colors_repeater['value'] = array('type' => 'colorpicker', 'label' => 'Value', 'description' => 'Use Hex values (ex. #ff0000)');
				// }

				$fields = array();

				foreach ($block_groups as $group => $blocks) {
					foreach($blocks as $block_slug => $block_label) {
						if ($block_settings = self::get_block_settings($block_slug)) {
							$block_settings['label'] = $block_label;
							$blocks[$block_slug] = $block_settings;
						}
					}

					$description = ($group == 'default') ? 'Determine what default blocks will be available.' : '';
					$fields['blocks_enabled_'.$group] = array('type' => 'checkbox', 'label' => ucwords(str_replace('_', ' ', $group)).' Blocks', 'options' => $blocks, 'description' => $description);
				}

				$fields['google_maps_api_key'] = array('type' => 'text', 'label' => 'Google Maps API Key', 'description' => 'Add a Google maps api key');
				$fields['google_maps_styles'] = array('type' => 'textarea', 'label' => 'Google Maps Custom Styles', 'description' => 'Add your own custom styles. We reccommend using a site like <a href="https://snazzymaps.com/" target="_blank">Snazzy Maps</a> to create custom map styles.');
				$fields['google_maps_default_lat_lng'] = array('type' => 'text', 'label' => 'Google Maps Default Latitude', 'description' => 'Set the default latitude and longitude for Google Maps fields used in block editors. This value must be comma separated. Ex: <code>45.5426225,-122.7944697</code>');
				$fields['google_maps_default_zoom'] = array('type' => 'text', 'label' => 'Google Maps Default Zoom Level', 'description' => 'Set the default zoom level for Google Maps fields used in block editors.');
				// $fields['background_colors'] = array('type' => 'repeater', 'label' => 'Background Color Options', 'fields' => $background_colors_repeater, 'description' => 'Choose what Background Colors you want to have the Gravitate Blocks.');
				$fields['post_types'] = array('type' => 'checkbox', 'label' => 'Post Types', 'options' => $post_types, 'description' => 'Determine the post types that Gravitate Blocks will appear on.');
				$fields['templates'] = array('type' => 'checkbox', 'label' => 'Page Templates', 'options' => $template_options, 'description' => 'Determine the page templates that Gravitate Blocks will appear on.');

				if (!empty($taxonomies)) {
					$fields['taxonomies'] = array('type' => 'checkbox', 'label' => 'Taxonomies', 'options' => $taxonomies, 'description' => 'Determine the Taxonomy Archive Pages that Gravitate Blocks will appear on.');
				}

				if (!empty($option_pages)) {
					$fields['option_pages'] = array('type' => 'checkbox', 'label' => 'Option Pages', 'options' => $option_pages, 'description' => 'Determine the ACF Option Pages that Gravitate Blocks will appear on.');
				}

			break;
		}

		return apply_filters('grav_blocks_settings_fields', $fields, $location);
	}


	/**
	 * Gets current version of Grav Blocks
	 *
	 *
	 * @return
	 */
	public static function get_version()
	{
		return self::$version;
	}


	/**
	 * Gets current version of foundation
	 *
	 *
	 * @return
	 */
	// public static function get_foundation_version()
	// {
	// 	$foundation_version = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('foundation', 0);
	// 	return $foundation_version;
	// }

	/**
	 * Gets current version of foundation
	 *
	 *
	 * @return
	 */
	// public static function get_foundation_file_name()
	// {
	// 	$foundation_version = self::get_foundation_version();
	// 	switch ($foundation_version){
	// 		case 'f5':
	// 			$foundation_file_name = 'foundation5';
	// 			break;

	// 		case 'f6':
	// 			$foundation_file_name = 'foundation6';
	// 			break;

	// 		default:
	// 		case 'f6flex':
	// 			$foundation_file_name = 'foundation6flex';
	// 			break;

	// 	}
	// 	return $foundation_file_name;
	// }


	/**
	 * Runs the Admin Page and outputs the HTML
	 *
	 * @return void
	 */
	public static function admin()
	{
		// Get Settings
		self::get_settings(true);

		// Save Settings if POST
		$response = GRAV_BLOCKS_PLUGIN_SETTINGS::save_settings();

		if ($response['error']) {
			$error = 'Error saving Settings. Please try again.';
		} else if ($response['success']) {
			$success = 'Settings saved successfully.';
		}

		add_thickbox();

		?>
		<div class="wrap grav-blocks">
			<header>
				<h1><img itemprop="logo" src="//uploads.gravitatedesign.com/2016/03/27080812/grav_logo.png" alt="Gravitate"> Blocks</h1>
			</header>
			<main>
				<h4 class="blocks-version">Version <?php echo self::$version;?></h4>
				<?php
				if (!function_exists('acf_add_local_field_group'))
				{
					self::acf_notice(false);
				}
				?>
				<?php if (!empty($error)) { ?><div class="error"><p><?php echo $error; ?></p></div><?php } ?>
				<?php if (!empty($success)) {?><div class="updated"><p><?php echo $success; ?></p></div><?php } ?>
			</main>
			<br>
			<div class="gravitate-redirects-page-links">
				<a href="<?php echo self::$page;?>&section=general" class="<?php echo self::get_current_tab($_GET['section'] ?? '', 'general'); ?>">General</a>
				<a href="<?php echo self::$page;?>&section=advanced" class="<?php echo self::get_current_tab($_GET['section'] ?? '', 'advanced'); ?>">Advanced</a>
				<a href="<?php echo self::$page;?>&section=developers" class="<?php echo self::get_current_tab($_GET['section'] ?? '', 'developers'); ?>">Developers</a>
				<a href="<?php echo self::$page;?>&section=usage" class="<?php echo self::get_current_tab($_GET['section'] ?? '', 'usage'); ?>">Block Usage</a>
			</div>
			<br>
			<br>
			<?php

			$section = (!empty($_GET['section']) ? $_GET['section'] : 'settings');

			switch ($section)
			{
				case 'advanced':
					self::form('advanced');
					break;

				case 'developers':
					self::developers();
					break;

				case 'usage':
					self::blocks_usage();
					break;

				default:
				case 'settings':
					self::form();
					break;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Outputs the Form with the correct fields
	 *
	 * @param string $location
	 *
	 * @return type
	 */
	private static function form($location = 'general')
	{
		// Get Form Fields
		switch ($location)
		{
			default;
			case 'general':
				$fields = self::get_settings_fields();
				break;

			case 'advanced':
				$fields = self::get_settings_fields('advanced');
				break;
		}

		GRAV_BLOCKS_PLUGIN_SETTINGS::get_form($fields);
	}

	private static function developers()
	{
		wp_enqueue_style('prism', plugin_dir_url(__FILE__).'library/css/admin/prism.css', [], null);
		wp_enqueue_script('prism', plugin_dir_url(__FILE__).'library/js/admin/prism.js', [], null, true);

		include_once 'library/includes/developer.php';
	}

	private static function blocks_usage() {
		include_once 'library/includes/blocks-usage.php';
	}

	/**
	 * Filters a string to be in a title format
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public static function unsanitize_title($title)
	{
		return ucwords(str_replace(array('_', '-'), ' ', $title));
	}

	/**
	 * Enqueue Admin Scripts
	 *
	 * @param $hook
	 *
	 * @return runs enqueue for admin
	 */
	public static function enqueue_admin_files($hook)
	{
		wp_enqueue_style('grav_blocks_admin_css', plugin_dir_url(__FILE__) . 'library/css/master.min.css', true, '1.0.0');

		wp_enqueue_style('layout-icons', plugin_dir_url(__FILE__).'library/css/admin/gravblock-col-layouts.css', [], null);

		wp_enqueue_script('grav_blocks_controls_js', plugin_dir_url(__FILE__) . 'library/js/block-admin.js', array('jquery'), true, true);

		wp_enqueue_style('grav_blocks_icons_css', 'https://i.icomoon.io/public/790bec4572/GravitateBlocks/style.css', true, '1.1.0');

		if ('toplevel_page_gravitate-blocks' != $hook) {
	        return;
	    }
	}

	/**
	 * Add any necessary JS to footer
	 *
	 * @param
	 *
	 * @return
	 */
	public static function add_footer_js()
	{
		$output_default_js = apply_filters('grav_blocks_output_default_js', true);

		if (!$output_default_js) {
			return;
		}

		$colorbox_params = [
			'.block-link-video' => [
				'iframe' => true,
				'height' => '80%',
				'width' => '80%'
			],
			'.block-link-gallery' => [
				'rel' => 'block-link-gallery',
				'height' => '80%',
				'width' => '80%',
				'transition' => 'fade'
			],
			'.grav-inline' => [
				'inline' => 'true',
				'height' => '80%',
				'width' => '80%',
				'transition' => 'fade'
			]
		];

		$colorbox_params = apply_filters('grav_blocks_colorbox_params', $colorbox_params);

		if (!count($colorbox_params)) {
			return;
		}

		wp_register_script(
			'grav_blocks_default_js',
			plugin_dir_url(__FILE__).'library/js/blocks-colorbox-init.js',
			['jquery'],
			filemtime(plugin_dir_path(__FILE__).'library/js/blocks-colorbox-init.js'),
			true
		);

		wp_localize_script('grav_blocks_default_js', 'blocksColorboxConfig', [
			'params' => $colorbox_params,
			'scriptUrl' => plugin_dir_url(__FILE__).'library/dependencies/js/jquery.colorbox-min.js'
		]);

		wp_enqueue_script('grav_blocks_default_js');
	}

	/**
	 * Check for the Post or Queried Object ID
	 *
	 * @param none
	 *
	 * @return int | false
	 */
	public static function get_queried_object_id()
	{
		$post_id = ( ($query = get_queried_object()) && !empty($query->ID) ) ? $query->ID : false;

		return $post_id;
	}

	/**
	 * Check if blocks are viewable on the front end
	 *
	 * @param none
	 *
	 * @return boolean
	 */
	public static function is_viewable($object=0)
	{
		$is_viewable = false;

		if (!$object) {
			$object = self::get_queried_object_id();
		}

		if ($object) {
			$locations = self::get_locations('viewable');

			if (!empty($locations['post_types']) && is_numeric($object)) {
				$post_type = get_post_type($object);

				if (!empty($post_type) && in_array($post_type, $locations['post_types'])) {
					$is_viewable = true;
				}
			}

			if (!empty($locations['templates']) && is_numeric($object)) {
				$is_default = (get_page_template_slug($object) == '' && in_array('default', $locations['templates']));

				if ($is_default || in_array(get_page_template_slug($object), $locations['templates'])) {
					$is_viewable = true;
				}
			}

			if (!empty($locations['taxonomies'])) {
				$queried_object = get_queried_object();

				if (!empty($queried_object->taxonomy)) {
					$queried_object = get_taxonomy($queried_object->taxonomy);

					if (!empty($queried_object->name) && in_array($queried_object->name, $locations['taxonomies'])) {
						$is_viewable = true;
					}
				}
			}

			if (!empty($locations['option_pages']) && in_array($object, $locations['option_pages'])) {
				$is_viewable = true;
			}
		}

		$is_viewable = apply_filters( 'grav_is_viewable', $is_viewable );

		return $is_viewable;
	}

	/**
	 * Filters the content and adds content blocks to the end of the content
	 *
	 * @param string $content
	 *
	 * @return
	 */
	public static function filter_content($content)
	{
		ob_start();

		self::display();

		$blocks = ob_get_contents();
		ob_end_clean();

		return $content . $blocks;
	}

	/**
	 * Gets acf registered Option Pages
	 *
	 * @param $titles_only (bool)
	 *
	 * @return
	 */
	public static function get_acf_option_pages($titles_only=false)
	{
		$pages = array();

		if (!empty($GLOBALS['acf_options_pages'])) {
			if ($titles_only) {
				foreach ($GLOBALS['acf_options_pages'] as $key => $page) {
					$pages[$key] = $page['page_title'];
				}
			} else {
				$pages = $GLOBALS['acf_options_pages'];
			}
		}

		return $pages;
	}

	/**
	 * Gets usable taxonomies
	 *
	 * @param
	 *
	 * @return
	 */
	public static function get_usable_taxonomies($titles_only=false)
	{
		$taxonomies = array();

		foreach (get_taxonomies(array('public' => true), 'objects') as $taxonomy) {
			if ($taxonomy->name !== 'post_format') {
				if ($titles_only) {
					$taxonomies[$taxonomy->name] = $taxonomy->label;
				} else {
					$taxonomies[] = $taxonomy;
				}
			}
		}

		return $taxonomies;
	}

	/**
	 * Gets usable post types
	 *
	 * @param
	 *
	 * @return
	 */
	public static function get_usable_post_types()
	{
		$posts = get_post_types();
		$post_types = array();

		foreach ($posts as $post_type) {
			if (!in_array($post_type, self::$posts_to_exclude)) {
				$post_types[$post_type] = self::unsanitize_title($post_type);
			}
		}

		// TODO add filter here for $post_types

		return $post_types;
	}

	/**
	 * Gets template options
	 *
	 * @param
	 *
	 * @return
	 */
	public static function get_template_options()
	{
		// TODO add filter here for $templates_to_exclude

		$templates = get_page_templates();
		$template_options = array();

		if (!in_array('default', array_map('strtolower', $templates)) && !in_array('page.php', array_map('strtolower', $templates)) && file_exists(get_template_directory().'/page.php')) {
			$templates = array_merge(array('Default' => 'default'), $templates);
		}

		foreach ($templates as $key => $template) {
			$template_options[$template] = self::unsanitize_title($key);
		}

		return $template_options;
	}

	/**
	 * Gets current tab and sets active state
	 *
	 * @param string $current
	 * @param string $section
	 *
	 * @return
	 */
	public static function get_current_tab($current = '' , $section = '')
	{
		if ($current == $section || ($current == '' && $section == 'general')) {
			return 'active';
		}
	}

	/**
	 * Converts a URL to a verified Vimeo ID
	 *
	 * @param  $url  (string) Url of a defined Vimeo Video.
	 *
	 * @return (int)
	 * @author GG
	 *
	 **/
	public static function get_vimeo_id($url)
	{
		preg_match('/([0-9]+)/', $url, $matches);

		if (!empty($matches[1]) && is_numeric($matches[1])) {
			return $matches[1];
		} else if (strpos($url, 'http') === false) {
			return $url;
		}

		return 0;
	}

	/**
	 * Converts a URL to a verified YouTube ID
	 *
	 * @param  $url  (string) Url of a defined Youtube Video.
	 *
	 * @return int
	 * @author GG
	 **/
	public static function get_youtube_id($url)
	{
		if (!$pos = strpos($url, 'youtu.be/')) {
			// $pos = strpos($url, '/watch?v=');
			$pos = strpos($url, 'v=');
		}

		if ($pos) {
			$split = explode("?", substr($url, ($pos+9)));
			$split = explode("&", $split[0]);

			return $split[0];
		} else if ($pos = strpos($url, '/embed/')) {
			$split = explode("?", substr($url, ($pos+7)));

			return $split[0];
		} else if ($pos = strpos($url, '/v/')) {
			$split = explode("?", substr($url, ($pos+3)));

			return $split[0];
		} else if (!$pos && strpos($url, 'http') === false) {
			return $url;
		}

		return 0;
	}

	/**
	 * Converts a URL to a verified YouTube video ID function
	 *
	 * @param  $url  (string) Url of a defined Youtube Video.
	 *
	 * REQUIRES: function grav_get_youtube_id()
	 *
	 * @return (str)
	 * @author GG
	 *
	 **/
	public static function get_video_url($url)
	{
		$autoplay = (strpos($url, 'autoplay=0') || strpos($url, 'autoplay=false')) ? 0 : 1;

		if (strpos($url, 'vimeo')) {
			$id = self::get_vimeo_id($url);

			if (is_numeric($id)) {
				return 'https://player.vimeo.com/video/'.$id.'?autoplay='.$autoplay;
			}

			return $url;
		}

		if ($id = self::get_youtube_id($url)) {
			return 'https://www.youtube.com/embed/'.$id.'?rel=0&amp;iframe=true&amp;wmode=transparent&amp;autoplay='.$autoplay;
		}

		$custom_video_url = apply_filters('grav_blocks_video_url', $url);

		if ($custom_video_url) {
			return $custom_video_url;
		}

		return '';
	}

	public static function column_width_options()
	{
		$column_width_options = array(
			2 => 'X-Small',
			4 => 'Small',
			6 => 'Medium',
			8 => 'Large',
			10 => 'X-Large',
		);

		// allow filtering of column sizes for the media with content block
		$filtered_column_width_options = apply_filters( 'grav_column_widths', $column_width_options );

		return $filtered_column_width_options;
	}

	/**
	 * Converts a single array of link options into multiple fields
	 *
	 * @param  $label, $includes, $show_text
	 *
	 * @return array
	 * @author GG & BF
	 *
	 **/
	public static function get_link_fields($label = 'link', $includes = array(), $show_text = true, $post_types = array(0 => 'all'), $conditional_logic = array())
	{
		$post_types = array();

		foreach (get_post_types(array('public' => true)) as $post_type) {
			if ($count = wp_count_posts($post_type)->publish) {
				if ($count > 0 && $post_type != 'post') {
					$post_types[$post_type] = $count;
				}
			}
		}

		$post_types['post'] = wp_count_posts('post');

		if (!empty($post_types)) {
			asort($post_types);
			$post_types = array_keys($post_types);
		}

		$params = [];
		$supports_button_styles = true;

		if (is_array($label)) {
			$params = $label;

			$arr = $label;
			$label = isset($arr['label']) ? $arr['label'] : (isset($arr['name']) ? ucwords(str_replace(array('_', '-'), ' ', $arr['name'])) : 'link');
			$name = isset($arr['name']) ? sanitize_title($arr['name']) : sanitize_title($label);
			$includes = isset($arr['includes']) ? $arr['includes'] : array();
			$show_text = isset($arr['show_text']) ? $arr['show_text'] : true;
			$post_types = isset($arr['post_types']) ? $arr['post_types'] : $post_types;
			$conditional_logic = isset($arr['conditional_logic']) ? $arr['conditional_logic'] : array();
			$supports_button_styles = $arr['supports_button_styles'] ?? $supports_button_styles;
		}

		if(empty($name)) {
			$name = sanitize_title($label);
		}

		global $block;

		$allowed_options = array(
			'none' => 'None',
			'page' => 'Page Link',
			'url' => 'URL',
			'file' => 'File Download',
			'video' => 'Play Video',
		);

		$allowed_fields = (!empty($includes)) ? $includes : $allowed_options;

		// Format the Array if it is not formatted correctly
		if (!empty($conditional_logic) && is_array($conditional_logic)) {
			// Check if it has Wrapping Array
			if (empty($conditional_logic[0][0])) {
				$conditional_logic = array($conditional_logic);
			}

			// If it is still not Wrapping then add another
			if (empty($conditional_logic[0][0])) {
				$conditional_logic = array($conditional_logic);
			}
		} else {
			$conditional_logic = array();
		}

		$label_title = ucwords($label);
		$fields = array();

		reset($allowed_fields);
		$default = key($allowed_fields);

		$field_key_base = 'field_'.$block.'_'.$name;

		if (isset($params['key_modifier']) && is_string($params['key_modifier'])) {
			$field_key_base .= '_'.$params['key_modifier'];
		}

		$fields[] = array (
			'key' => $field_key_base.'_type',
			'label' => $label_title.' Type',
			'name' => $name.'_type',
			'type' => 'radio',
			'layout' => 'horizontal',
			'wrapper' => array (
				'width' => $params['column_width_type'] ?? $params['column_width'] ?? '',
				'class' => '',
				'id' => '',
			),
			'choices' => $allowed_fields,
			'default_value' => $default,
			'allow_null' => 0,
			'multiple' => 0,
			'conditional_logic' => (!empty($conditional_logic) ? $conditional_logic : 0)
		);

		$field_conditional_logic = array (
			array (
				array (
					'field' => $field_key_base.'_type',
					'operator' => '!=',
					'value' => 'none',
				),
			),
		);

		if (!empty($conditional_logic)) {
			$field_conditional_logic[0][] = $conditional_logic[0][0];
		}

		if ($show_text) {
			$fields[] = array (
				'key' => $field_key_base.'_text',
				'label' => $label_title.' Text',
				'name' => $name.'_text',
				'type' => 'text',
				'required' => 1,
				'conditional_logic' => $field_conditional_logic,
				'wrapper' => array (
					'width' => $params['column_width_text'] ?? $params['column_width'] ?? '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			);
		}

		foreach ($allowed_fields as $allowed_field_key => $allowed_field_label) {
			$field_conditional_logic[0][0]['operator'] = '==';
			$field_conditional_logic[0][0]['value'] = $allowed_field_key;

			switch ($allowed_field_key) {
				case 'none':
					break;

				case 'url':
					$fields[] = array (
						'key' => $field_key_base.'_url',
						'label' => $allowed_field_label,
						'name' => $name.'_url',
						'type' => 'text',
						'required' => 1,
						'conditional_logic' => $field_conditional_logic,
						'wrapper' => array (
							'width' => $params['column_width_' . $allowed_field_key] ?? $params['column_width'] ?? '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => 'http://',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					);
					break;

				case 'page':
					$fields[] = array (
						'key' => $field_key_base.'_page',
						'label' => $allowed_field_label,
						'name' => $name.'_page',
						'type' => 'page_link',
						'required' => 1,
						'conditional_logic' => $field_conditional_logic,
						'wrapper' => array (
							'width' => $params['column_width_' . $allowed_field_key] ?? $params['column_width'] ?? '',
							'class' => '',
							'id' => '',
						),
						'post_type' => $post_types,
						'allow_null' => 0,
						'multiple' => 0,
					);
					break;

				case 'file':
					$fields[] = array (
						'key' => $field_key_base.'_file',
						'label' => $allowed_field_label,
						'name' => $name.'_file',
						'type' => 'file',
						'required' => 1,
						'conditional_logic' => $field_conditional_logic,
						'wrapper' => array (
							'width' => $params['column_width_' . $allowed_field_key] ?? $params['column_width'] ?? '',
							'class' => '',
							'id' => '',
						),
						'save_format' => 'url',
						'library' => 'all',
					);
					break;

				case 'video':
					$fields[] = array (
						'key' => $field_key_base.'_video',
						'label' => $allowed_field_label,
						'name' => $name.'_video',
						'type' => 'text',
						'required' => 1,
						'instructions' => 'This works for Vimeo or Youtube. Just paste in the url to the video you want to show.',
						'conditional_logic' => $field_conditional_logic,
						'wrapper' => array (
							'width' => $params['column_width_' . $allowed_field_key] ?? $params['column_width'] ?? '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => 'http://',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					);
					break;

				default:
					$custom_acf_field = apply_filters(
						'grav_blocks_get_link_fields_' . $allowed_field_key,
						[
							'key' => $field_key_base.'_'.$allowed_field_key,
							'label' => $allowed_field_label,
							'name' => $name.'_'.$allowed_field_key,
							'type' => 'text',
							'required' => 1,
							'instructions' => '',
							'conditional_logic' => $field_conditional_logic,
							'wrapper' => array (
								'width' => $params['column_width_' . $allowed_field_key] ?? $params['column_width'] ?? '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => ''
						]
					);

					if ($custom_acf_field && is_array($custom_acf_field)) {
						$fields[] = $custom_acf_field;
					}

					break;
			}
		}

		if ($supports_button_styles) {
			$button_style_options = apply_filters('grav_blocks_button_style_options', [
				'' => 'Primary',
				'button--secondary' => 'Secondary'
			]);

			if (is_array($button_style_options) && $button_style_options) {
				$fields[] = array (
					'key' => $field_key_base.'_style',
					'label' => 'Style',
					'name' => $name.'_style',
					'type' => 'select',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array (
						array (
							array (
								'field' => $field_key_base.'_type',
								'operator' => '!=',
								'value' => 'none',
							),
						),
					),
					'wrapper' => array (
						'width' => $params['column_width_style'] ?? $params['column_width'] ?? '',
						'class' => '',
						'id' => '',
					),
					'choices' => $button_style_options,
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,         // allows for multi-select
					'ui' => 0,               // creates a more stylized UI
					'ajax' => 0,
					'placeholder' => '',
					'disabled' => 0,
					'readonly' => 0,
				);
			}
		}

		$filtered_fields = apply_filters('grav_block_link_fields', $fields);

		return array('grav_link_fields' => $filtered_fields);
	}

	public static function get_link_url($field)
	{
		// TODO make this a public filterable array
		$allowed_options = array(
			'none' => 'None',
			'page' => 'Page Link',
			'url' => 'URL',
			'file' => 'File Download',
			'video' => 'Play Video',
		);

		if ($type = get_sub_field($field.'_type')) {
			if ($type != 'none') {
				$url = get_sub_field($field.'_'.$type);

				if (!array_key_exists($type, $allowed_options)) {
					$url = get_sub_field($field.'_url');
				}

				$process_video_url = apply_filters('grav_blocks_process_video_url', true, $url);

				if ($type == 'video' && $process_video_url) {
					$url = self::get_video_url($url);
				}

				return esc_url($url);
			}
		}

		return '';
	}

	public static function get_link_html($field, $class='')
	{
		$url = ($type_url = self::get_link_url($field)) ? $type_url : '#';

		if ($text = get_sub_field($field.'_text')) {
			?>
			<a class="block-link-<?php echo esc_attr(get_sub_field($field.'_type'));?><?php echo ($class ? ' '.$class : '');?>" href="<?php echo esc_url($url);?>"><?php echo esc_html($text);?></a>
			<?php
		}
	}

	public static function filter_layout_links(&$item, $key='', $lookup='')
	{
	    if (!empty($item) && is_array($item)) {
	        foreach ($item as $k => $v) {
	            if (is_array($v) && isset($v[$lookup])) {
	                array_splice($item, array_search($k, array_keys($item)), 1, $v[$lookup]);
	            }
			}

	        array_walk($item, array(__CLASS__, __METHOD__), $lookup);
	    }
	}

	/**
	* Get size information for all currently-registered image sizes.
	*
	* @global $_wp_additional_image_sizes
	* @uses   get_intermediate_image_sizes()
	* @return array $sizes Data for all currently-registered image sizes.
	*/
	public static function get_image_sizes()
	{
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach (get_intermediate_image_sizes() as $_size) {
			if (in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
				$sizes[$_size]['width'] = get_option("{$_size}_size_w");
				$sizes[$_size]['height'] = get_option("{$_size}_size_h");
				$sizes[$_size]['crop'] = (bool)get_option("{$_size}_crop");
			} else if (isset($_wp_additional_image_sizes[$_size])) {
				$sizes[$_size] = array(
					'width' => $_wp_additional_image_sizes[$_size]['width'],
					'height' => $_wp_additional_image_sizes[$_size]['height'],
					'crop' => $_wp_additional_image_sizes[$_size]['crop'],
				);
			}
		}

		return $sizes;
	}

	private static function get_prefered_image_size_src($image, $size='')
	{
		if (!empty($image['sizes'][$size])) {
			return $image['sizes'][$size];
		}

		if (!empty($image['sizes']['xlarge'])) {
			return $image['sizes']['xlarge'];
		}

		if (!empty($image['sizes']['large'])) {
			return $image['sizes']['large'];
		}

		if (!empty($image['url'])) {
			return $image['url'];
		}

		return '';
	}

	public static function image_sources($image = 'featured', $return_as_array = false)
	{
		$sources = array();

		if (is_numeric($image) && get_post_type($image) !== 'attachment') {
			$image = get_post_thumbnail_id($image);
		}

		if ($image === 'featured') {
			$image = get_post_thumbnail_id();
		}

		if (is_numeric($image) || !empty($image['sizes'])) {
			$image_sizes = self::get_image_sizes();

			if (is_numeric($image)) {
				foreach ($image_sizes as $size => $image_size) {
					// Only include sizes that are not cropped.
					if (empty($image_size['crop']) && $image_size['width']) {
						if ($url = wp_get_attachment_image_src($image, $size)) {
							$sources['data-rimg-'.$size] = $url[0];
						}
					}
				}
			} else {
				foreach ($image['sizes'] as $size => $url) {
					if (!preg_match('/\-width|\-height/i', $size) && isset($image_sizes[$size]['crop']) && empty($image_sizes[$size]['crop'])) {
						$sources['data-rimg-'.$size] = $url;
					}
				}
			}
		}

		if ($return_as_array) {
			return $sources;
		}

		foreach ($sources as $key => $source) {
			$sources[$key] = '"'.$source.'"';
		}

		return trim(urldecode(http_build_query($sources, '', ' ')));
	}

	public static function image_background($image = 'featured', $fallback_size = 'large')
	{
		if ($image === 'featured' || is_numeric($image)) {
			if (is_numeric($image) && get_post_type($image) !== 'attachment') {
				$attachment = get_post(get_post_thumbnail_id($image));
			} else {
				$attachment = get_post(get_post_thumbnail_id());
			}

			if ($attachment) {
				$image = array(
					'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
					'caption' => $attachment->post_excerpt,
					'description' => $attachment->post_content,
					'href' => get_permalink( $attachment->ID ),
					'src' => $attachment->guid,
					'url' => $attachment->guid,
					'title' => $attachment->post_title
				);

				$image['sizes'] = array();

				foreach (self::get_image_sizes() as $size => $image_size) {
					// Only include sizes that are not cropped.
					if (empty($image_size['crop']) && $image_size['width']) {
						if ($url = wp_get_attachment_image_src($attachment->ID, $size)) {
							$image['sizes'][$size] = $url[0];
						}
					}
				}
			}
		}

		if (!empty($image)) {
			$prefered_image_src = self::get_prefered_image_size_src($image, $fallback_size);

			if ($prefered_image_src) {
				return " style=\"background-image: url('".$prefered_image_src."');\" ";
			}
		}

		return '';
	}

	protected static function get_acf_image_object($source)
	{
		if (!$source) {
			return false;
		}

		$attachment_id = 0;

		if (is_int($source) || intval($source)) {
			// treat as attachment id
			$attachment_id = intval($source);
		} else if (is_string($source)) {
			if ($source == 'featured') {
				$attachment = get_post(get_post_thumbnail_id());
				$attachment_id = $attachment ? $attachment->ID : 0;
			}
		}

		if (!$attachment_id) {
			return false;
		}

		return acf_get_attachment($attachment_id);
	}

	public static function image($image='featured', $add_attr=array(), $tag_type='img', $fallback_size='')
	{
		// TODO: remove cropped images
		if (empty($image)) {
			return '';
		}

		$acf_image = (gettype($image) == 'array') ? $image : self::get_acf_image_object($image);

		if (!$acf_image) {
			return '';
		}

		$cropped_sizes = array();
		$attachment = get_post($acf_image['ID']);

		$image = array(
			'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
			'caption' => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'href' => get_permalink( $attachment->ID ),
			'src' => $attachment->guid,
			'url' => $attachment->guid,
			'title' => $attachment->post_title
		);

		$image['sizes'] = array();

		foreach (self::get_image_sizes() as $size => $image_size) {
			// Only include sizes that are not cropped.
			if (empty($image_size['crop']) && $image_size['width']) {
				if ($url = wp_get_attachment_image_src($attachment->ID, $size)) {
					$image['sizes'][$size] = $url[0];
				}
			} else {
				$cropped_sizes[] = $size;
			}
		}

		if ($tag_type === 'img') {
			$add_attr['alt'] = isset($add_attr['alt']) ? esc_attr($add_attr['alt']) : esc_attr($image['alt']);
			$add_attr['title'] = isset($add_attr['title']) ? esc_attr($add_attr['title']) : esc_attr($image['title']);

			// accessibility
			if (!$add_attr['alt']) {
				$add_attr['alt'] = $add_attr['title'];
			}
		}

		$image_sources = array();

		if (GRAV_BLOCKS_PLUGIN_SETTINGS::is_setting_checked('advanced_options', 'add_responsive_img')) {
			// 1x1 transparent PNG
			$add_attr['src'] = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
			$image_sources = self::image_sources($image, true);
		} else {
			$prefered_image_src = self::get_prefered_image_size_src($image, $fallback_size);

			if ($tag_type === 'img' && !isset($add_attr['src'])) {
				if ($prefered_image_src) {
					$add_attr['src'] = $prefered_image_src;
				}
			}

			if ($tag_type !== 'img' && $prefered_image_src) {
				$add_attr['style'] = " background-image: url('".$prefered_image_src."'); ";
			}
		}

		foreach ($add_attr as $attribute_key => $attribute_value) {
			$add_attr[$attribute_key] = '"'.esc_attr($attribute_value).'"';
		}

		$attributes_array = array_filter(array_merge($image_sources, $add_attr));

		// If not alt then add an empty one for validation
		if ($tag_type === 'img' && empty($add_attr['alt'])) {
			$attributes_array['alt'] = '""';
		}

		$attributes_str = trim(urldecode(http_build_query($attributes_array, '', ' ')));

		if ($attributes_str) {
			$default_markup = ($tag_type === 'div') ? '<div '.$attributes_str.'></div>' : '<img '.$attributes_str.' />';

			return apply_filters('grav_blocks_image_tag', $default_markup, $tag_type, $attributes_array, $acf_image);
		}

		return '';
	}


	public static function allow_br($value)
	{
		return str_replace(array('&lt;br&gt;','&lt;br/&gt;','&lt;br /&gt;'), '<br>', $value);
	}

	public static function get_gravity_forms()
	{
		$gravity_forms = array();

		// Return Cache if exists
		if (isset(self::$cache['gravity_forms'])) {
			return self::$cache['gravity_forms'];
		}

		if (class_exists('GFAPI') && method_exists('GFAPI', 'get_forms')) {
			self::$cache['gravity_forms'] = array();

			foreach(GFAPI::get_forms() as $gform) {
				$gravity_forms[] = $gform;
			}
		}

		self::$cache['gravity_forms'] = $gravity_forms;

		return $gravity_forms;
	}

	public static function get_radio_num_conditionals($field = '', $num = 0, $max = 4)
	{
		$conditional_array = array();

		if ($num) {
			for ($i = $max; $i >= $num; $i--) {
				$conditional_array[] = array (
					array (
						'field' => $field,
						'operator' => '==',
						'value' => $i,
					)
				);
			}
		}

		return $conditional_array;
	}

	public static function get_blocks_usage( $data=array() ) {
		// Do something with the $request
		$response = '';

		if ($grav_blocks = self::get_available_blocks()) {
			if (isset($data['name']) && $data['name']) {
				$chosen_blocks[$data['name']] = $grav_blocks[$data['name']];
			} else {
				$chosen_blocks = $grav_blocks;
			}

			foreach ($chosen_blocks as $block_name => $block) {
				$response .= '<div class="grav-blocks-row">';

				$posts = get_posts(array(
					'numberposts' => -1,
					'post_type' => get_post_types(),
					'meta_query' => array(
						array(
							'key' => 'grav_blocks',
							'value' => $block_name,
							'compare' => 'LIKE'
						),
					),
				));

				$response .= '<div class="grav-blocks-column"><h4>' . $block['label'] . ' (' . count($posts) . ')</h4></div>';

				if (count($posts) > 0) {
					$response .= '<div class="grav-blocks-column"><ul class="permalink block ' . $block_name . '">';

					foreach ($posts as $post) {
						// debug($post, true);
						$response .= '<li><a class="permalink" target="_blank" href="' . get_the_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a> (<a class="edit" href="' . admin_url() . 'post.php?post=' . $post->ID . '&action=edit" target="_blank">edit</a>)</li>';
					}

					$response .= '</ul></div>';
				}

				$response .= '</div>';
			}
		}

		return $response;
	}

	public static function get_wysiwyg_container_class()
	{
		return esc_attr(apply_filters('grav_blocks_wysiwyg_container_class', 'wysiwyg'));
	}
}
