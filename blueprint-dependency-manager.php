<?php
namespace BlueprintBlocks;

abstract class DependencyManager
{
	/**
	 * Array of currently active blocks that will have their dependencies processed
	 *
	 * @var array
	 */
	protected static $blocks = [];

	/**
	 * Adds a block to the internal queue for its dependencies to be processed
	 *
	 * @param string $block_name
	 * @param string $block_path
	 * @return void
	 */
	public static function add_block($block_name, $block_path)
	{
		if (!in_array($block_name, array_keys(self::$blocks))) {
			self::$blocks[$block_name] = $block_path;
		}
	}

	/**
	 * Convert the path name of a block into a URL
	 *
	 * @param string $block_name
	 * @param string $path
	 * @return string
	 */
	protected static function get_url_for_path($block_name, $path)
	{
		if (stripos($path, '/plugins/') !== false) {
			// block is in the plugin folder
			return plugin_dir_url(__FILE__).'grav-blocks/'.$block_name;
		} else if (stripos($path, '/themes/') !== false) {
			// block is in the theme folder
			return get_template_directory_url().'grav-blocks/'.$block_name;
		}

		return '';
	}

	/**
	 * Load JS dependencies for a block
	 * Returns an array of script handles that have been enqueued
	 *
	 * @param string $block_name
	 * @param string $block_path
	 * @param object $block_config
	 * @return array
	 */
	protected static function load_block_js($block_name, $block_path, $block_config)
	{
		$block_js_file = $block_path.DIRECTORY_SEPARATOR.'block.js';
		$use_default_js = apply_filters('grav_block_use_default_js', true, $block_name);

		if (!file_exists($block_js_file) || !$use_default_js) {
			return [];
		}

		$block_base_url = self::get_url_for_path($block_name, $block_path);
		$script_handle = 'block_'.$block_name.'_js';
		$additional_deps = $block_config->dependencies->js ?? [];

		wp_enqueue_script(
			$script_handle,
			$block_base_url.'/block.js',
			array_merge(['jquery'], $additional_deps),
			filemtime($block_js_file),
			true
		);

		return array_merge([$script_handle], $additional_deps);
	}

	/**
	 * Load CSS dependencies for a block
	 * Returns an array of style handles that have been enqueued
	 *
	 * @param string $block_name
	 * @param string $block_path
	 * @param object $block_config
	 * @return array
	 */
	protected static function load_block_css($block_name, $block_path, $block_config)
	{
		$block_css_file = $block_path.DIRECTORY_SEPARATOR.'block.css';
		$use_default_css = apply_filters('grav_block_use_default_css', true, $block_name);

		if (!file_exists($block_css_file) || !$use_default_css) {
			return [];
		}

		$block_base_url = self::get_url_for_path($block_name, $block_path);
		$style_handle = 'block_'.$block_name.'_css';
		$additional_deps = $block_config->dependencies->js ?? [];

		wp_enqueue_style(
			$style_handle,
			$block_base_url.'/block.css',
			$additional_deps,
			filemtime($block_css_file)
		);

		return [$style_handle];
	}

	/**
	 * Loop through all blocks that have been adeded and load JS/CSS dependencies for each
	 * Will also add a 'script_loader_tag' filter that adds a 'defer' attribute to each script enqueue
	 *
	 * @return void
	 */
	public static function load_dependencies()
	{
		$enqueued_js_handles = [];
		$enqueued_css_handles = [];

		foreach (self::$blocks as $block_name => $block_path) {
			$block_config = self::get_block_config_json($block_path);

			$enqueued_js_for_block = self::load_block_js($block_name, $block_path, $block_config);
			$enqueued_css_for_block = self::load_block_css($block_name, $block_path, $block_config);

			$enqueued_js_handles = array_merge($enqueued_js_handles, $enqueued_js_for_block);
			$enqueued_css_handles = array_merge($enqueued_css_handles, $enqueued_css_for_block);
		}

		// add defer attribute to all JS scripts enqueued by blocks
		add_filter('script_loader_tag', function($tag, $handle) use ($enqueued_js_handles) {
			if (!in_array($handle, $enqueued_js_handles)) {
				return $tag;
			}

			$tag = str_replace(' src=', ' defer src=', $tag);

			return $tag;
		}, 10, 2);
	}

	/**
	 * Get the 'block_config.json' data for the specified block path
	 *
	 * @param string $block_path
	 * @return object
	 */
	protected static function get_block_config_json($block_path)
	{
		$json_file = $block_path.DIRECTORY_SEPARATOR.'block_config.json';

		if (!file_exists($json_file)) {
			return false;
		}

		$file_contents = file_get_contents($json_file);

		return json_decode($file_contents);
	}
}
