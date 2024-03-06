import React, { useState, useEffect} from "react";
import LectureOrVidoeTopic from './lecture-or-video-topic';
import uuid from 'react-uuid'


export default class extends React.Component{

    constructor( props ) {
        super( props );
        this.state = {
            topicsData: { data:{ exams:[ ], lectures: [ ], mentors: [ ] }, loaded: false },
            topicItems: [],
        }

        this.removeLectureOrVideoTopic = this.removeLectureOrVideoTopic.bind(this);
        this.addLectureOrVideoTopic = this.addLectureOrVideoTopic.bind(this);
    }

    getContents(){
        return this.props.contents || {};
    }


    getTopicItems( ){
        if( Array.isArray(this.state.topicItems) && this.state.topicItems.length > 0 ) {
            return this.state.topicItems
        }
        return [];
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        //
    }

    componentDidMount() {


        if( Array.isArray( this.state.topicItems ) && this.state.topicItems.length === 0 ) {
            if( this.props.formData && this.props.formData.details ) {

                if( Array.isArray(this.props.formData.details) && this.props.formData.details.length > 0 ) {
                    this.setState(  {topicItems: [ ...this.props.formData.details ]});
                }

            }else {

                const item = { type : 'Class', contents: [], date: '', time: '' };
                this.setState( {topicItems: [ ...this.state.topicItems, item ] });

            }
        }
    }


    addLectureOrVideoTopic( e ){
        e.preventDefault( );
        const item = { type : 'Class', contents: [], date: '', time: '' };

        const topicItems = this.state.topicItems;
        topicItems.push({ type : 'Class', contents: [], date: '', time: '' });

        this.setState( { topicItems } );
    }

    removeLectureOrVideoTopic( item_index ){
        this.state.topicItems.splice( item_index, 1);
        this.setState( {topicItems: [ ...this.state.topicItems ] } );
    }



    render(){
        return (
            <div className='container-fluid'>

                <div className='row'>

                    { this.getTopicItems().map( (item, key) => <LectureOrVidoeTopic
                        onDataChange={this.props.onDataChange}
                        item={item}
                        index={key}
                        key={key}
                        removeItem={this.removeLectureOrVideoTopic}
                        data={ this.getContents() } /> )
                    }

                    <a href='' disabled={this.state.topicsData == null} className="btn btn-success" onClick={this.addLectureOrVideoTopic}>+ Add Slot</a>

                </div>
            </div>
        );
    }
}
