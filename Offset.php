<?php

namespace Time;

class Offset {
	static public function time_days(int $time, int $days, bool $count_start_day=false): int{
		if($count_start_day){
			if($days > 0){
				$days--;
			}
			else{
				$days++;
			}
			
			if(!$days){
				return $time;
			}
		}
		
		return $time += 60*60*24 * $days;
	}
	
	static public function time_months(int $time, int $months, bool $count_start_day=false): int{
		$day 	= date('d', $time);
		$month 	= date('n', $time);
		$year 	= date('Y', $time);
		
		if($months > 0){
			$is_increase 	= true;
			$year 			+= floor($months / 12);
		}
		else{
			$is_increase 	= false;
			$year 			+= ceil($months / 12);
		}
		
		$months 	= $months % 12;
		$month 		+= $months;
		
		if($month > 12){
			$year++;
			$month 	-= 12;
		}
		elseif($month < 1){
			$year--;
			$month 	+= 12;
		}
		
		if(checkdate($month, $day, $year)){
			$time_offset = mktime(0,0,0, $month, $day, $year);
		}
		else{
			while(!checkdate($month, $day, $year)){
				$day--;
			}
			
			$time_offset = mktime(0,0,0, $month, $day, $year);
		}
		
		if($count_start_day){
			if($is_increase){
				$time_offset -= 60*60*24;
			}
			else{
				$time_offset += 60*60*24;
			}
		}
		
		return $time_offset;
	}
}