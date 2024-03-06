@if ($bmdc_status==1)
<input class="hidden" type="text" name='bmdc_no_hidden' required="required">mobile number already exists.
@elseif ($bmdc_status==0)
 <input class="hidden" type="text" name='bmdc_no_hidden' required="required">mobile Number must be 11 digit (Example:013000000)
@endif