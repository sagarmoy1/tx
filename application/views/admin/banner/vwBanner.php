<?php
//error_reporting(0);
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
							<li class="active">Banner</li>
						</ul><!-- /.breadcrumb -->

						<!-- #section:basics/content.searchbox -->
						<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
							</form>
						</div><!-- /.nav-search -->

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
							Banner
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Edit Banner 
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
                         <?php if (validation_errors()!="") { ?>
                         <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <p> <?php echo validation_errors(); ?> </p>
                        </div>
                    <?php } ?>
                     <?php if (isset($message_success) && $message_success!="") { ?>
                         <div class="alert alert-block alert-success">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <p> <?php echo $message_success; ?> </p>
                        </div>
                    <?php } ?>

                     <?php if (isset($message_error) && $message_error!="") { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <p> <?php echo $message_error; ?> </p>
                        </div>
                    <?php } ?>
                    
                    
    <div class="col-xs-12">
    
    <?php
        //print_r($results);
        //die();
    ?>
        <?php 
		$attributes = array('class' => 'form-changeprofilepicture', 'id'=>'user-changeprofilepicture'); 
		echo form_open_multipart('admin/editbanner/'.$this->uri->segment(3).'', $attributes); 
		?>
            
            
            <div class="form-group" style="overflow:hidden;">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Profile Image(jpeg,jpg,png,gif)(1349 X 500) : </label>
                <div class="col-sm-5">
                	<input name="images" id="banner"  type="file" class="form-control col-xs-10 col-sm-5" value="" />
                </div>
            </div>
            
            <?php //if(count($results > 0)) { ?>
            
            
            <?php //echo $results[0]['images']; ?>
				<?php //if($results[0]['images']!="") { ?>
                <?php if($results[0]['images']!="" && file_exists("./uploads/banner/normal/".$results[0]['images'])) { ?>
                <div class="form-group" style="overflow:hidden;">
                    <div class="col-md-offset-3 col-md-8" style="overflow:hidden;">
                        <img src="<?php echo base_url(); ?>uploads/banner/normal/<?php echo $results[0]['images']; ?>" class="img-responsive" style="max-height:300px; max-width:300px;"  />
                    </div>
                <input name="preimage" id="preimage"  type="hidden" class="form-control col-xs-10 col-sm-5" value="<?php echo $results[0]['images']; ?>" />    
                    </div>
                    
                <?php } ?>
            <?php //} ?>
            
            
             <div class="form-group" style="overflow:hidden;">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Status </label>
                <div class="col-sm-5">
                	 <select name="status" id="status" class="form-control col-xs-10 col-sm-5 validate[required]">
              	<option value=""> Select Status </option>
                <option value="1" <?php if($results[0]['status']==1){echo "selected";} ?> >Active</option>
                <option value="0" <?php if($results[0]['status']==0){echo "selected";} ?>>Inactive</option>>
                </select>
                </div>
            </div>
            
            
            <div class="col-md-offset-3 col-md-8" style="padding-top:30px;">
                    <button class="btn btn-info" type="submit">
                        <i class="ace-icon fa fa-check bigger-110"></i>
                        Submit
                    </button>

                    &nbsp; &nbsp; &nbsp;
                    <button class="btn" type="reset">
                        <i class="ace-icon fa fa-undo bigger-110"></i>
                        Reset
                    </button>
                </div>

            <?php echo form_close(); ?>
            
        

								<!-- PAGE CONTENT ENDS -->
							<!-- /.col -->
						</div>
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->
        
        <!-- inline scripts related to this page -->
      
<?php
$this->load->view('admin/includes/vwFooter');
?>
