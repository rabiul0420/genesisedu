@if ($bmdc_status==1)
<input class="hidden" type="text" name='bmdc_no_hidden' value="0"  required="required">bmdc already exists.
@elseif ($bmdc_status==0)
 <input class="hidden" type="text" name='bmdc_no_hidden'  value="0" required="required">bmdc number must be 5-7 digits(only numbers)
@endif