<?php $this->load->view('vwHeader'); ?>
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

<div class="row">
    <div class="col-sm-12" style="padding:10px; margin-left: 15px;">
        <a href="<?php echo base_url('uploads/' . $document) ?>" class="btn btn-app btn-lg">Download this file</a>
    </div>
    <div class="col-sm-12">
        <?php

        switch ($file_type) {
            case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            case "application/msword":
            case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
            case "application/vnd.ms-powerpointtd":
            case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
            case "application/vnd.ms-excel":
            case "application/excel":
            case "application/powerpoint":
                ?>
				<center>
                <iframe class="myIfrm" loading="lazy" src='https://view.officeapps.live.com/op/embed.aspx?src=<?php echo base_url('uploads/' . $document) ?>' frameborder='0'>
                </iframe>
                </center>
				<?php
                break;

            case "application/pdf":
                ?>
				<center>
                <iframe class="myIfrm" loading="lazy" src="https://docs.google.com/gview?url=<?php echo base_url('uploads/' . $document) ?>&embedded=true"
                        frameborder="0"></iframe>
				</center>
                <?php
                break;

            case "text/x-comma-separated-values": ?>
                <iframe class="myIfrm" loading="lazy" src='http://datapipes.okfnlabs.org/csv/html/?url=<?php echo base_url('uploads/'.$document) ?>' frameborder='0'></iframe>
                <?php
                break;

            case "image/bmp":
            case "image/gif":
            case "image/jpeg":
            case "image/jpg":
            case "image/png":
                ?>
                <div id="over">
                    <img class="img-responsive" style=" max-height:450px;" src="<?php echo base_url() . 'uploads/' . $document ?>"/>
                </div>
                <?php
                break;

            default:
                ?>
                <div id="over">
                    <div style="width: 200px; margin: auto; font-size: 18px; font-weight: bold; padding-top: 10%;">
                        No preview available
                    </div>
                </div>
                <?php
                // force download
                // header('Pragma: public');
                // header('Expires: 0');
                // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                // header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime (base_url('uploads/'.$document))).' GMT');
                // header('Cache-Control: private',false);
                // header('Content-Type: '.$file_type);
                // header('Content-Disposition: attachment; filename="'.basename($file_info['name']).'"');
                // header('Content-Transfer-Encoding: binary');
                // header('Content-Length: '.$file_info['size']);
                // header('Connection: close');
                // readfile(base_url('uploads/'.$document));
                // exit();
                break;
        }
        ?>
    </div>
    </div>
    <?php
    $this->load->view('vwFooter');
    $this->load->view('vwFooterLower');
    ?>
