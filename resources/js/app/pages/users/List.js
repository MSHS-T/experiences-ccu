import React, { Component, useState, useEffect } from "react";
import MaterialTable from "material-table";

import { useAuthContext } from "../../context/Auth";

import ViewIcon from '@material-ui/icons/Visibility';

export default function Users() {
    const { user, accessToken } = useAuthContext();

    const [ isLoading, setLoading ] = useState(true);
    const [ tableData, setTableData ] = useState([]);

    const loadData = () => {
        setLoading(true);
        setTableData([]);

        fetch('http://localhost/api/user', { headers: { 'Authorization': 'bearer ' + accessToken } })
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

    useEffect(loadData, []); // Empty array means useEffect will only be called on first render

    return (
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
                    onClick: (event) => console.log("New User")
                },
                {
                    icon: () => <ViewIcon/>,
                    tooltip: 'Visualiser',
                    onClick: (event, rowData) => console.log("View User #" + rowData.id)
                },
                {
                    icon: 'edit',
                    tooltip: 'Modifier',
                    onClick: (event, rowData) => console.log("Edit User #" + rowData.id)
                },
                {
                    icon: 'delete',
                    tooltip: 'Supprimer',
                    onClick: (event, rowData) => console.log("Delete User #" + rowData.id)
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
    )
}

