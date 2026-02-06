<?php $this->load->view('admin/includes/vwHeader'); ?>
<style media="screen">
    html, body {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
    }

    .myIfrm {
        width: 100%;
        height: 450px;
    }

    #over img {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<?php
if (count($documents)) {
foreach ($documents as $document) {
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" style="padding:10px; margin-left: 15px;">
            <a href="<?php echo base_url('uploads/' . $document['document']) ?>" class="btn btn-app btn-lg">Download
                this file</a>
        </div>
        <div class="col-sm-8 col-sm-offset-2">
            <?php
            switch ($document['file_type']) {
                case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
                case "application/msword":
                    //case "application/csv":
                    //case "text/csv":
                    //case "text/x-csv":
//                case "text/x-comma-separated-values":
                case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
                case "application/vnd.ms-powerpointtd":
                case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                case "application/vnd.ms-excel":
                case "application/excel":
                case "application/powerpoint":

                    ?>
                    <iframe class="myIfrm" loading="lazy"
                            src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url('uploads/' . $document['document']) ?>'
                            frameborder='0'></iframe>

                    <?php
                    break;

                case "application/pdf":
                    ?>
                    <iframe class="myIfrm" loading="lazy"
                            src="https://docs.google.com/gview?url=<?php echo base_url() . 'uploads/' . $document['document'] ?>&embedded=true"
                            frameborder="0"></iframe>
                    <!--<iframe  class="myIfrm" src="<?php echo base_url('uploads/' . $document['document']) ?>" frameborder="0" ></iframe>-->

                    <?php
                    break;

                case "image/bmp":
                case "image/gif":
                case "image/jpeg":
                case "image/jpg":
                case "image/png":


                    ?>
                    <div id="over">
                        <img class="img-responsive" style=" max-height:450px;"
                             src="<?php echo base_url() . 'uploads/' . $document['document'] ?>"/>
                    </div>
                    <?php
                    break;

                case "text/x-comma-separated-values": ?>
                    <iframe class="myIfrm" loading="lazy" src='http://datapipes.okfnlabs.org/csv/html/?url=<?php echo base_url('uploads/'.$document['document']) ?>' frameborder='0'></iframe>
                    <?php
                    break;

                default:
                    ?>
                    <div id="over">
                        No preview available
                    </div>
                    <?php
                    // force download
                    // header('Pragma: public');
                    // header('Expires: 0');
                    // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    // header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime (base_url('uploads/'.$document['document']))).' GMT');
                    // header('Cache-Control: private',false);
                    // header('Content-Type: '.$document['file_type']);
                    // header('Content-Disposition: attachment; filename="'.basename($document['file_info']['name']).'"');
                    // header('Content-Transfer-Encoding: binary');
                    // header('Content-Length: '.$document['file_info']['size']);
                    // header('Connection: close');
                    // readfile(base_url('uploads/'.$document['document']));
                    // exit();
                    break;
            }

            }
            }
            ?>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/vwFooter'); ?>
