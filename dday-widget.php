<?php
/*
Plugin Name: DDay Widget
Plugin URI: http://mdkart.fr/blog/2007/04/16/plugin-dday-pour-wordpress
Description: Adds a Sidebar Widget To Display DDays From DDay Plugin. You Need To Activate DDay First.
Version: 0.3.2
Author: Mdkart
Author URI: http://mdkart.fr
*/


/*  
	Copyright 2007  Lester Chan  (email : gamerz84@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Function: Init WP-Polls Widget
function widget_dday_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	### Function: WP-Polls Widget
	function widget_dday($args) {
		extract($args);		
		if (function_exists('wp_dday_list')) {
			//$title='Dday (You Can modify this Title will be enhance in next version)'; 
			echo $before_widget.$before_title.$title.$after_title;
			wp_dday_list();
			echo $after_widget;
		}		
	}

	// Register Widgets
	register_sidebar_widget('DDay', 'widget_dday');
}


### Function: Load The WP-Polls Widget
add_action('plugins_loaded', 'widget_dday_init');
?>