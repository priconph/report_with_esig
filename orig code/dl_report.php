<?php
$pkid_report = $_GET['id'];

require_once( '../../media/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
require_once('../class/oop.php'); //OOP Path

$esig_path = "../../hris/esignature/";
$result = "";
$array_fields = array('approver_username','approver_status','date_time_approved','esig_cell_ordinate','approver_remarks','file_name','file_path','esig_sheet_no');
$table 	   	= 'vw_report';
$joins 	   	= '';
$sql_where 	= 'WHERE main_pkid='.$pkid_report;
$sql_order 	= '';
$sql_limit 	= '';
$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);

$ctr = 1;
$approved = array();
while($row = mysqli_fetch_array($result)){
	$approver_username	= $row['approver_username'];
	$approver_status	= $row['approver_status'];
	$date_time_approver	= $row['date_time_approved'];
	$esig_cell_ordinate	= $row['esig_cell_ordinate'];
	$esig_sheet_no		= ($row['esig_sheet_no'] - 1);
	// $esig_sheet_no		= ($row['esig_sheet_no']);
	$approver_remarks	= $row['approver_remarks'];
	$file_name			= $row['file_name'];
	$file_name_ext		= end(explode('.',$file_name));
	$file 				= $row['file_path'].$pkid_report.'.'.$file_name_ext;	
	$file_path 			= $row['file_path'].'/';	
	
	if($approver_status != 'APPROVED') {
		break;
	} else {
		$approved['username'][$ctr] 	= $approver_username;
		$approved['dt_approved'][$ctr] 	= $date_time_approver;
		$approved['cell_ordi'][$ctr]	= $esig_cell_ordinate;
		$approved['remarks'][$ctr]	 	= $approver_remarks;
		$approved['sheet_no'][$ctr]	 	= $esig_sheet_no;
		$approved['esig'][$ctr]	 		= return_empno($approver_username);
		$ctr++;
	}	
}	

$inputFileName = $file;
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
$excel2 = PHPExcel_IOFactory::createReader($inputFileType);
$excel2 = $excel2->load($inputFileName); // Empty Sheet

$objDrawing 	= new PHPExcel_Worksheet_Drawing();
$objPHPExcel 	= new PHPExcel();
/* Affix e-signature to approved report */

for($i=1; $i<$ctr;$i++) {
	$esig_file = $esig_path.$approved['esig'][$i];
	if(!file_exists($esig_file)) {
		$esig_file = $esig_path.'default.png';
	} 
	
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$excel2->setActiveSheetIndex($approved['sheet_no'][$i]);
	$objDrawing->setWorksheet($excel2->getActiveSheet());
	$objDrawing->setPath($esig_file);
	$objDrawing->setCoordinates($approved['cell_ordi'][$i]);
	$objDrawing->setHeight(30);
		
	$excel2->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun('Electronic Approval of Report System:')->getFont()->setBold(true);
	$excel2->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun("\r\n");
	$objCommentRichText  = $excel2->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun("Date/Time Approved: ".$approved['dt_approved'][$i]."\r\n");
	// $objCommentRichText->getFont()->setName('Lucida Fax');
	if($approved['remarks'][$i] != '') {
		$excel2->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun('Remarks: '.$approved['remarks'][$i]);
	}

}

$objWriter = PHPExcel_IOFactory::createWriter($excel2, $inputFileType);
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Cache-Control: max-age=0");
ob_clean();
$objWriter->save('php://output');

function return_empno($username) {
	require_once('../class/oop.php'); //OOP Path
	$return = $_POST;
	$result = "";
	$array_fields = array('EmpNo');
	$table 	   	= 'db_hris.vw_emp_hris';
	$joins 	   	= '';
	$sql_where 	= 'WHERE username="'.$username.'"';
	$sql_order 	= '';
	$sql_limit 	= '';
	$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
	if($row = mysqli_fetch_array($result)){
		$empno = $row['EmpNo'];
	} 
	return $empno. ".png";
}
?>