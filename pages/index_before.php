<?php
include('header.php');
if(isset($_GET['param'])){
	$param = unserialize(base64_decode($_GET['param']));
	$page = $param['page'];
	include($page);
}else{
	if($username == "jangeco" || $username == "naayes" || $username == "apbautista" || $username == "aaleoncio" || $username == "ricmart" ){
		include('homepage_mj.php');
	}else{
		include('homepage.php');
	}
}
include('footer.php');
?>




