import React, { useState, useEffect } from 'react';
import MaterialTable from 'material-table';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogTitle from '@material-ui/core/DialogTitle';
import Typography from '@material-ui/core/Typography';

import { makeStyles } from '@material-ui/core/styles';

import ArchiveIcon from '@material-ui/icons/Archive';
import AssignmentTurnedInIcon from '@material-ui/icons/AssignmentTurnedIn';
import CloseIcon from '@material-ui/icons/Close';
import PlaylistAddCheckIcon from '@material-ui/icons/PlaylistAddCheck';
import TodayIcon from '@material-ui/icons/Today';
import UnarchiveIcon from '@material-ui/icons/Unarchive';
import ViewIcon from '@material-ui/icons/Visibility';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';
import { List, ListItem, IconButton } from '@material-ui/core';
import moment from 'moment';
import { capitalize } from 'lodash';

const useStyles = makeStyles(theme => ({
    statusDanger: {
        color:      theme.palette.error.main,
        fontWeight: 'bold'
    },
    statusWarning: {
        color:      theme.palette.warning.main,
        fontWeight: 'bold'
    },
    statusOk: {
        color: theme.palette.success.main
    }
}));

const emptyAction = {
    icon:     '',
    tooltip:  '',
    disabled: true
};

export default function ManipulationList(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isLoading, setLoading] = useState(true);
    const [tableData, setTableData] = useState([]);
    const [deleteEntry, setDeleteEntry] = useState(null);
    const [deleteError, setDeleteError] = useState(null);
    const [callSheet, setCallSheet] = useState(null);

    const loadData = () => {
        setLoading(true);
        setTableData([]);

        fetch(Constants.API_MANIPULATIONS_ENDPOINT + 'all', { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Reprocess data :
            //  - get manager names from nested objects
            .then(data => data.map(row => ({
                ...row,
                manager_names: row.managers.map(u => u.name).sort().join(', ')
            })))
            // Set data in state
            .then(data => {
                setTableData(data);
                setLoading(false);
            });
    };

    const handleDelete = (entry) => {
        setDeleteError(null);
        fetch(Constants.API_MANIPULATIONS_ENDPOINT + entry.id, {
            method:  'DELETE',
            headers: {
                'Authorization': 'bearer ' + accessToken,
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`${response.status} (${response.statusText})`);
                }
                setDeleteEntry(null);
                loadData();
            })
            .catch(err => {
                setDeleteError(err.message);
                setDeleteEntry(null);
            });
    };

    const Stats = (rowData) => {
        const target = rowData.target_slots;
        const slots = rowData.slots.length || 0;
        const signedup = rowData.slots.filter(s => (!!s.booking && !!s.booking.confirmed)).length || 0;
        const signedupNotConfirmed = rowData.slots.filter(s => (!!s.booking && !s.booking.confirmed)).length || 0;
        const slotsPercent = slots / target * 100;
        const signedupPercent = signedup / target * 100;

        const signedupText = signedup + (signedupNotConfirmed > 0 ? ` (+ ${signedupNotConfirmed})` : '');

        // eslint-disable-next-line no-undef
        const colorPercent = (percent) => (percent < 100 ? classes.statusDanger : (percent >= APP_SETTINGS.manipulation_overbooking ? classes.statusOk : classes.statusWarning));

        return (
            <>
                <Typography variant="inherit" display="inline" className={colorPercent(slotsPercent)}>{slots} Créneaux</Typography>
                <span> | </span>
                <Typography variant="inherit" display="inline" className={colorPercent(signedupPercent)}>{signedupText} Inscrits</Typography>
                <span> | </span>
                <Typography variant="inherit" display="inline">{target} Cible</Typography>
            </>
        );
    };


    useEffect(loadData, []); // Empty array means useEffect will only be called on first render

    return (
        <>
            <MaterialTable
                title="Manipulations"
                isLoading={isLoading}
                columns={[
                    { title: 'ID', field: 'id', defaultSort: 'desc', width: 100 },
                    { title: 'Nom', field: 'name' },
                    { title: 'Date de début', field: 'start_date', type: 'date', width: 200 },
                    // TODO : Change datepicker format (see PR https://github.com/mbrn/material-table/pull/2082)
                    { title: 'Durée', field: 'duration', type: 'numeric', width: 100 },
                    { title: 'Statistiques', field: 'target_slots', render: Stats },
                    { title: 'Responsables', field: 'manager_names' }
                ]}
                actions={[
                    {
                        icon:         'refresh',
                        tooltip:      'Recharger',
                        isFreeAction: true,
                        onClick:      loadData,
                    },
                    {
                        icon:         'add',
                        tooltip:      'Nouvelle Manipulation',
                        isFreeAction: true,
                        onClick:      () => props.history.push('/manipulations/new')
                    },
                    {
                        icon:    () => <ViewIcon />,
                        tooltip: 'Visualiser',
                        onClick: (event, rowData) => props.history.push('/manipulations/' + rowData.id)
                    },
                    rowData => (rowData.deleted_at === null ? {
                        icon:    () => <TodayIcon />,
                        tooltip: 'Créneaux',
                        onClick: (event, rowData) => props.history.push('/manipulations/' + rowData.id + '/slots')
                    } : emptyAction),
                    rowData => (rowData.deleted_at === null ? {
                        icon:    () => <PlaylistAddCheckIcon />,
                        tooltip: 'Feuilles d\'Appel',
                        onClick: (event, rowData) => setCallSheet(rowData)
                    } : emptyAction),
                    rowData => (rowData.deleted_at === null ? {
                        icon:    () => <AssignmentTurnedInIcon />,
                        tooltip: 'Présence',
                        onClick: (event, rowData) => props.history.push('/manipulations/' + rowData.id + '/attendance')
                    } : emptyAction),
                    rowData => (rowData.deleted_at === null ? {
                        icon:    'edit',
                        tooltip: 'Modifier',
                        onClick: (event, rowData) => props.history.push('/manipulations/' + rowData.id + '/edit')
                    } : emptyAction),
                    rowData => ({
                        icon:    () => rowData.deleted_at === null ? <ArchiveIcon/> : <UnarchiveIcon />,
                        tooltip: rowData.deleted_at === null ? 'Archiver' : 'Désarchiver',
                        onClick: (event, rowData) => setDeleteEntry(rowData)
                    })
                ]}
                options={{
                    actionsColumnIndex:  -1,
                    filtering:           true,
                    pageSize:            25,
                    pageSizeOptions:     [10, 25, 50],
                    emptyRowsWhenPaging: false,
                    rowStyle:            rowData => ({
                        backgroundColor: (rowData.deleted_at !== null) ? '#CCC' : '#FFF'
                    })
                }}
                localization={{
                    pagination: {
                        labelDisplayedRows: '{from}-{to} sur {count}',
                        labelRowsSelect:    'lignes',
                        labelRowsPerPage:   'Lignes par page',
                    },
                    header: {
                        actions: 'Actions'
                    },
                    body: {
                        emptyDataSourceMessage: 'Aucune ligne à afficher',
                        filterRow:              {
                            filterTooltip: 'Filtrer'
                        }
                    }
                }}
                data={tableData}
            />
            <Dialog
                open={!!callSheet}
                onClose={() => {
                    setCallSheet(null);
                }}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">
                    {'Télécharger les Feuilles d\'Appel'}
                    <IconButton aria-label="close" className={classes.closeButton} onClick={() => setCallSheet(null)}>
                        <CloseIcon />
                    </IconButton>
                </DialogTitle>
                {callSheet && (
                    <List dense>
                        {
                            callSheet.slots
                                .filter(s => {
                                    const diff = moment(s.start).diff(moment().startOf('day'), 'days', true);
                                    return diff > 0 && diff < 7;
                                })
                                .reduce((all, s) => {
                                    const day = moment(s.start).format('YYYY-MM-DD');
                                    if(!all.includes(day)){
                                        all.push(day);
                                    }
                                    return all.sort();
                                }, [])
                                .map(day => (
                                    <ListItem button onClick={() => {
                                        window.open(
                                            Constants.API_SLOTS_ENDPOINT + callSheet.id + '/call_sheet?date='+day,
                                            '_blank'
                                        );
                                        setCallSheet(null);
                                    }} key={day}>
                                        {`${capitalize(moment(day).format('dddd'))} ${moment(day).format('D')} ${capitalize(moment(day).format('MMMM'))} ${moment(day).format('YYYY')}`}
                                    </ListItem>
                                ))
                        }
                    </List>
                )}
            </Dialog>
            <Dialog
                open={!!deleteEntry || !!deleteError}
                onClose={() => {
                    setDeleteEntry(null);
                    setDeleteError(null);
                }}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">
                    {deleteEntry ? (
                        deleteEntry.deleted_at === null ? 'Archiver la manipulation ' + deleteEntry.name + ' ?' : 'Restaurer la manipulation ' + deleteEntry.name + ' ?'
                    ) : ''}
                    {deleteError ? ('Erreur lors de la suppression : ' + deleteError) : ''}
                </DialogTitle>
                {deleteEntry && (
                    <DialogActions>
                        <Button onClick={() => setDeleteEntry(null)} color="secondary">
                            Annuler
                        </Button>
                        <Button onClick={() => handleDelete(deleteEntry)} color="primary" autoFocus>
                            Confirmer
                        </Button>
                    </DialogActions>
                )}
                {deleteError && (
                    <DialogActions>
                        <Button onClick={() => { setDeleteError(null); loadData(); }} color="primary" autoFocus>
                            Fermer
                        </Button>
                    </DialogActions>
                )}
            </Dialog>
        </>
    );
}
