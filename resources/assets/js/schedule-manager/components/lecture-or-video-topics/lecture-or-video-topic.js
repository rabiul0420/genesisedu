import Content from './content';
import React, { useState, useEffect} from "react";
import uuid from "react-uuid";

// export default ({ item, index, data, onDataChange, changeValue, removeItem }) => {
export default class extends React.Component  {


    // const initial_contents = item && Array.isArray( item.contents )  ? item.contents:[];
    // const [ contents, setContents ] = useState( initial_contents );

    constructor(props) {
        super(props);
        this.state = {
            contents: this.props.item && Array.isArray( this.props.item.contents )  ? this.props.item.contents : [ ]
        }
        this.getParent = this.getParent.bind(this);
        this.removeContent = this.removeContent.bind(this);
        this.addNewContent = this.addNewContent.bind(this);
        this.onAddParentItemClick = this.onAddParentItemClick.bind(this);
    }


    getContents(){
        return Array.isArray(this.state.contents) ? this.state.contents:[];
    }

    triggerDataChange( key, value ){
        if( typeof this.props.onDataChange == 'function' ) {
            const data = { };
            data[key] = value;
            this.props.onDataChange( data );
        }
    }

    componentDidMount() {

        if( this.state.contents.length === 0 ) {

            this.setState({ contents:[ this.props.data ] });
        }

        enableTimeAndDate( );
    }


    addNewContent( e ){
        e.preventDefault();

        const contents = this.state.contents;

        const item = {
            "id": null,
            "slot_id": null,
            "type":"Class",
            "class_or_exam_id": 0,
            "mentor_id": null,
            "parent_id": 0,
            "priority": null,
        };

        contents.push( item );

        this.setState({ contents });
    }

    removeContent( index ){
        let contents = this.state.contents;
        if( confirm( 'Are you sure?' )) {
            contents.splice( index, 1);
            this.setState({ contents: [ ...contents ] });
        }
    }


    itemId() {
        return isDuplicating() ? 0: ( this.props.item && this.props.item.id );
    }



    getParent( parent_id ){
        const data = this.state.contents.filter( item => ( item && item.id) === parent_id );
        return data[0] || null;
    }

    onAddParentItemClick( index ){

        if( confirm( 'Are you sure?' )) {
            const contents = this.state.contents;
            const parent = contents[index] || { };

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
            this.setState({ contents });
        }
    }

    render(){

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
                        <input  required className='form-control item-date' type='text' value={ this.props.item && this.props.item.date} name={'details[' + this.props.index + '][date]'}
                                placeholder='Date' onChange={(e) =>
                                this.triggerDataChange( e.target.name, e.target.value )} />
                    </div>
                    <label className='col-md-1 control-label' style={{ width: 'auto'}}>Time</label>
                    <div className='col-md-2'>
                        <input  required className='form-control timepicker' type='text' value={ this.props.item && this.props.item.time} name={'details[' + this.props.index + '][time]'}
                                placeholder='Time'onChange={(e) =>
                            this.triggerDataChange( e.target.name, e.target.value )} />
                    </div>
                    <RemoveBtn/>

                </div>

                <hr style={{marginTop: '10px'}}/>
                <div style={{ width:"100%", textAlign:"center" }}>
                    {  this.getContents().map( (content, key ) =>
                        <Content onAddParentItemClick={this.onAddParentItemClick}
                                 getParent={this.getParent}
                                 item={content || {}}
                                 slot_index={ this.props.index }
                                 index={key}
                                 key={uuid()}
                                 data={this.props.data || {}}
                                 onRemoveItemClick={this.removeContent}/> ) }

                    <a href='' style={{marginTop:'20px'}} onClick={this.addNewContent} className='btn btn-info'>+Add More</a>
                </div>

            </div>
        )
    }

}