import React from "react";
import uuid from "react-uuid";

export default class FacebookGroup extends React.Component {
    constructor( props ) {
        super( props );


        let fb_links = [];
        if( Array.isArray( this.props.fbLinks ) ) {
            fb_links = this.props.fbLinks;
            fb_links.map( link => link.id = uuid() );
        }

        this.state = {
            fb_links: fb_links || []
        }

        this.addNewLink = this.addNewLink.bind(this);
    }

    //
    // componentDidUpdate(prevProps, prevState, snapshot) {
    //
    //     if( this.props !== prevProps ) {
    //         if( Array.isArray( this.props.data.fb_links ) ) {
    //             const fb_links = this.props.data.fb_links;
    //
    //             fb_links.map( link => link.id = uuid() );
    //
    //             this.setState({ fb_links: fb_links });
    //         }
    //     }
    //
    // }

    addNewLink(e) {
        e.preventDefault( );
        this.setState({ fb_links: [ ...this.state.fb_links, ...[{title:'',links: '', id: uuid() }] ] });
    }

    removeLink(index){
        return (e) => {
            e.preventDefault();
            const links = this.state.fb_links;
            links.splice( index, 1 );
            this.setState({ fb_links: links });
        }
    }



    render() {

        return (
            <div className='container-fluid'>
                <div className='row'>
                    {( this.state.fb_links || [] ).map( (item, index) => {
                        const _uuid = uuid( );
                        return (

                            <div key={ item.id }>
                                <input type='text' name={'fb_links['+index+'][title]'} placeholder='Title' defaultValue={item.title} />
                                <input type='url'  name={'fb_links['+index+'][link]'} placeholder='Link' defaultValue={item.link}/>
                                <button onClick={this.removeLink(index)}>&times;</button>
                                {
                                    index + 1 == this.state.fb_links.length &&
                                    <button onClick={ this.addNewLink }>+</button>
                                }
                            </div>
                        )
                    })}

                    {(this.state.fb_links || []).length == 0 && <button onClick={ this.addNewLink }>+</button>}
                </div>
            </div>
        );
    }

}