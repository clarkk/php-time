<?php

namespace Time;

class Time {
	const DEFAULT_TIMEZONE 	= 'Europe/Copenhagen';
	
	static private $locale;
	
	static public function init(string $locale){
		self::$locale = $locale;
	}
	
	static public function format(string $format, int $time_utc, bool $apply_timezone=true): string{
		// https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
		
		/*
			y 		= year
			yy 		= year short
			M 		= month numeric
			MM 		= month numeric zerofill
			MMM 	= month alpha short
			MMMM 	= month alpha full
			d 		= day
			dd 		= day zerofill
			H 		= 24-hour
			HH 		= 24-hour zerofill
			m 		= minutes
			mm 		= minutes zerofill
			s 		= seconds
			ss 		= seconds zerofill
			S 		= fractional second
			SS 		= fractional second 2 decimals
			SSS 	= fractional second 3 decimals
			e 		= weekday numeric
			ee 		= weekday numeric zerofill
			eee 	= weekday alpha short
			eeee 	= weekday alpha full
			w 		= week of year
			ww 		= week of year zerofill
		*/
		
		return (new \IntlDateFormatter(
			self::$locale,
			\IntlDateFormatter::NONE,
			\IntlDateFormatter::NONE,
			$apply_timezone ? self::DEFAULT_TIMEZONE : null,
			null,
			$format)
		)->format(self::create_time($time_utc, false));
	}
	
	static public function time_today(): int{
		$time = time() + self::time_local_offset();
		
		return mktime(0,0,0, date('m', $time), date('d', $time), date('Y', $time));
	}
	
	static public function datestamp(int $time=0, bool $apply_timezone=true): string{
		return self::create_time($time, $apply_timezone)->format('Y-m-d');
	}
	
	static public function timestamp(int $time=0, bool $apply_timezone=true): string{
		return self::create_time($time, $apply_timezone)->format('Y-m-d H:i:s');
	}
	
	static public function timestamp_ms(): string{
		return self::create_time()->format('Y-m-d H:i:s').substr((string)microtime(), 1, 4);
	}
	
	static public function timestamp_rfc(int $time=0, bool $apply_timezone=true): string{
		return self::create_time($time, $apply_timezone)->format('D, j M Y H:i:s O');
	}
	
	static public function file_datestamp(int $time=0, bool $apply_timezone=true): string{
		return self::create_time($time, $apply_timezone)->format('Y-m-d');
	}
	
	static public function file_timestamp(int $time=0, bool $apply_timezone=true): string{
		return self::create_time($time, $apply_timezone)->format('Y-m-d-His');
	}
	
	static public function time_offset(bool $interval_month, int $interval_value, int $time): int{
		//	months
		if($interval_month){
			$day 	= date('d', $time);
			$month 	= date('n', $time);
			$year 	= date('Y', $time);
			
			for($i=0; $i<$interval_value; $i++){
				if($month == 12){
					$month = 1;
					$year++;
				}
				else{
					$month++;
				}
			}
			
			if(checkdate($month, $day, $year)){
				return mktime(0,0,0, $month, $day, $year) - (60*60*24);
			}
			else{
				while(!checkdate($month, $day, $year)){
					$day--;
				}
				
				return mktime(0,0,0, $month, $day, $year);
			}
		}
		//	days
		else{
			return $time + (60*60*24* ($interval_value - 1));
		}
	}
	
	static public function date(string $date): int{
		$date = trim($date);
		
		if(ctype_digit($date)){
			$parts = [
				substr($date, 0, 2),
				substr($date, 2, 2),
				substr($date, 4, 4)
			];
		}
		elseif(strpos($date, '-')){
			$parts = explode('-', $date);
		}
		else{
			$parts = preg_split('/\D/', $date);
		}
		
		$parts[0] = empty($parts[0]) ? 0 : (int)$parts[0];
		$parts[1] = empty($parts[1]) ? 0 : (int)$parts[1];
		$parts[2] = empty($parts[2]) ? 0 : (int)$parts[2];
		
		if(strlen($parts[0]) == 4){
			$parts = [
				'y'	=> $parts[0],
				'm'	=> $parts[1],
				'd'	=> $parts[2]
			];
		}
		else{
			$parts = [
				'd'	=> $parts[0],
				'm'	=> $parts[1],
				'y'	=> $parts[2] ?: date('Y')
			];
		}
		
		if(strlen($parts['y']) == 2){
			$parts['y'] = substr(date('Y'), 0, 2).$parts['y'];
		}
		
		if(checkdate($parts['m'], $parts['d'], $parts['y'])){
			return mktime(0,0,0, $parts['m'], $parts['d'], $parts['y']);
		}
		
		return -1;
	}
	
	static private function create_time(int $time_utc=0, bool $apply_timezone=true){
		$timezone = $apply_timezone ? new \DateTimeZone(self::DEFAULT_TIMEZONE) : null;
		
		if($time_utc){
			return (new \DateTime('', $timezone))->setTimestamp($time_utc);
		}
		else{
			return new \DateTime('now', $timezone);
		}
	}
	
	static private function time_local_offset(int $time_utc=0): int{
		return (new \DateTimeZone(self::DEFAULT_TIMEZONE))->getOffset($time_utc ? (new \DateTime)->setTimestamp($time_utc) : new \DateTime('now'));
	}
}