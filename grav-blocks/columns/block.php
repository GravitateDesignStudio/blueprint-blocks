<?php

$column_layout = $args['column_layout'] ?? (int)get_sub_field('num_columns');

$column_attributes = [
	'1' => [
		'1' => 'columns small-12 medium-10 medium-offset-1 large-8 large-offset-2'
	],
	'2' => [
		'1' => 'columns small-12 medium-6',
		'2' => 'columns small-12 medium-6'
	],
	'213' => [
		'1' => 'columns small-12 medium-4 sidebar',
		'2' => 'columns small-12 medium-8',
	],
	'231' => [
		'1' => 'columns small-12 medium-8',
		'2' => 'columns small-12 medium-4 sidebar',
	],
	'3' => [
		'1' => 'columns small-12 medium-4',
		'2' => 'columns small-12 medium-4',
		'3' => 'columns small-12 medium-4'
	],
	'4' => [
		'1' => 'columns small-12 medium-3',
		'2' => 'columns small-12 medium-3',
		'3' => 'columns small-12 medium-3',
		'4' => 'columns small-12 medium-3'
	]
];
?>

<div class="block-inner row">
	<?php foreach ($column_attributes[$column_layout] as $col_num => $column) : ?>
		<div class="<?= $column ?>">
			<?php the_sub_field('column_' . $col_num); ?>
		</div>
	<?php endforeach ?>
</div>
</div>