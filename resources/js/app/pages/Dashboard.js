import React, { useState, useEffect } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import { GridList, Typography, Card, CardContent } from '@material-ui/core';
import * as moment from 'moment';
import { useAuthContext } from '../context/Auth';
import * as Constants from '../data/Constants';
import Loading from './Loading';

const useStyles = makeStyles(theme => ({
    root: {
        display:        'flex',
        flexWrap:       'wrap',
        justifyContent: 'space-around',
        // overflow:        'hidden',
        // backgroundColor: theme.palette.background.paper,
    },
    card: {
        width:  350,
        height: 300,
        margin: theme.spacing(2)
    },
}));

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
                {data.map(manipulation => (
                    <Card className={classes.card} key={manipulation.id}>
                        <CardContent>
                            <Typography variant="h5" component="h2">
                                {manipulation.name}
                            </Typography>
                            <Typography variant="body1" component="div">
                                Ouvert depuis le {moment(manipulation.start_date).format('DD/MM/YYYY')} ({moment().diff(manipulation.start_date, 'days')} jours)
                            </Typography>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </>
    );

}