import React, { useState, useEffect } from 'react';
import { List, ListItem, ListItemText, Checkbox, ListItemSecondaryAction, IconButton, CircularProgress } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import SaveIcon from '@material-ui/icons/Save';
import * as moment from 'moment';
import { grey, green, orange } from '@material-ui/core/colors';

const useStyles = makeStyles((theme) => ({
    dayLabel: {
        fontWeight:   'bold',
        borderBottom: `1px solid ${theme.palette.divider}`,
    },
    alreadyDone: {
        backgroundColor: green[600]+'88',
    },
    notDone: {
        backgroundColor: orange[600]+'88',
    },
    disabled: {
        backgroundColor: grey[600]+'88',
    }
}));

const momentToTime = (date) => moment(date).format(moment.HTML5_FMT.TIME);

export default function AttendanceDay({ dayLabel, daySlots, handleSave, className, ...otherProps }) {
    const initialEnabled = daySlots.filter(s => s.booking.confirmed).map(s => s.id);

    const classes = useStyles();
    const [checked, setChecked] = useState([]);
    const [isSaving, setIsSaving] = useState(false);

    useEffect(() => {
        const initialChecked = daySlots.filter(s => s.booking.honored).map(s => s.id);
        setChecked(initialChecked);
    }, [daySlots]);

    const handleToggle = (value) => () => {
        const currentIndex = checked.indexOf(value);
        const newChecked = [...checked];

        if (currentIndex === -1) {
            newChecked.push(value);
        } else {
            newChecked.splice(currentIndex, 1);
        }

        setChecked(newChecked);
    };


    const handleSaveButtonClick = () => {
        if(isSaving){
            return false;
        }
        setIsSaving(true);

        const data = Object.fromEntries(daySlots.map(s => [s.id, checked.includes(s.id)]));
        handleSave(data).finally(() => setIsSaving(false));
    };

    const isAlreadyDone = daySlots.filter(s => s.booking.honored === null).length == 0;
    const isAfterToday = daySlots.length > 0 && moment(daySlots[0].start).isAfter(moment(), 'day');
    const isEmpty = daySlots.length == 0;

    const allClasses = [
        className,
        isAlreadyDone ? classes.alreadyDone : classes.notDone,
        (isAfterToday || isEmpty) ? classes.disabled : ''
    ].join(' ');


    return (
        <List dense className={allClasses} {...otherProps} >
            <ListItem>
                <ListItemText primary={dayLabel} primaryTypographyProps={{ className: classes.dayLabel }} />
                {daySlots.length > 0 && !isAfterToday && (
                    <ListItemSecondaryAction>
                        <IconButton edge="end" aria-label="delete" onClick={handleSaveButtonClick}>
                            {!!isSaving && (<CircularProgress size={20} />)}
                            {!isSaving && (<SaveIcon />)}
                        </IconButton>
                    </ListItemSecondaryAction>
                )}
            </ListItem>
            {!isAfterToday && daySlots.map((slot) => {
                const labelId = `checkbox-list-secondary-label-${slot.id}`;
                return (
                    <ListItem key={slot.id} button onClick={handleToggle(slot.id)}>
                        <ListItemText
                            id={labelId}
                            primary={`${momentToTime(slot.start)}-${momentToTime(slot.end)}`}
                            secondary={`${slot.booking.first_name} ${slot.booking.last_name.toUpperCase()}`}
                            secondaryTypographyProps={{ color: initialEnabled.includes(slot.id) ? 'initial' : 'textSecondary' }}
                        />
                        <ListItemSecondaryAction>
                            <Checkbox
                                edge="end"
                                onChange={handleToggle(slot.id)}
                                checked={checked.includes(slot.id)}
                                inputProps={{ 'aria-labelledby': labelId }}
                            />
                        </ListItemSecondaryAction>
                    </ListItem>
                );
            })}
            {!isAfterToday && isEmpty && (
                <ListItem>
                    <ListItemText primary={'Aucune réservation ce jour'} />
                </ListItem>
            )}
            {isAfterToday && (
                <ListItem>
                    <ListItemText primary={'Impossible de saisir la présence sur une date future'} />
                </ListItem>
            )}
            <ListItem>
                <ListItemText secondary={
                    (isAfterToday || isEmpty) ? 'Aucune action requise' : (
                        isAlreadyDone ? 'Saisie déjà effectuée, modification possible' : 'Saisie à faire'
                    )
                } secondaryTypographyProps={{ variant: 'caption', style: { fontStyle: 'italic' }}} />
            </ListItem>
        </List>
    );
}
