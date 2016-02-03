<?php
	require_once('inc/connection.inc.php');
	require_once('inc/function.inc.php');
	
	$MAJORS = ['sports_score', 'music_score', 'literary_scrore', 'theatre_score', 'others_score'];
	
	if(!isset($_GET['userid']) || !isset($_GET['eventid']) || !isset($_GET['score'])){
		//
	} else {
		$event_id = (int)$_GET['eventid'];
		$user_id = (int)$_GET['userid'];
		$score = (int)$_GET['score'];
		
		$query = "SELECT Z.majors FROM event_category_table Y LEFT JOIN event_categories Z ON Y.category_id = Z.id WHERE Y.event_id='$event_id' LIMIT 1";
		if($query_run = mysqli_query($connection, $query)){
			while($query_row = mysqli_fetch_assoc($query_run)){
				$major_id = (int)$query_row['majors'];
			}
		}
		
		$column_name = $MAJORS[$major_id - 1];
		$query = "UPDATE `users` SET `" . $column_name . "` = `" . $column_name . "` + '$score' WHERE `id`='$user_id'";
		if(mysqli_query($connection, $query)){
			echo true;
		} else {
			echo false;
		}
		
	}