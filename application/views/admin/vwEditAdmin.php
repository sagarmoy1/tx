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
                <a href="#">Admin</a>
              </li>
              <li class="active">Edit Admin</li>
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
                Edit Admin            
              </h1>
            </div><!-- /.page-header -->
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
                        
            <div class="row">
              
                            
                            <div class="col-xs-12">
                            
        
                                    <?php                 
      $attributes = array('class' => 'form-horizontal', 'id'=>'admin-edit', 'enctype' => 'multipart/form-data'); 
      echo form_open('admin/editprofile/'.$fetch->id,$attributes);                    
                  ?>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">First Name: </label>
                <div class="col-sm-9">
                    <input name="first_name" id="title" class="col-xs-10 col-sm-5 validate[required]"  type="text" value="<?php echo $fetch->first_name; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Last Name:</label>
                <div class="col-sm-9">
                    <input name="last_name" id="tag_line" class="col-xs-10 col-sm-5 validate[required]"  type="text" value="<?php echo $fetch->last_name; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Email:</label>
                <div class="col-sm-9">
                    <input name="email_address" id="email" class="col-xs-10 col-sm-5 validate[required],custom[email]"  type="text" value="<?php echo $fetch->email_addres; ?>" readonly>
                </div>
            </div>
             
             <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Username</label>
                <div class="col-sm-9">
                    <input name="user_name" id="email" class="col-xs-10 col-sm-5"  type="text" value="<?php echo $fetch->user_name; ?>" readonly>
                </div>
            </div>
        
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Phone No</label>
                <div class="col-sm-9">
                    <input name="phone_no" id="email" class="col-xs-10 col-sm-5 validate[required]"  type="text" value="<?php echo $fetch->phone; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Alternative Email</label>
                <div class="col-sm-9">
                    <input name="alter_email" id="email" class="col-xs-10 col-sm-5 validate[required],custom[email]"  type="text" value="<?php echo $fetch->alter_email; ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">New Password</label>
                <div class="col-sm-9">
                    <input name="alter_password" id="new_pw" class="col-xs-10 col-sm-5"  type="password">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Confirm New Password</label>
                <div class="col-sm-9">
                    <input name="alter_passwordConfirm" id="confirm_pw" class="col-xs-10 col-sm-5"  type="password">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" > Select Status: </label>
                <div class="col-sm-9">                   
                    <?php 
          $Admin_id=$fetch->id;                 
          $status= $fetch->status;        
            if($Admin_id==1)
           { ?>
          
            <select name="status" class="col-xs-10 col-sm-5" >
              <option value="1" <?php if($status==1){echo "selected";} ?> >Active</option>
            </select>
        
           <?php 
             }           
            else
             { 
            ?>
                     <select name="status" class="col-xs-10 col-sm-5" >
              <option value="1" <?php if($status==1){echo "selected";} ?> >Active</option>
              <option value="0" <?php if($status==0){echo "selected";} ?>>Inactive</option>
            </select>
           <?php  } ?>
            

                </div>
            </div>
         
            
            
                  <div class="clearfix"></div>        
            
                                            
            <div class="col-md-offset-3 col-md-9">
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
              </div>
                            
                            
            </div><!-- /.row -->
          </div><!-- /.page-content -->
        </div>
      </div><!-- /.main-content -->
    </div><!-- /.main-container -->
<!-- page specific plugin ck editor scripts -->
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/css/samples.css" />
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css" />
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/ckeditor.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/js/sample.js"></script>
<script>
    initSample();
</script>
<script type="text/javascript">
      CKEDITOR.replace( 'editor1' );
      CKEDITOR.add            
   </script>

   <script type="text/javascript">
      CKEDITOR.replace( 'editor2' );
      CKEDITOR.add            
   </script> 
   <script type="text/javascript">
      CKEDITOR.replace( 'editor3' );
      CKEDITOR.add            
   </script>
   <script type="text/javascript">
      CKEDITOR.replace( 'editor4' );
      CKEDITOR.add            
   </script>
   <script type="text/javascript">
      CKEDITOR.replace( 'editor5' );
      CKEDITOR.add            
   </script>
   <script type="text/javascript">
      CKEDITOR.replace( 'editor6' );
      CKEDITOR.add            
   </script>
   <script type="text/javascript">
      CKEDITOR.replace( 'editor7' );
      CKEDITOR.add            
   </script>
   <script type="text/javascript">
      CKEDITOR.replace( 'editor8' );
      CKEDITOR.add            
   </script>
<script type="text/javascript">
function doconfirm()
{
    del =confirm("Are you sure to delete permanently?");
    if(del!=true)
    {
        return false;
    }
}
</script>


<?php
$this->load->view('admin/includes/vwFooter');
?>
