import React, { Component, useState, useEffect } from "react";
import { Formik, Field, FieldArray } from "formik";
import * as Yup from 'yup';

import Button from '@material-ui/core/Button';
import Checkbox from '@material-ui/core/Checkbox';
import FormControl from '@material-ui/core/FormControl';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormGroup from '@material-ui/core/FormGroup';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormLabel from '@material-ui/core/FormLabel';
import Grid from '@material-ui/core/Grid';
import TextField from '@material-ui/core/TextField';
import Typography from "@material-ui/core/Typography";
import { makeStyles } from '@material-ui/core/styles';

import CancelIcon from '@material-ui/icons/Cancel';
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
    button: {
        margin: theme.spacing(2),
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
    const [isLoading, setLoading] = useState(false);
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);
    const [saveError, setSaveError] = useState(null);

    const loadData = (id) => {
        setLoading(true);
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
                setLoading(false);
            })
            .catch(err => {
                setUserData(null);
                setError(err.message);
                setLoading(false);
            });
    }

    const saveData = (data) => {

    }
    var UserSchema;

    useEffect(() => {
        if (props.match.params.hasOwnProperty("id")) {
            setMode("EDIT");
            loadData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render

    if (isLoading) {
        return <Loading />
    }
    if (error !== null || (mode === "EDIT" && userData === null)) {
        console.debug(error);
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
                    console.log('formik submit', values);
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
                                <Button
                                    variant="contained"
                                    color="secondary"
                                    className={classes.button}
                                    startIcon={<CancelIcon />}
                                    onClick={e => meProps.history.push('/users')}
                                >
                                    Annuler
                                </Button>
                                <Button
                                    type="submit"
                                    variant="contained"
                                    color="primary"
                                    size="large"
                                    className={classes.button}
                                    startIcon={<SaveIcon />}
                                >
                                    {mode === "CREATE" ? "Créer" : "Modifier"}
                                </Button>
                            </Grid>
                        </Grid>
                    </form>
                )}
            </Formik>
        </>
    )
}

