
<?php
$this->load->view('email/includes/vwHead');
?>

                                    <tr style="width:100%;margin:0;padding:0">
                                        <td colspan="4" style="border:1px solid #d4d4d4;width:100%;background:white;border-radius:10px;padding:20px;text-align:center" align="center">
                                            <p style="color:#003366;font-family:Helvetica,Arial,sans-serif;font-size:18px;margin:0 0 5px;padding:0;text-align:left">
                                              Dear <strong> <?php echo $name; ?> </strong>
                                         
                                            </p>
                                 <table style="width:640px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#003366;text-align:left">          
                               <thead>
<tr>
<th style="background:#208ce5;color:#fff;font-weight:normal;text-align:left;padding:10px;width:100%;font-size:13px">Your Message :</th>

</tr>
</thead>     
</table>       
                                            
                              <table style="width:636px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#003366;text-align:left;border: 1px solid rgb(32, 140, 229);  margin-left: 2.5px; margin-top: -2px;">

<tbody>
<tr>
<td style="background:#efefef;padding:10px;color:#003366">Job Title:
</td>                                                   
<td style="background:#efefef;padding:10px;color:#003366"> <a href="<?php echo base_url(); ?>job/<?php echo $job_alias; ?>"><?php echo $job_title; ?></a></td>
</tr>
<tr>
<td style="background:#efefef;padding:10px;color:#003366">Job price:
</td>                                                   
<td style="background:#efefef;padding:10px;color:#003366"><?php echo $job_price; ?></td>
</tr>
<tr>

<tr>
<td style="background:#efefef;padding:10px;color:#003366">Awarded Date:
</td>                                                   
<td style="background:#efefef;padding:10px;color:#003366"><?php echo date("jS F ,Y", strtotime($award_date));?></td>
</tr>

<tr>
<td style="background:#efefef;padding:10px;color:#003366"> Message:
</td>                                                   
<td style="background:#efefef;padding:10px;color:#003366"><?php echo $message; ?></td>
</tr>

</tbody>
</table>
                                            
                           <!--   <table style="width:680px;margin:0 auto;padding:0;text-align:center" align="center" cellpadding="0" cellspacing="0">
                                    <tbody><tr style="width:100%;margin:0;padding:0" align="center">
                                        <td style="width:100%;text-align:center" align="center">
                                            <p style="color:#208ce5;font-family:Helvetica,Arial,sans-serif;font-size:40px;margin:10px 0;padding:0">Learn even more</p>
<p style="color:#003366;font-family:Helvetica,Arial,sans-serif;font-size:14px;margin:0px 0 10px 0;padding:0;line-height:25px">If you have any questions, please visit our FAQs or contact our support team and we will be happy to help.</p>
                                        </td>
                                    </tr>
                                </tbody></table>

                              <table style="width:265px;margin:0 auto;padding:0;margin-bottom:20px" align="center" cellpadding="0" cellspacing="0">
                                    <tbody><tr style="width:100%;margin:0;padding:0" align="center">
                                        <td style="width:64px;height:110px">
    <a href="<?php echo base_url(); ?>" style="text-decoration:none;color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:16px" target="_blank">
        <img class="CToWUd" src="<?php echo base_url(); ?>img/link3.png" style="width:64px;min-height:63px;border:0">
        <span>Blog</span>
    </a>
</td>
<td style="width:7px;height:110px">&nbsp;</td>
<td style="width:64px;height:110px">
    <a href="<?php echo base_url(); ?>support" style="text-decoration:none;color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:16px" target="_blank">
        <img class="CToWUd" src="<?php echo base_url(); ?>img/link1.png" style="width:64px;min-height:63px;border:0">
        <span>FAQ</span>
    </a>
</td>
<td style="width:7px;height:110px">&nbsp;</td>
<td style="width:64px;height:110px;margin-left:7px">
    <a href="<?php echo base_url(); ?>contact-us" style="text-decoration:none;color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:16px" target="_blank">
        <img class="CToWUd" src="<?php echo base_url(); ?>img/link2.png" style="width:64px;min-height:63px;border:0">
        <span>Contact Us</span>
    </a>
</td>
                                    </tr>
                                </tbody></table>
                            </td>
                        </tr>
                    </tbody></table>

                </td>
            </tr>
        </tbody></table>
        
                </td>
            </tr>
        </tbody></table>
</body>
</html>-->
<?php
$this->load->view('email/includes/vwFoot');
?>