<?php
function mZ_nav_games_show_dates( $atts )
{
    $return = '<br/>';
	$mz_timeframe = mz_nav_games_schedule_nav($_GET);
    $return .= $mz_timeframe['SchedNav'];
    $return .= '<div style="border:1px solid blue">';
    $return .= '<br/> This week begins on <b>'. $mz_timeframe['StartDateTime'];
    $return .= '</b> and ends on <b>'. $mz_timeframe['EndDateTime']. '</b>';
    $return .= '<style type="text/css">';
    $return .= 'tr:nth-child(even) {background: #CCC}';
    $return .= 'tr:nth-child(odd) {background: #FFF}';
    $return .= 'table {border-spacing: 10px;border-collapse: separate;}';
    $return .= 'td {padding: 10px;}';
    $return .= '</style>';
    $return .= '<table>';
    foreach (mz_so_week_from_monday($mz_timeframe['StartDateTime']) as $day){
        $return    .= '<tr><td>'.date('l dS \o\f F Y', strtotime($day)).'</td></tr>';
    }
    $return .= '</table>';
    $return .= '</div>';
    return $return;
}//EOF mZ_games_show_dates

function mz_so_week_from_monday($date) {
    //from http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
    list($year, $month, $day) = explode("-", $date);
    
    // Timestamp of the monday for that week
    $date = mktime('0','0','0', $month, $day, $year);

    $seconds_in_a_day = 86400;

    // Get date for 7 days from Monday (inclusive)
    for($i=0; $i<7; $i++)
    {
        $dates[$i] = date('Y-m-d',$date+($seconds_in_a_day*$i));
    }

    return $dates;
}

function mz_nav_games_schedule_nav($mz_get_variables)
{
	$sched_nav = '';
	$mz_schedule_page = get_permalink();
	//sanitize input
	//set week number based on php date or passed parameter from $_GET
	$mz_date = empty($mz_get_variables['mz_date']) ? date_i18n('Y-m-d') : mz_ng_validate_date($mz_get_variables['mz_date']);
	//Navigate through the weeks
	$mz_start_end_date = mz_ng_getNavDates($mz_date);
	$mz_nav_weeks_text_prev = __('Previous Week');
	$mz_nav_weeks_text_current = __('Current Week');
	$mz_nav_weeks_text_following = __('Following Week');
		$sched_nav .= ' <a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[3]))).'>'.$mz_nav_weeks_text_prev.'</a>';
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[2]))).'>'.$mz_nav_weeks_text_following.'</a>';

	$mz_timeframe = array('StartDateTime'=>$mz_start_end_date[0], 'EndDateTime'=>$mz_start_end_date[1], 'SchedNav'=>$sched_nav);

	return $mz_timeframe;
}
?>
