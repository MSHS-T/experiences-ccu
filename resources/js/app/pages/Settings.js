import React, { useState } from 'react';
import { Typography, makeStyles, Grid, TextField, Button, CircularProgress, InputAdornment } from '@material-ui/core';
import { Formik } from 'formik';
import * as Yup from 'yup';
import { green } from '@material-ui/core/colors';
import { useAuthContext } from '../context/Auth';
import * as Constants from '../data/Constants';
import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import SaveIcon from '@material-ui/icons/Save';

const useStyles = makeStyles(theme => ({
    form: {
        width:     '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
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
        margin:         theme.spacing(2),
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
}));


export default function Settings(props) {

    const classes = useStyles();
    const { accessToken } = useAuthContext();
    const [isSaveLoading, setSaveLoading] = useState(false);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const saveData = (data) => {
        return fetch(Constants.API_SETTINGS_ENDPOINT, {
            method:  'POST',
            headers: {
                'Accept':        'application/json',
                'Authorization': 'bearer ' + accessToken,
                'Content-Type':  'application/json'
            },
            body: JSON.stringify(data)
        });
    };

    const meProps = props;

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {'Paramètres du site'}
            </Typography>
            <hr />
            <Formik
                // eslint-disable-next-line no-undef
                initialValues={APP_SETTINGS}
                validationSchema={() => {
                    let schema = {
                        booking_cancellation_delay: Yup.number().round()
                            .min(0, 'Minimum autorisé : 0')
                            .required('Requis'),
                        booking_opening_delay: Yup.number().round()
                            .min(0, 'Minimum autorisé : 0')
                            .required('Requis'),
                        manipulation_overbooking: Yup.number().round()
                            .min(100, 'Minimum autorisé : 100')
                            .required('Requis')
                    };
                    return Yup.object().shape(schema);
                }}
                onSubmit={(values, actions) => {
                    if (saveSuccess) return;

                    setSaveLoading(true);
                    saveData(values)
                        .then(response => response.json())
                        .then(data => {
                            if (data.errors) {
                                for (var field in data.errors) {
                                    if (!Object.prototype.hasOwnProperty.call(data.errors, field)) continue;
                                    actions.setFieldError(field, data.errors[field].join(' '));
                                }
                            } else if (data.exception) {
                                actions.setFieldError('general', data.message);
                            } else {
                                setSaveSuccess(true);
                                setTimeout(() => window.location.reload(), Constants.FORM_REDIRECT_TIMEOUT);
                            }
                        })
                        .finally(() => {
                            setSaveLoading(false);
                        });
                }}
            >
                {({ values, errors, touched, handleReset, handleChange, handleSubmit }) => (
                    <form className={classes.form} onSubmit={handleSubmit}>
                        <Grid container spacing={2} className={classes.centralAlignment}>
                            <Grid item xs={4} container spacing={2}>
                                <Grid item xs={12}>
                                    <TextField
                                        id="booking_cancellation_delay"
                                        name="booking_cancellation_delay"
                                        label="Délai de prévenance pour l'annulation d'une réservation"
                                        type="number"
                                        autoComplete="booking_cancellation_delay"
                                        value={values.booking_cancellation_delay}
                                        onChange={handleChange}
                                        error={errors.booking_cancellation_delay && touched.booking_cancellation_delay}
                                        helperText={(touched.booking_cancellation_delay && errors.booking_cancellation_delay || '') + ' (0 : le jour même, 1 : la veille, ...)'}
                                        variant="outlined"
                                        inputProps={{
                                            min:  '0',
                                            step: '1',
                                        }}
                                        InputProps={{
                                            endAdornment: <InputAdornment position="end">Jours</InputAdornment>,
                                        }}
                                        fullWidth
                                        autoFocus
                                    />
                                </Grid>
                                <Grid item xs={12}>
                                    <TextField
                                        id="booking_opening_delay"
                                        name="booking_opening_delay"
                                        label="Délai d'ouverture des inscriptions avant début de la manipulation"
                                        type="number"
                                        autoComplete="booking_opening_delay"
                                        value={values.booking_opening_delay}
                                        onChange={handleChange}
                                        error={errors.booking_opening_delay && touched.booking_opening_delay}
                                        helperText={touched.booking_opening_delay && errors.booking_opening_delay}
                                        variant="outlined"
                                        inputProps={{
                                            min:  '0',
                                            step: '1',
                                        }}
                                        InputProps={{
                                            endAdornment: <InputAdornment position="end">Jours</InputAdornment>,
                                        }}
                                        fullWidth
                                        autoFocus
                                    />
                                </Grid>
                                <Grid item xs={12}>
                                    <TextField
                                        id="manipulation_overbooking"
                                        name="manipulation_overbooking"
                                        label="Pourcentage de surréservation des créneaux de manipulation"
                                        type="number"
                                        autoComplete="manipulation_overbooking"
                                        value={values.manipulation_overbooking}
                                        onChange={handleChange}
                                        error={errors.manipulation_overbooking && touched.manipulation_overbooking}
                                        helperText={touched.manipulation_overbooking && errors.manipulation_overbooking}
                                        variant="outlined"
                                        inputProps={{
                                            min:  '100',
                                            step: '1',
                                        }}
                                        InputProps={{
                                            endAdornment: <InputAdornment position="end">%</InputAdornment>,
                                        }}
                                        fullWidth
                                        autoFocus
                                    />
                                </Grid>
                                <Grid item xs={12} className={classes.buttonRow}>
                                    <div className={classes.buttonWrapper}>
                                        <Button
                                            variant="contained"
                                            color="secondary"
                                            disabled={isSaveLoading}
                                            className={classes.button}
                                            startIcon={<CancelIcon />}
                                            onClick={handleReset}
                                        >
                                            {'Annuler'}
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
                                            {'Modifier'}
                                        </Button>
                                        {isSaveLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                                    </div>
                                </Grid>
                                <Grid item xs={12}>
                                    <Typography component="div" variant="caption" align="center" color="textSecondary" gutterBottom>
                                        {'La page sera rechargée après la sauvegarde afin que les nouveaux paramètres soient pris en compte dans l\'interface'}
                                    </Typography>
                                </Grid>
                            </Grid>
                        </Grid>
                        {errors.general && (
                            <Typography component="p" align="center" color="error">
                                Une erreur s&apos;est produite sur le serveur : <strong>{errors.general}</strong>.
                                <br />
                                Veuillez contacter l&apos;administrateur du site.
                            </Typography>
                        )}
                    </form>
                )}
            </Formik>
        </>
    );
}
