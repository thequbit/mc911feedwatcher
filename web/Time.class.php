<?php

	class Time
	{
		function StartTime()
		{
			// record start time
			$mtime = microtime(); 
			$mtime = explode(" ",$mtime); 
			$mtime = $mtime[1] + $mtime[0];
			$starttime = $mtime;
		
			return $starttime;
		}
		
		function TotalTime($starttime)
		{
			// calculate time taken
			$mtime = microtime(); 
			$mtime = explode(" ",$mtime); 
			$mtime = $mtime[1] + $mtime[0]; 
			$endtime = $mtime; 
			$totaltime = ($endtime - $starttime);
			
			return $totaltime;
		}
	}

?>