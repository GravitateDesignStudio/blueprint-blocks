<?php
require_once GRAV_BLOCKS::$plugin_path.'blueprint-docs.php';

$hooks_filters = BlueprintBlocks\Docs::get_json_entries('docs/hooks-filters.json');
$hooks_actions = BlueprintBlocks\Docs::get_json_entries('docs/hooks-actions.json');

?>
<div class="grav-blocks-developers">
    <h2>Placing the blocks in your theme</h2>
    <h4>There are 2 ways to include Gravitate Blocks in your theme:</h4>
    <ul>
        <li>By default the blocks will be filtered into "the_content()".  However, you can disable that in the <a href="admin.php?page=gravitate-blocks&section=advanced">Advanced Tab</a>.</li>
        <li>
            You can use the function to manually include them in your theme.
            <br>
            <code class="language-php">&lt;?php GRAV_BLOCKS::display(); ?&gt;</code>
        </li>
    </ul>

    <h2>Modifying Blocks</h2>
    <h4>There are a few options to modify an existing block.</h4>
    <ul>
        <li>You can copy the block from:
            <br>
            <code>wp-content/plugins/blueprint-blocks/grav-blocks</code>
            <br>
            and paste it in:
            <br>
            <code>wp-content/themes/your-theme-folder/grav-blocks</code>
            <br>
            <em>* This is not ideal as updates will not be applied to those blocks</em>
        </li>
        <li>You can Modify the Block by using the Hooks and Filters below (Recommended)</li>
    </ul>

    <h2>Adding your own blocks</h2>
    <h4>There are a few options for adding your own blocks.</h4>
    <ul>
        <li>You can create your own WP plugin to include your own blocks—this feature uses the "grav_blocks" filter below.</li>
        <li>You can create a block folder in:
            <br>
            <code>wp-content/themes/your-theme-folder/grav-blocks</code>
        </li>
        <li>You can use the "grav_blocks" filter below in your functions.php file.</li>
    </ul>

    <h2>Hooks - Actions and Filters</h2>

    <?php
    foreach ($hooks_filters as $filter)
    {
        echo $filter['markup'];
    }

    foreach ($hooks_actions as $action)
    {
        echo $action['markup'];
    }
    ?>
</div>
