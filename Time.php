<?php

namespace Time;

/*
 *	Default timezone (when retrieving UNIX timestamps, storing in the database and sending to the browser) must at any time by UTC.
 *	Javascript expects UNIX timestamps to be UTC and will automatically apply user timezone.
 *	If your application handles time by a different timezone, Javascript will show the user an incorrect time/date in the browser.
 */
date_default_timezone_set('UTC');

class Time {
	public const TZ_COPENHAGEN = 'Europe/Copenhagen';
	
	static private $timezone;
	static private $format_locale;
	
	static public function init(string $timezone, ?string $format_locale=null){
		self::$timezone 		= $timezone;
		self::$format_locale 	= $format_locale;
	}
	
	static public function format(string $format, int $time, bool $apply_timezone=false): string{
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
			self::$format_locale,
			\IntlDateFormatter::NONE,
			\IntlDateFormatter::NONE,
			$apply_timezone ? self::$timezone : null,
			null,
			$format)
		)->format(self::create_time($time, false));
	}
	
	static public function time_today(): int{
		$time = time() + self::time_local_offset();
		
		return mktime(0,0,0, date('m', $time), date('d', $time), date('Y', $time));
	}
	
	static public function datestamp(?int $time=null, bool $apply_timezone=false): string{
		return self::create_time($time, $apply_timezone)->format('Y-m-d');
	}
	
	static public function timestamp(?int $time=null, bool $apply_timezone=false): string{
		return self::create_time($time, $apply_timezone)->format('Y-m-d H:i:s');
	}
	
	static public function timestamp_rfc(?int $time=null, bool $apply_timezone=false): string{
		return self::create_time($time, $apply_timezone)->format('D, j M Y H:i:s O');
	}
	
	static public function timestamp_ms(): string{
		return self::create_time()->format('Y-m-d H:i:s').substr((string)microtime(), 1, 4);
	}
	
	static public function file_datestamp(?int $time=null): string{
		return self::create_time($time)->format('Y-m-d');
	}
	
	static public function file_timestamp(?int $time=null): string{
		return self::create_time($time)->format('Y-m-d-His');
	}
	
	static public function date(string $date): int{
		$date = trim($date);
		
		//	Date is pure digits without date separator
		if(ctype_digit($date)){
			$parts = [
				substr($date, 0, 2),
				substr($date, 2, 2),
				substr($date, 4, 4)
			];
		}
		//	Date separator is hyphen (-)
		elseif(strpos($date, '-')){
			$parts = explode('-', $date);
		}
		//	Date separator is something else
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
				'y'	=> $parts[2] ?: date('Y')	// If year is unset apply current year
			];
		}
		
		//	Apply full year representation (4 digits) if only 2 digits
		if(strlen($parts['y']) == 2){
			$parts['y'] = substr(date('Y'), 0, 2).$parts['y'];
		}
		
		//	Check if date is valid and return UNIX time
		if(checkdate($parts['m'], $parts['d'], $parts['y'])){
			return mktime(0,0,0, $parts['m'], $parts['d'], $parts['y']);
		}
		
		return -1;
	}
	
	static private function create_time(?int $time=null, bool $apply_timezone=true){
		$timezone = null;
		if($apply_timezone){
			if(!self::$timezone){
				throw new Error('No timezone is defined');
			}
			
			$timezone = new \DateTimeZone(self::$timezone);
		}
		
		return $time ? (new \DateTime('', $timezone))->setTimestamp($time) : new \DateTime('now', $timezone);
	}
	
	static private function time_local_offset(?int $time=null): int{
		return (new \DateTimeZone(self::$timezone))->getOffset($time ? (new \DateTime)->setTimestamp($time) : new \DateTime('now'));
	}
}

class Error extends \Error {}