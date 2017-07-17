<?php
$this->load->view('admin/includes/vwHeader');
$adminID=$this->session->userdata('admin_id');
?>


		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<!-- #section:basics/sidebar -->
			<?php
				$this->load->view('admin/includes/vwSidebar-left');
			?>
			<div class="main-content">
				<div class="main-content-inner">
					<!-- #section:basics/content.breadcrumbs -->
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
								<a href="#">Dashboard</a>
							</li>

						</ul><!-- /.breadcrumb -->

						<!-- #section:basics/content.searchbox -->
						<!--<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
							</form>
						</div>--><!-- /.nav-search -->

						<!-- /section:basics/content.searchbox -->
					</div>

					<!-- /section:basics/content.breadcrumbs -->
					<div class="page-content">
						<!-- #section:settings.box -->
                        <?php
							$this->load->view('admin/includes/vwSidebar-settings');
						?>
						<!-- /.ace-settings-container -->

						<!-- /section:settings.box -->
						<div class="page-header">
							<h1>
								Working Jobs
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
                                <!--<div class="alert alert-block alert-success">
									<button type="button" class="close" data-dismiss="alert">
										<i class="ace-icon fa fa-times"></i>
									</button>

									<i class="ace-icon fa fa-check green"></i>

									Welcome to
									<strong class="green">
										Translation
									</strong>, Admin panel.
								</div>-->

                             <?php if($this->session->flashdata('success_message'))
							    { ?>
								 <div class="alert alert-block alert-success">
								 <button type="button" class="close" data-dismiss="alert">
                                 <i class="ace-icon fa fa-times"></i>
                                 </button>

                                <p><?php echo $this->session->flashdata('success_message'); ?></p>
                                 </div>

                               <?php
							   }
							  ?>
                                 <?php if($this->session->flashdata('error_message'))
							    { ?>
								 <div class="alert alert-block alert-danger">
								 <button type="button" class="close" data-dismiss="alert">
                                 <i class="ace-icon fa fa-times"></i>
                                 </button>

                                <p><?php echo $this->session->flashdata('error_message'); ?></p>
                                 </div>

                               <?php
							   }
							  ?>


                                <!--<div class="admin_logo">
                                <ul>

 <li><a target="_blank" href="<?php echo base_url().'admin/changeprofile';?>"><img src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>images/changeprofile.jpg" width="150"></a></li>

 <li><a target="_blank" href="<?php echo base_url().'admin/changepass';?>"><img src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>images/changepassword.jpg" width="150"></a></li>

 <li><a target="_blank" href="<?php echo base_url().'admin/sitesettings';?>"><img src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>images/sitesetting.png" width="150"></a></li>
 </ul>
                                </div>-->




    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr> <th class="center">Job Title</th>
                                                 <th class="center">Translator</th>
                                                 <th class="center">Proposal</th>
                                                 <th class="center">Time</th>
                                                  <th class="center">Message</th>
                                                 <th class="center">Price</th>
                                                 <th class="center">Awarded Date</th>
                                                <!-- <th class="center">Canceled</th>-->
                                                <th class="center">Status</th>
                                                 <th>Details</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                        <style type="text/css">
											.order_by_cls {
												display:none;
											}


											.stage
											{
												display:none;
											}
										</style>
                                    <?php
									//$job_id= $this->uri->segment(3);
									$attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
									//save the columns names in a array that we will use as filter
									$options_category = array();
									//echo '<pre>'; print_r($category);
									foreach ($workingjob as $array) {
									foreach ($array as $key => $value) {
										$options_category[$key] = $key;
									  }
									  break;
									}

								    echo form_open('dashboard/index/', $attributes);
									?>



                                    <div class="stage">
                                    <select name="job_stage" class=" col-sm-2 validate[required]" >
                                    <option value=""> Select Stage </option>

                                    <option value="1" <?php if($stage_selected=='1'){echo 'selected';} ?> >Working</option>
                                    <option value="2" <?php if($stage_selected=='2'){echo 'selected';} ?>>Completed</option>
                                    </select>
                                    </div>



										<?php
									//$opts = 'placeholder="Job Title/Translator First Name or Last Name/Proposal/Time/Price"';
									//echo form_input('search_string', $search_string_selected,$opts,'style="width: 400px;height: 26px;"');

									 $datai = array(
                                           'name'        => 'search_string',
										   'placeholder' => 'Enter Job Title, Line Numbers',
                                           'value'          =>$search_string_selected,
                                           'style'       => 'width:450px;height:30px;'
                                                 );
								  echo form_input($datai);

								//echo form_label('Order by:', 'order');
								echo form_dropdown('order', $options_category, $order, 'class="span2 order_by_cls"');

							   $data_submit= array('name' => 'mysubmit', 'class' => 'btn btn-primary btn-sm', 'value' => 'Go');

								$options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
					echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1 order_by_cls"');
								echo '&nbsp;&nbsp;'.form_submit($data_submit).'&nbsp;&nbsp;';
                                echo form_close();

                                   ?>
       <a href="<?php base_url().'dashboard/index/'; ?>" class="btn btn-info btn-reser btn-sm" >Reset</a>
                                    <div class="clearfix">
                                        <div class="pull-right tableTools-container"></div>
                                    </div>
                                 <?php
									if ($count_workingjob!='0') {
                                        foreach ($workingjob as $key => $val) {
        									$translator_id=$val['trans_id'];
        									$sql="select * from `translator` where `id`='$translator_id'";
        									$query=mysql_query($sql);
        									$fetch=mysql_fetch_array($query);
        									$trans_name=$fetch['first_name'].'&nbsp;'.$fetch['last_name'];

        									$job_id=$val['job_id'];
        									$jobsql="select * from jobpost j where j.id='$job_id'";
        									$jobquery=mysql_query($jobsql);
        									$jobfetch=mysql_fetch_array($jobquery);
        									$job_title=$jobfetch['name'];
        									$job_alias=$jobfetch['alias'];

                                            $sql = "SELECT lineNumberCode FROM jobpost WHERE id = {$job_id}";
                                            $line_number_code = '';
                                            $query1 = $this->db->query($sql);
                                            if ($query1->num_rows()) {
                                                $row = $query1->row();
                                                if ($row->lineNumberCode != '') {
                                                    $line_number_code = "&nbsp;/&nbsp;".$row->lineNumberCode;
                                                } else {
                                                    $line_number_code = '';
                                                }

                                            }

                                            // echo '<pre>'; print_r($val); echo '</pre>';
                                            ?>
                                        <tr>
                                            <td> <a href="<?php echo base_url(); ?>admin_jobpost/viewsummary/<?php echo $val['job_id']; ?>" target="_blank" ><?php echo $job_title; ?><?php echo $line_number_code ?></a> </td>
                                            <td><a href="<?php echo ($admin_type!= '' && in_array($admin_type,array(4)) == false)?base_url().'admin_translators/edittranslator/'.$val['trans_id']:'javascript:void(0);'; ?>" <?php echo ($admin_type!= '' && in_array($admin_type,array(4)) ==false)?'target="_blank"':''; ?> ><?php echo $trans_name; ?></a></td>
                                            <td>
        										<?php
        										if (strlen($val['proposal'])>75) {
                                                    echo substr($val['proposal'],0,75).'...';
                                                } else {
                                                    echo $val['proposal'];
                                                }
                                                ?>
                                            </td>
                                            <?php
                                                $time=$val['time_need'];
                                                $time= $time/1440;
        								    ?>
                                            <td><?php echo  $time; ?>&nbsp;Day(s)
                                            <td>
                                                <a class="btn btn-info" href="<?php echo base_url(); ?>chat-box/?bid_id=<?php echo $val['bidjob_id'];?>&job_id=<?php echo $val['job_id'] ?>&trans_id=<?php echo $val['trans_id'];  ?>&type=admin&ciadminId=<?php echo $adminID; ?>" target="_blank">
                                                &nbsp;&nbsp;<i class="fa fa-envelope"></i>&nbsp;Chat &nbsp;&nbsp;&nbsp;&nbsp;
                                                </a><br />
                                                <?php if ($val['is_done'] == 1 or $jobfetch['jobDone'] == 1) { ?>
                                                	<span style ="color: GREEN">Check if Really Done</span>
                                                <?php } ?>
                                            </td>
                                            <td>$<?php echo $val['bid_price']; ?></td>
                                            <td> <?php echo date('m-d-Y',strtotime($val['award_date'])); ?> </td>
                                            <td>
                                                <?php // echo "**DEBUG** done: {$val['is_done']}, rated; {$val['is_rated']}, proofread:{$jobfetch['proofread_required']}<br/>" ?>

                                                <?php if ($val['is_done'] and !$val['is_rated'] and !$jobfetch['proofread_required']) { ?>
                                                <a href="<?php echo base_url(); ?>chat-box/?bid_id=<?php echo $val['bidjob_id'];?>&job_id=<?php echo $val['job_id'] ?>&trans_id=<?php echo $val['trans_id'];  ?>&type=admin&ciadminId=<?php echo $adminID; ?>&show=modal" target="_blank" class="btn btn-danger ">Working </a>

                                                <?php } elseif (!$val['is_done'] and !$val['is_rated'] and !$jobfetch['proofread_required']) { ?>
                                                <a href="<?php echo base_url(); ?>chat-box/?bid_id=<?php echo $val['bidjob_id'];?>&job_id=<?php echo $val['job_id'] ?>&trans_id=<?php echo $val['trans_id'];  ?>&type=admin&ciadminId=<?php echo $adminID; ?>&show=modal" target="_blank" class="btn btn-danger ">Working </a>

                                                <?php } else { ?>
                                                <button type="button" class="btn btn-danger toggle-admin-complete" data-id="<?php echo $val['bidjob_id'] ?>" data-jobid="<?php echo $val['job_id'] ?>" aria-haspopup="true" aria-expanded="false">Working </button>
                                                <!-- <button onclick="confir(<?php echo $val['id']; ?>,<?php echo $val['job_id'];?>)" type="button" class="btn btn-danger " aria-haspopup="true" aria-expanded="false">Working </button> -->
                                                <?php }?>


                                                <!-- <a href="javascript:void(0)"> -->
                                                <?php // if($val['stage']!='2' and ($val['is_done'] == 1 or $jobfetch['jobDone'] == 1)) { ?>
                                                <!-- <a href="<?php echo base_url(); ?>chat-box/?bid_id=<?php echo $val['id'];?>&job_id=<?php echo $val['job_id'] ?>&trans_id=<?php echo $val['trans_id'];  ?>&type=admin&ciadminId=<?php echo $adminID; ?>&show=modal" target="_blank" class="btn btn-danger ">Working</a> -->
                                                <!-- <button onclick="confir(<?php echo $val['id']; ?>,<?php echo $val['job_id'];?>)" type="button" class="btn btn-danger " aria-haspopup="true" aria-expanded="false">Working</button>  -->
                                                <?php // } else { ?>
                                                <!-- <button onclick="confir(<?php echo $val['id']; ?>,<?php echo $val['job_id'];?>)" type="button" class="btn btn-danger " aria-haspopup="true" aria-expanded="false">Working</button> -->
                                                <!-- <?php // } ?> -->
                                                </a>
                                            </td>
                                            <td> <a class="btn btn-success" href="<?php echo base_url(); ?>admin_awardjob/viewawardjob/<?php echo $val['id']; ?>"> View </a> </td>
                                        </tr>
                                <?php
										}
									} else {
                                            ?>
                                            <tr><td colspan="9" align="center">No Working Jobs Found!</td></tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>

    <?php
    if ($this->pagination->create_links()) {
        echo '<div class="pagination">'.$this->pagination->create_links().'</div>';
    }
    ?>

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
                            <button class="btn btn-info btn-sm" onclick="goBack()">Go Back</button>
						</div>
                   <!-- /.row -->
					</div><!-- /.page-content -->
		<!---->		</div>
			</div>


		</div>

        <div id="dialog-admin-confirm" title="Verify" style="display:none;">
          <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Are you sure the translator has successfully completed the translation?</p>
        </div>

<script type="text/javascript">
// function confir(id,job_id) {
//     con=confirm("Are you sure the translator have successfully completed the translation?");
//     if (con!=true) {
//         return false;
//     } else {
//         window.location.href="<?php echo base_url(); ?>dashboard/workcomplete/"+id+"/"+job_id;
//     }
// }
</script>
<script>
function goBack() {
    window.history.back();
}
</script>

<?php $this->load->view('admin/includes/vwFooter');
?>
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/uploadfilemulti.css" />
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/jquery-ui-1.12.1.min.css" />
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/select2.css" />

<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-1.8.2.min.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-ui.custom.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $(document).on('click', '.toggle-admin-complete', function () {
        var id = $(this).data('id');
        var job_id = $(this).data('jobid');

        $('#dialog-admin-confirm').dialog({
            resizable: false,
            height: "auto",
            width: 500,
            modal: false,
            buttons: {
                'Yes': function () {
                    $(this).dialog('close');
                    window.location.href="<?php echo base_url(); ?>dashboard/workcomplete/"+id+"/"+job_id;
                },
                'No': function () {
                    $(this).dialog('close');
                }
            }
        });
    });
});
</script>
