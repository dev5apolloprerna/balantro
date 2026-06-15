<?php

return [
    'documents' => [
        'controller' => [
            'verify' => [
                'success' => 'Document verified successfully.',
                'error' => 'Failed to verify document: :errors',
            ],
        ],
        'flash' => [
            'create' => [
                'success' => 'Successfully created :count documents.',
                'error' => 'Failed to create :count documents: :details',
            ],
            'delete' => [
                'success' => 'Document deleted successfully!',
                'error'   => 'Only documents in "Uploaded" status can be deleted.',
            ],
        ],
    ],

];
