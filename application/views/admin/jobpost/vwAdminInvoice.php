
<?php $this->load->view('admin/includes/vwHeader'); ?>
 <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
 <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
 <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="http://formvalidation.io/vendor/formvalidation/js/formValidation.min.js"></script>
<script src="http://formvalidation.io/vendor/formvalidation/js/framework/bootstrap.min.js"></script>
<link href="http://formvalidation.io/vendor/formvalidation/css/formValidation.min.css" rel="stylesheet">
<script src="<?php echo base_url();?>includes/js/validate.js"></script>
<script>

 $(document).on('click', '.toggle-unaward-job', function (e) {
        var bidjob_id = $(this).data('id');
        var job_id    = $(this).data('job');
        var trans_id  = $(this).data('trans');
        var redirect;

        $('#dialog-document-unaward').dialog({
            resizable: false,
            height: "auto",
            width: 500,
            modal: false,
            closeOnEscape: false,
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                "Yes": function () {
                    $(this).dialog('close');

                    $.ajax({
                        url: "<?php echo base_url('admin_jobpost/unaward_check_if_invoiced') ?>",
                        type: "get",
                        data: { job_id: job_id, bidjob_id: bidjob_id, trans_id: trans_id },
                        dataType: 'json',
                        success: function (response) {
                            redirect = response.redirect;

                            if (response.success && response.data.is_invoiced) {
                                $('#dialog-document-unaward-confirm').dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 500,
                                    modal: false,
                                    closeOnEscape: false,
                                    open: function(event, ui) {
                                        $(".ui-dialog-titlebar-close").hide();
                                    },
                                    buttons: {
                                        "Yes": function () {
                                            $(this).dialog('close');

                                            $('#dialog-document-unaward-message').dialog({
                                                resizable: false,
                                                height: "auto",
                                                width: 500,
                                                modal: false,
                                                closeOnEscape: false,
                                                open: function(event, ui) {
                                                    $(".ui-dialog-titlebar-close").hide();
                                                },
                                                buttons: {
                                                    "Submit": function () {
                                                        $(this).dialog('close');

                                                        var message = $('#message').text();

                                                        $.ajax({
                                                            url: "<?php echo base_url('admin/awardcupdate') ?>/" + bidjob_id + '/' + job_id,
                                                            type: "get",
                                                            data: { trans_id: trans_id, form: $('#notification-form').serialize() },
                                                            dataType: 'json',
                                                            success: function (response) {

                                                                if (response.review_job) {
                                                                    $('#dialog-document-review-job-info').dialog({
                                                                        resizable: false,
                                                                        height: "auto",
                                                                        width: 600,
                                                                        modal: false,
                                                                        closeOnEscape: false,
                                                                        open: function(event, ui) {
                                                                            $(".ui-dialog-titlebar-close").hide();
                                                                        },
                                                                        buttons: {
                                                                            'Okay': function() {
                                                                                window.location.href = response.url;
                                                                            }
                                                                        }
                                                                    });

                                                                    $('#job-name-wrapper').html(response.review_job.job_name);
                                                                    $('#line-number-wrapper').html(response.review_job.line_number);
                                                                    $('#hiring-status-wrapper').html(response.review_job.hiring_status);
                                                                    $('#has-awarded-wrapper').html(response.review_job.has_awarded);
                                                                } else {
                                                                    window.location.href = response.url;
                                                                }

                                                            }
                                                        });
                                                    },
                                                    "Cancel": function () {
                                                        $(this).dialog('close');
                                                    }
                                                }
                                            });
                                        },
                                        "No": function () {
                                            $(this).dialog('close');
                                        }
                                    }
                                });

                                $('#translator-name').text(response.data.translator_name);
                                $('#invoice-due').text('$' + response.data.invoice_amount);
                            } else if (response.success && !response.data.is_invoiced) {
                                $('#dialog-document-unaward-message').dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 500,
                                    modal: false,
                                    closeOnEscape: false,
                                    open: function(event, ui) {
                                        $(".ui-dialog-titlebar-close").hide();
                                    },
                                    buttons: {
                                        "Submit": function () {
                                            $(this).dialog('close');

                                            var message = $('#message').text();

                                            $.ajax({
                                                url: "<?php echo base_url('admin/awardcupdate') ?>/" + bidjob_id + '/' + job_id,
                                                type: "get",
                                                data: { trans_id: trans_id, form: $('#notification-form').serialize() },
                                                dataType: 'json',
                                                success: function (response) {

                                                    if (response.review_job) {
                                                        $('#dialog-document-review-job-info').dialog({
                                                            resizable: false,
                                                            height: "auto",
                                                            width: 600,
                                                            modal: false,
                                                            closeOnEscape: false,
                                                            open: function(event, ui) {
                                                                $(".ui-dialog-titlebar-close").hide();
                                                            },
                                                            buttons: {
                                                                'Okay': function() {
                                                                    window.location.href = response.url;
                                                                }
                                                            }
                                                        });

                                                        $('#job-name-wrapper').html(response.review_job.job_name);
                                                        $('#line-number-wrapper').html(response.review_job.line_number);
                                                        $('#hiring-status-wrapper').html(response.review_job.hiring_status);
                                                        $('#has-awarded-wrapper').html(response.review_job.has_awarded);
                                                    } else {
                                                        window.location.href = response.url;
                                                    }

                                                }
                                            });
                                        },
                                        "Cancel": function () {
                                            $(this).dialog('close');
                                        }
                                    }
                                });
                            } else {
                                $('#dialog-document-unaward-error').dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 500,
                                    modal: false,
                                    closeOnEscape: false,
                                    open: function(event, ui) {
                                        $(".ui-dialog-titlebar-close").hide();
                                    },
                                    buttons: {
                                        "Okay": function () {
                                            $(this).dialog('close');
                                        }
                                    }
                                });
                            }
                        }
                    });
                },
                "No": function () {
                    $(this).dialog('close');
                }
            }
        });
    });


	$(document).ready(function() {

	    <?php if($this->session->flashdata('success_message')){ ?>
		alert('<?php echo $this->session->flashdata('success_message'); ?>');
		<?php }
		if ($this->session->flashdata('error_message')) { ?>
        alert('<?php echo  $this->session->flashdata('error_message'); ?>');
        <?php } ?>
		var last_selected_type = $("#type option:selected").val();
    	var last_selected_proofread_required = $("input[name='proofread_required']:checked").val();

    	var current_selected_line_month = $("#lineMonth option:selected").val();
    	var current_selected_line_year = $("#lineYear option:selected").val();
    	var current_line_number_value = $('#lineNumber').val();

    	var formChanged = false;
		
		$(document).on('change', '#lineMonth, #lineYear', function (e) {
        	$('#lineNumber').trigger('blur');
    	});
    	
    	 $(document).on('blur', '#lineNumber', function (e) {
        if (current_selected_line_month == $("#lineMonth option:selected").val() && current_selected_line_year == $('#lineYear option:selected').val() && current_line_number_value == $(this).val()) {
        } else {
            $.ajax({
                url: "<?php echo base_url() ?>jobpost/check/line-number",
                data: { line_month: $('#lineMonth option:selected').val(), line_year: $('#lineYear option:selected').val(), line_number: $('#lineNumber').val() },
                success: function (response) {
                    if (response != null && response != '') {
                        response = jQuery.parseJSON(response);
                        $('#dialog-line-numbers').dialog({
                            resizable: false,
                            height: "auto",
                            width: 700,
                            modal: false,
                            closeOnEscape: false,
                            open: function(event, ui) {
                                $(".ui-dialog-titlebar-close").hide();
                            },
                            buttons: {
                                "Yes": function () {
                                    $(this).dialog('close');

                                    $.ajax({
                                        url: "<?php echo base_url() ?>admin_jobpost/get/job-price",
                                        data: { line_month: $('#lineMonth option:selected').val(), line_year: $('#lineYear option:selected').val(), line_number: $('#lineNumber').val() },
                                        success: function (response) {
                                            if (response != null || response != '') {
                                                response = jQuery.parseJSON(response);
                                                $('.remaining-balance-wrapper').html('Remaining Balance: $' + response.price);
                                                $('#remaining_balance').val(response.price);
                                                $('#price').val(response.original_price);
                                                $('#original_price').val(response.original_price);

                                                $('#_lineMonth').val($('#lineMonth option:selected').val());
                                                $('#_lineYear').val($('#lineYear option:selected').val());
                                                $('#_lineNumber').val($('#lineNumber').val());

                                                $('#price').attr('readonly', 'readonly');
                                                $('#lineNumber').attr('readonly', 'readonly');
                                                $('#lineMonth').attr('disabled', 'disabled');
                                                $('#lineYear').attr('disabled', 'disabled');

                                                formChanged = true;
                                            }
                                        }
                                    });
                                },
                                "No": function () {
                                    $(this).dialog("close");
                                    $('#lineNumber').val(current_line_number_value);
                                    $('#lineMonth option[value='+ current_selected_line_month +']').attr('selected', 'selected');
                                    $('#lineYear option[value='+ current_selected_line_year +']').attr('selected', 'selected');
                                    $('#lineNumber').focus();

                                    formChanged = false;
                                }
                            }
                        });

                        var content = "Job: <span style='font-weight: bold'>" + response.job_name + "</span>, <span style='font-weight: bold'>Language: " + response.language_from + "</span> to <span style='font-weight: bold'>" + response.language_to + "</span>, Price: <span style='font-weight: bold'>$"+ response.price +"</span>. Date Posted: <span style='font-weight: bold'>" + response.date_added + "</span>";
                        $('.job-info-wrapper').html(content);
                    }
                }

            });
        }
    });
    	
	});
