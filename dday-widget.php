<?php
/*
Plugin Name: DDay Widget
Plugin URI: http://mdkart.fr/blog/2007/04/16/plugin-dday-pour-wordpress
Description: Adds a Sidebar Widget To Display DDays From DDay Plugin. You Need To Activate DDay First.
Version: 0.3.7
Author: Mdkart
Author URI: http://mdkart.fr

Thanks to Xavier Montaron to suggest the modification for title setting.
*/

define('DDAY_TOKEN', 'DDay');
### Function: Init WP-Polls Widget
function widget_dday_init() {
    if (!function_exists('register_sidebar_widget')) {
        return;
    }

    ### Function: WP-Polls Widget
    function widget_dday($args) {
        extract($args);        
        if (function_exists('wp_dday_list')) {
            $options = get_option('widget_dday');
            $title = empty($options['title']) ? DDAY_TOKEN : $options['title']; 
            echo $before_widget; 
            echo $before_title . $title . $after_title;
            wp_dday_list();
            echo $after_widget;
        }        
    }
    
    function widget_dday_control() {
		$options = $newoptions = get_option('widget_dday');
		if ( $_POST["dday-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["dday-title"]));
		}
		if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_dday', $options);
		}
		$title = attribute_escape($options['title']);
        ?> 
		<p><label for="dday-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="dday-title" name="dday-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" id="dday-submit" name="dday-submit" value="1" />
        <?php 
    }

    // Register Widgets
    register_sidebar_widget(DDAY_TOKEN, 'widget_dday');
    register_widget_control(DDAY_TOKEN, 'widget_dday_control');
}

### Function: Load The WP-Polls Widget
add_action('plugins_loaded', 'widget_dday_init');
?>