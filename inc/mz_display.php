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

    // Get the weekday of the given date
    $wkday = date('l',mktime('0','0','0', $month, $day, $year));

    switch($wkday) {
        case 'Monday': $numDaysToMon = 0; break;
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

    // Get date for 7 days from Monday (inclusive)
    for($i=0; $i<7; $i++)
    {
        $dates[$i] = date('Y-m-d',$monday+($seconds_in_a_day*$i));
    }

    return $dates;
}

function mz_nav_games_schedule_nav($mz_get_variables)
{
	$sched_nav = '';
	$mz_schedule_page = get_permalink();
	//sanitize input
	//set week number based on php date or passed parameter from $_GET
	$mz_mz_ng_weeknumber = empty($mz_get_variables['mz_week']) ? date_i18n("W", strtotime(date_i18n('Y-m-d'))) : mz_validate_weeknum($mz_get_variables['mz_week']);
	//Navigate through the weeks
	mz_StartEndDate(date_i18n('Y-m-d'));
	$mz_nav_weeks_text_prev = __('Previous Week');
	$mz_nav_weeks_text_current = __('Current Week');
	$mz_nav_weeks_text_following = __('Following Week');
	$mz_current_year = strftime('%G', strtotime(date_i18n('Y-m-d'))); 
	$num_weeks_in_year =  mz_ng_weeknumber($mz_current_year, 12, 31);
	if (($mz_mz_ng_weeknumber < $num_weeks_in_year) && empty($mz_get_variables['mz_next_yr']))
	{
		$mz_start_end_date = mz_StartEndDate(date_i18n('Y-m-d'));
		mz_pr($mz_start_end_date);
		$mz_following_week = add_query_arg(array('mz_following' => ($mz_mz_ng_weeknumber + 1)));
		$sched_nav .= ' <a href='.$mz_start_end_date[3].'>'.$mz_nav_weeks_text_prev.'</a>';
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.$mz_start_end_date[2].'>'.$mz_nav_weeks_text_following.'</a>';
	}
	else
	{   //BOF following year
		$mz_next_year = isset($mz_get_variables['mz_next_yr']) ? mz_ng_validate_year($mz_get_variables['mz_next_yr']) : "1";
		$mz_mz_ng_weeknumber = ($mz_mz_ng_weeknumber > 40) ? $mz_mz_ng_weeknumber - ($num_weeks_in_year - 1) : $mz_mz_ng_weeknumber;
		$from_the_future_backwards = ($mz_mz_ng_weeknumber == 2) ? $num_weeks_in_year : ($mz_mz_ng_weeknumber - 1);
		$mz_num_weeks_forward = add_query_arg(array('mz_week' => ($mz_mz_ng_weeknumber + 1), 'mz_next_yr' => ($mz_current_year + 1)));
		if ($mz_mz_ng_weeknumber == 01)
		{//if we are in first week of year
			$mz_num_weeks_back = add_query_arg(array('mz_week' => ($num_weeks_in_year - 1)));
			$sched_nav .= ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
		}
		else
		{
$mz_num_weeks_back = add_query_arg(array('mz_week' => ($mz_mz_ng_weeknumber - 1), 'mz_next_yr' => ($mz_current_year + 1)));
			$sched_nav .= ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
		}
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.$mz_num_weeks_forward.'>'.$mz_nav_weeks_text_following.'</a> ';
		$mz_start_end_date = mz_ng_getStartandEndDate($mz_mz_ng_weeknumber,($mz_current_year +1));
	}//EOF Following Year

	$mz_timeframe = array('StartDateTime'=>$mz_start_end_date[0], 'EndDateTime'=>$mz_start_end_date[1], 'SchedNav'=>$sched_nav);

	return $mz_timeframe;
}
?>
