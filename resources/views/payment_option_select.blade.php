<div class="modal-header">                                            
<h4 class="modal-title">Payment Option</h4>
</div>
<div class="modal-body">
<h6>Please select a payment option :</h6><br>
<label class="radio-inline" style="cursor:pointer;" >
    <input type="radio" name="payment_option" required value="single"> Full Payment {{ ($batch->full_payment_waiver > 0 ) ? ' ( Save '.$batch->full_payment_waiver.' BDT )': '' }}
</label>
<br><br>
<label class="radio-inline" style="cursor:pointer;" >
    <input type="radio" name="payment_option" required value="{{ ($doctor_course->paid_amount() <= 0 || $doctor_course->paid_amount() ==  null)?(($doctor_course->payment_option == 'custom' )?'custom':'default'):$doctor_course->payment_option }}"> Installment
</label>
</div>
<div class="modal-footer">
<a class="btn btn-default" data-dismiss="modal">Close</a>
<a id="id_submit" class="btn btn-xs btn-secondary disabled"  style="color:white;background-color:red" href="#" data-doctor-course-id="{{ $doctor_course->id }}">Submit</a>
</div>