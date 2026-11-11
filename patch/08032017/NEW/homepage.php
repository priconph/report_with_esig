<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
?>

<div class="container-fluid">
	<div class="panel panel-primary">
		<div class="panel-heading">Summary of Report for Approval</div>
		<div class="panel-body">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			  <div class="panel panel-default box box-info">
				<div class="panel-heading" role="tab" id="headingOne">
				  <h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="color:blue;">
					  <i class="fa fa-lightbulb-o"></i> Additional Information
					</a>
				  </h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
				  <div class="panel-body">
					<h5><p> 
						<i class="fa fa-info-circle" style="color:blue;"></i> 
						* System cannot affix Date / Time of approval and remarks to Excel 2003 below.<br>
						* For non-critical documents (like EMM), ISS will manually purge the reports in the database and will just maintain the last day of the month update. <br>
					</p></h5>
				  </div>
				</div>	
			  </div>	
			</div>	
			<div id="row">
				<div class="col-sm-4">
					<!-- <button class="btn btn-info btn-md fa fa-search" id="btn_advance_search_main"> Advance Search</button> -->
				</div>
				<div class="col-sm-8">
					<p style="text-align:right;padding-bottom:20px;">
						<button class="btn btn-primary btn-md fa fa-upload" id="btn_upload_report"> Upload New Report</button>
 						<!--<button class="btn btn-success btn-md fa fa-file-excel-o" id="btn_report_main"> Report</button> -->
					</p>
				</div>
				<div style="width:100%;overflow-y: scroll;">
					<table id="tbl_record" class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th style="width:5%"><center>Status</center></th>
								<th style="width:8%"><center>Date / Time Created</center></th>
								<th style="width:10%"><center>Created By</center></th>
								<th style="width:20%"><center>Report Title</center></th>
								<th style="width:10%"><center>File</center></th>
								<th style="width:20%"><center>Report Approver/s</center></th>
								<th style="width:8%"><center>Last Update</center></th>
								<th style="width:10%"><center>Action</center></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
	</div> 
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="mdl_upload_report">
  <div class="modal-dialog" role="document" style="width:70%;height:auto;max-height:100%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-upload"></span> Upload Report</h4>
      </div>
	  <form id="frm_upload_report" method="post" enctype="multipart/form-data">
      <div class="modal-body container-fluid">
        <div class="row">
			<div class="col-md-12 form-inline">
				<div class="alert alert-danger" role="alert" style="display:none;" id="div_message_submit">			
				</div>
			</div>
			<div class="col-md-12 form-inline">
				<label class="control-label" style="width:25%;">Report Title: </label>
				<input type="text" style="width:73%;margin:2px;" class="form-control" id="txt_report_title" name="txt_report_title" required />
			</div>
			<div class="col-md-12 form-inline">
				<label class="control-label" style="width:25%;">Upload report: </label>
				<input type="file" style="width:73%;margin:2px;" class="form-control" id="txt_file_upload" name="txt_file_upload" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required />
			</div>
			<div class="col-md-12 form-inline">
				<label class="control-label" style="width:25%;">Remarks: </label>
				<textarea style="width:73%;margin:2px;" class="form-control" id="creator_remarks" name="creator_remarks"></textarea>
			</div>
			<div class="col-md-6 col-md-offset-6 form-inline">
				<button type="button" class="btn btn-primary fa fa-plus pull-right" id="btn_add_approver" style="margin:3px;"> Add Approver</button>
			</div>
			<div class="col-md-12 form-inline">
				<div class="alert alert-danger" role="alert" style="display:none;" id="div_message_approver">			
				</div>
				<table class="table table-bordered" id="tbl_approver">
					<thead>
						<tr>
							<th style="width:15%">Order</th>
							<th style="width:45%">Approver</th>
							<th style="width:15%">Sheet #</th>
							<th style="width:15%">Ordinate (COLROW)</th>
							<th style="width:10%"> <i class="fa fa-cogs"></i></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary fa fa-upload" id="btn_submit"> Submit</button>
        <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
      </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="mdl_upload_report_view">
  <div class="modal-dialog" role="document" style="width:70%;height:auto;max-height:100%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-upload"></span> Update / View Report</h4>
      </div>
	  <form id="frm_upload_report_view" method="post" enctype="multipart/form-data">
      <div class="modal-body container-fluid">
        <div class="row">
			<div class="col-md-12 form-inline">
				<div class="alert alert-danger" role="alert" style="display:none;" id="div_message_submit_view">			
				</div>
			</div>
			<div class="col-md-12 form-inline">
				<label class="control-label" style="width:25%;">Report Title: </label>
				<input type="text" style="width:73%;margin:2px;" class="form-control" id="txt_report_title_view" name="txt_report_title" required>
			</div>
			<div class="col-md-12 form-inline">
				<label class="control-label" style="width:25%;">Current report: </label>
				<button type="button" class="btn btn-link" id="btn_file_download_view"></button>
			</div>
			<div class="col-md-12 form-inline checkbox" id="container_reupload">
				<label class="control-label" style="width:25%;"><input type="checkbox" id="chk_reupload"> Re-upload report: </label>
				<input type="file" style="width:73%;margin:2px;" class="form-control" title="Check the checkbox on the left side to re-upload file" id="txt_file_upload_view" name="txt_file_upload" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" disabled>
			</div>
			<div class="col-md-12 form-inline">
				<label class="control-label" style="width:25%;">Remarks: </label>
				<textarea style="width:73%;margin:2px;" class="form-control" id="creator_remarks_view" name="creator_remarks"></textarea>
			</div>
			<div class="col-md-6 col-md-offset-6 form-inline">
				<button type="button" class="btn btn-primary fa fa-plus pull-right" id="btn_add_approver_view" style="margin:3px;"> Add Approver</button>
			</div>
			<div class="col-md-12 form-inline">
				<div class="alert alert-danger" role="alert" style="display:none;" id="div_message_approver_view">			
				</div>
				<table class="table table-bordered" id="tbl_approver_view">
					<thead>
						<tr>
							<th style="width:10%">Status</th>
							<th style="width:10%">Order</th>
							<th style="width:45%">Approver</th>
							<th style="width:15%">Sheet #</th>
							<th style="width:10%">Ordinate (COLROW)</th>
							<th style="width:10%"> <i class="fa fa-cogs"></i></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary fa fa-upload" id="btn_submit_view"> Submit</button>
        <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal"> Close</button>
      </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modal_decision">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-question-circle-o"></span> System Message</h4>
      </div>
	  <form id="frm_decision">
      <div class="modal-body">
		<div class="alert alert-success" role="alert" style="display:none;" id="div_msg_decision"></div>				
		<input type="hidden" id="hdn_pkid" />
		<div class="alert alert-danger" role="alert" id="mdl_div_msg"> 	
			<div class="row">
				<div class="col-sm-1">
					<i class="fa fa-warning fa-2x"></i>
				</div>
				<div class="col-sm-11" id="" style="font-size:14px;">
					<h3 id="mdl_text_msg">Are you sure you want to approve the report? </h3>
				</div>
			</div>
			<div class="row" style="font-size:14px;">
				<div class="col-sm-12">
					<br>Remarks: <br>
					<textarea id="reason" style="width:100%;height:10%;color:black;" name="reason" ></textarea>
				</div>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary fa fa-check" id="btn_yes"> Yes</button>
        <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal" id="btn_no_reset"> No</button>
      </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="mdl_msg">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-question-circle-o"></span> System Message</h4>
      </div>
      <div class="modal-body">
		<div class="alert alert-success" role="alert" id="mdl_msg_content"> 	
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default fa fa-close" data-dismiss="modal" id=""> Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="mdl_cancel_request">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="fa fa-cancel"></span> Cancel Request</h4>
      </div>	
	  <form id="frm_cancel">
		  <div class="modal-body">	
			<div class="alert alert-danger" role="alert"> 	
				<div class="row">
					<div class="col-sm-1">
						<i class="fa fa-warning fa-2x"></i>
					</div>
					<div class="col-sm-11" id="" style="font-size:14px;">
						<h3>Are you sure you want to cancel the report request? </h3>
					</div>
				</div>
				<div class="row" style="font-size:14px;">
					<div class="col-sm-12">
						<br>Reason: <br>
						<textarea id="reason" style="width:100%;height:10%;color:black;" name="remarks" required></textarea>
					</div>
				</div>
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-primary fa fa-check" id="btn_yes_cancel"> Yes</button>
			<button type="button" class="btn btn-default fa fa-close" data-dismiss="modal" id="btn_no_reset"> No</button>
		  </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
