import React, { useState, useEffect, useCallback} from "react";
import LectureOrVidoeTopic from './lecture-or-video-topic';
import uuid from 'react-uuid'
import Content from "./content";
import Select2 from 'react-select2-wrapper';


export default class extends React.Component{

    constructor( props ) {
        super( props );
        this.state = {
            slot_details: [ ],
        }

        this.removeLectureOrVideoTopic = this.removeLectureOrVideoTopic.bind(this);
        this.addLectureOrVideoTopic = this.addLectureOrVideoTopic.bind(this);
        this.changeSlotValue = this.changeSlotValue.bind(this);
        this.addLectureOrVideoTopic = this.addLectureOrVideoTopic.bind(this);
        this.removeContent = this.removeContent.bind(this);
        this.addNewContent = this.addNewContent.bind(this);
        this.addParentContent = this.addParentContent.bind(this);
        this.onChangeContentType = this.onChangeContentType.bind(this);
    }

    getContents(){
        return this.props.contents || {};
    }


    getSlotDetails( ){
        if( Array.isArray(this.state.slot_details) && this.state.slot_details.length > 0 ) {
            return this.state.slot_details
        }
        return [];
    }

    emptyContent( _contentId, type, parent_id  = 0 ){
        return {
            class_or_exam_id: 0,
            deleted_at: null,
            deleted_by: null,
            id: uuid(),
            mentor_id: 0,
            parent_id: parent_id,
            priority: 0,
            slot_id: _contentId,
            type: type,
        }
    }

    emptySlotDetails( ){
        const _contentId = uuid( );
        return  {
            id: _contentId,
            time: "",
            date: "",
            contents: [ this.emptyContent( _contentId, "Class" )],
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if( this.props !== prevProps) {
            let slot_details = this.state.slot_details;
            let _details = [ ];

            if( this.props.formData.details && Array.isArray( this.props.formData.details ) && this.props.formData.details.length > 0 ) {
                slot_details = [ ];
                _details = [...slot_details, ...this.props.formData.details ];
            }else {
                _details.push( this.emptySlotDetails() );
            }

            this.setState({ slot_details: _details });
        }
    }

    componentWillMount( ) {
        this.setState({ slot_details: [ this.emptySlotDetails() ] });
    }

    addLectureOrVideoTopic( e ){
        e.preventDefault( );
        const slot_details = this.state.slot_details;
        slot_details.push( this.emptySlotDetails() );
        this.setState( { slot_details } );

        setTimeout( () => {
            $('.mentor-select2').select2( );
        }, 1000)

    }

    addNewContent( index, type ){
       const slot_details = this.state.slot_details;

       if( slot_details && Array.isArray( slot_details[index].contents ) ) {

           const slot_details_contents = slot_details[index].contents;

           slot_details_contents.push( this.emptyContent( slot_details_contents.id, type ) );

           slot_details[index].contents = slot_details_contents;

            this.setState( { slot_details: slot_details })
       }
    }

    addParentContent( index, slot_index, parent_id ){
        const slot_details = this.state.slot_details;



        if( slot_details && Array.isArray( slot_details[index].contents ) ) {

            const slot_details_contents = slot_details[index].contents;

            const newContent = this.emptyContent( slot_details_contents.id, 'Class' , parent_id )

            slot_details_contents.splice( slot_index+1, 0, newContent );
            slot_details[index].contents = slot_details_contents;

            this.setState( { slot_details: slot_details })
        }
    }

    removeContent( index, slot_index ){

        const slot_details = this.state.slot_details;

        if( slot_details && Array.isArray( slot_details[index].contents ) ) {

            const slot_details_contents = slot_details[index].contents;

            slot_details_contents.splice(slot_index, 1);
            slot_details[index].contents = slot_details_contents;

            this.setState( { slot_details: slot_details })
        }
    }

    removeLectureOrVideoTopic( item_index ){
        const slot_details = this.state.slot_details;
        slot_details.splice( item_index, 1 );
        this.setState( { slot_details } );
    }

    changeSlotValue( index, key ){
        console.log('Index', index, key)
        return  (e) => {

        }
    }

    onChangeContentType(e){
        const slot_details = this.state.slot_details;
        const data = e.target.dataset;


        const index = data.index || -1;
        const slot_index = data.slotIndex || -1;
        //console.log(data, index, slot_index);

        if( slot_details[ index ] && Array.isArray( slot_details[index].contents ) ) {

            const slot_details_contents = slot_details[ index ].contents;

            //console.log( 'e.target.value', e.target.value, slot_details_contents, slot_index )
            if( slot_details_contents[ slot_index ] ) {
                slot_details_contents[ slot_index ].type = e.target.value;


                slot_details[ index ].contents = slot_details_contents;
                this.setState( { slot_details: slot_details });
            }

        }

    }


    render( ){


        return (

            <div className='container-fluid'>

                <div className='row'>

                    {this.getSlotDetails().map( (slot, index) => <TimeSlot
                        slot={ slot }
                        index={ index }
                        key={ slot.id }
                        topicContents={this.getContents()}
                        removeItem={this.removeLectureOrVideoTopic}
                        changeSlotValue={this.changeSlotValue}
                        addNewContent={this.addNewContent}
                        removeContent={this.removeContent}
                        addParentContent={this.addParentContent}
                        onChangeContentType={this.onChangeContentType}
                    />)}

                    <a href='' disabled={this.state.slot_details == null} className="btn btn-success" onClick={this.addLectureOrVideoTopic}>+ Add Slot</a>
                </div>
            </div>

        );
    }

}

class TimeSlot extends React.Component{

