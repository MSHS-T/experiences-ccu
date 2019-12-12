import React, { Component, useState, useEffect } from "react";
import MaterialTable from "material-table";
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';

import ViewIcon from '@material-ui/icons/Visibility';

import { useAuthContext } from "../../context/Auth";
import * as Constants from "../../data/Constants";


export default function UserList(props) {
    const { accessToken } = useAuthContext();

    const [isLoading, setLoading] = useState(true);
    const [tableData, setTableData] = useState([]);
    const [deleteEntry, setDeleteEntry] = useState(null);
    const [deleteError, setDeleteError] = useState(null);

    const loadData = () => {
        setLoading(true);
        setTableData([]);

        fetch(Constants.API_USERS_ENDPOINT, { headers: { 'Authorization': 'bearer ' + accessToken } })
            // Parse JSON response
            .then(data => data.json())
            // Reprocess data :
            //  - flatten roles array to keep only the name
            .then(data => data.map(row => ({
                ...row,
                roles: row.roles.map(r => r.name).sort().join(', ')
            })))
            // Set data in state
            .then(data => {
                setTableData(data);
                setLoading(false);
            });
    }

    const handleDelete = (entry) => {
        setDeleteError(null);
        fetch(Constants.API_USERS_ENDPOINT + entry.id, {
            method: 'DELETE',
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
    }


    useEffect(loadData, []); // Empty array means useEffect will only be called on first render

    return (
        <>
            <MaterialTable
                title="Utilisateurs"
                isLoading={isLoading}
                columns={[
                    { title: 'ID', field: 'id', defaultSort: 'desc' },
                    { title: 'Prénom', field: 'first_name' },
                    { title: 'Nom', field: 'last_name' },
                    { title: 'Email', field: 'email' },
                    { title: 'Rôle(s)', field: 'roles', sorting: false },
                ]}
                actions={[
                    {
                        icon: 'refresh',
                        tooltip: 'Recharger',
                        isFreeAction: true,
                        onClick: loadData,
                    },
                    {
                        icon: 'add',
                        tooltip: 'Nouvel Utilisateur',
                        isFreeAction: true,
                        onClick: (event) => props.history.push('/users/new')
                    },
                    {
                        icon: () => <ViewIcon />,
                        tooltip: 'Visualiser',
                        onClick: (event, rowData) => props.history.push('/users/' + rowData.id)
                    },
                    {
                        icon: 'edit',
                        tooltip: 'Modifier',
                        onClick: (event, rowData) => props.history.push('/users/' + rowData.id + '/edit')
                    },
                    {
                        icon: 'delete',
                        tooltip: 'Supprimer',
                        onClick: (event, rowData) => setDeleteEntry(rowData)
                    }
                ]}
                options={{
                    actionsColumnIndex: 5,
                    filtering: true,
                    pageSize: 10,
                    pageSizeOptions: [10, 25, 50]
                }}
                localization={{
                    pagination: {
                        labelDisplayedRows: '{from}-{to} sur {count}',
                        labelRowsSelect: 'lignes',
                        labelRowsPerPage: 'Lignes par page',
                    },
                    header: {
                        actions: 'Actions'
                    },
                    body: {
                        emptyDataSourceMessage: 'Aucune ligne à afficher',
                        filterRow: {
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
                    {deleteEntry ? ("Supprimer l'utilisateur " + deleteEntry.first_name + " " + deleteEntry.last_name + " ?") : ""}
                    {deleteError ? ("Erreur lors de la suppression : " + deleteError) : ""}
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
    )
}

