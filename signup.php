<?php	
	require_once('inc/connection.inc.php');
	
	$error_message = array(
		"Email Already Registered",
		"Registered Successfully",
		"Could Not Register, Try Again Later",
		"Invalid Email Address"
	);
	
	if(!isset($_GET['name']) || !isset($_GET['pass']) || !isset($_GET['email'])){
		header("Location: index.php");
	} else {
		$name = $_GET['name'];
		$password = md5(substr(md5($_GET['pass']),0,30));
		$emailID = trim(strtolower($_GET['email']));
		
		if(filter_var($emailID, FILTER_VALIDATE_EMAIL) === false){
			$error = 3;
		} else {
			$query = "SELECT `name` FROM `users` WHERE `email`='$emailID'";
			if($query_run = mysqli_query($connection,$query)){
				if(mysqli_num_rows($query_run) > 0 ){
					$error = 0;
				} else {
					$query = "INSERT INTO `users` (`name`,`password`,`email`) VALUES ('$name','$password','$emailID')";
					if(mysqli_query($connection,$query)){
						$error = 1;
						$id = mysqli_insert_id($connection);
					} else {
						$error = 2;
					}
				}
			} else {
				$error = 2;
			}		
		}
		
		if($error == 1){
			$response = array(
				"success" 	=> true,
				"id"		=> $id
			);
		} else {
			$response = array(
				"success"	=> false,
				"message"	=> $error_message[$error]
			);
		}			
		echo json_encode($response);
	}
?>