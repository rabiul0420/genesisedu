
<div class="form-group">
    <label class="col-md-3 control-label couese" >Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-4"> 
        <div class="input-icon right"> 
            @php  $sessions->prepend('Select session', ''); @endphp                           
            {!! Form::select( 'session_id', $sessions, '', ['class'=>'form-control','required'=>'required']) !!}<i></i>                         
        </div>
    </div>
</div>
