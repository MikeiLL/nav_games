<?php
function mZ_nav_games_show_dates( $atts )
{
    $return = '';
	$mz_timeframe = mz_nav_games_schedule_nav($_GET);
    $return .= $mz_timeframe['SchedNav'];
    $return .= '<div style="border:1px solid blue">';
    $return .= '<br/> This week begins on <b>'. $mz_timeframe['StartDateTime']. '</b>';
    $return .= ' and ends on <b>'. $mz_timeframe['EndDateTime']. '</b>';
    $return .= '</div>';
    return $return;
}//EOF mZ_games_show_dates

function mz_nav_games_schedule_nav($mz_get_variables)
{
	$sched_nav = '';
	$mz_schedule_page = get_permalink();
	//sanitize input
	//set week number based on php date or passed parameter from $_GET
	$mz_mz_ng_weeknumber = empty($mz_get_variables['mz_week']) ? date_i18n("W", strtotime(date_i18n('Y-m-d'))) : mz_validate_weeknum($mz_get_variables['mz_week']);
	//Navigate through the weeks
	$mz_nav_weeks_text_prev = __('Previous Week');
	$mz_nav_weeks_text_current = __('Current Week');
	$mz_nav_weeks_text_following = __('Following Week');
	$mz_current_year = strftime('%G', strtotime(date_i18n('Y-m-d'))); 
	$num_weeks_in_year =  mz_ng_weeknumber($mz_current_year, 12, 31);
	echo $num_weeks_in_year."<hr/> "." ".$mz_mz_ng_weeknumber." ".$mz_current_year."<hr/>";
	if (($mz_mz_ng_weeknumber < $num_weeks_in_year) && empty($mz_get_variables['mz_next_yr']))
	{
	    echo "<br/>one";
	    if ($mz_mz_ng_weeknumber == 01)
		{//if we are in first week of year
		echo "<br/> and it is.";
			$mz_num_weeks_back = add_query_arg(array('mz_week' => ($num_weeks_in_year - 1)));
			}else{
		$mz_num_weeks_back = add_query_arg(array('mz_week' => ($mz_mz_ng_weeknumber - 1)));
		}
		$mz_num_weeks_forward = add_query_arg(array('mz_week' => ($mz_mz_ng_weeknumber + 1)));
		$sched_nav .= ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.$mz_num_weeks_forward.'>'.$mz_nav_weeks_text_following.'</a>';
		$mz_start_end_date = mz_ng_getStartandEndDate($mz_mz_ng_weeknumber,$mz_current_year);
	}
	else
	{   //BOF following year
	    echo "<br/>two";
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
		echo "<br/>three";
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
