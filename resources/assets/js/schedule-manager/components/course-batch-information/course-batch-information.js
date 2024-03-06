import React, { useState } from 'react';
import DropdownOptions from "../DropdownOptions";
import ReqIcon from "../ReqIcon";
import {ShowErrors} from "../../Application";

// export default ({ data, formData, onDataChange, onInputChange }) => {
export default class extends React.Component {


    constructor( props ) {
        super( props);
        this._courses = [ ];
        this.state = {
            dt: {},
            selected: props.formData,
            courses: [],
            sessions: null,
        }

        this.setCourses = this.setCourses.bind( this );
        this.changeBatchesData = this.changeBatchesData.bind( this );
        this.changeCourseData = this.changeCourseData.bind( this );
        this.changeValue = this.changeValue.bind( this );
    }

    componentDidUpdate( prevProps, prevState, snapshot ) {

        if( this.props !== prevProps ) {
            this.instituteChanged( this.props.formData.institute_id );



            //const sessions = this.getSessionsByCourseIdAndYear( this.props.formData.course_id, this.props.formData.year );



            if(  typeof  this.state.dt.institutes == 'undefined' ) {
                this.setState({ sessions: this.props.data.sessions, dt: this.props.data });
            }

        }
    }


    componentDidMount() {


        // const sessions = this.getSessionsByCourseIdAndYear( this.props.formData && this.props.formData.course_id, this.props.formData && this.props.formData.year );
        // this.setState( { sessions } );

        if( ( this.state.selected.year && this.state.selected.course_id && this.state.selected.institute_id ) ) {
            this.loadBatches( {
                year: this.state.selected.year || '',
                course_id: this.state.selected.course_id || '',
                institute_id: this.state.selected.institute_id || '',
            });
        }

        // this.instituteChanged( this.props.formData.institute_id );
        // if( this.props.data.hasDisciplineFacultyChange && this.state.dt.fac_dis_loaded === false ) {
        //     this.setFacultyDiscipline( this.props.data );
        // }
        //
        // if( $( '#faculties, #disciplines' ).length > 0 ) {
        //     $( '#faculties, #disciplines' ).off('change');
        //     $( '#faculties, #disciplines' ).on('change', this.facultyDisciplineChange( this.props.data ) );
        //     $('#faculties, #disciplines').select2({ width: '100%' });
        // }
    }

    getCourses( ){
        return this.state.courses || [];
    }

    changeValue( e ){

        const key = e.target.name;
        this.props.formData[key] = e.target.value;
        const selected = this.state.selected;

        if( e.target.name == 'batch_id' ) {
            this.props.formData['faculty_id'] = '';
            this.props.formData['subject_id'] = '';
            this.props.formData['bcps_subject_id'] = '';
            selected.faculty_id = '';
            selected.subject_id = '';
            selected.bcps_subject_id = '';
        }


        if( key == 'year' ) {
            this.getSessionsByCourseIdAndYear( );
        }


        selected[key] = this.props.formData[key];

        this.setState({ selected });

        if( typeof this.props.onBatchInfoChange == 'function' ) {
            this.props.onBatchInfoChange(  this.state.selected );
        }

        if( [ 'year','course_id', 'institute_id' ].indexOf(key) > -1
            && ( this.state.selected.year && this.state.selected.course_id && this.state.selected.institute_id ) ) {
            this.loadBatches( {
                year: this.state.selected.year || '',
                course_id: this.state.selected.course_id || '',
                institute_id: this.state.selected.institute_id || '',
            });
        }
    }

    loadBatches( params ){
        axios.get( '/admin/batch-schedules/course-data',{ params })
            .then( ( {data} ) => {
                this.setBatches( data )
            })
            .catch( err => console.log( 'RERRRRRR', err ));

    }


    getCoursesByInstituteId( institute_id ){
        if( Array.isArray( this.props.data.institutes ) ) {
            const institute = this.props.data.institutes.filter( item => item.id == institute_id );
            if( institute.length >= 1 ) {
                const found = institute[0] || {};
                return found.courses;
            }
        }
    }


    instituteChanged( institute_id ){
        const dt = this.state.dt;
        dt.institute_id = institute_id;


        const courses = this.getCoursesByInstituteId( institute_id ) || [];
        this._courses = courses;


        this.setState( { courses: courses, dt: dt });


    }

    setCourses( e ){
        const institute_id = e.target.value;
        this.changeValue( e );
        this.instituteChanged( institute_id );
    }

    facultyDisciplineChange( data ) {
        // return  ( e ) {
        //     // const name = e.target.name.replace( '[]', '' );
        //     // dt[name] = $(this).val( );
        //     //
        //     // setData({ ...dt, ...data });
        //     changeValue( e )
        // }
    }

