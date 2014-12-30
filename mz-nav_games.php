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

    include_once plugin_dir_path( __FILE__ )."inc/mz_display.php";

	add_shortcode('mz-nav-games-show-dates', 'mZ_nav_games_show_dates' );

function mz_ng_getNavDates($date) {
    /*Gets a YYYY-mm-dd date and returns an array of four dates:
        start of requested week
        end of requested week 
        following week start date
        previous week start date
    adapted from http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
    */
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

function mz_ng_validate_date( $string ) {
	if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',$string))
	{
		return $string;
	}
	else
	{
		return "mz_validate_weeknum error";
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
