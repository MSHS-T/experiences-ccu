import React, { useState, useEffect } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import { Typography, Card, CardContent, Grid, Box, CircularProgress, withStyles, Button } from '@material-ui/core';
import * as moment from 'moment';
import { useAuthContext } from '../context/Auth';
import * as Constants from '../data/Constants';
import Loading from './Loading';
import { grey } from '@material-ui/core/colors';
import AssignmentTurnedInIcon from '@material-ui/icons/AssignmentTurnedIn';
import PlaylistAddCheckIcon from '@material-ui/icons/PlaylistAddCheck';
import { capitalize } from 'lodash';
import DropdownButton from '../components/DropdownButton';

const useStyles = makeStyles(theme => ({
    root: {
        display:        'flex',
        flexWrap:       'wrap',
        justifyContent: 'space-around',
    },
    card: {
        width:  400,
        // height: 350,
        margin: theme.spacing(2)
    },
    buttonCaption: {
        textAlign: 'center',
        fontStyle: 'italic'
    },
    buttonContainer: {
        marginTop:      theme.spacing(2),
        display:        'flex',
        flexDirection:  'column',
        justifyContent: 'start',
        alignItems:     'center'
    },
}));

const OutlinedCircularProgress = withStyles(theme => ({
    circleStatic: {
        fill: theme.palette.background.default,
    }
}), { withTheme: true })(CircularProgress);

export default function Dashboard(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isLoading, setLoading] = useState(true);
    const [data, setData] = useState([]);

    const loadData = () => {
        setLoading(true);
        setData([]);

        fetch(Constants.API_MANIPULATIONS_ENDPOINT + 'all', { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Reprocess data :
            //  - remove archived manipulations
            //  - remove inactive manipulations (without slots in the future)
            //  - get manager names from nested objects
            .then(data => data
                .filter(row => row.deleted_at === null)
                .filter(row => row.slots.filter(s => moment(s.start).isAfter(moment(), 'day')).length > 0)
                .map(row => ({
                    ...row,
                    manager_names: row.managers.map(u => u.name).sort().join(', ')
                }))
            )
            // Set data in state
            .then(data => {
                setData(data);
                setLoading(false);
            });
    };

    useEffect(loadData, []); // Empty array means useEffect will only be called on first render

    if (isLoading) {
        return <Loading />;
    }

    if (data.length == 0){
        return (
            <>
                <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                    {'Tableau de Bord'}
                </Typography>
                <hr />
                <Typography component="h2" variant="h5" align="center" color="textPrimary" gutterBottom>
                    {'Aucune manipulation en cours.'}
                </Typography>
            </>
        );
    }

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {'Tableau de Bord'}
            </Typography>
            <hr />
            <div className={classes.root}>
                {data.map(manipulation => {
                    const slotsBookedConfirmed = manipulation.slots.filter(s => s.booking !== null && s.booking.confirmed).length;
                    const slotsBookedUnconfirmed = manipulation.slots.filter(s => s.booking !== null && !s.booking.confirmed).length;
                    const honoredBookings = manipulation.slots.filter(s => s.booking !== null && s.booking.honored).length;
                    const progress = Math.floor(Math.min(honoredBookings, manipulation.target_slots) / manipulation.target_slots * 100);

                    const attendanceStatus = manipulation.slots.filter(s => moment(s.start).isBefore(moment(), 'day') && s.booking !== null && s.booking.honored === null);
                    const callSheets = manipulation.slots
                        .filter(s => {
                            const diff = moment(s.start).diff(moment().startOf('day'), 'days', true);
                            return diff > 0 && diff < 7;
                        })
                        .reduce((all, s) => {
                            const day = moment(s.start).format('YYYY-MM-DD');
                            if(!all.includes(day)){
                                all.push(day);
                            }
                            return all.sort();
                        }, [])
                        .reduce((obj, day) => {
                            obj[day] = `${capitalize(moment(day).format('dddd'))} ${moment(day).format('D')} ${capitalize(moment(day).format('MMMM'))} ${moment(day).format('YYYY')}`;
                            return obj;
                        }, {});

                    return (
                        <Card className={classes.card} key={manipulation.id}>
                            <CardContent>
                                <Typography variant="h5" component="h2" align="center">
                                    {manipulation.name}
                                </Typography>
                                <Typography variant="body1" component="div" align="center" gutterBottom>
                                Ouverte depuis le {moment(manipulation.start_date).format('DD/MM/YYYY')} ({moment().diff(manipulation.start_date, 'days')} jours)
                                </Typography>
                                <Box width={'100%'} display="flex" alignItems="center" justifyContent="center">
                                    <Box position="relative" display="inline-flex">
                                        <OutlinedCircularProgress variant="static" value={progress} size={160} />
                                        <Box
                                            top={0}
                                            left={0}
                                            bottom={0}
                                            right={0}
                                            position="absolute"
                                            display="flex"
                                            alignItems="center"
                                            justifyContent="center"
                                            flexDirection="column"
                                        >
                                            <Typography variant="h6" component="div" color="textPrimary">
                                                {`${progress}%`}
                                            </Typography>
                                            <Typography variant="caption" component="div" color="textSecondary">
                                                <center>
                                                    <strong>{manipulation.target_slots}</strong> ciblés
                                                    <br/>
                                                    <strong>{manipulation.slots.length}</strong> créneaux
                                                    <br/>
                                                    <strong>{`${slotsBookedConfirmed} (+${slotsBookedUnconfirmed})`}</strong> inscriptions
                                                    <br/>
                                                    <strong>{honoredBookings}</strong> participants
                                                    <br/>
                                                    <br/>
                                                </center>
                                            </Typography>
                                        </Box>
                                    </Box>
                                </Box>
                                <Grid container spacing={2}>
                                    <Grid item xs={5} className={classes.buttonContainer}>
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            size="small"
                                            startIcon={<AssignmentTurnedInIcon />}
                                            onClick={() => {
                                                var week = '';
                                                if(attendanceStatus.length > 0){
                                                    week = '#' + moment(attendanceStatus[0].start).format(moment.HTML5_FMT.WEEK);
                                                }
                                                props.history.push('/manipulations/' + manipulation.id + '/attendance' + week);
                                            }}
                                        >
                                        Présence
                                        </Button>
                                        <Typography
                                            variant="caption"
                                            className={classes.buttonCaption}
                                            component="div"
                                            color={attendanceStatus.length > 0 ? 'error' : 'textSecondary'}
                                        >
                                            {attendanceStatus.length > 0 ? 'Action requise' : 'A jour'}
                                        </Typography>
                                    </Grid>
                                    <Grid item xs={7} className={classes.buttonContainer}>
                                        <DropdownButton
                                            variant="contained"
                                            color="primary"
                                            size="small"
                                            startIcon={<PlaylistAddCheckIcon />}
                                            onClick={(chosenDay) => {
                                                window.open(
                                                    Constants.API_SLOTS_ENDPOINT + manipulation.id + '/call_sheet?date='+chosenDay,
                                                    '_blank'
                                                );
                                            }}
                                            options={callSheets}
                                        >
                                            {'Feuille d\'Appel'}
                                        </DropdownButton>
                                    </Grid>
                                </Grid>
                            </CardContent>
                        </Card>
                    ); })}
            </div>
        </>
    );

}