    constructor( props ) {
        super( props );

        this.addNewContent = this.addNewContent.bind(this);
        this.removeContent = this.removeContent.bind(this);
        this.getParent = this.getParent.bind(this);
        this.addParentContent = this.addParentContent.bind(this);
    }

    itemId() {
        return isDuplicating( ) ? uuid(): ( this.props.slot && this.props.slot.id );
    }

    componentDidMount() {
        enableTimeAndDate( );
    }

    changeSlotValue(e){
        // return (e)=> {
            console.log(e.target.value )
        // }
    }

    addNewContent( e ){
        e.preventDefault();
        if( typeof  this.props.addNewContent == 'function' ) {
            this.props.addNewContent( this.props.index, 'Class' );

            setTimeout( () => {
                $(".mentor-select2").select2();
            },600)
        }
    }



    getContents( ){



        return this.props.slot && Array.isArray( this.props.slot.contents )
            ? this.props.slot.contents : [ ];
    }


    removeContent( slot_index ){
        if( confirm( 'Are you sure?' )) {
            this.props.removeContent( this.props.index, slot_index );
        }
    }

    addParentContent( slot_index, parent_id ){
        if( confirm( 'Are you sure?' )) {
            this.props.addParentContent( this.props.index, slot_index, parent_id );
        }
    }


    getParent( parent_id ){
        if( this.props && this.props.slot.contents ) {
            const data = this.props.slot.contents.filter( item => ( item.id ) === parent_id );
            return data[0] || null;
        }
        return null;
    }

    render( ){

        const RemoveBtn = ( ) => {
            if( this.props.index === 0 )
                return '';
            return <div className='pull-right' style={{marginRight: '15px'}}>
                <a className='btn btn-warning btn-sm' href='' onClick={(e)=> {
                    if( confirm('Are your sure want to remove this date and time?') )  {
                        e.preventDefault();
                        this.props.removeItem( this.props.index );
                    }
                }}>Remove</a>
            </div>
        }

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
                        <input type='hidden' value={this.itemId()} name={'details[' + this.props.index + '][slot_id]'}/>
                        <input required className='form-control item-date' type='text'
                               defaultValue={ this.props.slot && this.props.slot.date}
                               name={'details[' + this.props.index + '][date]'}
                               placeholder='Date' />
                    </div>

                    <label className='col-md-1 control-label' style={{ width: 'auto'}}>Time</label>

                    <div className='col-md-2'>
                        <input required className='form-control timepicker' type='text'
                               defaultValue={ this.props.slot && this.props.slot.time}
                               name={'details[' + this.props.index + '][time]'}
                               placeholder='Time' />
                    </div>
                    <RemoveBtn/>
                </div>

                <hr style={{marginTop: '10px'}}/>

                <div style={{ width:"100%", textAlign:"center" }}>

                    {this.getContents().map( (content, slot_index)  => <SlotContent
                        key={this.props.slot.id+'-'+content.id}
                        getParent={this.getParent}
                        item={content}
                        data={this.props.topicContents}
                        index={this.props.index}
                        slot_index={slot_index}
                        onRemoveItemClick={this.removeContent}
                        addParentContent={this.addParentContent}
                        onChangeContentType={this.props.onChangeContentType}
                    /> )}

                    <a href='' style={{marginTop:'20px'}} onClick={this.addNewContent} className='btn btn-info'>+Add More</a>
                </div>

            </div>
        )
    }
}
const select2_enabled = {};
const mentor_select2_enabled = [];
class SlotContent extends  React.Component {

