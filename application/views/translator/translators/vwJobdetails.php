<?php
$this->load->view('vwHeader');
?>
 <link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/uploadfilemulti.css" />
<!--<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-1.8.0.min.js"></script>-->
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery.fileuploadmulti.min.js"></script>



<?php if($this->session->flashdata('success_message'))
        { ?>
<script>
jQuery(document).ready(function(){
	alert("<?php echo $this->session->flashdata('success_message'); ?>");
});
</script>
<?php } ?>
<style>
.invisible
{
display:block	!important
}
</style>
<div id="content">
  <div id="title">
    <h1 class="inner title-2">Job Details
          <ul class="breadcrumb-inner">
        <li> <a href="<?php echo base_url()?>">Home</a></li>
     <?php    $alias = $this->uri->segment(2); ?>
        <li> <a href="<?php echo base_url()?>job/<?php echo $alias;?>">Job Details</a></li>
      </ul>
    </h1>
  </div>
  <div class="inner">
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
    <!-- Content Inner -->
    <div class="content-inner ">




      <!-- Content Center -->
     <div id="content">
      <!-- Content Left -->

      <!-- /Content Left -->

      <!-- Content Center -->

        <div class="heading-l">
          <h2> Job Description </h2>
        </div> <?php //print_r($results);

		$string=rtrim($results[0]['file'], " ");
		$view=explode("##",$string);
		array_pop($view);
		//print_r($view);
		$num_of_file= count($view);
		?>
        <div class=" border box-1">
          <div id="job-content-field">
            <div class="field-container single no_border">
              <div class="header-fields">

                <div class="title-company">
                  <div class="title"><strong>Job Name:</strong><a href="#"> <?php echo $results[0]['name']?></a></div>

                </div>
              </div>
              <div class="body-field">

                <div class="teaser">
                  <p><strong>Description:</strong><?php echo $results[0]['description']?></p>
                </div>

              </div>
              <div class="block-fields">
             <div class="block ">
                  <div class="block-content">
                   <?php $sql="select * from languages where id='" . $results[0]['language_from'] . "'  ORDER BY `name`";
				  $val = $this->db->query($sql);
				  $fetch= $val->row();
				  //echo $fetch1->name; ?>
                    <div class = "tag-field">From  <?php echo  $fetch->name;?></div>
                    <?php $sql="select * from languages where id='" . $results[0]['language'] . "'";
				  $val = $this->db->query($sql);
				  $fetch= $val->row();
				// echo
				  ?>
                    <div class = "tag-field">To <?php echo  $fetch->name;?></div>
                    <div class = "tag-field">Job Posted: <?php echo date("jS F, Y", strtotime($results[0]['created']));?></div>
                 <div style="clear:both"></div>
                    <div><strong>Files:</strong></div>
                  <?php
					 for ($i = 0; $i < $num_of_file; $i++){
						 if($view[$i]!="" && file_exists("./uploads/jobpost/".$view[$i])) {
						 $vie = strstr($view[$i], '/');
							 $str = ltrim($vie, '/');
							 if($str == ''){
								 $str = $view[$i];
								 }
					?>

                    <div class = "tag-field" style="clear:both;"><a href="<?php echo base_url(); ?>uploads/jobpost/<?php echo $view[$i]; ?>" class="tag-field" target="_blank"><?php echo $str; ?></a></div>
                    <?php }} ?>
                  </div>
                  <!-- Cleaner -->
                  <div class="clear"></div>


                  <!-- /Cleaner -->
                </div>
              <div class="block ">
                <div class="block-content invisible">
                    <?php
					 for ($i = 0; $i < $num_of_file; $i++){
						 if($view[$i]!="" && file_exists("./uploads/jobpost/".$view[$i])) {

				?>
                    <div class = "tag-field"><a href="<?php echo base_url(); ?>uploads/jobpost/<?php echo $view[$i]; ?>" class="tag-field" target="_blank">Download</a></div>
                    <?php } }echo "<br />"; ?>

                   </div>
                   </div>
              </div>
              <div class="block-fields">



              <div class="block ">
                <div class="block-content">


                   </div>
                   </div>

              </div>
              <!--<input type="reset" class="btn gray next-btn" value="Login to Proposal">-->

            </div>
          </div>
        </div>

        <div class="clear"></div>

        <div class="heading-l">
          <h2>Send a proposal </h2>

        </div>
         <?php if(!$this->session->userdata('is_translator')){
	  			$job_alias=$this->uri->segment(2);
				/*$this->session->unset_userdata('referrer_url');
				$this->session->unset_userdata('last_url');

				$data = array('last_url' =>  base_url().'job/'.$job_alias);
				$this->session->set_userdata($data);*/

				?>
                <a href="<?php echo base_url()?>translator/login" class="btn gray next-btn">Login To Bid </a>

         <?php } else{
			 $sql1 = "SELECT * from bidjob WHERE trans_id = '" . $this->session->userdata('translator_id') . "' AND job_id = '".$results[0]['id']."'";
			$val1 = $this->db->query($sql1);
			if($val1->num_rows()=='1'){
			$fetch1= $val1->row();
		 /*	if($fetch1->awarded==1){?>
        <div class=" block field-container odd  hide">
        <div class="block background">
        <div class = "block-content">
  <div class="proposal_title">Proposal</div>

      <div class="table-responsive">
        <table summary="This table shows how to create responsive tables using Bootstrap's default functionality" class="table table-bordered table-hover">

          <thead>
            <tr>
              <th>Awarded date</th>
              <th>Proposal</th>

            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo date("jS F ,Y", strtotime($fetch1->award_date));?></td>
              <td><?php echo $fetch1->proposal ;?></td>

            </tr>
          </tbody>
        </table>
      </div>
      </div>
      </div><!--end of .table-responsive-->
    </div>
    <?php	}*/

			//else{
				?>



    <div class=" block field-container odd  hide">
    <div class="block background">
               <div class = "block-content">
               <h2 class="title-1">Edit Your Bid</h2>
               <div class = "block-content">
    <?php
				$attributes = array('class' => 'form-changeprofilepicture', 'id'=>'user-changeprofilepicture');
				echo form_open_multipart('translator/bidjobedit', $attributes);
				?>
            <!--<form name="" id="" action="">-->
                <div class="about">
                <input type="hidden" name="job_id" value="<?php echo $fetch1->job_id ?>"/>
                 <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Expected Turnaround Time *(In days)                   </label>
                    <input id="time_need" name="time_need_day" type="text" class="form-control validate[required,custom[integer]] text-input" placeholder="Time You Need" value="<?php echo ($fetch1->time_need)/1440 ;?>"  >
                     <input id="time_need" name="time_need" type="hidden" class="form-control validate[required,custom[integer]] text-input" placeholder="Time You Need" value="<?php echo $fetch1->time_need ;?>"  >

                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Your Quote for this translation job * ( In US dollars)                   </label>
                    <input id="price" name="price" type="text" class="form-control validate[required,custom[integer]] text-input" placeholder="Price" value="<?php echo $fetch1->price ;?>" >


						<?php

                      //  $string=rtrim($fetch1->file, " ");
                        $view=explode("##",$fetch1->file);
                        array_pop($view);
                        $num_of_file= count($view);
						//echo  $num_of_file;die;
                        ?>


                        <?php  if($fetch1->file!= "") {
                        for ($i = 0; $i < $num_of_file; $i++){
                        if($view[$i]!= "") {
						 $vie = strstr($view[$i], '/');
                		$str = ltrim($vie, '/');
						if($str == ''){
						$str = $view[$i];
						}

                        ?>
                                    <div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Uploaded File :                                        </label>

										<div class="col-sm-9">
                            <a href="<?php echo base_url(); ?>uploads/bidjobpost/<?php echo $view[$i]; ?>" class="btn btn-success" target="_blank"><?php echo $str; ?></a>
                             <a href="javascript:void(0);" class="btn btn-danger" onclick="removealert('<?php echo $fetch1->id; ?>','<?php echo $view[$i]; ?>')">Remove File</a>
										</div>
									</div>
            <input type="hidden" name="prefile" size="20" class="col-xs-10 col-sm-5" value="<?php echo $fetch1->file; ?>" />
			<input type="hidden" name="numberfile" id="numberfile" size="20" class="col-xs-10 col-sm-5" value="<?php echo $num_of_file; ?> " />
                                    <?php }}} ?>



                      <label class="col-sm-3 control-label no-padding-right" for="form-field-1">You should be able to upload up to 5 files                      </label>
                 <!--  <input type="file" name="userfile[]" id="userfile" data-buttonName="btn-primary" multiple="multiple" >-->
                   					<div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3">
                                    <div id="mulitplefileuploader">Upload</div>
									<div id="status"></div>
                                    <input type="hidden" name="totalFile" id="totalFile" value="" class="validate[required]" />
                                    </div>
							    </div>


                      <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Message about your Proposal*                      </label>
                    <textarea id="editor" name="proposal" class="form-control validate[required] text-input" placeholder="Your Proposal" rows="5" ><?php echo $fetch1->proposal ;?></textarea>

                </div>
               <?php if($fetch1->awarded!=1) {?>
                <div id = "send">
                <input id="send_btn" type="submit" value="Submit"></a>
              </div>
              <?php }?>
                            <div id = "send" class="invisible">

                <button class="btn btn-info" type="submit">
            <i class="ace-icon fa fa-check bigger-110"></i>
            Send
           </button>&nbsp; &nbsp;
                <button class="btn btn-info" type="reset" >
                        <i class="ace-icon fa fa-undo bigger-110"></i>
                        Reset
                    </button>
              </div>

            </form>

           </div>
            </div>
            </div>

        </div>
        	<?php	//}

			}else{

			?>
       <div class="block field-container odd  hide">
               <div class="block background">
               <div class = "block-content">
               <h2 class="title-1">Post Your Bid</h2>
               <div class = "block-content">
            <?php
				$attributes = array('class' => 'form-changeprofilepicture', 'id'=>'user-changeprofilepicture');
				echo form_open_multipart('translator/bidjob', $attributes);
				?>
                <div id = "about">
                 <input type="hidden" name="id" value="<?php echo $results[0]['id']?>"/>


                   <label class="col-sm-3 control-label no-padding-right" for="form-field-1">  Expected Turnaround Time *(In Days)                     </label>
                    <input id="time_need_day" name="time_need_day" type="text" class="form-control validate[required,custom[integer]] text-input" placeholder="Time You Need"  >


                    <input id="time_need" name="time_need" type="hidden" class="form-control validate[required,custom[integer]] text-input" placeholder="Time You Need"  >
                     <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Your Quote for this translation job * ( In US dollars)                      </label>
                    <input id="price" name="price" type="text" class="form-control validate[required,custom[integer]] text-input" placeholder="Price" >
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-1">You should be able to upload up to 5 files                      </label>
                   <!--<input type="file"  name="file[]" id="file" data-buttonName="btn-primary" multiple="multiple">
                    <input type="hidden"  name="prefile" id="prefile" data-buttonName="btn-primary">-->

                    			 <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3">
                                    <div id="mulitplefileuploader">Upload</div>
									<div id="status"></div>
                                    <input type="hidden" name="totalFile" id="totalFile" value="" class="validate[required]" />
                                    </div>
							    </div>

                  <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Message about your Proposal*                      </label>
                    <textarea id="editor" name="proposal" class="form-control  text-input" placeholder="Your Proposal" rows="5"></textarea>

                </div>
                <div id = "send">
                <input id="send_btn" type="submit" value="Submit"></a>
              </div>
            </form>
            </div>
            </div>
            </div>
        </div>
        <!--/similar Jobs Block-->

      <!-- /Content Center -->
      <div class="clear"></div>
      <!-- Clear Line -->

    </div><?php }}?>


      </div>


      <div class="clear"></div>


    </div>


  </div>
