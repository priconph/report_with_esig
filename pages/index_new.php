<?php
include('header.php');
if(isset($_GET['param'])){
	$param = unserialize(base64_decode($_GET['param']));
	$page = $param['page'];
	include($page);
}else{
	if($username == "jangeco" || $username == "naayes" || $username == "apbautista" || $username == "aaleoncio" || $username == "ricmart" || $username == "ragenovea"){ //ISS
		include('admin_homepage/homepage_ISS.php');

	}else if ($username == "dormarm" || $username == "josgabu" || $username == "maralca"){ //QAD
		include('admin_homepage/homepage_QAD.php');

	}else if ($username == "margarc" || $username == "jeannmi" || $username == "ginacan"){ //IAS_SEC
		include('admin_homepage/homepage_IASEC.php');

	}else if ($username == "nsnambayan" || $username == "ccbonagua" || $username == "arasan" || $username == "margsor" || $username == "jlcuadillera" || $username == "kempant" || 
				$username == "lormail"){ //FINANCE
		include('admin_homepage/homepage_FIN.php');

	}else if ($username == "ailcomp" || $username == "vcrelativo"){ //EMS
		include('admin_homepage/homepage_EMS.php');

	}else if ($username == "rmpaulino" || $username == "margodo" || $username == "rebvale"){ //LOG
		include('admin_homepage/homepage_LOG.php');

	}else if ($username == "eppanelo" || $username == "ibmanuevo" || $username == "jdfarcon"){ //FAC
		include('admin_homepage/homepage_FAC.php');

	}else if ($username == "marbonc" || $username == "rptiongco" || $username == "dangvil" || $username == "melrobl" || $username == "emmaalf"){ //HRD
		include('admin_homepage/homepage_FAC.php');

	}
	else{
		include('homepage.php');
	}
}
include('footer.php');
?>




