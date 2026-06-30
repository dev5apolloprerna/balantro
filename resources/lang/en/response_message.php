<?php
return [
    'password' => [
        'forgot_success'    => 'Password reset link sent to :email.',
        'email_not_found'   => 'We could not find a user with that email address.',
        'otp_sent_success'  => 'OTP has been sent to :email.',
        'otp_success'     => 'OTP has been been match successfully.',
        'reset_success'     => 'Your password has been reset successfully.',
    ],

    'profile' => [
        'show_success' => 'Profile loaded successfully!', // put your exact text here
        'update_success' => 'Profile Updated successfully!', // put your exact text here
        'profile_not_found' => 'Profile Not Found.', // put your exact text here
        'create_error' => 'Unable to Process!', // put your exact text here
        'show_error' => 'Profile Not Found.', // put your exact text here
    ],
    'dashboard' => [
        'document_summary' => 'Document summary generated successfully.',
        'dashboard_error'  => 'Something went wrong while loading the dashboard. Please try again.',
        'dropdown_type_list'  => 'Dropdown types loaded successfully.',
        'dropdown_type_error' => 'Unable to load dropdown types. Please try again.',
        'graph_list' => 'Graph list fetched successfully.',
        'graph_list_error' => 'Unable to fetch graph list. Please try again later.',
        'unauthorized_role'      => 'You are not authorized to access this dashboard.',
        'dashboard_data'         => 'Dashboard data retrieved successfully.',
        // 'dashboard_error'        => 'Failed to load dashboard data. Please try again later.',
        'groups_loaded'          => 'Dashboard groups loaded successfully.',
        'groups_error'           => 'Unable to load dashboard groups.',
        'groups_multiple_of_four' => 'Groups should be in multiples of four.',
        'preferences_saved'      => 'Dashboard preferences saved successfully.',
        'preferences_error'      => 'Unable to save dashboard preferences. Please try again.',
        'graph_data'             => 'Graph data fetched successfully.',
        'graph_error'            => 'Unable to load graph data. Please try again.',
        'year_listing'           => 'Financial year list loaded successfully.',
        'year_listing_error'     => 'Unable to load financial year list.',
        'financial_summary'       => 'Dashboard financial summary loaded successfully.',
        'financial_summary_error' => 'Unable to load dashboard financial summary.',
        'monthly_financial_columns' => 'Monthly financial columns loaded successfully.',
        'monthly_financial_columns_error' => 'Unable to load monthly financial columns.',
        // 'document_summary' => 'Document summary generated successfully.',
        // 'dashboard_error'  => 'Failed to load dashboard data.',
    ],
    'document' => [
        'id_required'        => 'Document ID is required.',
        'not_found'          => 'Document not found.',
        'delete_success'     => 'Document deleted successfully.',
        'delete_error'       => 'Failed to delete the document.',
        'delete_unauthorized' => 'You are not authorized to delete this document.',
        'create_success'     => 'Document created successfully.',
        'index_success'      => 'Document list retrieved successfully.',
    ],

];