var handler 	= 'handler/handler.php';
var username	= $('#hd_username').val();
var table_id 	= 'tbl_record';
var data_table 	= '';
var row_cnt 	= 1;

var approver_name 		= [];
var approval_hierarchy	= [];
var approver_ordinate 	= [];
$(document).ready(function() {
	data_table = $('#'+table_id).DataTable({
        "aaSorting": [],
        // "bProcessing": true,
        // "bServerSide": true,
        "sAjaxSource": "./server_side_scripts/dt_record.php?un="+username
    });	
});

$(window).keydown(function(event){
	if(event.keyCode == 13) {
	  event.preventDefault();
	  return false;
	}
});

$('#btn_upload_report').click(function() {
	row_cnt 	= 1;
	$('#div_message_approver').hide();
	$('#div_message_submit').hide();
	$('#btn_submit').show();
	$('#tbl_approver tbody').empty();
	$('#mdl_upload_report').modal();
});
$('#btn_add_approver').click(function(){
	var row_cnt_label = get_last_row_index(row_cnt);
	var cmb_id = 'cmb_approver_'+row_cnt_label;
	
	var html  = '<tr>';
		html += '	<td style="width:15%">'+row_cnt_label+'</td>';
		html += '	<td style="width:45%"><select class="chosen-select" data-placeholder="Select Approver Name" id="'+cmb_id+'"></select></td>';
		html += '	<td style="width:15%"><input type="number" min="1" step="1" id="txt_sheet_no" style="width:100%" class="form-control" value="1" required></td>';
		html += '	<td style="width:15%"><input type="text" style="text-transform:uppercase" id="txt_cell_ordinates" style="width:100%" class="form-control" required></td>';
		html += '	<td style="width:10%"><button type="button" class="btn btn-danger fa fa-remove" id="btn_remove"></td>';
		html += '</tr>';
	$('#tbl_approver tbody').append(html);
	
	load_approver_list(cmb_id,function() {
		$('.chosen-select#'+cmb_id).chosen({width:"100%"});
	});
});
$('#tbl_approver tbody').on('click','#btn_remove', function() {
	var row = $(this).closest('tr');
	row.remove();
	reorder_approvers('tbl_approver');
});
$('#tbl_approver tbody').on('keypress','#txt_cell_ordinates', function(e) {
    var regex = new RegExp("^[a-zA-Z0-9]+$");
	var txt_box = $('#txt_cell_ordinates').val();
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
		if(txt_box.length == 0) {
			if($.isNumeric(str.charAt(0))) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
    }
	
    e.preventDefault();
    return false;
});
$('#frm_upload_report').on('submit', function(e) {
	// console.log(approver_name);
	e.preventDefault();
	$('.btn').prop("disabled",true);
	var serialized_data = new FormData(this);
	if(row_cnt != 1) {
		read_assigned_approvers('tbl_approver',function() {				
			serialized_data.append("action","upload_new_report");
			serialized_data.append("username",username);
			serialized_data.append("approver_name",JSON.stringify(approver_name));
			serialized_data.append("approval_hierarchy",JSON.stringify(approval_hierarchy));
			serialized_data.append("approver_sheet",JSON.stringify(approver_sheet));
			serialized_data.append("approver_ordinate",JSON.stringify(approver_ordinate));
		
			call_ajax_attachment(serialized_data, handler, function(result){	
				console.log(result);
				$('#div_message_submit').html('<span class="fa fa-info-circle"> </span> '+result['msg']);
				$('#div_message_submit').show();
				if(result['msg'] == 'Report title already exists!' || result['msg'] == 'Sorry, your file already exists.') {
					$('#btn_submit').show();
					$('#div_message_submit').attr('class', 'alert alert-danger');
				} else {
					$('#btn_submit').hide();
					$('#div_message_submit').attr('class', 'alert alert-success');
				}
				data_table.ajax.reload();
				$('.btn').prop("disabled",false);
			});
		});
	} else {
		$('#div_message_submit').html('<span class="fa fa-info-circle"> </span> Please select approver/s.');
		$('#div_message_submit').show();
		$('#div_message_submit').attr('class', 'alert alert-danger');
	}
});
$('#' + table_id + ' tbody').on('click', '#btn_file_link', function() {
	// window.location.href = "./pages/dl_report.php?id="+$(this).val();
	window.location.href = "./pages/dl_report2.php?id="+$(this).val();
});
$('#' + table_id + ' tbody').on('click', '#btn_approve', function() {
	$('#mdl_div_msg').attr('class','alert alert-success');
	$('#hdn_pkid').val($(this).val());
	$('#btn_yes').val('APPROVED');
	$('#mdl_text_msg').html('Are you sure you want to approve the report?');
	$('#reason').attr('required',false);
	$('#modal_decision').modal('show');
});
$('#' + table_id + ' tbody').on('click', '#btn_disapprove', function() {
	$('#mdl_div_msg').attr('class','alert alert-danger');
	$('#hdn_pkid').val($(this).val());
	$('#btn_yes').val('DISAPPROVED');
	$('#mdl_text_msg').html('Are you sure you want to disapprove the report?');
	$('#reason').attr('required',true);
	$('#modal_decision').modal('show');
});
$('#' + table_id + ' tbody').on('click', ' tr', function() {
	$('#'+table_id+' tbody tr').attr('style','');
	$(this).attr('style','background:teal;color:white;');
});
$('#frm_decision').on('submit', function(e) {
	e.preventDefault();
	$('.btn').prop("disabled",true);
	var serialized_data = $(this).serialize();
		approvers_decision(serialized_data);
});
$('#' + table_id + ' tbody').on('click', '#btn_edit', function() {
	var fkreport = $(this).val();
	$('#btn_submit_view').val(fkreport);
	row_cnt 	= 1;
	return_request_info(fkreport, function() {
		return_request_details(fkreport, '', function() {
			$('#div_message_submit_view').hide();
			$('#txt_file_upload_view').prop('disabled',true);
			$('#chk_reupload').prop('checked', false);
			$('#mdl_upload_report_view').modal();
		});		
	});
});
$('#chk_reupload').click(function() {
	if($(this).is(":checked")) {
		$('#txt_file_upload_view').prop('disabled',false);
		$('#div_message_submit_view').attr('class', 'alert alert-danger');
		$('#div_message_submit_view').html('<span class="fa fa-warning"> </span> Report approval will reset on the re-uploading of file.');
		$('#div_message_submit_view').show();
		return_request_details($('#btn_submit_view').val(), 'REUPLOAD', function() {});
		$('#txt_file_upload_view').attr('required',true);
	} else {
		$('#txt_file_upload_view').prop('disabled',true);
		$('#div_message_submit_view').hide();
		return_request_details($('#btn_submit_view').val(), '', function() {});
		$('#txt_file_upload_view').attr('required',false);
	}
});
$('#btn_file_download_view').click(function() {
	// window.location.href = "./pages/dl_report.php?id="+$(this).val();
	window.location.href = "./pages/dl_report2.php?id="+$(this).val();
});

