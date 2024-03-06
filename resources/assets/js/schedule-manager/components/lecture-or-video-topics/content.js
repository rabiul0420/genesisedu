import React, { useState, useEffect} from "react";

const Content = ( { item,  data, onRemoveItemClick, index, slot_index, onAddParentItemClick, getParent } ) => {

    const [ contentType, setContentType ] = useState(   item.type  );
    const [ selected, setSelected ] = useState({ content_id: item.class_or_exam_id });

    function setSelectedContentId( id ){
        setSelected({ ...selected, ...{ content_id: id} });
    }

    function getContentType( ){
        const type = contentType || item.type;
        return type || 'Class';
    }

    function getContents( ){
        return data && Array.isArray( data.topics ) ? data.topics : [];
    }

    function  getExamsOrLectures( topic ){
        let items = [ ];
        topic = topic || { };
        const ct = getContentType();

        if( ct === 'Class' ) {

            if( Array.isArray( topic.lectures ) ) {
                if( item.parent_id == 0  ) {
                    items = topic.lectures.filter( lecture => lecture.type == 1 );
                }else {
                    const parent = getParent( item.parent_id );
                    const parent_type = parent.type || null;
                    if( parent_type ) {
                        items = topic.lectures.filter( lecture => lecture.type == ( parent_type == 'Exam' ? 2 : 3) );
                    }
                }
            }
        } else if ( ct === 'Exam' ) {
            items = topic.exams;
        }

        return Array.isArray( items ) ? items : [];
    }

    function itemId() {
        return isDuplicating() ? 0: ( item && item.id );
    }

    useEffect( function (){
        let video_type = 1;

        if( item.parent_id > 0 ) {
            const parent = getParent( item.parent_id );
            if( parent.type === 'Exam' ) {
                video_type = 2;
            } else if( parent.type === 'Class' ) {
                video_type = 3;
            }
        }

        $( '#exam_or_class_id-' +slot_index+'-'+ index )
            .content_list_select2( { width: '100%', type: getContentType(), video_type, placeholder: '--Select ' + getContentType() + '--' } )



        // $( '#exam_or_class_id-' +slot_index+'-'+ index ).off( 'change' );
        // $( '#exam_or_class_id-' +slot_index+'-'+ index ).on('change', function (){
        //     console.log( $(this).val() );
        //     setSelectedContentId( $(this).val() );
        // });

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


    function ContentOptionGroups(){
        function ContentOptions({topic}){
            const content_items = getExamsOrLectures( topic );

            if( content_items.length === 0 )
                return <option value='' disabled>--No {getContentType()} found--</option>

            return content_items.map( ( content ) =>
                <option key={content.id + getContentType() }
                        value={ content.id }
                        selected={ selected && ( selected.content_id == content.id )} >
                    { content.name }
                </option>
            )
        }

        return getContents( ).map( ( topic, key ) => {
            return (
                <optgroup label={topic.name} key={topic.id}>
                    <ContentOptions topic={topic}/>
                </optgroup>
            )
        })
    }

    return (
        <div className='form-group' style={getRowStyle()}>

            <input type='hidden' name={'details['+ slot_index+'][contents]['+index+'][detail_id]'} value={ itemId( ) } />
            <input type='hidden' name={'details['+ slot_index+'][contents]['+index+'][parent_id]'} value={ item && item.parent_id } />

            <label className='col-md-1 control-label' style={{ width: 'auto'}}>Type</label>

            <div className='col-md-2'>
                <TypeSelection />
            </div>

            <label className='col-md-1 control-label'>{ getContentType() }</label>
            <div className='col-md-3'>
                <select style={getRowStyle()}
                        required className='form-control' id={ 'exam_or_class_id-' +slot_index+'-'+ index  } name={'details['+ slot_index+'][contents]['+index+'][class_or_exam_id]'}>
                    <option value=''>--select { getContentType() }--</option>
                    <ContentOptionGroups/>
                </select>
            </div>

            <label className='col-md-1 control-label'>Mentor</label>
            <div className='col-md-2'>

                <select required className='form-control' name={'details['+ slot_index+'][contents]['+index+'][mentor_id]'}>
                    <option value=''>--select mentor--</option>
                    { Array.isArray( data.mentors ) &&
                    data.mentors.map( (mentor,key) =>
                        <option    key={mentor.id} value={mentor.id} selected={ item && ( item.mentor_id == mentor.id )}>{ mentor.name }</option> )
                        // selected={ item && ( item.mentor_id == mentor.id )}
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

export  default  React.memo( Content );