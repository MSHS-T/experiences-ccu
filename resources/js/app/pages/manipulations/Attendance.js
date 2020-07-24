import React, { useState, useEffect } from 'react';

import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';

import ArrowLeftIcon from '@material-ui/icons/ArrowLeft';
import ArrowRightIcon from '@material-ui/icons/ArrowRight';
import CancelIcon from '@material-ui/icons/Cancel';

import * as moment from 'moment';
import capitalize from 'lodash/capitalize';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';
import { IconButton } from '@material-ui/core';
import AttendanceDay from './AttendanceDay';

const useStyles = makeStyles(theme => ({
    weekWrapper: {
        border: `1px solid ${theme.palette.divider}`,
    },
    weekChange: {
        padding: theme.spacing(0.5)
    },
    daysWrapper: {
        display:    'flex',
        alignItems: 'stretch',
    },
    dayItem: {
        margin: theme.spacing(1),
        border: `1px solid ${theme.palette.divider}`,
    },
    buttonWrapper: {
        margin:         theme.spacing(2),
        position:       'relative',
        display:        'flex',
        flexDirection:  'column',
        justifyContent: 'center'
    },
    buttonRow: {
        display:        'flex',
        justifyContent: 'center'
    },
    button: {
        margin: theme.spacing(2)
    },
}));

