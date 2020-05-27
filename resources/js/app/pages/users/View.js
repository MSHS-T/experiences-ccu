import React, { useState, useEffect } from 'react';

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

const useStyles = makeStyles(theme => ({
    label: {
        fontWeight:     'bold',
        textDecoration: 'underline',
        display:        'inline-block'
    },
    value: {
        fontSize:   '110%',
        display:    'inline-block',
        marginLeft: theme.spacing(2)
    },
    buttonRow: {
        display:        'flex',
        justifyContent: 'center'
    },
    button: {
        margin: theme.spacing(2)
    }
}));

export default function UserView(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isDataLoading, setDataLoading] = useState(false);
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);
    const [deleteEntry, setDeleteEntry] = useState(false);
    const [deleteError, setDeleteError] = useState(null);

    const loadData = (id) => {
        setDataLoading(true);
        setUserData(null);

        fetch(Constants.API_USERS_ENDPOINT + id, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Reprocess data :
            //  - flatten roles array to keep only the key field
            .then(data => ({ ...data, roles: data.roles.map(r => r.name) }))
            // Set data in state
            .then(data => {
                setUserData(data);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setUserData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    const handleDelete = () => {
        setDeleteError(null);
        fetch(Constants.API_USERS_ENDPOINT + userData.id, {
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
                props.history.push('/users');
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
                {`Visualisation de l'utilisateur #${props.match.params.id}`}
            </Typography>
            <hr />
            <Grid container spacing={2}>
                <Grid item xs={12} sm={6}>
                    <Typography className={classes.label}>Prénom :</Typography>
                    <Typography className={classes.value}>{userData && userData.first_name}</Typography>
                </Grid>
                <Grid item xs={12} sm={6}>
                    <Typography className={classes.label}>Nom :</Typography>
                    <Typography className={classes.value}>{userData && userData.last_name}</Typography>
                </Grid>
                <Grid item xs={12}>
                    <Typography className={classes.label}>Adresse Email :</Typography>
                    <Typography className={classes.value}>{userData && userData.email}</Typography>
                </Grid>
                <Grid item xs={12}>
                    <Typography className={classes.label}>Rôles :</Typography>
                    <Typography className={classes.value}>{userData && userData.roles.join(', ')}</Typography>
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
                        onClick={() => props.history.push('/users/' + userData.id + '/edit')}
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
                    {(deleteEntry && userData) ? ('Supprimer l\'utilisateur ' + userData.first_name + ' ' + userData.last_name + ' ?') : ''}
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
