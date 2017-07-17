<?php
$this->load->view('admin/includes/vwHeader');
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

			<!-- /section:basics/sidebar -->
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
								<a href="#">Job</a>
							</li>
							<li class="active">Job Messages</li>
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
								Job Messages
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									View Job Messages List
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
                                
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
                                
								<div>
                                    <div class="clearfix">
                                        <div class="pull-right tableTools-container"></div>
                                    </div>
                                    <div class="table-header">
                                    <?php 
									$job_id= $this->uri->segment(2);
									
									$sql4="update `message` set `read`='1' where `job_id`='$job_id' and `type`='1'";
									$query4=$this->db->query($sql4);
									
									$sql3="select `name` from `jobpost` where `id`='$job_id'";
									$query3=$this->db->query($sql3);
									$fetch3=$query3->row();
									$Job_name=$fetch3->name;							
									?>
                                    Message for Job Name: <b><?php echo $Job_name; ?></b>
                                    </div>
                                    <div class="clearfix">
                                        <div class="pull-right tableTools-container"></div>
                                    </div>
                                    <div class="clearfix">
                                        <div class="pull-right tableTools-container"></div>
                                    </div>
                                 
                                         <style type="text/css">
											.order_by_cls {
												display:none;	
											}
											.nonvisible
											{
											display:none;	
											}
										</style>
                                    <?php
									$attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');
									//save the columns names in a array that we will use as filter         
									$options_category = array();
									//echo '<pre>'; print_r($category);
									foreach ($jobmessages as $array) {
									foreach ($array as $key => $value) {
										$options_category[$key] = $key;
									  }
									  break;
									}
							        $job_id= $this->uri->segment(2);
								    echo form_open('admin/jobmessages/'.$job_id.'/', $attributes);																
									//echo form_label('Search:', 'search_string');
									echo form_input('search_string', $search_string_selected, 'style="width: 170px;
									height: 26px; display:none;"');								
								    //echo form_label('Order by:', 'order');
								echo form_dropdown('order', $options_category, $order, 'class="span2 order_by_cls"');									
			          $data_submit= array('name' => 'mysubmit', 'class' => 'btn btn-primary btn-sm nonvisible', 'value' => 'Go');
								$options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
		               echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1 order_by_cls"');                             	echo form_submit($data_submit);							
                                echo form_close();
                                
								
								
								
								            //echo '<pre>';print_r($jobmessages);die;
                                            foreach($jobmessages as $key => $val){											
                                            ?>                                            
											<div class="dialogs">
														<div class="itemdiv dialogdiv">
															<div class="user">
															Admin
															</div>

															<div class="body">
																<div class="time">
                                                                   Date:<span class="green">
													<?php  echo date('m-d-Y',strtotime($val['created']));?>
                                                                    </span>
																	<i class="ace-icon fa fa-clock-o"></i>
													                
                                                                    <span class="green">
													<?php  echo date('h:i:sa',strtotime($val['created']));?>
                                                                    </span>
																</div>

																<div class="name">
																<?php  echo $val['text'];?>
																</div>
																<div class="text">
                                                                To:
																<?php  $totrans_id=$val['trans_id'];
                                                                $sql2="select * from `translator` where `id`='$totrans_id'";
                                                                $query2=$this->db->query($sql2);
                                                                $fetch2=$query2->row();
                                                                $toname=$fetch2->first_name.'&nbsp;'.$fetch2->last_name;
                                                                echo $toname;
                                                                ?>
                                                                </div>
																<div class="name">
                                                                <?php if($val['file']!="" && file_exists("./uploads/message/".$val['file'])) { ?>
					<a target="_blank" href="<?php echo base_url().'uploads/message/'.$val['file'] ; ?>" class="btn btn-minier btn-info">
																<i class="icon-only ace-icon fa fa-download"></i> File
																</a><?php } ?>
																</div>
															</div>
														</div>											
									</div>
                                            <?php $id=$val['id'];
                                            $sql="select * from `message` where `reply_id`='$id' and `type`=1";
											$query=$this->db->query($sql);
											if($query->num_rows()>=1)
											{
											$fetch=$query->result_array();
											foreach($fetch as $row)
											{
											?>	
											<div class="dialogs" style="padding-left:50px;">
														<div class="itemdiv dialogdiv">
															<div class="user">
															<?php
                                                            $trans_id=$row['trans_id'];
                                                            $sql1="select * from `translator` where `id`='$trans_id'";
                                                            $query1=$this->db->query($sql1);
                                                            $fetch1=$query1->row();
                                                            $name=$fetch1->first_name.'&nbsp;'.$fetch1->last_name;
                                                            echo $name;
                                                            ?>
															</div>
                                                   <div class="clearfix">
                                                   <div class="pull-right tableTools-container"></div>
                                                   </div>
                                                   <div class="clearfix">
                                                   <div class="pull-right tableTools-container"></div>
                                                   </div>
                                                   <div class="clearfix">
                                                   <div class="pull-right tableTools-container"></div>
                                                   </div>
															<div class="body" style="height:75px;">
																<div class="time">
                                                                   Date:<span class="green">
													<?php  echo date('m-d-Y',strtotime($row['modified']));?>
                                                                    </span>
																	<i class="ace-icon fa fa-clock-o"></i>
													                
                                                                    <span class="green">
																				
											        <?php	echo date('h:i:sa',strtotime($row['modified']));?>
											
                                                                    </span>
																</div>

																<div class="name">
																<?php  echo $row['text'];?>
																</div>
																<div class="text">
                                                                To:Admin																
                                                                </div>
																<div class="name">
                                                                <?php if($row['file']!="" && file_exists("./uploads/reply/".$row['file'])) { ?>
					<a style="float:right;" target="_blank" href="<?php echo base_url().'uploads/reply/'.$row['file'] ; ?>" class="btn btn-minier btn-info">
																<i class="icon-only ace-icon fa fa-download"></i> File
																</a><?php } ?>
																</div>
															</div>
														</div>											
									                 </div>											
											<?php	
											}
											}?>
                                    
                                    
                                    
                                    
                                          
											<?php
											 }                                        
                                            ?> 
                                     
                                    <?php echo '<div class="pagination">'.$this->pagination->create_links().'</div>'; ?>
                                    
                                    
                                    
                                </div>
                                 <button class="btn btn-info btn-sm" onclick="goBack()">Go Back</button>
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->


<script type="text/javascript">
function alert(id)
{
    del =confirm("Are you sure to delete permanently?");
    if(del!=true)
    {
        return false;
    }
	else
	{
	window.location.href="<?php echo base_url(); ?>admin_jobpost/delete/"+id;
	}
}
</script>
<script type="text/javascript">
function reload()
{
window.location.href="<?php echo base_url().'admin/joblist/'?>";	
}
</script>
<script>
function goBack() {
    window.history.back();
}
</script>
<?php
$this->load->view('admin/includes/vwFooter');
?>
