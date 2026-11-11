<?php
$pkid_report = $_GET['id'];
error_reporting(E_ALL);
ini_set('display.errors', 1);
require_once( '../../media/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
require_once('../class/oop.php'); //OOP Path
/* Get password of excel */
$array_fields_pw = array('excel_password');
$table_pw 	   	= 'tbl_excel_pw';
$joins_pw 	   	= '';
$sql_where_pw 	= '';
$sql_order_pw 	= '';
$sql_limit_pw 	= '';
$result_pw = REPORT::getInstance()->select_query($array_fields_pw,$table_pw,$joins_pw,$sql_where_pw,$sql_order_pw,$sql_limit_pw);
if($row_pw = mysqli_fetch_array($result_pw)) {
	$excel_password = $row_pw['excel_password'];
}

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
	$esig_cell_ordinate	= str_replace(" ","",$row['esig_cell_ordinate']);
	$esig_sheet_no		= ((trim($row['esig_sheet_no'],' ')) - 1);
	$approver_remarks	= $row['approver_remarks'];
	$file_name			= $row['file_name'];
	$file_name_ext		= end(explode('.',$file_name));
	$file 				= $row['file_path'].'/'.$pkid_report.'.'.$file_name_ext;	
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
if(file_exists($file)) {
	$inputFileName = $file;
	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	$objPHPExcel = PHPExcel_IOFactory::createReader($inputFileType);
	$objPHPExcel = $objPHPExcel->load($inputFileName); 

	$sheetCount = $objPHPExcel->getSheetCount();
	/* Affix e-signature to approved report */
	for($i=1; $i<$ctr;$i++) {
		// $esig_file = $esig_path.$approved['esig'][$i];
		// if(!file_exists($esig_file)) {
		// 	$esig_file = $esig_path.'default.png';
		// } 		
		// $objDrawing = new PHPExcel_Worksheet_Drawing();
		// if(($approved['sheet_no'][$i]+1) > $sheetCount) {
		// 	$objPHPExcel->setActiveSheetIndex($sheetCount-1);
		// } else {
		// 	$objPHPExcel->setActiveSheetIndex($approved['sheet_no'][$i]);
		// }
		// $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		// $objDrawing->setPath($esig_file);
		// $objDrawing->setCoordinates($approved['cell_ordi'][$i]);
		// $objDrawing->setHeight(70);
			
		// /* Insert Date and time approved and remarks */	
		// $objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun('Electronic Report Approval System:')->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun("\r\n");
		// $objCommentRichText  = $objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun("Date/Time Approved: ".date('M d, Y h:i:s',strtotime($approved['dt_approved'][$i]))."\r\n");
		
		// if($approved['remarks'][$i] != '') {
		// 	$objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->getText()->createTextRun('Remarks: '.$approved['remarks'][$i]);
		// }
		// $objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->setWidth('300pt');
		// $objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->setHeight('100pt');
		// $objPHPExcel->getActiveSheet()->getComment($approved['cell_ordi'][$i])->setMarginLeft('150pt');
		
		/* Lock the worksheet from any changes */
		// $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		// $objPHPExcel->getSecurity()->setLockWindows(true);
		// $objPHPExcel->getSecurity()->setLockStructure(true);
		// $objPHPExcel->getActiveSheet()->getProtection()->setObjects(true);
		// $objPHPExcel->getSecurity()->setWorkbookPassword($excel_password);
	}
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
	$objWriter->setPreCalculateFormulas(false);
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment; filename=\"$file_name\"");
	header("Cache-Control: max-age=0");
	ob_clean();
	$objWriter->save('php://output');


	$sheet = $objPHPExcel->getActiveSheet();
	PHPExcel_Calculation::getInstance()->writeDebugLog = true;

} else {
	echo '<script> alert("File does not exist!");</script>';
	$url = htmlspecialchars($_SERVER['HTTP_REFERER']);
	echo '<script>window.location.href="'.$url.'";</script>';
}
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