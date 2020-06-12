import React, { useState, useEffect } from 'react';
import MaterialTable from 'material-table';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogTitle from '@material-ui/core/DialogTitle';

import ViewIcon from '@material-ui/icons/Visibility';

import { useAuthContext } from '../../context/Auth';
import * as Constants from '../../data/Constants';


export default function EquipmentList(props) {
    const { accessToken, user } = useAuthContext();

    const [isLoading, setLoading] = useState(true);
    const [tableData, setTableData] = useState([]);
    const [deleteEntry, setDeleteEntry] = useState(null);
    const [deleteError, setDeleteError] = useState(null);

    const loadData = () => {
        setLoading(true);
        setTableData([]);

        fetch(Constants.API_EQUIPMENTS_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken }})
            // Parse JSON response
            .then(data => data.json())
            // Set data in state
            .then(data => {
                setTableData(data);
                setLoading(false);
            });
    };

    const handleDelete = (entry) => {
        setDeleteError(null);
        fetch(Constants.API_EQUIPMENTS_ENDPOINT + entry.id, {
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

    useEffect(loadData, []); // Empty array means useEffect will only be called on first render

    return (
        <>
            <MaterialTable
                title="Matériel"
                isLoading={isLoading}
                columns={[
                    { title: 'ID', field: 'id', defaultSort: 'desc' },
                    { title: 'Nom', field: 'name' },
                    { title: 'Type', field: 'type' },
                    { title: 'Quantité', field: 'quantity' }
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
                        tooltip:      'Nouvel Utilisateur',
                        isFreeAction: true,
                        onClick:      () => props.history.push('/equipments/new')
                    },
                    {
                        icon:    () => <ViewIcon />,
                        tooltip: 'Visualiser',
                        onClick: (event, rowData) => props.history.push('/equipments/' + rowData.id)
                    },
                    {
                        icon:    'edit',
                        tooltip: 'Modifier',
                        onClick: (event, rowData) => props.history.push('/equipments/' + rowData.id + '/edit')
                    },
                    (user.roles.includes('ADMIN') ? {
                        icon:    'delete',
                        tooltip: 'Supprimer',
                        onClick: (event, rowData) => setDeleteEntry(rowData)
                    } : null)
                ]}
                options={{
                    actionsColumnIndex: 5,
                    filtering:          true,
                    pageSize:           10,
                    pageSizeOptions:    [10, 25, 50]
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
                    {deleteEntry ? ('Supprimer le matériel ' + deleteEntry.name + ' ?') : ''}
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

