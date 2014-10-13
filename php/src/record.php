<?php
if(!function_exists("is_admin")) { include("include.php"); }

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	include("recordwrite.php");
}
include("recordread.php");

?>
