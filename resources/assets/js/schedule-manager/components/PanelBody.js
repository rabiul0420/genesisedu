import React from "react";

const PanelBody = ({children, title}) => {
    const styles = {
        main: { borderColor:'#eee' },
        heading: {backgroundColor: '#eee', color: 'black', borderColor: '#eee'}
    };

    return <div className="panel panel-primary" style={styles.main}>
        <div className="panel-heading" style={styles.heading}>{title}</div>
        <div className="panel-body">{children}</div>
    </div>
}

export default React.memo( PanelBody );