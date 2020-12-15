<?php

/**
 *
 * @author Gravitate
 *
 */
class GRAV_BLOCKS_CSS
{
	public $class = [];


	/**
	 * This is the initial setup that connects the Settings and loads the Fields from ACF
	 *
	 * @return void
	 */
	public function __construct()
	{
		// return $this->class;
	}

	public function css(): GRAV_BLOCKS_CSS
	{
		return $this;
	}

	/**
	 * Runs on WP init
	 *
	 * @return void
	 */
	public function col($small = null, $med = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		$this->class[] = 'columns';

		if (is_numeric($small)) {
			$this->class[] = 'small-' . $small;
		} else {
			$this->class[] = 'small-12';
		}

		if (is_numeric($med)) {
			$this->class[] = 'medium-' . $med;
		}

		if (is_numeric($large)) {
			$this->class[] = 'large-' . $large;
		}

		if (is_numeric($xlarge)) {
			$this->class[] = 'xlarge-' . $xlarge;
		}

		return $this;
	}

	public function col_offset($small = null, $med = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		if (is_numeric($small)) {
			$this->class[] = 'small-offset-' . $small;
		}

		if (is_numeric($med)) {
			$this->class[] = 'medium-offset-' . $med;
		}

		if (is_numeric($large)) {
			$this->class[] = 'large-offset-' . $large;
		}

		if (is_numeric($xlarge)) {
			$this->class[] = 'xlarge-offset-' . $xlarge;
		}

		return $this;
	}

	public function col_order($small = null, $med = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		if (is_numeric($small) && $small > 0) {
			$this->class[] = 'small-order-' . $small;
		}

		if (is_numeric($med) && $med > 0) {
			$this->class[] = 'medium-order-' . $med;
		}

		if (is_numeric($large) && $large > 0) {
			$this->class[] = 'large-order-' . $large;
		}

		if (is_numeric($xlarge) && $xlarge > 0) {
			$this->class[] = 'xlarge-order-' . $xlarge;
		}

		return $this;
	}

	public function col_push($small = null, $med = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		if (is_numeric($small)) {
			$this->class[] = 'small-push-' . $small;
		}

		if (is_numeric($med)) {
			$this->class[] = 'medium-push-' . $med;
		}

		if (is_numeric($large)) {
			$this->class[] = 'large-push-' . $large;
		}

		if (is_numeric($xlarge)) {
			$this->class[] = 'xlarge-push-' . $xlarge;
		}

		return $this;
	}

	public function col_pull($small = null, $med = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		if (is_numeric($small)) {
			$this->class[] = 'small-pull-' . $small;
		}

		if (is_numeric($med)) {
			$this->class[] = 'medium-pull-' . $med;
		}

		if (is_numeric($large)) {
			$this->class[] = 'large-pull-' . $large;
		}

		if (is_numeric($xlarge)) {
			$this->class[] = 'xlarge-pull-' . $xlarge;
		}

		return $this;
	}

	public function grid($small = 1, $med = 2, $large = 3, $xlarge = 4): GRAV_BLOCKS_CSS
	{
		if ($small && $small <= 6) {
			$this->class[] = 'small-up-' . $small;
		}

		if ($med && $med <= 6) {
			$this->class[] = 'medium-up-' . $med;
		}

		if ($large && $large <= 6) {
			$this->class[] = 'large-up-' . $large;
		}

		if ($xlarge && $xlarge <= 6) {
			$this->class[] = 'xlarge-up-' . $xlarge;
		}

		return $this;
	}

	public function row(): GRAV_BLOCKS_CSS
	{
		$this->class[] = 'row';

		return $this;
	}

	public function collapsed(): GRAV_BLOCKS_CSS
	{
		$this->class[] = 'collapsed';

		return $this;
	}

	public function col_center($small = true, $medium = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		$sizes = [
			[ 'name' => 'small', 'value' => $small ],
			[ 'name' => 'medium', 'value' => $medium ],
			[ 'name' => 'large', 'value' => $large ],
			[ 'name' => 'xlarge', 'value' => $xlarge ]
		];

		foreach ($sizes as $size) {
			if ($size['value']) {
				$this->class[] = 'center-block';
				$this->class[] = 'mx-auto';
				$this->class[] = $size['name'] . '-centered';
			}
		}

		return $this;
	}

	public function col_uncenter($small = true, $medium = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		$sizes = [
			[ 'name' => 'small', 'value' => $small ],
			[ 'name' => 'medium', 'value' => $medium ],
			[ 'name' => 'large', 'value' => $large ],
			[ 'name' => 'xlarge', 'value' => $xlarge ]
		];

		foreach ($sizes as $size) {
			if ($size['value']) {
				$this->class[] = $size['name'] . '-uncentered';
			}
		}

		return $this;
	}

	public function text_align($align = 'center'): GRAV_BLOCKS_CSS
	{
		$this->class[] = 'text-' . $align;
		$this->class[] = 'text-xs-' . $align;

		return $this;
	}

	public function align($align = 'center'): GRAV_BLOCKS_CSS
	{
		$this->class[] = 'align-' . $align;

		return $this;
	}

	public function hide($small = null, $medium = null, $large = null, $xlarge = null): GRAV_BLOCKS_CSS
	{
		if ($small) {
			$this->class[] = 'hide-for-small-only';
		}

		if ($medium) {
			$this->class[] = 'hide-for-medium-only';
		}

		if ($large) {
			$this->class[] = 'hide-for-large-only';
		}

		if ($xlarge) {
			$this->class[] = 'hide-for-xlarge';
		}

		return $this;
	}

	public function add($classes): GRAV_BLOCKS_CSS
	{
		$classes = (!is_array($classes)) ? explode(',', str_replace(' ', '', trim($classes))) : $classes;

		foreach($classes as $class) {
			$this->class[] = $class;
		}

		return $this;
	}

	public function get(): string
	{
		$blocks_name = GRAV_BLOCKS::$current_block_name;
		$classes = $this->class;
		$classes = apply_filters('grav_get_css', $classes, $blocks_name);

		return implode(' ', array_unique($classes));
	}

	public function out()
	{
		echo $this->get();
	}
}
