import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PanelBody from "./components/PanelBody";
import ScheduleInformation from "./components/ScheduleBasicData";
import CourseBatchInformation from "./components/course-batch-information/course-batch-information";
import LectureOrVidoeTopics from "./components/lecture-or-video-topics/lecture-or-video-topics"
import FacebookGroup from "./components/facebook-links";



export default class Application extends Component {

    constructor( props ) {
        super( props );

        this.state = {
            data : { },
            inputs : { },
            formData : editing_data || { },
            lecture_exam_contents : { mentors: [ ], topics: [ ] }
        }

        this.handleOnInputChange = this.handleOnInputChange.bind(this);
        this.onBatchInfoChange = this.onBatchInfoChange.bind(this);
    }


    onBatchInfoChange( data ){



        const filtering_keys = [ 'year', 'institute_id', 'course_id', 'session_id', 'batch_id' ];

        let has_required_values = true;
        let params = {};

        filtering_keys.forEach( key => {
            params[key] = String( data[ key ] );
            has_required_values = has_required_values && !( params[key] == null || params[key] == '' || params[key] == 'undefined' );
        })


        if( data['faculty_id'] ) {
            params.faculty_id = String(data['faculty_id']);
        }else if( data['subject_id'] ) {
            params.subject_id = String(data['subject_id']);
        }

        if( data['bcps_subject_id'] ) {
            params.bcps_subject_id = String(data['bcps_subject_id']);
        }




        if( has_required_values ) {
            this.loadLecturesExamsMentors( params )
        }
    }

    loadLecturesExamsMentors( params ){


        axios.get( '/admin/batch-schedules/lecture-exams-mentors', { params } )
            .then( ({data}) => {
                if( data.topics ) {

                    this.setState({ lecture_exam_contents: data });


                }
            }).catch( err => console.log( err ));
    }

    submitDisabled( ) {
        if( this.state.data ) {
            return !this.state.data.loaded;
        }
        return this.state.data === null;
    }

    getExecutives( ){
        return this.state.data && this.state.data.executive_list;
    }

    getRoomTypes( ){
        return this.state.data && this.state.data.rooms_types;
    }

    setInput( key, value ){
        const inputs = this.state.formData;
        inputs[key] = value;
        this.setState( { inputs })
    }

    getInputValue( key ){
        const inputs = this.state.formData || {};
        return inputs[key];
    }

    getData( ){
        return this.state.data || [];
    }

    onProgressing({ loaded }){
        const  dt = this.state.data;
        dt.loaded = loaded;
        this.setState( dt );
    }



    componentDidMount( ) {

        this.onProgressing({ loaded: false });

        axios.get( '/admin/batch-schedules/get-data', {
            params: {
                schedule_id: scheduleId(),
                action: isEdit() ? 'edit' : 'create'
            }
        }).then( ({data}) => {

            if( data !== null ) {
                const lecture_exam_contents = { mentors: data.mentors || [], topics: data.topics_with_lectures || [] };
                this.setState({ loaded: true, data , lecture_exam_contents })
            }


            $(".mentor-select2").select2();


        }).catch( err => console.log( err ));

    }

    handleOnInputChange( e ) {
        this.setInput( e.target.name, e.target.value );
    }

    render( ){

        let formData = editing_data ? editing_data: {};
        let ClassData = [];
        let id = id ? id : 0;
        let action = action ? action : 'create';

        const InputType = () => this.props.is_edit ? <input name="_method" type="hidden" value="PUT"/> : ''

        return (
            <div className="portlet">
                <div className="portlet-title">
                    <div className="caption">
                        <i className="fa fa-reorder"></i>{ this.props.pageTitle || 's' }
                    </div>
                </div>
                <div className="portlet-body form">
                    {/*<form className="form-horizontal" method="post" encType="multipart/form-data" onSubmit={ submitData }>*/}

                    <form {...formAttributes()}>
                    {/*<form>*/}
                        <div className="form-body">

                            <InputType/>
                            <input name="_token" type="hidden" value={token} />

                            <PanelBody title="Select Batches Schedules Information">
                                <ScheduleInformation executives={ this.getExecutives( ) } 
                                    roomTypes={this.getRoomTypes()}
                                    formData={ old || editing_data || {}} />
                            </PanelBody>

                            <PanelBody title="Select Batches Schedules Course Information">
                                <CourseBatchInformation data={ this.getData() } formData={ old || editing_data || {}} onBatchInfoChange={this.onBatchInfoChange} />
                            </PanelBody>

                            <PanelBody title="Facebook Group Link">
                                <FacebookGroup fbLinks={ (old || editing_data || {}).fb_links || [] } />
                            </PanelBody>

                            <PanelBody title="Add/Remove class or exam">
                                <LectureOrVidoeTopics contents={ this.state.lecture_exam_contents } formData={ old || editing_data || {} } onProgressing={this.onProgressing} />
                            </PanelBody>

                            <div className='row'>
                                <div className='col-md-6'>
                                    {/*<button disabled={submitDisabled()} className='btn btn-lg btn-success'>Submit</button>*/}
                                    <button className='btn btn-lg btn-success'>Submit</button>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        )
    }
}

function ErrItem({msg}){
    return <p>{msg}</p>
}

export function ShowErrors({name}){
    let _errors = errors || {}

    if( _errors ) {
        const errs = _errors[name];
        if( Array.isArray( errs ) ) {
            return <div style={{color: 'magenta', fontStyle: 'italic'}}>
                { errs.map( (msg, index) => <ErrItem key={index} msg={msg} /> ) }
            </div>
        }
    }
    return '';
}

if ( document.getElementById( 'application' ) ) {
    ReactDOM.render( <Application
        is_edit={isEdit()}
        pageTitle={isEdit() ? 'Edit Schedule':( isDuplicating() ? 'Duplicate Schedule':'Create Schedule')} />,  document.getElementById('application' ) );
}

