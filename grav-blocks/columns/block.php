<?php

$column_num = isset($column_num) ? $column_num : get_sub_field('num_columns');
$format = isset($format) ? $format : get_sub_field('format');

$columns = isset($columns) ? $columns : array();

if($column_num){

	$sidebar = ($column_num == 2) ? $format : '';

	$cols_span = (12/$column_num);
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
	$medium_col = $column_num < 3 ? (12/$column_num) : 12;
	$large_col = $column_num >= 2 ? $cols_span : 12;
	$large_col = $column_num < 2 ? $cols_span : $large_col;

?>
	<div class="block-inner num-col-<?php echo $column_num; ?> <?php echo $sidebar; ?>">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->add('align-center')->get();?>">
		<?php
			for( $i = 1; $i <= $column_num; $i++ ) {
				if($sidebar != '' && $column_num == 2){
					if($i == 1){
						$medium_col = ($sidebar == 'format-sidebar-left') ? 4 : 8;
						$large_col = ($sidebar == 'format-sidebar-left') ? 4 : 8;
					} else {
						$medium_col = ($sidebar == 'format-sidebar-left') ? 8 : 4;
						$large_col = ($sidebar == 'format-sidebar-left') ? 8 : 4;
					}
				}
				?>
				<div class="<?php echo GRAV_BLOCKS::css()->col(12, $medium_col, $large_col)->add('col-content')->get(); ?>">
					<?php echo isset($columns['column_'.$i]) ? $columns['column_'.$i] : get_sub_field('column_'.$i); ?>
				</div>
		<?php } ?>
		</div>
	</div>
<?php
}
