<?php

return [
    'parking_transaction' => [
        'create' => [
            '200' => "Transaction has been recorded.",
            'slot_not_exist' => 'No vacant slot for this entrypoint.'
        ],
        'unpark' => [
            '200' => "Transaction has been updated.",
            'transaction_not_exist' => 'Transaction does not exist.'
        ]
    ],
];
