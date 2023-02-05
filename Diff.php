<?php

namespace Time;

class Diff {
	private const DAY = 60*60*24;
	
	static public function working_days(int $time_start, int $time_end): int{
		$time 	= self::time_date(date('n', $time_start), date('d', $time_start), date('Y', $time_start));
		$days 	= ceil((self::time_date(date('n', $time_end), date('d', $time_end), date('Y', $time_end)) - $time) / self::DAY) + 1;
		$count 	= 0;
		for($i=0; $i<$days; $i++){
			if(date('N', $time) < 6){
				$count++;
			}
			
			$time += self::DAY;
		}
		
		return $count;
	}
	
	static private function time_date(int $month, int $day, int $year): int{
		return mktime(0,0,0, $month, $day, $year);
	}
}