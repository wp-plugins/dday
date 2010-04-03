<?php
/*
Author: Mdkart
Author URI: http:/mdkart.fr
Description: Admin tool for the Dday plugin.
*/
// Localisation
load_plugin_textdomain('wpdday', 'wp-content/plugins/dday/languages');

### Variables Variables Variables
$base_name = plugin_basename('dday/edit-dday.php');
$base_page = 'edit.php?page='.$base_name;

$edit = $create = $save = $delete = false;

// Global variable cleanup. 
$edit = $create = $save = $delete = false;

// How to control the app
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
$ddayID = !empty($_REQUEST['ddayID']) ? $_REQUEST['ddayID'] : '';

// Messages for the user
$debugText = '';
$messages = '';

echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/dday/script/style.css" type="text/css" media="screen" />'."\n";
echo '<script src="'.get_option('siteurl').'/wp-includes/js/scriptaculous/prototype.js" type="text/javascript"></script>';
echo '<script src="'.get_option('siteurl').'/wp-includes/js/scriptaculous/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>';
echo '<script src="'.get_option('siteurl').'/wp-content/plugins/dday/script/validation.js" type="text/javascript"></script>';
?>		<script type="text/javascript">
		function go(expr) {
		var reg = new RegExp("(&)", "g");
		var reg2 = new RegExp("[^0-9,]", "g");
		var liste1 = expr.replace(reg, ",");
		var liste = liste1.replace(reg2, "");
		document.location = ('edit.php?page=dday/edit-dday.php&new_order='+liste);
		}
		</script>
<noscript>
<div class="error">
<h1 style ="text-align: center;"><?php _e('JAVASCRIPT MUST BE ENABLED', 'wpdday')?></h1>
<p><?php _e('Many errors are possible if javascript not enabled', 'wpdday')?></p></div></noscript>
<?php
//////////////    Start First Run Check&Run   /////////////////
$tableExists = false;
$tables = $wpdb->get_results("show tables;");

foreach ( $tables as $table )
{	foreach ( $table as $value )
	{	if ( $value == WP_DDAY_TABLE )
		{
			$tableExists=true;
			break;
		}
	}
}

