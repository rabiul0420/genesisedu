import React from "react";

const DropdownOptions =  ({onChange, name, items, selected, id, disabled, multiple, defaultItem, required, defaultValue, keyBy }) => {

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

    function getKey(item, _default){
        if( typeof item == 'object' && item[keyBy] ) {
            return item[keyBy];
        }
        return _default;
    }

    function onChangeSelect(e){
        if( typeof  onChange == 'function' ) {
            onChange( e )
        }
    }
    return (
        <div className="input-icon right">

            {/*value={selected} defaultValue={selected}*/}
            <select onChange={onChangeSelect} multiple={multiple}  name={name}  className="form-control" id={id} disabled={disabled} required={required} >
                {(()=>{
                    if( defaultItem === false ) {
                        return '';
                    }else if( typeof defaultItem == 'string' ) {
                        return <option value=''>{defaultItem}</option>
                    }
                    return <option value=''>--select--</option>
                })()}
                {/*selected={ isSelected( item ) }*/}
                {
                    Array.isArray( items ) &&
                    items.map( ( item, index ) => <option selected={ isSelected( item ) } key={getKey(item,index)} value={ getVal( item) } >{ getName(item) }</option> )
                }
            </select>
        </div>
    )

}
export default React.memo( DropdownOptions );
