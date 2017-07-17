<?php
	$this->load->view('admin/includes/vwHeader');
?>

 <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
 <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script src="http://formvalidation.io/vendor/formvalidation/js/formValidation.min.js"></script>
<script src="http://formvalidation.io/vendor/formvalidation/js/framework/bootstrap.min.js"></script>
<link href="http://formvalidation.io/vendor/formvalidation/css/formValidation.min.css" rel="stylesheet">
<script>
	$(document).ready(function() {
	
		$("#add").formValidation({
			framework: 'bootstrap',
            excluded: [':disabled'],
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
			fields: {
					'email': {
						validators: {
							notEmpty: {
                            	message: 'Email is required'
                        	},
                        	emailAddress: {
								message: 'Email Not valid'	
							},
                        	remote: {
		                        url: '<?php echo base_url();?>translator/check',
		                        // Send { username: 'its value', email: 'its value' } to the back-end
		                        data: function(validator, $field, value) {
		                            return {
		                                email: validator.getFieldElements('email').val(),
		                                csrf_name:validator.getFieldElements('csrf_name').val()
		                            };
		                        },
		                        message: 'Email already exist',
		                        type: 'POST'
                    		}
						}
					},
					'first_name': {
						validators: {
							notEmpty: {
                            	message: 'First Name is required'
                        	}
						}
					},
					'last_name': {
						validators: {
							notEmpty: {
                            	message: 'Last Name is required'
                        	}
						}
					}
				}
		})
		.on('success.form.fv', function(e) {
            	add();
            	return false;	
         });
	});
	
	function add() {
		run_waitMe('ios','#add');
		$.ajax({
        	url: "<?php echo base_url()?>translator/add_action",
			type: "POST",
			data: new FormData($('#add')[0]),
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(data)
		    {
	   				alert('Translator Added');
               // console.log(data.lang);
	   				$('#add').waitMe('hide');
	   				window.setTimeout(function(){ document.location.reload(true); }, 2000);
		    },
		  	error: function() 
	    	{
	    	} 	        
	   });
	}
	function getLanguage(id)
    {
        $('#pid_selected').val(id.val());
        $("#bb").show();
        $("#prfrdNo").hide();

        var valu=id.val();
       // alert(valu);
        $.ajax({
            url: "<?php echo base_url()?>translator/translateTo",
            type: "POST",
            data: {'valu':valu},
            success: function($data)
            {
                console.log($data);
               // $('#aa').html($data);
               document.getElementById("bb").innerHTML=$data;
                    $("#aa").hide();
            },
        });

    }
    function getLanguage101(id)
    {
        $('#pid_selected').val(id.val())
        $("#bb").hide();
        $("#aa").show();
        $("#prfrd_no").hide();
        $("#prfrdNo").show();
    }
</script>
<script>
    function insertLanguagePair(){
        var prfread=$('#pid_selected').val();
        //alert(prfread);
        if(prfread==1)
        {
            var rowCount = $('#languagePairSelection tr').length;

            if(rowCount <= 6){
                var tbl = $("#languagePairSelection");
                $("<tr><td><select class = 'form-control selectpicker' name = 'languageFrom[]' data-live-search='true' ><option>Select Language</option><?php foreach ($from as $fr){ ?><option value = '<?php echo $fr->id; ?>'><?php echo $fr->name; ?></option><?php } ?></select></td><td><select class = 'form-control selectpicker' name = 'languageTo2[]' data-live-search='true'><option value ='1'>English</option></select></td><td><button class = 'btn btn-danger delRowBtn' type = 'submit'><i class = 'fa fa-times'></i></button></td></tr>").appendTo(tbl);

            }
        }else{
            var rowCount = $('#languagePairSelection tr').length;

            if(rowCount <= 6){
                var tbl = $("#languagePairSelection");
                $("<tr><td><select class = 'form-control selectpicker' name = 'languageFrom[]' data-live-search='true' ><option>Select Language</option><?php foreach ($from as $fr){ ?><option value = '<?php echo $fr->id; ?>'><?php echo $fr->name; ?></option><?php } ?></select></td><td><select class = 'form-control selectpicker' name = 'languageTo[]' data-live-search='true'><option>Select Language</option><?php foreach ($from as $fr){ ?><option value = '<?php echo $fr->id; ?>'><?php echo $fr->name; ?></option><?php } ?></select></td><td><button class = 'btn btn-danger delRowBtn' type = 'submit'><i class = 'fa fa-times'></i></button></td></tr>").appendTo(tbl);

            }
        }


    }

    $(document.body).delegate(".delRowBtn", "click", function() {
        $(this).closest("tr").remove();
    });