if ( !$tableExists )
{	$sql = "CREATE TABLE `" . WP_DDAY_TABLE . "` (
				`ddayID` INT  NOT NULL AUTO_INCREMENT ,
				`title` TEXT NOT NULL ,
				`date` TEXT NOT NULL ,
				`url` TEXT NOT NULL ,
				`des` TEXT NOT NULL ,
				`rpt` INT NOT NULL ,
				`frq_rpt` INT NOT NULL ,
				`nbr_jr_av` INT NOT NULL ,
				`nbr_jr_ap` INT NOT NULL ,
				`rank` INT NOT NULL ,
				`past` TEXT NOT NULL ,
				`yester` TEXT NOT NULL ,
				`tod` TEXT NOT NULL ,
				`imm` TEXT NOT NULL ,
				`tom` TEXT NOT NULL ,
				`futur` TEXT NOT NULL ,
				`visible` ENUM( 'yes', 'no' ) NOT NULL ,
				PRIMARY KEY ( `ddayID` )
			)";
	$wpdb->get_results($sql);
	
	$sql = "INSERT INTO `" . WP_DDAY_TABLE . "`(ddayID, title, date, past, yester, tod, imm, tom, futur, visible) values "
	     . '(1, "DDAY OPTIONS", 0, "%TITLE% for %DELAY_DAY% days", "%TITLE% yesterday", "%TITLE% today", "%TITLE% today in %DELAY_HR%h%DELAY_MIN%", "%TITLE% tomorrow", "%TITLE% in %DELAY_DAY% days", "no")';
	$wpdb->get_results($sql);
	?>
	<div class="updated"><p><strong>
	<?php _e('Hola,', 'wpdday');?>
	</strong><br /><br />
	<?php _e('This seems to be your first time visiting this page. I\'ve created a database table for you (wp_dday).', 'wpdday');?><br />
	<?php _e('If you want to remove the data, make sure to delete that table after deactivating the plugin', 'wpdday');?><br />
	</p></div>
	<div class="error"><p>
	<?php _e('You can edit default options on the bottom of this page', 'wpdday');?><br />
	<strong><?php _e('THIS IS HIGHLY RECOMMANDED IF YOUR BLOG ISNT IN ENGLISH', 'wpdday');?>
	<h3><a href ="edit.php?page=dday/edit-dday.php&action=edit&ddayID=1"><?php _e('Edit Options', 'wpdday');?></a></h3></strong>
	</p></div>
	<?php
}
///////////////      End first run      ///////////////
///////////////   Handle any manipulations   //////////////////
if ( $action == 'add' )
{	$title = $_POST['dd_title'];	
	$day = $_POST['dd_day'];
	$month = $_POST['dd_month'];
	$year = $_POST['dd_year'];
	$hour = $_POST['dd_hour'];
	$min = $_POST['dd_min'];
	$sec = $_POST['dd_sec'];
	$url = $_POST['dd_url'];
	$des = $_POST['dd_des'];
	$rpt = $_POST['dd_rpt'];
	$frq_rpt = $_POST['dd_frq_rpt'];
	$nbr_jr_av = $_POST['dd_nbr_jr_av'];
	$nbr_jr_ap = $_POST['dd_nbr_jr_ap'];
	$past = $_POST['dd_past'];
	$yester = $_POST['dd_yester'];
	$tod = $_POST['dd_tod'];
	$imm = $_POST['dd_imm'];
	$tom = $_POST['dd_tom'];
	$futur = $_POST['dd_futur'];
	$visible = $_POST['dd_visible'];
	// why do people leave this crap on?! turn it OFF OFF OFF!
	if ( ini_get('magic_quotes_gpc') )
	{	$title = stripslashes($title);
		$url = stripslashes($url);
		$des = stripslashes($des);
		$past = stripslashes($past);
		$yester = stripslashes($yester);
		$tod = stripslashes($tod);
		$imm = stripslashes($imm);
		$tom = stripslashes($tom);
		$futur = stripslashes($futur);
	}	
	if ( empty($title) or empty($month) or empty($year) or empty($day))
	{	?>
		<div class="error"><p><strong><?php _e('Fill all required fields', 'wpdday');?></strong></p></div>
		<?php 
	}	
	else {
	$sql_max_rank = "SELECT MAX(rank) FROM " . WP_DDAY_TABLE;
	$req_max_rank = mysql_query($sql_max_rank);
	$max_rank = mysql_result($req_max_rank, 0);
	$rank = $max_rank+1;
	if ($hour == 0) { $hour = 0;}
	if ($min == 0) { $min = 0;}
	if ($sec == 0) { $sec = 0;}
	$timestamp = gmmktime($hour, $min, $sec, $month, $day, $year);
	$sql = "INSERT INTO " . WP_DDAY_TABLE . " SET title='" . mysql_escape_string($title)
		 . "', url='" . mysql_escape_string($url) . "', date='" .$timestamp
		 . "', nbr_jr_av='" .$nbr_jr_av . "', nbr_jr_ap='" .$nbr_jr_ap
		 . "', rpt='" . $rpt . "', frq_rpt='" . $frq_rpt
		 . "', des='" . mysql_escape_string($des) . "', past='" . mysql_escape_string($past) 
		 . "', yester='" . mysql_escape_string($yester) . "', tod='" . mysql_escape_string($tod) 
		 . "', imm='" . mysql_escape_string($imm) . "', tom='" . mysql_escape_string($tom) 
		 . "', futur='" . mysql_escape_string($futur) . "', rank='" .$rank. "', visible='" . mysql_escape_string($visible) . "'";
	     
	$wpdb->get_results($sql);
	
	$sql = "SELECT ddayID FROM " . WP_DDAY_TABLE . " WHERE title='" . mysql_escape_string($title) . "'";
	$result = $wpdb->get_results($sql);
	
	if ( empty($result) || empty($result[0]->ddayID) )
	{ ?>
		<div class="error"><p><strong><?php _e('Failure:', 'wpdday');?></strong><?php _e('Holy crap you destroyed the internet! That, or something else went wrong when I tried to insert the DDay. Try again? ', 'wpdday');?></p></div>
		<?php 
	}
	else {?>
		<div class="updated"><p><?php _e('Freaking sweet. You just added a new DDay to the database.', 'wpdday');?></p></div>
		<?php 
	}
	}
}
elseif ( $action == 'edit_save' )
{	$title = $_POST['dd_title'];	
	$day = $_POST['dd_day'];
	$month = $_POST['dd_month'];
	$year = $_POST['dd_year'];
	$hour = $_POST['dd_hour'];
	$min = $_POST['dd_min'];
	$sec = $_POST['dd_sec'];
	$url = $_POST['dd_url'];
	$des = $_POST['dd_des'];
	$rpt = $_POST['dd_rpt'];
	$frq_rpt = $_POST['dd_frq_rpt'];
	$nbr_jr_av = $_POST['dd_nbr_jr_av'];
	$nbr_jr_ap = $_POST['dd_nbr_jr_ap'];
	$past = $_POST['dd_past'];
	$yester = $_POST['dd_yester'];
	$tod = $_POST['dd_tod'];
	$imm = $_POST['dd_imm'];
	$tom = $_POST['dd_tom'];
	$futur = $_POST['dd_futur'];
	$visible = $_POST['dd_visible'];
	
	// why do people leave this crap on?! turn it OFF OFF OFF!
	if ( ini_get('magic_quotes_gpc') )
	{	$title = stripslashes($title);
		$url = stripslashes($url);
		$des = stripslashes($des);
		$past = stripslashes($past);
		$yester = stripslashes($yester);
		$tod = stripslashes($tod);
		$imm = stripslashes($imm);
		$tom = stripslashes($tom);
		$futur = stripslashes($futur);
	}
	if ( empty($ddayID) )
	{
	?>
		<div class="error"><p><strong><?php _e('Failure: ', 'wpdday');?></strong> <?php _e('No DDay ID given. Can\'t save nothing. Giving up...', 'wpdday');?></p></div>
		<?php 
	}
	elseif ( (empty($title) or empty($month) or empty($year) or empty($day)) and ($ddayID != 1)) 
	{
		?>
		<div class="error"><p><strong><?php _e('Fill all required fields', 'wpdday');?></strong></p></div>
		<?php	
	}
	else
	{	if ($hour == 0) { $hour = 0;}
		if ($min == 0) { $min = 0;}
		if ($sec == 0) { $sec = 0;}
		$timestamp = gmmktime($hour, $min, $sec, $month, $day, $year);
		$sql = "UPDATE " . WP_DDAY_TABLE . " SET title='" . mysql_escape_string($title)	     
		  . "', url='" . mysql_escape_string($url) . "', date='" .$timestamp
		 . "', nbr_jr_av='" .$nbr_jr_av . "', nbr_jr_ap='" .$nbr_jr_ap
		 . "', rpt='" .$rpt . "', frq_rpt='" .$frq_rpt
		 . "', des='" . mysql_escape_string($des) . "', past='" . mysql_escape_string($past) 
		 . "', yester='" . mysql_escape_string($yester) . "', tod='" . mysql_escape_string($tod) 
		 . "', imm='" . mysql_escape_string($imm) . "', tom='" . mysql_escape_string($tom) 
		 . "', futur='" . mysql_escape_string($futur)	. "', visible='" . mysql_escape_string($visible) . "'"
		 . " where ddayID='" .$ddayID . "'";
		     
		$wpdb->get_results($sql);
		
		$sql = "SElECT ddayID FROM " . WP_DDAY_TABLE . " WHERE title='" . mysql_escape_string($title) . "'";
		$result = $wpdb->get_results($sql);
		
		if ( empty($result) || empty($result[0]->ddayID) )
		{ ?>
			<div class="error"><p><strong><?php _e('Failure: ', 'wpdday');?></strong><?php _e('The Evil Monkey wouldn\'t let me update your Dday. Try again?', 'wpdday');?> </p></div>
			<?php 
		}
		else
		{ ?>
			<div class="updated"><p><?php _e('This Dday updated successfully.', 'wpdday');?></p></div>
			<?php 
		}		
	}
}
elseif ( $action == 'delete' )
{	if ( empty($ddayID) )
	{ ?>
		<div class="error"><p><strong><?php _e('Failure: ', 'wpdday');?></strong><?php _e('No DDay ID given. I guess I deleted nothing successfully.', 'wpdday');?></p></div>
		<?php 
	}
	else
	{
		$sql = "DELETE FROM " . WP_DDAY_TABLE . " WHERE ddayID=" .$ddayID;
		$wpdb->get_results($sql);
		
		$sql = "SELECT ddayID FROM " . WP_DDAY_TABLE . " WHERE ddayID=" .$ddayID;
		$result = $wpdb->get_results($sql);
		
		if ( empty($result) || empty($result[0]->ddayID) )
		{
			?>
			<div class="updated"><p><?php _e('This Dday is deleted successfully', 'wpdday');?></p></div>
			<?php
		}
		else
		{
			?>
			<div class="error"><p><strong><?php _e('Failure: ', 'wpdday');?></strong> <?php _e('Ninjas proved my kung-fu to be too weak to delete that DDay.', 'wpdday');?></p></div>
			<?php
		}		
	}
}
elseif ( $action == 'publish' )
{  	$vis = $_GET['vis'];
	if ( empty($ddayID) )
	{ 
		?>
		<div class="error"><p><strong><?php _e('Failure: ', 'wpdday');?></strong> <?php _e('No DDay ID given.', 'wpdday');?></p></div>
		<?php 
	}
	else
	{	$sql = "UPDATE " . WP_DDAY_TABLE . " SET visible='" . mysql_escape_string($vis) . "'"
		 . " WHERE ddayID=" .$ddayID;
		$wpdb->get_results($sql);	
	
			?>
			<div class="updated"><p>
			<?php 
			if ($vis =='no')
			_e('This DDay is now unpublished.', 'wpdday');
			else 
			_e('This DDay is now published.', 'wpdday');
			?>
			</p></div>
			<?php	
	}
}
if(isset($_GET['new_order'])) 
{
	$id = array();
	$sql_ids = "SELECT rank, ddayID FROM " . WP_DDAY_TABLE ;
	$req_ids = mysql_query($sql_ids) or die(mysql_error());
	while($ids = mysql_fetch_assoc($req_ids))
	$id[$ids['rank']] = $ids['ddayID'];
	$tab = explode(',',$_GET['new_order']);
	$rank = 1;
	foreach($tab as $valeur) {
	$sql_tab = "UPDATE " . WP_DDAY_TABLE . " SET rank= $rank where ddayID= ".$id[$valeur];
	mysql_query($sql_tab) or die(mysql_error());
	$rank++;}
	?>
	<div class="updated"><p><?php _e('Ddays are reordered.', 'wpdday');?></p></div>
	<?php
}
//////////////   Heh.. I said manipulation ////////////////////
?>
<div class="wrap">
	<?php
	if ( $action == 'edit' )
	{ ?>
		<h2>Edit DDay</h2>
		<?php
		if ( empty($ddayID) )
		{?>
			<div class="error"><p><?php _e('I didn\'t get a DDay identifier from the query string. Giving up...', 'wpdday');?></p></div>;
		<?php
		}
		else
		{
			wp_dday_edit_form('edit_save', $ddayID);
		}	
	}
	else
	{ ?>
		<h2><?php _e('Manage DDay', 'wpdday');?></h2>
		<?php wp_dday_display_list();?>
		</div>
		<div class="wrap">
		<h2><?php _e('Add DDay', 'wpdday');?></h2>
		<?php wp_dday_edit_form();?></div>
		<div class="wrap">
		<h2><?php _e('Options and Usage', 'wpdday');?></h2>
		<h3><a href ="edit.php?page=dday/edit-dday.php&action=edit&ddayID=1"><?php _e('Edit Options', 'wpdday');?></a></h3>
		<ul>
			<li><?php _e('To embed the list of the dday active, use the function <b>&lt;?php wp_dday_list(); ?&gt;</b> in your sidebar or use the <b>Widget</b>', 'wpdday');?></li>
			<li><?php _e('You can find the ID of a DDay in Manage DDay at the right of the title [id]', 'wpdday');?></li>
			<li><?php _e('To show specific DDay, use : <b>&lt;?php wp_dday(2);?&gt;</b> where 2 is your DDay id.', 'wpdday');?></li>
			<li><?php _e('To embed a specific DDay in your post, use : <b>[dday=2]</b> where 2 is your DDay id.', 'wpdday');?></li>
		</ul>
	<?php 
	} ?>
