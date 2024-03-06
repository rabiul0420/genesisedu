import React, { useState } from 'react';
import DropdownOptions from "../DropdownOptions";
import ReqIcon from "../ReqIcon";

export default ({ data, formData, onDataChange, onInputChange }) => {


    //
    // return null;


    const [ dt, setData  ] = useState( { fac_dis_loaded: false});
    const [ sessions, setSessions ] = useState( null );

    React.useEffect(()=> {

        const _sessions = getSessionsByCourseId( formData && formData.course_id );
        instituteChanged( formData && formData.institute_id );
        setSessions( _sessions );

    },[ data ] )



    if( data.institutes && getCourses().length === 0 ) {
        instituteChanged( formData.institute_id );
    }

    if( data.batches && (  dt.batches || [] ).length === 0 ) {
        setBatches( data );
    }

    if( data.hasDisciplineFacultyChange && dt.fac_dis_loaded === false ) {
        setFacultyDiscipline( data );
    }


    function getCourses( ){
        return dt.courses || [];
    }


    function changeValue( e ){
        const key = e.target.name;
        formData[key] = e.target.value;

        if( typeof onDataChange == 'function' ) {
            onDataChange(  formData, key )
        }

        if( typeof onInputChange == 'function' ) {
            onInputChange(  e )
        }
    }

    function getCoursesByInstituteId( institute_id ){
        if( Array.isArray( data.institutes ) ) {
            const institute = data.institutes.filter( item => item.id == institute_id );

            dt.institute_id = institute_id;

            if( institute.length >= 1 ) {
                const found = institute[0] || {};
                return found.courses;
            }

        }
    }


    function instituteChanged( institute_id ){
        dt.courses = getCoursesByInstituteId( institute_id ) || [];
        setData( { ...dt } );
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

    function getSessionsByCourseId( course_id ){

        let courses = data.courses || [];
        // if( courses === null ) {
        //     courses= dt.courses || [];
        // }



        const courses_filtered = courses.filter( course => course.id == course_id );


        const course =  courses_filtered[ 0 ] || [ ];
        if( course && course.sessions ) {
            return course.sessions;
        }
        return [];
    }

    //
    // function setSessionsByCourses( course_id ){
    //     const courses = data.courses || [];
    //     const courses_filtered = courses.filter( course => course.id == course_id );
    //     const course = courses_filtered[ 0 ] || null;
    //     if( course && course.sessions ) {
    //         setSessions( course.sessions );
    //     }
    // }
    //

    function changeCourseData( e ){
        const course_id = e.target.value;
        dt.course_id = course_id;
        changeValue( e );

        setSessions( getSessionsByCourseId( course_id ) );

        axios.get(
            '/admin/batch-schedules/course-data',{
                params: { course_id }
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

        if( getCourses( ).length > 0 || isEdit() ) {
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

    function Sessions( ){
        const data_sessions = sessions || [ ];

        return (
            <div className="sessions">
                <div className="form-group">
                    <label className="col-md-3 control-label">Session (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <DropdownOptions onChange={changeValue} items={ data_sessions } name='session_id'  selected={formData && formData.session_id} required />
                        </div>
                    </div>
                </div>
            </div>
        );
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
        const [ disciplines, setDisciplines ] = useState([]);

        function setFacultyDiscipline( faculty_id ){


            const filtered = faculties.filter( fac => fac.id == faculty_id );

            if( filtered[ 0 ] ){
                setDisciplines( filtered[ 0 ].subjects || [] );
            }
        }


        function handleFacultyChange( e ){
            setFacultyDiscipline( e.target.value );

            const faculty_id = e.target.value;
            const filtered = faculties.filter( fac => fac.id == faculty_id );

            if( Array.isArray(filtered) ){

                setDisciplines( filtered[ 0 ].subjects || [] );
            }
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
                                                     onChange={onInputChange}
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
                                                     onChange={onInputChange}
                                                     items={ faculties }
                                                     name='faculty_id' selected={formData && formData.faculty_id} id='faculties' />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/*<FacultyDisciplines disciplines={disciplines}/>*/}

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
                                             onChange={onInputChange}
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

            <Sessions/>

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