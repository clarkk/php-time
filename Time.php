<?php

namespace Time;

class Time {
	const DEFAULT_TIMEZONE = 'Europe/Copenhagen';
	
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
	
	public static function timestamp_rfc(int $time=0): string{
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
}