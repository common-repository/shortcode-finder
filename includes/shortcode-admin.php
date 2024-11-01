<div class="wrap">
<h2>Available Shortcodes:</h2>
<?php

global $ms_shortcode;

// Retrieve shortcodes
$all_shortcodes = $ms_shortcode->get_shortcodes();

// Arrange shortcodes by function
$shortcode_array = $ms_shortcode->sort_shortcodes_by_function($all_shortcodes);

echo '
<iframe id="mssc-iframe" class="mssc-iframe" src="'.get_site_url().'/wp-content/plugins/shortcode-finder/includes/shortcode-iframe.php" onload="mssc.toolsIframeLoad(this)" seamless width="1" height="1"></iframe>
<table class="mssc-table">
    <tr>
        <th>Available to Admins</th>
        <th>Available to Users</th>
    </tr>
    <tr>
        <td class="mssc-caption">These shortcodes are available to other plugins and admin processes when viewing the Administration Screens.</td>
        <td class="mssc-caption">These shortcodes are available to general users of your site but may not be visible by other plugins.</td>
    </tr>
    <tr>
        <td>
';

// Display all server shortcodes
foreach ($shortcode_array as $name => $group) {
    if (!empty($group)) {
        echo '
            <h3>'.$name.'</h3>
            <table class="widefat importers">
            <tbody>
            <tr>
                <th><strong>Shortcode</strong></th>
                <th><strong>Arguments</strong></th>
            </tr>
        ';  
        foreach ($group as $shortcode) {
            echo '<tr><th>['.$shortcode['name'].']</th>';
            echo '<td>';
            if (!empty($shortcode['params'])) {
                // Retrieve final element.
                $last_key = end(array_keys($shortcode['params']));
                foreach ($shortcode['params'] as $key => $param) {
                    if ($key == $last_key) { echo $param; }
                    else { echo $param.', '; }
                }
            } else { echo 'N/A'; }
            echo '</td></tr>';
        }
        echo '
            </tbody>
            </table>
        ';
    }
}

echo '
        </td>
        <td id="userShortcodes"></td>
    </tr>
</table>
';
?>
</div>