    changeBatchesData( e ){

        const dt = this.state.dt;

        if( e.target.value && dt && this.props.formData.course_id && this.props.formData.institute_id ) {

            this.changeValue( e );

            this.setState({ ...dt, ...{ faculties: [ ], disciplines: [ ], discipline_ids:[ ], faculty_ids: [ ]}});

            axios.get(
                '/admin/batch-schedules/faculties-disciplines', {
                    params: {
                        batch_id: e.target.value,
                        institute_id: this.props.formData.institute_id,
                        course_id: this.props.formData.course_id,
                    }
                })
                .then( ( {data} ) => {
                    this.setFacultyDiscipline( data );
                })
                .catch( err => console.log( 'RERRRRRR', err ));
        }
    }

    getSessionsByCourseIdAndYear( course_id, year ){

        if( !year ) {
            year = document.querySelector(`[name="year"]`)
                ? document.querySelector(`[name="year"]`).value:'';
        }

        if( !course_id ) {
            course_id = document.querySelector(`[name="course_id"]`)
                ? document.querySelector(`[name="course_id"]`).value:'';
        }

        let courses = this.state.courses || this._courses;
        const courses_filtered = courses.filter( course => ( course.id == course_id ) );

        const course =  courses_filtered[ 0 ] || {};
        if( course && course.course_sessions ) {
            //console.log( 'year, course_id', year, course_id, course.course_sessions, course.course_sessions.filter( cs => cs.year == year) )
            return course.course_sessions.filter( cs => cs.year == year);
        }

        return [];
    }

    changeCourseData( e ){

        const dt = this.state.dt;
        // const course_id = e.target.value;

        const year = document.querySelector(`[name="year"]`)
            ? document.querySelector(`[name="year"]`).value:'';

        const course_id = document.querySelector(`[name="course_id"]`)
            ? document.querySelector(`[name="course_id"]`).value:'';

        dt.course_id = course_id;
        this.changeValue( e );

        this.setState( { sessions: this.getSessionsByCourseIdAndYear( course_id, year ), dt });

    }


    setBatches( data ) {
        const dt = this.state.dt;
        dt.batches = data.batches;
        this.setState( {dt} );
    }

    setFacultyDiscipline( data ) {

        const dt = this.state.dt;
        dt.fac_dis_loaded = data.hasDisciplineFacultyChange;

        if( data.hasDisciplineFacultyChange === false ) {
            dt.faculties = [];
            dt.disciplines = [];
            dt.discipline_ids = [];
            dt.faculty_ids = [];
        }

        this.setState({ dt: {...dt, ...data} });


    }

    getFaculties( ){
        return this.state.dt.faculties || [ ];
    }

    getDisciplines( ){
        return this.state.dt.disciplines || [ ];
    }


    getDataValue( key ){
        const data = this.props.data || {};
        return data[ key ];
    }

    getYears( key ){
        const data = this.props.data || {};
        if( data[ 'years' ]) {
            return Array.isArray(data[ 'years' ])
                ? data['years']
                : (
                    typeof data[ 'years' ] == 'object'
                        ? Object.keys(data[ 'years' ]).map( (y) => data['years'][y])
                        : []
                )
        }
    }

