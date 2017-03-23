<?php
	
	function redirect_to($new_location) {
		header("Location: " . $new_location);
		exit;
	}

	function make_connection($dbname){
		//connect to the database
		//
		$connection = mysqli_connect("localhost","root","","{$dbname}");
		//check if connection was successful
		if(mysqli_connect_errno($connection)){
			die("Database connection failed.");
		}
		else{
			return $connection;
		}
	}

	function fetch_using_designation($text)
	{
		if(strcasecmp($text, "professor"))
		{
			//'professor' was entered
			if($stmt = $conn->prepare("SELECT Sno,designation,department,email,interests FROM professor_info WHERE designation = 'Professor' or designation = 'Professor and HOD' ;"))
			{
				//query returned true, prepared statement object has been made
				//execute the query
				$stmt->execute();
				//bind the result
				$stmt->bind_result($Sno,$designation,$department,$email,$interests);
				//fetch the results
				while ($stmt->fetch())
				{
					//push the id and the name in an array, to be used later
					array_push($arr, $Sno);
					array_push($arr, $designation);
					array_push($arr, $department);
					array_push($arr, $email);
					array_push($arr, $interests);
			    }
			    $stmt->close();
			    //for every professor of the given name, search for their name in the links table
			    for($i=0;$i < sizeof($arr);$i=$i+5) {
			    	$sno = $arr[$i];
			    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?")){
			    		//bind the parameters
			    		$stmt->bind_param('s',$sno);
			    		$stmt->execute();
			    		$stmt->bind_result($name);
			    		while ($stmt->fetch())
						{
							echo $name . ' ' . $arr[$i+1] . ' ' . $arr[$i+2] . ' ' . $arr[$i+3] . ' ' . $arr[$i+4] . '<br>';
					    }
					    $stmt->close();
			    	}
			    }
			    //no need to execute the rest of the code
			    die();
			}
		}
		else{
			//something else containing professor was entered
			if($stmt = $conn->prepare("SELECT Sno,designation,department,email,interests FROM professor_info WHERE designation LIKE ? ;"))
			{
				//query returned true, prepared statement object has been made
				//bind the parameters
				$stmt->bind_param('s', $text);
				//execute the query
				$stmt->execute();
				//bind the result
				$stmt->bind_result($Sno,$designation,$department,$email,$interests);
				//fetch the results
				while ($stmt->fetch())
				{
					//push the id and the name in an array, to be used later
					array_push($arr, $Sno);
					array_push($arr, $designation);
					array_push($arr, $department);
					array_push($arr, $email);
					array_push($arr, $interests);
			    }
			    $stmt->close();
			    //for every professor of the given name, search for their name in the links table
			    for($i=0;$i < sizeof($arr);$i=$i+5) {
			    	$sno = $arr[$i];
			    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?")){
			    		$stmt->bind_param('s',$sno);
			    		$stmt->execute();
			    		$stmt->bind_result($name);
			    		while ($stmt->fetch())
						{
							echo $name . ' ' . $arr[$i+1] . ' ' . $arr[$i+2] . ' ' . $arr[$i+3] . ' ' . $arr[$i+4] . '<br>';
					    }
					    $stmt->close();
			    	}
			    }
			    //no need to execute the rest of the code
			    die();
			}
		}
	}

	function fetch_using_department($text)
	{
		$arr = ("maths"=>"Mathematics","math"=>"Mathematics","aero"=>"Aeronautical & Automobile Engineering","auto"=>"Aeronautical & Automobile Engineering","automobile"=>"Aeronautical & Automobile Engineering","biomed"=>"Biomedical Engineering","pme"=>"Printing & Media Engineering",
			"ice"=>"Instrumentation & Control Engineering","chem"=>"Chemistry","mechatronics"=>"Mechatronics Engineering","humanities"=>"Humanities & Management","civil"=>"Civil Engineering","it"=>"Information & Communication Technology","ict"=>"Information & Communication Technology",
			"cse"=>"Computer Science & Engineering","computer science"=>"Computer Science & Engineering","mech"=>"Mechanical & Manufacturing Engineering","mechanical"=>"Mechanical & Manufacturing Engineering","mme"=>"Mechanical & Manufacturing Engineering","eee"=>"Electrical and Electronics Engineering","electrical"=>"Electrical and Electronics Engineering","ece"=>"Electronics & Communication Engineering");
		foreach ($arr as $key => $value) {
			if(strcasecmp($key, $text))
			{
				//the user entered one of the keys. substitute them for the corresponding values
				$text = $arr[$key];
				break;
			}
		}
	}

	function check_text($text)
	{
		$arr=("cse"=>);
		//first we will check for designation
		if(stripos($text, "professor")!==false)
		{
			fetch_using_designation($text);
		}
		else
		{
			fetch_using_department($text);
		}
	}

	function search($text)
	{
		$conn = make_connection("research");
		$param = "%".$text."%";
		$arr = array();
		echo $param;
		//if the text entered is name of the professor
		if ($stmt = $conn->prepare("SELECT Sno,name FROM links WHERE name LIKE ? ;")){
			//query returned true, prepared statement object has been made
			//bind the parameters
			$stmt->bind_param('s', $param);
			//execute the query
			$stmt->execute();
			//bind the result
			$stmt->bind_result($id,$name);
			//fetch the results
			while ($stmt->fetch())
			{
				//push the id and the name in an array, to be used later
				array_push($arr, $id);
				array_push($arr, $name);
		    }
		    $stmt->close();
		    //for every professor of the given name, search for their info in the professor_info table
		    for($i=0;$i < sizeof($arr);$i=$i+2) {
		    	$sno = $arr[$i];
		    	if($stmt = $conn->prepare("SELECT designation,department,email,interests FROM professor_info WHERE Sno = ?")){
		    		$stmt->bind_param('s',$sno);
		    		$stmt->execute();
		    		$stmt->bind_result($designation,$department,$email,$interests);
		    		while ($stmt->fetch())
					{
						echo $arr[$i+1] . ' ' . $designation . ' ' . $department . ' ' . $email . ' ' . $interests . '<br>';
				    }
				    $stmt->close();
		    	}
		    }
		    //no need to execute the rest of the code
		    die();
		}
		//if the text entered is interests
		if($stmt = $conn->prepare("SELECT Sno,designation,department,email,interests FROM professor_info WHERE interests LIKE ? ;"))
		{
			//query returned true, prepared statement object has been made
			//bind the parameters
			$stmt->bind_param('s', $param);
			//execute the query
			$stmt->execute();
			//bind the result
			$stmt->bind_result($Sno,$designation,$department,$email,$interests);
			//fetch the results
			while ($stmt->fetch())
			{
				//push the id and the name in an array, to be used later
				array_push($arr, $Sno);
				array_push($arr, $designation);
				array_push($arr, $department);
				array_push($arr, $email);
				array_push($arr, $interests);
		    }
		    $stmt->close();
		    //for every professor of the given name, search for their name in the links table
		    for($i=0;$i < sizeof($arr);$i=$i+5) {
		    	$sno = $arr[$i];
		    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?")){
		    		$stmt->bind_param('s',$sno);
		    		$stmt->execute();
		    		$stmt->bind_result($name);
		    		while ($stmt->fetch())
					{
						echo $name . ' ' . $arr[$i+1] . ' ' . $arr[$i+2] . ' ' . $arr[$i+3] . ' ' . $arr[$i+4] . '<br>';
				    }
				    $stmt->close();
		    	}
		    }
		    //no need to execute the rest of the code
		    die();
		}
		//if the text entered is either name or interests, then its sorted. otherwise, check what kind of text has been entered
		check_text($text);
		die();
	}
?>