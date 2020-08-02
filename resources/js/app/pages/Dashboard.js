import React, { useState, useEffect } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import { GridList, Typography, Card, CardContent, Grid, Chip, Box, CircularProgress, withStyles } from '@material-ui/core';
import * as moment from 'moment';
import { useAuthContext } from '../context/Auth';
import * as Constants from '../data/Constants';
import Loading from './Loading';
import { grey } from '@material-ui/core/colors';

const useStyles = makeStyles(theme => ({
    root: {
        display:        'flex',
        flexWrap:       'wrap',
        justifyContent: 'space-around',
    },
    card: {
        width:  350,
        height: 300,
        margin: theme.spacing(2)
    }
}));

const OutlinedCircularProgress = withStyles(() => ({
    circleStatic: {
        fill: grey[200],
    },
}))(CircularProgress);

export default function Dashboard() {
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
            //  - get manager names from nested objects
            .then(data => data.map(row => ({
                ...row,
                manager_names: row.managers.map(u => u.name).sort().join(', ')
            })))
            // Set data in state
            .then(data => {
                // Filter data to remove manipulations without slots in the future
                data = data.filter(manipulation => manipulation.slots.filter(s => moment(s.start).isAfter(moment(), 'day')).length > 0);
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
                    return (
                        <Card className={classes.card} key={manipulation.id}>
                            <CardContent>
                                <Typography variant="h5" component="h2">
                                    {manipulation.name}
                                </Typography>
                                <Typography variant="body1" component="div">
                                Ouvert depuis le {moment(manipulation.start_date).format('DD/MM/YYYY')} ({moment().diff(manipulation.start_date, 'days')} jours)
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
                            </CardContent>
                        </Card>
                    ); })}
            </div>
        </>
    );

}