<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && $_POST['Submit'] == 'Add zone') {
	include("zoneadd.php");
}
if(isset($_GET['delete']) && $_GET['delete'] == 'y') {
	include("zonedelete.php");
}
include("zoneread.php");

?>
