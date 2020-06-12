import React, { useState, useEffect, useMemo } from 'react';

import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { green, orange, red, grey } from '@material-ui/core/colors';

import AddIcon from '@material-ui/icons/Add';
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
import { gcd } from 'mathjs';

import DayTimeTable from '../../components/DayTimeTable';
import LabelledOutline from '../../components/LabelledOutline';
import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';
import { CircularProgress, Card, CardContent, IconButton, CardHeader, Box, LinearProgress } from '@material-ui/core';
import { useConfirm } from 'material-ui-confirm';
import { MuiPickersUtilsProvider, DateTimePicker, DatePicker } from '@material-ui/pickers';
import MomentUtils from '@date-io/moment';

const useStyles = makeStyles(theme => ({
    cardHeader: {
        padding:       theme.spacing(1),
        paddingBottom: theme.spacing(0.5),
        fontWeight:    'bold',
        fontSize:      '1rem'
    },
    cardContent: {
        padding:        theme.spacing(1),
        paddingTop:     theme.spacing(0),
        paddingBottom:  theme.spacing(0.5),
        '&:last-child': {
            paddingBottom: theme.spacing(0.5)
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

const GreenLinearProgress = withStyles(() => ({
    root: {
        height:       10,
        borderRadius: 5,
    },
    colorPrimary: {
        backgroundColor: green[200],
    },
    bar: {
        borderRadius:    5,
        backgroundColor: green[700],
    },
}))(LinearProgress);

const OrangeLinearProgress = withStyles(() => ({
    root: {
        height:       10,
        borderRadius: 5,
    },
    colorPrimary: {
        backgroundColor: orange[200],
    },
    bar: {
        borderRadius:    5,
        backgroundColor: orange[700],
    },
}))(LinearProgress);

const RedLinearProgress = withStyles((theme) => ({
    root: {
        height:       10,
        borderRadius: 5,
    },
    colorPrimary: {
        backgroundColor: grey[theme.palette.type === 'light' ? 200 : 700],
    },
    bar: {
        borderRadius:    5,
        backgroundColor: red[700],
    },
}))(LinearProgress);

export default function ManipulationSlots(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();
    const confirm = useConfirm();

    // Data loading states
    const [isDataLoading, setDataLoading] = useState(false);
    const [isManipulationLoading, setManipulationLoading] = useState(false);
    // Data states
    const [manipulationData, setManipulationData] = useState(null);
    const [slotData, setSlotData] = useState([]);
    // Secondary data states
    const [calendarBounds, setCalendarBounds] = useState([]);
    const [currentMonday, setCurrentMonday] = useState(null);
    // CRUD loading states
    const [isGenerateLoading, setGenerateLoading] = useState(false);
    const [isDeleteLoading, setDeleteLoading] = useState(false);
    const [isCreateLoading, setCreateLoading] = useState(false);
    // CRUD success states
    const [generateSuccess, setGenerateSuccess] = useState(false);
    const [deleteSuccess, setDeleteSuccess] = useState(null);
    const [createSuccess, setCreateSuccess] = useState(null);
    // Next availability states
    const [newSlotDate, setNewSlotDate] = useState(null);
    const [newSlotDatetime, setNewSlotDatetime] = useState(null);

    // Misc states
    const [error, setError] = useState(null);
    const [createError, setCreateError] = useState(null);

    const storeSlotData = (data) => {
        setSlotData(data);

        if(data.length > 0){
            // Process data to get the calendar bounds
            setCalendarBounds([
                moment(data[0].start).format('YYYY-MM-DD'),
                moment(data[data.length-1].end).format('YYYY-MM-DD')
            ]);
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

    const createSlot = (start, fromInput) => {
        setCreateLoading(fromInput);
        setCreateSuccess(null);

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
                        setCreateError(data.message);
                    });
                } else {
                    setCreateError(`${response.status} (${response.statusText})`);
                }
                setCreateLoading(false);
                throw new Error();
            })
            // Set data in state
            .then(() => {
                setCreateError(null);
                setCreateLoading(false);
                setCreateSuccess(fromInput);
                setTimeout(() => {
                    setCreateSuccess(null);
                    loadSlotData(props.match.params.id);
                }, 1000);
            })
            .catch(() => {});
    };

    const generateSlots = (date, fromInput) => {
        setGenerateLoading(fromInput);
        setGenerateSuccess(null);

        fetch(Constants.API_SLOTS_ENDPOINT + props.match.params.id + '/generate', {
            method:  'POST',
            headers: {
                'Authorization': 'bearer ' + accessToken,
                'Content-Type':  'application/json'
            },
            body: JSON.stringify({ date })
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
                setGenerateLoading(false);
                setGenerateSuccess(fromInput);
                setTimeout(() => {
                    setGenerateSuccess(null);
                    storeSlotData(data);
                }, 1000);
            })
            .catch(err => {
                setError(err.message);
                setGenerateLoading(false);
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
        const slots = slotData.filter((s) => moment(s.start).isSame(moment(day), 'day'));
        const hasReservations = slots.filter(s => s.subject_email !== null).length > 0;

        confirm({
            title:       'Confirmation de suppression',
            description: (
                <>
                    {'Êtes-vous sûr de vouloir supprimer ces créneaux ? Cette action est irréversible.'}
                    {hasReservations && (
                        <>
                            <br/>
                            <Typography variant="subtitle2" component="span" color="error">
                                {'Au moins un créneau étant réservé, les participants seront informés de l\'annulation de leurs rendez-vous.'}
                            </Typography>
                        </>
                    )}
                </>
            )
        })
            .then(() => {
                setDeleteLoading(day);
                setDeleteSuccess(null);

                var deleting = slots.length;
                slots.forEach((slot) => {
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
            })
            .catch(() => {});
    };

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
                            start: momentToTime(s.start),
                            end:   momentToTime(s.end),
                            text:  (
                                <div>
                                    <Card style={{ backgroundColor: s.subject_email !== null ? 'lightGreen' : 'orange' }}>
                                        <CardHeader
                                            className={classes.cardHeader}
                                            action={
                                                <IconButton
                                                    aria-label="delete single slot"
                                                    size="small"
                                                    disabled={!!isDeleteLoading}
                                                    onClick={() => {
                                                        confirm({
                                                            title:       'Confirmation de suppression',
                                                            description: (
                                                                <>
                                                                    {'Êtes-vous sûr de vouloir supprimer ce créneau ? Cette action est irréversible.'}
                                                                    {s.subject_email !== null
                                                                        ? (
                                                                            <>
                                                                                <br/>
                                                                                <Typography variant="subtitle2" component="span" color="error">
                                                                                    {'Ce créneau étant réservé, le participant sera informé de l\'annulation de son rendez-vous.'}
                                                                                </Typography>
                                                                            </>
                                                                        ) : ''
                                                                    }
                                                                </>
                                                            )
                                                        })
                                                            .then(() => { deleteSlot(s.id); })
                                                            .catch(() => {});
                                                    }}
                                                >
                                                    { isDeleteLoading === s.id && <CircularProgress size={16} />}
                                                    { deleteSuccess === s.id && <CheckIcon />}
                                                    { deleteSuccess !== s.id && isDeleteLoading !== s.id && <CloseIcon />}
                                                </IconButton>
                                            }
                                            title={momentToTime(s.start) + ' - ' + momentToTime(s.end)}
                                            disableTypography
                                        />
                                        <CardContent className={classes.cardContent}>
                                            {s.subject_email !== null && (
                                                <>
                                                    {s.subject_first_name} {s.subject_last_name}
                                                    <br />
                                                    <a href={'mailto:'+s.subject_email} title={s.subject_email}>
                                                        {truncate(s.subject_email, { length: 20 })}
                                                    </a>
                                                </>
                                            )}
                                        </CardContent>
                                    </Card>
                                </div>
                            )
                        };
                    })
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
    const momentToTime = (date) => moment(date).format(moment.HTML5_FMT.TIME);
    const diffMinutes = (a, b) => momentTime(b).diff(momentTime(a), 'minutes', true);

    const navigateWeek = (direction) => {
        const newMonday = moment(currentMonday)[direction == 1 ? 'add' : 'subtract'](7, 'days');
        setCurrentMonday(newMonday.format('YYYY-MM-DD'));
        location.hash = newMonday.format(moment.HTML5_FMT.WEEK);
    };
    const createTableCaption = (monday) => monday && (
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
    // const tableCaption = useMemo(() => createTableCaption(currentMonday), [currentMonday]);

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
                            disabled={!!isGenerateLoading}
                            className={generateSuccess === 'all' ? classes.buttonSuccess : ''}
                            startIcon={generateSuccess === 'all' ? <CheckIcon /> : <SettingsIcon />}
                            onClick={() => generateSlots(null, 'all')}
                        >
                            {'Générer les créneaux de manipulation'}
                        </Button>
                        {isGenerateLoading === 'all' && <CircularProgress size={24} className={classes.buttonProgress} />}
                    </div>
                </Grid>
            </>
        );
    }

    // Compute calendar settings
    if(manipulationData && slotData){
        // Get an array with unique durations gathered from default duration and slots actual durations
        const slotsDurations = slotData.reduce((all, slot) => {
            const d = diffMinutes(slot.start, slot.end);
            if(!all.includes(d)){
                all.push(d);
            }
            return all;
        }, [manipulationData.duration]);
        // Compute the calendar interval (duration of each row) as the GCD of all the slots durations
        var interval = gcd(...slotsDurations);
        // Compute min and max hours based on manipulation available hours
        var defaultMin = Object.values(manipulationData.available_hours).reduce((best, item) => {
            if(item.enabled && item.am) { return item.start_am < best ? item.start_am : best; }
            if(item.enabled && item.pm) { return item.start_pm < best ? item.start_pm : best; }
            return best;
        }, '23:59');
        var defaultMax = Object.values(manipulationData.available_hours).reduce((best, item) => {
            if(item.enabled && item.pm) { return item.end_pm > best ? item.end_pm : best; }
            if(item.enabled && item.am) { return item.end_am > best ? item.end_am : best; }
            return best;
        }, '00:01');
        // Update min/max based on real slots positioning
        var min = slotData.reduce((best, slot) => momentToTime(slot.start) < best ? momentToTime(slot.start) : best, defaultMin);
        var max = slotData.reduce((best, slot) => momentToTime(slot.start) > best ? momentToTime(slot.end) : best, defaultMax);

        min = momentToTime(momentTime(min).subtract(2 * manipulationData.duration, 'minutes'));
        max = momentToTime(momentTime(max).add(2 * manipulationData.duration, 'minutes'));
        var data = getCalendarData();

        // TODO : Get real overbooking setting
        var overbooking = 110;
        var slotCountProgress = (slotData.length / manipulationData.target_slots)*100;

        // Store next availability
        var firstAvailableDay = moment(slotData[slotData.length-1].end);
        // eslint-disable-next-line no-constant-condition
        while(true){
            // Add 1 day until we find an enabled day
            firstAvailableDay.add(1, 'day');
            const dayHours = manipulationData.available_hours[firstAvailableDay.clone().locale('en').format('ddd')];
            if(dayHours.enabled){
                // Now set the time to the start of the day
                if(dayHours.am){
                    const [start_h, start_m] = dayHours.start_am.split(':');
                    firstAvailableDay.hour(start_h).minutes(start_m);
                    break;
                }
                if(dayHours.pm){
                    const [start_h, start_m] = dayHours.start_pm.split(':');
                    firstAvailableDay.hour(start_h).minutes(start_m);
                    break;
                }
            }
        }
        if(newSlotDatetime === null){
            setNewSlotDatetime(firstAvailableDay);
            setNewSlotDate(firstAvailableDay);
        }
    }

    return (
        <>
            {pageTitle}
            <hr />
            <Grid container spacing={2}>
                <Grid item xs={12}>
                    {manipulationData && (
                        <DayTimeTable
                            caption={createTableCaption(currentMonday)}
                            cellKey={cell => cell.id}
                            cellStyle={(rowIndex, colIndex,) => {
                                if(colIndex < 0){ return {}; }
                                const day = Object.values(manipulationData.available_hours)[colIndex];
                                const cellTime = momentToTime(momentTime(min).add(interval * rowIndex, 'minutes'));

                                const isGreyed = !day.enabled
                                    || (!day.am && (cellTime < day.start_pm || cellTime >= day.end_pm))
                                    || (!day.pm && (cellTime < day.start_am || cellTime >= day.end_am))
                                    || (cellTime < day.start_am || (cellTime >= day.end_am && cellTime < day.start_pm) || cellTime >= day.end_pm);

                                return isGreyed ? {
                                    background: 'repeating-linear-gradient( -55deg, #ddd, #ddd 10px, #fff 10px, #fff 20px )'
                                } : {};
                            }}
                            calcCellHeight={cell => Math.ceil(diffMinutes(cell.start, cell.end)/interval) }
                            showHeader={col => (
                                <Typography component="h3" variant="h6" align="center" color="textPrimary">
                                    {col.name}
                                </Typography>
                            )}
                            showFooter={col => col.info.length > 0 ? (
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
                                    {momentToTime(momentTime(min).add(interval * step, 'minutes'))}
                                </center>
                            )}
                            isActive={(cell, step) => {
                                var current = momentTime(min).add(interval * step, 'minutes');
                                return momentTime(cell.start) <= current && current < momentTime(cell.end);
                            }}
                            tableProps={{ size: 'small' }}
                            timeText=""
                            toolTip="Table has tooltip"
                            rowStyle={{ height: '50px' }}
                            max={max}
                            min={min}
                            data={data}
                            rowNum={diffMinutes(min, max)/interval}
                            valueKey="info"
                        />
                    )}
                </Grid>
                <Grid item xs={12} container justify="center">
                    <Grid item xs={8} container>
                        <Grid item xs={4}>
                            <Typography component="p" variant="body1" align="left" color="textPrimary">
                                {'Cible de participants :'} <strong>{manipulationData.target_slots}</strong>
                            </Typography>
                        </Grid>
                        <Grid item xs={4}>
                            <Typography component="p" variant="body1" align="right" color="textPrimary">
                                {'Nombre de créneaux créés :'} <strong>{slotData.length}</strong>
                            </Typography>
                        </Grid>
                        <Grid item xs={4}>
                            <Typography component="p" variant="body1" align="center" color="textPrimary">
                                {/* // TODO : Fetch real overbooking setting */}
                                {'Paramétrage de surréservation :'} <strong>{overbooking+'%'}</strong>
                            </Typography>
                        </Grid>
                        <Grid item xs={12}>
                            <Box
                                display="flex"
                                alignItems="center"
                                color={slotCountProgress < 100 ? 'error.main' : (slotCountProgress < 110 ? 'warning.main' : 'success.main')}
                            >
                                <Box width="100%" mr={1}>
                                    {slotCountProgress < 100 && <RedLinearProgress variant="determinate" value={slotCountProgress} />}
                                    {slotCountProgress < overbooking && slotCountProgress >= 100 && <OrangeLinearProgress variant="determinate" value={slotCountProgress-100} />}
                                    {slotCountProgress >= overbooking && <GreenLinearProgress variant="determinate" value={Math.min(slotCountProgress-100, 100)} />}
                                </Box>
                                <Box minWidth={35}>
                                    <Typography variant="body2" component="div" color="inherit" align="right">
                                        <strong>
                                            {Math.floor(slotCountProgress)+'%'}
                                        </strong>
                                    </Typography>
                                </Box>
                            </Box>
                        </Grid>
                    </Grid>
                </Grid>
                <Grid item xs={12} container>
                    <MuiPickersUtilsProvider utils={MomentUtils}>
                        <Grid item xs={4} container justify="center">
                            <LabelledOutline id="newSlotDatetime" label="Ajouter un créneau">
                                <DateTimePicker
                                    ampm={false}
                                    format="DD/MM/YYYY HH:mm"
                                    minutesStep={5}
                                    value={newSlotDatetime}
                                    onChange={date => { setNewSlotDatetime(date); }}
                                />
                                <Button
                                    variant="contained"
                                    color="default"
                                    size="small"
                                    ml={2}
                                    disabled={!!isCreateLoading}
                                    startIcon={(
                                        <>
                                            { isCreateLoading === 'newSlotDatetime' && <CircularProgress size={16} />}
                                            { createSuccess === 'newSlotDatetime' && <CheckIcon />}
                                            { createSuccess !== 'newSlotDatetime' && isCreateLoading !== 'newSlotDatetime' && <AddIcon />}
                                        </>
                                    )}
                                    onClick={() => createSlot(newSlotDatetime.format('YYYY-MM-DD HH:mm'), 'newSlotDatetime')}
                                >
                                    Ajouter
                                </Button>
                                <br/>
                                {createError && (
                                    <Typography component="p" variant="subtitle2" align="center" color="error">
                                        <strong>{createError}</strong>
                                    </Typography>
                                )}
                            </LabelledOutline>
                        </Grid>
                        <Grid item xs={4} container justify="center">
                            <LabelledOutline id="newSlotDatetime" label="Remplir une journée">
                                <DatePicker
                                    ampm={false}
                                    format="DD/MM/YYYY"
                                    minutesStep={5}
                                    value={newSlotDate}
                                    onChange={date => { setNewSlotDate(date); }}
                                />
                                <Button
                                    variant="contained"
                                    color="default"
                                    size="small"
                                    ml={2}
                                    disabled={!!isGenerateLoading}
                                    startIcon={(
                                        <>
                                            { isGenerateLoading === 'newSlotDate' && <CircularProgress size={16} />}
                                            { generateSuccess === 'newSlotDate' && <CheckIcon />}
                                            { generateSuccess !== 'newSlotDate' && isGenerateLoading !== 'newSlotDate' && <AddIcon />}
                                        </>
                                    )}
                                    onClick={() => generateSlots(newSlotDate.format('YYYY-MM-DD HH:mm'), 'newSlotDate')}
                                >
                                    Remplir
                                </Button>
                            </LabelledOutline>
                        </Grid>
                        <Grid item xs={4} container justify="center">
                            <LabelledOutline id="newSlotDatetime" label="Générer jusqu'à remplissage">

                                <Button
                                    variant="contained"
                                    color="default"
                                    size="small"
                                    ml={2}
                                    disabled={!!isCreateLoading || !!isGenerateLoading}
                                    startIcon={(
                                        <>
                                            { isGenerateLoading === 'fromDate' && <CircularProgress size={16} />}
                                            { generateSuccess === 'fromDate' && <CheckIcon />}
                                            { generateSuccess !== 'fromDate' && isGenerateLoading !== 'fromDate' && <SettingsIcon />}
                                        </>
                                    )}
                                    onClick={() => generateSlots(null, 'fromDate')}
                                >
                                    Générer à partir du {firstAvailableDay.format('DD/MM/YYYY')}
                                </Button>
                            </LabelledOutline>
                        </Grid>
                    </MuiPickersUtilsProvider>
                </Grid>
                <Grid item xs={12} className={classes.buttonRow}>
                    <div className={classes.buttonWrapper}>
                        <Button
                            variant="contained"
                            color="default"
                            disabled={!!isGenerateLoading || !!isDeleteLoading || !!isCreateLoading}
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
