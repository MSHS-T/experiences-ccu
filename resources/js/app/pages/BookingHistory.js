import React, { useState } from 'react';
import { Typography, makeStyles, Grid, TextField, Button, CircularProgress, InputAdornment, Fab } from '@material-ui/core';
import { green, blue } from '@material-ui/core/colors';
import { useAuthContext } from '../context/Auth';
import * as Constants from '../data/Constants';
import SearchIcon from '@material-ui/icons/Search';
import BookingStats from '../components/BookingStats';

const useStyles = makeStyles(theme => ({
    form: {
        width:     '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
        display:   'flex',
    },
    searchInput: {
        flexGrow:  1,
        marginTop: '6px'
    },
    buttonRow: {
        display:        'flex',
        justifyContent: 'center'
    },
    centralAlignment: {
        display:       'flex',
        flexDirection: 'column',
        alignItems:    'center'
    },
    buttonWrapper: {
        margin:         '6px',
        position:       'relative',
        display:        'flex',
        flexDirection:  'column',
        justifyContent: 'center'
    },
    buttonSuccess: {
        backgroundColor: green[500],
        '&:hover':       {
            backgroundColor: green[700],
        },
    },
    buttonProgress: {
        color:      green[500],
        position:   'absolute',
        top:        '50%',
        left:       '50%',
        marginTop:  -12,
        marginLeft: -12,
    },
    fabProgress: {
        color:    blue[500],
        position: 'absolute',
        top:      -6,
        left:     -6,
        zIndex:   1,
    },
}));


export default function BookingHistory() {

    const classes = useStyles();
    const { accessToken } = useAuthContext();
    const [email, setEmail] = useState('');
    const [isSaveLoading, setSaveLoading] = useState(false);

    const [isLoading, setLoading] = useState(false);
    const [data, setData] = useState({});

    const loadData = (email) => {
        setLoading(true);
        setData({});

        fetch(Constants.API_BOOKING_HISTORY_ENDPOINT + '?email=' + encodeURIComponent(email), { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Set data in state
            .then(data => {
                setData(data);
                setLoading(false);
            })
            .catch(err => {
                setData(null);
                setLoading(false);
            });
    };

    const updateData = () => {
        setSaveLoading(true);

        return fetch(Constants.API_BOOKING_HISTORY_ENDPOINT + '?email=' + encodeURIComponent(email), {
            method:  'PUT',
            headers: {
                'Accept':        'application/json',
                'Authorization': 'bearer ' + accessToken,
                'Content-Type':  'application/json'
            }
        })
            .then(() => {
                setSaveLoading(false);
                loadData(email);
            })
            .catch(() => {
                setSaveLoading(false);
                setData(null);
            });
    };

    const handleSubmit = e => {
        e.preventDefault();
        loadData(email);
    };

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {'Historique de réservations'}
            </Typography>
            <hr />
            <Grid container spacing={2} className={classes.centralAlignment}>
                <Grid item xs={4} container spacing={2}>
                    <Grid item xs={12}>
                        <form onSubmit={handleSubmit} className={classes.form}>
                            <TextField
                                id="email"
                                name="email"
                                label="Email"
                                type="email"
                                autoComplete="email"
                                value={email}
                                onChange={e => setEmail(e.target.value)}
                                variant="outlined"
                                className={classes.searchInput}
                                autoFocus
                            />
                            <div className={classes.buttonWrapper}>
                                <Fab
                                    aria-label="save"
                                    color="primary"
                                    type="submit"
                                >
                                    <SearchIcon />
                                </Fab>
                                {isLoading && <CircularProgress size={68} className={classes.fabProgress} />}
                            </div>
                        </form>
                    </Grid>
                    <Grid item xs={12}>
                        {!isLoading && data === null && (
                            <>
                                <Typography component="div" variant="body1" align="center" color="error" gutterBottom>
                                    {'L\'adresse email n\'a pas été trouvée dans la base de données'}
                                </Typography>
                            </>
                        )}
                        {!isLoading && data !== null && Object.entries(data).length > 0 && (
                            <BookingStats data={data} />
                        )}
                    </Grid>
                    <Grid item xs={12}>
                        {!isLoading && data !== null && Object.entries(data).length > 0 && (
                            <>
                                <Typography component="div" variant="body1" align="center" gutterBottom>
                                    Adresse e-mail <strong>{data.blocked ? 'bloquée' : 'autorisée'}</strong>.
                                    <br/>
                                    <div className={classes.buttonWrapper}>
                                        <Button
                                            variant="contained"
                                            color="primary"
                                            disabled={isSaveLoading}
                                            onClick={updateData}
                                        >
                                            {data.blocked ? 'Débloquer' : 'Bloquer'}
                                        </Button>
                                        {isSaveLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                                    </div>
                                </Typography>
                            </>
                        )}
                    </Grid>
                </Grid>
            </Grid>
        </>
    );
}
