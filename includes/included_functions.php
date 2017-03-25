<?php
	
	function redirect_to($new_location) {
		header("Location: " . $new_location);
		exit;
	}

	function make_connection($dbname)
	{
		//connect to the database
		$connection = mysqli_connect("localhost","root","","{$dbname}");
		//check if connection was successful
		if(mysqli_connect_errno($connection))
		{
			die("Database connection failed.");
		}
		else
		{
			return $connection;
		}
	}

	function fetch_using_designation($text)
	{
		$conn = make_connection("research");
		$arr = array();
		if(strcasecmp($text, "professor")==0)
		{
			//only 'professor' was entered
			if($stmt = $conn->prepare("SELECT count(*) FROM professor_info WHERE designation = 'Professor' or designation = 'Professor and HOD' ;"))
			{
				//query returned true, prepared statement object has been made
				//execute the query
				$stmt->execute();
				//bind the result
				$row_count=0;//no. of rows returned
				$stmt->bind_result($count);
				while($stmt->fetch())
				{
					$row_count=$count;
				}
				if($row_count==0)
				{
					$_SESSION["empty_result"] = true;
				}
				else
				{
					$_SESSION["numberofrows"] = $row_count;
				}
				$stmt->close();
				if($stmt = $conn->prepare("SELECT Sno,designation,department,email,interests FROM professor_info WHERE designation = 'Professor' or designation = 'Professor and HOD'"))
				{
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
				    $j = 0;
				    for($i=0;$i < sizeof($arr);$i=$i+5) 
				    {
				    	$sno = $arr[$i];
				    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?"))
				    	{
				    		//bind the parameters
				    		$stmt->bind_param('s',$sno);
				    		$stmt->execute();
				    		$stmt->bind_result($name);
				    		while ($stmt->fetch())
							{
								
								$_SESSION["name".$j]= $name;
								$_SESSION["designation".$j] = $arr[$i+1];
								$_SESSION["department".$j] = $arr[$i+2];
								$_SESSION["email".$j] = $arr[$i+3];
								$_SESSION["interests".$j]=$arr[$i+4];
								// echo "Name ".$j." : ".$name."<br>";
								// echo "Desgination ".$j." : ".$_SESSION["designation".$j]."<br>";
								// echo "Department ".$j." : ".$_SESSION["department".$j]."<br>";
								// echo "email ".$j." : ".$_SESSION["email".$j]."<br>";
								// echo "Interests ".$j." : ".$_SESSION["interests".$j]."<br>";
								// echo "<br><br>";
								$j++;
						    }
						    $stmt->close();
				    	}
				    }
				}
				return;				
			}
		}
		else
		{
			//something else containing professor was entered
			$param = "%".$text."%";
			if($stmt = $conn->prepare("SELECT count(*) FROM professor_info WHERE designation LIKE ? ;"))
			{
				//query returned true, prepared statement object has been made
				//execute the query
				$stmt->bind_param('s', $param);
				$stmt->execute();
				//bind the result
				$row_count=0;//no. of rows returned
				$stmt->bind_result($count);
				while($stmt->fetch())
				{
					$row_count=$count;
				}
				if($row_count==0)
				{
					$_SESSION["empty_result"] = true;
				}
				else
				{
					$_SESSION["numberofrows"] = $row_count;
				}
				$stmt->close();
				if($stmt = $conn->prepare("SELECT Sno,designation,department,email,interests FROM professor_info WHERE designation LIKE ? ;"))
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
				    $j=0;
				    for($i=0;$i < sizeof($arr);$i=$i+5) 
				    {
				    	$sno = $arr[$i];
				    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?"))
				    	{
				    		$stmt->bind_param('s',$sno);
				    		$stmt->execute();
				    		$stmt->bind_result($name);
				    		while ($stmt->fetch())
							{
								$_SESSION["name".$j]= $name;
								$_SESSION["designation".$j] = $arr[$i+1];
								$_SESSION["department".$j] = $arr[$i+2];
								$_SESSION["email".$j] = $arr[$i+3];
								$_SESSION["interests".$j]=$arr[$i+4];
								// echo "Name ".$j." : ".$name."<br>";
								// echo "Desgination ".$j." : ".$_SESSION["designation".$j]."<br>";
								// echo "Department ".$j." : ".$_SESSION["department".$j]."<br>";
								// echo "email ".$j." : ".$_SESSION["email".$j]."<br>";
								// echo "Interests ".$j." : ".$_SESSION["interests".$j]."<br>";
								// echo "<br><br>";
								$j++;
						    }
						    $stmt->close();
				    	}
				    }
				    //no need to execute the rest of the code
				}
			}
			return;
		}
	}

	function check_for_department($text)
	{
		//associative array consisting of all the possible acronyms that could be used by the user as keys and the value as it is in the database
		$arr = array("maths"=>"Mathematics","math"=>"Mathematics","aero"=>"Aeronautical & Automobile Engineering","auto"=>"Aeronautical & Automobile Engineering","aeronautical"=>"Aeronautical & Automobile Engineering","automobile"=>"Aeronautical & Automobile Engineering","biomed"=>"Biomedical Engineering","pme"=>"Printing & Media Engineering","ice"=>"Instrumentation & Control Engineering","chem"=>"Chemistry","biotech"=>"Biotechnology","mechatronics"=>"Mechatronics Engineering","humanities"=>"Humanities & Management","civil"=>"Civil Engineering","it"=>"Information & Communication Technology","ict"=>"Information & Communication Technology","cse"=>"Computer Science & Engineering","cs"=>"Computer Science & Engineering","computer science"=>"Computer Science & Engineering","mech"=>"Mechanical & Manufacturing Engineering","mechanical"=>"Mechanical & Manufacturing Engineering","mme"=>"Mechanical & Manufacturing Engineering","eee"=>"Electrical and Electronics Engineering","electrical"=>"Electrical & Electronics Engineering","electronics"=>"Electronics & Communication Engineering","ece"=>"Electronics & Communication Engineering","enc"=>"Electronics & Communication Engineering","mathematics"=>"Mathematics","aeronautical & automobile engineering"=>"Aeronautical & Automobile Engineering","biomedical"=>"Biomedical Engineering","biomedical engineering"=>"Biomedical Engineering","computer applications"=>"Computer Applications","chemical"=>"Chemical Engineering","chemical engineering"=>"Chemical Engineering","biotechnology"=>"Biotechnology","instrumentation"=>"Instrumentation & Control Engineering","mechatronics engineering"=>"Mechatronics Engineering","print & media"=>"Print & Media Engineering","humanities & management"=>"Humanities & Management","civil engineering"=>"Civil Engineering","physics"=>"physics","information & communication technology"=>"Information & Communication Technology","computer science & engineering"=>"Computer Science & Engineering","mechanical & manufacturing engineering"=>"Mechanical & Manufacturing Engineering","electrical & electronics engineering"=>"Electrical & Electronics Engineering","electronics & communication engineering"=>"Electronics & Communication Engineering","electrical & electronics"=>"Electrical & Electronics Engineering","e&e"=>"Electrical & Electronics Engineering","e&c"=>"Electronics & Communication Engineering");
		if(stripos($text, "and"))
		{
			//since all the department names contain & in the database, replace and with &
			$text=str_replace("and", "&", $text);
		}
		foreach ($arr as $key => $value) 
		{
			if(strcasecmp($key, $text) == 0)
			{
				//the user entered one of the keys. return the department name to the calling functions
				return $value;
			}
		}
		return 0;
	}

	function fetch_using_department($text)
	{
		$conn = make_connection("research");
		$arr = array();
		if($stmt = $conn->prepare("SELECT count(*) FROM professor_info WHERE department = ? ;"))
		{
			//query returned true, prepared statement object has been made
			//execute the query
			$stmt->bind_param('s', $text);
			$stmt->execute();
			//bind the result
			$row_count=0;//no. of rows returned
			$stmt->bind_result($count);
			while($stmt->fetch())
			{
				$row_count=$count;
			}
			if($row_count==0)
			{
				$_SESSION["empty_result"] = true;
			}
			else
			{
				$_SESSION["numberofrows"] = $row_count;
			}
			$stmt->close();
			if($stmt = $conn->prepare("SELECT Sno,designation,department,email,interests FROM professor_info WHERE department = ? ;"))
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
			    $j=0;
			    //for every professor of the given name, search for their name in the links table
			    for($i=0;$i < sizeof($arr);$i=$i+5) 
			    {
			    	$sno = $arr[$i];
			    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?"))
			    	{
			    		$stmt->bind_param('s',$sno);
			    		$stmt->execute();
			    		$stmt->bind_result($name);
			    		while ($stmt->fetch())
						{
							$_SESSION["name".$j]= $name;
							$_SESSION["designation".$j] = $arr[$i+1];
							$_SESSION["department".$j] = $arr[$i+2];
							$_SESSION["email".$j] = $arr[$i+3];
							$_SESSION["interests".$j]=$arr[$i+4];
							// echo "Name ".$j." : ".$name."<br>";
							// echo "Desgination ".$j." : ".$_SESSION["designation".$j]."<br>";
							// echo "Department ".$j." : ".$_SESSION["department".$j]."<br>";
							// echo "email ".$j." : ".$_SESSION["email".$j]."<br>";
							// echo "Interests ".$j." : ".$_SESSION["interests".$j]."<br>";
							// echo "<br><br>";
							$j++;
					    }
					    $stmt->close();
			    	}
		  		}
			}
			return;
		}
	}

	function check_text($text)
	{
		//first we will check for designation
		if(stripos($text, "professor")!==false or stripos($text, "senior")!==false or stripos($text, "assistant")!==false or stripos($text, "associate")or stripos($text, "director")!==false or stripos($text, "hod")!==false or stripos($text, "head")!==false)
		{
			//the user entered something containing professor
			fetch_using_designation($text);
			return;
		}
		else if(check_for_department($text))
		{
			//the user entered department maybe
			$ret_text = check_for_department($text);
			fetch_using_department($ret_text);
			return;
		}
		else
		{
			//the user must've entered name or interests
			search($text);
			return;
		}
	}

	function search($text)
	{
		//the text entered is neither a department nor a designation
		$conn = make_connection("research");
		$param = "%".$text."%";
		$arr = array();
		//if the text entered is name of the professor
		if($stmt = $conn->prepare("SELECT count(*) FROM links WHERE name LIKE ? ;"))
		{
			//query returned true, prepared statement object has been made
			//execute the query
			$stmt->bind_param('s', $param);
			$stmt->execute();
			//bind the result
			$row_count=0;//no. of rows returned
			$stmt->bind_result($count);
			while($stmt->fetch())
			{
				$row_count=$count;
			}
			if($row_count==0)
			{
				goto check_interests;
				$_SESSION["empty_result"] = true;
			}
			else
			{
				$_SESSION["numberofrows"] = $row_count;
			}
			$stmt->close();
			if ($stmt = $conn->prepare("SELECT Sno,name FROM links WHERE name LIKE ? ;"))
			{
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
			    $j=0;
			    //for every professor of the given name, search for their info in the professor_info table
			    for($i=0;$i < sizeof($arr);$i=$i+2) 
			    {
			    	$sno = $arr[$i];
			    	if($stmt = $conn->prepare("SELECT designation,department,email,interests FROM professor_info WHERE Sno = ?")){
			    		$stmt->bind_param('s',$sno);
			    		$stmt->execute();
			    		$stmt->bind_result($designation,$department,$email,$interests);
			    		while ($stmt->fetch())
						{
							$_SESSION["name".$j]= $arr[$i+1];
							$_SESSION["designation".$j] = $designation;
							$_SESSION["department".$j] = $department;
							$_SESSION["email".$j] = $email;
							$_SESSION["interests".$j]=$interests;
							$j++;
					    }
					    $stmt->close();
			    	}
		    	}
			}
			return;
		}
		check_interests:
		if($stmt = $conn->prepare("SELECT count(*) FROM professor_info WHERE interests LIKE ? ;"))
		{
			//query returned true, prepared statement object has been made
			//execute the query
			$stmt->bind_param('s', $param);
			$stmt->execute();
			//bind the result
			$row_count=0;//no. of rows returned
			$stmt->bind_result($count);
			while($stmt->fetch())
			{
				$row_count=$count;
			}
			if($row_count==0)
			{
				$_SESSION["empty_result"] = true;
			}
			else
			{
				$_SESSION["numberofrows"] = $row_count;
			}
			$stmt->close();
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
			    $j=0;
			    //for every professor of the given name, search for their name in the links table
			    for($i=0;$i < sizeof($arr);$i=$i+5) 
			    {
			    	$sno = $arr[$i];
			    	if($stmt = $conn->prepare("SELECT name FROM links WHERE Sno = ?"))
			    	{
			    		$stmt->bind_param('s',$sno);
			    		$stmt->execute();
			    		$stmt->bind_result($name);
			    		while ($stmt->fetch())
						{
							$_SESSION["name".$j]= $name;
							$_SESSION["designation".$j] = $arr[$i+1];
							$_SESSION["department".$j] = $arr[$i+2];
							$_SESSION["email".$j] = $arr[$i+3];
							$_SESSION["interests".$j]=$arr[$i+4];
							// echo "Name ".$j." : ".$name."<br>";
							// echo "Desgination ".$j." : ".$_SESSION["designation".$j]."<br>";
							// echo "Department ".$j." : ".$_SESSION["department".$j]."<br>";
							// echo "email ".$j." : ".$_SESSION["email".$j]."<br>";
							// echo "Interests ".$j." : ".$_SESSION["interests".$j]."<br>";
							// echo "<br><br>";
							$j++;
					    }
					    $stmt->close();
			    	}
			    }
			}
			return;
		}	
	}
?>