<?php

namespace Time;

class Time {
	const DEFAULT_TIMEZONE 	= self::TZ_COPENHAGEN;
	
	const TZ_COPENHAGEN 	= 'Europe/Copenhagen';
	
	static public function time_today(): int{
		$time = self::time_local_offset();
		
		return mktime(0,0,0, date('m', $time), date('d', $time), date('Y', $time));
	}
	
	static public function timestamp(int $time=0): string{
		return date('Y-m-d H:i:s', self::time_local_offset($time));
	}
	
	static public function timestamp_ms(): string{
		return date('Y-m-d H:i:s'.substr((string)microtime(), 1, 4), self::time_local_offset());
	}
	
	static public function timestamp_rfc(int $time=0): string{
		return date('D, j M Y H:i:s O', self::time_local_offset($time));
	}
	
	static public function file_datestamp(int $time=0): string{
		return date('Y-m-d', self::time_local_offset($time));
	}
	
	static public function file_timestamp(int $time=0): string{
		return date('Y-m-d-His', self::time_local_offset($time));
	}
	
	static public function time_local_offset(int $time=0, string $timezone=self::DEFAULT_TIMEZONE): int{
		return $time ?: time() + (new \DateTimeZone($timezone))->getOffset(new \DateTime('now'));
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
		else{
			$parts = preg_split('/\D/', $date);
		}
		
		$parts[0] = empty($parts[0]) ? 0 : $parts[0];
		$parts[1] = empty($parts[1]) ? 0 : $parts[1];
		$parts[2] = empty($parts[2]) ? 0 : $parts[2];
		
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
}