</script>
	<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
<?php
	$this->load->view('admin/includes/vwSidebar-left');
?>
<div class="main-content">
<div class="main-content-inner">
	<div class="breadcrumbs" id="breadcrumbs">
		<ul class="breadcrumb">
			<li>
				<i class="ace-icon fa fa-home home-icon"></i>
				<a href="#">Home</a>
			</li>

			<li>
				<a href="#">Translator</a>
			</li>
			<li class="active"> List</li>
		</ul>
	</div>
	<div class="page-content">
	<div class="page-header">
		<h1>	Translator
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
				Add Translator
		</small>
		</h1>
	</div>
	
	<div class="row">
	<div class="col-xs-12">
	<?php
		$attributes = array(
						'class' => 'form-horizontal', 
						'id' => 'add'
					);
		echo form_open('#', $attributes);
	?>

        <input type="hidden" id="pid_selected" name="pid_selected" value="0">
		<div class="form-group">
       		<label class="col-sm-2 control-label">Email</label> 
           	<div class="col-sm-4"> 
            	<input type="text" class="form-control" id="email" name="email"> 
        	</div>
   		</div>
   		
   		<div class="form-group">
       		<label class="col-sm-2 control-label">First Name</label> 
           	<div class="col-sm-4"> 
            	<input type="text" class="form-control" id="first_name" name="first_name"> 
        	</div>
   		</div>
   		
   		<div class="form-group">
       		<label class="col-sm-2 control-label">Last Name</label> 
           	<div class="col-sm-4"> 
            	<input type="text" class="form-control" id="last_name" name="last_name"> 
        	</div>
   		</div>
   		
   		<div class="form-group">
       		<label class="col-sm-2 control-label">Location</label> 
           	<div class="col-sm-4"> 
            	<input type="text" class="form-control" id="location" name="location"> 
        	</div>
   		</div>
        <div class="form-group">
            <label class="col-sm-2 control-label">English Proofreader</label>
            <div class="col-sm-9" style=""...">
                <input type="radio" onclick="getLanguage($(this))"  class="form-input input-radio proofread_required" value="1" name="proofreader" /> Yes |
                <input type="radio" onclick="getLanguage101($(this))" class="form-input radio-input proofread_required" value="0" name="proofreader" checked="" /> No
            </div>
        </div>
   		
   		<div class="form-group" style="padding-left: 100px;">
   			<div class="col-sm-10" id="prfrdNo">
   			<table class="table" id="languagePairSelection">
   				<tr>
   					<th>Translate From</th>
   					<th>Translate To</th>
   					<th></th>
   				</tr>
   				<tr>
   					<td>
   						
   						<select class = "form-control selectpicker" name = "languageFrom[]" data-live-search="true">
   						<option>Select Language</option>
   						<?php foreach($from as $fr) { ?>
   							<option value="<?php echo $fr->id;?>"><?php echo $fr->name;?></option>
   						<?php } ?>	
   						</select>
   						
   					</td>

                    <td id="aa">
                                <select class = "form-control selectpicker" name = "languageTo[]" id="lang_to"  data-live-search="true">
                                    <option>Select Language</option>'
                                    <?php foreach($from as $fr) { ?>
                                        <option value="<?php echo $fr->id;?>"><?php echo $fr->name;?></option>
                                    <?php } ?>
                                </select>
                    </td>
                    <td id="bb">

                    </td>

   					<td>
   						 <button class = "btn btn-danger delRowBtn" type = "submit"><i class = "fa fa-times"></i></button>
   					</td>
   				</tr>
   			</table>
   			 <input id = "addlanguagePair" type = "button" class = "btn btn-primary pull-right" value = "Add More Language Pair" onclick = "insertLanguagePair($(this))" />
   			</div>
   		</div>
   		<div class="form-group">
      		<div class="col-sm-4 col-sm-offset-2">
           		<button class="btn btn-primary" type="submit">Save changes</button>
        	</div>
     	</div>
	</form>
	</div>
	</div>
	</div>
</div>
</div>
</div>

<?php
	$this->load->view('admin/includes/vwFooter');
?>