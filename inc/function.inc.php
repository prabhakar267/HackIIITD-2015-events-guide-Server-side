<?php
	function getTimeStamp($time){
		return date('Y-m-d h:i:s', $time);
	}
	
	function curl_URL_call($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}