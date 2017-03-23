<?php
require_once("../includes/included_functions.php");
if(isset($_POST["submit"]))
{
	$search_query = $_POST["search_query"];
	search($search_query);
}
?>