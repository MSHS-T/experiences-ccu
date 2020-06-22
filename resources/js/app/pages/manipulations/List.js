import React, { useState, useEffect } from 'react';
import MaterialTable from 'material-table';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogTitle from '@material-ui/core/DialogTitle';
import Typography from '@material-ui/core/Typography';

import { makeStyles } from '@material-ui/core/styles';

import ViewIcon from '@material-ui/icons/Visibility';
import TodayIcon from '@material-ui/icons/Today';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';

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

export default function ManipulationList(props) {
    const classes = useStyles();
    const { accessToken } = useAuthContext();

    const [isLoading, setLoading] = useState(true);
    const [tableData, setTableData] = useState([]);
    const [deleteEntry, setDeleteEntry] = useState(null);
    const [deleteError, setDeleteError] = useState(null);

    const loadData = () => {
        setLoading(true);
        setTableData([]);

        fetch(Constants.API_MANIPULATIONS_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
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
        const signedup = rowData.slots.filter(s => (!!s.subject_email && !!s.subject_confirmed)).length || 0;
        const slotsPercent = slots / target * 100;
        const signedupPercent = signedup / target * 100;

        // eslint-disable-next-line no-undef
        const colorPercent = (percent) => (percent < 100 ? classes.statusDanger : (percent >= APP_SETTINGS.manipulation_overbooking ? classes.statusOk : classes.statusWarning));

        return (
            <>
                <Typography variant="inherit" display="inline" className={colorPercent(slotsPercent)}>{slots} Créneaux</Typography>
                <span> | </span>
                <Typography variant="inherit" display="inline" className={colorPercent(signedupPercent)}>{signedup} Inscrits</Typography>
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
                    { title: 'Durée', field: 'duration', type: 'int', width: 100 },
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
                    {
                        icon:    () => <TodayIcon />,
                        tooltip: 'Créneaux',
                        onClick: (event, rowData) => props.history.push('/manipulations/' + rowData.id + '/slots')
                    },
                    {
                        icon:    'edit',
                        tooltip: 'Modifier',
                        onClick: (event, rowData) => props.history.push('/manipulations/' + rowData.id + '/edit')
                    },
                    {
                        icon:    'delete',
                        tooltip: 'Supprimer',
                        onClick: (event, rowData) => setDeleteEntry(rowData)
                    }
                ]}
                options={{
                    actionsColumnIndex:  -1,
                    filtering:           true,
                    pageSize:            25,
                    pageSizeOptions:     [10, 25, 50],
                    emptyRowsWhenPaging: false,
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
                open={!!deleteEntry || !!deleteError}
                onClose={() => {
                    setDeleteEntry(null);
                    setDeleteError(null);
                }}
                aria-labelledby="alert-dialog-title"
                aria-describedby="alert-dialog-description"
            >
                <DialogTitle id="alert-dialog-title">
                    {deleteEntry ? ('Supprimer la manipulation ' + deleteEntry.name + ' ?') : ''}
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
