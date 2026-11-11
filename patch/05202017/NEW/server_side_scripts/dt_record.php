<?php
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);

	$aColumns = array(  'main_pkid',
						'date_time_created',
						'created_by',
						'report_title',
						'file_path',
						'file_name',
						'approval_order',
						'approval_status',
						'lastupdate',
						'approver_username'
						);
	
	/* used this field for searching data typed in the search box */
	// $array_search = array('empno', 'LastName',
							// 'date', 'date_out');
	// $array_search = array('empno', 'LastName');
							
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "main_pkid";
	
	/* DB table to use */
	$sTable = "vw_report";
	
	require_once('../db_config/config.php');
	/* Database connection information */
	$gaSql['user']       = $username;
	$gaSql['password']   = $password;
	$gaSql['db']         = $db_name;
	$gaSql['server']     = $server;
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
		
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	 
	 /* wag gagalawin yung sWhere default to para sa searchbox ng datatables check mo sa baba kung pano gagawin mo :) */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			// if( in_array($aColumns[$i],$array_search) ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			// }
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	/* 
		dito ka mag add ng where mo, una check mo kung may laman na yung $sWhere pag wala append mo yung where mo na meron
		kasama where kapag naman may laman na AND na syempre diba :)
	*/
	
	$un = $_GET['un'];
	
	if($sWhere == ""){
		$sWhere .= "WHERE  ";
	}else{
		$sWhere .= "AND ";
	}
	
	$sWhere = "WHERE (created_by = '".$un."' OR approver_username= '".$un."')";
	$sOrder = "ORDER BY lastupdate DESC";
	
	// $sWhere = '';
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	// echo "limit: ".$sLimit." : ".$sQuery."<hr>";
	$query_used = $sQuery;
	// echo $query_used;
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	$pkids = array();
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$buttons = '';		
		$row = array();
		unset($row);
		if(!in_array($aRow['main_pkid'],$pkids)) {
			$pkids[] = $aRow['main_pkid'];
			$approver = is_approver($aRow['main_pkid'], $un, $aRow['approval_order']);
			if($approver['is_visible'] == 'YES') {
				if($un == $aRow['created_by']) {
					$buttons .= '<button type="button" value="'.$aRow['main_pkid'].'" class="btn btn-primary fa fa-edit" style="width:100px;margin:2%;" id="btn_edit"> Edit / View</button>';
					if($aRow['approval_status'] != 'CANCELLED') {
						$buttons .= '<button type="button" value="'.$aRow['main_pkid'].'" class="btn btn-danger fa fa-ban" style="width:100px;margin:2%;" id="btn_cancel"> Cancel</button>';
					}
				}
				if($aRow['approval_status'] != 'CANCELLED') {
					if($approver['status'] == 'FOR APPROVAL' || $approver['status'] == 'PENDING' || $approver['status'] == '') {
						$buttons .= '<button type="button" value="'.$approver['pkid'].'" class="btn btn-success fa fa-thumbs-o-up" style="width:100px;margin:2%;" id="btn_approve" > Approve'.'</button><br>';
						$buttons .= '<button type="button" value="'.$approver['pkid'].'" class="btn btn-danger fa fa-thumbs-o-down" style="width:100px;margin:2%;" id="btn_disapprove" > Disapprove'.'</button><br>';
					} else if($approver['status'] == 'CANCELLED' || $approver['status'] == 'APPROVED' || $approver['status'] == 'DISAPPROVED') {
						$buttons .= '<button type="button" value="'.$aRow['main_pkid'].'" class="btn btn-default fa fa-eye" style="width:100px;margin:2%;" id="btn_edit"> View</button>';
					}
				} else {
					if($un != $aRow['created_by']) {
						$buttons .= '<button type="button" value="'.$aRow['main_pkid'].'" class="btn btn-default fa fa-eye" style="width:100px;margin:2%;" id="btn_edit"> View</button>';
					}
				}
				$ext = explode('.',$aRow['file_name']);
				$file = $aRow['file_path'].'/'.$aRow['main_pkid'].'.'.$ext[1];		
				
				$row[] = $aRow['approval_status'];
				$row[] = date('M d, Y h:i:s A', strtotime($aRow['date_time_created']));
				$row[] = return_user_name($aRow['created_by']);
				$row[] = $aRow['report_title'];
				$row[] = '<button type="button" class="btn btn-link" value="'.$aRow['main_pkid'].'" id="btn_file_link">'.$aRow['file_name'].'</button>';
				$row[] = report_approvers($aRow['main_pkid']);
				$row[] = $aRow['lastupdate'] != '' ? date('M d, Y h:i:s A', strtotime($aRow['lastupdate'])) : '';
				$row[] = '<center> '.$buttons.' </center>';
				array_push($output['aaData'],$row);
			}
		} 		
	}
	
	echo json_encode( $output );
	
	function is_approver($fkreport, $username,$next_approver) {
		require_once('../class/oop.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$array_fields = array('created_by','approver_status','pkid','approver_hierarchy');
		$table 	   	= 'tbl_report_signatories';
		$joins 	   	= '';
		$sql_where 	= 'WHERE fkreport="'.$fkreport.'" AND approver_username="'.$username.'" AND logdel=0';
		$sql_order 	= '';
		$sql_limit 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$script = REPORT::getInstance()->select_query_script($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		$approver =  array();
		if($result->num_rows == 0) {
			$approver['status'] 	= 'N/A';
			$approver['pkid'] 		= $fkreport;
			$approver['is_visible'] = 'YES';	
		} else {
			$row = mysqli_fetch_array($result);
			if($next_approver == $row['approver_hierarchy'] || $username == $row['created_by'] || $username == $row['created_by']) {
				$approver['status'] = $row['approver_status'];
				$approver['pkid'] = $row['pkid'];
			} else {
				$approver['status'] = $row['approver_status'];
				$approver['pkid'] = 'N/A';		
			}
			if($username == $row['created_by']) {
				$approver['is_visible'] = 'YES';	
			} else {
				if($row['approver_status'] == '' && $next_approver != $row['approver_hierarchy']) {
					$approver['is_visible'] = 'NO';		
				} else {
					$approver['is_visible'] = 'YES';	
				}
			}
		}
		return $approver;
	}
	function report_approvers($fkreport) {
		require_once('../class/oop.php'); //OOP Path
		$return = $_POST;
		$result = "";
		$array_fields = array('approver_username', 'approver_status', 'date_time_approved');
		$table 	   	= 'vw_report';
		$joins 	   	= '';
		$sql_where 	= 'WHERE main_pkid="'.$fkreport.'"';
		$sql_order 	= 'ORDER BY `approver_hierarchy`';
		$sql_limit 	= '';
		$report_approvers 	= '';
		$result = REPORT::getInstance()->select_query($array_fields,$table,$joins,$sql_where,$sql_order,$sql_limit);
		while($row=mysqli_fetch_array($result)) {
			$report_approvers .= return_user_name($row['approver_username']). ' [ <i>'.($row['date_time_approved'] == '' ? '-' : (date('M d, Y h:i:s',strtotime($row['date_time_approved'])))).'</i> ]'.($row['approver_status'] == '' ? '' : (' - <b>'.$row['approver_status'].'</b>')).' <br>';
		} 
		return $report_approvers;
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
	

?>