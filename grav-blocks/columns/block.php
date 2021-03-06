<?php
$column_num = isset($column_num) ? (int)$column_num : (int)get_sub_field('num_columns');
$format = isset($format) ? $format : get_sub_field('format');
$columns = isset($columns) ? $columns : array();

if ($column_num) {
	$sidebar = ($column_num == 2) ? $format : '';
	$cols_span = (12 / $column_num);

	if (!$sidebar) {
		switch ($cols_span) {
			case 12:
			    $cols_span = 8;
				break;

			case 6:
			    $cols_span = 4;
			    break;
		}
	}

	$cols_span = apply_filters('grav_block_content_columns', $cols_span);
	$medium_col = $column_num < 3 ? (12 / $column_num) : 12;
	$large_col = $column_num >= 2 ? $cols_span : 12;
	$large_col = $column_num < 2 ? $cols_span : $large_col;

	?>
	<div class="block-inner num-col-<?php echo $column_num; ?> <?php echo $sidebar; ?>">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->align('center')->get(); ?>">
			<?php
			for ($i = 1; $i <= $column_num; $i++)
			{
				$col_sizes = [
					'small' => 12,
					'medium' => $medium_col,
					'large' => $large_col,
					'small-offset' => 0,
					'medium-offset' => 0,
					'large-offset' => 0
				];

				if ($sidebar != '' && $column_num == 2)
				{
					if ($i == 1)
					{
						$col_sizes['medium'] = ($sidebar == 'format-sidebar-left') ? 4 : 8;
						$col_sizes['large'] = ($sidebar == 'format-sidebar-left') ? 4 : 8;
					}
					else
					{
						$col_sizes['medium'] = ($sidebar == 'format-sidebar-left') ? 8 : 4;
						$col_sizes['large'] = ($sidebar == 'format-sidebar-left') ? 8 : 4;
					}
				}

				$col_sizes = apply_filters('grav_block_content_column_sizes', $col_sizes, $i, $column_num, $format);
				$col_classes = explode(' ', GRAV_BLOCKS::css()->col($col_sizes['small'], $col_sizes['medium'], $col_sizes['large'])->add('col-content')->get());
				$col_classes[] = GRAV_BLOCKS::get_wysiwyg_container_class();

				if ($col_sizes['small-offset']) {
					$col_classes[] = 'small-offset-'.$col_sizes['small-offset'];
				}

				if ($col_sizes['medium-offset']) {
					$col_classes[] = 'medium-offset-'.$col_sizes['medium-offset'];
				}

				if ($col_sizes['large-offset']) {
					$col_classes[] = 'large-offset-'.$col_sizes['large-offset'];
				}

				$col_classes = apply_filters('grav_block_content_column_classes', $col_classes, $i, $column_num, $format);

				?>
				<div class="<?php echo implode(' ', $col_classes); ?>">
					<?php echo isset($columns['column_'.$i]) ? $columns['column_'.$i] : get_sub_field('column_'.$i); ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
