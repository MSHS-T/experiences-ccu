<?php

return [
    'access_map' => 'storage/plan-acces-ccu.jpg',
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
            'delete' => ['administrator'],
        ],
        'plateau' => [
            'list'   => ['administrator', 'plateau_manager'],
            'create' => ['administrator', 'plateau_manager'],
            'edit'   => ['administrator', 'plateau_manager'],
            'delete' => ['administrator'],
        ],
        'attribution' => [
            'list'    => ['administrator', 'plateau_manager', 'manipulation_manager'],
            'edit'    => ['administrator', 'plateau_manager'],
            'create'  => ['administrator', 'plateau_manager'],
            'delete'  => ['administrator', 'plateau_manager'],
        ],
        'manipulation' => [
            'list'    => ['administrator', 'plateau_manager', 'manipulation_manager'],
            'edit'    => ['administrator', 'manipulation_manager'],
            'create'  => ['administrator', 'manipulation_manager'],
            'publish' => ['administrator', 'plateau_manager'],
            'archive' => ['administrator',],
            'delete'  => ['administrator'],
        ],
    ]
];
