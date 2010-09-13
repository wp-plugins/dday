<?php
/*
Plugin Name: DDay
Plugin URI: http://mdkart.fr/blog/plugin-dday-pour-wordpress
Description: This plugin allows you to display DDay's. It also has a spiffy management tool in the administrative console. Fully customizable. 
Author: Mdkart
Author URI: http://mdkart.fr/
Version: 0.4.1
Put in /wp-content/plugins/dday/ of your Wordpress installation
Inpsired by :
- DDay plugin by Franck Paul for Dotclear : http://franck.paul.free.fr/dotclear/?2005/03/22/105-plugin-jour-j
- Code of Wp-quotes by zombierobot : http://www.zombierobot.com/wp-quotes/
- Code of Wp-Polls by zombierobot  : http://www.lesterchan.net/wordpress/readme/wp-polls.html
*/

//Can be modified
$nice_tooltip = 2; // 0 -> No effect, 1 -> Enable nice tootltip when mouse over a DDay, 2 -> Better if you've already jQuery loaded in your page 

### DDay Table Name
define('WP_DDAY_TABLE', $table_prefix . 'dday');

### Function: Poll Administration Menu
add_action('admin_menu', 'wp_dday_admin_menu');
function wp_dday_admin_menu() {
	add_management_page('Dday', 'Dday', 8, 'dday/edit-dday.php');
}

## Function: Enqueue Polls Stylesheets/JavaScripts In WP-Admin
add_action('admin_enqueue_scripts', 'dday_scripts_admin');
function dday_scripts_admin($hook_suffix) {
	$dday_admin_pages = array('dday/edit-dday.php');
	if(in_array($hook_suffix, $dday_admin_pages)) {
		wp_enqueue_style('wp-dday-admin-css', plugins_url('dday/script/style.css'));
		wp_enqueue_script('prototype');
		wp_enqueue_script('scriptaculous');
		wp_enqueue_script('scriptaculous-effects');
		wp_enqueue_script('scriptaculous-dragdrop');
		wp_enqueue_script('dday_script_validation', plugins_url('dday/script/validation.js'), array('scriptaculous'));
	}
}

### Function: Load The WP-Polls Widget
add_action('plugins_loaded', 'widget_dday_init');


### Nice tooltip display
if ($nice_tooltip != 0 && !is_admin())
	add_action('wp_enqueue_scripts', 'dday_tooltip_scripts');
if ($nice_tooltip == 1){
	function dday_tooltip_scripts($nice_tooltip) {
		wp_register_script(
			'dday_qtip_js',	plugins_url('dday/script/qTip.js'),
			false, '1.0', true);
		wp_enqueue_style('dday_qtip', plugins_url('dday/script/dday.css'));
		wp_enqueue_script('dday_qtip_js');
	}
}
else {	
	function dday_tooltip_scripts($nice_tooltip) {
			wp_register_script(
				'dday_tiptip_js', plugins_url('dday/script/jquery.tipTip.minified.js'),
				array('jquery'), '1.3', true);
			wp_enqueue_style('dday_tiptip', plugins_url('dday/script/tipTip.css'));
			wp_enqueue_script('dday_tiptip_js');
	}
}

