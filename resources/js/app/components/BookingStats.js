import React from 'react';
import { Table, TableBody, TableRow, TableCell } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';

const useStyles = makeStyles(theme => ({
    statsCell: {
        borderWidth: 1,
        borderStyle: 'solid',
        borderColor: theme.palette.grey[500]
    },
    statsNumber: {
        fontSize:   '1.2rem',
        textAlign:  'center',
        fontWeight: 'bold'
    },
    statsLegend: {
        textAlign: 'center',
    },
}));

export default function BookingStats({ data }) {
    const classes = useStyles();

    return (
        <Table>
            <TableBody>
                {data.slot_count !== undefined && (
                    <TableRow>
                        <TableCell className={classes.statsCell} colSpan={2}>
                            <div className={classes.statsNumber}>
                                {data.slot_count}
                            </div>
                            <div className={classes.statsLegend}>
                                {'Créneaux'}
                            </div>
                        </TableCell>
                    </TableRow>
                )}
                <TableRow>
                    <TableCell className={classes.statsCell} colSpan={2}>
                        <div className={classes.statsNumber}>
                            {data.booking_made}
                        </div>
                        <div className={classes.statsLegend}>
                            {'Réservations'}
                        </div>
                    </TableCell>
                </TableRow>
                <TableRow>
                    <TableCell className={classes.statsCell}>
                        <div className={classes.statsNumber}>
                            {data.booking_confirmed}
                        </div>
                        <div className={classes.statsLegend}>
                            {'Réservations Confirmées'}
                        </div>
                    </TableCell>
                    <TableCell className={classes.statsCell}>
                        <div className={classes.statsNumber}>
                            {data.booking_made - data.booking_confirmed}
                        </div>
                        <div className={classes.statsLegend}>
                            {'Réservations Non Confirmées'}
                        </div>
                    </TableCell>
                </TableRow>
                <TableRow>
                    <TableCell className={classes.statsCell}>
                        <div className={classes.statsNumber}>
                            {data.booking_confirmed_honored}
                        </div>
                        <div className={classes.statsLegend}>
                            {'Réservations Confirmées & Honorées'}
                        </div>
                    </TableCell>
                    <TableCell className={classes.statsCell}>
                        <div className={classes.statsNumber}>
                            {data.booking_unconfirmed_honored}
                        </div>
                        <div className={classes.statsLegend}>
                            {'Réservations Non Confirmées & Honorées'}
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    );
}
