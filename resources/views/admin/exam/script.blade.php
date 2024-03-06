
<script src="{{ asset('js/content-selector.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/select2-configs.js') }}" type="text/javascript"></script>



<script type="text/javascript">
    $(document).ready(function() {

        function enableClassSearching( input ){
            $( '.class-selection' ).select2( select2Configs.classSearching( input ) );
        }

        content_manager({
            onInit: enableClassSearching,
            year: {
                onChange: input => {
                    $('[name="class"]').html('');
                    enableClassSearching( input );
                },
            },
            course: {
                url: '/admin/exam/courses'
            },
            session: {
                url: '/admin/exam/sessions',
                onChange: input => {
                    $('[name="class"]').html('');
                    enableClassSearching( input );
                }
            }
        });

        $('#datepicker').datepicker({
            format: 'yyyy-mm-dd',
            startDate: '1900-01-01',
            endDate: '2030-12-30',
        }).on('changeDate', function(e){
            $(this).datepicker('hide');
        });

        $("body").on( "change", "[name='question_type']", function() {
            var question_type = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/question-type',
                dataType: 'HTML',
                data: {question_type : question_type},
                success: function( data ) {
                    $('.question_type').html(data);

                }
            });
        })

        $("body").on( "change", "[name='institute_id']", function() {
            var institute_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/institute-course',
                dataType: 'HTML',
                data: {institute_id : institute_id},
                success: function( data ) {
                    $('.course').html(data);
                    $('.faculty').html('');
                    $('.subject').html('');
                }
            });
        })



        $("body").on( "change", "[name='course_id']", function() {

            var course_id = $(this).val();
            var url = $("[name='url']").val();
            if(url=='course-faculty' || action == 'edit' ){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/'+url,
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculty').html(data);
                    }
                });
            }

            if( action == 'edit ') {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-topics',
                    dataType: 'HTML',
                    data: { course_id: course_id },
                    success: function( data ) {
                        $('.topics').html(data);
                        $('.topic2').select2().trigger('change');
                    }
                });
            }

        });


        // $("body").on( "change", "[name='course_id']", function() {
        //
        //     var course_id = $(this).val();
        //     $.ajax({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         type: "POST",
        //         url: '/admin/'+$("[name='url']").val(),
        //         dataType: 'HTML',
        //         data: {course_id: course_id},
        //         success: function( data ) {
        //             $('.faculty').html(data);
        //         }
        //     });
        //     $.ajax({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         type: "POST",
        //         url: '/admin/course-topics',
        //         dataType: 'HTML',
        //         data: {course_id: course_id},
        //         success: function( data ) {
        //             $('.topics').html(data);
        //             $('.topic2').select2().trigger('change');
        //         }
        //     });
        // })


        $("body").on( "change", "[name='faculty_id']", function() {
            var faculty_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/faculty-subject',
                dataType: 'HTML',
                data: {faculty_id: faculty_id},
                success: function( data ) {
                    $('.subject').html(data);
                }
            });
        })


        //alert( question_type_id );

        $("body").on( "change", "[name='question_type_id'], [name='sif_only']", function() {

            var sif_only = $('[name="sif_only"]').val();
            var question_type_id = $('[name="question_type_id"]').val();

            // if(sif_only=='Yes'){
            //     $('.mcq-sba').empty();
            //     return;
            // }else if(question_type_id==''){
            //     $('.mcq-sba').empty();
            //     return;
            // }
            // $('.mcq-sba').show();


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/question-info',
                dataType: 'HTML',
                data: {question_type_id: question_type_id},
                success: function( data ) {
                    $('.question_type_info').html(data);
                }
            });

            // $.ajax({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            //     type: "POST",
            //     url: '/admin/question-type-mcq-sba',
            //     dataType: 'HTML',
            //     data: { question_type_id: question_type_id, exam_id: '{{ request()->segment(3)  }}' },
            //     success: enable_mcq_sba
            // });
        })

        function enable_mcq_sba( data ) {

            if( data !== null ) {
                $( '.mcq-sba' ).html(data);
            }

            $( '.mcqs2' ).select2( manage_question_selection({ type: 1}));
            $( '.sbas2' ).select2( manage_question_selection({ type: 2}));
            $( '.another-mcqs2' ).select2( manage_question_selection({ type: 1}));
            $( '.another-sbas2' ).select2( manage_question_selection({ type: 2}));

            function manage_question_selection( option ){
                option = option || {};

                function processResult( data ){
                    return {
                        results: $.map(data, function (item, i) {
                            return {
                                id : item.id ,
                                text : item.question_title +' (' + item.id + ') - <span class="text-muted">'
                                    + ' <strong>Subject:</strong> ' + item.quest_subject.name
                                    + ' <strong>Chapter:</strong> ' + item.quest_chapter.name
                                    + ' <strong>Topic:</strong> ' + item.quest_topic.name
                                    + '</span>'
                            };
                        })
                    };
                }

                const label = ( option.type == 1 ? 'MCQ':'SBA' );
                const url = "{{ url('') }}" + '/admin/'+label.toLowerCase() + '/create';

                return {
                    minimumInputLength: 3,
                    escapeMarkup: function (markup) { return markup; },
                    language: {
                        noResults: function () {
                            return "No "+ label +" question found, for add new "+label+" question please <a target='_blank' href='" + url +"'>Click here</a>";
                        }
                    },
                    ajax: {
                        url: '/admin/search-questions',
                        dataType: 'json',
                        type: "GET",
                        quietMillis: 50,
                        data: function (term) {
                            return {
                                term: term,
                                type: option.type
                            };
                        },
                        processResults:  processResult
                    }
                }
            }

              manage_more_text.call( $('.mcqs2') );
              manage_more_text.call( $('.another-mcqs2') );
              manage_more_text.call( $('.sbas2') );
              manage_more_text.call( $('.another-sbas2') );
        }


        enable_mcq_sba( null );

        $( "body" ).on( 'select2:close', '.mcqs2', manage_more_text );
        $( "body" ).on( 'select2:close', '.another-mcqs2', manage_more_text );
        $( "body" ).on( 'select2:close', '.sbas2', manage_more_text );
        $( "body" ).on( 'select2:close', '.another-sbas2', manage_more_text );



        // $("body").on('select2:close','.mcqs2', function() {
        //     let select = $(this);
        //     $(this).next('span.select2').find('ul').html(function() {
        //         let selected_mcq = select.select2('data').length;
        //         let total_mcq = $('[name="mcq_count"]').val();
        //         let moreq = total_mcq-selected_mcq;
        //
        //         if(moreq){
        //             $('.mcq_count').text('Add more '+moreq+' Questions');
        //             $('[name="mcq_full"]').attr('required',true);
        //         }else{
        //             $('.mcq_count').text('');
        //             $('[name="mcq_full"]').removeAttr('required');
        //         }
        //     })
        // });
        //
        //
        // $("body").on('select2:close','.another-mcqs2', function() {
        //     let select = $(this);
        //     $(this).next('span.select2').find('ul').html(function() {
        //         let selected_mcq = select.select2('data').length;
        //         let total_mcq = $('[name="mcq2_count"]').val();
        //         let moreq = total_mcq-selected_mcq;
        //
        //         if(moreq){
        //             $('.mcq2_count').text('Add more '+moreq+' Questions');
        //             $('[name="mcq2_full"]').attr('required',true);
        //         }else{
        //             $('.mcq2_count').text('');
        //             $('[name="mcq2_full"]').removeAttr('required');
        //         }
        //     })
        // });
        //
        //
        // $("body").on('select2:close','.sbas2', function() {
        //     let select = $(this)
        //     $(this).next('span.select2').find('ul').html(function() {
        //         let selected_sba = select.select2('data').length;
        //         let total_sba = $('[name="sba_count"]').val();
        //         let moreq =total_sba-selected_sba;
        //         if(moreq){
        //             $('.sba_count').text('Add more '+moreq+' Questions');
        //             $('[name="sba_full"]').attr('required',true);
        //         }else{
        //             $('.sba_count').text('');
        //             $('[name="sba_full"]').removeAttr('required');
        //         }
        //
        //     })
        // })
        //
        // $("body").on('select2:close','.another-sbas2', function() {
        //     let select = $(this)
        //     $(this).next('span.select2').find('ul').html(function() {
        //
        //         let selected_sba = select.select2('data').length;
        //         let total_sba = $('[name="sba2_count"]').val();
        //         let moreq =total_sba-selected_sba;
        //         if(moreq){
        //             $('.sba2_count').text('Add more '+moreq+' Questions');
        //             $('[name="sba2_full"]').attr('required',true);
        //         }else{
        //             $('.sba2_count').text('');
        //             $('[name="sba2_full"]').removeAttr('required');
        //         }
        //
        //     })
        // })

        function manage_more_text( ){
            let select = $(this)
            let name = $(this).data('name');

            console.log( this, name, select );

            $(this).next('span.select2').find('ul').html(function() {

                let selected_sba = select.select2('data').length;
                let total_sba = $('[name="'+name+'_count"]').val();
                console.log( total_sba, selected_sba );

                let moreq =total_sba-selected_sba;
                if(moreq){
                    $('.'+name+'_count').text('Add more '+moreq+' Questions');
                    // $('[name="'+name+'_full"]').attr('required',true);
                }else{
                    $('.'+name+'_count').text('');
                    $('[name="'+name+'_full"]').removeAttr('required');
                }

            })
        }


    })
</script>
