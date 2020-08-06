import React, { useState, useEffect } from 'react';
import ReactHtmlParser from 'react-html-parser';

import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import { makeStyles, useTheme } from '@material-ui/core/styles';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';
import { Container, Card, CardHeader, CardContent, CardActions, Divider, Badge, Chip, useMediaQuery } from '@material-ui/core';
import RouterLink from '../../components/RouterLink';

import * as moment from 'moment';

const useStyles = makeStyles(theme => ({
    '@global': {
        p: {
            marginTop: 0
        }
    },
    card: {
        marginBottom: theme.spacing(2)
    },
    cardHeader: {
        backgroundColor: theme.palette.action.disabledBackground,
    },
    cardContentBlock: {
        borderColor:                    theme.palette.divider,
        padding:                        theme.spacing(0, 2),
        [theme.breakpoints.down('xs')]: {
            borderBottom: '1px solid'
        },
        [theme.breakpoints.up('sm')]: {
            borderRight: '1px solid'
        }
    },
    cardCounter: {
        padding:        theme.spacing(2),
        fontSize:       '1.2rem',
        display:        'flex',
        flexDirection:  'column',
        justifyContent: 'start',
        alignItems:     'stretch',
        textAlign:      'center',
        '& hr':         {
            width: '100%'
        }
    },
    cardActions: {
        padding:        theme.spacing(0, 2, 1),
        display:        'flex',
        justifyContent: 'center'
    }
}));

export default function ManipulationList() {
    const classes = useStyles();
    const theme = useTheme();
    const xsDown = useMediaQuery(theme.breakpoints.down('xs'));
    const { accessToken } = useAuthContext();

    const [isDataLoading, setDataLoading] = useState(true);
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);

    const loadData = () => {
        setDataLoading(true);
        setData(null);

        fetch(Constants.API_MANIPULATIONS_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                setData(data);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    useEffect(loadData, []); // Empty array means useEffect will only be called on first render

    if (isDataLoading) {
        return <Loading />;
    }
    if (error !== null) {
        return (
            <ErrorPage>
                Une erreur s&apos;est produite : <strong>{(error !== null ? error : ('No data'))}</strong>
            </ErrorPage>
        );
    }

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {'Liste des manipulations ouvertes'}
            </Typography>
            <hr />
            {isDataLoading && (<Loading/>)}
            {!isDataLoading && data.length > 0 && (
                <Container maxWidth="lg" component="main">
                    {data.map(manip => (
                        <Card key={manip.id} className={classes.card}>
                            <CardHeader
                                title={manip.name}
                                titleTypographyProps={{ align: 'center', component: 'h3', variant: 'h5', gutterBottom: false }}
                                className={classes.cardHeader}
                            />
                            <CardContent>
                                <Grid container alignItems="stretch">
                                    <Grid item xs={12} sm={6} className={classes.cardContentBlock}>
                                        <Typography component="div" variant="subtitle2" align="justify">
                                            {ReactHtmlParser(manip.description)}
                                        </Typography>
                                    </Grid>
                                    <Grid item xs={12} sm={4} className={classes.cardContentBlock}>
                                        <Typography component="div" variant="subtitle2" align="justify">
                                            {'Pour participer vous devez pouvoir justifier des critères suivants :'}
                                        </Typography>
                                        <ul>
                                            {manip.requirements.map((r, i) => <li key={`requirement-${i}`}>{r}</li>)}
                                        </ul>
                                    </Grid>
                                    <Grid item xs={12} sm={2} className={classes.cardCounter}>
                                        <Typography component="div" variant="subtitle2">
                                            <Chip label={manip.available_slots_count}/>
                                            {` créneau${manip.available_slots_count > 0 ? 'x' : ''} disponibles`}
                                        </Typography>
                                        <hr/>
                                        <Typography component="div" variant="subtitle2">
                                            {'Durée '}
                                            <Chip label={manip.duration}/>
                                            {' minutes'}
                                        </Typography>
                                        <hr/>
                                        <Typography component="div" variant="subtitle2">
                                            {'Prochain créneau disponible : '}
                                            <Chip label={moment(manip.available_slots.shift().start).format('DD/MM/YYYY HH:mm')}/>
                                        </Typography>

                                    </Grid>
                                </Grid>
                            </CardContent>
                            <CardActions className={classes.cardActions}>
                                <Button variant="contained" color="primary" fullWidth={xsDown}>
                                    Rejoindre
                                </Button>
                            </CardActions>
                        </Card>
                    ))}
                </Container>
            )}
        </>
    );
}
