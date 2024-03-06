(function ($){

    let selected_exam_ids = Array.isArray( editing_data.seleted_exam_ids ) ? editing_data.seleted_exam_ids : [ ];
    let seleted_video_ids = Array.isArray( editing_data.seleted_video_ids ) ? editing_data.seleted_video_ids : [ ];

    console.log( 'selected_class_ids:prev ', seleted_video_ids );

    function getSelectedVideoIds(){
        return seleted_video_ids;
    }
    function getExamVideoIds(){
        return seleted_exam_ids;
    }

    jQuery.prototype.content_list_select2 = function ( option ){
        if( typeof jQuery.prototype.select2 == 'function' ) {
            const content_type = option && option.type;
            const elemID = $( this ).attr( 'id' );
            let contentID = $( '#' + elemID ).val();

            const opt = {
                minimumInputLength: 3,
                escapeMarkup: function (markup) { return markup; },
                ajax: {

                    url: '/admin/batch-schedules/lecture-exams-mentors',
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,

                    data: function ( term ) {
                        term.content_type = option && option.type;

                        if( term.content_type === 'Class') {
                            term.video_type = option && ( option.video_type || 1 );
                            //a
                            //term.selected_content_ids = getSelectedVideoIds();
                        } else {
                            //a
                            //term.selected_content_ids = getExamVideoIds();
                        }

                        return term;
                    },
                    beforeSend: function () {
                        //removeAndReplaceContent( content_type, contentID, $(this).val( ) );
                        // contentID =  $(this).val( );
                        //
                        // if(  content_type == 'Exam') {
                        //     console.log( 'selected_class_ids:after>exam ', selected_exam_ids );
                        // }else {
                        //     console.log( 'selected_class_ids:after>class ', seleted_video_ids );
                        // }
                    },
                    processResults: function ( data ) {

                        return {
                            results: $.map( data.topics, function ( item ) {

                                const dt = {  "text": 'Topic(s): '+ item.name, "children" : []  }

                                const contents = ( option.type == 'Class' ) && Array.isArray( item.lectures )
                                    ? item.lectures :
                                    ( ( option.type == 'Exam' ) && Array.isArray( item.exams ) ? item.exams:[] );

                                contents.map( content => dt.children.push({ id: content.id, text: content.name }) );

                                return  dt;
                            })
                        };

                    }

                }
            };


            //
            // $( '#'+elemID ).off('select2:selecting' );
            // $( '#'+elemID ).on('select2:selecting', function(){
            //     console.log( 'selected_class_ids:after++ ', contentID );
            //     removeAndReplaceContent( content_type, contentID, $(this).val( ) )
            //     contentID =  $(this).val( );
            //
            // });

            return $(this).select2( $.extend( option, opt ) );

        }



        return $(this);
    }

    function removeAndReplaceContent( content_type, contentId, replaceContentId ) {
        let _content_ids = content_type === 'Class' ? seleted_video_ids : selected_exam_ids;

        console.log( 'selected_class_ids:--- ', _content_ids, contentId );

        const ind = _content_ids.indexOf( Number(contentId) );

        if( replaceContentId ) {
            if( ind > -1 ) {
                _content_ids.splice( ind, 1,  Number( replaceContentId ) )
            }else {
                _content_ids.push( Number(replaceContentId) );
            }
        } else {
            _content_ids.splice( ind, 1 )
        }
    }

    function scheduleId(){
        return schedule_id ? schedule_id : 0;
    }

    function isEdit(){
        let act = action ? action : 'create';
        return act == 'edit';
    }

    function isDuplicating(){
        let act = action ? action : 'create';
        return act == 'duplicate';
    }

    function formAttributes(){
        return {
            action: (url || '/') + '/admin/batch-schedules/' + (  isEdit() ? 'update/' + scheduleId() : 'store' ),
            method:'POST',
            className: 'form-horizontal',
            encType: 'multipart/form-data',
        }
    }



    const domContainer = document.querySelector('#application' );
    ReactDOM.render(<Application pageTitle="Batches Schedules Create" baseUrl={ url || '/' }/>, domContainer);

    function PanelBody({children, title}){
        const styles = {
            main: { borderColor:'#eee' },
            heading: {backgroundColor: '#eee', color: 'black', borderColor: '#eee'}
        };

        return <div className="panel panel-primary" style={styles.main}>
            <div className="panel-heading" style={styles.heading}>{title}</div>
            <div className="panel-body">{children}</div>
        </div>
    }

    function ReqIcon( ){
        return <span className="fa fa-asterisk ipd-star" style={{fontSize:'9px'}}> </span>;
    }

    function enableTimeAndDate( ){




        if( $.prototype.datetimepicker ) {
            $( '.timepicker' ).datetimepicker({
                format: 'LT'
            });
        }

        if( $.prototype.datepicker ) {
            $( '.item-date' ).datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                startDate: '1900-01-01',
                endDate: '2035-01-01',
            }).on('changeDate', function(e){
                $(this).datepicker('hide');
            });
        }
    }

    function DropdownOptions({onChange, name, items, selected, id, disabled, multiple, defaultItem, required, defaultValue }){

        function getName( item ){
            return  typeof item == 'string' || typeof item == 'number' ? item : item.name;
        }

        function getVal( item ){
            return  typeof item == 'string' || typeof item == 'number' ? item : item.id;
        }

        function isSelected( item ){
            const value = getVal( item );

            if( Array.isArray( selected ) ) {
                return  selected.indexOf(value) > -1;
            }else {
                return selected == value;
            }
        }

        return (
            <div className="input-icon right">
                <select onChange={onChange} multiple={multiple}  name={name}  className="form-control" id={id} disabled={disabled} required={required} defaultValue={defaultValue}>
                    {(()=>{
                        if( defaultItem === false ) {
                            return '';
                        }else if( typeof defaultItem == 'string' ) {
                            return <option value=''>{defaultItem}</option>
                        }
                        return <option value=''>--select--</option>
                    })()}

                    {
                        Array.isArray( items ) &&
                        items.map( ( item, key ) => <option key={key} value={ getVal( item) } selected={ isSelected( item ) }>{ getName(item) }</option> )
                    }
                </select>
            </div>
        )

    }



    function ScheduleInformation( {executives, formData, onDataChange} ){

        function changeData( e ){
            formData[ e.target.name ] = e.target.value;

            if( typeof onDataChange == 'function') {
                onDataChange( formData, e.target.name );
            }
        }



        return (
            <div>
                <div className="form-group">

                    <label className="col-md-2 control-label">Schedule Name (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <input required type="text" name="name" defaultValue={formData.name} placeholder="e.g: Specila Schedule - March'20" onChange={changeData}
                                   className="form-control"/>
                        </div>
                    </div>
                    <label className="col-md-2 control-label">Schedule Sub Line </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <input required type="text" name="tag_line" defaultValue={formData.tag_line}  placeholder="e.g: Every Monday & Thursday" onChange={changeData}
                                   className="form-control"/>
                        </div>
                    </div>

                </div>

                <div className="form-group">

                    <label className="col-md-2 control-label">Schedule Contact Details (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <input required type="text" name="contact_details" defaultValue={formData.contact_details} placeholder="Schedule Contact Person and Mobile No" onChange={changeData}
                                   className="form-control"/>
                        </div>
                    </div>

                    <label className="col-md-2 control-label">Executive (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <DropdownOptions items={executives} name='executive_id' selected={formData.executive_id} onChange={changeData} required  />
                    </div>

                </div>
            </div>
        )
    }

    function CourseBatchInformation({ data, formData, onDataChange }){
        const [ dt, setData  ] = React.useState( { fac_dis_loaded: false});

        if( data.institutes && getCourses().length === 0 ) {
            instituteChanged( formData.institute_id );
        }

        if( data.batches && (  dt.batches || [] ).length === 0 ) {
            setBatches( data );
        }

        if( data.hasDisciplineFacultyChange && dt.fac_dis_loaded === false ) {
            setFacultyDiscipline( data );
        }



        function triggerDataChange( ref ){
            if( typeof onDataChange == 'function' ) {
                onDataChange(  formData, ref )
            }
        }

        function getCourses( ){
            return dt.courses || [];
        }



        // function getBatches( ){
        //     return dt.batches || [];
        // }



        function changeValue( e ){
            const key = e.target.name;
            formData[key] = e.target.value;
            if( typeof onDataChange == 'function' ) {
                onDataChange(  formData, key )
            }
        }


        function instituteChanged( institute_id ){


            if( Array.isArray( data.institutes )) {
                const institute = data.institutes.filter( item => item.id == institute_id );
                let dataValue = dt;
                if( institute.length >= 1 ) {
                    const found = institute[0];
                    dt.courses = found.courses;
                    dt.institute_id = institute_id;
                    setData( { ...dt } );
                }
            }
        }

        function setCourses( e ){
            const institute_id = e.target.value;
            changeValue( e );
            instituteChanged( institute_id );
        }

        function facultyDisciplineChange( data ) {
            return function ( e ) {
                // const name = e.target.name.replace( '[]', '' );
                // dt[name] = $(this).val( );
                //
                // setData({ ...dt, ...data });
                changeValue( e )
            }
        }

        function changeBatchesData( e ){


            if( e.target.value && dt && formData.course_id && formData.institute_id ) {

                changeValue( e );

                console.log( 'OK==============' );

                setData({ ...dt, ...{ faculties: [ ], disciplines: [ ], discipline_ids:[ ], faculty_ids: [ ]}});

                axios.get(
                    '/admin/batch-schedules/faculties-disciplines', {
                        params: {
                            batch_id: e.target.value,
                            institute_id: formData.institute_id,
                            course_id: formData.course_id,
                        }
                    })
                    .then( ( {data} ) => {
                        setFacultyDiscipline( data );
                    })
                    .catch( err => console.log( 'RERRRRRR', err ));
            }
        }


        function changeCourseData( e ){
            const course_id = e.target.value;
            dt.course_id = course_id;
            changeValue(e);
            axios.get(
                '/admin/batch-schedules/course-data',{
                   params: {
                        course_id
                   }
            })
            .then( ( {data} ) => {
                setBatches( data )
            })
            .catch( err => console.log( 'RERRRRRR', err ));

        }


        function setBatches( data ) {
            dt.batches = data.batches;
            setData( {...dt} );
        }




        function setFacultyDiscipline( data ) {
            dt.fac_dis_loaded = true;


            if( data.hasDisciplineFacultyChange ) {

                setData({ ...dt, ...data });

            } else {
                setData({ ...dt, ...{ faculties: [ ], disciplines: [ ], discipline_ids:[ ], faculty_ids: [ ]}});
            }
        }

        if( $( '#faculties, #disciplines' ).length > 0 ) {
            $( '#faculties, #disciplines' ).off('change');
            $( '#faculties, #disciplines' ).on('change', facultyDisciplineChange( data ) );
            $('#faculties, #disciplines').select2({ width: '100%' });
        }

        function getFaculties( ){
            return dt.faculties || [ ];
        }

        function getDisciplines( ){
            return dt.disciplines || [ ];
        }

        function Courses(){

            if( getCourses().length > 0 || isEdit() ) {
                return <div className="courses">
                    <div className="form-group">
                        <label className="col-md-3 control-label">Courses (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions onChange={changeCourseData} items={ getCourses() } name='course_id' selected={formData && formData.course_id}  required />
                            </div>
                        </div>
                    </div>
                </div>
            }
            return '';
        }


        class Batches extends React.Component {

            componentDidMount(){

                if( $("#batches").length > 0) {
                    $("#batches").on('change', changeBatchesData );
                    $('#batches').select2({ width: '100%' });
                }
            }

            getBatches( ){
                return this.props.batches || [];
            }


            render( ){

                if( this.getBatches().length > 0 || isEdit()  ) {
                    return <div className="batches">
                        <div className="form-group">
                            <label className="col-md-3 control-label">Batches (<ReqIcon/>) </label>
                            <div className="col-md-3">
                                <div className="input-icon right">
                                    <DropdownOptions required defaultItem="---Select Batch---"
                                                     items={ this.getBatches() }
                                                     name='batch_id' selected={formData && formData.batch_id} id='batches'  />
                                </div>
                            </div>
                        </div>
                    </div>
                }
                return '';
            }
        }


        function Faculties(){
            const faculties = dt.faculties || [ ];
            const [ disciplines, setDisciplines ] = React.useState([]);

            function setFacultyDiscipline( faculty_id ){
                console.log( 'FACULTY',faculty_id );

                const filtered = faculties.filter( fac => fac.id == faculty_id );

                if( filtered[ 0 ] ){
                    setDisciplines( filtered[ 0 ].subjects || [] );
                }
            }


            function handleFacultyChange( e ){
                setFacultyDiscipline( e.target.value );

                // const faculty_id = e.target.value;
                // const filtered = faculties.filter( fac => fac.id == faculty_id );
                //
                // if( Array.isArray(filtered) ){
                //     console.log( faculty_id, faculties, filtered );
                //     setDisciplines( filtered[ 0 ].subjects || [] );
                // }
            }

            if( formData && formData.faculty_id && disciplines.length == 0 ) {
                setFacultyDiscipline( formData.faculty_id )
            }


            function FacultyDisciplines({disciplines}){

                if( disciplines.length > 0 ) {

                    return (
                        <div className="faculty-disciplines" style={{ width: '100%' }}>
                            <div className="form-group">
                                <label className="col-md-3 control-label">{ dt && dt.is_combined ? 'Residency Discipline' : 'Discipline' } (<ReqIcon/>) </label>
                                <div className="col-md-3">
                                    <div className="input-icon right">
                                        <DropdownOptions required
                                                         items={ disciplines }
                                                         name='subject_id' selected={formData && formData.subject_id} id='faculty-disciplines' />
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                }
                return '';
            }


            if( faculties.length > 0 ) {
                return (
                    <div>

                        <div className="faculties" style={{ width: '100%' }}>
                            <div className="form-group">
                                <label className="col-md-3 control-label">{ dt && dt.faculty_label } (<ReqIcon/>) </label>
                                <div className="col-md-3">
                                    <div className="input-icon right">
                                        <DropdownOptions required
                                                         onChange={handleFacultyChange}
                                                         items={ faculties }
                                                         name='faculty_id' selected={formData && formData.faculty_id} id='faculties' />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <FacultyDisciplines disciplines={disciplines}/>

                    </div>
                )
            }
            return '';
        }

        function Disciplines(){
            let selected = null;
            let id = null;
            if( typeof formData == 'object' ) {
                selected = dt && dt.is_combined ?  formData.bcps_subject_id : formData.subject_id;
                id = dt && dt.is_combined ?  'bcps_discipline_id' : 'discipline_id';
            }

            if( getDisciplines().length > 0 )
            {
                return <div className="subjects"
                            style={{display: getDisciplines().length > 0 ? 'block' : 'none', width: '100%'}}>
                    <div className="form-group">
                        <label className="col-md-3 control-label">{dt && dt.discipline_label} (<ReqIcon/>)
                        </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions required
                                                 items={ getDisciplines( ) }
                                                 name={ dt && dt.is_combined ? 'bcps_subject_id' : 'subject_id' }
                                                 selected={ selected }
                                                 id={ id } />
                            </div>
                        </div>
                    </div>
                </div>
            }
            return '';
        }

        return (

            <div>
                <div className="years">
                    <div className="form-group">
                        <label className="col-md-3 control-label">Year (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions onChange={changeValue} items={ data.years } name='year' selected={formData && formData.year} required/>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="sessions">
                    <div className="form-group">
                        <label className="col-md-3 control-label">Session (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions onChange={changeValue} items={ data.sessions } name='session_id'  selected={formData && formData.session_id} required />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="institutes">
                    <div className="form-group">
                        <label className="col-md-3 control-label">Institute (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions onChange={ setCourses } selected={formData && formData.institute_id} items={ data.institutes } name='institute_id' required  />
                            </div>
                        </div>
                    </div>
                </div>

                <Courses/>

                <Batches batches={dt.batches || []}/>

                <Faculties/>

                <Disciplines/>

                <div className="form-group">
                    <label className="col-md-3 control-label">Active (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <DropdownOptions defaultItem={false} required
                                             items={[{name: 'Active', id:1},{name: 'InActive', id:0}]}
                                             selected={formData && formData.status}
                                             name='status' onChange={changeValue}  />
                        </div>
                    </div>
                </div>


            </div>
        );
    }

    function Content( { item,  data, onRemoveItemClick, key, index, slot_index, onAddParentItemClick, getParent } ){
        // const contType = ( item && item.parent_id ) > 0 ? 'Class' : ( ( item && item.type ) || 'Class' );
        //
        // console.log( contType );

        console.log( 'contentType@@@__', index, item.type )
        const [ contentType, setContentType ] = React.useState(  null  );

        function getContentType(){
            const type = contentType || item.type;
            return type || 'Class';
        }

        function getContents( ){
            return Array.isArray( data.topics ) ? data.topics : [];
        }

        console.log( 'contentType@@@',index, contentType )

        function  getExamsOrLectures( topic ){
            let items = [ ];
            topic = topic || { };
            const ct = getContentType();

            if( ct === 'Class' ) {
                items = topic.lectures;
            } else if ( ct === 'Exam' ) {
                items = topic.exams;
            }

            return Array.isArray( items ) ? items : [];
        }

        function itemId() {
            return isDuplicating() ? 0: ( item && item.id );
        }

        React.useEffect( function (){
            let video_type = 1;

            if( item.parent_id > 0 ) {
                const parent = getParent( item.parent_id );
                if( parent.type === 'Exam' ) {
                    video_type = 2;
                } else if( parent.type === 'Class' ) {
                    video_type = 3;
                }
            }
            $( '#exam_or_class_id-' +slot_index+'-'+ index ).content_list_select2( { width: '100%', type: getContentType(), video_type } );
        })

        function AddPrentBtn(){
            if( item && item.parent_id == 0 ) {
                return <a href='' style={{ marginTop: '2px'}} className='btn btn-sm btn-primary' onClick={(e) => {
                    e.preventDefault();
                        if( typeof onAddParentItemClick == 'function')
                            onAddParentItemClick( index )
                    }}>Add { item.type == "Exam" ? 'Solve':'Feedback'} Class
                </a>
            } else { return '' }
        }

        function TypeSelection( ){
            if( item.parent_id > 0 ) {
                const parent = getParent( item.parent_id );

                if( parent ) {
                    return <div style={{ marginTop: '10px'}}>
                        <input name={'details['+ slot_index+'][contents]['+index+'][type]'} type='hidden' value='Class'/>
                        {parent.type == 'Exam' ? 'Solve Class' : 'Feedback Class' }
                    </div>
                }

            }else {
                return <select required name={'details['+ slot_index+'][contents]['+index+'][type]'}
                        onChange={ (e) => { setContentType( e.target.value ) } }
                        className='form-control'>
                    <option value='Class' selected={ getContentType() == 'Class' }>Class</option>
                    <option value='Exam' selected={ getContentType() == 'Exam' }>Exam</option>
                </select>
            }
        }

        function getRowStyle(){
            const parent = getParent( item.parent_id )
            const common = { marginBottom: 0, padding: 0 }
            if( parent && parent.type ) {
                return { ...{ marginTop: '5px',marginBottom: '5px', color: parent.type === 'Exam'? '#d6791a' : '#05e005 ' , fontWeight: 'bold'}, ...common };
            }

            return { ...{ marginTop: '18px'}, ...common };

        }


        return (
            <div className='form-group' style={getRowStyle()}>

                <input type='hidden' name={'details['+ slot_index+'][contents]['+index+'][detail_id]'} value={ itemId() } />
                <input type='hidden' name={'details['+ slot_index+'][contents]['+index+'][parent_id]'} value={ item && item.parent_id } />

                <label className='col-md-1 control-label' style={{ width: 'auto'}}>Type</label>

                <div className='col-md-2'>
                    <TypeSelection />
                </div>

                <label className='col-md-1 control-label'>{ getContentType() }</label>
                <div className='col-md-3'>

                    <select style={getRowStyle()} required className='form-control' id={'exam_or_class_id-' +slot_index+'-'+ index } name={'details['+ slot_index+'][contents]['+index+'][class_or_exam_id]'}>

                        <option value=''>--select { getContentType() }--</option>
                        { getContents( ).map( (topic, key) => {
                                return (
                                    <optgroup label={topic.name} key={key}>
                                        {
                                            getExamsOrLectures( topic ).map( ( content, indx) =>
                                                <option key={indx} value={ content.id } selected={ item && ( item.class_or_exam_id == content.id )} >{ content.name }</option>
                                            )
                                        }
                                    </optgroup>
                                )
                            })
                        }

                    </select>
                </div>

                <label className='col-md-1 control-label'>Mentor</label>
                <div className='col-md-2'>

                    <select  required className='form-control' name={'details['+ slot_index+'][contents]['+index+'][mentor_id]'}>
                        <option value=''>--select mentor--</option>
                        { Array.isArray( data.mentors ) &&
                            data.mentors.map( (mentor,key) =>
                                <option  selected={ item && ( item.mentor_id == mentor.id )}  key={key} value={mentor.id}>{ mentor.name }</option> )
                        }
                    </select>
                </div>

                <div className='col-md-2'>
                    <AddPrentBtn />

                    {(( ) => {
                        if( index > 0 ) {
                            return <div className='pull-right' style={{ marginRight: '15px' }}>
                                <a href='' className='btn btn-danger btn-sm' onClick={ (e) => {
                                    e.preventDefault();
                                    if( typeof onRemoveItemClick == 'function' ) {
                                        onRemoveItemClick( index );
                                    }
                                }}>&times;</a>
                            </div>
                        }
                    })( )}
                </div>


            </div>
        );

    }

    function LectureOrVidoeTopic({ item, index, data, onDataChange, changeValue, removeItem }){



        const initial_contents = item && Array.isArray( item.contents )  ? item.contents:[];

        const [ contents, setContents ] = React.useState( initial_contents );



        function getContents(){
            console.log( 'contentType&&', contents );
            return Array.isArray(contents) ? contents:[];
        }

        function triggerDataChange( key, value ){
            if( typeof onDataChange == 'function' ) {
                const data = { };
                data[key] = value;
                onDataChange( data );
            }
        }

        if( contents.length === 0 ) {

            console.log( 'Items-Before', data );

            setContents([ data ]);
        }

        function addNewContent( e ){
            e.preventDefault();

            const item = {
                "id": null,
                "slot_id": null,
                "type":"Class",
                "class_or_exam_id": 0,
                "mentor_id": null,
                "parent_id": 0,
                "priority": null,
            };

            // setContents( [...contents, data] );
            setContents( [...contents, item] );
        }

        function removeContent( index ){
            let items = contents;
            if( confirm('Are you sure?')) {
                items.splice( index, 1);
                setContents( [...items] );
            }
        }

        React.useEffect( function ( ){
            enableTimeAndDate( );
        });

        function itemId() {
            return isDuplicating() ? 0: ( item && item.id );
        }

        function RemoveBtn() {
            if( index === 0)
                return '';
            return <div className='pull-right' style={{marginRight: '15px'}}>
                <a className='btn btn-warning btn-sm' href='' onClick={(e)=> {
                    if( confirm('Are your sure want to remove this date and time?') )  {
                        e.preventDefault();
                        removeItem( index );
                    }
                }}>Remove</a>
            </div>
        }

        function getParent( parent_id ){
            const data = contents.filter( item => item.id === parent_id );
            return data[0] || null;
        }

        function onAddParentItemClick( index ){

            if( confirm( 'Are you sure?' )) {

                const parent = contents[index] || { };

                console.log( 'data--', contents[index], data );

                const item = {
                    "id": null,
                    "slot_id":parent.slot_id,
                    "type":"Class",
                    "class_or_exam_id": 0,
                    "mentor_id":parent.mentor_id,
                    "parent_id":parent.id,
                    "priority": null,
                    "parent": parent
                };

                contents.splice( (1+index), 0, item );
                setContents([ ...contents ]);

                contents.map( (itm,i) => console.log( 'contentType##',i, itm.type ) );

            }
        }


        console.log( '==========', getContents());

        return  (
            <div className='container-fluid' style={{
                border: '1px solid #ccc',
                borderRadius: '5px',
                paddingTop: '10px',
                paddingBottom: '10px',
                marginBottom: '30px' }}
            >
                <div className='form-group' style={{marginBottom: 0}}>
                    <label className='col-md-1 control-label' style={{ width: 'auto'}}>Date</label>
                    <div className='col-md-2'>
                        <input type='hidden' value={itemId()} name={'details[' + index + '][slot_id]'}/>
                        <input required className='form-control item-date' type='text' value={item && item.date} name={'details[' + index + '][date]'} placeholder='Date' onChange={(e) =>
                            triggerDataChange( e.target.name, e.target.value )} />
                    </div>
                    <label className='col-md-1 control-label' style={{ width: 'auto'}}>Time</label>
                    <div className='col-md-2'>
                        <input required className='form-control timepicker' type='text' value={item && item.time} name={'details[' + index + '][time]'} placeholder='Time'onChange={(e) =>
                            triggerDataChange( e.target.name, e.target.value )} />
                    </div>
                    <RemoveBtn/>

                </div>
                <hr style={{marginTop: '10px'}}/>
                <div style={{ width:"100%"}}>
                    {  getContents().map( (content, key ) => <Content onAddParentItemClick={onAddParentItemClick} getParent={getParent} item={content} slot_index={ index } index={key} key={key} data={data} onRemoveItemClick={removeContent}/> ) }
                    <a href='' onClick={addNewContent} className='btn btn-info'>+Add More</a>
                </div>

            </div>
        )
    }

    function LectureOrVidoeTopics({dataLoaded, onDataChange, formData}){
        const [ topicsData, setTopicsData ] = React.useState( { data:{ exams:[ ], lectures: [ ], mentors: [ ] }, loaded: false } );
        const [ topicItems, setTopicItems ] = React.useState( [] );


        function getTopicItems( ){
            if( Array.isArray(topicItems) && topicItems.length > 0) {
                return topicItems
            }
            return [];
        }

        React.useEffect( () => {

            axios.get( '/admin/batch-schedules/lecture-exams-mentors' )
                .then( ( {data} ) => {
                    if( !topicsData.loaded ) {
                        setTopicsData( { data, loaded: true } );
                        if( typeof dataLoaded == 'function' ){
                            dataLoaded( );
                        }
                    }
                })
                .catch( err => console.log( 'RERRRRRR', err ));

        }, [!topicsData.loaded] )

        function addLectureOrVideoTopic( e ){
            e.preventDefault( );
            const item = { type : 'Class', contents: [], date: '', time: '' };
            let items = [ ...topicItems, item ];
            setTopicItems( items );
        }

        function removeLectureOrVideoTopic( item_index ){
            topicItems.splice( item_index, 1);
            setTopicItems( [ ...topicItems ] );
        }


        if( Array.isArray( topicItems ) && topicItems.length === 0 ) {
            if( formData && formData.details ) {

                if( Array.isArray(formData.details) && formData.details.length > 0 ) {
                    setTopicItems(  [ ...formData.details ] );
                }

            }else {

                const item = { type : 'Class', contents: [], date: '', time: '' };
                setTopicItems( [ ...topicItems, item ] );

            }
        }

        return (
            <div className='container-fluid'>

                <div className='row'>

                    { getTopicItems().map( (item, key) => <LectureOrVidoeTopic
                        onDataChange={onDataChange}
                        item={item}
                        index={key} key={key}
                        removeItem={removeLectureOrVideoTopic}
                        data={ topicsData.data } /> )
                    }

                    { ( () =>
                         <a href='' disabled={topicsData == null} className="btn btn-success" onClick={addLectureOrVideoTopic}>+ Add Exams or Classes</a>
                    ) () }

                </div>
            </div>
        );
    }

    function Application( { pageTitle, baseUrl } ){

        const [ data, setData ] = React.useState(null);
        const csrf = document.querySelector('meta[name="csrf-token"]');
        const csrf_token = csrf && csrf.getAttribute('content');

        axios.defaults.baseURL = this.props.baseUrl;
        axios.defaults.headers.common["X-CSRF-TOKEN"] = csrf_token;
        axios.defaults.headers.common["Accept"] = 'application/json';
        axios.defaults.headers.common["ContentType"] = 'application/json';

       React.useEffect( () => {
            onProgressing({ loaded: false });
            axios.get( '/admin/batch-schedules/get-data', {
                params: {
                    schedule_id: scheduleId(),
                    action: isEdit() ? 'edit' : 'create'
                }
            }).then( ({data}) => {

                if( data !== null ) {
                    data.loaded = true;
                    setData( data );
                }
            }).catch( err => console.log( err ));

        } , [data === null] )


        function getExecutives( ){

            return data && data.executive_list;
        }

        function getData(){
            return data || [];
        }
        
        function getSubmitData( ) {

        }

        let ClassData = [];

        let formData = editing_data ? editing_data: {};
        let id = id ? id : 0;
        let action = action ? action : 'create';

        function submitData( e ){
            e.preventDefault(  );

            axios.post( '/admin/batch-schedules/store', formData )
                .then( ({data}) => {
                    if( data !== null ) {
                        // setData( data );

                    }
                })
                .catch( err => console.log( err ));
        }



        function onProgressing({ loaded }){


            setData({ ...data, ...{ loaded } });
        }

        function submitDisabled( ) {
            if( data ) {
                return !data.loaded;
            }
            return data === null;
        }

        const InputType = () => isEdit() ? <input name="_method" type="hidden" value="PUT"/> : ''

        return (
            <div className="portlet">
                <div className="portlet-title">
                    <div className="caption">
                        <i className="fa fa-reorder"></i>{ pageTitle }
                    </div>
                </div>
                <div className="portlet-body form">
                    {/*<form className="form-horizontal" method="post" encType="multipart/form-data" onSubmit={ submitData }>*/}
                    <form {...formAttributes()}>
                        <div className="form-body">

                            <InputType/>
                            <input name="_token" type="hidden" value={csrf_token} />

                            <PanelBody title="Select Batches Schedules Information">
                                <ScheduleInformation executives={ getExecutives() } formData={formData} onDataChange={(data, ref) =>  formData = data }/>
                            </PanelBody>

                            <PanelBody title="Select Batches Schedules Course Information">
                                <CourseBatchInformation data={ getData() } formData={formData} onDataChange={(data, ref) =>  formData = data }/>
                            </PanelBody>

                            <PanelBody title="Add/Remove class or exam">
                                <LectureOrVidoeTopics formData={ formData } onProgressing={onProgressing}  onDataChange={(data, ref) =>  formData = data }/>
                            </PanelBody>

                            <div className='row'>
                                <div className='col-md-6'>
                                    <button disabled={submitDisabled()} className='btn btn-lg btn-success'>Submit</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        )
    }
})(jQuery);