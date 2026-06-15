<?php

return [
    'base' => [
        'flash' => [
            'authorize_super_admin_alert' => 'You are not authorized to access this page.',
        ],
    ],
    'user_create_msg' => ':role created successfully.',
    'managers' => [
        'user_create_msg' => 'Manager created successfully.',
        'user_create_msg'   => 'Manager created successfully.',
        'user_update_msg'   => 'Manager updated successfully.',
        'user_delete_msg'   => 'Manager deleted successfully.',
        'user_create_error' => 'Unable to create the manager. Please try again.',
    ],
    'supervisors' => [
        'user_create_msg' => 'Supervisor created successfully.',
    ],
    'data_entry_operators' => [
        'user_create_msg' => 'Data Entry Operator created successfully.',
    ],
    'managers' => [
        'flash' => [
            'manager_delete_msg' => 'Manager deleted successfully.',
            'manager_update_msg' => 'Manager updated successfully.',
        ],
        'user_create_msg' => 'Manager created successfully.',
        'user_update_msg' => 'Manager updated successfully.',
        'user_delete_msg' => 'Manager deleted successfully.',
    ],
    'manager' => [
        'user_create_msg'   => 'Manager created successfully.',
        'user_update_msg'   => 'Manager updated successfully.',
        'user_delete_msg'   => 'Manager deleted successfully.',
        'user_create_error' => 'Unable to create the manager. Please try again.',
    ],
    'supervisors' => [
        'flash' => [
            'supervisor_update_msg' => 'Supervisor updated successfully.',
            'supervisor_delete_msg' => 'Supervisor was deleted successfully.',
            'supervisor_delete_alert' => 'Could not delete supervisor.',
            'manager_assigned_msg' => 'Managers assigned to supervisor successfully.',
            'supervisor_create_msg' => 'Supervisor created successfully.',
        ],
    ],
    'supervisor' => [
        'user_create_msg'   => 'Supervisor created successfully.',
        'user_update_msg'   => 'Supervisor updated successfully.',
        'user_delete_msg'   => 'Supervisor deleted successfully.',
        'user_create_error' => 'Unable to create the supervisor. Please try again.',
    ],
    'assign_groups' => [
        'messages' => [
            'update_success' => 'Groups updated for supervisor.',
        ],
    ],
    'assign_permissions' => [
        'messages' => [
            'update_success' => 'Permissions updated successfully.',
        ],
    ],
    // 'clients' => [
    //     'flash' => [
    //         'client_update_msg'   => 'Client saved successfully.',
    //         // (optional extras if you use them elsewhere)
    //         'client_delete_msg'   => 'Client deleted successfully.',
    //         'users_assigned_msg'  => 'Users assigned successfully.',
    //     ],
    // ],
    'clients' => [
        'flash' => [
            'client_update_msg'   => 'Client updated successfully.',
            'client_create_msg'   => 'Client created successfully.',
            'client_delete_msg'   => 'Client deleted successfully.',
            'client_update_error' => 'Unable to update the Client. Please try again.',
            'users_assigned_msg'  => 'Users assigned successfully.',
        ],
    ],
    'data_entry_operator' => [
        'user_create_msg'   => 'Data Entry Operator created successfully.',
        'user_update_msg'   => 'Data Entry Operator updated successfully.',
        'user_delete_msg'   => 'Data Entry Operator deleted successfully.',
        'user_create_error' => 'Unable to create the Data Entry Operator. Please try again.',
    ],
    'data_entry_operators' => [
        'flash' => [
            'operator_update_msg' => 'Data Entry Operator updated successfully.',
            'operator_create_msg'   => 'Data Entry Operator created successfully.',
            'operator_delete_msg'   => 'Data Entry Operator deleted successfully.',
            'operator_update_error' => 'Unable to update the Data Entry Operator. Please try again.',
            'users_assigned_msg' => 'Users have been successfully assigned to the Data Entry Operator.',
        ],
    ],

    // (optional extras used in your code)
    'assign_groups' => [
        'messages' => [
            'update_success' => 'Groups updated successfully.',
        ],
    ],
    'assign_permissions' => [
        'messages' => [
            'update_success' => 'Permissions updated successfully.',
        ],
    ],

    'assign_permissions' => [
        'messages' => [
            'update_success' => 'Permissions updated successfully.',
        ],
    ],
    'group' => [
        'flash' => [
            'update_success' => 'Group updated successfully!',
            'permissions_assigned' => 'Permissions have been successfully assigned to the group.',
            'create_success' => 'The group has been successfully created.',
            'delete_success' => 'The group has been successfully deleted.',

        ],
    ],
];
