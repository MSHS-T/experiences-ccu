import React, { useState, useEffect } from 'react';

import Button from '@material-ui/core/Button';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';

import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import SaveIcon from '@material-ui/icons/Save';

import * as moment from 'moment';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';
import { CircularProgress } from '@material-ui/core';

const useStyles = makeStyles(theme => ({
    label: {
        fontWeight: 'bold',
        textDecoration: 'underline',
        display: 'block',
        float: 'left'
    },
    value: {
        fontSize: '110%',
        display: 'inline-block',
        marginLeft: theme.spacing(2)
    },
    noMargin: {
        marginTop: 0,
        paddingLeft: theme.spacing(1)
    },
    wysiwygvalue: {
        fontSize: '110%',
        display: 'inline-block',
        borderLeft: `1px solid ${theme.palette.divider}`,
        marginLeft: theme.spacing(2),
        paddingLeft: theme.spacing(1)
    },
    buttonRow: {
        display: 'flex',
        justifyContent: 'center'
    },
    button: {
        margin: theme.spacing(2)
    }
}));

export default function ManipulationSlots(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isSaveLoading, setSaveLoading] = useState(false);
    const [isDataLoading, setDataLoading] = useState(false);
    const [slotData, setSlotData] = useState(null);
    const [error, setError] = useState(null);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const loadData = (id) => {
        setDataLoading(true);
        setSlotData(null);

        fetch(Constants.API_SLOTS_ENDPOINT + id, { headers: { 'Authorization': 'bearer ' + accessToken } })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                setSlotData(data);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setSlotData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    const saveData = (data) => {
        return fetch(Constants.API_MANIPULATIONS_ENDPOINT + props.match.params.id, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'bearer ' + accessToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
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
                {`Gestion des Créneaux de la manipulation #${props.match.params.id}`}
            </Typography>
            <hr />
            <Grid container spacing={2}>
                <Grid item xs={12}>
                    
                </Grid>
                
                <Grid item xs={12} className={classes.buttonRow}>
                    <div className={classes.buttonWrapper}>
                        <Button
                            variant="contained"
                            color="secondary"
                            disabled={isSaveLoading}
                            className={classes.button}
                            startIcon={<CancelIcon />}
                            onClick={() => !saveSuccess && props.history.push('/manipulations')}
                        >
                            Annuler
                        </Button>
                    </div>
                    <div className={classes.buttonWrapper}>
                        <Button    
                            type="submit"
                            variant="contained"
                            color="primary"
                            size="large"
                            disabled={isSaveLoading}
                            className={saveSuccess ? classes.buttonSuccess : ''}
                            startIcon={saveSuccess ? <CheckIcon /> : <SaveIcon />}
                        >
                            Enregistrer
                        </Button>
                        {isSaveLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                    </div>
                </Grid>
            </Grid>
        </>
    );
}
