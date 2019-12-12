import React, { Component, useState, useEffect } from "react";
import { Formik, Field, FieldArray } from "formik";
import * as Yup from 'yup';

import Button from '@material-ui/core/Button';
import Checkbox from '@material-ui/core/Checkbox';
import CircularProgress from "@material-ui/core/CircularProgress";
import FormControl from '@material-ui/core/FormControl';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormGroup from '@material-ui/core/FormGroup';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormLabel from '@material-ui/core/FormLabel';
import Grid from '@material-ui/core/Grid';
import TextField from '@material-ui/core/TextField';
import Typography from "@material-ui/core/Typography";
import { makeStyles } from '@material-ui/core/styles';
import { green } from '@material-ui/core/colors';

import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import SaveIcon from '@material-ui/icons/Save';

import { useAuthContext } from "../../context/Auth";
import ErrorPage from "../Error";
import Loading from "../Loading";

const useStyles = makeStyles(theme => ({
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    buttonRow: {
        display: 'flex',
        justifyContent: 'center'
    },
    buttonWrapper: {
        margin: theme.spacing(2),
        position: 'relative',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'center'
    },
    buttonSuccess: {
        backgroundColor: green[500],
        '&:hover': {
            backgroundColor: green[700],
        },
    },
    buttonProgress: {
        color: green[500],
        position: 'absolute',
        top: '50%',
        left: '50%',
        marginTop: -12,
        marginLeft: -12,
    },
}));

const allRoles = {
    ADMIN: "Administrateur",
    PLAT: "Responsable Plateau",
    MANIP: "Responsable Manipulation",
}

export default function UserForm(props) {
    const classes = useStyles();
    const { user, accessToken } = useAuthContext();

    const [mode, setMode] = useState("CREATE");
    const [isDataLoading, setDataLoading] = useState(false);
    const [isSaveLoading, setSaveLoading] = useState(false);
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const loadData = (id) => {
        setDataLoading(true);
        setUserData(null);

        fetch('http://localhost/api/user/' + id, { headers: { 'Authorization': 'bearer ' + accessToken } })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json()
            })
            // Reprocess data :
            //  - flatten roles array to keep only the key field
            .then(data => ({ ...data, roles: data.roles.map(r => r.key) }))
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
    }

    const saveData = (data) => {
        if (mode === "CREATE") {
            return fetch('http://localhost/api/user/', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'bearer ' + accessToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
        } else {
            return fetch('http://localhost/api/user/' + props.match.params.id, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'bearer ' + accessToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
        }
    }

    useEffect(() => {
        if (props.match.params.hasOwnProperty("id")) {
            setMode("EDIT");
            loadData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render

    if (isDataLoading) {
        return <Loading />
    }
    if (error !== null || (mode === "EDIT" && userData === null)) {
        return (
            <ErrorPage>
                Une erreur s'est produite : <strong>{(error !== null ? error : ("No data"))}</strong>
            </ErrorPage>
        )
    }

    const meProps = props;

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {mode === "CREATE" ? "Nouvel Utilisateur" : `Modification de l'utilisateur #${props.match.params.id}`}
            </Typography>
            <hr />
            <Formik
                initialValues={{
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    roles: [],
                    ...userData
                }}
                validationSchema={() => {
                    let schema = {
                        first_name: Yup.string()
                            .required('Requis'),
                        last_name: Yup.string()
                            .required('Requis'),
                        email: Yup.string()
                            .email('Format de l\'email invalide')
                            .required('Requis'),
                        roles: Yup.array()
                            .min(1, 'Minimum 1 rôle')
                            .required('Requis')
                    }
                    if (mode === "CREATE") {
                        schema.password = Yup.string().min(6, 'Trop court (6 caractères minimum)').required('Requis');
                    }
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
                                    if (!data.errors.hasOwnProperty(field)) continue;
                                    actions.setFieldError(field, data.errors[field].join(" "));
                                }
                            } else if (data.exception) {
                                actions.setFieldError('general', data.message);
                            } else {
                                setSaveSuccess(true);
                                setTimeout(() => props.history.push('/users'), 2000);
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
                                    id="first_name"
                                    name="first_name"
                                    label="Prénom"
                                    autoComplete="fname"
                                    value={values.first_name}
                                    onChange={handleChange}
                                    error={errors.first_name && touched.first_name}
                                    helperText={touched.first_name && errors.first_name}
                                    variant="outlined"
                                    fullWidth
                                    autoFocus
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    id="last_name"
                                    name="last_name"
                                    label="Nom"
                                    autoComplete="lname"
                                    value={values.last_name}
                                    onChange={handleChange}
                                    error={errors.last_name && touched.last_name}
                                    helperText={touched.last_name && errors.last_name}
                                    variant="outlined"
                                    fullWidth
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    id="email"
                                    name="email"
                                    label="Adresse Email"
                                    autoComplete="email"
                                    value={values.email}
                                    onChange={handleChange}
                                    error={errors.email && touched.email}
                                    helperText={touched.email && errors.email}
                                    variant="outlined"
                                    fullWidth
                                />
                            </Grid>
                            {mode === "EDIT" ? "" : (
                                <Grid item xs={12}>
                                    <TextField
                                        id="password"
                                        name="password"
                                        label="Mot de passe initial"
                                        value={values.password}
                                        onChange={handleChange}
                                        error={errors.password && touched.password}
                                        helperText={touched.password && errors.password}
                                        variant="outlined"
                                        autoComplete="off"
                                        fullWidth
                                    />
                                </Grid>
                            )}
                            <Grid item xs={12}>
                                <FieldArray
                                    name="roles"
                                    render={arrayHelpers => (
                                        <FormControl
                                            component="fieldset"
                                            error={typeof errors.roles === 'string'}
                                        >
                                            <FormLabel component="legend">Rôles</FormLabel>
                                            <FormGroup>
                                                {Object.entries(allRoles).map(r => (
                                                    <FormControlLabel
                                                        key={"roles-" + r[0]}
                                                        label={r[1]}
                                                        control={<Checkbox
                                                            name="roles"
                                                            value={r[0]}
                                                            checked={values.roles.includes(r[0])}
                                                            onChange={e => {
                                                                if (e.target.checked) arrayHelpers.push(r[0]);
                                                                else {
                                                                    const idx = values.roles.indexOf(r[0]);
                                                                    arrayHelpers.remove(idx);
                                                                }
                                                            }}
                                                            color="primary"
                                                        />}
                                                    />
                                                ))}
                                            </FormGroup>
                                            {typeof errors.roles === 'string' && (<FormHelperText>{errors.roles}</FormHelperText>)}
                                        </FormControl>
                                    )}
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
                                        onClick={e => !saveSuccess && meProps.history.push('/users')}
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
                                        {mode === "CREATE" ? "Créer" : "Modifier"}
                                    </Button>
                                    {isSaveLoading && <CircularProgress size={24} className={classes.buttonProgress} />}
                                </div>
                            </Grid>
                        </Grid>
                        {errors.general && (
                            <Typography component="p" align="center" color="error">
                                Une erreur s'est produite sur le serveur : <strong>{errors.general}</strong>.
                                <br />
                                Veuillez contacter l'administrateur du site.
                            </Typography>
                        )}
                    </form>
                )}
            </Formik>
        </>
    )
}

