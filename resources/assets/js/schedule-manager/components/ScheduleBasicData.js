import ReqIcon from "./ReqIcon";
import DropdownOptions from "./DropdownOptions";
import {ShowErrors} from "../Application";

//import {CKEditor} from '@ckeditor/ckeditor5-react';
// import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

let editor_applied = false;

export default ( {executives, formData , onInputChange, roomTypes} ) => {


    if( $( "#terms-and-condition" ).length && editor_applied === false ) {  
        try{
            CKEDITOR.replace( 'terms_and_condition' );
            editor_applied = true;
        } catch( err ) {
            console.log ( err );
        }

    }


    return (

        <div>
            <div className="form-group">
                <label className="col-md-2 control-label">Schedule Name (<ReqIcon/>) </label>

                <div className="col-md-3">
                    <div className="input-icon right">
                        <input  required type="text" name="name"
                                defaultValue={formData.name} placeholder="e.g: Specila Schedule - March'20" onChange={onInputChange}
                                className="form-control"/>
                    </div>
                    <ShowErrors name='name'/>
                </div>

                <label className="col-md-2 control-label">Schedule Sub Line </label>
                <div className="col-md-3">
                    <div className="input-icon right">
                        <input  type="text" name="tag_line"
                                defaultValue={formData.tag_line}
                                placeholder="e.g: Every Monday & Thursday" onChange={onInputChange}
                                className="form-control"/>
                    </div>
                    <ShowErrors key='tag_line'/>
                </div>
            </div>

            <div className="form-group">

                <label className="col-md-2 control-label">Schedule Contact Details (<ReqIcon/>) </label>
                <div className="col-md-3">
                    <div className="input-icon right">
                        <input required type="text" name="contact_details" defaultValue={formData.contact_details}
                               placeholder="Schedule Contact Person and Mobile No" onChange={onInputChange}
                                className="form-control"/>
                    </div>
                    <ShowErrors name='contact_details'/>
                </div>

                <label className="col-md-2 control-label">Executive (<ReqIcon/>) </label>
                <div className="col-md-3">
                    <DropdownOptions items={executives} name='executive_id' selected={formData.executive_id} onChange={onInputChange} required  />
                    <ShowErrors name='contact_details'/>
                </div>

            </div>

            <div className="form-group">      
                <label className="col-md-2 control-label">Room Number (<ReqIcon/>) </label>
                <div className="col-md-3">
                    <DropdownOptions items={roomTypes} name='room_id' selected={formData.room_id} onChange={onInputChange} required  />
                    <ShowErrors name='room_id'/>
                </div>

                <label className="col-md-2 control-label">Address (<ReqIcon/>) </label>
                <div className="col-md-3">
                    <div className="input-icon right">

                        <textarea required  name="address" className="form-control" placeholder="Type address here" onChange={onInputChange}>{formData.address}</textarea>
                    </div>
                    <ShowErrors name='address'/>
                </div>


            </div>

            <div className="form-group">
                <label className="col-md-2 control-label">Terms & Condition (<ReqIcon/>) </label>
                <div className="col-md-6">
                    <div className="input-icon right">
                        <textarea id="terms-and-condition"  required type="text" name="terms_and_condition" rows="5"
                            defaultValue={formData.terms_and_condition} placeholder="e.g: Specila Schedule - March'20" onChange={onInputChange}
                            className="form-control"/>
                    </div>
                    <ShowErrors name='terms_and_condition'/>
                </div>
            </div>

        </div>

    )
}