function manage_disciplines_and_faculties( action ){

    $( "body" ).on( "change", "[name='course_id']", function() {
        var institute_id = $("[name='institute_id']").val();
        var course_id = $(this).val();
        var year = $("[name='year']").val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '/admin/course-changed-in-lecture-videos?prepend=false',
            dataType: 'HTML',
            data: {institute_id:institute_id,course_id: course_id},
            success: function( data ) {
                //alert( "OKKK" );

                var data = JSON.parse(data);
                $('.faculties').html('');
                $('.disciplines').html('');
                $('.faculties').html(data['faculties']);
                $('.disciplines').html(data['subjects']);

                // $('.topics').html('');
                // $('.topics').html(data['topics']);

                $('.select2').select2({ });

                $( '#faculty_id' ).on( 'change', function (e) {
                    var data = $( this ).select2('data');
                    var faculty_ids = [];

                    $.each( data, function ( key,item ) {
                        faculty_ids.push( item.id );
                    });

                    console.log( faculty_ids );

                    load_disciplines_by_faculties( faculty_ids );
                });
            }
        });

        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '/admin/course-sessions',
            dataType: 'HTML',
            data: {course_id : course_id,year : year},
            success: function( data ) {
                console.log(data)
                $('.session').html('');
                $('.session').html(data);
            }
        });

        load_disciplines_by_faculties( null );
    });

    function load_disciplines_by_faculties( ids ){
        ids = ids || [];
        // $('.disciplines').html('');
        var institute_id = $("[name='institute_id']").val();
        var course_id = $("[name='course_id']").val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '/admin/disciplines-by-multiple-faculties?prepend=false',
            dataType: 'HTML',
            data: { institute_id:institute_id, course_id: course_id, faculty_ids: ids },
            success: function( data ) {

                var data = JSON.parse( data );
                $('.disciplines').html('');
                $('.disciplines').html(data['subjects']);
                $('.select2').select2({ });
            }
        });
    }

    if( action == 'edit' ) {
        $('.select2').select2({ });
    }

    $("body").on( "click", "#checkbox", function() {
        if($("#checkbox").is(':checked') ){
            $(".disciplines .select2 > option").prop("selected","selected");
            $(".disciplines .select2").trigger("change");
        }else{
            $(".disciplines .select2 > option").removeAttr("selected");
            $(".disciplines .select2").trigger("change");
        }
    });

    $("body").on( "click", "#checkbox_faculty", function() {
        if($("#checkbox_faculty").is(':checked') ){
            $(".faculties .select2 > option").prop("selected","selected");
            $(".faculties .select2").trigger("change");
        }else{
            $(".faculties .select2 > option").removeAttr("selected");
            $(".faculties .select2").trigger("change");
        }
    });

}