</div>


    </div>

</div>
</div>
<div class="clearfix"></div>

 <div class="clear"></div>
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/css/samples.css" />
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css" />
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/ckeditor.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/js/sample.js"></script>
<script>
    initSample();
</script>
<script>


$(document).ready(function() {
		$('#time_need_day').change(function() {
		var days = $(this).val();
		//alert(days);
		var hours = days*1440;
		//alert(hours);
		$(time_need).val(hours);

	});
	});

</script>
      <script type="text/javascript">
function removealert(id,file)
{

    del =confirm("Are you sure to delete permanently?");
    if(del!=true)
    {
        return false;
    }
	else
	{
	window.location.href="<?php echo base_url(); ?>translator/removefile/"+id+"/"+file;
	}
}
</script>
<script>
$(document).ready(function()
{ 		var $fileUpload = $("#numberfile").val();
		var file=parseInt($fileUpload);
		var num=5;
		if (file!=0){
         var filecount= num-file;
		}else{
			var filecount=5;
		}


	var settings = {
	dataType: "html",
	url: "<?php echo base_url().'translator/'.'upload';?>",
	method: "POST",
    allowedTypes:"jpg,jpeg,docx,xls,xlsx,ppt,pptx,png,gif,doc,pdf,zip,tar,txt,ai,mp3,wav,csv",
	fileName: "myfile",
	maxFileCount:filecount,
	multiple: false,
	onSuccess:function(files,data,xhr)
	{
		var total=$('#totalFile').val();
		$('#totalFile').val(total+data);
		var total1=$('#totalFile').val();
		var filePath = data;
		var currentId= $(".remove-file-cls").attr("id");
 		 $('#upload-statusbar-'+currentId).find('.remove-file-cls').html("<a href='javascript:void(0);' onclick='return theFunction();' class='test' id='"+filePath+"'>Remove</a>");
	},
    afterUploadAll:function()
    {

    },
	onError: function(files,status,errMsg)
	{
		$("#status").html("<font color='red'>Upload is Failed</font>");
	}
}

$("#mulitplefileuploader").uploadFile(settings);

});
</script>
<script>
$(document).ready(function(){

});
</script>
<script type="text/javascript">
  	function theFunction () {//alert("hello");
	var id = $(".test").attr('id');
	//alert(ID);

		 $.ajax({
					dataType: "html",
					type: "POST",
					data: {id:id},
					cache: false,
					url:  '<?php echo  base_url().'translator/linkdelete';?>',
					success: function (data, textStatus){
						alert(data);


                	}
            });



    exit;
    }
</script>

<?php
$this->load->view('vwFooter');
?>

<?php
$this->load->view('vwFooterLower');

?>