    constructor( props ) {
        super( props );

        this.state = {
            contentType: props.item.type || 'Class',
            topics: props.data.topics,
            selected_content_id: props.item.class_or_exam_id
        };

        this.getExamsOrLectures = this.getExamsOrLectures.bind( this );
        this.onChangeContentType = this.onChangeContentType.bind( this );
    }

     getRowStyle( emptyLecture ){
        const common = { marginBottom: 0, padding: 0 }


        if( emptyLecture ) {
            common.backgroundColor = '#FA9';
        }

        const parent = this.props.getParent( this.props.item.parent_id )
        if( parent && parent.type ) {
            return { ...{ marginTop: '5px',marginBottom: '5px', color: parent.type === 'Exam'? '#d6791a' : '#05e005 ' , fontWeight: 'bold'}, ...common };
        }

        return { ...{ marginTop: '18px'}, ...common };
    }

    itemId( ) {
        // return isDuplicating( ) ? uuid( ): ( this.props.item && this.props.item.id );
        return isDuplicating( ) ? ( this.props.item && this.props.item.dup_id ): ( this.props.item && this.props.item.id );
        // return ( this.props.item && this.props.item.id );
    }

    itemParentId( ) {
        // return isDuplicating( ) ? uuid( ): ( this.props.item && this.props.item.id );
        return isDuplicating( ) ? ( this.props.item && this.props.item.dup_parent_id ): ( this.props.item && this.props.item.parent_id );
        // return ( this.props.item && this.props.item.id );
    }

    getContents( ){
        return this.props.data && Array.isArray( this.props.data.topics ) ? this.props.data.topics : [];
    }



    enableSelect2( rt ){
        const ID = '#exam_or_class_id-' +this.props.index+'-'+ this.props.slot_index;
        $( ID ).select2();
        $( ID ).off('select2:select');
        const comp = this;

        $( ID ).on('select2:select',  function ( ) {
            const v = $( this ).val( );
            comp.setState({ selected_content_id: v });
            $(this).val( null ).trigger('change');
            $(this).val( v ).trigger('change');
        });
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if( prevProps != this.props ) {

            if( Array.isArray( this.props.data.topics ) && this.props.data.topics.length > 0 ) {

                if( this.props.data.topics != prevProps.data.topics ) {
                    this.setState({topics: this.props.data.topics});
                }

                if( this.props.item.class_or_exam_id != prevProps.item.class_or_exam_id ) {
                    this.setState({selected_content_id: this.props.item.class_or_exam_id});
                }
                this.enableSelect2('update');

            }


        }

    }


    componentDidMount() {

        if( this.props.item ) {

        }

        if( this.props.item ) {
            //mentor_select2_enabled.push( this.props.item.id );
        }

        this.enableSelect2('mount');
    }

    getExamsOrLectures( topic ){
        let items = [ ];
        topic = topic || { };
        const ct = this.state.contentType;

        if( this.props && this.props.item ) {




            if( ct === 'Class' ) {


                if( Array.isArray( topic.lectures ) ) {

                    if( this.props.item.parent_id == 0  ) {
                        items = topic.lectures.filter( lecture => lecture.type == 1 );
                    }else {
                        const parent = this.props.getParent( this.props.item.parent_id )


                        if( parent ) {
                            const parent_type = parent.type || null;
                            if( parent_type ) {
                                items = topic.lectures.filter( lecture => lecture.type == ( parent_type == 'Exam' ? 2 : 3) );
                            }
                        }

                    }
                }
            } else if ( ct === 'Exam' ) {
                items = topic.exams;
            }
        }


        return Array.isArray( items ) ? items : [];
    }


    getExam(  ){
        const id = this.state.selected_content_id;

        if( this.state.contentType === 'Exam' ) {
            if( Array.isArray( this.state.topics ) ) {
                let result = null;

                this.state.topics.filter( topic => {
                    const children = topic.exams;
                    const one = children.filter( exam => exam.id === id )
                    if( one[0] ) {
                        result = one[0];
                    }
                });
                return result;
            }
        }
        return null;
    }

