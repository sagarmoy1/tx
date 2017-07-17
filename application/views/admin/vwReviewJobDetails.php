<?php
$this->load->view('admin/includes/vwHeader');
?>


<!-- /section:basics/navbar.layout -->
<div class="main-container" id="main-container">
    <script type="text/javascript">
        try {
            ace.settings.check('main-container', 'fixed')
        } catch (e) {
        }
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
                    try {
                        ace.settings.check('breadcrumbs', 'fixed')
                    } catch (e) {
                    }
                </script>

                <ul class="breadcrumb">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#">Home</a>
                    </li>

                    <li>
                        <a href="#">Review Job</a>
                    </li>
                    <li class="active">Edit Review Job</li>
                </ul><!-- /.breadcrumb -->
            </div>

            <!-- /section:basics/content.breadcrumbs -->
            <div class="page-content">
                <!-- #section:settings.box -->
                <!-- /.ace-settings-container -->
                <?php
                $this->load->view('admin/includes/vwSidebar-settings');
                ?>
                <!-- /section:settings.box -->
                <div class="page-header">
                    <h1>
                        Review Job
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Edit Review Job
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <div id="form-nothing-changed" class="alert alert-block alert-warning" style="display:none">
                            <button type="button" class="close" data-dismiss="alert">
                                <i class="ace-icon fa fa-times"></i>
                            </button>
                            <p> Nothing has been updated! </p>
                        </div>

                        <?php if ($this->session->flashdata('flash_message')) {
                            if ($this->session->flashdata('flash_message') == 'try_another') { ?>
                                <div class="alert alert-block alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">
                                        <i class="ace-icon fa fa-times"></i>
                                    </button>
                                    <p> Alias name is not available. Try another. </p>
                                </div> <?php
                            }
                        } ?>

                        <?php if (validation_errors() != "") { ?>
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>
                                <p> <?php echo validation_errors(); ?> </p>
                            </div>
                        <?php } ?>

                        <?php if ($this->session->flashdata('success_message')) { ?>
                            <div class="alert alert-block alert-success">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>

                                <p><?php echo $this->session->flashdata('success_message'); ?></p>
                            </div>

                            <?php
                        }
                        ?>

                        <?php if ($this->session->flashdata('error_message')) { ?>
                            <div class="alert  alert-danger">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>

                                <p><?php echo $this->session->flashdata('error_message'); ?></p>
                            </div>

                            <?php
                        }
                        ?>


                        <?php
                        $attributes = array('class' => 'form-horizontal', 'id' => 'form-review-job', 'enctype' => 'multipart/form-data');
                        echo form_open('admin_review/editprofile/' . $fetch->id, $attributes);
                        ?>


                        <div class="form-group lineNumberInput">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Line
                                Number* </label>
                            <div class="col-sm-9">
                                <?php
                                $curr_month = date("m");
                                $curr_year = date("y");

                                if ($fetch->lineMonth != '' && $fetch->lineMonth != "0") {
                                    $curr_month = $fetch->lineMonth;
                                    // } else {
                                    // $curr_month = $curr_values['lineMonth'];
                                }


                                if ($fetch->lineYear != '' && $fetch->lineYear != "0") {
                                    $curr_year = $fetch->lineYear;
                                    // } else {
                                    // $curr_year = $curr_values['lineYear'];
                                }
                                ?>
                                <select name="lineMonth" id="lineMonth" class="col-xs-3 col-sm-2 validate[required]">
                                    <option value="01" <?php if ($curr_month == "01") echo "selected"; ?>>January
                                    </option>
                                    <option value="02" <?php if ($curr_month == "02") echo "selected"; ?>>February
                                    </option>
                                    <option value="03" <?php if ($curr_month == "03") echo "selected"; ?>>March</option>
                                    <option value="04" <?php if ($curr_month == "04") echo "selected"; ?>>April</option>
                                    <option value="05" <?php if ($curr_month == "05") echo "selected"; ?>>May</option>
                                    <option value="06" <?php if ($curr_month == "06") echo "selected"; ?>>June</option>
                                    <option value="07" <?php if ($curr_month == "07") echo "selected"; ?>>July</option>
                                    <option value="08" <?php if ($curr_month == "08") echo "selected"; ?>>August
                                    </option>
                                    <option value="09" <?php if ($curr_month == "09") echo "selected"; ?>>September
                                    </option>
                                    <option value="10" <?php if ($curr_month == "10") echo "selected"; ?>>October
                                    </option>
                                    <option value="11" <?php if ($curr_month == "11") echo "selected"; ?>>November
                                    </option>
                                    <option value="12" <?php if ($curr_month == "12") echo "selected"; ?>>December
                                    </option>
                                </select>
                                <div style="float:left;">&nbsp;</div>
                                <select name="lineYear" id="lineYear" class="col-xs-2 col-sm-1 validate[required]">
                                    <?php foreach (range(2016, 2050) as $year) { ?>
                                        <option value="<?php echo substr($year, -2); ?>" <?php if ($curr_year == substr($year, -2)) echo 'selected'; ?>><?php echo $year; ?></option>
                                    <?php } ?>
                                </select>
                                <span style="float:left;">&nbsp;L&nbsp;</span>
                                <input name="lineNumber" type="number" id="lineNumber" class="col-xs-2 col-sm-1"
                                       value="<?php echo $fetch->lineNumber ?>"/>
                            </div>

                            <input type="hidden" id="_lineMonth" name="_lineMonth" value=""/>
                            <input type="hidden" id="_lineYear" name="_lineYear" value=""/>
                            <input type="hidden" id="_lineNumber" name="_lineNumber" value=""/>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Name* </label>
                            <div class="col-sm-9">
                                <input name="job_title" id="job_title" class="col-xs-10 col-sm-5 validate[required]"
                                       type="text" value="<?php echo $fetch->name; ?>" readonly>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Price($)*</label>
                            <div class="col-sm-3">
                                <input name="job_price" id="job_price" type="text" value="<?php echo $fetch->price; ?>"
                                       style="width: 100%;" readonly>
                            </div>
                            <div class="col-sm-6">
                                <div class="remaining-balance-wrapper"></div>
                                <input type="hidden" id="remaining_balance" name="remaining_balance"/>
                                <input type="hidden" id="original_balance" name="original_balance"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"
                                   for="form-field-1">Description*</label>
                            <div class="col-sm-9">
                <textarea name="job_description" id="editor" class="validate[required]">
                 <?php echo $fetch->description; ?>
                </textarea>
                            </div>
                        </div>

                        <?php
                        $sql = " SELECT * FROM `languages` ORDER BY `name` ASC";
                        $query = $this->db->query($sql);
                        $Language_fetch = $query->result();
                        ?>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Uploaded File
                                :</label>
                            <div class="col-sm-9">
                                <?php
                                $sql = "SELECT p2.*, p2.id AS proofread_job_doc_id, CONCAT(first_name, ' ', last_name) AS translator_name FROM proofread_jobs p1";
                                $sql .= " JOIN proofread_jobs_docs p2 ON p2.proofread_job_id = p1.id ";
                                $sql .= " JOIN translator t ON t.id = p2.translator_id ";
                                $sql .= "WHERE p1.job_id=" . $fetch->id;
                                $sql .= " AND p2.is_active = 1";
                                $query = $this->db->query($sql);
                                ?>


                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center" style="width: 5%">#</th>
                                        <th class="center" style="width: 30%">Original File</th>
                                        <th class="center" style="width: 30%">Translated File</th>
                                        <th class="center" style="width: 20%">Translator</th>
                                        <th style="width: 5%;">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($query->num_rows()) {
                                        foreach ($query->result_array() as $i => $result) {
                                            $row_count = (int)$i + 1;
                                            $original = explode('/', $result['original_file']);
                                            $translated = explode('/', $result['translated_file']);
                                            ?>
                                            <tr id="document-<?php echo $result['id'] ?>-wrapper">
                                                <td style="text-align: center; vertical-align: middle;"><?php echo $row_count ?></td>
                                                <td>
                                                    <a href="<?php echo base_url() ?>admin/review/document/viewer/<?php echo $result['proofread_job_doc_id'] ?>/original_file"
                                                       class="btn btn-app btn-purple btn-lg"
                                                       target="_blank"><?php echo $original[1]; ?></a></td>
                                                <td>
                                                    <a href="<?php echo base_url() ?>admin/review/document/viewer/<?php echo $result['proofread_job_doc_id'] ?>/translated_file"
                                                       class="btn btn-app btn-purple btn-lg"
                                                       target="_blank"><?php echo $translated[1]; ?></a></td>
                                                <td style="vertical-align: middle"><?php echo $result['translator_name'] ?></td>
                                                <td style="text-align: center; padding-top: 22px;"><a
                                                            href="javascript: void(0);" title="Delete this file"
                                                            class="toggle-delete-file"
                                                            data-proofread="<?php echo $result['id'] ?>"><i
                                                                class="glyphicon glyphicon-remove"></i></a></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center;">No document(s) associated with
                                                this job
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="prefile" size="20" class="col-xs-10 col-sm-5"
                                   value="<?php echo $fetch->file; ?>"/>
                        </div>

                        <div class="form-group" id="documents-container">
                            <div class="col-sm-9 col-sm-offset-3" style="margin-bottom: 10px;">
                                <a href="javascript: void(0);" id="toggle-upload-more" style="text-decoration: none;"><i
                                            class="ace-icon glyphicon glyphicon-plus"></i>&nbsp;&nbsp;Upload
                                    documents</a>
                            </div>

                            <div class="uploader-wrapper">
                                <div class="col-sm-9 col-sm-offset-3 first" style="margin-bottom:10px;">
                                    <div class="col-sm-1" id="row-num" style="text-align: right;">#<span
                                                class="row-num-val">1</span></div>
                                    <div class="col-sm-3">
                                        <input type="file" name="document[original][]" style="width: 100%"/>
                                        <label>Original File</label>
                                    </div>

                                    <div class="col-sm-3">
                                        <input type="file" name="document[translated][]" style="width: 100%"/>
                                        <label>Translated File</label>
                                    </div>

                                    <?php if ($fetch->proofread_required) { ?>
                                        <div class="col-sm-4">
                                            <?php
                                            $proofread_job = $this->db->from('proofread_jobs')->where('job_id', $fetch->id)->get();

                                            if ($proofread_job->num_rows()) {
                                                $translator = $this->db->from('translator')->where('id', $proofread_job->row()->translator_id)->get()->row();
                                            } else {
                                                $translator = null;
                                            }

                                            if (count($translator)) {
                                                ?>
                                                <div id="translator-field">
                                                    <input type="text" name="translator" class="translator-name"
                                                           style="width: 100%"
                                                           value="<?php echo $translator->last_name ?>, <?php echo $translator->first_name ?>"
                                                           placeholder="Type translator name here" readonly="readonly"/>
                                                    <input type="hidden" class="translator-id"
                                                           name="document[translator][]"
                                                           value="<?php echo $translator->id ?>"/>
                                                </div>

                                                <?php
                                            } else {
                                                ?>
                                                <div id="translator-field">
                                                    <input type="text" name="translator" class="translator-name"
                                                           style="width: 100%" placeholder="Type translator name here"/>
                                                    <input type="hidden" class="translator-id"
                                                           name="document[translator][]"/>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <label style="
    font-size: 14px;
">Who translated this?</label>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-sm-4">
                                            <div id="translator-field">
                                                <input type="text" name="translator" class="translator-name"
                                                       style="width: 100%" placeholder="Type translator name here"/>
                                                <input type="hidden" class="translator-id"
                                                       name="document[translator][]"/>
                                            </div>
                                            <label style="
    font-size: 14px;
">Who translated this?</label>
                                        </div>
                                    <?php } ?>

                                    <div class="col-sm-1">
                                        <a href="javascript:void(0)" id="toggle-remove-uploader"
                                           style="margin-top:5px;"><i
                                                    class="ace-icon glyphicon glyphicon-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php
                        $language_arr = explode('/', $fetch->language);
                        if (count($language_arr)) {
                            $language_from = $language_arr[0];
                            $language_to = $language_arr[1];
                        } else {
                            $language_from = '';
                            $language_to = '';
                        }
                        ?>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Translate
                                From*</label>
                            <div class="col-sm-9">
                                <select id="language-from" name="language_from" class="validate[required]"
                                        style="width: 42%">
                                    <option value=""> Select Language</option>
                                    <?php foreach ($Language_fetch as $row) { ?>
                                        <option value="<?php echo $row->id ?>" <?php echo ($language_from == $row->id) ? "selected" : "" ?> ><?php echo $row->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Translate
                                To*</label>
                            <div class="col-sm-9">
                                <select id="job-language" name="job_language" class="validate[required]"
                                        style="width: 42%">
                                    <option value=""> Select Language</option>
                                    <?php foreach ($Language_fetch as $row) { ?>
                                        <option value="<?php echo $row->id; ?>" <?php echo ($language_to == $row->id) ? "selected" : "" ?> ><?php echo $row->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Due Date*</label>
                            <div class="col-sm-9">
                                <?php
                                $due_date = explode(' ', $fetch->dueDate);
                                if (isset($due_date[1])) {
                                    $time = explode(':', $due_date[1]);
                                    if ($time[0]) {
                                        $hr = $time[0];
                                    } else {
                                        $hr = date('h');
                                    }
                                    if ($time[1]) {
                                        $min = $time[1];
                                    } else {
                                        $min = date('i');
                                    }
                                    if (isset($due_date[2])) {
                                        $ampm = $due_date[2];
                                    } else {
                                        $ampm = date('A');
                                    }
                                } else {
                                    $hr = date('h');
                                    $min = date('i');
                                    $ampm = date('A');
                                }

                                ?>
                                <input type="text" name="dueDate" id="dueDate"
                                       class="datepicker col-xs-5 col-sm-2 validate[required]"
                                       value="<?php echo $due_date[0]; ?>"/>
                                <select class="col-xs-5 col-sm-1 validate[required]" name="hour"
                                        style="height: 37px; margin-left: 7px;">
                                    <option value="">Hr</option>
                                    <option value="01" <?php echo ($hr == '01') ? 'selected' : '' ?> >01</option>
                                    <option value="02" <?php echo ($hr == '02') ? 'selected' : '' ?> >02</option>
                                    <option value="03" <?php echo ($hr == '03') ? 'selected' : '' ?> >03</option>
                                    <option value="04" <?php echo ($hr == '04') ? 'selected' : '' ?> >04</option>
                                    <option value="05" <?php echo ($hr == '05') ? 'selected' : '' ?> >05</option>
                                    <option value="06" <?php echo ($hr == '06') ? 'selected' : '' ?> >06</option>
                                    <option value="07" <?php echo ($hr == '07') ? 'selected' : '' ?> >07</option>
                                    <option value="08" <?php echo ($hr == '08') ? 'selected' : '' ?> >08</option>
                                    <option value="09" <?php echo ($hr == '09') ? 'selected' : '' ?> >09</option>
                                    <option value="10" <?php echo ($hr == '10') ? 'selected' : '' ?> >10</option>
                                    <option value="11" <?php echo ($hr == '11') ? 'selected' : '' ?> >11</option>
                                    <option value="12" <?php echo ($hr == '12') ? 'selected' : '' ?> >12</option>
                                </select>
                                <select class="col-xs-5 col-sm-1 validate[required]" name="minute"
                                        style="height: 37px; margin-left: 7px;">
                                    <option value="">Min</option>
                                    <?php for ($i = 0; $i <= 59; $i++) { ?>
                                        <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?php echo ($min == $i) ? 'selected' : '' ?> ><?php echo str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php } ?>
                                </select>
                                <select class="col-xs-5 col-sm-1 validate[required]" name="ampm"
                                        style="height: 37px; width: 79px; margin-left: 7px;">
                                    <option value="AM" <?php echo ($ampm == 'AM') ? 'selected' : '' ?> >AM</option>
                                    <option value="PM" <?php echo ($ampm == 'PM') ? 'selected' : '' ?> >PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Job
                                Type: </label>
                            <div class="col-sm-9">
                                <select id="type" name="type" class="col-xs-10 col-sm-5 validate[required]">
                                    <option value="">Select Type</option>
                                    <option value="1" <?php echo ($fetch->job_type == 1) ? 'selected' : '' ?> >Private
                                    </option>
                                    <option value="0" <?php echo ($fetch->job_type == 0) ? 'selected' : '' ?> >Public
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="proofreadQuestion">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Proofreading
                                required? </label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="radio" class="form-input input-radio proofread_required" value="1"
                                       id="proofread_required" name="proofread_required" checked/> Yes
                            </div>
                        </div>
                        <input type="hidden" name="job_stage" value="0">

                        <div class="form-group" id="proofreadSettings">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-1">Proofreading
                                type</label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="radio" class="form-input input-radio proofreadType" value="editing"
                                       id="proofreadType"
                                       name="proofreadType" <?php echo ($fetch->proofreadType == 'editing') ? "checked" : "" ?> />
                                Editing |
                                <input type="radio" class="form-input radio-input proofreadType" value="comparison"
                                       id="proofreadType"
                                       name="proofreadType" <?php echo ($fetch->proofreadType == 'comparison') ? "checked" : "" ?> />
                                Comparison
                            </div>
                        </div>

                        <div class="clearfix form-actions">
                            <div class="col-md-offset-3 col-md-9">
                                <button class="btn btn-info" type="submit" id="submit-form-edit">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Submit
                                </button>
                            </div>
                        </div>


                        <?php echo form_close(); ?>


                        <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
</div><!-- /.main-container -->

<div id="dialog-line-numbers1" title="Edit Job" style="display:none">
    <p style="font-size: 14px;">Are you sure you want to change the line number to <span
                class="line-number-wrapper"></span>?</p>
</div>

<div id="dialog-line-numbers2" title="Edit Job" style="display:none">
    <p style="font-size: 14px;">This line number already has a job associated to it. Do you want to associate this?</p>
    <div class="job-info-wrapper" style="padding: 10px; font-size: 15px;"></div>
</div>

<div id="dialog-delete-document" title="Delete Document" style="display:none">
    <p style="font-size: 14px;">Are you sure you want to delete this document?</p>
</div>

<div id="dialog-language" title="Change language" style="display:none">
    <p style="font-size: 14px;">Are you sure you want to change the language selection of this job? This may affect who
        is able to bid on this job.</p>
</div>

<div id="dialog-proofread" title="Change proofreading types" style="display:none">
    <p style="font-size: 14px;">Are you sure you want to change the proofreading selection of this job? This may affect
        who is able to bid on this job.</p>
</div>

<div id="dialog-common" title="Data has been changed" style="display:none">
    <p style="font-size: 14px;">Are you sure you want to make this change?</p>
</div>

<!-- page specific plugin ck editor scripts -->
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/css/samples.css"/>
<link rel="stylesheet"
      href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css"/>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/ckeditor.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>ckeditor/samples/js/sample.js"></script>
<script>
    initSample();
</script>
<!--<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jqueryn.js"></script>-->


<script>
    $(document).ready(function () {
        $("#userfile").change(function () {
            var filename = $('#userfile').val();

            var filename1 = filename.split('.');

            var filename2 = filename1.pop();

            var ext = filename2.toLowerCase();

            //var ext = $('#formID').val().split('.').pop().toLowerCase();
            if ($.inArray(ext, ['pdf', 'doc', 'xls', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'ai']) == -1) {
                alert('Invalid file format!. Please select pdf,doc,docx,xls,jpg,jpeg,png,zip or txt.');
                $('#userfile').val('');
            }
        });

    });


</script>

<script type="text/javascript">
    function message() {
        alert('Did Not Update');
    }
</script>
<?php $this->load->view('admin/includes/vwFooter'); ?>
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/uploadfilemulti.css"/>
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/jquery-ui-1.12.1.min.css"/>
<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/select2.css"/>

<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>css/jquery-confirm.min.css"/>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css"/>


<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery.min.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-ui.custom.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-ui.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/select2.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery.fileuploadmulti.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo HTTP_ASSETS_PATH_ADMIN; ?>js/jquery-confirm.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var last_selected_type = $("#type option:selected").val();
        var last_selected_proofread_required = $("input[name='proofread_required']:checked").val();
        var last_selected_proofread_type = $("input[name='proofreadType']:checked").val();

        var current_selected_line_month = $("#lineMonth option:selected").val();
        var current_selected_line_year = $("#lineYear option:selected").val();
        var current_line_number_value = $('#lineNumber').val();

        var formChanged = false;


        $(document).on('change', 'select', function () {
            formChanged = true;
        });

        $(document).on('change', 'input[type=radio]', function () {
            formChanged = true;
        });

        $(document).on('change keyup paste mouseup', 'input', function () {
            formChanged = true;
        });


        CKEDITOR.instances.editor.on('change', function () {
            formChanged = true;
        });

        $(document).on('click', '#submit-form-edit', function (e) {
            e.preventDefault();
            //alert("hello");
            if (formChanged) {
                //  $('#form-review-job').submit();
                var error = '';
                if ($("input[name='lineMonth']").attr("selectedIndex") == 0) {
                    error += '<p>Select Line Month</p>';

                }
                if ($("input[name='lineYear']").attr("selectedIndex") == 0) {
                    error += '<p>Select Line Year</p>';
                }
                if ($("input[name='lineNumber']").val() == '') {
                    error += '<p>Enter Line Number</p>';
                    //alert("Enter Line Number");
                }
                if ($("input[name='job_title']").val() == '') {
                    error += '<p>Enter Job Name</p>';
                }
                if ($("input[name='job_price']").val() == '') {
                    error += '<p>Enter Price</p>';
                    //alert("Enter Price");
                }
                if (CKEDITOR.instances['editor'].getData() == '') {
                    error += '<p>Enter Description</p>';
                }
                if ($("input[name='language_from_val']").val() == '') {
                    error += '<p>Select Language from</p>';
                }
                if ($("input[name='job_language__val']").val() == '') {
                    error += '<p>Select Language To</p>';
                }
                if ($("input[name='dueDate']").val() == '') {
                    error += '<p>Select Due Date</p>';
                }
                if ($("input[name='minute']").attr("selectedIndex") == 0) {
                    error += '<p>Enter Due Date Minute</p>';
                }
                if ($("input[name='ampm']").attr("selectedIndex") == 0) {
                    error += '<p>Select AM/PM</p>';
                }
                if ($("input[name='type']").attr("selectedIndex") == 0) {
                    error += '<p>Select Type</p>';
                }
                //check if form has empty val
                if (error == '') {
                    $('#form-review-job').submit();
                } else {
                    //alert(error);
                    $.alert({
                        theme: 'modern',
                        closeIcon: false,
                        type: 'dark',
                        typeAnimated: true,
                        icon: 'fa fa-warning',
                        title: 'Error!!',
                        content: error,
                    });
                }
            } else {
                $('#form-nothing-changed').show();
                setInterval(function () {
                    $('#form-nothing-changed').fadeOut(200)
                }, 3000);
            }

        });


        $('#language-from').select2({
            placeholder: "Select language",
            allowClear: true
        });

        $('#job-language').select2({
            placeholder: "Select language",
            allowClear: true
        });

        $('body').on('focus', '#dueDate', function () {
            $(this).datetimepicker({
                format: 'MM-DD-YYYY',
                minDate: 'now'
            });
        });

        $(document).on('change', '#type', function (e) {
            $('#dialog-common').dialog({
                resizable: false,
                height: "auto",
                width: 500,
                modal: false,
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                buttons: {
                    'Yes': function () {
                        $(this).dialog('close');
                        formChanged = true;
                    },
                    'No': function () {
                        $(this).dialog('close');
                        formChanged = false;

                        $('#type option[value=' + last_selected_type + ']').attr('selected', 'selected');
                    }
                }
            });
        });

        $(document).on('click', '#type', function () {
            last_selected_type = $("#type option:selected").val();
        });

        $(document).on('change', '#proofreadType', function (e) {
            $('#dialog-proofread').dialog({
                resizable: false,
                height: "auto",
                width: 500,
                modal: false,
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                buttons: {
                    'Yes': function () {
                        $(this).dialog('close');
                        formChanged = true;
                    },
                    'No': function () {
                        $(this).dialog('close');
                        formChanged = false;

                        $("input[name=proofreadType][value='" + last_selected_proofread_type + "']").prop("checked", true);
                    }
                }
            });
        });

        $(document).on('click', '.toggle-delete-file', function (e) {
            var $id = $(this).data('proofread');

            $('#dialog-delete-document').dialog({
                resizable: false,
                height: "auto",
                width: 600,
                modal: false,
                buttons: {
                    'Yes': function () {
                        $(this).dialog('close');

                        $.ajax({
                            url: "<?php echo base_url() ?>admin_review/delete_document_from_job",
                            data: {id: $id},
                            success: function (response) {
                                response = jQuery.parseJSON(response);

                                if (response.status) {
                                    $('#document-' + $id + '-wrapper').remove();
                                }
                            }
                        });
                    },
                    'No': function () {
                        $(this).dialog('close');
                    }
                }
            });
        });

        $(document).on('change', '#language-from', function (e) {
            $('#dialog-language').dialog({
                resizable: false,
                height: "auto",
                width: 500,
                modal: false,
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                buttons: {
                    'Yes': function () {
                        $(this).dialog('close');
                        formChanged = true;
                    },
                    'No': function () {
                        $('#language-from').val("<?php echo $language_from ?>").trigger("change");
                        formChanged = false;
                        $(this).dialog('close');
                    }
                }
            });
        });

        $(document).on('change', '#job-language', function (e) {
            $('#dialog-language').dialog({
                resizable: false,
                height: "auto",
                width: 500,
                modal: false,
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                buttons: {
                    'Yes': function () {
                        $(this).dialog('close');
                        formChanged = true;
                    },
                    'No': function () {
                        $('#job-language').val("<?php echo $language_to ?>").trigger("change");
                        formChanged = false;
                        $(this).dialog('close');
                    }
                }
            });
        });

        $(document).on('change', '#lineMonth, #lineYear', function (e) {
            $('#lineNumber').trigger('blur');
        });

        $(document).on('blur', '#lineNumber', function (e) {
            if (current_selected_line_month == $("#lineMonth option:selected").val() && current_selected_line_year == $('#lineYear option:selected').val() && current_line_number_value == $(this).val()) {
            } else {
                $('#dialog-line-numbers1').dialog({
                    resizable: false,
                    height: "auto",
                    width: 600,
                    modal: false,
                    closeOnEscape: false,
                    open: function (event, ui) {
                        $(".ui-dialog-titlebar-close").hide();
                    },
                    buttons: {
                        "Yes": function () {
                            $(this).dialog('close');

                            $.ajax({
                                url: "<?php echo base_url() ?>admin_review/check/line-number",
                                data: {
                                    line_month: $('#lineMonth option:selected').val(),
                                    line_year: $('#lineYear option:selected').val(),
                                    line_number: $('#lineNumber').val()
                                },
                                success: function (response) {
                                    if (response != null && response != '') {
                                        response = jQuery.parseJSON(response);
                                        $('#dialog-line-numbers2').dialog({
                                            resizable: false,
                                            height: "auto",
                                            width: 800,
                                            modal: false,
                                            closeOnEscape: false,
                                            open: function (event, ui) {
                                                $(".ui-dialog-titlebar-close").hide();
                                            },
                                            buttons: {
                                                "Yes": function () {
                                                    $(this).dialog('close');

                                                    $.ajax({
                                                        url: "<?php echo base_url() ?>admin_review/get/job-price",
                                                        data: {
                                                            line_month: $('#lineMonth option:selected').val(),
                                                            line_year: $('#lineYear option:selected').val(),
                                                            line_number: $('#lineNumber').val()
                                                        },
                                                        success: function (response) {
                                                            if (response != null || response != '') {
                                                                response = jQuery.parseJSON(response);
                                                                $('.remaining-balance-wrapper').html('Remaining Balance: $' + response.price);
                                                                $('#remaining_balance').val(response.price);
                                                                $('#job_price').val(response.original_price);
                                                                $('#original_price').val(response.original_price);

                                                                $('#_lineMonth').val($('#lineMonth option:selected').val());
                                                                $('#_lineYear').val($('#lineYear option:selected').val());
                                                                $('#_lineNumber').val($('#lineNumber').val());

                                                                $('#job_price').attr('readonly', 'readonly');
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
                                                    $('#lineNumber').focus();

                                                    formChanged = false;
                                                }
                                            }
                                        });

                                        var content = "Job: <span style='font-weight: bold'>" + response.job_name + "</span>, <span style='font-weight: bold'>Language: " + response.language_from + "</span> to <span style='font-weight: bold'>" + response.language_to + "</span>, Price: <span style='font-weight: bold'>$" + response.price + "</span>. Date Posted: <span style='font-weight: bold'>" + response.date_added + "</span>";
                                        $('.job-info-wrapper').html(content);
                                    } else {
                                        $('#job_price').removeAttr('readonly');
                                        $('#job_price').focus();
                                    }
                                }
                            });
                        },
                        "No": function () {
                            $(this).dialog('close');
                            $('#lineNumber').val(current_line_number_value);
                            $('#lineMonth option[value=' + current_selected_line_month + ']').attr('selected', 'selected');
                            $('#lineYear option[value=' + current_selected_line_year + ']').attr('selected', 'selected');
                            $('#lineNumber').focus();

                            formChanged = false;
                        },
                    }
                });

                var $line_number = 'M' + $('#lineMonth option:selected').val() + $('#lineYear option:selected').val() + 'L' + $('#lineNumber').val();
                $('.line-number-wrapper').html($line_number);
            }
        });

        $('.translator-name').autocomplete({
            source: "<?php echo base_url() . 'admin_review/get/translator' ?>",
            minLength: 3,
            select: function (event, ui) {
                $(this).next('.translator-id').val(ui.item.id);
            }
        });

        var row = 1;

        $(document).on('click', '#toggle-upload-more', function () {
            var clone_me = $('.uploader-wrapper').clone(true).show();
            $('.first', clone_me).removeClass('first');

            if (row <= 50) {
                row++;
                $('.row-num-val', clone_me).text(row);
                $('input[name="pair"]', clone_me).val(row);
                $('#documents-container').append('<div>' + clone_me.html() + '</div>');

                $('.translator-name').autocomplete({
                    source: "<?php echo base_url() . 'admin_review/get/translator' ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        $(this).next('.translator-id').val(ui.item.id);
                    }
                });
            }
        });

        $(document).on('click', '#toggle-remove-uploader', function () {
            $(this).parent().parent().remove();
        });
    });
</script>

