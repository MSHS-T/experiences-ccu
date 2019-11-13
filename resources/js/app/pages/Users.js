import React, { Component } from "react";
import MaterialTable from "material-table";

import { useAuthContext } from "../context/Auth";
import TableIcons from "../data/TableIcons";

export default function Users() {
    const { user, accessToken } = useAuthContext();
    return (
        <MaterialTable
            title="Utilisateurs"
            icons={TableIcons}
            columns={[
                { title: 'ID', field: 'id' },
                { title: 'Prénom', field: 'first_name' },
                { title: 'Nom', field: 'last_name' },
                { title: 'Email', field: 'email' },
                { title: 'Rôle(s)', field: 'roles', render: rowData => rowData.roles.map(r => r.name).sort().join(', ') },
            ]}
            data={query =>
                new Promise((resolve, reject) => {
                    let url = 'http://localhost/api/user?';
                    url += 'count=' + query.pageSize;
                    url += '&page=' + (query.page + 1);
                    const params = {
                        headers: { 'Authorization': "bearer " + accessToken }
                    };
                    fetch(url, params)
                        .then(response => response.json())
                        .then(result => {
                            resolve({
                                data: result.data,
                                page: result.current_page - 1,
                                totalCount: result.total,
                            })
                        })
                })
            }
        />
    )
}