</div>
<?php
/**
 * Display code for the listing
 */
function wp_dday_display_list()
{	global $wpdb;
	$ddays = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID != 1 ORDER BY RANK");
	if ( !empty($ddays) )
	{ ?>		
		<ul id="dday_list" class="sortable-list">
            <?php foreach ($ddays as $dday) { ?>
                <li id="dday_<?php echo $dday->rank; ?>" style="border : 2px solid<?php if ($dday->visible=='no') echo ' red'; else echo ' green'; ?>">
				<?php 
				echo '<b>'.date ('d/m/Y H:i:s', $dday->date ).'</b>';
				echo ' : '.$dday->title; 
				echo ' ['.$dday->ddayID.']'?>
				<div class="edition">
				<a href="edit.php?page=dday/edit-dday.php&amp;action=publish&amp;ddayID=<?php echo $dday->ddayID;?>&amp;vis=<?php if ($dday->visible=='no') echo 'yes'; else echo 'no'; ?>">
				<?php if ($dday->visible=='yes') 
				_e('Unpublish', 'wpdday');
				else 
				_e('Publish', 'wpdday');?> </a> 
				<a href="edit.php?page=dday/edit-dday.php&amp;action=edit&amp;ddayID=<?php echo $dday->ddayID;?>"><?php _e('Edit', 'wpdday');?></a>
				<a href="edit.php?page=dday/edit-dday.php&amp;action=delete&amp;ddayID=<?php echo $dday->ddayID;?>" onclick="return confirm('<?php _e('Are you sure you want to delete this dday?', 'wpdday');?>')"><?php _e('Delete', 'wpdday');?>></a>
				</div>
				</li>
            <?php } ?>
			</ul>
			<INPUT type="button" class="button bold" value="<?php _e('Save this Order', 'wpdday');?> &raquo;" onClick="go(Sortable.serialize('dday_list'))" style="margin-top: 10px"/>
		<script type="text/javascript">
            Sortable.create('dday_list');
        </script>        
		<?php
	}
	else
	{
		?>
		<p><?php _e('You haven\'t entered any DDay yet', 'wpdday');?></p>
		<?php	
	}
}
/**
 * Display code for the add/edit form
 */
