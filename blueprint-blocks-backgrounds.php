<?php
namespace BlueprintBlocks;

abstract class Backgrounds
{
	/**
	 * The currently active display group referenced during block rendering
	 *
	 * @var array|null
	 */
	private static $active_display_group = null;

	/**
	 * Echo the opening markup for a group container if necessary based on the
	 * given background groups and the current block index. This is used during
	 * render.
	 *
	 * @param array $bg_groups
	 * @param integer $block_index The current block index
	 * @return void Markup will be echoed
	 */
	public static function open_group_container(array $bg_groups, int $block_index)
	{
		if (self::$active_display_group || !is_array($bg_groups)) {
			return;
		}

		self::$active_display_group = array_reduce($bg_groups, function ($found_group, $bg_group) use ($block_index) {
			if ($bg_group['start_index'] === $block_index) {
				$found_group = $bg_group;
			}

			return $found_group;
		}, null);

		if (self::$active_display_group) {
			?>
			<div class="block-background-group <?php echo esc_attr(self::$active_display_group['bg_name']); ?>">
			<?php
		}
	}

	/**
	 * Echo the closing markup for a group container if necessary based on the
	 * given background groups and the current block index. This is used during
	 * render.
	 *
	 * @param array $bg_groups
	 * @param integer $block_index The current block index
	 * @return void Markup will be echoed
	 */
	public static function close_group_container(array $bg_groups, int $block_index)
	{
		if (!self::$active_display_group || !is_array($bg_groups)) {
			return;
		}

		if (self::$active_display_group['end_index'] === $block_index) {
			?>
			</div>
			<?php

			self::$active_display_group = null;
		}
	}

	/**
	 * Check if there is an active display group. This is used during render.
	 *
	 * @return boolean
	 */
	public static function is_active_group(): bool
	{
		return self::$active_display_group !== null;
	}

	/**
	 * Get the background group name of the active display group. This is used
	 * during render.
	 *
	 * @return string
	 */
	public static function get_active_group_bg_name(): string
	{
		return self::$active_display_group !== null ? self::$active_display_group['bg_name'] : '';
	}

	/**
	 * Get an array of background groups from the specified block fields.
	 * Each background group will have 'bg_name', 'start_index', and 'end_index' keys
	 *
	 * @param array $block_fields An array of ACF block field values
	 * @param array $allowable_groups An array of allowable background group names
	 * @param integer $index_offset An optional indexing offset used to stay in sync with GRAV_BLOCKS::$block_index
	 * @return array
	 */
	public static function get_background_groups(array $block_fields, array $allowable_groups = [], int $index_offset = 0): array
	{
		$groups = [];
		$active_group = null;
		$last_index = 0;
		$last_bg = '';
		$in_group = false;

		foreach ($block_fields as $index => $field) {
			$cur_index = $index + $index_offset;
			$cur_bg = $field['block_background'] ?? '';

			// Start a new active group if one does not exist, the current bg is
			// the same as the last bg, and the current bg is allowable as a group
			if (!$in_group && $cur_bg === $last_bg && in_array($cur_bg, $allowable_groups, true)) {
				$in_group = true;
				$active_group = [
					'bg_name' => $cur_bg,
					'start_index' => $last_index + 1 // block indicies start at 1
				];
			}

			// Close the current active group if it exists and the current bg is
			// not the same as the last bg
			if ($in_group && $cur_bg !== $last_bg) {
				$active_group['end_index'] = $cur_index; // block indicies start at 1
				$groups[] = $active_group;

				$active_group = null;
				$in_group = false;
			}

			$last_index = $cur_index;
			$last_bg = $cur_bg;
		}

		// Close the active group if it exists and the loop has ended
		if ($in_group) {
			$active_group['end_index'] = count($block_fields) + $index_offset;
			$groups[] = $active_group;

			$active_group = null;
			$in_group = false;
		}

		return $groups;
	}
}
