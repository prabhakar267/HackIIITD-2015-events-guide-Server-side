<?php
	$google_maps_api_key = "AIzaSyBoiLyedheMPA5rx7erg5vNXYKyrp5b1C4";

	require_once 'inc/connection.inc.php';
	require_once 'inc/function.inc.php';
	$final_response = array();
	
	$restraunt_response = array();
	$art_gallery_response = array();
	$shopping_response = array();
	$hotels_response = array();
	
	if(!isset($_GET['eventid']) || !isset($_GET['userid'])){
		header("Location: index.php");
	} else {
		$event_id = (int)$_GET['eventid'];
		$user_id = (int)$_GET['userid'];
		
		curl_URL_call('http://csinsit.org/prabhakar/aloogobhi/increase-priority.php?userid=' . $user_id . '&eventid=' . $event_id . '&score=1');
		
		$query = "SELECT `latitude`,`longitude`,`description`,`venue`,`link` FROM `events` WHERE `id`='$event_id'";
		if($query_run = mysqli_query($connection, $query)){
			while($query_row = mysqli_fetch_assoc($query_run)){
				
				$event_categories = array();
				
				$query_category = "SELECT X.category_name FROM event_category_table E INNER JOIN event_categories X ON E.category_id = X.id WHERE E.event_id='$event_id'";
				if($query_category_run = mysqli_query($connection, $query_category)){
					while($query_category_row = mysqli_fetch_assoc($query_category_run)){
						if($query_category_row['category_name'] != ""){
							array_push($event_categories, $query_category_row['category_name']);
						}
					}
				}
				$lat = (float)$query_row['latitude'];
				$lng = (float)$query_row['longitude'];
				$long_description = strip_tags(htmlspecialchars_decode($query_row['description']));
				$venue = $query_row['venue'];
				$link = $query_row['link'];
			}
		}
		
		if(isset($lat) && isset($lng)){
			$url_init = 'https://maps.googleapis.com/maps/api/place/radarsearch/json?location=' . $lat .',' . $lng . '&radius=5000&types=restaurant&key=' . $google_maps_api_key;
			$response = curl_URL_call($url_init);
			$response = json_decode($response, true);
			$count = 0;
			foreach($response['results'] as $place){
				$count++;
				if($count >= 4){
					break;
				}
				$placeID = $place['place_id'];
				
				$url_finl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $placeID . "&key=" . $google_maps_api_key;
				$response_place = curl_URL_call($url_finl);
				$response_place = json_decode($response_place, true);
	
				$place_website = $response_place['result']['website'];
				if($place_website == ""){
					$place_website = $response_place['result']['url'];
				}
				
				$place_array = array(
					"name"		=> trim($response_place['result']['name']),
					"address"	=> trim($response_place['result']['vicinity']),
					"contact"	=> trim($response_place['result']['international_phone_number']),
					"latitude"	=> (float)$response_place['result']['geometry']['location']['lat'],
					"longitude"	=> (float)$response_place['result']['geometry']['location']['lng'],
					"website" 	=> ($place_website == "") ? null : trim($place_website),
					//"rating"	=> (float)$response_place['result']['rating']
				);
				array_push($restraunt_response, $place_array);
			}
			
			//art-gallery + museum
			$url_init = 'https://maps.googleapis.com/maps/api/place/radarsearch/json?location=' . $lat .',' . $lng . '&radius=5000&types=art_gallery|museum&key=' . $google_maps_api_key;
			$response = curl_URL_call($url_init);
			$response = json_decode($response, true);
			$count = 0;
			foreach($response['results'] as $place){
				$count++;
				if($count >= 4){
					break;
				}
				$placeID = $place['place_id'];
				
				$url_finl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $placeID . "&key=" . $google_maps_api_key;
				$response_place = curl_URL_call($url_finl);
				$response_place = json_decode($response_place, true);
	
				$place_website = $response_place['result']['website'];
				if($place_website == ""){
					$place_website = $response_place['result']['url'];
				}
				
				$place_array = array(
					"name"		=> trim($response_place['result']['name']),
					"address"	=> trim($response_place['result']['vicinity']),
					"contact"	=> trim($response_place['result']['international_phone_number']),
					"latitude"	=> (float)$response_place['result']['geometry']['location']['lat'],
					"longitude"	=> (float)$response_place['result']['geometry']['location']['lng'],
					"website" 	=> ($place_website == "") ? null : trim($place_website),
					//"rating"	=> (float)$response_place['result']['rating']
				);
				array_push($art_gallery_response, $place_array);
			}
			
			//shopping + clothing store
			$url_init = 'https://maps.googleapis.com/maps/api/place/radarsearch/json?location=' . $lat .',' . $lng . '&radius=5000&types=clothing_store|shopping_mall|store|shoe_store&key=' . $google_maps_api_key;
			$response = curl_URL_call($url_init);
			$response = json_decode($response, true);
			$count = 0;
			foreach($response['results'] as $place){
				$count++;
				if($count >= 4){
					break;
				}
				$placeID = $place['place_id'];
				
				$url_finl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $placeID . "&key=" . $google_maps_api_key;
				$response_place = curl_URL_call($url_finl);
				$response_place = json_decode($response_place, true);
	
				$place_website = $response_place['result']['website'];
				if($place_website == ""){
					$place_website = $response_place['result']['url'];
				}
				
				$place_array = array(
					"name"		=> trim($response_place['result']['name']),
					"address"	=> trim($response_place['result']['vicinity']),
					"contact"	=> trim($response_place['result']['international_phone_number']),
					"latitude"	=> (float)$response_place['result']['geometry']['location']['lat'],
					"longitude"	=> (float)$response_place['result']['geometry']['location']['lng'],
					"website" 	=> ($place_website == "") ? null : trim($place_website),
					//"rating"	=> (float)$response_place['result']['rating']
				);
				array_push($shopping_response, $place_array);
			}
			
			//lodging
			$url_init = 'https://maps.googleapis.com/maps/api/place/radarsearch/json?location=' . $lat .',' . $lng . '&radius=5000&types=lodging&key=' . $google_maps_api_key;
			$response = curl_URL_call($url_init);
			$response = json_decode($response, true);
			$count = 0;
			foreach($response['results'] as $place){
				$count++;
				if($count >= 4){
					break;
				}
				$placeID = $place['place_id'];
				
				$url_finl = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $placeID . "&key=" . $google_maps_api_key;
				$response_place = curl_URL_call($url_finl);
				$response_place = json_decode($response_place, true);
	
				$place_website = $response_place['result']['website'];
				if($place_website == ""){
					$place_website = $response_place['result']['url'];
				}
				
				$place_array = array(
					"name"		=> trim($response_place['result']['name']),
					"address"	=> trim($response_place['result']['vicinity']),
					"contact"	=> trim($response_place['result']['international_phone_number']),
					"latitude"	=> (float)$response_place['result']['geometry']['location']['lat'],
					"longitude"	=> (float)$response_place['result']['geometry']['location']['lng'],
					"website" 	=> ($place_website == "") ? null : trim($place_website),
					//"rating"	=> (float)$response_place['result']['rating']
				);
				array_push($hotels_response, $place_array);
			}
		}
		
		$final_response = array(
			"long_description"	=> $long_description,
			"category"			=> $event_categories,
			"venue"				=> $venue,
			"link"				=> $link,
			"hotels"			=> $hotels_response,
			"shopping"			=> $shopping_response,
			"art_gallery"		=> $art_gallery_response,
			"restraunt"			=> $restraunt_response
		);
		
		echo json_encode($final_response);
	}