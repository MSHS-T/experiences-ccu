<?php

return [
    /**
     * Role list
     */
    'roles' => [
        'administrator',
        'plateau_manager',
        'manipulation_manager',
    ],

    /**
     * Permission array structure :
     *   key = group
     *   value = array :
     *     key = permission
     *     value = allowed role list
     *
     * N.B. : no need to add owner since they have all permissions
     *
     * @see app/Providers/AuthServiceProvider.php
     */
    'permissions' => [
        'user' => [
            'list'   => ['administrator'],
            'create' => ['administrator'],
            'edit'   => ['administrator'],
            'delete' => ['administrator'],
        ],
    ]
];
