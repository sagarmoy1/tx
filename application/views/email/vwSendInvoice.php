<?php
$this->load->view('email/includes/vwHeader');
?>

<div class="container">
Hi, <?php echo $name; ?> <br />
    <?php echo $content; ?> 
    <br />
   Payable Amount: &pound;<?php echo $amount; ?>, 
   If you prefer to process the payment with a credit card or a Paypal account please <a href="http://www.demand-ingtalent.co.uk/admin_user/payment/<?php echo $invoice_id; ?>" > Click Here</a>
    
</div>


<?php
$this->load->view('email/includes/vwFooter');
?>