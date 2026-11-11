<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	if(is_ajax()) {
		if(isset($_POST["action"]) && !empty($_POST["action"])) {
			$action = $_POST["action"];
			switch($action) {
				/* do not edit below */
				case "encode" 										: encode(); break; 
				case "decode" 										: decode(); break; 
				
				case "upload_new_report" 							: upload_new_report(); break; 
				case "load_approver_list" 							: load_approver_list(); break; 
				case "approvers_decision" 							: approvers_decision(); break; 
				case "return_request_info" 							: return_request_info(); break; 
				case "return_request_details" 						: return_request_details(); break; 
				case "update_report" 								: update_report(); break; 
				case "cancel_request" 								: cancel_request(); break; 
			}
		}
	}

	function is_ajax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	/* convert an array to encode base64 and serialize */
	function encode(){
		$str = base64_encode(serialize($_POST));
		echo json_encode($str);
	}

	/* revert to normal array, decode str to base64 and unserialize */
	function decode(){
		$array = unserialize(base64_decode($_POST));
		echo json_encode($array);
	}

	function encodeURIComponent($str) {
		$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
		return strtr(rawurlencode($str), $revert);
	}
    
	function load_employee_names(){
		require_once('../class/oop.php'); 
		$return = $_POST;
		$result = "";
		$array_fields = array('*');
		$table 	   	= 'tbl_records';
		$joins 	   	= '';
		$sql_where 	= '';
		$sql_order 	= 'ORDER BY pkid';
		$sql_limit 	= '';
		$result = TEST::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctr = 0;
		while($row = mysqli_fetch_array($result)){
			$return['pkid'][$ctr] 		= $row['pkid'];
			$return['name'][$ctr] 		= $row['name'];
			$return['lastname'][$ctr] 	= $row['lastname'];
			$return['address'][$ctr] 	= $row['address'];
			$return['remarks'][$ctr] 	= $row['remarks'];
			$ctr++;
		}
		$return['ctr']	  = $ctr;
		$return['result'] = json_encode($result);
		echo json_encode($return);
	}
	
	function upload_new_report() {
		require_once('../class/oop.php'); 
		$date_time_today = date('Y-m-d H:i:s');
		$username		 = $_POST['username'];
		$report_title	 = $_POST['txt_report_title'];
		$creator_remarks = $_POST['creator_remarks'];
		$approver_name	 = json_decode($_POST['approver_name']);
		$approval_hierarchy	= json_decode($_POST['approval_hierarchy']);
		$approver_sheet		= json_decode($_POST['approver_sheet']);
		$approver_ordinate	= json_decode($_POST['approver_ordinate']);
		$target_dir  = '../uploaded_report/'.$username;
		$temp_file 	 = $_FILES["txt_file_upload"]["tmp_name"];
		$file 		 = basename(preg_replace('/\s+/','_', $_FILES["txt_file_upload"]["name"]));
		$msg = '';
		
		if (!is_dir($target_dir)) {
			// mkdir($target_dir, 0777);
			// mkdir($target_dir, 0775);
			// mkdir($target_dir, 1700);
			mkdir($target_dir);
			chmod($target_dir, 0777);
			$target_file = $target_dir . '/' . $file;
			// $msg = "New folder has been created.";
		} else {
			$target_file = $target_dir . '/' . $file;
			// chmod($target_dir, 0777);
			// chmod($target_dir, 1700);
			// $msg = "Old folder has been used.";
		}
		if(check_if_report_exists($report_title) == 'NO') {
			if (file_exists($target_file)) {
				$msg .= "Sorry, your file already exists.";
			} else {
				if (move_uploaded_file($temp_file, $target_file)) {
					/* Save the record and return the pkid */
					$table_main 		= "tbl_report";
					$array_fields_main 	= array('date_time_created', 'created_by', 'report_title', 'file_path', 'file_name','creator_remarks','approval_order','status','lastupdate','username');
					$array_values_main 	= array($date_time_today,$username,$report_title,$target_dir.'/',$file,$creator_remarks,1,'FOR APPROVAL',$date_time_today,$username);
					$fkreport			= REPORT::getInstance()->insert_query_id($table_main,$array_fields_main,$array_values_main);
						
					$table_signa 			= "tbl_report_signatories";
					for($i=0;$i<count($approver_name);$i++) {
						$app_name 	= $approver_name[$i];
						$app_order 	= $approval_hierarchy[$i];
						$app_sheet 	= $approver_sheet[$i];
						$app_ordi 	= strtoupper($approver_ordinate[$i]);
						
						if($app_order == 1) {
							$app_status = 'PENDING';
						} else {
							$app_status = '';
						}
						
						$array_fields_signa 	= array('date_time_created', 'created_by', 'fkreport', 'approver_username', 'approver_hierarchy', 'approver_status', 'esig_sheet_no', 'esig_cell_ordinate','lastupdate','username');
						$array_values_signa 	= array($date_time_today,$username,$fkreport,$app_name,$app_order,$app_status,$app_sheet,strtoupper($app_ordi),$date_time_today,$username);
						$result 				= REPORT::getInstance()->insert_query($table_signa,$array_fields_signa,$array_values_signa);
					}
				
					$ext = pathinfo($target_file, PATHINFO_EXTENSION);
					$new_file_name  = $fkreport.".".$ext;
					if(rename ($target_file, $target_dir.'/'.$new_file_name)){					
						$msg .= "Record was successfully saved. <br>File ". basename( $file ). " has been uploaded.";
					} else {
						$msg .= "Sorry, there was an error uploading and renaming of your file.";
					}
					
					/* Send email notification to first approver */
					$msg .= '<br>'.send_email_approval($fkreport, 1);
				} else {
					$msg = "Sorry, there was an error uploading your file.";
				}
			} 
		} else {
			$msg = 'Report title already exists!';
		}
		
		$return['msg'] = $msg;
		echo json_encode($return);
	}
	function check_if_report_exists($report_title) {
		require_once('../class/oop.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$array_fields = array('report_title');
		$table 	   	= 'tbl_report';
		$joins 	   	= '';
		$sql_where 	= 'WHERE report_title="'.$report_title.'" AND logdel=0';
		$sql_order 	= '';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($result->num_rows == 0) {
			$is_exists = 'NO';
		} else {
			$is_exists = 'YES';
		}
		return $is_exists;
	}
	function load_approver_list() {
		require_once('../class/oop.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$array_fields = array('CONCAT(lastname, ", ", firstname) AS emp_name', 'username');
		$table 	   	= 'db_hris.vw_emp_hris';
		$joins 	   	= '';
		$sql_where 	= 'WHERE EmpStatus=1 AND (email_add IS NOT NULL AND email_add != "kiosk@pricon.ph")';
		$sql_order 	= 'ORDER BY lastname';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctr = 0;
		while($row = mysqli_fetch_array($result)){
			$return['username'][$ctr] 			= $row['username'];
			$return['emp_name'][$ctr] 			= $row['emp_name'];
			$ctr++;
		}
		$return['ctr']	  = $ctr;
		$return['result'] = json_encode($result);
		echo json_encode($return);
	}
	function send_email_approval($fkreport, $hierarchy) {
		require_once('../class/oop.php'); //OOP Path
		require_once('../class/send_email.php');
		$return = $_POST;
		$result = "";
		$array_fields = array('created_by','report_title', 'file_path', 'file_name','approver_username');
		$table 	   	= 'vw_report';
		$joins 	   	= '';
		$sql_where 	= 'WHERE main_pkid="'.$fkreport.'" AND approver_hierarchy="'.$hierarchy.'"';
		$sql_order 	= '';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$report_title 		= $row['report_title'];
			$file_name 			= $row['file_name'];
			$approver_username 	= $row['approver_username'];
			$created_by 		= $row['created_by'];
			$file_ext			= end(explode('.',$row['file_name']));
			$file_name  		= $fkreport.".".($file_ext);
			$file 				= $row['file_path'].'/'.$file_name;
			$new_file_name		= $row['file_name'].".".$file_ext;
		}
		$subject 	 = 'FOR APPROVAL: '.$report_title.'';
		$body 	 	 = 'Please be informed that you have report for approval.<br> <br>';
		$body 		.= 'Report details: <br>';
		$body 		.= '&emsp;Report Title: '.$report_title.' <br>';
		$body 		.= '&emsp;Created By: '.return_user_name($created_by).' <br>';
		$to 		 = return_user_email_add($approver_username);
		$from 		 = return_user_email_add($created_by);
		
		$php_mailer = new email();
		$msg = $php_mailer->send_email($to, $from, $from, $subject, $body,'','');
		return $msg;
	}
	function return_user_email_add($username) {
		require_once('../class/oop.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$array_fields = array('email_add');
		$table 	   	= 'db_acl.tbl_useraccnt';
		$joins 	   	= '';
		$sql_where 	= 'WHERE username="'.$username.'"';
		$sql_order 	= '';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)) {
			$email_add = $row['email_add'];		
		} else {
			$email_add = '';
		}
		return $email_add;
	}
	function return_user_name($username) {
		require_once('../class/oop.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$array_fields = array('CONCAT(lastname, ", ", firstname) AS emp_name');
		$table 	   	= 'db_hris.vw_emp_hris';
		$joins 	   	= '';
		$sql_where 	= 'WHERE username="'.$username.'"';
		$sql_order 	= '';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)) {
			$emp_name = $row['emp_name'];		
		} else {
			$emp_name = '';
		}
		return $emp_name;
	}
	function approvers_decision() {
		require_once('../class/oop.php'); 
		$date_time_today 	= date('Y-m-d H:i:s');
		$status 			= $_POST['status'];
		$remarks 			= $_POST['reason'];
		$fkdetails 			= $_POST['fkdetails'];
		$username 			= $_POST['username'];
		
		$result 		= "";
		$table 			= "tbl_report_signatories";
		$array_fields 	= array('approver_status','date_time_approved','approver_remarks','username','lastupdate'); 
		$array_values 	= array($status,$date_time_today,$remarks,$username,$date_time_today); 
		$msg 			= REPORT::getInstance()->update_query($table,$array_fields,$array_values,$fkdetails);
		/* If report was approved, update the order to next approver */
		if($status == 'APPROVED') {
			$array_fields = array('approver_hierarchy','fkreport');
			$table 	   	= 'tbl_report_signatories';
			$joins 	   	= '';
			$sql_where 	= 'WHERE pkid="'.$fkdetails.'"';
			$sql_order 	= '';
			$sql_limit 	= '';
			$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			if($row = mysqli_fetch_array($result)) {
				$current_approver 	= $row['approver_hierarchy'];
				$next_approver 		= $current_approver + 1;
				$fkreport 			= $row['fkreport'];
			}
			
			$array_fields = array('approver_hierarchy');
			$table 	   	= 'tbl_report_signatories';
			$joins 	   	= '';
			$sql_where 	= 'WHERE fkreport="'.$fkreport.'" AND approver_hierarchy="'.$next_approver.'"';
			$sql_order 	= '';
			$sql_limit 	= '';
			$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			if($result->num_rows == 0) {
				$table 			= "tbl_report";
				$array_fields 	= array('approval_order', 'status', 'lastupdate','username'); 
				$array_values 	= array($current_approver,$status,$date_time_today,$username); 
				$msg 			= REPORT::getInstance()->update_query($table,$array_fields,$array_values,$fkreport);
			} else {
				$table 			= "tbl_report";
				$array_fields 	= array('approval_order', 'lastupdate','username'); 
				$array_values 	= array($next_approver,$date_time_today,$username); 
				$msg 			= REPORT::getInstance()->update_query($table,$array_fields,$array_values,$fkreport);
				$table 			= "tbl_report_signatories";
				$array_fields 	= array('approver_status', 'lastupdate','username'); 
				$array_values 	= array('PENDING',$date_time_today,$username); 
				$sql_where		= 'WHERE fkreport='.$fkreport.' AND approver_hierarchy='.$next_approver; 
				$msg 			= REPORT::getInstance()->update_query_detailed($table,$array_fields,$array_values,$sql_where);
			}			
		} else if($status == 'DISAPPROVED') {
			$array_fields = array('fkreport');
			$table 	   	= 'tbl_report_signatories';
			$joins 	   	= '';
			$sql_where 	= 'WHERE pkid="'.$fkdetails.'"';
			$sql_order 	= '';
			$sql_limit 	= '';
			$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			if($row = mysqli_fetch_array($result)) {
				$fkreport 			= $row['fkreport'];
			}
			$table 			= "tbl_report";
			$array_fields 	= array('status', 'lastupdate','username'); 
			$array_values 	= array($status,$date_time_today,$username); 
			$msg 			= REPORT::getInstance()->update_query($table,$array_fields,$array_values,$fkreport);
		}
		$msg = send_email_decision($fkdetails);
		$return['msg']  = $msg;
		$return['result'] = json_encode($result);
		echo json_encode($return);
	}
	function send_email_decision($fkdetails) {
		require_once('../class/oop.php'); //OOP Path
		require_once('../class/send_email.php');
		$return = $_POST;
		$result = "";
		$array_fields = array('main_pkid','created_by','report_title', 'approver_username','approver_status','date_time_approved','approver_remarks');
		$table 	   	= 'vw_report';
		$joins 	   	= '';
		$sql_where 	= 'WHERE details_pkid="'.$fkdetails.'"';
		$sql_order 	= '';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$fkreport 			= $row['main_pkid'];
			$report_title 		= $row['report_title'];
			$approver_username 	= $row['approver_username'];
			$created_by 		= $row['created_by'];
			$approver_status 	= $row['approver_status'];
			$approver_remarks 	= $row['approver_remarks'];
			$date_time_approved = date('M d, Y h:i:s',strtotime($row['date_time_approved']));
			
		}
		$subject 	 = strtoupper($approver_status).' REPORT: '.$report_title.'';
		$body 	 	 = 'Please be informed that your report has been '.strtolower($approver_status).' by '.return_user_name($approver_username).'.<br> <br>';
		$body 		.= 'Report details: <br>';
		$body 		.= '&emsp;Report Title: '.$report_title.' <br>';
		$body 		.= '&emsp;Created By: '.return_user_name($created_by).' <br>';
		$body 		.= '&emsp;Date/Time '.$approver_status.': '.$date_time_approved.' <br>';
		$body 		.= $approver_remarks == '' ? '' : '&emsp;Approver\'s Remarks: '.$approver_remarks.' <br>';
		$to 		 = return_user_email_add($created_by);
		$from 		 = return_user_email_add($approver_username);
		
		$php_mailer = new email();
		$msg = $php_mailer->send_email($to, $from, $from, $subject, $body,'','');
		
		if($approver_status == 'APPROVED') {
			/* Get the next approver */
			$table 	   	= 'tbl_report_signatories';
			$array_fields = array('approver_hierarchy');
			$joins 	   	= '';
			$sql_where 	= 'WHERE fkreport='.$fkreport.' AND (approver_status = "" OR approver_status="PENDING") AND logdel=0';
			$sql_order 	= 'ORDER BY approver_hierarchy';
			$sql_limit 	= 'LIMIT 0,1';
			$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			$script = REPORT::getInstance()->select_query_script($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			if($result->num_rows != 0) {
				if($row = mysqli_fetch_array($result)){
					$approver_hierarchy = $row['approver_hierarchy'];
				}
				/* Send email notification to next approver */
				send_email_approval($fkreport, $approver_hierarchy);
			}
		}
		
		return $msg;
	}
	function return_request_info() {
		require_once('../class/oop.php'); //OOP Path
		$result = "";
		$array_fields = array('created_by','report_title', 'file_path', 'file_name', 'creator_remarks','status');
		$table 	   	= 'tbl_report';
		$joins 	   	= '';
		$sql_where 	= 'WHERE pkid='.$_POST['pkid'];
		$sql_order 	= '';
		$sql_limit 	= 'LIMIT 0,1';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$return['created_by']		= $row['created_by'];
			$return['report_title']		= $row['report_title'];
			$return['file_path'] 		= $row['file_path'];
			$return['file_name'] 		= $row['file_name'];
			$return['remarks'] 			= $row['creator_remarks'];
			$return['status'] 			= $row['status'];
		}
		$return['result'] = json_encode($result);
		echo json_encode($return);
	}
	function return_request_details() {
		require_once('../class/oop.php'); //OOP Path
		$result = "";
		$array_fields = array('created_by','approver_username', 'approver_hierarchy', 'approver_status', 'date_time_approved', 'esig_cell_ordinate', 'esig_sheet_no', 'approver_remarks');
		$table 	   	= 'tbl_report_signatories';
		$joins 	   	= '';
		$sql_where 	= 'WHERE fkreport='.$_POST['pkid'].' AND logdel=0';
		$sql_order 	= 'ORDER BY approver_hierarchy';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$ctr = 0;
		while($row = mysqli_fetch_array($result)){
			$return['created_by'][$ctr]			= $row['created_by'];
			$return['approver_username'][$ctr]	= $row['approver_username'];
			$return['approver_hierarchy'][$ctr] = $row['approver_hierarchy'];
			$return['approver_status'][$ctr] 	= $row['approver_status'];
			$return['date_time_approved'][$ctr] = $row['date_time_approved'];
			$return['esig_cell_ordinate'][$ctr] = $row['esig_cell_ordinate'];
			$return['esig_sheet_no'][$ctr] 		= $row['esig_sheet_no'];
			$ctr++;
		}
		$return['ctr'] = $ctr;
		$return['result'] = json_encode($result);
		echo json_encode($return);
	}
	function update_report() {
		require_once('../class/oop.php'); 
		$date_time_today = date('Y-m-d H:i:s');		
		$fkreport		 = $_POST['fkreport'];
		$username		 = $_POST['username'];
		$report_title	 = $_POST['txt_report_title'];
		$creator_remarks = $_POST['creator_remarks'];
		$approver_name	 = json_decode($_POST['approver_name']);
		$approval_hierarchy	= json_decode($_POST['approval_hierarchy']);
		$approver_sheet		= json_decode($_POST['approver_sheet']);
		$approver_ordinate	= json_decode($_POST['approver_ordinate']);
		
		$msg = '';
		
		/* Reupload the file. Delete the current file and replace by new one */
		if(isset($_FILES["txt_file_upload"]["tmp_name"])) {
			$temp_file 	 		= $_FILES["txt_file_upload"]["tmp_name"];
			$file 		 		= basename(preg_replace('/\s+/','_', $_FILES["txt_file_upload"]["name"]));
			/* Get the file path */
			$table 		= "tbl_report";		
			$array_fields = array('file_path','file_name');
			$joins 	   	= '';
			$sql_where 	= 'WHERE pkid='.$fkreport.' AND logdel=0';
			$sql_order 	= '';
			$sql_limit 	= 'LIMIT 0,1';
			$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
			if($row = mysqli_fetch_array($result)){
				$target_file = $row['file_path'].$fkreport.'.'.end(explode('.',$row['file_name']));
				unlink($target_file);
				$target_file = $row['file_path'].$fkreport.'.'.end(explode('.',$file));
				if (move_uploaded_file($temp_file, $target_file)) {
					$msg .= "<br>File was successfully replaced.";
				} else {
					$msg .= "<br>Sorry, there was an error uploading your file.";
				}
			}
			
			/* Delete the old record  */
			$table 			= "tbl_report_signatories";		
			$where			= 'WHERE fkreport='.$fkreport;
			$delete_query	= REPORT::getInstance()->delete_query_details($table,$where);
			
			/* Re-insert new record */
			for($i=0;$i<count($approver_name);$i++) {
				$app_name 	= $approver_name[$i];
				$app_order 	= $approval_hierarchy[$i];
				$app_sheet 	= $approver_sheet[$i];
				$app_ordi 	= strtoupper($approver_ordinate[$i]);
				
				if($app_order == 1) {
					$app_status = 'PENDING';
				} else {
					$app_status = '';
				}
				
				$array_fields_signa 	= array('date_time_created', 'created_by', 'fkreport', 'approver_username', 'approver_hierarchy', 'approver_status', 'esig_sheet_no', 'esig_cell_ordinate','lastupdate');
				$array_values_signa 	= array($date_time_today,$username,$fkreport,$app_name,$app_order,$app_status,$app_sheet,strtoupper($app_ordi),$date_time_today);
				$result 				= REPORT::getInstance()->insert_query($table,$array_fields_signa,$array_values_signa);
				// $msg 				.= REPORT::getInstance()->insert_query_script($table,$array_fields_signa,$array_values_signa);
			}
			
			if(count($approver_name) != 0) {	
				/* Send email notification to first approver */
				$msg .= '<br>'.send_email_approval($fkreport, 1);
			}
			
			/* Update approver hierarchy and file name */
			$table 			= "tbl_report";		
			$array_fields 	= array('approval_order','file_name','username', 'lastupdate');
			$array_values 	= array(1, $file, $username,$date_time_today);
			$where			= 'WHERE pkid='.$fkreport;
			$result 		= REPORT::getInstance()->update_query_detailed($table,$array_fields,$array_values,$where);			
		} else {
			/* Update the status of report to 1  */
			$table 			= "tbl_report_signatories";		
			$array_fields 	= array('username', 'lastupdate','logdel');
			$array_values 	= array($username,$date_time_today,1);
			$where			= 'WHERE fkreport='.$fkreport.' AND (approver_status = "" OR approver_status = "PENDING")';
			$msg 			= REPORT::getInstance()->update_query_detailed($table,$array_fields,$array_values,$where);
			
			/* Re-insert new record */
			for($i=0;$i<count($approver_name);$i++) {
				$app_name 	= $approver_name[$i];
				$app_order 	= $approval_hierarchy[$i];
				$app_sheet 	= $approver_sheet[$i];
				$app_ordi 	= strtoupper($approver_ordinate[$i]);
				
				if($app_order == 1) {
					$app_status = 'PENDING';
				} else {
					$app_status = '';
				}
				
				$array_fields_signa 	= array('date_time_created', 'created_by', 'fkreport', 'approver_username', 'approver_hierarchy', 'approver_status', 'esig_sheet_no', 'esig_cell_ordinate','lastupdate');
				$array_values_signa 	= array($date_time_today,$username,$fkreport,$app_name,$app_order,$app_status,$app_sheet,strtoupper($app_ordi),$date_time_today);
				$result 				= REPORT::getInstance()->insert_query($table,$array_fields_signa,$array_values_signa);
			}
			
			if(count($approver_name) != 0) {	
				/* Get the next approver */
				$array_fields = array('approver_hierarchy');
				$joins 	   	= '';
				$sql_where 	= 'WHERE fkreport='.$fkreport.' AND (approver_status = "" OR approver_status="PENDING") AND logdel=0';
				$sql_order 	= 'ORDER BY approver_hierarchy';
				$sql_limit 	= 'LIMIT 0,1';
				$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
				if($row = mysqli_fetch_array($result)){
					$approver_hierarchy = $row['approver_hierarchy'];
				}
				/* Send email notification to first approver */
				$msg .= '<br>'.send_email_approval($fkreport, $approver_hierarchy);
			}
		}	
		/* Update the main table */
		$table		= 'tbl_report_signatories';
		$array_fields = array('approver_status');
		$joins 	   	= '';
		$sql_where 	= 'WHERE fkreport='.$fkreport.' AND logdel=0';
		$sql_order 	= 'ORDER BY lastupdate DESC';
		$sql_limit 	= 'LIMIT 0,1';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		if($row = mysqli_fetch_array($result)){
			$approver_status = $row['approver_status'] == '' ? 'FOR APPROVAL' : ($row['approver_status'] == 'PENDING' ? 'FOR APPROVAL' : $row['approver_status']);
		}
		$table 			= "tbl_report";		
		$array_fields 	= array('report_title','creator_remarks','status','username', 'lastupdate');
		$array_values 	= array($report_title,$creator_remarks, $approver_status,$username,$date_time_today);
		$where			= 'WHERE pkid='.$fkreport;
		$result 		= REPORT::getInstance()->update_query_detailed($table,$array_fields,$array_values,$where);
		// $msg 		.= REPORT::getInstance()->update_query_detailed($table,$array_fields,$array_values,$where);
		
		$return['msg'] = $msg;
		$return['approver_name'] = $approver_name;
		$return["result"] = json_encode($result); 
		echo json_encode($return);
	}
	function cancel_request() {
		require_once('../class/oop.php'); 
		$date_time_today = date('Y-m-d H:i:s');		
		$pkid		 	= $_POST['pkid'];
		$username		 = $_POST['username'];
		$remarks		 = $_POST['remarks'];
		$result 		 = "";
		$table 			 = "tbl_report";
		$array_fields 	 = array('status','cancel_remarks','username','lastupdate'); 
		$array_values 	 = array('CANCELLED',$remarks,$username,$date_time_today); 
		$result 		 = REPORT::getInstance()->update_query($table,$array_fields,$array_values,$pkid);
		
		/* send email notification*/
		send_email_cancelled($pkid);
		
		$return['msg']   = $result;
		$return["result"] = json_encode($result); 
		echo json_encode($return);
	}
	function send_email_cancelled($fkreport) {
		require_once('../class/oop.php'); //OOP Path
		require_once('../class/send_email.php');
		$return = $_POST;
		$result = "";
		$array_fields = array('created_by','report_title', 'approver_username','approval_status','date_time_approved','cancel_remarks');
		$table 	   	= 'vw_report';
		$joins 	   	= '';
		$sql_where 	= 'WHERE main_pkid="'.$fkreport.'"';
		$sql_order 	= '';
		$sql_limit 	= '';
		$to_recipients	= array();
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		while($row = mysqli_fetch_array($result)){
			$report_title 		= $row['report_title'];
			$approver_username 	= $row['approver_username'];
			$created_by 		= $row['created_by'];
			$approval_status 	= $row['approval_status'];
			$cancel_remarks 	= $row['cancel_remarks'];
			$date_time_approved = date('M d, Y h:i:s',strtotime($row['date_time_approved']));
			if($approver_username == 'APPROVED') {
				$to_recipients[]    = return_user_email_add($approver_username);
			}
		}
		$subject 	 = strtoupper($approval_status).' REPORT: '.$report_title.'';
		$body 	 	 = 'Please be informed that your report has been '.strtolower($approval_status).' by '.return_user_name($created_by).'.<br> <br>';
		$body 		.= 'Report details: <br>';
		$body 		.= '&emsp;Report Title: '.$report_title.' <br>';
		$body 		.= '&emsp;Created By: '.return_user_name($created_by).' <br>';
		$body 		.= '&emsp;Date/Time '.$approval_status.': '.$date_time_approved.' <br>';
		$body 		.= '&emsp;Reason of Cancellation: '.$cancel_remarks.' <br>';
		$to			 = implode(',',$to_recipients);
		$from 		 = return_user_email_add($created_by);
		$cc 		 = return_user_email_add($created_by);
		
		$php_mailer = new email();
		$msg = $php_mailer->send_email($to, $from, $cc, $subject, $body,'','');
		
		return $msg;
	}
	?>