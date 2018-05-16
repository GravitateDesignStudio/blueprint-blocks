<?php

wp_enqueue_script( 'image_gallery_block_js', plugin_dir_url(__FILE__) . 'image-gallery-block.js', $deps = array('jquery'), $ver = false, $in_footer = true );
