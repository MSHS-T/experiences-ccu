import React, { Component } from 'react';
import Typography from '@material-ui/core/Typography';
import ErrorIcon from '@material-ui/icons/Error';

export default class Error extends Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div>
                <Typography component="h1" variant="h4" align="center" color="error" gutterBottom>
                    <ErrorIcon /> ERREUR <ErrorIcon />
                </Typography>
                <hr />
                <Typography align="center" color="textPrimary" gutterBottom>
                    {this.props.children}
                </Typography>
            </div>
        )
    }
}
