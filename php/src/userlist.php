<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	include("useradd.php");
}
if(isset($_GET['delete']) && $_GET['delete'] == 'y') {
	include("userdelete.php");
}

include("userlistread.php");

?>
