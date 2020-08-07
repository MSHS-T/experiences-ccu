import React, { useState, useEffect } from 'react';

import ReactQuill from 'react-quill';
import 'react-quill/dist/quill.snow.css';

import { Formik } from 'formik';
import * as Yup from 'yup';

import Button from '@material-ui/core/Button';
import CircularProgress from '@material-ui/core/CircularProgress';
import FormControl from '@material-ui/core/FormControl';
import FormHelperText from '@material-ui/core/FormHelperText';
import Grid from '@material-ui/core/Grid';
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import Select from '@material-ui/core/Select';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import { green } from '@material-ui/core/colors';

import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import SaveIcon from '@material-ui/icons/Save';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import ErrorPage from '../Error';
import Loading from '../Loading';

const useStyles = makeStyles(theme => ({
    form: {
        width:     '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    formControl: {
        width: '100%',
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

export default function PlateauForm(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [mode, setMode] = useState('CREATE');
    const [dataLoading, setDataLoading] = useState(0);
    const [isSaveLoading, setSaveLoading] = useState(false);
    const [plateauData, setPlateauData] = useState(null);
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const loadUsers = () => {
        setDataLoading(dataLoading + 1);
        setUserData(null);

        fetch(Constants.API_USERS_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Reprocess data:
            //  - flatten roles array to keep only the key field
            //  - filter users to keep only those possessing the "PLAT" role
            .then(data => data.map(row => ({
                ...row,
                roles: row.roles.map(r => r.key)
            })).filter(row => row.roles.includes('PLAT')))
            // Set data in state
            .then(data => {
                setUserData(data);
                setDataLoading(dataLoading - 1);
            });
    };
    const loadData = (id) => {
        setDataLoading(dataLoading + 1);
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
                setDataLoading(dataLoading - 1);
            })
            .catch(err => {
                setPlateauData(null);
                setError(err.message);
                setDataLoading(dataLoading - 1);
            });
    };

    const saveData = (data) => {
        if (mode === 'CREATE') {
            return fetch(Constants.API_PLATEAUX_ENDPOINT, {
                method:  'POST',
                headers: {
                    'Accept':        'application/json',
                    'Authorization': 'bearer ' + accessToken,
                    'Content-Type':  'application/json'
                },
                body: JSON.stringify(data)
            });
        } else {
            return fetch(Constants.API_PLATEAUX_ENDPOINT + '/' + props.match.params.id, {
                method:  'PUT',
                headers: {
                    'Accept':        'application/json',
                    'Authorization': 'bearer ' + accessToken,
                    'Content-Type':  'application/json'
                },
                body: JSON.stringify(data)
            });
        }
    };

    const inputLabel = React.useRef(null);
    const [labelWidth, setLabelWidth] = React.useState(0);

    useEffect(() => {
        setLabelWidth(inputLabel.current.offsetWidth);
        loadUsers();
        if (Object.prototype.hasOwnProperty.call(props.match.params, 'id')) {
            setMode('EDIT');
            loadData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render

    if (dataLoading > 0) {
        return <Loading />;
    }
    if (error !== null || (mode === 'EDIT' && plateauData === null)) {
        return (
            <ErrorPage>
                Une erreur s&apos;est produite : <strong>{(error !== null ? error : ('No data'))}</strong>
            </ErrorPage>
        );
    }

    const meProps = props;

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {mode === 'CREATE' ? 'Nouveau Plateau' : `Modification du Plateau #${props.match.params.id}`}
            </Typography>
            <hr />
            <Formik
                initialValues={{
                    name:        '',
                    description: '',
                    manager_id:  '',
                    ...plateauData
                }}
                validationSchema={() => {
                    let schema = {
                        name: Yup.string()
                            .required('Requis'),
                        manager_id: Yup.number()
                            .required('Requis'),
                        description: Yup.string()
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
                                setTimeout(() => props.history.push('/plateaux'), Constants.FORM_REDIRECT_TIMEOUT);
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
                                    autoComplete="plateau_name"
                                    value={values.name}
                                    onChange={handleChange}
                                    error={errors.name && touched.name}
                                    helperText={touched.name && errors.name}
                                    variant="outlined"
                                    fullWidth
                                    autoFocus
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <FormControl variant="outlined" className={classes.formControl}>
                                    <InputLabel
                                        id="manager-id-label"
                                        ref={inputLabel}
                                    >
                                        Responsable
                                    </InputLabel>
                                    <Select
                                        labelId="manager-id-label"
                                        labelWidth={labelWidth}
                                        id="manager_id"
                                        name="manager_id"
                                        value={values.manager_id}
                                        onChange={handleChange}
                                        error={errors.manager_id && touched.manager_id}
                                    >
                                        {/* <MenuItem value=""></MenuItem> */}
                                        {userData && userData.map(u => (
                                            <MenuItem key={u.id} value={u.id}>{u.name}</MenuItem>
                                        ))}
                                    </Select>
                                    {touched.manager_id && errors.manager_id && (
                                        <FormHelperText error variant="outlined">
                                            {touched.manager_id && errors.manager_id}
                                        </FormHelperText>
                                    )}
                                </FormControl>

                            </Grid>
                            <Grid item xs={12}>
                                <ReactQuill
                                    value={values.description}
                                    onChange={v => {
                                        setFieldValue('description', v);
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
                                        {mode === 'CREATE' ? 'Créer' : 'Modifier'}
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