### Recupere les valeurs d'options	
function countdown($ddayID, $option, $data='')
{	global $wpdb;
	$item_list = '';
	//$data = false;	
	if ( $ddayID != false )
	{
		if ( intval($ddayID) != $ddayID )
		{	return "<div class=\"error\"><p>Bad Monkey! No banana!</p></div>";
		}else{	
      if ($data==''){
        $data = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID=".$ddayID);
			  if ( empty($data) )
			   {	return "<div class=\"error\"><p>I couldn't find a DDay linked up with that identifier. Giving up...</p></div>";
			  }
			}
			foreach ( $data as $datai )
		  { 	
        if ($datai->ddayID == $ddayID){
          $data = $datai;
        }
      }
    }
	}
	$title = $data->title;
	$date = $data->date;
	$url = $data->url;
	$des = $data->des;
	$nbr_jr_av = $data->nbr_jr_av;
	$nbr_jr_ap = $data->nbr_jr_ap;
	$frq_rpt = $data->frq_rpt;
	$rpt = $data->rpt;
	$format_past = $data->past;		
	$format_yester = $data->yester;		
	$format_tod = $data->tod;		
	$format_imm = $data->imm;		
	$format_tom = $data->tom;		
	$format_futur = $data->futur;
		
	$option_var = $option[0];
	if (empty($format_past)) {$format_past =  $option_var->past;}
	if (empty ($format_yester)) {$format_yester =  $option_var->yester;}
	if (empty ($format_tod)) {$format_tod =  $option_var->tod;}
	if (empty ($format_imm)) {$format_imm =  $option_var->imm;}
	if (empty ($format_tom)) {$format_tom =  $option_var->tom;}
	if (empty ($format_futur)) {$format_futur =  $option_var->futur;}
	
	// get current unix timestamp
	$today = current_time(timestamp, 0);
	if ($rpt == 1 or $rpt == 2 or $rpt == 3 or $rpt == 4)
		{
		if ($rpt==1) {$unit = "day";} 
		elseif ($rpt==2) {$unit = "week";}
		elseif($rpt==3) {$unit = "month";}
		elseif($rpt==4) {$unit = "year";}
		$offset = " +".$frq_rpt." ".$unit.($frq_rpt > 1 ? "s" : "");  
		if ($today >= ($date+24*60*60*$nbr_jr_ap)) 
			{
			do 	{
				$date = strtotime(gmdate("Y-m-d H:i:s", $date).$offset);
				} 
			while ($today >= ($date+24*60*60*$nbr_jr_ap));	
			}
		}
		
	$date_day = strtotime(gmdate("Y-m-d 0:0:0", $date));
	$today_day = strtotime(gmdate("Y-m-d 0:0:0", $today));
	$delay_day = round(($date_day - $today_day) / 24 / 60 / 60);
	$delay = $date - $today; // In seconds
	$signe = ($delay >= 0 ? 1 : -1);	
	$delay = abs($delay);
	$delay_hour = floor($delay / 60 / 60);
	$delay -= ($delay_hour * 60 * 60);
	$delay_minute = round($delay / 60);
  
  	# Determine si l'affichage doit etre effectue 
	$display_item = true;
	if (($nbr_jr_av > 0) && ($delay_day > $nbr_jr_av)) 
	{
		$display_item = false;
	} elseif (($nbr_jr_ap > 0) && ((- $delay_day) > $nbr_jr_ap)) 
	{
		$display_item = false;
	} elseif (($signe == -1) && ($nbr_jr_ap == -1)) 
	{
		$display_item = false;
	} elseif (($signe == 1) && ($nbr_jr_av == -1)) 
	{
		$display_item = false;
	}	
	if ($display_item == true) 
	{
		# Mise en place du jour J
		# Debut de l'URL
		if ($url <> '') {
		$item_list .= '<a href="'.$url.'"  title="';			
		}
		else $item_list .= '<span title="';
		if ($des <> '') {
		$item_list .= $des.' : <br/>';
		}
		$item_list .= gmdate ( "d/m/Y H:i:s" , $date ).'" class="dday-title">';
		switch (true) {
			case ($delay_day < -1):
			# C'etait avant-hier ou encore avant
				$format_past = str_replace("%TITLE%", $title, $format_past);
				$format_past = str_replace("%DELAY_DAY%", - $delay_day, $format_past);
				$item_list .= $format_past;
				break;
			case ($delay_day == -1):
			# C'etait hier
				$format_yester = str_replace("%TITLE%", $title, $format_yester);
				$format_yester = str_replace("%DELAY_DAY%", - $delay_day, $format_yester);
				$item_list .= $format_yester;
			break;
			case ($delay_day == 0):
			# C'est aujourd'hui
			if ($signe == -1) {
				# C'est deja passe dans la journee
					$format_tod = str_replace("%TITLE%", $title, $format_tod);
					$item_list .= $format_tod;
				} else {
				# C'est bientot dans la journee
					$format_imm = str_replace("%TITLE%", $title, $format_imm);
					$format_imm = str_replace("%DELAY_DAY%", $delay_day, $format_imm);
					$format_imm = str_replace("%DELAY_HR%", $delay_hour, $format_imm);
					$format_imm = str_replace("%DELAY_MIN%", $delay_minute, $format_imm);
					$item_list .= $format_imm;
					}
			break;
			case ($delay_day == 1):
			# C'est demain
				$format_tom = str_replace("%TITLE%", $title, $format_tom);
				$format_tom = str_replace("%DELAY_DAY%", $delay_day, $format_tom);
				$item_list .= $format_tom;
			break;
			case ($delay_day > 1):
			# C'est apres-demain ou encore plus tard
				$format_futur = str_replace("%TITLE%", $title, $format_futur);
				$format_futur = str_replace("%DELAY_DAY%", $delay_day, $format_futur);
				$item_list .= $format_futur;
			break;
			}
			# Fin de l'URL
			$item_list = str_replace("%DATEandHOUR%", gmdate("d/m/Y H:i:s" , $date), $item_list);
			$item_list = str_replace("%DATE%", gmdate("d/m/Y" , $date), $item_list);
			if ($url <> '') {
				$item_list .= '</a>';
			}else $item_list .= '</span>';			
	}
	return $item_list;
}

function wp_dday_list()
{ global $wpdb;
	echo '<ul class=\'dday\'>';
	$ddays = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID != 1 AND visible='yes' ORDER BY RANK");
	$option = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID=1");
	if ( empty($option) )
	{	echo "<div class=\"error\"><p>I couldn't find a dday linked up with the options...</p></div>";
		return;
	}
	if ( !empty($ddays) )
	{	foreach ( $ddays as $dday )
		{ 	$ddayID = $dday->ddayID;
			$result = countdown($ddayID, $option, $ddays);
			if ( !empty($result) )
			{	echo '<li>'.$result.'</li>';
			}
		}		
	}
	else echo 'No DDAY enter yet';
echo '</ul>';
}

add_filter('the_content', 'place_dday', 7);
function place_dday ($content)
{
    $content = preg_replace( "/\[dday=(\d+)\]/ise", "wp_dday_src('$1')", $content); 
    return $content;
}
function wp_dday_src($ddayID)
{ 	global $wpdb;
	$option = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID=1");
	if ( empty($option) )
	{	echo "<div class=\"error\"><p>I couldn't find a dday linked up with the options...</p></div>";
		return;
	}		
	return countdown($ddayID, $option);
}

function wp_dday($ddayID)
{ 	global $wpdb;
	$option = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID=1");
	if ( empty($option) )
	{	echo "<div class=\"error\"><p>I couldn't find a dday linked up with the options...</p></div>";
		return;
	}		
	echo countdown($ddayID, $option);
}

function widget_dday_init() {
    if (!function_exists('register_sidebar_widget')) {
        return;
    }

    ### Function: WP-Polls Widget
    function widget_dday($args) {
        extract($args);        
        if (function_exists('wp_dday_list')) {
            $options = get_option('widget_dday');
            $title = empty($options['title']) ? 'DDay' : $options['title']; 
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
    register_sidebar_widget('DDay', 'widget_dday');
    register_widget_control('DDay', 'widget_dday_control');
}
?>
