<?php
/*
Author: Mdkart
Author URI: http:/mdkart.fr
Description: Admin tool for the Dday plugin.
*/

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
echo '<script src="'.get_option('siteurl').'/wp-content/plugins/dday/script/scriptaculous/lib/prototype.js" type="text/javascript"></script>';
echo '<script src="'.get_option('siteurl').'/wp-content/plugins/dday/script/scriptaculous/src/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>';
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
<h1 style ="text-align: center;">JAVASCRIPT MUST BE ENABLED</h1>
<p>Many errors possible if javascript not enabled</p></div></noscript>
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
	echo "<div class=\"updated\"><p><strong>Hola,</strong><br /><br />
	This seems to be your first time visiting this page. 
	I've created a database table for you (" . WP_DDAY_TABLE . "). If you want to remove the data, make sure to delete that table after deactivating the plugin.<br /> 
	You can edit default options to <strong>translate this plugin to your language <a href=\"http://mdkart.fr/blog/wp-admin/edit.php?page=dday/edit-dday.php&action=edit&ddayID=1\">here</a></strong>.<br />
	This plugin's website is at <a href=\"http://mdkart.fr/\">http://mdkart.fr/</a></p></div>";
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
		<div class="error"><p><strong>Fill all required fields</strong></p></div>
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
	$timestamp = mktime($hour, $min, $sec, $month, $day, $year);
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
		<div class="error"><p><strong>Failure:</strong> Holy crap you destroyed the internet! That, or something else went wrong when I tried to insert the DDay. Try again? </p></div>
		<?php 
	}
	else {?>
		<div class="updated"><p>Freaking sweet. You just added DDay id <?php echo $result[0]->ddayID;?> to the database.</p></div>
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
		<div class="error"><p><strong>Failure:</strong> No DDay ID given. Can't save nothing. Giving up...</p></div>
		<?php 
	}
	elseif ( (empty($title) or empty($month) or empty($year) or empty($day)) and ($ddayID != 1)) 
	{
		?>
		<div class="error"><p><strong>Fill all required fields</strong></p></div>
		<?php	
	}
	else
	{	if ($hour == 0) { $hour = 0;}
		if ($min == 0) { $min = 0;}
		if ($sec == 0) { $sec = 0;}
		$timestamp = mktime($hour, $min, $sec, $month, $day, $year);
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
			<div class="error"><p><strong>Failure:</strong> The Evil Monkey wouldn't let me update your Dday. Try again? </p></div>
			<?php 
		}
		else
		{ ?>
			<div class="updated"><p>Dday <?php echo $ddayID;?> updated successfully</p></div>
			<?php 
		}		
	}
}
elseif ( $action == 'delete' )
{	if ( empty($ddayID) )
	{ ?>
		<div class="error"><p><strong>Failure:</strong> No DDay ID given. I guess I deleted nothing successfully.</p></div>
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
			<div class="updated"><p>DDay <?php echo $ddayID;?> deleted successfully</p></div>
			<?php
		}
		else
		{
			?>
			<div class="error"><p><strong>Failure:</strong> Ninjas proved my kung-fu to be too weak to delete that DDay.</p></div>
			<?php
		}		
	}
}
elseif ( $action == 'publish' )
{  	$vis = $_GET['vis'];
	if ( empty($ddayID) )
	{ 
		?>
		<div class="error"><p><strong>Failure:</strong> No DDay ID given.</p></div>
		<?php 
	}
	else
	{	$sql = "UPDATE " . WP_DDAY_TABLE . " SET visible='" . mysql_escape_string($vis) . "'"
		 . " WHERE ddayID=" .$ddayID;
		$wpdb->get_results($sql);	
	
			?>
			<div class="updated"><p>DDay <?php echo $ddayID;?> now <?php if ($vis =='no') echo 'un'?>published.</p></div>
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
	<div class="updated"><p>DDay are reordered.</p></div>
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
		{
			echo "<div class=\"error\"><p>I didn't get a DDay identifier from the query string. Giving up...</p></div>";
		}
		else
		{
			wp_dday_edit_form('edit_save', $ddayID);
		}	
	}
	else
	{ ?>
		<h2>Manage DDay</h2>
		<?php wp_dday_display_list();?>
		</div>
		<div class="wrap">
		<h2>Add DDay</h2>
		<?php wp_dday_edit_form();?></div>
		<div class="wrap">
		<h2>Options and Usage</h2>
		<h3><a href ="edit.php?page=dday/edit-dday.php&action=edit&ddayID=1">Edit Options</a></h3>
		<ul>
			<li>To embed the list of the dday active, use the function <b>&lt;?php wp_dday_list(); ?&gt;</b> in your sidebar or use the <b>Widget</b>.</li>
			<li>You can find the ID of a DDay in Manage DDay at the right of the title [id]</li>
			<li>To show specific DDay, use : <b>&lt;?php wp_dday(2);?&gt;</b> where 2 is your DDay id.</li>
			<li>To embed a specific DDay in your post, use : <b>[dday=2]</b> where 2 is your DDay id.</li>
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
				<a href="edit.php?page=dday/edit-dday.php&amp;action=publish&amp;ddayID=<?php echo $dday->ddayID;?>&amp;vis=<?php if ($dday->visible=='no') echo 'yes'; else echo 'no'; ?>"><?php if ($dday->visible=='yes') echo 'Unp'; else echo 'P';?>ublish</a>
				<a href="edit.php?page=dday/edit-dday.php&amp;action=edit&amp;ddayID=<?php echo $dday->ddayID;?>">Edit</a>
				<a href="edit.php?page=dday/edit-dday.php&amp;action=delete&amp;ddayID=<?php echo $dday->ddayID;?>" onclick="return confirm('Are you sure you want to delete this dday?')">Delete></a>
				</div>
				</li>
            <?php } ?>
			</ul>
			<INPUT type="button" class="button bold" value="Save this Order &raquo;" onClick="go(Sortable.serialize('dday_list'))" style="margin-top: 10px"/>
		<script type="text/javascript">
            Sortable.create('dday_list');
        </script>        
		<?php
	}
	else
	{
		?>
		<p>You haven't entered any DDay yet</p>
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
			echo "<div class=\"error\"><p>Bad Monkey! No banana!</p></div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . WP_DDAY_TABLE . " WHERE ddayID=".$ddayID);
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>I couldn't find a DDay linked up with that identifier. Giving up...</p></div>";
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
							<strong class="label">Title : </strong>
							<div class="field-widget"><input name="dd_title" id="dd_title" class="required" title="Enter a title" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->title); ?>"/></div>
						</div>
						<div class="form-row">
							<strong class="label">Date (D/M/Y H:M:S): </strong>
							<div class="field-widget"><input type="text" name="dd_day" id="dd_day" class="required validate-day" title="Day" size="1" value="<?php if ( !empty($data) ) echo date( 'd' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_month" id="dd_month" class="required validate-month" title="Month in DIGITS" size="1" value="<?php if ( !empty($data) ) echo date( 'm' , $data->date ); ?>" /></div>
							<div class="field-widget"><input type="text" name="dd_year" id="dd_year" class="required validate-year" title="Year" size="3" value="<?php if ( !empty($data) ) echo date( 'Y' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_hour" id="dd_hour" class="validate-hour" title="Hour" size="1" value="<?php if ( !empty($data) ) echo date( 'H' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_min" id="dd_min" class="validate-min" title="Minute" size="1" value="<?php if ( !empty($data) ) echo date( 'i' , $data->date ); ?>"/></div>
							<div class="field-widget"><input type="text" name="dd_sec" id="dd_sec" class="validate-sec" title="Second" size="1" value="<?php if ( !empty($data) ) echo date( 's' , $data->date ); ?>"/></div>
						</div>
						<div class="form-row">
							<strong class="label">URL : </strong>
							<div class="field-widget"><input name="dd_url" id="dd_url" class="validate-url" title="Enter an URL (optional)" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->url); ?>"/></div>
						</div>
						<div class="form-row">
							<strong class="label">Description : </strong>
							<div class="field-widget"><input name="dd_des" id="dd_des" class="" title="Enter a description (optional)" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->des); ?>"/></div>
						</div>
						<div class="form-row">
							<strong class="label">Repeat every : </strong>
							<div class="field-widget">
							<div class="field-widget"><input name="dd_frq_rpt" id="dd_frq_rpt" class="validate-number" title="Frequence of the repetition" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->frq_rpt); ?>"/></div>
								<select id="dd_rpt" name="dd_rpt" title="Choose your repeatition">
									<option value="0" <?php if ( empty($data) || $data->rpt=='0' ) echo "selected" ?>>No</option>
									<option value="1" <?php if ( $data->rpt=='1' ) echo "selected" ?>>Day</option>
									<option value="2" <?php if ( $data->rpt=='2' ) echo "selected" ?>>Week</option>
									<option value="3" <?php if ( $data->rpt=='3' ) echo "selected" ?>>Month</option>
									<option value="4" <?php if ( $data->rpt=='4' ) echo "selected" ?>>Year</option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<strong class="label">Show it before during : </strong>
							<div class="field-widget"><input name="dd_nbr_jr_av" id="dd_nbr_jr_av" class="validate-number" title="Repeat every n unit" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->nbr_jr_av); ?>"/> days <small>( 0 or nothing = infinite // -1 = No display )</small></div>
						</div>
						<div class="form-row">
							<strong class="label">Show it after during : </strong>
							<div class="field-widget"><input name="dd_nbr_jr_ap" id="dd_nbr_jr_ap" class="validate-number validate-frq-rpt" title="Repeat every n unit" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->nbr_jr_ap); ?>"/> days <small>( 0 or nothing = infinite // -1 = No display )</small></div>
						</div>
						<a onclick="Effect.toggle('format-sup','blind');;return false" href="#1">More options</a>
					<div id="format-sup" <?php if (($mode == 'add') or (empty($data->past) and empty($data->yester) and empty($data->tod) and empty($data->imm)and empty($data->tom) and empty($data->futur))) {echo 'style="display:none"';}?>>
						<?php if (($ddayID) != 1) 
				;} ?>
						<h3>Format for past events :</h3>
						<div class="form-row">
							<div class="label"><strong>For 2 days or more : </strong><br/><small>Ex : TITLE% for %DELAY_DAY% days</small></div>
							<div class="field-widget"><textarea name="dd_past" id="dd_past" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->past); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong>Since yesterday : </strong><br/><small>Ex : %TITLE% yesterday</small></div>
							<div class="field-widget"><textarea name="dd_yester" id="dd_yester" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->yester); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong>Since today : </strong><br/><small>Ex : "%TITLE% today</small></div>
							<div class="field-widget"><textarea name="dd_tod" id="dd_tod" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->tod); ?></textarea></div>
						</div>
						<h3>Format for futures events :</h3>
						<div class="form-row">
							<div class="label"><strong>Imminent : </strong><br/><small>Ex : %TITLE% today in %DELAY_HR%h%DELAY_MIN%</small></div>
							<div class="field-widget"><textarea name="dd_imm" id="dd_imm" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->imm); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong>Tomorrow : </strong><br/><small>Ex : %TITLE% tomorrow</small></div>
							<div class="field-widget"><textarea name="dd_tom" id="dd_tom" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->tom); ?></textarea></div>
						</div>
						<div class="form-row">
							<div class="label"><strong>In 2 days or more : </strong><br/><small>Ex : %TITLE% in %DELAY_DAY% days</small></div>
							<div class="field-widget"><textarea name="dd_futur" id="dd_futur" cols=45 rows=3 ><?php if ( !empty($data) ) echo htmlspecialchars($data->futur); ?></textarea></div>
						</div>
						<h3>Note :</h3>
						<ul>
						<li><b>%TITLE%</b> will be replace by the title</li>
						<li><b>%DELAY_DAY%</b> will be replace by the delay in days</li>			
						<li>Only for imminent events <b>%DELAY_HR%</b> will be replace by the delay in hours and <b>%DELAY_MIN%</b> by the delay in minutes</li>
						<ul>
					</div>
					<?php if (($ddayID) != 1) 
				{ ?>
						<div class="form-row">
							<strong >Visible : </strong>
							<div class="field-widget">
								<select id="dd_visible" name="dd_visible" title="Choose your repeatition">
									<option value="yes" <?php if ( empty($data) || $data->visible=='yes' ) echo "selected" ?>>Yes</option>
									<option value="no" <?php if ( !empty($data) && $data->visible=='no' ) echo "selected" ?>>No</option>
								</select>
							</div>
						</div>
			<?php if (($ddayID) != 1) 
				;} ?>						
					</fieldset>
				<input type="submit" name="save" class="button bold" value="Save &raquo;" />
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
		['validate-day', 'Day : 1 to 31', {
		mini : 1,
		maxi : 31
		}],
		['validate-month', 'Month in DIGIT : 1 to 12', {
		mini : 1,
		maxi : 12
		}],
		['validate-year', 'Year : 1970 to 2040', {
		mini : 1970,
		maxi : 2040
		}],
		['validate-hour', 'Hour : 0 to 23', {
		mini : 0,
		maxi : 23
		}],
		['validate-min', 'Min: 0 to 59', {
		mini : 0,
		maxi : 59
		}],
		['validate-sec', 'Sec : 0 to 59', {
		mini : 0,
		maxi : 59
		}],
		['validate-frq-rpt', 'Show it before + Show it after must be <= Period of repetition in days (frq * unit)', function(){
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