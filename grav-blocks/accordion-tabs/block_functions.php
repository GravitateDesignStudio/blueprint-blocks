<?php

    wp_enqueue_script( 'accordion_tabs_block_js', plugin_dir_url(__FILE__) . 'accordion-tabs-block.js', $deps = array('jquery'), $ver = true, $in_footer = true );
    wp_enqueue_style( 'accordion-tabs__tab-list', plugin_dir_url(__FILE__) . 'accordion-tabs-block.css', $deps = array(), $ver = true, $media = 'all' );