    getLecture(  ){
        const id = this.state.selected_content_id;


        if( this.state.contentType === 'Class' ) {
            if( Array.isArray( this.state.topics ) ) {
                let result = null;

                this.state.topics.filter( topic => {
                    const children = topic.lectures;
                    const one = children.filter( lecture => lecture.id == id )
                    if( one[0] ) {
                        result = one[0];
                    }
                });
                return result;
            }
        }
        return null;
    }


    getTopicsContents( ){
        const data = [];

        if( Array.isArray( this.state.topics ) ) {

            this.state.topics.map( topic => {
                const children = this.state.contentType === 'Exam' ? topic.exams : (this.state.contentType === 'Class' ? topic.lectures:[])

                if( Array.isArray( children ) && children.length > 0 ) {

                    data.push({
                        text: topic.name,
                        id: topic.id, children
                    });
                }
            });
        }

        return data;
    }

    onChangeContentType( e ){
        this.props.onChangeContentType( e );


        //console.log( e.target, $( e.target ).parents( '.form-group' ).find( '.contents' ) );

        $( e.target ).parents( '.form-group' ).find( '.contents' ).select2().val( null ).trigger('change');

        // $(this).val(null).trigger('change');

        this.setState({ contentType: e.target.value });
    }


    contentEditBtn(emptyContent, content ){
        if( emptyContent ) {
            if( this.state.contentType == 'Class' ) {
                return <a href={ '/admin/lecture-video/'+content.id+'/edit'} className='btn btn-success' style={{marginTop: '5px', marginBottom: '5px'}}>Edit</a>
            } else {
                return <a href={ '/admin/exam/'+content.id+'/edit'} className='btn btn-success' style={{marginTop: '5px', marginBottom: '5px'}}>Edit</a>
            }
        }
        return '';
    }

    render() {

        let emptyContent = false;
        let content = null;

        if( this.state.contentType == 'Class' ) {
            content = this.getLecture( );
            emptyContent = content && !content.lecture_address;
        }else {
            content = this.getExam( );
            emptyContent = content && content.status == 2;
        }

        // console.log('EMPTYLECTUR', emptyContent, lecture)
        // console.log( 'Contents', this.getTopicsContents() )
        return (
            <div className='form-group' style={this.getRowStyle( emptyContent  )}>

                <input type='hidden' name={'details['+ this.props.index+'][contents]['+this.props.slot_index+'][id]'} value={ this.itemId( ) } />
                <input type='hidden' name={'details['+ this.props.index+'][contents]['+this.props.slot_index+'][detail_id]'} value={ this.itemId( ) } />
                <input type='hidden' name={'details['+ this.props.index+'][contents]['+this.props.slot_index+'][parent_id]'} value={ this.itemParentId( ) } />

                <input type='hidden' name={'details['+ this.props.index+'][contents]['+this.props.slot_index+'][dup_id]'} value={ this.itemId( ) } />
                <input type='hidden' name={'details['+ this.props.index+'][contents]['+this.props.slot_index+'][dup_parent_id]'} value={ this.itemParentId( ) } />

                <label className='col-md-1 control-label' style={{ width: 'auto'}}>Type</label>

                <div className='col-md-2'>

                    <TypeSelection item={this.props.item}
                                   parent={ this.props.getParent( this.props.item.parent_id ) }
                                   content_type={this.state.contentType}
                                   index={this.props.index}
                                   slot_index={ this.props.slot_index }
                                   onChangeType={ this.onChangeContentType } />

                </div>

                <label className='col-md-1 control-label'>{ this.state.contentType }</label>
                <div className='col-md-3'>
                    <select required className='form-control contents'
                            id={ 'exam_or_class_id-' +this.props.index+'-'+ this.props.slot_index  }
                            name={'details['+ this.props.index+'][contents]['+this.props.slot_index+'][class_or_exam_id]'}
                    >
                        <option value=''
                                key={this.props.index+'-'+ this.props.slot_index + 'content_empty'}>--select--</option>

                        {( this.getTopicsContents() || []).map(
                            ( topic, index ) => <optgroup label={topic.text} key={ this.props.item.id +'-'+topic.id }>
                                {(topic.children || []).map( (child, idx) =>{
                                        return <option selected={this.state.selected_content_id == child.id} value={child.id}
                                                       key={this.props.item.id +'-'+topic.id+ '-'+ child.id }>{child.text}</option>
                                    }
                                )}
                            </optgroup>
                        )}

                    </select>
                    {this.contentEditBtn( emptyContent, content )}
                </div>

                <label className='col-md-1 control-label'>Mentor</label>
                <div className='col-md-2'>
                    <select required className='form-control mentor-select2'  name={'details['+ this.props.index+'][contents]['+ this.props.slot_index+'][mentor_id]'}
                            defaultValue={this.props.item && ( this.props.item.mentor_id  )}
                    >
                        <option value=''>--select mentor--</option>

                        { Array.isArray( this.props.data.mentors ) &&

                        this.props.data.mentors.map( (mentor,key) =>
                            <option  key={mentor.id} value={mentor.id} selected={ this.props.item && ( this.props.item.mentor_id == mentor.id )}>{ mentor.name }</option> )
                            // selected={ item && ( item.mentor_id == mentor.id )}
                        }
                    </select>
                </div>

                <div className='col-md-2'>
                    <AddPrentBtn item={this.props.item} contentType={this.state.contentType} onAddParentItemClick={ ( e ) =>{
                        e.preventDefault( );
                        if( this.props ) {
                            this.props.addParentContent( this.props.slot_index, this.props.item.id  );
                            setTimeout(() => {
                                $('.mentor-select2').select2();
                            }, 600);
                        }
                    }} />

                    {(( ) => {
                        if( this.props.slot_index > 0 ) {
                            return <div className='pull-right' style={{ marginRight: '15px' }}>
                                <a href='' className='btn btn-danger btn-sm' onClick={ (e) => {
                                    e.preventDefault( );
                                    if( typeof this.props.onRemoveItemClick == 'function' ) {
                                        this.props.onRemoveItemClick( this.props.slot_index );
                                    }
                                }}>&times;</a>
                            </div>
                        }
                    })( )}
                </div>
            </div>
        );
    }
}

