<?php
namespace BlueprintBlocks;

abstract class Docs
{
	public static function get_json_entries(string $json_file): array
	{
		$file_json_entries = \GRAV_BLOCKS::$plugin_path.$json_file;
		$entries = [];

		if (!file_exists($file_json_entries)) {
			return [];
		}

		$json_entries = json_decode(file_get_contents($file_json_entries), true);

		$entries = array_map(function($entry) {
			$markup = self::build_entry_markup($entry);
			
			return [
				'name' => $entry['name'] ?? '',
				'markup' => $markup
			];
		}, $json_entries ?? []);

		return $entries;
	}

	protected static function build_entry_markup(array $entry): string
	{
		$name = $entry['name'] ?? '';

        if (!$name) {
            return '';
        }

        $args = array_map(function($arg) {
            return '<code>'.esc_html($arg).'</code>';
        }, $entry['arguments'] ?? []);

        $description = trim($entry['description']) ?? '';

        $examples = array_map(function($example) {
            $output = '';
            $label = $example['label'] ?? '';
            $file = $example['file'] ?? '';
			$filename = $file ? \GRAV_BLOCKS::$plugin_path.'docs/examples/'.$file : '';
			$note = $example['note'] ?? '';

            if ($label) {
                $output .= '<strong>'.$example['label'].'</strong>';
            }

            if ($filename && file_exists($filename)) {
                $file_contents = file_get_contents($filename);

                $output .= '<pre><code class="language-php">'.esc_html($file_contents).'</code></pre>';
			}
			
			if ($note) {
				$output .= '<br><em>'.$note.'</em>';
			}

            return $output;
		}, $entry['examples'] ?? []);
		
		$args_markup = $args ? '<h4>Arguments: '.implode(', ', $args).'</h4>' : '';
		$desc_markup = $description ? '<p>'.$description.'</p>' : '';
		$examples_markup = $examples ? implode('', $examples) : '';

		$markup = '
			<div id="'.sanitize_title($name).'">
				<h3>'.esc_html($name).'</h3>
				'.$args_markup.'
				'.$desc_markup.'
				'.$examples_markup.'
			</div>
		';

		return trim($markup);
	}
}
