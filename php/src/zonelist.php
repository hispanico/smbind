<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	include("zoneadd.php");
}
if(isset($_GET['delete']) && $_GET['delete'] == 'y') {
	include("zonedelete.php");
}
include("zoneread.php");

?>