</script>

<script>
	$(document).ready(function() {
		$("#add_invoice").validate({
			ignore: [],
			rules: {
				amount_owed:"required",
				datetimepicker1:"required",
				trans_id:"required",
				to_language:"required",
				from_language:"required",
				lineNumber:"required",
				price:"required",
				amount_owed:"required",
				awarded_date:"required",
				q1:"required",
				q2:"required",
				q3:"required",
				q4:"required",
				rate:"required"
			},
			errorPlacement: function(error, element) {
	            if (element.attr("type") == "radio") {
	                error.insertBefore(element);
	            } else {
	                error.insertAfter(element);
	            }
        	},
			submitHandler: function(form) {
				add();
			}
			});

	});
	
	function add() {
		$.ajax({
        	url: "<?php echo base_url()?>admin_invoice/add_action_invoice",
			type: "POST",
			data: new FormData($('#add_invoice')[0]),
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(data)
		    {
	   				alert('Invoice Added');
	   				//$('#add').waitMe('hide');
	   				window.setTimeout(function(){ document.location.reload(true); }, 2000);
		    },
		  	error: function() 
	    	{
	    	} 	        
	   });
		return false;
	}


    function edit() {
        $.ajax({
            url: "<?php echo base_url()?>admin_invoice/add_action_invoice",
            type: "POST",
            data: new FormData($('#add_invoice')[0]),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data)
            {
                alert('Invoice Added');
                //$('#add').waitMe('hide');
                window.setTimeout(function(){ document.location.reload(true); }, 2000);
            },
            error: function()
            {
            }
        });
        return false;
    }
