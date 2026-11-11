<?php
include('header.php');
if(isset($_GET['param'])){
	$param = unserialize(base64_decode($_GET['param']));
	$page = $param['page'];
	include($page);
}else{
	if($username == "jangeco" || 
		$username == "mrronquez" || 
		$username == "mclegaspi" || 
		$username == "cpagtalunan" || 
		$username == "cbretusto" || 
		$username == "cdcasuyon" ||
		$username == "litanto" ||
		$username == "emcabatbat" ||
		$username == "habathan" ||
		$username == "ndbastian" ||
		$username == "jedelosreyes"
		){ //ISS
		include('admin_homepage/homepage_ISS.php');

	}else if ($username == "dormarm" || $username == "josgabu" || $username == "maralca"){ //QAD
		include('admin_homepage/homepage_QAD.php');

	}else if ($username == "margarc" || $username == "jeannmi" || $username == "roasuncion" || $username == "iggarcia"){ //IAS_SEC
		include('admin_homepage/homepage_IASEC.php');

	}else if ($username == "nsnambayan" || $username == "arasan" || $username == "ncaberde" || $username == "bamelu"){ //FINANCE
		include('admin_homepage/homepage_FIN.php');

	}else if ($username == "ailcomp" || $username == "vcrelativo"){ //EMS
		include('admin_homepage/homepage_EMS.php');

	}else if ($username == "nmvaldez" || $username == "mspenaflorida" || $username == "amnueva"){ //LOG
		include('admin_homepage/homepage_LOG.php');

	}else if ($username == "eppanelo" || $username == "mdtayag" || $username == "jdfarcon"){ //FAC
		include('admin_homepage/homepage_FAC.php');

	}else if ($username == "marbonc" || $username == "dangvil" || $username == "melrobl" || $username == "emmaalf"){ //HRD
		include('admin_homepage/homepage_HRD.php');

	}
	else{
		include('homepage.php');
	}
}
include('footer.php');
?>