$('#btn_add_approver_view').click(function(){
	console.log(row_cnt);
	var row_cnt_label = get_last_row_index(row_cnt);
	var cmb_id = 'cmb_approver_'+row_cnt_label;
	
	var html  = '<tr>';
		html += '	<td style="width:10%">PENDING</td>';
		html += '	<td style="width:10%">'+row_cnt_label+'</td>';
		html += '	<td style="width:50%"><select class="chosen-select" id="'+cmb_id+'"></select></td>';
		html += '	<td style="width:10%"><input type="number" id="txt_sheet_no" style="width:100%" class="form-control" value="1" required></td>';
		html += '	<td style="width:10%"><input type="text" style="text-transform:uppercase" id="txt_cell_ordinates" style="width:100%" class="form-control" required></td>';
		html += '	<td style="width:10%"><button type="button" class="btn btn-danger fa fa-remove" id="btn_remove"></td>';
		html += '</tr>';
	$('#tbl_approver_view tbody').append(html);
	
	load_approver_list(cmb_id,function() {
		$('.chosen-select#'+cmb_id).chosen({width:"100%"});
	});
});
$('#tbl_approver_view tbody').on('click','#btn_remove', function() {
	var row = $(this).closest('tr');
	row.remove();
	reorder_approvers('tbl_approver_view');
});
$('#tbl_approver_view tbody').on('keypress','#txt_cell_ordinates', function(e) {
    var regex = new RegExp("^[a-zA-Z0-9]+$");
    var txt_box = $('#txt_cell_ordinates').val();
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
		if(txt_box.length == 0) {
			if($.isNumeric(str.charAt(0))) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
    }
    e.preventDefault();
    return false;
});
$('#frm_upload_report_view').on('submit', function(e) {
	e.preventDefault();
	$('.btn').prop("disabled",true);
	var serialized_data = new FormData(this);
	read_assigned_approvers('tbl_approver_view', function() {	
		serialized_data.append("action","update_report");
		serialized_data.append("fkreport",$('#btn_submit_view').val());
		serialized_data.append("username",username);
		serialized_data.append("approver_name",JSON.stringify(approver_name));
		serialized_data.append("approval_hierarchy",JSON.stringify(approval_hierarchy));
		serialized_data.append("approver_sheet",JSON.stringify(approver_sheet));
		serialized_data.append("approver_ordinate",JSON.stringify(approver_ordinate));
	
		call_ajax_attachment(serialized_data, handler, function(result){	
			// console.log(result);
			$('#div_message_submit_view').html('<span class="fa fa-info-circle"> </span> '+result['msg']);
			$('#div_message_submit_view').show();
			if(result['msg'] == 'Report title already exists!' || result['msg'] == 'Sorry, your file already exists.') {
				$('#btn_submit').show();
				$('#div_message_submit_view').attr('class', 'alert alert-danger');
			} else {
				$('#btn_submit').hide();
				$('#div_message_submit_view').attr('class', 'alert alert-success');
			}
			data_table.ajax.reload();
			$('.btn').prop("disabled",false);
		});
	});
	
});
$('#' + table_id + ' tbody').on('click', '#btn_cancel', function() {
	var fkreport = $(this).val();
	$('#btn_yes_cancel').val(fkreport);
	$('#mdl_cancel_request').modal();
});
$('#frm_cancel').on('submit', function(e) {
	e.preventDefault();
	$('.btn').prop("disabled",true);
	var serialized_data = $(this).serialize();
	cancel_request(serialized_data);
});
	
