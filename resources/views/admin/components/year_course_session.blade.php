<div class="form-group col-md-2">
    <label class="col-md-3 control-label">Year</label>
    <div class="controls">
        <select name="year" id="year" class="form-control" required>
            <option value="">Select Year</option>
            @for ($year = date("Y") + 1; $year >= 2017; $year--)
            <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </select>
    </div>
</div>

<div class="course"></div>    

<div class="session"></div>


<!-- jQuery & Waypoints -->   
<script src="{{ asset('js/jquery-2.1.1.min.js') }}"></script>
<script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

<script type="text/javascript">
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("body").on( "change", "[name='year']", function() {
            var year = $('#year').val();
            $.ajax({
                type: "GET",
                url: '/admin/class-course-search',
                dataType: 'HTML',
                data: { year: year },
                success: function( data ) {
                     $('.course').html(data); 
                     $('#course_id').select2(); 
                }
            });
        })


        $("body").on( "change", "[name='course_id']", function() {
            var course_id = $(this).val();
            var year = $('#year').val();
            $.ajax({
                type: "GET",
                url: '/admin/class-search-session',
                dataType: 'HTML',
                data: {course_id : course_id, year: year },
                success: function( data ) {
                     $('.session').html(data); 
                     $('#session_id').html(data); 
                }
            });
        })

    })
</script>