<?php

return [
    'refund_request_type' => [
        'return' => 'Return',
        'refund' => 'Refund',
        'replacement' => 'Replacement',
    ],

    'refundRequestTypeLabel' => [
        'return' => '<label class="badge badge-primary text-white">Return</label>',
        'refund' => '<label class="badge badge-success text-white">Refund</label>',
        'replacement' => '<label class="badge badge-warning text-white">Replacement</label>',
    ],

    'refund_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'processing' => 'Processing',
        'completed' => 'Completed',
    ],

    'refundStatusLabel' => [
        'pending'    => '<label class="badge bg-warning text-white">Pending</label>',
        'approved'   => '<label class="badge bg-success text-white">Approved</label>',
        'rejected'   => '<label class="badge bg-danger text-white">Rejected</label>',
        'processing' => '<label class="badge bg-info text-white">Processing</label>',
        'completed'  => '<label class="badge bg-primary text-white">Completed</label>',
    ],

    'refundMethod' => [
        'original_payment' => 'Original Payment',
        'store_credit' => 'Store Credit',
        'manual' => 'Manual',
    ],
];
