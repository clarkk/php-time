<?php

namespace Time;

class Time {
	const DEFAULT_TIMEZONE = 'Europe/Copenhagen';
	
	static public function timestamp(int $base_time=0): string{
		return date('Y-m-d H:i:s', self::time_local_offset($base_time));
	}
	
	static public function file_timestamp(int $base_time=0): string{
		return date('Y-m-d-His', self::time_local_offset($base_time));
	}
	
	static private function time_local_offset(int $base_time=0, string $timezone=self::DEFAULT_TIMEZONE){
		return ($base_time ?: time()) + (new \DateTimeZone($timezone))->getOffset(new \DateTime('now'));
	}
}