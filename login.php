<?php	
	require_once('inc/connection.inc.php');
	
	$error_message = array(
		"Success",
		"Invalid Credentials",
		"Could Not Register, Try Again Later",
	);
	
	if(!isset($_GET['pass']) || !isset($_GET['email'])){
		header('Location: index.php');
	} else {
		$email = trim(strtolower($_GET['email']));
		$password = md5(substr(md5($_GET['pass']),0,30));
		
		$query = "SELECT `id`,`name` FROM `users` WHERE `email`='$email' AND `password`='$password'";
		if($query_run = mysqli_query($connection,$query)){		
			if(mysqli_num_rows($query_run) == 1 ){
				while($query_row = mysqli_fetch_assoc($query_run)){
					$name = $query_row['name'];
					$id = $query_row['id'];
				}
				$error = 0;
			} else {
				$error = 1;
			}
		} else {
			$error = 2;
		}
		
		if($error == 0){
			$sendArray = array( 
				'id' 	=> (int)$id,
				'name'	=> $name
			);
		} else {
			$sendArray = array(
				'message' => $error_message[$error]
			);
		}
		echo json_encode($sendArray); 
	}
?>