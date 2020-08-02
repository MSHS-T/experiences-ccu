import React, { useState, useEffect } from 'react';

import { Formik } from 'formik';
import * as Yup from 'yup';

import Button from '@material-ui/core/Button';
import CircularProgress from '@material-ui/core/CircularProgress';
import Grid from '@material-ui/core/Grid';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import { green } from '@material-ui/core/colors';

import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import SaveIcon from '@material-ui/icons/Save';

import { useAuthContext } from '../context/Auth';
import * as Constants from '../data/Constants';
import ErrorPage from './Error';
import Loading from './Loading';

const useStyles = makeStyles(theme => ({
    form: {
        width:     '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    buttonRow: {
        display:        'flex',
        justifyContent: 'center'
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

export default function Profile(props) {
    const classes = useStyles();
    const { accessToken, isContextLoading, refreshToken } = useAuthContext();

    const [dataLoading, setDataLoading] = useState(false);
    const [isSaveLoading, setSaveLoading] = useState(false);
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const loadData = () => {
        if(accessToken === ''){
            return;
        }

        setDataLoading(true);
        setUserData(null);

        fetch(Constants.API_PROFILE_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json();
            })
            // Set data in state
            .then(data => {
                const userData = Object.fromEntries(
                    Object.entries(data)
                        .filter(([key]) => ['first_name', 'last_name', 'email'].includes(key))
                );
                setUserData(userData);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setUserData(null);
                setError(err.message);
                setDataLoading(false);
            });
    };

    const saveData = (data) => {
        return fetch(Constants.API_PROFILE_ENDPOINT, {
            method:  'PUT',
            headers: {
                'Accept':        'application/json',
                'Authorization': 'bearer ' + accessToken,
                'Content-Type':  'application/json'
            },
            body: JSON.stringify(data)
        });
    };

    useEffect(() => {
        if(!isContextLoading){
            loadData();
        }
    }, [isContextLoading, accessToken]);

    if (dataLoading) {
        return <Loading />;
    }

    if (error !== null) {
        return (
            <ErrorPage>
                Une erreur s&apos;est produite : <strong>{(error !== null ? error : ('Unknown Error'))}</strong>
            </ErrorPage>
        );
    }

    const meProps = props;

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {'Modification du Profil'}
            </Typography>
            <hr />
            <Formik
                initialValues={{
                    first_name: '',
                    last_name:  '',
                    email:      '',
                    ...userData
                }}
                validationSchema={() => {
                    let schema = {
                        first_name:            Yup.string().required('Requis'),
                        last_name:             Yup.string().required('Requis'),
                        email:                 Yup.string().email().required('Requis'),
                        password:              Yup.string().notRequired(),
                        password_confirmation: Yup.string().when('password', (password, schema) => {
                            return (password && password.length > 0)
                                ? schema.oneOf([Yup.ref('password')], 'Les deux mots de passe doivent correspondre')
                                : schema.notRequired();
                        })
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
                                refreshToken();
                                setTimeout(() => setSaveSuccess(false), Constants.FORM_REDIRECT_TIMEOUT);
                            }
                        })
                        .finally(() => {
                            setSaveLoading(false);
                        });
                }}
            >
                {({ values, errors, touched, handleChange, handleSubmit }) => (
                    <form className={classes.form} onSubmit={handleSubmit}>
                        <Grid container spacing={2}>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    autoComplete="fname"
                                    name="first_name"
                                    value={values.first_name}
                                    onChange={handleChange}
                                    variant="outlined"
                                    required
                                    fullWidth
                                    id="first_name"
                                    label="Prénom"
                                    error={errors.first_name && touched.first_name}
                                    helperText={touched.first_name && errors.first_name}
                                    autoFocus
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    autoComplete="lname"
                                    name="last_name"
                                    value={values.last_name || ''}
                                    onChange={handleChange}
                                    variant="outlined"
                                    required
                                    fullWidth
                                    id="last_name"
                                    label="Nom"
                                    error={errors.last_name && touched.last_name}
                                    helperText={touched.last_name && errors.last_name}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    autoComplete="email"
                                    name="email"
                                    variant="outlined"
                                    required
                                    fullWidth
                                    id="email"
                                    label="Adresse E-Mail"
                                    value={values.email || ''}
                                    onChange={handleChange}
                                    error={errors.email && touched.email}
                                    helperText={touched.email && errors.email}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Typography variant="body2" color="textSecondary">
                                    {'Si vous souhaitez changer votre mot de passe, veuillez remplir les deux champs ci-dessous avec le nouveau mot de passe.'}
                                </Typography>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    autoComplete="current-password"
                                    name="password"
                                    variant="outlined"
                                    fullWidth
                                    id="password"
                                    label="Nouveau Mot de Passe"
                                    type="password"
                                    onChange={handleChange}
                                    error={errors.password && touched.password}
                                    helperText={touched.password && errors.password}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    autoComplete="current-password"
                                    name="password_confirmation"
                                    variant="outlined"
                                    fullWidth
                                    id="password_confirmation"
                                    label="Confirmation du nouveau Mot de Passe"
                                    type="password"
                                    onChange={handleChange}
                                    error={errors.password_confirmation && touched.password_confirmation}
                                    helperText={touched.password_confirmation && errors.password_confirmation}
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
                                        onClick={() => !saveSuccess && meProps.history.push('/plateaux')}
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
                                        {'Enregistrer'}
                                    </Button>
                                    {isSaveLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                                </div>
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