export default function ManipulationAttendance(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    // Data loading states
    const [isDataLoading, setDataLoading] = useState(false);
    const [isManipulationLoading, setManipulationLoading] = useState(false);
    // Data states
    const [manipulationData, setManipulationData] = useState(null);
    const [slotData, setSlotData] = useState([]);
    // Secondary data states
    // const [calendarBounds, setCalendarBounds] = useState([]);
    const [currentMonday, setCurrentMonday] = useState(null);
    // CRUD loading states
    const [isSaveLoading, setSaveLoading] = useState(false);
    // CRUD success states
    const [saveSuccess, setSaveSuccess] = useState(null);

    // Misc states
    const [error, setError] = useState(null);
    const [saveError, setSaveError] = useState(null);

    const storeSlotData = (data) => {
        setSlotData(data);

        if(data.length > 0){
            // Process data to get the calendar bounds
            // setCalendarBounds([
            //     moment(data[0].start).format('YYYY-MM-DD'),
            //     moment(data[data.length-1].end).format('YYYY-MM-DD')
            // ]);
            // Deduce the first monday from the bounds (or the hash if filled)
            if(currentMonday === null){
                const hash = location.hash.slice(1);
                if(hash === ''){
                    setCurrentMonday(moment(data[0].start).startOf('week').format('YYYY-MM-DD'));
                } else {
                    setCurrentMonday(moment(hash, moment.HTML5_FMT.WEEK).startOf('week').format('YYYY-MM-DD'));
                }
            }
        }
    };

    const loadManipulationData = (id) => {
        setManipulationLoading(true);
        setManipulationData(null);

        fetch(Constants.API_MANIPULATIONS_ENDPOINT + id, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                setManipulationData(data);
                setError(null);
                setManipulationLoading(false);
            })
            .catch(err => {
                setManipulationData(null);
                setError(err.message);
                setManipulationLoading(false);
            });
    };

    const loadSlotData = (id) => {
        setDataLoading(true);
        setSlotData([]);

        fetch(Constants.API_SLOTS_ENDPOINT + id, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                storeSlotData(data);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setSlotData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    const saveSlot = (start, fromInput) => {
        setSaveLoading(fromInput);
        setSaveSuccess(null);

        fetch(Constants.API_SLOTS_ENDPOINT + props.match.params.id, {
            method:  'POST',
            headers: {
                'Accept':        'application/json',
                'Authorization': 'bearer ' + accessToken,
                'Content-Type':  'application/json'
            },
            body: JSON.stringify({
                start: start.format('YYYY-MM-DD HH:mm:ss'),
                end:   start.clone().add(interval, 'minutes').format('YYYY-MM-DD HH:mm:ss')
            })
        })
            // Parse JSON response
            .then(response => {
                if(response.ok){
                    return response.json();
                }
                if(response.status == 400){
                    response.json().then((data) => {
                        setSaveError(data.message);
                    });
                } else {
                    setSaveError(`${response.status} (${response.statusText})`);
                }
                setSaveLoading(false);
                throw new Error();
            })
            // Set data in state
            .then(() => {
                setSaveError(null);
                setSaveLoading(false);
                setSaveSuccess(fromInput);
                setTimeout(() => {
                    setSaveSuccess(null);
                    loadSlotData(props.match.params.id);
                }, 1000);
            })
            .catch(() => {});
    };

    const onDaySave = (dayData) => {
        console.log(dayData);
    };

    useEffect(() => {
        if (Object.prototype.hasOwnProperty.call(props.match.params, 'id')) {
            loadManipulationData(props.match.params.id);
            loadSlotData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render


    const momentTime = (time) => moment(time, moment.HTML5_FMT.TIME);
    const momentToTime = (date) => moment(date).format(moment.HTML5_FMT.TIME);
    const diffMinutes = (a, b) => momentTime(b).diff(momentTime(a), 'minutes', true);

    const navigateWeek = (direction) => {
        const newMonday = moment(currentMonday)[direction == 1 ? 'add' : 'subtract'](7, 'days');
        setCurrentMonday(newMonday.format('YYYY-MM-DD'));
        location.hash = newMonday.format(moment.HTML5_FMT.WEEK);
    };
    const tableCaption = (monday) => monday && (
        <Grid container justify="center">
            <Grid item>
                <IconButton
                    aria-label="Semaine précédente"
                    className={classes.weekChange}
                    // disabled={ moment(monday).format('YYYY-MM-DD') <= calendarBounds[0]}
                    onClick={() => { navigateWeek(-1); }}
                >
                    <ArrowLeftIcon/>
                </IconButton>
            </Grid>
            <Grid item xs={4} className={classes.weekWrapper}>
                <Typography component="h2" variant="h5" align="center" color="textPrimary">
                    {`Semaine ${moment(monday).format('ww')} → ${moment(monday).format('DD/MM/YYYY')}-${moment(monday).add(6, 'days').format('DD/MM/YYYY')}`}
                </Typography>
            </Grid>
            <Grid item>
                <IconButton
                    aria-label="Semaine précédente"
                    className={classes.weekChange}
                    onClick={() => { navigateWeek(1); }}
                >
                    <ArrowRightIcon/>
                </IconButton>

            </Grid>
        </Grid>
    );

    if (isDataLoading || isManipulationLoading) {
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
                {`Gestion de la Présence de la manipulation #${props.match.params.id}`}
            </Typography>
            <hr />
            <Grid container spacing={2}>
                <Grid item xs={12}>
                    {manipulationData && (
                        <>
                            {tableCaption(currentMonday)}
                            <div className={classes.daysWrapper}>
                                {[...Array(7).keys()].map(i => {
                                    const day = moment(currentMonday).add(i, 'days');
                                    const daySlots = slotData.filter(s => moment(s.start).isSame(day, 'day') && s.booking !== null);
                                    return (
                                        <AttendanceDay
                                            key={`day-${i}`}
                                            dayLabel={`${capitalize(day.format('dddd'))} ${day.format('D')} ${capitalize(day.format('MMMM'))}`}
                                            daySlots={daySlots}
                                            handleSave={onDaySave}
                                            className={classes.dayItem}
                                        />
                                    );
                                })}
                            </div>
                        </>
                    )}
                </Grid>
                <Grid item xs={12} className={classes.buttonRow}>
                    <div className={classes.buttonWrapper}>
                        <Button
                            variant="contained"
                            color="default"
                            disabled={!!isSaveLoading}
                            className={classes.button}
                            startIcon={<CancelIcon />}
                            onClick={() => props.history.push('/manipulations')}
                        >
                            Retour
                        </Button>
                    </div>
                </Grid>
            </Grid>
        </>
    );
}
