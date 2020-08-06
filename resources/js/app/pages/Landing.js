import React, { useState, useEffect } from 'react';
import Badge from '@material-ui/core/Badge';
import Box from '@material-ui/core/Box';
import Button from '@material-ui/core/Button';
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import CardHeader from '@material-ui/core/CardHeader';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import Container from '@material-ui/core/Container';
import { makeStyles } from '@material-ui/core/styles';

import * as Constants from '../data/Constants';

import Loading from './Loading';
import Footer from '../components/Footer';
import { Avatar } from '@material-ui/core';

const isDarkMode = window.localStorage.getItem('theme') === 'dark';

const useStyles = makeStyles(theme => ({
    '@global': {
        ul: {
            margin:  0,
            padding: 0,
        },
        li: {
            listStyle: 'none',
        },
    },
    avatar: {
        margin:          theme.spacing(1),
        backgroundColor: theme.palette.grey[isDarkMode ? 800 : 200],
        width:           60,
        height:          60
    },
    button: {
        margin: theme.spacing(1, 1.5),
    },
    heroContent: {
        padding:       theme.spacing(6, 0, 6),
        display:       'flex',
        flexDirection: 'column',
        alignItems:    'center',
    },
    cardHeader: {
        backgroundColor: theme.palette.action.disabledBackground,
    },
    cardExperience: {
        height:         '100%',
        display:        'flex',
        flexDirection:  'column',
        alignItems:     'stretch',
        justifyContent: 'start'
    },
    cardContent: {
        flexGrow: 1
    }
}));

export default function Landing() {
    const classes = useStyles();

    // eslint-disable-next-line no-undef
    const presentation_text = { __html: APP_SETTINGS.presentation_text };

    const [isLoading, setLoading] = useState(true);
    const [data, setData] = useState([]);

    const loadData = () => {
        setLoading(true);
        setData([]);

        fetch(Constants.API_MANIPULATIONS_ENDPOINT)
            // Parse JSON response
            .then(data => data.json())
            // Set data in state
            .then(data => {
                setData(data);
                console.log(data);
                setLoading(false);
            });
    };

    useEffect(loadData, []);


    return (
        <div>
            {/* Hero unit */}
            <Container maxWidth="sm" component="main" className={classes.heroContent}>
                <Avatar className={classes.avatar}>
                    <img src={`/favicon${isDarkMode ? '-dark-mode' : ''}.png`} alt="Logo" height="45" />
                </Avatar>
                <Typography component="h1" variant="h2" align="center" color="textPrimary" gutterBottom>
                    Expériences CCU
                </Typography>
                <Typography variant="h5" align="center" color="textSecondary" component="div">
                    <div dangerouslySetInnerHTML={presentation_text}></div>
                </Typography>
            </Container>
            {/* End hero unit */}
            <Container maxWidth="lg" component="main">
                { isLoading && (<Loading />)}
                { !isLoading && (
                    <Grid container spacing={5}>
                        {data.slice(0, 3).map(manip => {
                            return (
                                <Grid item key={manip.id} xs={12} sm={6} md={4}>
                                    <Card className={classes.cardExperience}>
                                        <CardHeader
                                            title={manip.name}
                                            titleTypographyProps={{ align: 'center', component: 'h3', variant: 'h5' }}
                                            className={classes.cardHeader}
                                        />
                                        <CardContent className={classes.cardContent}>
                                            <Typography component="h4" variant="h6" color="textPrimary" align="center">
                                                {`${manip.available_slots_count} créneau${manip.available_slots_count > 0 ? 'x' : ''} disponibles`}
                                            </Typography>
                                            <Typography component="div" variant="subtitle1" align="justify">
                                                <div dangerouslySetInnerHTML={{ __html: manip.description }}></div>
                                            </Typography>
                                        </CardContent>
                                        <CardActions>
                                            <Button fullWidth variant="contained" color="primary">
                                            Rejoindre
                                            </Button>
                                        </CardActions>
                                    </Card>
                                </Grid>
                            );
                        })}
                    </Grid>
                )}
                <Box my={2}>
                    <Grid container spacing={5} alignItems="center" justify="center">
                        <Button variant="outlined" color="secondary">
                            Voir toutes les expériences
                        </Button>
                    </Grid>
                </Box>
            </Container>

            <Footer />
        </div>
    );
}