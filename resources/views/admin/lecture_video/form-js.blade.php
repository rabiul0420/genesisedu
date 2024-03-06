<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

<script src="{{ asset('js/content-selector.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/select2-configs.js') }}" type="text/javascript"></script>
<script type="text/javascript">



    $(document).ready(function() {

        function enableClassSearching( input ){
            $('.topic-selection').select2( select2Configs.classSearching( input ));
        }

        content_manager({
            onInit: enableClassSearching,
            year: {
                onChange: input => {
                    $('[name="classes"]').html('');
                    enableClassSearching( input );
                }
            },
            course: {
                url: '/admin/lecture-video/courses'
            },
            session: {
                url: '/admin/lecture-video/sessions',
                onChange: input => {
                    $('[name="classes"]').html('');
                    enableClassSearching( input );
                }
            }
        });


        function LectureLinkManager( id ){

            let events = [];

            function getUID(length) {
                var result           = '';
                var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;

                for ( var i = 0; i < length; i++ ) {
                    console.log()
                    result += characters.charAt(Math.floor( Math.random() * charactersLength ));
                }
                return result;
            }

            function LectureVideoInputItem(data){

                const uid = getUID(20);

                let input; data = data || { };

                const onChange = typeof data.onChange == 'function' ? data.onChange : () => {};

                events.push({
                    selector: `[data-input-id="${uid}"]`,
                    action: onChange,
                    type: 'input'
                });



                input = `<div class="form-group">`;
                    input += `<label class="col-md-3 control-label">`;
                        input += data.label;
                        input +=  data.isRequired ? `(<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>)`:``;
                    input += `</label>`;

                    input += `<div class="col-md-3">`;
                        input += `<div class="input-icon right">`;
                            input += `<input type="text" name="${data.name}" value="${data.value}" class="form-control" data-input-id="${uid}" >`;
                        input += `</div>`;
                    input += `</div>`;
                input += `</div>`;
                return input;
            }


            function LectureVideoContent({data}){

                data = data || {};

                let vc = '<div class="row" style="border: 1px solid red; margin-bottom: 30px">';

                    vc += LectureVideoInputItem({
                        label: 'Lecture Video Title',
                        isRequired: true,
                        name: 'title' ,
                        value: data.title,
                        onChange: (e) => {
                            console.log( e.target.name, e.target.value )
                        }
                    });

                    vc += LectureVideoInputItem({
                        label: 'Lecture Web Address',
                        isRequired: true,
                        name: 'link' ,
                        value: data.link,
                    });

                    vc += LectureVideoInputItem({
                        label: 'Password',
                        isRequired: true,
                        name: 'password' ,
                        value: data.password,
                    });

                vc += `</div>`;

                return vc;
            }


            const lectureItems = [

                {
                    title: "Title 1",
                    link: "Link 1",
                    password: "4878sds",
                },

                {
                    title: "Title 2",
                    link: "Link 2",
                    password: "isuiyirsd",
                }

            ];


            let result = '';


            result = lectureItems.map( video => {
                return  LectureVideoContent({ data: video });
            })

            result += LectureVideoContent({ data:{ title: '', link:'', password: '778' }});




            function render( ){
                $( id ).append( result );


                console.log( events );

                events.forEach( e => {
                    $(e.selector).off( e.type );
                    $(e.selector).on( e.type, e.action );
                });

                events = [];
            }

            render( );
        }


        LectureLinkManager( '#lecture-video-links' );


        $("body").on( "change", "[name='classes']", function() {
            var class_id = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '/admin/change-mentor',
                dataType: 'HTML',
                data: {class_id},
                success: function( data ) {
                    $('.mentor').html(data);
                    
                }
            });
        })

    })
</script>