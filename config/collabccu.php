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
        'equipment' => [
            'list'   => ['administrator', 'plateau_manager'],
            'create' => ['administrator', 'plateau_manager'],
            'edit'   => ['administrator', 'plateau_manager'],
            'delete' => ['administrator', 'plateau_manager'],
        ],
        'plateau' => [
            'list'   => ['administrator', 'plateau_manager'],
            'create' => ['administrator', 'plateau_manager'],
            'edit'   => ['administrator', 'plateau_manager'],
            'delete' => ['administrator', 'plateau_manager'],
        ],
        'manipulation' => [
            'list'    => ['administrator', 'plateau_manager', 'manipulation_manager'],
            'edit'    => ['administrator', 'plateau_manager', 'manipulation_manager'],
            'create'  => ['administrator', 'plateau_manager', 'manipulation_manager'],
            'delete'  => ['administrator', 'plateau_manager', 'manipulation_manager'],
            'archive' => ['administrator', 'plateau_manager'],
            'publish' => ['administrator', 'plateau_manager'],
        ],
    ]
];