function wp_dday_edit_form($mode='add', $ddayID=false)
{	global $wpdb;
	$data = false;
	
	if ( $ddayID !== false )
	{
		// this next line makes me about 200 times cooler than you.
		if ( intval($ddayID) != $ddayID )
		{	
			?><div class="error"><p>
			<?php _e('Bad Monkey! No banana!', 'wpdday');?>"</p></div>";<?php
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID=".$ddayID);
			if ( empty($data) )
			{
				?><div class="error"><p>
				<?php _e('I couldn\'t find a DDay linked up with that identifier. Giving up...', 'wpdday');?>
				</p></div>";<?php
				return;
			}
			$data = $data[0];
		}	
	}
	
	?>
	<form name="ddayform" id="ddayform" method="post" action="<?php echo $base_page; ?>">
		<input type="hidden" name="action" value="<?php echo $mode?>"/>
		<input type="hidden" name="ddayID" value="<?php echo $ddayID?>"/>
		<div id="item_manager">
			<div style="float: left; width: 98%; clear: both;" class="top">
				<!-- List URL -->
				<?php if (($ddayID) != 1) 
				{ ?>
					<fieldset>
						<div class="form-row">
							<strong class="label"><?php _e('Title : ', 'wpdday');?></strong>
							<div class="field-widget"><textarea name="dd_title" id="dd_title" class="required" title="Enter a title" cols=45 rows=3><?php if ( !empty($data) ) echo htmlspecialchars($data->title); ?> </textarea></div>
						</div>
						<div class="form-row">
							<strong class="label"><?php _e('Date (D/M/Y H:M:S): ', 'wpdday');?></strong>
							<div class="field-widget"><input type="text" name="dd_day" id="dd_day" class="required validate-day" title="Day" size="1" value="<?php if ( !empty($data) ) echo gmdate( 'd' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_month" id="dd_month" class="required validate-month" title="Month in DIGITS" size="1" value="<?php if ( !empty($data) ) echo gmdate( 'm' , $data->date ); ?>" /></div>
							<div class="field-widget"><input type="text" name="dd_year" id="dd_year" class="required validate-year" title="Year" size="3" value="<?php if ( !empty($data) ) echo gmdate( 'Y' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_hour" id="dd_hour" class="validate-hour" title="Hour" size="1" value="<?php if ( !empty($data) ) echo gmdate( 'H' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_min" id="dd_min" class="validate-min" title="Minute" size="1" value="<?php if ( !empty($data) ) echo gmdate( 'i' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_sec" id="dd_sec" class="validate-sec" title="Second" size="1" value="<?php if ( !empty($data) ) echo gmdate( 's' , $data->date ); ?>"/></div>
						</div>
						<div class="form-row">
							<strong class="label"><?php _e('URL : ', 'wpdday');?></strong>
							<div class="field-widget"><textarea name="dd_url" id="dd_url" class="validate-url" title="Enter an URL (optional)" cols=45 rows=2><?php if ( !empty($data) ) echo htmlspecialchars($data->url); ?></textarea></div>
						</div>
						<div class="form-row">
							<strong class="label"><?php _e('Description : ', 'wpdday');?></strong>
							<div class="field-widget"><textarea name="dd_des" id="dd_des" class="" title="Enter a description (optional)" cols=45 rows=3><?php if ( !empty($data) ) echo htmlspecialchars($data->des); ?></textarea></div>
						</div>
						<div class="form-row">
							<strong class="label"><?php _e('Repeat every : ', 'wpdday');?></strong>
							<div class="field-widget">
							<div class="field-widget"><input name="dd_frq_rpt" id="dd_frq_rpt" class="validate-number" title="Frequence of the repetition" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->frq_rpt); ?>"/></div>
								<select id="dd_rpt" name="dd_rpt" title="Choose your repeatition">
									<option value="0" <?php if ( empty($data) || $data->rpt=='0' ) echo "selected" ?>><?php _e('No', 'wpdday');?></option>
									<option value="1" <?php if ( $data->rpt=='1' ) echo "selected" ?>><?php _e('Day', 'wpdday');?></option>
									<option value="2" <?php if ( $data->rpt=='2' ) echo "selected" ?>><?php _e('Week', 'wpdday');?></option>
									<option value="3" <?php if ( $data->rpt=='3' ) echo "selected" ?>><?php _e('Month', 'wpdday');?></option>
									<option value="4" <?php if ( $data->rpt=='4' ) echo "selected" ?>><?php _e('Year', 'wpdday');?></option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<strong class="label"><?php _e('Show it before during : ', 'wpdday');?></strong>
							<div class="field-widget"><input name="dd_nbr_jr_av" id="dd_nbr_jr_av" class="validate-number" title="Repeat every n unit" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->nbr_jr_av); ?>"/> <?php _e('days', 'wpdday');?> <small>( <?php _e( '0 or nothing = infinite // -1 = No display', 'wpdday');?> )</small></div>
						</div>
						<div class="form-row">
							<strong class="label"><?php _e('Show it after during : ', 'wpdday');?></strong>
							<div class="field-widget"><input name="dd_nbr_jr_ap" id="dd_nbr_jr_ap" class="validate-number validate-frq-rpt" title="Repeat every n unit" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->nbr_jr_ap); ?>"/> <?php _e('days', 'wpdday');?> <small>( <?php _e('0 or nothing = infinite // -1 = No display', 'wpdday');?> )</small></div>
						</div>
						<a onclick="Effect.toggle('format-sup','blind');;return false" href="#1"><?php _e('More options', 'wpdday');?></a>
					<div id="format-sup" <?php if (($mode == 'add') or (empty($data->past) and empty($data->yester) and empty($data->tod) and empty($data->imm)and empty($data->tom) and empty($data->futur))) {echo 'style="display:none"';}?>>
						<?php if (($ddayID) != 1) 
				;} ?>
						<h3><?php _e('Format for past events :', 'wpdday');?></h3>
						<div class="form-row">
							<div class="label"><strong><?php _e('For 2 days or more', 'wpdday');?> : </strong><br/><small>Ex : TITLE% for %DELAY_DAY% days</small></div>
							<div class="field-widget"><textarea name="dd_past" id="dd_past" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->past); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong><?php _e('Since yesterday', 'wpdday');?> : </strong><br/><small>Ex : %TITLE% yesterday</small></div>
							<div class="field-widget"><textarea name="dd_yester" id="dd_yester" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->yester); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong><?php _e('Since today', 'wpdday');?> : </strong><br/><small>Ex : "%TITLE% today</small></div>
							<div class="field-widget"><textarea name="dd_tod" id="dd_tod" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->tod); ?></textarea></div>
						</div>
						<h3><?php _e('Format for futures events', 'wpdday');?> :</h3>
						<div class="form-row">
							<div class="label"><strong><?php _e('Imminent', 'wpdday');?> : </strong><br/><small>Ex : %TITLE% today in %DELAY_HR%h%DELAY_MIN%</small></div>
							<div class="field-widget"><textarea name="dd_imm" id="dd_imm" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->imm); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong><?php _e('Tomorrow', 'wpdday');?> : </strong><br/><small>Ex : %TITLE% tomorrow</small></div>
							<div class="field-widget"><textarea name="dd_tom" id="dd_tom" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->tom); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong><?php _e('In 2 days or more', 'wpdday');?> : </strong><br/><small>Ex : %TITLE% in %DELAY_DAY% days</small></div>
							<div class="field-widget"><textarea name="dd_futur" id="dd_futur" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->futur); ?></textarea></div>
						</div>
						<h3><?php _e('Note', 'wpdday');?> :</h3>
						<ul>
						<li><?php _e('<b>%TITLE%</b> will be replaced by the title', 'wpdday');?></li>
						<li><?php _e('<b>%DELAY_DAY%</b> will be replaced by the delay in days', 'wpdday');?></li>
						<li><?php _e('Only for imminent events <b>%DELAY_HR%</b> will be replaced by the delay in hours and <b>%DELAY_MIN%</b> by the delay in minutes', 'wpdday');?></li>
						<li><?php _e('For Title, Description and Format : <b>%DATE%</b> will be replaced by d/m/y. <b>%DATEandHOUR%</b> adds h:m:s', 'wpdday');?></li>
						<ul>
					</div>
					<?php if (($ddayID) != 1) 
				{ ?>
						<div class="form-row">
							<strong ><?php _e('Visible : ', 'wpdday');?></strong>
							<div class="field-widget">
								<select id="dd_visible" name="dd_visible" title="Choose your repeatition">
									<option value="yes" <?php if ( empty($data) || $data->visible=='yes' ) echo "selected" ?>><?php _e('Yes', 'wpdday');?></option>
									<option value="no" <?php if ( !empty($data) && $data->visible=='no' ) echo "selected" ?>><?php _e('No', 'wpdday');?></option>
								</select>
							</div>
						</div>
			<?php if (($ddayID) != 1) 
				;} ?>						
					</fieldset>
				<input type="submit" name="save" class="button bold" value="<?php _e('Save', 'wpdday');?> &raquo;" />
			</div>
			<div style="clear:both; height:1px;">&nbsp;</div>
		</div>
	</form>
	<script type="text/javascript">
		function formCallback(result, form) {
		window.status = "validation callback for form '" + form.id + "': result = " + result;
		}
		var valid = new Validation('ddayform', {immediate : true, onFormValidate : formCallback});
		Validation.addAllThese([
		['validate-day', '<?php _e('Day : 1 to 31', 'wpdday');?>', {
		mini : 1,
		maxi : 31
		}],
		['validate-month', '<?php _e('Month in DIGIT : 1 to 12', 'wpdday');?>', {
		mini : 1,
		maxi : 12
		}],
		['validate-year', '<?php _e('Year : 1970 to 2040', 'wpdday');?>', {
		mini : 1970,
		maxi : 2040
		}],
		['validate-hour', '<?php _e('Hour : 0 to 23', 'wpdday');?>', {
		mini : 0,
		maxi : 23
		}],
		['validate-min', '<?php _e('Min: 0 to 59', 'wpdday');?>', {
		mini : 0,
		maxi : 59
		}],
		['validate-sec', '<?php _e('Sec : 0 to 59', 'wpdday');?>', {
		mini : 0,
		maxi : 59
		}],
		['validate-frq-rpt', '<?php _e("Show it before + Show it after must be <= Period of repetition in days (frq * unit)", "wpdday");?>', function(){
		var elm = $('dd_rpt');
		var selection = elm.selectedIndex;
		if (selection == 0)	{return true;}
		if (selection == 1)	{var unit = 1;}
		else if (selection == 2) {var unit = 7;}
		else if (selection == 3) {var unit = 31;}
		else if (selection == 4) {var unit = 365.25;} 
		return ((unit * (parseFloat($F('dd_frq_rpt')))) >= parseFloat($F('dd_nbr_jr_av')) + parseFloat($F('dd_nbr_jr_ap')));	
		}]							
		]);
	</script>	
	<?php } ?>