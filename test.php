<?php	
	require_once('inc/connection.inc.php');
	require_once('inc/function.inc.php');
	
	$id = (int)$_GET['userid'];
	$response_array = array();
	date_default_timezone_set('Asia/Kolkata');
	$current_time = getTimeStamp(time());

	if($id > 0){
		$query = "SELECT * FROM `users` WHERE `id`='$id'";
		if($query_run = mysqli_query($connection,$query)){
			if(mysqli_num_rows($query_run) != 1){
				//
			} else {
				while($query_row = mysqli_fetch_assoc($query_run)){
					$scores = array(
						(int)$query_row['sports_score'],
						(int)$query_row['music_score'],
						(int)$query_row['literary_scrore'],
						(int)$query_row['theatre_score'],
						(int)$query_row['others_score']
					);
				}
					
				$max_score_index = 0;
				for($i=0;$i<5;$i++){
					if($scores[$i] >= $scores[$max_score_index]){
						$max_score_index = $i;
					} else {
						continue;
					}
				}
				$max_score_index++;
				$query = "
					SELECT X.description, X.id, X.name, X.start_time, X.latitude, X.longitude
					FROM `event_categories` Z
						LEFT JOIN event_category_table Y ON Z.id = Y.category_id
						LEFT JOIN events X ON Y.event_id = X.id
					WHERE
						Z.majors='$max_score_index'
					LIMIT 5";
				if($query_run = mysqli_query($connection, $query)){
					while($query_row = mysqli_fetch_assoc($query_run)){
						$short_description = htmlspecialchars_decode(trim(substr($query_row['description'], 0, 300)));
						$response = array(
							'id'			=> (int)$query_row['id'],
							'name'			=> $query_row['name'],
							'start_time'	=> date("jS M Y", strtotime($query_row['start_time'])),
							'latitude'		=> (float)$query_row['latitude'],
							'longitude'		=> (float)$query_row['longitude'],
							'description'	=> strip_tags($short_description . "...")
						);
						array_push($response_array, $response);
					}
				}
			}	
		} else {
			//
		}		
	} else {
		$query = "SELECT `id`,`name`,`start_time`,`latitude`,`longitude`,`description` FROM `events` WHERE `start_time`>='$current_time' ORDER BY `start_time` LIMIT 20";
		if($query_run = mysqli_query($connection,$query)){
			while($query_row = mysqli_fetch_assoc($query_run)){
				$short_description = htmlspecialchars_decode(trim(substr($query_row['description'], 0, 300)));
				$response = array(
					'id'			=> (int)$query_row['id'],
					'name'			=> $query_row['name'],
					'start_time'	=> date("jS M Y", strtotime($query_row['start_time'])),
					'latitude'		=> (float)$query_row['latitude'],
					'longitude'		=> (float)$query_row['longitude'],
					'description'	=> strip_tags($short_description . "...")
				);
				array_push($response_array, $response);
			}
		} else {
			//
		}
	}
	echo json_encode(array("events" => $response_array));
?>