import React, { useState, useEffect } from "react";

import ReactQuill from 'react-quill';
import 'react-quill/dist/quill.snow.css';

import { Formik } from "formik";
import * as Yup from 'yup';

import Button from '@material-ui/core/Button';
import CircularProgress from "@material-ui/core/CircularProgress";
import FormHelperText from "@material-ui/core/FormHelperText";
import Grid from '@material-ui/core/Grid';
import TextField from '@material-ui/core/TextField';
import Typography from "@material-ui/core/Typography";
import { makeStyles } from '@material-ui/core/styles';
import { green } from '@material-ui/core/colors';

import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import SaveIcon from '@material-ui/icons/Save';

import { useAuthContext } from "../../context/Auth";
import * as Constants from "../../data/Constants";
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

export default function EquipmentForm(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [mode, setMode] = useState("CREATE");
    const [isDataLoading, setDataLoading] = useState(false);
    const [isSaveLoading, setSaveLoading] = useState(false);
    const [equipmentData, setEquipmentData] = useState(null);
    const [error, setError] = useState(null);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const loadData = (id) => {
        setDataLoading(true);
        setEquipmentData(null);

        fetch(Constants.API_EQUIPMENTS_ENDPOINT + id, { headers: { 'Authorization': 'bearer ' + accessToken } })
            // Parse JSON response
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                return response.json()
            })
            // Set data in state
            .then(data => {
                setEquipmentData(data);
                setError(null);
                setDataLoading(false);
            })
            .catch(err => {
                setEquipmentData(null);
                setError(err.message);
                setDataLoading(false);
            });
    }

    const saveData = (data) => {
        if (mode === "CREATE") {
            return fetch(Constants.API_EQUIPMENTS_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'bearer ' + accessToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
        } else {
            return fetch(Constants.API_EQUIPMENTS_ENDPOINT + props.match.params.id, {
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
    if (error !== null || (mode === "EDIT" && equipmentData === null)) {
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
                {mode === "CREATE" ? "Nouveau Matériel" : `Modification du Matériel #${props.match.params.id}`}
            </Typography>
            <hr />
            <Formik
                initialValues={{
                    name: '',
                    type: '',
                    quantity: 0,
                    description: "",
                    ...equipmentData
                }}
                validationSchema={() => {
                    let schema = {
                        name: Yup.string()
                            .required('Requis'),
                        type: Yup.string()
                            .required('Requis'),
                        quantity: Yup.number()
                            .min(0, 'Minimum autorisé : 0')
                            .required('Requis'),
                        description: Yup.string()
                            .required('Requis')
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
                                setTimeout(() => props.history.push('/equipments'), Constants.FORM_REDIRECT_TIMEOUT);
                            }
                        })
                        .finally(() => {
                            setSaveLoading(false);
                        });
                }}
            >
                {({ values, errors, touched, setFieldValue, handleChange, handleSubmit }) => (
                    <form className={classes.form} onSubmit={handleSubmit}>
                        <Grid container spacing={2}>
                            <Grid item xs={12}>
                                <TextField
                                    id="name"
                                    name="name"
                                    label="Nom"
                                    autoComplete="equipment_name"
                                    value={values.name}
                                    onChange={handleChange}
                                    error={errors.name && touched.name}
                                    helperText={touched.name && errors.name}
                                    variant="outlined"
                                    fullWidth
                                    autoFocus
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    id="type"
                                    name="type"
                                    label="Type"
                                    autoComplete="equipment_type"
                                    value={values.type}
                                    onChange={handleChange}
                                    error={errors.type && touched.type}
                                    helperText={touched.type && errors.type}
                                    variant="outlined"
                                    fullWidth
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    id="quantity"
                                    name="quantity"
                                    label="Quantité"
                                    autoComplete="equipment_quantity"
                                    type="number"
                                    inputProps={{ min: 0 }}
                                    value={values.quantity}
                                    onChange={handleChange}
                                    error={errors.quantity && touched.quantity}
                                    helperText={touched.quantity && errors.quantity}
                                    variant="outlined"
                                    fullWidth
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <ReactQuill
                                    value={values.description}
                                    onChange={v => {
                                        setFieldValue("description", v)
                                    }}
                                    style={{
                                        // borderRadius: '0.5em',
                                        border: errors.description && touched.description ? '1px solid #f44336' : 'none'
                                    }}
                                    modules={{
                                        toolbar: [
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{ 'list': 'ordered' }, { 'list': 'bullet' }, 'clean']
                                        ]
                                    }}
                                    formats={['bold', 'italic', 'underline', 'strike', 'list', 'bullet']}
                                />
                                {errors.description && touched.description && (
                                    <FormHelperText error variant="outlined">
                                        {errors.description}
                                    </FormHelperText>
                                )}
                            </Grid>
                            <Grid item xs={12} className={classes.buttonRow}>
                                <div className={classes.buttonWrapper}>
                                    <Button
                                        variant="contained"
                                        color="secondary"
                                        disabled={isSaveLoading}
                                        className={classes.button}
                                        startIcon={<CancelIcon />}
                                        onClick={e => !saveSuccess && meProps.history.push('/equipments')}
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

