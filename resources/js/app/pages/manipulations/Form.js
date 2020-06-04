import React, { useState, useEffect } from 'react';

import ReactQuill from 'react-quill';
import 'react-quill/dist/quill.snow.css';

import { Formik, FieldArray } from 'formik';
import * as Yup from 'yup';

import mapValues from 'lodash/mapValues';

import Button from '@material-ui/core/Button';
import CircularProgress from '@material-ui/core/CircularProgress';
import FormControl from '@material-ui/core/FormControl';
import FormHelperText from '@material-ui/core/FormHelperText';
import Grid from '@material-ui/core/Grid';
import IconButton from '@material-ui/core/IconButton';
import InputAdornment from '@material-ui/core/InputAdornment';
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import Select from '@material-ui/core/Select';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import { green } from '@material-ui/core/colors';

import * as moment from 'moment';
import { MuiPickersUtilsProvider, KeyboardDatePicker } from '@material-ui/pickers';
import MomentUtils from '@date-io/moment';

import AddIcon from '@material-ui/icons/Add';
import CancelIcon from '@material-ui/icons/Cancel';
import CheckIcon from '@material-ui/icons/Check';
import RemoveIcon from '@material-ui/icons/Remove';
import SaveIcon from '@material-ui/icons/Save';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import AvailableHours from '../../components/AvailableHours';
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
    standaloneLabel: {
        margin: theme.spacing(2)
    },
    requirementsContainer: {
        border:       '1px solid ' + theme.palette.divider,
        borderRadius: 5,
        padding:      theme.spacing(2),
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

export default function ManipulationForm(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [mode, setMode] = useState('CREATE');
    const [dataLoading, setDataLoading] = useState(0);
    const [isSaveLoading, setSaveLoading] = useState(false);
    const [manipulationData, setManipulationData] = useState(null);
    const [userData, setUserData] = useState(null);
    const [plateauData, setPlateauData] = useState(null);
    const [error, setError] = useState(null);
    const [saveSuccess, setSaveSuccess] = useState(false);

    const loadExtraData = () => {
        setDataLoading(dataLoading + 2);
        setUserData(null);

        fetch(Constants.API_USERS_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Reprocess data:
            //  - flatten roles array to keep only the key field
            //  - filter users to keep only those possessing the "MANIP" role
            .then(data => data.map(row => ({
                ...row,
                roles: row.roles.map(r => r.key)
            })).filter(row => row.roles.includes('MANIP')))
            // Set data in state
            .then(data => {
                setUserData(data);
                setDataLoading(dataLoading - 1);
            });
        fetch(Constants.API_PLATEAUX_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Set data in state
            .then(data => {
                setPlateauData(data);
                setDataLoading(dataLoading - 1);
            });
    };
    const loadData = (id) => {
        setDataLoading(dataLoading + 1);
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
                setDataLoading(dataLoading - 1);
            })
            .catch(err => {
                setManipulationData(null);
                setError(err.message);
                setDataLoading(dataLoading - 1);
            });
    };

    const saveData = (data) => {
        // Reformat some data
        data.available_hours = mapValues(data.available_hours, (v) => {
            ['start_am', 'end_am', 'start_pm', 'end_pm'].forEach(timeField => {
                v[timeField] = moment(v[timeField], 'HH:mm').format('HH:mm');
            });
            return v;
        });
        data.start_date = moment(data.start_date).format('YYYY-MM-DD');

        if (mode === 'CREATE') {
            return fetch(Constants.API_MANIPULATIONS_ENDPOINT, {
                method:  'POST',
                headers: {
                    'Accept':        'application/json',
                    'Authorization': 'bearer ' + accessToken,
                    'Content-Type':  'application/json'
                },
                body: JSON.stringify(data)
            });
        } else {
            return fetch(Constants.API_MANIPULATIONS_ENDPOINT + props.match.params.id, {
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
        loadExtraData();
        if (Object.prototype.hasOwnProperty.call(props.match.params, 'id')) {
            setMode('EDIT');
            loadData(props.match.params.id);
        }
    }, []); // Empty array means useEffect will only be called on first render

    if (dataLoading > 0) {
        return <Loading />;
    }
    if (error !== null || (mode === 'EDIT' && manipulationData === null)) {
        return (
            <ErrorPage>
                Une erreur s&apos;est produite : <strong>{(error !== null ? error : ('No data'))}</strong>
            </ErrorPage>
        );
    }

    const meProps = props;

    let today = new Date();
    today.setHours(0);
    today.setMinutes(0);
    today.setSeconds(0);

    return (
        <>
            <Typography component="h1" variant="h4" align="center" color="textPrimary" gutterBottom>
                {mode === 'CREATE' ? 'Nouvelle Manipulation' : `Modification de la Manipulation #${props.match.params.id}`}
            </Typography>
            <hr />
            <MuiPickersUtilsProvider utils={MomentUtils}>
                <Formik
                    initialValues={{
                        name:            '',
                        description:     '',
                        plateau_id:      '',
                        duration:        0,
                        target_slots:    0,
                        start_date:      new Date(),
                        location:        '',
                        requirements:    [Constants.REQUIREMENT_PREFIX],
                        available_hours: {},
                        managers:        [],
                        ...manipulationData
                    }}
                    validationSchema={() => {
                        // let hourCondition = {
                        //     is: true,
                        //     then: Yup.string()
                        //         .matches(/^\d{2}:\d{2}$/, 'Mauvais format d\'heure (HH:mm requis)')
                        //         .transform((v, ov) => { return moment(ov, 'HH:mm').format('HH:mm'); }),
                        //     otherwise: Yup.mixed()
                        //         .notRequired()
                        //         .transform((v, ov) => { return null; })
                        // };
                        let daySchema = Yup
                            .object({
                                day:      Yup.string().oneOf(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
                                enabled:  Yup.boolean(),
                                am:       Yup.boolean(),
                                start_am: Yup.object(), //Yup.mixed().when('am', hourCondition),
                                end_am:   Yup.object(), //Yup.mixed().when('am', hourCondition),
                                pm:       Yup.boolean(),
                                start_pm: Yup.object(), //Yup.mixed().when('pm', hourCondition),
                                end_pm:   Yup.object(), //Yup.mixed().when('pm', hourCondition),
                            })
                            .test(
                                'am-hours',
                                'La fin de matinée doit être postérieure au début de matinée',
                                v => (!(v.enabled && v.am) || v.end_am > v.start_am)
                            )
                            .test(
                                'pm-hours',
                                'La fin d\'après-midi doit être postérieure au début d\'après-midi',
                                v => (!(v.enabled && v.pm) || v.end_pm > v.start_pm)
                            )
                            .test(
                                'pm-hours',
                                'Le début d\'après-midi doit être postérieur à la fin de matinée',
                                v => (!(v.enabled && v.am && v.pm) || v.start_pm > v.end_am)
                            );

                        let schema = {
                            name: Yup.string()
                                .required('Requis'),
                            description: Yup.string()
                                .required('Requis'),
                            plateau_id: Yup.number()
                                .required('Requis'),
                            duration: Yup.number()
                                .required('Requis'),
                            target_slots: Yup.number()
                                .required('Requis'),
                            start_date: Yup.date()
                                .required('Requis'),
                            location: Yup.string()
                                .required('Requis'),
                            requirements: Yup.array().of(
                                Yup.string().required('Requis').notOneOf([Constants.REQUIREMENT_PREFIX], 'Requis')
                            )
                                .min(1, '1 Pré-requis minimum'),
                            available_hours: Yup.object({
                                Mon: daySchema,
                                Tue: daySchema,
                                Wed: daySchema,
                                Thu: daySchema,
                                Fri: daySchema,
                                Sat: daySchema,
                                Sun: daySchema,
                            })
                                .test(
                                    'min-1-ampm',
                                    'Au moins 1 demi-journée doit être active',
                                    v => (Object.values(v).reduce(
                                        (total, d) => (total + ((d.enabled && d.am) ? 1 : 0) + ((d.enabled && d.pm) ? 1 : 0))
                                        , 0
                                    ) > 0)
                                )
                                .required('Requis'),
                            managers: Yup.array().of(Yup.number())
                                .min(1, '1 Responsable minimum')
                                .max(2, '2 Responsables maximum')
                                .required('Requis'),
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
                                    setTimeout(() => props.history.push('/manipulations'), Constants.FORM_REDIRECT_TIMEOUT);
                                }
                            })
                            .finally(() => {
                                setSaveLoading(false);
                            });
                    }}
                >
                    {({ values, errors, touched, setFieldValue, setFieldTouched, handleChange, handleSubmit }) => (
                        <form className={classes.form} onSubmit={handleSubmit}>
                            <Grid container spacing={2}>
                                <Grid item xs={12}>
                                    <TextField
                                        id="name"
                                        name="name"
                                        label="Nom"
                                        autoComplete="manipulation_name"
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
                                    <ReactQuill
                                        value={values.description}
                                        onChange={v => {
                                            setFieldValue('description', v);
                                            setFieldTouched('description', true);
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
                                <Grid item xs={12} sm={6}>
                                    <FormControl variant="outlined" className={classes.formControl}>
                                        <InputLabel
                                            id="plateau-id-label"
                                            ref={inputLabel}
                                        >
                                            Plateau
                                        </InputLabel>
                                        <Select
                                            labelId="plateau-id-label"
                                            labelWidth={labelWidth}
                                            id="plateau_id"
                                            name="plateau_id"
                                            value={values.plateau_id}
                                            onChange={handleChange}
                                            error={errors.plateau_id && touched.plateau_id}
                                        >
                                            {plateauData && plateauData.map(p => (
                                                <MenuItem key={p.id} value={p.id}>{p.name}</MenuItem>
                                            ))}
                                        </Select>
                                        {touched.plateau_id && errors.plateau_id && (
                                            <FormHelperText error variant="outlined">
                                                {touched.plateau_id && errors.plateau_id}
                                            </FormHelperText>
                                        )}
                                    </FormControl>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <TextField
                                        id="duration"
                                        name="duration"
                                        label="Durée (minutes)"
                                        autoComplete="manipulation_duration"
                                        type="number"
                                        inputProps={{ min: 0 }}
                                        value={values.duration}
                                        onChange={e => { handleChange(e); }}
                                        error={errors.duration && touched.duration}
                                        helperText={touched.duration && errors.duration}
                                        variant="outlined"
                                        fullWidth
                                    />
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <TextField
                                        id="target_slots"
                                        name="target_slots"
                                        label="Cible de participants"
                                        autoComplete="manipulation_target_slots"
                                        type="number"
                                        inputProps={{ min: 0 }}
                                        value={values.target_slots}
                                        onChange={handleChange}
                                        error={errors.target_slots && touched.target_slots}
                                        helperText={touched.target_slots && errors.target_slots}
                                        variant="outlined"
                                        fullWidth
                                    />
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <FormControl variant="outlined" className={classes.formControl}>
                                        <KeyboardDatePicker
                                            disableToolbar
                                            autoOk
                                            label="Date de début"
                                            value={moment(values.start_date)}
                                            format="DD/MM/YYYY"
                                            error={errors.start_date && touched.start_date}
                                            helperText={touched.start_date && errors.start_date}
                                            onChange={v => {
                                                if (moment(v).isValid()) {
                                                    setFieldValue('start_date', moment(v).toDate());
                                                }
                                            }}
                                            inputVariant="outlined"
                                            InputAdornmentProps={{ position: 'start' }}
                                            KeyboardButtonProps={{
                                                'aria-label': 'change date',
                                            }}
                                        />
                                    </FormControl>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <FormControl variant="outlined" className={classes.formControl}>
                                        <InputLabel
                                            id="managers-label"
                                            ref={inputLabel}
                                        >
                                            Responsables
                                        </InputLabel>
                                        <Select
                                            labelId="managers-label"
                                            labelWidth={labelWidth}
                                            id="managers"
                                            name="managers"
                                            value={values.managers}
                                            onChange={handleChange}
                                            error={!!errors.managers && !!touched.managers}
                                            multiple
                                        >
                                            {userData && userData.map(u => (
                                                <MenuItem key={u.id} value={u.id}>{u.name}</MenuItem>
                                            ))}
                                        </Select>
                                        {touched.managers && errors.managers && (
                                            <FormHelperText error variant="outlined">
                                                {touched.managers && errors.managers}
                                            </FormHelperText>
                                        )}
                                    </FormControl>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <TextField
                                        id="location"
                                        name="location"
                                        label="Lieu"
                                        autoComplete="manipulation_location"
                                        value={values.location}
                                        onChange={handleChange}
                                        error={errors.location && touched.location}
                                        helperText={touched.location && errors.location}
                                        variant="outlined"
                                        fullWidth
                                    />
                                </Grid>
                                <Grid item xs={12}>
                                    <Grid container spacing={2} className={classes.requirementsContainer}>
                                        <FieldArray
                                            name="requirements"
                                            render={arrayHelpers => (
                                                <>
                                                    {values.requirements.map((req, idx) => (
                                                        <Grid item xs={12} key={`requirement-${idx}`}>
                                                            <TextField
                                                                name="requirements[]"
                                                                label={'Pré-requis #' + (idx + 1)}
                                                                value={req}
                                                                onChange={(e) => { arrayHelpers.replace(idx, e.target.value); }}
                                                                error={
                                                                    errors.requirements && errors.requirements[idx]
                                                                    && touched.requirements && touched.requirements[idx]
                                                                }
                                                                helperText={
                                                                    touched.requirements && touched.requirements[idx]
                                                                    && errors.requirements && errors.requirements[idx]
                                                                }
                                                                variant="outlined"
                                                                fullWidth
                                                                InputProps={{
                                                                    endAdornment: (
                                                                        <InputAdornment position="end">
                                                                            <IconButton
                                                                                aria-label="add new requirement"
                                                                                onClick={() => {
                                                                                    if (idx == values.requirements.length - 1) {
                                                                                        arrayHelpers.push(Constants.REQUIREMENT_PREFIX);
                                                                                    } else {
                                                                                        arrayHelpers.remove(idx);
                                                                                    }
                                                                                }}
                                                                                edge="end"
                                                                            >
                                                                                {idx == values.requirements.length - 1 ? <AddIcon /> : <RemoveIcon />}
                                                                            </IconButton>
                                                                        </InputAdornment>
                                                                    )
                                                                }}
                                                            />
                                                        </Grid>
                                                    ))}
                                                </>
                                            )}
                                        />
                                        {touched.requirements && errors.requirements && typeof errors.requirements === 'string' && (
                                            <FormHelperText error variant="outlined">
                                                {touched.requirements && errors.requirements}
                                            </FormHelperText>
                                        )}
                                    </Grid>
                                </Grid>
                                <Grid item xs={12}>
                                    <AvailableHours
                                        dayLabels={Constants.DAYS_LABELS}
                                        value={values.available_hours}
                                        duration={values.duration}
                                        error={errors.available_hours && touched.available_hours}
                                        helperText={touched.available_hours && errors.available_hours}
                                        onChange={v => {

                                            setFieldValue('available_hours', v);
                                            setFieldTouched('available_hours', true, false);
                                        }}
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
                                            onClick={() => !saveSuccess && meProps.history.push('/manipulations')}
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
            </MuiPickersUtilsProvider>
        </>
    );
}