</script>
<style>
	.error {
		color:red;
	}
	#poor {
		color:red;
		font-size: 20px;
	}
	#average {
		color:yellow;
		font-size: 20px;
	}
	#pass {
		color:green;
		font-size: 20px;
	}
</style>


<div id="main-container" class="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
	<?php	$this->load->view('admin/includes/vwSidebar-left'); ?>
	<div class="main-content">
		<div class="main-content-inner">

			<div class="breadcrumbs" id="breadcrumbs">
				<script type="text/javascript">
					try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
				</script>
				<ul class="breadcrumb">
					<li>
						<i class="ace-icon fa fa-home home-icon"></i>
						<a href="#">Home</a>
					</li>
					<li>
						<a href="#">Invoice</a>
					</li>
					<li class="active">Invoice List</li>
				</ul>
			</div>

			<div class="page-content">

				<div class="page-header">
					<h1>Invoice <small><i class="ace-icon fa fa-angle-double-right"></i>View Invoice List</small></h1>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">Invoice List</div>
				  	<div class="panel-body" id="invoice_list">

				  		<div class = "row">
				  			<div class = "col-md-12">
				  																		<div class = "col-md-4">
				  				<button class="btn btn-info btn-reset btn-md pull-left" type="button" data-toggle="modal" data-target="#invoice_modal" >Manually Add Invoice</button>
				  			</div>
				  			
				  			<div class = "col-md-8">
				  				<button class="btn btn-info btn-reset btn-md pull-right" onClick="reload()" >Reset Filter</button>
				  				</div>
				  			
				  			</div>
				  		</div>

					  	<div class = "row" style = "padding: 15px 0;">
					  		<?php echo form_open('admin/invoice/'); ?>
					  		<div class = "col-md-6">
					  			<h5>Filter by Date</h5>
                                <?php
                                $date_form = $this->session->userdata('dateFrom');
                                $date_to = $this->session->userdata('dateTo');

                                if(isset($date_form) && $date_form != ''){
                                    $date_form = date('m/d/Y',strtotime($date_form));
                                }else{
                                    $date_to = '';
                                }

                                if(isset($date_to) && $date_to!= ''){
                                    $date_to = date('m/d/Y',strtotime($date_to));
                                }else{
                                    $date_to = '';
                                }
                                ?>
						  		<div class = "row">
						  			<div class = "col-md-5">
						  				<input class = "form-control" type="text" id = "invoiceDateFrom" name = "invoiceDateFrom" placeholder = "From Date" value="<?php echo $date_form; ?>">
						  			</div>
						  			<div class = "col-md-5">
						  				<input class = "form-control" type="text" id = "invoiceDateTo" name = "invoiceDateTo" placeholder = "To Date" value="<?php echo $date_to; ?>">
						  			</div>
						  			<div class = "col-md-2">
										<?php $data_submit= array('name' => 'mysubmit', 'class' => 'btn btn-primary btn-sm', 'value' => 'Go'); ?>
					  					<?php echo '&nbsp;&nbsp;'.form_submit($data_submit).'&nbsp;&nbsp;'; ?>
						  			</div>
						  		</div>

					  		</div>
					  		<div class = "col-md-6">
					  			<h5>Filter by Payment Status and Keyword Search</h5>
					  			<div class = "row">
					  				<div class = "col-md-6">
									    <select name="payment_status" class="validate[required] form-control">
									        <option value="0" <?php if($payment_status_selected=='0'){echo 'selected';} ?> >Unpaid</option>
									        <option value="1" <?php if($payment_status_selected=='1'){echo 'selected';} ?> >Paid</option>
									    </select>
					  				</div>
					  				<div class = "col-md-6">
					  					<input name = "search_string" id = "search_string" placeholder = "Search Key" type = "text" value="<?php echo $this->session->userdata('search_string'); ?>">
					  					<?php $data_submit= array('name' => 'mysubmit', 'class' => 'btn btn-primary btn-sm', 'value' => 'Go'); ?>
					  					<?php echo '&nbsp;&nbsp;'.form_submit($data_submit).'&nbsp;&nbsp;'; ?>
					  				</div>
					  			</div>

					  		</div>
					  		<?php echo form_close(); ?>
					  	</div>

                        <style>
                            .sort{width: 100%;height: 15px;text-align: right;}
                            .sort-a{text-align: right; position: absolute;margin-left: -10px;margin-top: -21px; color:#d3d3d3}
                            .sort-a:before{ content: "\f0d8"; position: absolute; font-family: FontAwesome; margin-right: -20px; }
                            .sort-d{text-align: right; position: absolute;margin-left: -10px;margin-top: -13px; color: #d3d3d3}
                            .sort-d:before{ content: "\f0d7"; position: absolute; font-family: FontAwesome; margin-right: -20px; }

                        </style>

                        <!--?php echo $pages; ?-->

						<table class="table table-striped table-bordered">
							<tr>
								<th style = "text-align: center;">Invoice No</th>
								<th style = "width: 200px; text-align: center;">Job Title</th>
								<th style = "width: 150px; text-align: center;">Translator</th>
								<!--<th style = "width: 100px; text-align: center;">Time test</th>-->
								<th style = "text-align: center;">Price</th>
								<th style = "text-align: center; width: 100px;">Awarded Date</th>
								<th style = "text-align: center; width: 100px;">Completed Date</th>
								<th style = "width: 100px;">
                                    <a href="javascript: void(0);" class="toggle-sort" data-sort="payment_date" data-sorttype="desc">Due Date</a>
                                    <div class="sort">
                                        <?php
                                        $sort_type = $this->session->userdata('order_by_invoice');
                                        if(isset($sort_type) == false || $sort_type == ''){
                                            $sort_type = 'invoice.id ASC';
                                            $this->session->set_userdata('order_by_invoice',$sort_type);
                                        }
                                        ?>
                                    <a href="javascript:void(0);" <?php echo (isset($sort_type) && $sort_type == 'bidjob.complete_date ASC')?'style="color:#337ab7!important"':''; ?> onclick="sort('bidjob.complete_date ASC')" class="sort-a "></a>
                                    <a href="javascript:void(0);" <?php echo (isset($sort_type) && $sort_type == 'bidjob.complete_date DESC')?'style="color:#337ab7!important"':''; ?> onclick="sort('bidjob.complete_date DESC')" class="sort-d"></a>
                                    </div>
                                </th>
								<th style = "text-align: center; width: 100px;">Date Paid</th>
								<th style = "text-align: center; width: 300px;">Payment</th>
								<th style = "text-align: center;">Operations</th>
							</tr>
							<tbody id="invoice-wrapper">
								<?php $partialTotal = "0.00"; ?>
								
								<?php foreach ($invoices as $rowInvoice){ ?>
									<tr>
										<td><?php echo $rowInvoice->invoice_id; ?></td>
                                        <?php if ($rowInvoice->lineNumberCode) { ?>
										<td><a href = "<?=(base_url());?>admin_jobpost/edit/<?php echo $rowInvoice->job_id; ?>"><?php if(!empty($rowInvoice->name)) { echo $rowInvoice->name; } else { echo 'Job Manually Entered'; } ?>&nbsp;/&nbsp;<?php echo $rowInvoice->lineNumberCode ?></a></td>
                                        <?php } else { ?>
                                        <td><a href = "<?=(base_url());?>admin_jobpost/edit/<?php echo $rowInvoice->job_id; ?>"><?php echo $rowInvoice->name; ?></a></td>
                                        <?php } ?>
										<td><a href = "<?= ($admin_type != '' && in_array($admin_type,[4])==false)?base_url().'admin_translators/edittranslator/'.$rowInvoice->trans_id:'javascript:void(0);';?>"><?php echo $rowInvoice->first_name." ".$rowInvoice->last_name; ?></a></td>
										<!--<td><?php echo ($rowInvoice->time_need/1440); ?> Day(s)</td>-->

										<td>$<?php echo number_format($rowInvoice->bidjobprice, 2, '.', ','); ?></td>
											<?php $partialTotal = $partialTotal + $rowInvoice->bidjobprice; ?>

										<td style = "text-align: center;"><?php echo date('m-d-Y', strtotime($rowInvoice->award_date)); ?></td>
										<td style = "text-align: center;">
                                            <?php echo date('m-d-Y', strtotime($rowInvoice->complete_date)); ?>
										</td>
										<td style = "text-align: center;"><?php echo date('m-d-Y', strtotime('+31 days', strtotime($rowInvoice->complete_date))); ?></td>
										<td style = "text-align: center;">
											<?php if ($rowInvoice->payment == "0"){ ?>
												<form method = "post" action = "<?php echo base_url();?>admin_invoice/manual_payment/">
	                               					<input type = "hidden" name = "invoiceID" value = "<?php echo $rowInvoice->invoice_id; ?>">
                               						<button onclick = "return confirm('Are you sure you want to do a manual payment for this Invoice?');" type="submit" class="btn btn-primary btn-xs">Mark as Paid</button>
                               					</form>
											<?php }else if ($rowInvoice->payment == "1") { ?>
												<?php echo date('m-d-Y', strtotime($rowInvoice->payment_date)); ?>
											<?php } ?>
										</td>
										<td style = "text-align: center;">
											<?php if ($rowInvoice->payment == "0"){ ?>
												<?php
													$dueDate = strtotime($rowInvoice->complete_date.'+31 days');
													$currentDate = time();

													if ($dueDate < $currentDate){
														echo '<span class = "btn btn-danger btn-xs">Overdue</span>';
													}else{
														echo '<span class = "btn btn-success btn-xs">Open</span>';
													}
												?>
												<a href="<?php echo base_url().'paypal/?id='.$rowInvoice->bidjobid;?>" class="btn btn-warning btn-xs" target="_blank">Pay Now</a>
											<?php }else if ($rowInvoice->payment == "1") { ?>
					                            <form method = "post" action = "<?php echo base_url();?>admin_invoice/mark_unpaid/">
					                            	<input type = "hidden" name = "invoiceID" value = "<?php echo $rowInvoice->invoice_id; ?>">
					                            	<button onclick = "return confirm('Are you sure you want to mark this Invoice as Unpaid?');" type="submit" class="btn btn-danger btn-xs btn-block">Mark as Unpaid</button>
					                            </form>
											<?php } ?>
										</td>
										  <td>
										        <div class="hidden-sm hidden-xs action-buttons">
                                                    <?php if($rowInvoice->payment == 0){?>
                                                    <a class="red" href="javascript:void(0);" onClick="doconfirm();">
                                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                                    </a>
                                                    <?php } ?>
                                                    <?php if($rowInvoice->name == ''){ ?>
                                                    <a class="blue" href="javascript:void(0);" onclick="editInvoice('<?php echo  $rowInvoice->invoice_id; ?>')"><i class="ace-icon fa fa-pencil bigger-130"></i></a>
                                                    <?php } ?>
                                                </div>

                                            </td>
									</tr>
								<?php } ?>
							</tbody>
							<tfooter>
								<tr>
									<th colspan = "3"></th>
									<th>$<?php echo number_format($partialTotal, 2, '.', ','); ?></th>
									<th colspan = "6"></th>
								</tr>
							</tfooter>
						</table>

						<?php echo $pages; ?>

						<hr>
						<h3>Total Accounts Payable: $<?php echo (count($invoices) > 0)?number_format($payable, 2, '.', ','):'0.00'; ?></h3>

					</div>
				</div>

			</div>
		</div>
	</div>
</div>

 <div id="dialog-line-numbers" title="Validating Job Line Number" style="display:none">
            <p style="font-size: 14px;">This line number already has a job associated to it. Do you want to associate this?</p>
            <div class="job-info-wrapper" style="padding: 10px; font-size: 15px;"> </div>
        </div>

<!-- InPage Scripts -->
<script type="text/javascript">

    $(document).ready(function () {
        $(document).on('click', '.toggle-sort', function (e) {
            var $sort = $(this).data('sort');
            var $sort_type = $(this).data('sorttype');

            if ($sort_type == 'asc') {
                $(this).data('sorttype', 'desc');
            } else {
                $(this).data('sorttype', 'asc');
            }

            $.ajax({
                url: "<?php echo base_url() ?>admin_invoice/reload_invoices",
                data: { order_by: $sort, order_type: $sort_type },
                success: function (response) {
                    $('#invoice-wrapper').html(response);
                }
            });
        });
    });

	$(function() {
    	$( "#invoiceDateFrom" ).datepicker().on('changeDate',function(e) {

		});

	    $( "#invoiceDateTo" ).datepicker().on('changeDate',function(e) {

		});

	});


	function editInvoice(id){
	if(id != null){
$.ajax({
    type:"POST",
    url: "<?= base_url(); ?>admin/invoice/edit/" + id,
    data:{id: id},
    dataType:'json',
    success:function (data) {
        if(data.res == 1) {
            $("#invoice_edit_modal").find('.modal-body').html(data.invoice);
            $('#datetimepicker1_edit').datetimepicker({
            });
            $('#awarded_date_edit').datetimepicker({
            });
            $("#invoice_edit_modal").modal('show');
        }
    }
});
	}
}

function sort(data) {
    if(data != ''){
        $.ajax({
            type:"POST",
            url:"<?php echo base_url().'admin/invoice/sort' ?>",
            data:{sort_type: data},
            success:function (data) {
                window.location.reload();
            }
        });
    }
}

	function reload(){

        $.ajax({
            type: "POST",
            url: "<?=(base_url());?>admin_invoice/clearFilters",
        });

		window.location.href="<?php echo base_url().'admin/invoice/'?>";

	}

	function confir(id,job_id) {

	    con = confirm("Are you sure to mark as Completed this awarded project?");

	    if(con!=true){
	        return false;
	    } else {
			window.location.href="<?php echo base_url(); ?>admin/complete/"+id+"/"+job_id;
		}

	}
	function doconfirm()
	{
	$( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Yes": function() {
//            $( "#dialog-confirm" ).dialog( "close" );
		  $( "#dialog-form" ).dialog({
      resizable: false,
      height: "auto",
      width: 350,
      modal: true,
      buttons: {
        "Submit and delete": function(){
			var message = $("#del_message").val();
			if(message!= ''){
				$("#error_del_message").html('');
				$("#notification-form").submit();
			}else{
				$("#error_del_message").html('Please enter a message');
			}
		},
        "Cancel": function() {
			$("#error_del_message").html('');
			$("#notification-form").trigger('reset');
            $( "#dialog-form" ).dialog( "close" );
        }
      },
              open:function () {
              $(".ui-dialog").css('display','table');
              $(".ui-dialog").css('left','40%');
              $(".ui-dialog-titlebar").css('display','table');
              },
      close: function() {
			$("#error_del_message").html('');
			$("#notification-form").trigger('reset');
      }
    });
        },
		"No":function(){

            $( "#dialog-confirm" ).dialog( "close" );
		}
      }
    });

	}
	function goBack() {
    	window.history.back();
	}

