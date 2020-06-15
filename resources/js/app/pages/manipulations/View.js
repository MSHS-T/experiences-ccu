import React, { useState, useEffect } from 'react';
import ReactHtmlParser from 'react-html-parser';

import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogTitle from '@material-ui/core/DialogTitle';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';

import CancelIcon from '@material-ui/icons/Cancel';
import EditIcon from '@material-ui/icons/Edit';
import DeleteIcon from '@material-ui/icons/Delete';

import * as moment from 'moment';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';
import { Link } from '@material-ui/core';
import RouterLink from '../../components/RouterLink';

const useStyles = makeStyles(theme => ({
    label: {
        fontWeight:     'bold',
        textDecoration: 'underline',
        textAlign:      'right'
    },
    value: {
        fontSize:   '110%',
        display:    'inline-block',
        marginLeft: theme.spacing(2)
    },
    noMargin: {
        marginTop:   0,
        paddingLeft: theme.spacing(1)
    },
    wysiwygvalue: {
        fontSize:    '110%',
        display:     'inline-block',
        borderLeft:  `1px solid ${theme.palette.divider}`,
        marginLeft:  theme.spacing(2),
        paddingLeft: theme.spacing(1)
    },
    buttonRow: {
        display:                      'flex',
        flexDirection:                'column',
        justifyContent:               'center',
        [theme.breakpoints.up('sm')]: {
            flexDirection: 'row',
        }
    },
    button: {
        margin: theme.spacing(1)
    }
}));

export default function ManipulationView(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isDataLoading, setDataLoading] = useState(false);
    const [manipulationData, setManipulationData] = useState(null);
    const [error, setError] = useState(null);
    const [deleteEntry, setDeleteEntry] = useState(false);
    const [deleteError, setDeleteError] = useState(null);

    const loadData = (id) => {
        setDataLoading(true);
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
                setDataLoading(false);
            })
            .catch(err => {
                setManipulationData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    const handleDelete = () => {
        setDeleteError(null);
        fetch(Constants.API_MANIPULATIONS_ENDPOINT + manipulationData.id, {
            method:  'DELETE',
            headers: {
                'Authorization': 'bearer ' + accessToken,
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                setDeleteEntry(false);
                props.history.push('/manipulations');
            })
            .catch(err => {
                setDeleteError(err.message);
                setDeleteEntry(false);
            });
    };

    useEffect(() => {
        if (Object.prototype.hasOwnProperty.call(props.match.params, 'id')) {
            loadData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render

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
                {`Visualisation de la manipulation #${props.match.params.id}`}
            </Typography>
            <hr />
            <Grid container spacing={1}>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Nom</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>{manipulationData && manipulationData.name}</Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Description</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography component="div" className={classes.wysiwygvalue}>{manipulationData && ReactHtmlParser(manipulationData.description)}</Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Plateau</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>
                        { manipulationData && (
                            <Link component={RouterLink} to={`/plateaux/${manipulationData.plateau.id}`}>
                                {manipulationData.plateau.name}
                            </Link>
                        )}
                    </Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Durée</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>{manipulationData && manipulationData.duration} minutes</Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Cible de participants</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>{manipulationData && manipulationData.target_slots}</Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Date de début</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>{manipulationData && moment(manipulationData.start_date).format('DD/MM/YYYY')}</Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Responsables</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>
                        {
                            manipulationData && manipulationData.managers
                                .sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0))
                                .map((m, i) => (
                                    <Link component={RouterLink} to={`/users/${m.id}`} key={`plateau-manager-${i}`}>
                                        {m.name}
                                    </Link>
                                ))
                                .reduce((prev, curr) => [prev, ', ', curr])
                        }
                    </Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Pré-requis</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <div className={classes.value}>
                        <ul className={classes.noMargin}>
                            {manipulationData && manipulationData.requirements.map((r, i) => <li key={`requirement-${i}`}>{r}</li>)}
                        </ul>
                    </div>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Horaires</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <div className={classes.value}>
                        <dl className={classes.noMargin}>
                            {manipulationData && Object.values(manipulationData.available_hours)
                                .filter((d) => d.enabled && (d.am || d.pm))
                                .map((d) => {
                                    const hours = [];
                                    if (d.am) { hours.push(d.start_am + '-' + d.end_am); }
                                    if (d.pm) { hours.push(d.start_pm + '-' + d.end_pm); }
                                    return (
                                        <React.Fragment key={`frag-${d.day}`}>
                                            <dt key={`label-${d.day}`}>
                                                {Constants.DAYS_LABELS[d.day]}
                                            </dt>
                                            <dd key={`hours-${d.day}`}>
                                                {hours.reduce((prev, curr, i) => [prev, <br key={i} />, curr])}
                                            </dd>
                                        </React.Fragment>
                                    );
                                })
                            }
                        </dl>
                    </div>
                </Grid>
                <Grid item xs={12} className={classes.buttonRow}>
                    <Button
                        variant="contained"
                        color="default"
                        className={classes.button}
                        startIcon={<CancelIcon />}
                        onClick={props.history.goBack}
                    >
                        Retour
                    </Button>
                    <Button
                        variant="contained"
                        color="primary"
                        className={classes.button}
                        startIcon={<EditIcon />}
                        onClick={() => props.history.push('/manipulations/' + manipulationData.id + '/edit')}
                    >
                        Modifier
                    </Button>
                    <Button
                        variant="contained"
                        color="secondary"
                        className={classes.button}
                        startIcon={<DeleteIcon />}
                        onClick={() => setDeleteEntry(true)}
                    >
                        Supprimer
                    </Button>
                </Grid>
            </Grid>
            <Dialog
                open={deleteEntry || !!deleteError}
                onClose={() => {
                    setDeleteEntry(false);
                    setDeleteError(null);
                }}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">
                    {(deleteEntry && manipulationData) ? ('Supprimer la manipulation ' + manipulationData.name + ' ?') : ''}
                    {deleteError ? ('Erreur lors de la suppression : ' + deleteError) : ''}
                </DialogTitle>
                {deleteEntry && (
                    <DialogActions>
                        <Button onClick={() => setDeleteEntry(false)} color="secondary">
                            Annuler
                        </Button>
                        <Button onClick={() => handleDelete(deleteEntry)} color="primary" autoFocus>
                            Confirmer
                        </Button>
                    </DialogActions>
                )}
                {deleteError && (
                    <DialogActions>
                        <Button onClick={() => setDeleteError(null)} color="primary" autoFocus>
                            Fermer
                        </Button>
                    </DialogActions>
                )}
            </Dialog>
        </>
    );
}
