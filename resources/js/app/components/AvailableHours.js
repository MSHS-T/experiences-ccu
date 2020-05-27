import React, { useState, useEffect } from 'react';
import Paper from '@material-ui/core/Paper';
import Switch from '@material-ui/core/Switch';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import { makeStyles } from '@material-ui/core/styles';

import mapValues from 'lodash/mapValues';
import * as moment from 'moment';
import { KeyboardTimePicker } from '@material-ui/pickers';
import { Alert, AlertTitle } from '@material-ui/lab';

const useStyles = makeStyles(theme => ({
    cell: {
        width: '12%'
    },
    tableError: {
        border: '1px solid #f44336'
    },
    cellError: {
        color: '#f44336'
    },
    disabledArea: {
        background: 'repeating-linear-gradient( -45deg, #999, #999 10px, #AAA 10px, #AAA 20px )',
        minHeight:  100,
    },
    disabledDay: {
        background: 'repeating-linear-gradient( -45deg, #999, #999 10px, #AAA 10px, #AAA 20px )',
    },
    enabledArea: {
        minHeight: 100
    },
    timePicker: {
        width:   150,
        display: 'inline-block',
        margin:  theme.spacing(1)
    },
    ampmCell: {
        width: 110
    }
}));

export default function AvailableHours({ dayLabels, duration, onChange, value, error, helperText }) {
    const classes = useStyles();
    const [durationWarning, setDurationWarning] = useState([]);
    const days = {
        Mon: 'Lundi',
        Tue: 'Mardi',
        Wed: 'Mercredi',
        Thu: 'Jeudi',
        Fri: 'Vendredi',
        Sat: 'Samedi',
        Sun: 'Dimanche',
        ...dayLabels
    };
    let initialStateData = mapValues(days, (label, d) => ({
        day:      d,
        enabled:  (d != 'Sat' && d != 'Sun'),
        am:       true,
        start_am: moment('09:00', 'HH:mm'),
        end_am:   moment('12:00', 'HH:mm'),
        pm:       true,
        start_pm: moment('14:00', 'HH:mm'),
        end_pm:   moment('17:00', 'HH:mm'),
    }));
    if (Object.keys(value).length > 0) {
        // console.log("Given value in props : ", value);
        initialStateData = {
            ...initialStateData,
            ...value
        };
        initialStateData = mapValues(initialStateData, (v) => {
            ['start_am', 'end_am', 'start_pm', 'end_pm'].forEach(timeField => {
                v[timeField] = moment(v[timeField], 'HH:mm');
            });
            return v;
        });
        // console.log("Transformed initial state data to : ", initialStateData);
    }
    const [data, setData] = useState(initialStateData);

    const toggleSwitch = (e) => {
        const d = e.target.value;
        const field = e.target.getAttribute('data-target');
        setData({
            ...data,
            [d]: { ...data[d], [field]: !data[d][field] }
        });
    };

    const handleTimeChange = (name, dateObj) => {
        if (dateObj.isValid()) {
            let day, field;
            [day, field] = name.split('-');

            setData({
                ...data,
                [day]: { ...data[day], [field]: dateObj }
            });
        }
    };


    const validateData = () => {
        // Validate data
        let warning = [];
        if (duration > 0) {
            Object.keys(days).forEach(d => {
                if (!Object.prototype.hasOwnProperty.call(data, d)) { return; }
                if (!data[d].enabled) { return; }

                ['am', 'pm'].map(ampm => {
                    if (data[d][ampm]) {
                        const openDuration = moment(data[d][`end_${ampm}`], 'HH:mm')
                            .diff(moment(data[d][`start_${ampm}`], 'HH:mm'), 'minutes');
                        if (openDuration % duration !== 0) {
                            warning.push([
                                days[d],
                                ' ',
                                ampm == 'am' ? 'matin' : 'après-midi',
                                ' (',
                                openDuration % duration,
                                ' minutes perdues)'
                            ].join(''));
                        }
                    }
                });
            });
            setDurationWarning(warning);
        }

        // If valid, we fire onChange given in props
        onChange(data);
    };

    // When data changes, validate it
    useEffect(validateData, [data]);

    return (
        <>
            <TableContainer component={Paper} className={error ? classes.tableError : ''}>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell className={classes.cell}>
                                <strong>Horaires</strong>
                            </TableCell>
                            {Object.keys(days).map(d => (
                                <TableCell align="center" key={`th-${d}`} className={classes.cell}>
                                    {days[d]}
                                    <Switch
                                        checked={data[d].enabled}
                                        onChange={toggleSwitch}
                                        inputProps={{ 'data-target': 'enabled' }}
                                        value={d}
                                        color="primary"
                                    />
                                </TableCell>
                            ))}
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow>
                            <TableCell component="th" scope="row" className={classes.ampmCell}>
                                Matin
                            </TableCell>
                            {Object.keys(days).map(d => {
                                const rootCls = data[d].enabled ? '' : classes.disabledDay;
                                const cls = data[d].am ? classes.enabledArea : classes.disabledArea;
                                return (
                                    <TableCell align="center" key={`am-${d}`} className={rootCls}>
                                        <Switch
                                            checked={data[d].am}
                                            disabled={!data[d].enabled}
                                            inputProps={{ 'data-target': 'am' }}
                                            onChange={toggleSwitch}
                                            value={d}
                                            color="primary"
                                        />
                                        <div className={cls}>
                                            <KeyboardTimePicker
                                                className={classes.timePicker}
                                                disabled={!data[d].am}
                                                ampm={false}
                                                mask="__:__"
                                                name={`${d}-start_am`}
                                                value={data[d].start_am}
                                                minutesStep={5}
                                                onChange={(date, value) => { handleTimeChange(`${d}-start_am`, date, value); }}
                                            />
                                            <KeyboardTimePicker
                                                className={classes.timePicker}
                                                disabled={!data[d].am}
                                                ampm={false}
                                                mask="__:__"
                                                name={`${d}-end_am`}
                                                value={data[d].end_am}
                                                minutesStep={5}
                                                onChange={(date, value) => { handleTimeChange(`${d}-end_am`, date, value); }}
                                            />
                                        </div>
                                    </TableCell>
                                );
                            })}
                        </TableRow>

                        <TableRow>
                            <TableCell component="th" scope="row" className={classes.ampmCell}>
                                Après-midi
                            </TableCell>
                            {Object.keys(days).map(d => {
                                const rootCls = data[d].enabled ? '' : classes.disabledDay;
                                const cls = data[d].pm ? classes.enabledArea : classes.disabledArea;
                                return (
                                    <TableCell align="center" key={`pm-${d}`} className={rootCls}>
                                        <Switch
                                            checked={data[d].pm}
                                            disabled={!data[d].enabled}
                                            inputProps={{ 'data-target': 'pm' }}
                                            onChange={toggleSwitch}
                                            value={d}
                                            color="primary"
                                        />
                                        <div className={cls}>
                                            <KeyboardTimePicker
                                                className={classes.timePicker}
                                                disabled={!data[d].pm}
                                                ampm={false}
                                                mask="__:__"
                                                name={`${d}-start_pm`}
                                                value={data[d].start_pm}
                                                minutesStep={5}
                                                onChange={(date, value) => { handleTimeChange(`${d}-start_pm`, date, value); }}
                                            />
                                            <KeyboardTimePicker
                                                className={classes.timePicker}
                                                disabled={!data[d].pm}
                                                ampm={false}
                                                mask="__:__"
                                                name={`${d}-end_pm`}
                                                value={data[d].end_pm}
                                                minutesStep={5}
                                                onChange={(date, value) => { handleTimeChange(`${d}-end_pm`, date, value); }}
                                            />
                                        </div>
                                    </TableCell>
                                );
                            })}
                        </TableRow>
                        {error && (
                            <TableRow>
                                {typeof helperText === 'string' ? (
                                    <TableCell align="center" colSpan={8} className={classes.cellError}>
                                        {helperText}
                                    </TableCell>
                                ) : (
                                    <>
                                        <TableCell></TableCell>
                                        {Object.keys(days).map(d => (
                                            <TableCell
                                                align="center"
                                                key={`error-${d}`}
                                                className={classes.cellError}
                                            >
                                                {
                                                    /* helperText[d] == null will match with undefined too */
                                                    (typeof helperText[d] === 'string' || helperText[d] == null)
                                                        ? helperText[d]
                                                        : Object.values(helperText[d]).join('<br/>')
                                                }
                                            </TableCell>
                                        ))}
                                    </>
                                )}
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </TableContainer>
            {
                durationWarning.length > 0 && (
                    <Alert severity="warning" variant="filled">
                        <AlertTitle>
                            Les horaires des demi-journées suivantes ne permettent pas un nombre optimal de créneaux :
                        </AlertTitle>
                        <ul>
                            {durationWarning.map((dw, i) => (
                                <li key={`warning-${i}`}>{dw}</li>
                            ))}
                        </ul>
                    </Alert>
                )
            }
        </>
    );
}