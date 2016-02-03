<?php
$static_categories = array(
    0 => "Drama",
    1 => "Comedy",
    2 => "Musical",
    3 => "Satire",
    4 => "Romantic",
    5 => "Poetry" ,
    6 => "Monologue",
    7 => "Guided Tours",
    8 => "Photography",
    9 => "Workshop",
    10 => "Stand-Up",
    11 => "Dance",
    12 => "Convention" ,
    13 => "Workshops",
    14 => "Live Show",
    15 => "Pop",
    16 => "Education",
    17 => "Food",
    18 => "Talks",
    19 => "Entertainment",
    20 => "Gaming",
    21 => "History",
    22 => "Musical Comedy",
    23 => "Food Festivals",
    24 => "Indian Music",
    25 => "Sufi",
    26 => "Family",
    27 => "Indian Classical" ,
    28 => "Adventure",
    29 => "Bollywood",
    30 => "International Music" ,
    31 => "Rock",
    32 => "Tennis",
    33 => "Marathon",
    34 => "Fitness",
    35 => "Motorsports",
    36 => "Sports"
);

	require 'inc/curl_call.php';
	require 'inc/connection.inc.php';
	
	$url_categories = ['PL','CT','SP'];
	// $categories = array();
	$count = 0;

	foreach($url_categories as $url_category){
		$url = 'http://in.bookmyshow.com/getJSData/?cmd=GETEVENTLIST&f=json&et=' . $url_category . '&rc=NCR&t=67x1xa33b4x422b361ba&sr=&lt=&lg=';
		$json_response = curl_URL_call($url);

		$json_response = json_decode($json_response, true);
		$json_response = $json_response['BookMyShow'];


		foreach($json_response['arrEvent'] as $event){
			if($event['arrVenues'][0]['RegionCode'] != 'NCR'){
				continue;
			}

			$event_name = trim(str_replace("'", "", $event['EventTitle']));
			$event_description = htmlspecialchars(trim($event['EventSynopsis']), ENT_QUOTES);
			$category_array = $event['GenreArray'];

			$date = (int)$event['arrDates'][0]['ShowDateCode'];
			$date_year = (int) ($date / 10000);
			$date = $date % 10000;
			$date_month = (int) ($date / 100);
			$date = $date % 100;
			$date = $date_year . "-" . $date_month . "-" . $date;
			$date = getTimestamp(strtotime($date));

			$lat = (float)$event['arrVenues'][0]['VenueLatitude'];
			$lng = (float)$event['arrVenues'][0]['VenueLongitude'];
			$venueName = $event['arrVenues'][0]['VenueName'];
			$image = $event['BannerURL'];

			$link = $event['FShareURL'];
			if($link == ''){
				$link = $event['EventWebViewURL'];
			}

			$query = "INSERT INTO `events` (`name`,`description`,`start_time`,`link`,`venue`,`latitude`,`longitude`,`image_url`) VALUES ('$event_name','$event_description','$date','$link','$venueName','$lat','$lng','$image')";
			if(mysqli_query($connection, $query)){
				$id = mysqli_insert_id($connection);
				foreach ($category_array as $cat) {
						$cat_id = array_search($cat, $static_categories);
						$temp = (int)$cat_id;
						$temp++;
						$query = "INSERT INTO `event_category_table` (`event_id`,`category_id`) VALUES ('$id','$temp')";
						// array_push($categories, $cat);
						// $query = "INSERT INTO `event-categories` (`category_name`) VALUES ('$cat')";
						if(mysqli_query($connection, $query)){
							echo "success";
						} else {
							echo "failure";
						}
					echo "\n";
				}
			} else {
				echo "fail";
			}
					// die;
			// echo "\n";

// die;

			// foreach ($category_array as $cat) {
			// 		$cat_id = array_search($cat, $static_categories);
			// 		$query = "INSERT INTO `event_category_table` (`event_id`,`category_id`) VALUES ('$id')"
			// 		// array_push($categories, $cat);
			// 		// $query = "INSERT INTO `event-categories` (`category_name`) VALUES ('$cat')";
			// 		// if(mysqli_query($connection, $query)){
			// 		// 	echo "success<br>";
			// 		// } else {
			// 		// 	echo "failure<br>";
			// 		// }
			// 	// }
			// }
		}
		$count++;
		if($count%20 == 0){
			echo "\n";
		}
	}


	// print_r($static_categories);