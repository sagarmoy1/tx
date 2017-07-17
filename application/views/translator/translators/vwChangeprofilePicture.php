<?php
error_reporting(0);
$this->load->view('vwHeader');
?>
<script>
		$(document).ready(function(){	
			$("#images").change(function (){
				var filename = $('#images').val();
				var filename1 = filename.split('.');
				var filename2 = filename1.pop();
				var ext = filename2.toLowerCase();				
				if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
					alert('Invalid file format!. Please select jpg, png or gif.');
					$('#images').val('');
				}
				
			});	
	      });
    
 </script>
<div id="content">
  <div id="title">
    <h1 class="inner title-2">My Profile
    <small>
        <i class="ace-icon fa fa-angle-double-right"></i>
        Change Profile Picture 
    </small>
      <ul class="breadcrumb-inner">
        <li> <a href="<?php echo base_url()?>"><i class="ace-icon fa fa-home home-icon"></i>Home</a></li>
        <li> <a href="<?php echo base_url()?>translator/changeprofilepicture">Change Profile Picture</a></li>
      </ul>
    </h1>
  </div>
  <div class="inner"> 
    
    <!-- Content Inner -->
    <div class="content-inner"> 
      
      <!-- Content Center -->
         <div class="content-center">
         <div class="block field-container odd box-1 hide">  
       <!-- <div id="contacts" class="block post-box box-1 contact-address" style="
    width: 80%;
">-->
          <div class="block-content">
            <div class="block background">
          <h2 class="title-1">Change Profile Picture</h2>
          <div class = "block-content">
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
                    
                    <?php if ($this->session->flashdata('flsh_success')) { ?>
                         <div class="alert alert-block alert-success">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <p> <?php  echo $this->session->flashdata('flsh_success'); ?> </p>
                        </div>
                    <?php } ?>
                    
       <?php 
		$attributes = array('class' => 'form-changeprofilepicture', 'id'=>'user-changeprofilepicture'); 
		echo form_open_multipart('translator/changeprofilepicture', $attributes); 
		?>
              <div id = "about">
      <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Profile Image (200px*150px for best fit)<span class='error-label'>*</span></label>
                <input name="images" id="images"  type="file" class="validate[required]" value="" />
                <?php if($results[0]['images']!="" && file_exists("./uploads/translator/profile/".$results[0]['images'])) { ?>
                <!--<div class="textfield2" style="max-height:165px; max-width:150px;">-->
                <img style="padding-top:10px;" src="<?php echo base_url(); ?>uploads/translator/profile/<?php echo $results[0]['images']; ?>" class="img-responsive"  width="200px"/>
                <!--</div>-->
                <input type="hidden" name="preimage" id="preimage" size="20" class="textfield2  validate[required]" value="<?php echo $results[0]['images']; ?>" />
                <?php }  ?>
       			 </div>
             
              <div id = "send">
                <button type="submit" class="btn btn-info" style="
margin-top: 7px;">Submit</button>
                <button class="btn btn-info" type="reset" style="
margin-top: 7px;">
                        <i class="ace-icon fa fa-undo bigger-110"></i>
                        Reset
                    </button>
              </div>
           <?php echo form_close(); ?>
          </div>
        </div>
            
          </div>
        </div>
      </div>
      <!-- /Content Center --> 
      
      <!-- Content Right -->
       <div class="content-right">
 		<?php
				$this->load->view('translator/includes/vwSidebar-left');
			?>
            </div>
      <!-- /Content Right -->
      
      <div class="clear"></div>
      <!-- Clear Line --> 
      
    </div>
    <!-- /Content Inner --> 
    
  </div>
</div>





		<!-- /section:basics/navbar.layout -->
		
			
			

			<!-- /section:basics/sidebar -->
			<!-- /.main-container -->
        
        <!-- inline scripts related to this page -->
<?php
$this->load->view('vwFooter');
?>
<?php
$this->load->view('vwFooterLower');
?>