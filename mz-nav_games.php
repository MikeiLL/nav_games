<?php
/**
Plugin Name: mZoo Nav Games
Description: MZ Nav Games is designed for development of plugins that need to navigate through a calendar.
Version: 1.0
Author: mZoo.org
Author URI: http://www.mZoo.org/
Plugin URI: http://www.mzoo.org/mz-nav-games-wp

Based on API written by Devin Crossman.
*/

//define plugin path and directory
define( 'mZ_nav_games_DIR', plugin_dir_path( __FILE__ ) );
define( 'mZ_nav_games_URL', plugin_dir_url( __FILE__ ) );

//register activation and deactivation hooks
register_activation_hook(__FILE__, 'mZ_nav_games_activation');
register_deactivation_hook(__FILE__, 'mZ_nav_games_deactivation');

load_plugin_textdomain('mz-nav-games-api',false,'mz-nav-games-schedule/languages');

function mZ_nav_games_activation() {
	//Don't know if there's anything we need to do here.
}

function mZ_nav_games_deactivation() {
	// actions to perform once on plugin deactivation go here
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mZ_nav_games_uninstall');

function mZ_nav_games_uninstall(){
	//actions to perform once on plugin uninstall go here
	delete_option('mz_nav_games_options');
}

if ( is_admin() )
{ // admin actions
	add_action ('admin_menu', 'mz_nav_games_settings_menu');

	function mz_nav_games_settings_menu() {
		//create submenu under Settings
		add_options_page ('Nav Games Settings','Nav Games',
		'manage_options', __FILE__, 'mz_nav_games_settings_page');
	}

	function mz_nav_games_settings_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<form action="options.php" method="post">
				<?php settings_fields('mz_nav_games_options'); ?>
				<?php do_settings_sections('mz_nav_games'); ?>
				<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
			</form>
		</div>
		<?php
	}

	// Register and define the settings
	add_action('admin_init', 'mz_nav_games_admin_init');

	function mz_nav_games_admin_init(){
		register_setting(
			'mz_nav_games_options',
			'mz_nav_games_options',
			'mz_nav_games_validate_options'
		);

		add_settings_section(
			'mz_nav_games_main',
			'Nav Games Setting',
			'mz_nav_games_section_text',
			'mz_nav_games'
		);

		add_settings_field(
			'mz_nav_games_source_name',
			'Nav Games Field: ',
			'mz_nav_games_source_name',
			'mz_nav_games',
			'mz_nav_games_main'
		);
	}

	// Draw the section header

	function mz_nav_games_section_text() { ?>
		<p><?php _e('Set something here, maybe:') ?></p>
		<?php
	}
	
	// Display and fill the form field
	function mz_nav_games_source_name() {
		// get option 'mz_source_name' value from the database
		$options = get_option( 'mz_nav_games_options',__('Option Not Set') );
		$mz_nav_game_a = (isset($options['mz_nav_game_a'])) ? $options['mz_nav_game_a'] : _e('Some Setting');
		// echo the field
		echo "<input id='mz_nav_game_a' name='mz_nav_games_options[mz_nav_game_a]' type='text' value='$mz_nav_game_a' />";
	}

	// Validate user input (we want text only)
	function mz_nav_games_validate_options( $input ) {
	    foreach ($input as $key => $value)
	    {
				$valid[$key] = wp_strip_all_tags(preg_replace( '/\s/', '', $input[$key] ));
				if( $valid[$key] != $input[$key] )
				{
					add_settings_error(
						'mz_nav_games_text_string',
						'mz_nav_games_texterror',
						'Does not appear to be valid ',
						'error'
					);
				}
			}

		return $valid;
	}

}
else
{// non-admin enqueues, actions, and filters


	foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
        include_once $file;

	add_shortcode('mz-nav-games-show-dates', 'mZ_nav_games_show_dates' );

}//EOF Not Admin


function mz_ng_getStartAndEndDate($week, $year) {
  // Adding leading zeros for weeks 1 - 9.
  $date_string = $year . 'W' . sprintf('%02d', $week);
  $return = array();
  $return[0] = date('Y-m-d', strtotime($date_string));//not date('Y-n-j
  $return[1] = date('Y-m-d', strtotime($date_string . '7'));
  return $return;
}

function mz_StartEndDate($date) {
    list($year, $month, $day) = explode("-", $date);

    // Get the weekday of the given date
    $wkday = date('l',mktime('0','0','0', $month, $day, $year));

    switch($wkday) {
        case 'Monday': $numDaysToMon = 7; break;
        case 'Tuesday': $numDaysToMon = 1; break;
        case 'Wednesday': $numDaysToMon = 2; break;
        case 'Thursday': $numDaysToMon = 3; break;
        case 'Friday': $numDaysToMon = 4; break;
        case 'Saturday': $numDaysToMon = 5; break;
        case 'Sunday': $numDaysToMon = 6; break;   
    }

    // Timestamp of the monday for that week
    $monday = mktime('0','0','0', $month, $day, $year);
    $seconds_in_a_day = 86400;
    $return[0] = date('Y-m-d',$monday);// requested week
    $return[1] = date('Y-m-d',$monday+($seconds_in_a_day*$numDaysToMon));// end of requested week
    $return[2] = date('Y-m-d',$monday+($seconds_in_a_day*($numDaysToMon))); // following week
    $return[3] = date('Y-m-d',$monday+($seconds_in_a_day*($numDaysToMon - ($numDaysToMon+7)))); // previous week
    return $return;
}

//May need this for week iteration:
/*  w e e k n u m b e r  -------------------------------------- //
mz_ng_weeknumber returns a week number from a given date (>1970, <2030)
Wed, 2003-01-01 is in week 1
Mon, 2003-01-06 is in week 2
Wed, 2003-12-31 is in week 53, next years first week
Be careful, there are years with 53 weeks.
// ------------------------------------------------------------ */
function mz_ng_weeknumber ($y, $m, $d) {
  $wn = strftime("%W",mktime(0,0,0,$m,$d,$y));
  $wn += 0; # wn might be a string value
  $firstdayofyear = getdate(mktime(0,0,0,1,1,$y));

  if ($firstdayofyear["wday"] != 1)    # if 1/1 is not a Monday, add 1
  {
      $wn += 1;
  }
  return ($wn);
}

function mz_ng_validate_weeknum( $string ) {
	if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',$string))
	{
		return $string;
	}
	else
	{
		return "mz_validate_weeknum error";
	}
}

function mz_ng_validate_year( $string ) {
 	if (preg_match('/^\d{4}$/',$string))
 	{
	 	return $string;
 	}
 	else
 	{
 		return "mz_ng_validate_year error";
 	}
}

//Format arrays for display in development
function mz_ng_pr($data)
{
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}
?>
