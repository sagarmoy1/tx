<?php $this->load->view('vwHeader'); ?>

<div id="content">

  <div id="title">
    <h1 class="inner title-2">Paypal Info
      <ul class="breadcrumb-inner">
        <li> <a href="<?php echo base_url()?>"><i class="ace-icon fa fa-home home-icon"></i>Home</a></li>
        <li> <a href="<?php echo base_url()?>translator/changeprofile">Change Profile</a></li>
      </ul>
    </h1>
  </div>

  <div class="inner"> 
    <!-- Content Inner -->
    <div class="content-inner"> 
      <!-- Content Center -->
      <div class="content-center">
    
        <div class="block field-container odd box-1 hide">  
          <!--<div id="contacts" class="block post-box box-1 contact-address" style="width:80%">-->
          <div class="block-content">
            
            <div class="block background">
              <h2 class="title-1">Paypal Info</h2>
              <div class = "block-content">
              
                <?php if (validation_errors()!="") { ?>
  			            <div class="alert alert-danger">
                      <button type="button" class="close" data-dismiss="alert">
                        <i class="ace-icon fa fa-times"></i>
                      </button>
                      <p><?php echo validation_errors(); ?> </p>
                    </div>
  		          <?php } ?>
          
                <?php if (isset($message_error) && $message_error!="") { ?>
          			<div class="alert alert-danger">
                  <button type="button" class="close" data-dismiss="alert">
                    <i class="ace-icon fa fa-times"></i>
                  </button>
                  <p><?php echo $message_error; ?> </p>
                </div>
          		  <?php } ?>
                  
                <?php if (isset($message_success) && $message_success!="") { ?>
          			<div class="alert alert-block alert-success">
          			  <button type="button" class="close" data-dismiss="alert">
          				  <i class="ace-icon fa fa-times"></i>
          				</button>
                  <p><?php echo $message_success; ?> </p>
                </div>
          		  <?php } ?>
        
                <?php if($this->session->flashdata('message_success')){ ?>
                <div class="alert alert-block alert-success">
          			    <button type="button" class="close" data-dismiss="alert">
          					  <i class="ace-icon fa fa-times"></i>
          				  </button>
                    <p><?php echo $this->session->flashdata('message_success'); ?> </p>
                </div>
                <?php } ?>
                  
                <?php if($this->session->flashdata('message_error')){ ?>
                <div class="alert alert-block alert-success">
          				<button type="button" class="close" data-dismiss="alert">
          					<i class="ace-icon fa fa-times"></i>
          				</button>
                  <p><?php echo $this->session->flashdata('message_error'); ?> </p>
                </div>
                <?php } ?>

                <?php
                  $attributes = array('class' => 'form-registration', 'id'=>'changeprofile'); 
        		      echo form_open('translator/paypal', $attributes); 
        		    ?>
             
                <div id = "about">
                  <?php /*?><input title="Your Title" type="text" name="title" class="textfield2" placeholder="Title" onclick="this.value='';" onfocus="$(this).addClass('active');" onblur="$(this).removeClass('active');"/>
                  <input title="Your alias" type="text" name="alias" class="textfield2" placeholder="Alias" onclick="this.value='';" onfocus="$(this).addClass('active');" onblur="$(this).removeClass('active');"/><?php */?>

                  <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Paypal Id(Email)*:</label>
                  <?php 
                    $sql="select * from translator where id='".$this->session->userdata('translator_id')."'"; 
                    // echo $sql;die; 
                	  $query=$this->db->query($sql);
                		$val=$query->row();
                		$paypal_id=$val->paypal_id;
                		$paypal_id;
                  ?>	

                  <?php if($paypal_id != ""){ ?>
                    <input title="Your Paypal Id" type="email" name="paypal_id" class="form-control text-input  validate[required,custom[email]" placeholder="Paypal Id" onfocus="$(this).addClass('active');" onblur="$(this).removeClass('active');" value="<?php echo $paypal_id;?>" />
                  <?php }else{ ?>
                    <input title="Your Paypal Id" type="email" name="paypal_id" class="form-control text-input  validate[required,custom[email]" placeholder="Paypal Id" onfocus="$(this).addClass('active');" onblur="$(this).removeClass('active');" value="" />
                  <?php } ?>
                </div>

                  <div id = "send">
                    <button class="btn btn-info" type="submit">
                      <i class="ace-icon fa fa-check bigger-110"></i>
                      Submit
                    </button>
                    <?php /*
                    <button class="btn btn-info" type="reset" >
                      <i class="ace-icon fa fa-undo bigger-110"></i>
                      Reset
                    </button>
                    */ ?>
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


	
        <!-- inline scripts related to this page -->
		
   <!-- page specific plugin ck editor scripts -->
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/css/samples.css" />
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css" />
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/ckeditor.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/js/sample.js"></script>
<script>
    initSample();
</script>


      
<?php
$this->load->view('vwFooter');
?>
<?php
$this->load->view('vwFooterLower');
?>
