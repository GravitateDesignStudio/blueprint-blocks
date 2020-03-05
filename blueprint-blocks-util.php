<?php
namespace BlueprintBlocks;

abstract class Util
{
	public static function is_rest_search(): bool
	{
		$current_url = wp_parse_url(add_query_arg([]), PHP_URL_PATH);

		if (!$current_url) {
			return false;
		}

		$matching_urls = apply_filters('grav_block_rest_search_urls', [
			'/wp-json/wp/v2/search'
		]);

		return in_array($current_url, $matching_urls);
	}

	public static function is_search(): bool
	{
		return is_search() || self::is_rest_search();
	}

	public static function get_search_query(): string
	{
		$query = get_search_query();

		if (!$query) {
			$query = $_GET['search'] ?? '';
		}

		return $query;
	}
}