    render( ){

        return (

            <div>
                <div className="years">
                    <div className="form-group">
                        <label className="col-md-3 control-label">Year (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions onChange={this.changeValue} items={ this.getYears('years' ) || [] } name='year'
                                                 selected={this.props.formData && this.props.formData.year} required/>
                            </div>
                            <ShowErrors name='year'/>
                        </div>
                    </div>
                </div>

                <div className="institutes">
                    <div className="form-group">
                        <label className="col-md-3 control-label">Institute (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions
                                    keyBy='id'
                                    onChange={ this.setCourses } selected={this.props.formData && this.props.formData.institute_id}
                                    items={ this.props.data.institutes } name='institute_id' required  />
                            </div>
                            <ShowErrors name='institute_id'/>
                        </div>
                    </div>
                </div>

                <Courses courses={ this.getCourses() } changeCourseData={this.changeCourseData} selected_course_id={this.props.formData && this.props.formData.course_id}/>

                <Sessions sessions={this.state.sessions} changeValue={this.changeValue} selected_session_id={this.props.formData && this.props.formData.session_id}/>

                <Batches batches={ this.state.dt.batches || []} changeBatchesData={this.changeBatchesData} formData={this.props.formData} />

                <Faculties faculties={this.getFaculties()} formData={this.props.formData} onInputChange={this.changeValue} faculty_label={ this.state.dt.faculty_label }/>

                <Disciplines disciplines={this.getDisciplines()}
                             formData={this.props.formData}
                             onInputChange={this.changeValue}
                             is_combined={this.state.dt.is_combined}
                             discipline_label={this.state.dt.discipline_label}/>

                <div className="form-group">
                    <label className="col-md-3 control-label">Active (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <DropdownOptions defaultItem={false} required
                                             items={[{name: 'Active', id:1},{name: 'InActive', id:0}]}
                                             selected={this.props.formData && this.props.formData.status}
                                             name='status' onChange={this.props.changeValue}  />
                        </div>
                        <ShowErrors name='status'/>
                    </div>
                </div>
            </div>

        );
    }
}

class Batches extends React.Component {

    componentDidUpdate(prevProps, prevState, snapshot) {
        if( this.props != prevProps) {
            if( $("#batches").length > 0) {
                $("#batches").off( 'change' );
                $('#batches').select2({ width: '100%' });
                $("#batches").on('change', this.props.changeBatchesData );
            }
        }
    }


    componentDidMount(){
        if( $("#batches").length > 0) {
            $('#batches').select2({ width: '100%' });
            $("#batches").on('change', this.props.changeBatchesData );
        }
    }

    getBatches( ){
        return this.props.batches || [];
    }


    render( ){

        if( this.getBatches( ).length > 0 || isEdit( )  ) {

            return <div className="batches">
                <div className="form-group">
                    <label className="col-md-3 control-label">Batches (<ReqIcon/>) </label>
                    <div className="col-md-3">
                        <div className="input-icon right">
                            <DropdownOptions required defaultItem="---Select Batch---"
                                             keyBy='id'
                                             items={ this.getBatches() }
                                             name='batch_id' selected={this.props.formData && this.props.formData.batch_id} id='batches'  />
                            <ShowErrors name='batch_id'/>
                        </div>

                    </div>
                </div>
            </div>

        }

        return '';
    }
}

const Disciplines = React.memo(({formData, onInputChange, disciplines, discipline_label, is_combined }) => {
    let selected = null;
    let id = null;

    disciplines = disciplines || [];

    if( typeof formData == 'object' ) {
        selected = is_combined ?  formData.bcps_subject_id : formData.subject_id;
        id = is_combined ?  'bcps_discipline_id' : 'discipline_id';
    }

    if( disciplines.length > 0 )
    {
        return <div className="subjects"
                    style={{display: disciplines.length > 0 ? 'block' : 'none', width: '100%'}}>
            <div className="form-group">
                <label className="col-md-3 control-label">{discipline_label} (<ReqIcon/>)
                </label>
                <div className="col-md-3">
                    <div className="input-icon right">
                        <DropdownOptions required
                                         keyBy='id'
                                         onChange={onInputChange}
                                         items={ disciplines }
                                         name={ is_combined ? 'bcps_subject_id' : 'subject_id' }
                                         selected={ selected }
                                         id={ id } />
                        <ShowErrors name={ is_combined ? 'bcps_subject_id' : 'subject_id' }/>
                    </div>
                </div>
            </div>
        </div>
    }

    return '';
});

const  Faculties = React.memo( ({ onInputChange, formData, faculties, faculty_label }) => {

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

    if( faculties.length > 0 ) {
        return (
            <div>

                <div className="faculties" style={{ width: '100%' }}>
                    <div className="form-group">
                        <label className="col-md-3 control-label">{ faculty_label } (<ReqIcon/>) </label>
                        <div className="col-md-3">
                            <div className="input-icon right">
                                <DropdownOptions required
                                                 keyBy='id'
                                                 onChange={onInputChange}
                                                 items={ faculties }
                                                 name='faculty_id' selected={formData && formData.faculty_id} id='faculties' />
                            </div>
                            <ShowErrors name='faculty_id'/>
                        </div>
                    </div>
                </div>

                {/*<FacultyDisciplines disciplines={disciplines}/>*/}

            </div>
        )
    }
    return '';
});


const Courses = React.memo( ( {courses, selected_course_id, changeCourseData}) => {

    if( courses.length > 0 || isEdit() ) {
        return <div className="courses">
            <div className="form-group">
                <label className="col-md-3 control-label">Courses (<ReqIcon/>) </label>
                <div className="col-md-3">
                    <div className="input-icon right">
                        <DropdownOptions keyBy='id' onChange={changeCourseData} items={ courses } name='course_id' selected={selected_course_id}  required />
                    </div>
                    <ShowErrors name='course_id'/>
                </div>
            </div>
        </div>
    }

    return '';
})

const Sessions = React.memo( ( { sessions, selected_session_id, changeValue } ) => {
    const data_sessions = sessions || [ ];



    return (
        <div className="sessions">
            <div className="form-group">
                <label className="col-md-3 control-label">Session (<ReqIcon/>) </label>
                <div className="col-md-3">
                    <div className="input-icon right">
                        <DropdownOptions keyBy='id' onChange={changeValue} items={ data_sessions } name='session_id'  selected={selected_session_id} required />
                        <ShowErrors name='session_id'/>
                    </div>
                </div>
            </div>
        </div>
    );
});