function get_last_row_index(num){
	row_cnt++;
	switch (num % 10) {
		case 1:  return num+'st';
		case 2:  return num+'nd';
		case 3:  return num+'rd';
	}
    return num+'th';  
}
function load_approver_list(cmb_approver, callback) {
	$('#'+cmb_approver).empty();
	var data = {
		"action" 	: "load_approver_list"
	} 
	call_ajax(data, handler, function(result){
		$('#'+cmb_approver).append('<option></option>');
		for(var i=0; i<result['ctr'];i++) {
			$('#'+cmb_approver).append('<option value="'+result['username'][i]+'">'+result['emp_name'][i]+'</option>');
		}
		callback();
	});
}
function reorder_approvers(tbl_approver_id) {
	var updated_ctr = '';
	var ctr = 1;
	if(tbl_approver_id == 'tbl_approver') {
		$('#tbl_approver tbody tr').each(function() {
			updated_ctr = get_last_row_index(ctr);
			var order 	= $(this).find('td:eq(0)').html(updated_ctr);
			ctr++;
		});
	} else if(tbl_approver_id == 'tbl_approver_view') {
		$('#tbl_approver_view tbody tr').each(function() {
			updated_ctr = get_last_row_index(ctr);
			var order 	= $(this).find('td:eq(1)').html(updated_ctr);
			ctr++;
		});
	}  
	row_cnt = ctr;
}
function read_assigned_approvers(tbl_approver, callback) {
	approval_hierarchy 	= [];
	approver_name 		= [];
	approver_sheet 		= [];
	approver_ordinate 	= [];
	var no_approver 	= '';
	
	if(tbl_approver == 'tbl_approver') {
		$('#tbl_approver tbody tr').each(function() {
			var order 			= $(this).find('td:eq(0)').text();
			var approver 		= $(this).find('td:eq(1) option:selected').val();
			var sheet_number 	= $(this).find('td:eq(2) #txt_sheet_no').val();
			var cell_ordinates 	= $(this).find('td:eq(3) #txt_cell_ordinates').val();
			if(approver == '') {
				no_approver += order + ',';
			} else {
				approval_hierarchy.push(order.slice(0,-2));
				approver_name.push(approver);
				approver_sheet.push(sheet_number);
				approver_ordinate.push(cell_ordinates);
			}	
		}).promise().done(function() {
			if(no_approver != '') {
				$('#div_message_approver').html('<span class="fa fa-info-circle"> </span> Please select name on '+no_approver.slice(0,-1)+' approver.');
				$('#div_message_approver').show();				
			} else {
				$('#div_message_approver').hide();
			}
			if(approval_hierarchy.length == 0) {
				$('#btn_submit').prop('disabled', true);
			} else {
				$('#btn_submit').prop('disabled', false);
			}
			callback();
		});
	} else if(tbl_approver == 'tbl_approver_view') {
		$('#tbl_approver_view tbody tr').each(function() {
			var status 			= $(this).find('td:eq(0)').text();
			var order 			= $(this).find('td:eq(1)').text();
			var approver 		= $(this).find('td:eq(2) option:selected').val();
			var sheet_number 	= $(this).find('td:eq(3) #txt_sheet_no').val();
			var cell_ordinates 	= $(this).find('td:eq(4) #txt_cell_ordinates').val();
			if(approver == '') {
				no_approver += order + ',';
			} else {
				if(status == 'PENDING') {
					approval_hierarchy.push(order.slice(0,-2));
					approver_name.push(approver);
					approver_sheet.push(sheet_number);
					approver_ordinate.push(cell_ordinates);
				}
			}	
		}).promise().done(function() {
			if(no_approver != '') {
				$('#div_message_approver_view').html('<span class="fa fa-info-circle"> </span> Please select name on '+no_approver.slice(0,-1)+' approver.');
				$('#div_message_approver_view').show();
			}
			if(approval_hierarchy.length == 0) {
				$('#btn_submit_view').prop('disabled', true);
			} else {
				$('#btn_submit_view').prop('disabled', false);
			}
			callback();
		});
	} 
}
function approvers_decision(serialized_data) {
	var data = {
		"action" 			: "approvers_decision",
		"fkdetails"			: $('#hdn_pkid').val(),
		"status"			: $('#btn_yes').val(),
		"username"			: username
	} 
	call_ajax_serialize(data, serialized_data, handler, function(result){
		data_table.ajax.reload();
		$('#modal_decision').modal('hide');
		$('#mdl_msg').modal();
		$('#mdl_msg_content').html('<span class="fa fa-info-circle"> </span> '+result['msg']);
		$('.btn').prop("disabled",false);
	});
}
function return_request_info(pkid, callback) {
	var data = {
		"action" 		: "return_request_info",
		"pkid"			: pkid
	} 
	call_ajax(data, handler, function(result){
		$('#txt_report_title_view').val(result['report_title']);
		$('#creator_remarks_view').val(result['remarks']);
		$('#btn_file_download_view').val(pkid);
		$('#btn_file_download_view').text(result['file_name']);
		// if(result['created_by'] != username || result['status'] != 'FOR APPROVAL') {
		if(result['created_by'] != username || result['status'] == 'CANCELLED') {
			$('#btn_submit_view').hide();
			$('#btn_add_approver_view').hide();
			$('#container_reupload').hide();
		} else {
			$('#btn_submit_view').show();
			$('#btn_add_approver_view').show();
			$('#container_reupload').show();
		}
		callback();
	});
}
function return_request_details(fkreport, action, callback) {
	row_cnt = 1;
	var data = {
		"action" 		: "return_request_details",
		"pkid"			: fkreport
	} 
	call_ajax(data, handler, function(result){
		$('#tbl_approver_view tbody').empty();
		var html = '';
		var order = '';
		var cmb_id = '';
		for(var i=0; i < result['ctr'];i++) {
			order  = get_last_row_index(result['approver_hierarchy'][i]);
			cmb_id = 'cmb_approver_view_'+order;
			
			append_approver_table_view(result['created_by'][i], order, cmb_id, result['approver_username'][i], result['esig_sheet_no'][i], result['esig_cell_ordinate'][i], result['approver_status'][i], action, function() {
			});
		}
		callback();
	});
}
function append_approver_table_view(created_by,order, cmb_id, approver, esig_sheet_no, esig_cell_ordinate, status, action, callback) {
	if(status == '' || status == 'PENDING' ) {	
		if(created_by != username) {
			var disabled = 'disabled';
		} else {
			var disabled = '';
		}
		status = 'PENDING';
	} else {
		if(action == 'REUPLOAD') {
			if(created_by != username) {
				var disabled = 'disabled';
			} else {
				var disabled = '';
			}
			status = 'PENDING';
		} else {
			var disabled = 'disabled';
		}
	}
	var html  = '<tr>';
		html += '	<td style="width:10%">'+status+'</td>';
		html += '	<td style="width:10%">'+order+'</td>';
		html += '	<td style="width:45%"><select class="chosen-select" data-placeholder="Select Approver Name" id="'+cmb_id+'"></select></td>';
		html += '	<td style="width:15%"><input type="number" min="1" step="1" id="txt_sheet_no" value="'+esig_sheet_no+'" style="width:100%" class="form-control" required></td>';
		html += '	<td style="width:10%"><input type="text" id="txt_cell_ordinates" value="'+esig_cell_ordinate+'" style="width:100%" class="form-control" required></td>';
		html += '	<td style="width:10%"><button type="button" class="btn btn-danger fa fa-remove" id="btn_remove" '+disabled+'></td>';
		html += '</tr>';	
	$('#tbl_approver_view tbody').append(html);
	load_approver_list(cmb_id,function() {
		$('.chosen-select#'+cmb_id).chosen({width:"100%"});
		$('#'+cmb_id).val(approver).trigger("chosen:updated");
		if(action == 'REUPLOAD') {
			$('#'+cmb_id).prop('disabled', false).trigger("chosen:updated");
		} else {
			if(status == 'PENDING') {
				$('#'+cmb_id).prop('disabled', false).trigger("chosen:updated");
			} else {
				$('#'+cmb_id).prop('disabled', true).trigger("chosen:updated");
			}
		}
	});
	
	callback();
}
function cancel_request(serialized_data) {
	var data = {
		"action" 		: "cancel_request",
		"pkid"			: $('#btn_yes_cancel').val(),
		"username"		: username
	} 
	call_ajax_serialize(data, serialized_data, handler, function(result){
		data_table.ajax.reload();
		$('#mdl_cancel_request').modal('hide');
		$('#mdl_msg').modal();
		$('#mdl_msg_content').html('<span class="fa fa-info-circle"> </span> '+result['msg']);
		$('.btn').prop("disabled",false);
	});
}

</script>