import React, { useState, useEffect, useMemo } from 'react';

import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import { green } from '@material-ui/core/colors';

import ArrowLeftIcon from '@material-ui/icons/ArrowLeft';
import ArrowRightIcon from '@material-ui/icons/ArrowRight';
import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import CloseIcon from '@material-ui/icons/Close';
import DeleteIcon from '@material-ui/icons/Delete';
import SettingsIcon from '@material-ui/icons/Settings';

import * as moment from 'moment';
import capitalize from 'lodash/capitalize';
import truncate from 'lodash/truncate';

import DayTimeTable from '../../components/DayTimeTable';
import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';
import { CircularProgress, Card, CardContent, IconButton, CardHeader } from '@material-ui/core';

const useStyles = makeStyles(theme => ({
    cardHeader: {
        padding:    theme.spacing(1),
        fontWeight: 'bold',
        fontSize:   '1rem'
    },
    cardContent: {
        padding:        theme.spacing(1),
        '&:last-child': {
            paddingBottom: theme.spacing(1)
        }
    },
    weekWrapper: {
        border: `1px solid ${theme.palette.divider}`,
    },
    weekChange: {
        padding: theme.spacing(0.5)
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
    buttonProgress: {
        color:      green[500],
        position:   'absolute',
        top:        '50%',
        left:       '50%',
        marginTop:  -12,
        marginLeft: -12,
    }
}));

export default function ManipulationSlots(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isSaveLoading, setSaveLoading] = useState(false);
    const [isDeleteLoading, setDeleteLoading] = useState(false);
    const [isDataLoading, setDataLoading] = useState(false);
    const [isManipulationLoading, setManipulationLoading] = useState(false);
    const [manipulationData, setManipulationData] = useState(null);
    const [slotData, setSlotData] = useState([]);
    const [saveSuccess, setSaveSuccess] = useState(false);
    const [deleteSuccess, setDeleteSuccess] = useState(false);
    const [error, setError] = useState(null);
    const [calendarBounds, setCalendarBounds] = useState([]);
    const [currentMonday, setCurrentMonday] = useState(null);

    const storeSlotData = (data) => {
        setSlotData(data);

        if(data.length > 0){
            // Process data to get the calendar bounds
            setCalendarBounds([
                moment(data[0].start).format('YYYY-MM-DD'),
                moment(data[data.length-1].end).format('YYYY-MM-DD')
            ]);
            // Deduce the first monday from the bounds
            if(currentMonday === null){
                setCurrentMonday(moment(data[0].start).startOf('week').format('YYYY-MM-DD'));
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
        setSlotData(null);

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

    const createSlot = (manipulationId, start, end) => {
        setSaveLoading(true);
        setSaveSuccess(false);

        fetch(Constants.API_SLOTS_ENDPOINT + manipulationId, {
            method:  'PUT',
            headers: {
                'Accept':        'application/json',
                'Authorization': 'bearer ' + accessToken,
                'Content-Type':  'application/json'
            },
            body: JSON.stringify({ start, end })
        })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                console.log(data);
                setError(null);
                setSaveLoading(false);
                setSaveSuccess(true);
            })
            .catch(err => {
                setError(err.message);
                setSaveLoading(false);
            });
    };

    const createAllSlots = () => {
        setSaveLoading(true);

        fetch(Constants.API_SLOTS_ENDPOINT + props.match.params.id + '/generate', {
            method:  'POST',
            headers: { 'Authorization': 'bearer ' + accessToken },
        })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                setError(null);
                setSaveSuccess(true);
                setTimeout(() => {
                    setSaveLoading(false);
                    storeSlotData(data);
                }, 1000);
            })
            .catch(err => {
                setError(err.message);
                setSaveLoading(false);
            });
    };

    const deleteSlot = (id, enableEvents = true) => {
        enableEvents && setDeleteLoading(id);
        enableEvents && setDeleteSuccess(null);
        return fetch(Constants.API_SLOTS_ENDPOINT + id, {
            method:  'DELETE',
            headers: { 'Authorization': 'bearer ' + accessToken }
        })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                setError(null);
                enableEvents && setDeleteLoading(null);
                enableEvents && setDeleteSuccess(id);
                setTimeout(() => {
                    enableEvents && setDeleteSuccess(null);
                    enableEvents && loadSlotData(props.match.params.id);
                }, 1000);
            })
            .catch(err => {
                setError(err.message);
                enableEvents && setDeleteLoading(null);
            });
    };

    const deleteSlotsForDay = (day) => {
        setDeleteLoading(day);
        setDeleteSuccess(null);

        var deleting = 0;
        slotData.filter((s) => moment(s.start).isSame(moment(day), 'day')).forEach((slot) => {
            deleting++;
            deleteSlot(slot.id, false).finally(() => {
                deleting--;
                if(deleting == 0){
                    setDeleteLoading(null);
                    setDeleteSuccess(day);
                    setTimeout(() => {
                        setDeleteSuccess(null);
                        loadSlotData(props.match.params.id);
                    }, 1000);
                }
            });
        });
    };

    // const handleSave = (values) => {
    //     setSaveLoading(true);
    //     saveData(values)
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.errors) {
    //                 for (var field in data.errors) {
    //                     if (!Object.prototype.hasOwnProperty.call(data.errors, field)) continue;
    //                     actions.setFieldError(field, data.errors[field].join(' '));
    //                 }
    //             } else if (data.exception) {
    //                 actions.setFieldError('general', data.message);
    //             } else {
    //                 setSaveSuccess(true);
    //                 setTimeout(() => props.history.push('/manipulations'), Constants.FORM_REDIRECT_TIMEOUT);
    //             }
    //         })
    //         .finally(() => {
    //             setSaveLoading(false);
    //         });
    // };

    const getCalendarData = () => {
        var data = [];
        if(slotData !== null && slotData.length > 0 && currentMonday !== null){
            for(let i = 0; i < 7; i++){
                let day = moment(currentMonday).add(i, 'days');
                data.push({
                    name: (
                        <>
                            {capitalize(day.format('dddd'))}
                            <br/>
                            {day.format('D')} {capitalize(day.format('MMMM'))}
                        </>
                    ),
                    date: day.format('YYYY-MM-DD'),
                    info: slotData.filter((s) => moment(s.start).isSame(day, 'day')).map((s) => {
                        return {
                            id:    s.id,
                            start: moment(s.start).format('HH:mm'),
                            end:   moment(s.end).format('HH:mm'),
                            text:  (
                                <div>
                                    <Card style={{ backgroundColor: s.subject_email !== null ? 'lightGreen' : 'orange' }}>
                                        <CardHeader
                                            className={classes.cardHeader}
                                            action={
                                                // TODO : Ask confirmation
                                                <IconButton aria-label="delete single slot" size="small" disabled={!!isDeleteLoading} onClick={() => deleteSlot(s.id)}>
                                                    { isDeleteLoading === s.id && <CircularProgress size={16} />}
                                                    { deleteSuccess === s.id && <CheckIcon />}
                                                    { deleteSuccess !== s.id && isDeleteLoading !== s.id && <CloseIcon />}
                                                </IconButton>
                                            }
                                            title={moment(s.start).format('HH:mm')+ ' - ' + moment(s.end).format('HH:mm')}
                                            disableTypography
                                        />
                                        <CardContent className={classes.cardContent}>
                                            {s.subject_email !== null && (
                                                <>
                                                    {s.subject_first_name} {s.subject_last_name}
                                                    <br />
                                                    <a href={'mailto:'+s.subject_email} title={s.subject_email}>{truncate(s.subject_email, { length: 20 })}</a>
                                                </>
                                            )}
                                        </CardContent>
                                    </Card>
                                </div>
                            )
                        };
                    })
                    // TODO : Add grey blocks outside of available hours
                    // TODO : Prevent crash if a slot is outside available hours
                });
            }
        }
        return data;
    };

    useEffect(() => {
        if (Object.prototype.hasOwnProperty.call(props.match.params, 'id')) {
            loadManipulationData(props.match.params.id);
            loadSlotData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render


    const momentTime = (time) => moment(time, moment.HTML5_FMT.TIME);
    const diffMinutes = (a, b) => momentTime(b).diff(momentTime(a), 'minutes', true);

    const navigateWeek = (direction) => {
        setCurrentMonday(moment(currentMonday)[direction == 1 ? 'add' : 'subtract'](7, 'days').format('YYYY-MM-DD'));
    };
    const createTableCaption = (monday) => monday && (
        <Grid container justify="center">
            <Grid item>
                <IconButton
                    aria-label="Semaine précédente"
                    className={classes.weekChange}
                    disabled={ moment(monday).format('YYYY-MM-DD') <= calendarBounds[0]}
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
    const tableCaption = useMemo(() => createTableCaption(currentMonday), [currentMonday]);

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

    const pageTitle = (
        <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
            {`Gestion des Créneaux de la manipulation #${props.match.params.id}`}
        </Typography>
    );

    if(slotData.length == 0){
        return (
            <>
                {pageTitle}
                <hr/>
                <Typography component="h2" variant="h5" align="center" color="textPrimary" gutterBottom>
                    {'Aucun créneau existant pour cette manipulation.'}
                </Typography>
                <Grid container justify="center">
                    <div className={classes.buttonWrapper}>
                        <Button
                            type="submit"
                            variant="contained"
                            color="primary"
                            size="large"
                            disabled={isSaveLoading}
                            className={saveSuccess ? classes.buttonSuccess : ''}
                            startIcon={saveSuccess ? <CheckIcon /> : <SettingsIcon />}
                            onClick={createAllSlots}
                        >
                            {'Générer les créneaux de manipulation'}
                        </Button>
                        {isSaveLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                    </div>
                </Grid>
            </>
        );
    }

    // Compute calendar settings
    if(manipulationData){
        // TODO : Compute interval based on manipulation duration
        var interval = 30; // 30 minutes rows for the table view
        // TODO : Compute min and max based on real
        var min = Object.values(manipulationData.available_hours).reduce((best, item) => {
            if(item.enabled && item.am) { return item.start_am < best ? item.start_am : best; }
            if(item.enabled && item.pm) { return item.start_pm < best ? item.start_pm : best; }
            return best;
        }, '23:59');
        var max = Object.values(manipulationData.available_hours).reduce((best, item) => {
            if(item.enabled && item.pm) { return item.end_pm > best ? item.end_pm : best; }
            if(item.enabled && item.am) { return item.end_am > best ? item.end_am : best; }
            return best;
        }, '00:01');
        min = momentTime(min).subtract(interval, 'minutes').format('HH:mm');
        max = momentTime(max).add(interval, 'minutes').format('HH:mm');
        var data = getCalendarData();

    }

    return (
        <>
            {pageTitle}
            <hr />
            <Grid container spacing={2}>
                <Grid item xs={12}>
                    {manipulationData && (
                        <DayTimeTable
                            caption={tableCaption}
                            cellKey={cell => cell.id}
                            calcCellHeight={cell => diffMinutes(cell.start, cell.end)/interval }
                            showHeader={col => (
                                <Typography component="h3" variant="h6" align="center" color="textPrimary">
                                    {col.name}
                                </Typography>
                            )}
                            showFooter={col => col.info.length > 0 ? (
                                // TODO : Ask for confirmation
                                <Button
                                    size="small"
                                    variant="outlined"
                                    disabled={!!isDeleteLoading}
                                    onClick={() => deleteSlotsForDay(col.date)}
                                    startIcon={(
                                        <>
                                            { isDeleteLoading === col.date && <CircularProgress size={16} />}
                                            { deleteSuccess === col.date && <CheckIcon />}
                                            { deleteSuccess !== col.date && isDeleteLoading !== col.date && <DeleteIcon />}
                                        </>
                                    )}
                                >
                                    Supprimer la journée
                                </Button>
                            ) : ''}
                            showCell={cell => cell.text}
                            showTime={step => (
                                <center>
                                    {momentTime(min).add(interval * step, 'minutes').format('HH:mm')}
                                </center>
                            )}
                            isActive={(cell, step) => {
                                var current = momentTime(min).add(interval * step, 'minutes');
                                return momentTime(cell.start) <= current && current < momentTime(cell.end);
                            }}
                            tableProps={{ size: 'small' }}
                            timeText=""
                            toolTip="Table has tooltip"
                            max={max}
                            min={min}
                            data={data}
                            rowNum={diffMinutes(min, max)/interval}
                            valueKey="info"
                        />
                    )}
                </Grid>

                <Grid item xs={12} className={classes.buttonRow}>
                    <div className={classes.buttonWrapper}>
                        <Button
                            variant="contained"
                            color="default"
                            disabled={isSaveLoading}
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