const ContentOptionGroups = React.memo(({ contents, getTopicsContents, getExamsOrLectures, content_type, selected }) => {

    const ContentOptions = ({ topic }) => {
        const content_items = getExamsOrLectures( topic );

        if( content_items.length === 0 )
            return <option value='' key={content_type + topic.id + '_not_found' } disabled>--No {content_type} found--</option>

        return content_items.map( ( content ) =>
            <option key={ content_type + topic.id + content.id } selected={  selected == content.id  }
                    value={ content.id } >
                    {/*selected={ item && ( item.class_or_exam_id == content.id ) }*/}
                { content.name }
            </option>
        )

        return '';
    }

    if( Array.isArray( contents ) ) {
        return contents.map( ( topic, key ) => {
            return (
                <optgroup label={topic.name} key={topic.id} data-key={topic.id}>
                    <ContentOptions topic={topic}/>
                </optgroup>
            )
        })
    }

    return '';
});

const AddPrentBtn = React.memo(({ item, onAddParentItemClick, index, contentType }) => {
    if( item && item.parent_id == 0 ) {
        return <a href='' style={{ marginTop: '2px'}} className='btn btn-sm btn-primary' onClick={onAddParentItemClick}>Add { contentType == "Exam" ? 'Solve':'Feedback'} Class
        </a>
    } else { return '' }
});

const TypeSelection = React.memo( ( { parent, item, slot_index, index, content_type, onChangeType }) => {
    const _type = content_type || item.type;
    const contentType = _type || 'Class';



    if( item.parent_id && parent  ) {

        return <div style={{ marginTop: '10px'}}>
            <input name={'details['+ index+'][contents]['+slot_index+'][type]'} type='hidden' value='Class'/>
            {parent.type == 'Exam' ? 'Solve Class' : 'Feedback Class' }
        </div>
    }else {
        return <select data-index={index} data-slot-index={slot_index} required name={'details['+ index+'][contents]['+slot_index+'][type]'}
                       onChange={ onChangeType }
                       className='form-control'>
            <option value='Class' selected={ contentType == 'Class' }>Class</option>
            <option value='Exam' selected={ contentType == 'Exam' }>Exam</option>
        </select>
    }

    return '';
});