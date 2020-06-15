import React from 'react';
import Typography from '@material-ui/core/Typography';
import ErrorIcon from '@material-ui/icons/Error';
import { Helmet } from 'react-helmet';

export default function Error(props) {
    return (
        <div>
            <Helmet>
                <title>Erreur</title>
            </Helmet>
            <Typography component="h1" variant="h4" align="center" color="error" gutterBottom>
                <ErrorIcon /> ERREUR <ErrorIcon />
            </Typography>
            <hr />
            <Typography align="center" color="textPrimary" gutterBottom>
                {props.children}
            </Typography>
        </div>
    );
}
