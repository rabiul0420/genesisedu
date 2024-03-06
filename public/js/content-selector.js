// require( './select2' );


let script = document.createElement("script" );
script.src = '/js/select2.js';
script.type = 'text/javascript';
document.head.appendChild( script );

let style = document.createElement("link" );
style.href = '/css/select2.css';
style.rel = 'stylesheet';
style.type = 'text/css';
document.head.appendChild( style );


window.content_manager = window.content_manager || function ( options ){
    let year_selection = getConfig('year.selection', '#year' );
    let institute_selection = getConfig('institute.selection', '#institute_id' );
    let course_selection = getConfig('course.selection', '#course_id' );
    let session_selection = getConfig('session.selection', '#session_id' );
    let batch_selection = getConfig('batch.selection', '#batch_id' );
    let faculty_selection = getConfig('faculty.selection', '#faculty_id' );
    let subject_selection = getConfig('subject.selection', '#subject_id' );
    let bcps_subject_selection = getConfig('bcps_discipline.selection', '#bcps_subject_id' );


    let year = getConfig('year.selected', $(year_selection).val() );
    let institute_id = getConfig('institute.selected', $(institute_selection).val() );
    let course_id = getConfig('course.selected', $(course_selection).val() );
    let session_id = getConfig('session.selected', $(session_selection).val() );
    let batch_id = getConfig('batch.selected', $(batch_selection).val() );

    let faculty_id = getConfig('faculty.selected', $(faculty_selection).val() );
    let selected_subject_ids = getConfig('subject.selected', $(subject_selection).val() );
    let bcps_subject_id = getConfig('bcps_discipline.selected', $(bcps_subject_selection).val() );

    let output_data = {
        institute_id,
        course_id,
        session_id,
        batch_id,
        year,
        selected_subject_ids,
        faculty_id,
        subject_id: selected_subject_ids,
        bcps_subject_id
    };

    function getConfig( segment, defaultValue ){
        let value = null;

        function  fetchVal( key ){
            if( value === null ) {
                value = typeof options[ key ] != 'undefined' ? options[key] : null;
            } else {
                value = typeof value[ key ] != 'undefined' ? value[key] : null;
            }
        }

        String( segment ).split('.').map( fetchVal );
        return value || defaultValue;
    }

    function setOutputData( moreData ){
        output_data = { ...{
            institute_id,
            course_id,
            session_id,
            batch_id,
            year,
            selected_subject_ids,
            faculty_id,
            subject_id: selected_subject_ids,
            bcps_subject_id
        }, ...(moreData || {}) };
    }

    let onInit = getConfig('onInit' );

    if( typeof onInit == 'function' ) {
        setOutputData();
        onInit.call( this,  output_data );
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });

    jQuery.prototype.loadHtml = async function ( url, params, callback ){

        if( url ) {
            return $.get( url, params, ( data, xhr ) => {
                $( this ).html( data );
                if( typeof callback == 'function' ) {
                    callback(  xhr );
                    return Promise.resolve('success');
                }
            })
        }

        return Promise.reject('Failed, no url provided');
    };


    const runCallback = ( segment, params ) => {

        let callback = getConfig( segment );
        console.log('DDD', callback, segment );
        if( typeof callback == 'function' ) {
            callback = callback.bind( this );
            callback.apply( this, params )
        }
    }


    function onChangeYear( ) {
        year = $( this ).val( );
        setOutputData( );
        runCallback( 'year.onChange', [output_data, 'year'] )
    }

    function onChangeInstitute( ){
        institute_id = $( this ).val( );
        setOutputData( );
        runCallback( 'institute.onChange', [output_data, 'institute' ] )

        const url = getConfig('course.url' );
        const selector = getConfig('institute.target', '#courses' );

        $(selector).loadHtml( url, { institute_id });
    }




    async function load_batches( ){
        const year = $( '#year' ).val( );

        console.log( 'Y I C S : ', year , institute_id , course_id , session_id);

        const url = getConfig('batch.url' );
        const selector = getConfig('batch.target', '#batches' );

        if( year && institute_id && course_id && session_id) {
            return $(selector).loadHtml( url, { year, institute_id, course_id, session_id }, async ( ) => {

                setOutputData( {  year });
                runCallback( 'batch.onLoad'[ output_data ]);

                if( typeof jQuery.prototype.select2 == 'function' ) {
                    $( selector ).find( 'select' ).select2( );
                }

            })
        }
    }

    function onChangeCourse( ){
        course_id = $( this ).val( );
        year = $( '.year' ).val( );

        if( !year ) {
            year = $( '#year' ).val( );
        }

        if( !year ) {
            year = $( '[name="year"]' ).val( );
        }

        setOutputData( );
        runCallback( 'course.onChange', [output_data] );

        load_faculties( );
        load_bcps_discipline( );

        const url = getConfig('session.url' );
        const selector = getConfig('course.target', '#sessions' );
        $(selector).loadHtml(url, {course_id , year})
    }

    function onChangeSession( ){
        session_id = $(this).val();
        setOutputData();
        runCallback('session.onChange', [ output_data ] );
        load_batches( );
    }

    function onChangeBatch( ){
        batch_id = $( this ).val( );
        setOutputData( );
        load_faculties();
        load_bcps_discipline();


        runCallback('batch.onChange', [ output_data, 'batch' ] );
    }


    function onChangeFaculty( ){
        faculty_id = $( this ).val( );
        setOutputData( );
        runCallback('faculty.onChange', [ output_data, 'faculty' ] );
    }

    function onChangeDiscipline( ){
        selected_subject_ids = $( this ).val( );
        setOutputData( );
        runCallback('discipline.onChange', [ output_data, 'discipline' ] );
    }

    function onChangeBCPSDiscipline( ){
        bcps_subject_id = $( this ).val( );
        setOutputData( );
        runCallback('bcps_discipline.onChange', [ output_data, 'bcps_discipline' ] );
    }

    function load_faculties( ){

        const url = getConfig('faculty.url' );
        const selector = getConfig('faculty.target', '#faculties' );

        if( institute_id && course_id ) {
            $(selector).loadHtml( url, {  institute_id, course_id, batch_id }, ( ) => {
                setOutputData( {  year });
                runCallback( 'faculty.onLoad'[ output_data ])

                if( typeof jQuery.prototype.select2 == 'function' ) {
                    $( selector ).find( 'select' ).select2( );
                }
            })
        }
    }


    function load_bcps_discipline( ){

        const url = getConfig('bcps_discipline.url' );
        const selector = getConfig('bcps_discipline.target', '#bcps_disciplines' );

        if( institute_id && course_id ) {
            $(selector).loadHtml( url, {  institute_id, course_id, batch_id }, ( ) => {
                runCallback( 'bcps_discipline.onLoad'[ output_data ])

                if( typeof jQuery.prototype.select2 == 'function' ) {
                    $( selector ).find( 'select' ).select2( );
                }
            })
        }
    }


    $("body").on( "change", "#institute_id", onChangeInstitute );
    $("body").on( "change", "#course_id", onChangeCourse );
    $("body").on( "change", "#session_id", onChangeSession );
    $("body").on( "change", "#year", onChangeYear );
    $("body").on( "change", "#faculty_id", onChangeFaculty );
    $("body").on( "change", "#discipline_id, #subject_id", onChangeDiscipline );
    $("body").on( "change", "#bcps_discipline_id, #bcps_subject_id", onChangeBCPSDiscipline );

    if( typeof jQuery.prototype.select2  == 'function' ) {
        $("#batch_id").select2( );
    }else {
        $.getScript('/js/select2.js', function (){
            $("#batch_id").select2( );
        })
    }

    $("body").on( "change", "#batch_id", onChangeBatch );

    //$("body").on( "change", "#faculty_id", faculty_discipline_manager().onChangeFaculty );
    // $("body").on( "change", "#subject_id", faculty_discipline_manager().onChangeDiscipline );

    //$( '.select2' ).select2();
    App.handleSelect2AllSelector();
};