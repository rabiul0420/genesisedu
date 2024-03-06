
{{-- <div class="">
    <select name="upazila_id" id="" class="form-control p-1 shadow-none">
        <option value="" disabled>Select Upazila</option>
        @foreach ($upazilas as $key=>$name)
            <option value="{{$key}}">{{$name}}</option>
        @endforeach
    </select>
</div> --}}

<div class="">
    @php  $upazilas->prepend('Select Upazila', ''); @endphp
    {!! Form::select('upazila_id',$upazilas, old('upazila_id'),['class'=>'form-control p-1 shadow-none','required']) !!}<i></i>
</div>