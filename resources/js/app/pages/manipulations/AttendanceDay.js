import React, { useState } from 'react';
import { List, ListItem, ListItemText, Checkbox, ListItemSecondaryAction, IconButton, CircularProgress } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import SaveIcon from '@material-ui/icons/Save';
import * as moment from 'moment';
import capitalize from 'lodash/capitalize';

const useStyles = makeStyles((theme) => ({
    dayLabel: {
        fontWeight:   'bold',
        borderBottom: `1px solid ${theme.palette.divider}`,
    }
}));

const momentToTime = (date) => moment(date).format(moment.HTML5_FMT.TIME);

export default function AttendanceDay({ dayLabel, daySlots, handleSave, ...otherProps }) {

    const initialChecked = daySlots.filter(s => s.booking.confirmed && s.booking.honored).map(s => s.id);
    const initialEnabled = daySlots.filter(s => s.booking.confirmed).map(s => s.id);

    const classes = useStyles();
    const [checked, setChecked] = useState(initialChecked);
    const [isSaving, setIsSaving] = useState(false);

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

    const isAfterToday = daySlots.length > 0 && moment(daySlots[0].start).isAfter(moment(), 'day');

    return (
        <List dense {...otherProps} >
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
                                checked={checked.indexOf(slot.id) !== -1}
                                inputProps={{ 'aria-labelledby': labelId }}
                            />
                        </ListItemSecondaryAction>
                    </ListItem>
                );
            })}
            {!isAfterToday && daySlots.length == 0 && (
                <ListItem>
                    <ListItemText primary={'Aucune réservation ce jour'} />
                </ListItem>
            )}
            {isAfterToday && (
                <ListItem>
                    <ListItemText primary={'Impossible de saisir la présence sur une date future'} />
                </ListItem>
            )}
        </List>
    );
}
