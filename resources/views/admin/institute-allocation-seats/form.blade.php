<div class="form-body">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">

                <select name="institute_discipline_id" class="form-control">
                    <option value="">-- Select Discipline --</option>
                    @foreach ($instituteDisciplines as $key => $instituteDiscipline)
                        <option {{ old('institute_discipline_id', $instituteAllocationSeat->institute_discipline_id ?? '') == $key ? 'selected' : '' }}
{{--                        <option {{ old( 'institute_discipline_id' ) == $key ? 'selected' : '' }}--}}
                                value="{{ $key }}">{{ $instituteDiscipline }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>


<div class="form-body" id="institute-allocation-selection">
    @include( 'admin.ajax.institute_allocation_select' )
</div>

<div class="form-body" id="allocation-course-selection">
    @include( 'admin.ajax.course_allocation' )
</div>


<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Select Year (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <select name="year" class="form-control">
                    <option value="">-- Select Year --</option>
                    <option {{ (old('year') ?? $instituteAllocationSeat->year) == date('Y')+1 ? 'selected' : '' }} value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                    <option {{ (old('year') ?? $instituteAllocationSeat->year) == date('Y') ? 'selected' : '' }} value="{{ date('Y') }}">{{ date('Y') }}</option>
                    <option {{ (old('year') ?? $instituteAllocationSeat->year) == date('Y')-1 ? 'selected' : '' }} value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Private Seat(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="number" name="private" required value="{{ old('private') ?? $instituteAllocationSeat->private }}" placeholder="Private Seat" class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Government Seat(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="number" name="government" required value="{{ old('government') ?? $instituteAllocationSeat->government }}" placeholder="Government Seat" class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">BSMMU Seat(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="number" name="bsmmu" required value="{{ old('bsmmu') ?? $instituteAllocationSeat->bsmmu }}" placeholder="BSMMU Seat" class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Armed Forces Seat(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="number" name="armed_forces" required value="{{ old('armed_forces') ?? $instituteAllocationSeat->armed_forces }}" placeholder="Armed Forces Seat" class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Others Seat(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
        <div class="col-md-6">
            <div class="input-icon right">
                <input type="number" name="others" required value="{{ old('others') ?? $instituteAllocationSeat->others }}" placeholder="Others Seat" class="form-control">
            </div>
        </div>
    </div>
</div>

@section('js')

    <script>

        enable_course_loader();

        function enable_course_loader() {

            $( '[name="institute_allocation_id"]' ).on( 'change', function(){

                var id = $( this ).val( );

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "GET",
                    url: '/admin/allocation-courses',
                    dataType: 'HTML',
                    data: {  institute_allocation_id: id},
                    success: function( data ) {
                        $('#allocation-course-selection').html(data);
                    }
                });

            });
        }

        $( '[name="institute_discipline_id"]' ).on( 'change', function(){

            var id = $( this ).val( );


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: '/admin/allocation-institute-discipline',
                dataType: 'HTML',
                data: {  dicipline_id: id},
                success: function( data ) {
                    $('#institute-allocation-selection').html(data);

                    enable_course_loader();
                }
            });

        });




    </script>

@endsection