</script>
 
 <!--Dialog show div-->
 
 <div id="dialog-confirm" title="Confirm delete invoice" style="display: none">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Are you sure you want to delete this invoice? This also delete the bid against which the invoice is generated.</p>
</div>


<div id="dialog-form" title="Message" style="display:none">
    <div class="row">
        <div class="col-md-12">
            <p style="font-size: 14px;padding:10px 0 10px;">Let the Freelancer know why their invoice is being deleted.</p>
        </div>
        <div class="col-md-12">
            <form id="notification-form" action="<?php echo base_url(); ?>admin/deleteinvoice/<?php echo $rowInvoice->invoice_id;  ?>" method="post">
                <textarea id="del_message" name="message" style="width: 100%; height: 100px"></textarea>
				<span id="error_del_message" style="color: red"></span>
				<div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>
 
 <!--Dialog show div-->




<div class="modal fade" id="invoice_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Invoice</h4>

            </div>
            <div class="modal-body">
            <form method="post" action="#" id="add_invoice">
			<div class="control-group">
	            <label class="control-label" for="txt_email">Email:</label>
	            <div class="controls">
	                <select class = "form-control selectpicker" data-live-search="true" id="trans_id" name="trans_id">
	   						<option value="">Select Translator</option>
	   						<?php foreach($translator as $tr) { ?>
	   							<option value="<?php echo $tr->id;?>"><?php echo $tr->first_name.''.$tr->last_name.'('.$tr->email_address.')';?></option>
	   						<?php } ?>	
	   				</select>
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Translate From:</label>
	            <div class="controls">
	                <select class = "form-control selectpicker" data-live-search="true" id="from_language" name="from_language">
	   						<option value="">Select Language</option>
	   						<?php foreach($list_languages as $fr) { ?>
	   							<option value="<?php echo $fr->id;?>"><?php echo $fr->name;?></option>
	   						<?php } ?>	
	   				</select>
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Translate To:</label>
	            <div class="controls">
	                <select class = "form-control selectpicker" data-live-search="true" id="to_language" name="to_language">
	   						<option value="">Select Languate</option>
	   						<?php foreach($list_languages as $to) { ?>
	   							<option value="<?php echo $to->id;?>"><?php echo $to->name;?></option>
	   						<?php } ?>	
	   				</select>
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Line Number:</label>
	            <div class="controls">
	             <?php
                                                $curr_month = date("m"); $curr_year = date("y");
                                                if (isset($curr_values['lineMonth'])) {
                                                    $curr_month = $curr_values['lineMonth'];
                                                }

                                                if (isset($curr_values['lineYear'])) {
                                                    $curr_year = $curr_values['lineYear'];
                                                }
                                            ?>
                    <div class="row">
                    <div class="col-md-3">
                  	<select name="lineMonth" id="lineMonth" class="form-control selectpicker" >
                								<option value="01" <?php if($curr_month == "01") echo "selected"; ?>>January</option>
                								<option value="02" <?php if($curr_month == "02") echo "selected"; ?>>February</option>
                								<option value="03" <?php if($curr_month == "03") echo "selected"; ?>>March</option>
                								<option value="04" <?php if($curr_month == "04") echo "selected"; ?>>April</option>
                								<option value="05" <?php if($curr_month == "05") echo "selected"; ?>>May</option>
                								<option value="06" <?php if($curr_month == "06") echo "selected"; ?>>June</option>
                								<option value="07" <?php if($curr_month == "07") echo "selected"; ?>>July</option>
                								<option value="08" <?php if($curr_month == "08") echo "selected"; ?>>August</option>
                								<option value="09" <?php if($curr_month == "09") echo "selected"; ?>>September</option>
                								<option value="10" <?php if($curr_month == "10") echo "selected"; ?>>October</option>
                								<option value="11" <?php if($curr_month == "11") echo "selected"; ?>>November</option>
                								<option value="12" <?php if($curr_month == "12") echo "selected"; ?>>December</option>
                							</select>
                	</div>
                	<div class="col-md-3">
                		<select name="lineYear" id="lineYear"class="form-control selectpicker" >
                								<?php foreach(range(date('Y'), 2050) as $year) { ?>
            									<option value="<?php echo substr($year, -2); ?>" <?php if($curr_year == substr($year, -2)) echo 'selected'; ?>><?php echo $year; ?></option>
                								<?php } ?>
                							</select>
                	</div>
                	<div class="col-md-3">
                		<input name="lineNumber" type="text" id="lineNumber" class="form-control" value="<?php echo set_value('lineNumber') ?>" />
                		<input type="hidden" id="_lineMonth" name="_lineMonth" value="" />
                                        <input type="hidden" id="_lineYear" name="_lineYear" value="" />
                                        <input type="hidden" id="_lineNumber" name="_lineNumber" value="" />
                	</div>
                	</div>
	               
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Amount Charged to Client:</label>
	            <div class="controls">
	               <input class = "form-control" name="price" id="price" value="<?php echo set_value('price') ?>">
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Amount owed to translator:</label>
	            <div class="controls">
	               <input class = "form-control" name="amount_owed" id="amount_owed">
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Awarded Date:</label>
	            <div class="controls">
	          	<div class="form-group">
	                <div class='input-group date' id='awarded_date'>
	                    <input id="awarded_date" name="awarded_date" type='text' class="form-control" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
            	</div>
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<div class="control-group">
	            <label class="control-label" for="txt_email">Date the job was handed in by the translator:</label>
	            <div class="controls">
	          	<div class="form-group">
	                <div class='input-group date' id='datetimepicker1'>
	                    <input id="datetimepicker1" name="datetimepicker1" type='text' class="form-control" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
            	</div>
	                <p class="help-block"></p>
	            </div>
        	</div>
        	
        	<p><h2>Rating</h2></p>
        	<p>How good did the original translator do?</p>
        	<p><i>10 being the highest</i></p>
        	
        	<div class="control-group">
	            <div class="controls">
		          	<div class="form-group">
		           		<div class="form-inline">
					        <label class="radio" id="poor">
					        	1
					            <input type="radio" value="1" name="rate" id="rate"/>
					        </label>
					        <label class="radio" id="poor">
					        	2
					            <input type="radio" value="2" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="poor">
					        	3
					            <input type="radio" value="3" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="poor">
					        	4
					            <input type="radio" value="4" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="poor">
					        	5
					            <input type="radio" value="5" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="average">
					        	6
					            <input type="radio" value="6" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="average">
					        	7
					            <input type="radio" value="7" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="pass">
					        	8
					            <input type="radio" value="8" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="pass">
					        	9
					            <input type="radio" value="9" name="rate" id="rate"/>
					         </label>
					         <label class="radio" id="pass">
					        	10
					            <input type="radio" value="10" name="rate" id="rate"/>
					         </label>
    					</div>     
	            	</div>
	            </div>
        	</div>
        	
        	
        	
        	<div class="control-group">
        		<div class="q1" style="margin-top: 10px;">
		            <p>1. Is all spelling and grammar now accurate?</p>
		            <input type="radio" id="q1-yes-answer" name="q1" value="Is all spelling and grammar now accurate? Yes" /><label for="q1-yes-answer">Yes</label>
		            <input type="radio"  id="q1-no-answer" name="q1" value="Is all spelling and grammar now accurate? No" /><label for="q1-no-answer">No</label>
		        </div>
        	</div>
        	
        	<div class="control-group">
        		<div class="q2">
		            <p>2. Has literal translation been avoided?</p>
		            <input type="radio" id="q2-answer" name="q2" value="Has literal translation been avoided? Yes" /><label for="q2-yes-answer">Yes</label>
		            <input type="radio" id="q2-answer" name="q2" value="Has literal translation been avoided? No" /><label for="q2-no-answer">No</label>
        		</div>
        	</div>
        	
        	<div class="control-group">
        		<div class="q3">
		            <p>3. Have numbers and money quantities been changed to match the target text style.</p>
		            <p>For Example: 10.000 to 10,000 if translating or vise versa?</p>
		            <input type="radio" id="q3-answer" name="q3" value="Have numbers and money quantities been changed to match the target text style Yes" /><label for="q3-yes-answer">Yes</label>
		            <input type="radio" id="q3-answer" name="q3" value="Have numbers and money quantities been changed to match the target text style No" /><label for="q3-no-answer">No</label>
		            <input type="radio" id="q4-answer" name="q3" value=" Have numbers and money quantities been changed to match the target text styleDon't know" /><label for="q4-no-answer">Dont know</label>
        		</div>
        	</div>
        	
        	<div class="control-group">
        		 <div class="q4">
            <p>4. Has the terminology been consistent throughout the text?</p>
            <input type="radio" id="q4-answer" name="q4" value="Has the terminology been consistent throughout the text? Yes" /><label for="q4-yes-answer">Yes</label>
            <input type="radio" id="q4-answer" name="q4" value="Has the terminology been consistent throughout the text? No" /><label for="q4-no-answer">No</label>
        </div>
        	</div>
        	
        	<div class="control-group">
	      		<div class="controls">
	           		<button class="btn btn-primary" type="submit">Save Changes</button>
	        	</div>
     		</div>
        	</form>
            </div>
            
        </div>
    </div>
</div>

<style>
    .ui-dialog-titlebar{
        display: table!important;
    }
</style>


<div class="modal fade" id="invoice_edit_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Invoice</h4>

            </div>
            <div class="modal-body">

            </div>

        </div>
    </div>
</div>





<script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker();
                $('#awarded_date').datetimepicker();
            });
</script>
<?php $this->load->view('admin/includes/vwFooter'); ?>
