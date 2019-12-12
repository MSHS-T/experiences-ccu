import React from 'react'
import { makeStyles } from '@material-ui/core/styles';
import CircularProgress from '@material-ui/core/CircularProgress';
import Grid from '@material-ui/core/Grid';

const useStyles = makeStyles(theme => ({
    container: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        marginTop: theme.spacing(5)
    }
}));

const Loading = () => {
    const classes = useStyles();
    return (
        <Grid container spacing={2}>
            <Grid item xs={12} className={classes.container}>
                <CircularProgress />
            </Grid>
        </Grid>
    )
}

export default Loading
