<?php
session_start();
require_once("../includes/included_functions.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../assets/stylesheets/result_style.css">
</head>
<body>
<div id="header">
	<div id="heading">
		<h1>
			<a href="index.html">FACULTY SEARCH</a>
		</h1>
	</div>
</div>
<!-- <div id="prev_form">
	<form method="POST" action="search.php">
		<h1 id="search_h">search</h1><input type="text" name="search_query" required id="input_text"><input type="submit" name="submit" value="GO">
	</form>
</div> -->
<?php
if(isset($_POST["submit"]))
{
	$_POST["search_query"] = trim($_POST["search_query"]);//trim the leading and trailing while spaces
	if($_POST["search_query"] == ''){
		redirect_to("index.html");
	}
	$search_query = $_POST["search_query"];
	check_text($search_query);
	if(isset($_SESSION["empty_result"]))
	{
		echo "<h3 id='fail_mes'>No results found.</h3>";
	}
	else if(isset($_SESSION["numberofrows"]))
	{
		echo "
		<div class='table-wrapper'>
			<table>
				<tr>
					<th>Name</th>
					<th>Designation</th>
					<th>Department</th>
					<th>Email</th>
					<th>Interests</th>
				</tr>";
		for ($i=0; $i < $_SESSION["numberofrows"]; $i++) 
		{ 
			echo "<tr>".
			"<td>".$_SESSION["name".$i]."</td>".
			"<td>".$_SESSION["designation".$i]."</td>".
			"<td>".$_SESSION["department".$i]."</td>".
			"<td>".$_SESSION["email".$i]."</td>".
			"<td>".$_SESSION["interests".$i]."</td>"
			."</tr>";
		}
		echo "</table></div>";
	}
	session_unset();
}
?>
</body>
</html>