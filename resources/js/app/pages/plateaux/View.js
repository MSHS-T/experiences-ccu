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
    wysiwygvalue: {
        fontSize:    '110%',
        display:     'inline-block',
        borderLeft:  `1px solid ${theme.palette.divider}`,
        paddingLeft: theme.spacing(2)
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

export default function PlateauView(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isDataLoading, setDataLoading] = useState(false);
    const [plateauData, setPlateauData] = useState(null);
    const [error, setError] = useState(null);
    const [deleteEntry, setDeleteEntry] = useState(false);
    const [deleteError, setDeleteError] = useState(null);

    const loadData = (id) => {
        setDataLoading(true);
        setPlateauData(null);

        fetch(Constants.API_PLATEAUX_ENDPOINT + '/' + id, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                setPlateauData(data);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setPlateauData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    const handleDelete = () => {
        setDeleteError(null);
        fetch(Constants.API_PLATEAUX_ENDPOINT + '/' + plateauData.id, {
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
                props.history.push('/plateaux');
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
                {`Visualisation du plateau #${props.match.params.id}`}
            </Typography>
            <hr />
            <Grid container spacing={2}>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Nom</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>{plateauData && plateauData.name}</Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Responsable</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography className={classes.value}>
                        {plateauData && (
                            <Link component={RouterLink} to={`/users/${plateauData.manager.id}`}>
                                {plateauData.manager.name}
                            </Link>
                        )}
                    </Typography>
                </Grid>
                <Grid item xs={4} sm={6}>
                    <Typography className={classes.label}>Description</Typography>
                </Grid>
                <Grid item xs={8} sm={6}>
                    <Typography component="div" className={classes.wysiwygvalue}>{plateauData && ReactHtmlParser(plateauData.description)}</Typography>
                </Grid>
                {plateauData && plateauData.manipulations.length > 0 && (
                    <>
                        <Grid item xs={4} sm={6}>
                            <Typography className={classes.label}>Manipulations</Typography>
                        </Grid>
                        <Grid item xs={8} sm={6}>
                            <Typography className={classes.value}>
                                {
                                    plateauData.manipulations.map((m, i) => (
                                        <Link component={RouterLink} to={`/manipulations/${m.id}`} key={`plateau-manipulations-${i}`}>
                                            {m.name}
                                        </Link>
                                    )).reduce((prev, curr) => [prev, ', ', curr])
                                }
                            </Typography>
                        </Grid>
                    </>
                )}
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
                        onClick={() => props.history.push('/plateaux/' + plateauData.id + '/edit')}
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
                    {(deleteEntry && plateauData) ? ('Supprimer le plateau ' + plateauData.name + ' ?') : ''}
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
