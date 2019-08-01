import React from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
// import {GridList, GridTile} from 'material-ui/GridList';
import IconButton from 'material-ui/IconButton';
import NavigationChevronLeft from 'material-ui/svg-icons/navigation/chevron-left';

import Gallery from 'react-photo-gallery';

import AppBar from './AppBar.jsx';

class UserPhotoList extends React.Component
{
    constructor(props) {
        super(props);

        this.uid = props.params.uid;
        this.state = {
            photos: [],
        };
    }

    componentDidMount() {
        this._componentInitHandle();
    }

    componentWillReceiveProps(newProps) {
        if (newProps.params.uid != this.uid) {
            this.uid = newProps.params.uid;
            this._componentInitHandle();
        } else {
            appNode.style.display = 'block';
        }
    }

    _componentInitHandle() {
        appNode.style.display = 'block';
        
        $.ajax({
            url: buildURL('user', 'photos'),
            type: 'POST',
            dataType: 'json',
            data: {uid: this.uid},
        })
        .done(function(photos) {
            if (photos.length) {
                this.state.photos = photos;
            } else {
                this.state.photos = [];
            }
        }.bind(this))
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            this.setState(this.state);
        }.bind(this));

    }

    render() {
        // <GridList
        //     style={styles.box}
        //     cols={4}
        //     cellHeight={'25%'}
        // >
        //     <GridTile>
        //         <img src={'http://demo.thinksns.com/ts4/data/upload/avatar/f5/4f/d2/original_200_200.jpg?v1457687065'} />
        //     </GridTile>
        // </GridList>

        return (
            <MuiThemeProvider muiTheme={muiTheme}>
                <div style={styles.root}>
                    <AppBar
                        title={'相册'}
                        iconElementLeft={
                            <IconButton onTouchTap={goBack}>
                                <NavigationChevronLeft />
                            </IconButton>
                        }
                    />
                    <Gallery photos={this.state.photos} />
                </div>
            </MuiThemeProvider>
        );
    }
}

const styles = {
    root: {
        paddingTop: '50px',
    },
    box: {
        justifyContent: 'space-around',
    }
};

export default UserPhotoList;
