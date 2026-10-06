<?php

return [
    'uninstall' => [
        'title' => 'Conferma disinstallazione',
        'message' => 'Sei sicuro di voler disinstallare il plugin :name?',
        'warning' => '⚠️ Questa azione non può essere annullata e cancellerà i dati in modo permanente.',
    ],
    'dependents' => [
        'title' => 'Plugin dipendenti',
        'description' => 'Questi plugin dipendono da questo. Le dipendenze installate devono essere prima disinstallate.',
        'installed' => 'Installato',
        'not_installed' => 'Non installato',
    ],
    'dependency_warning' => [
        'title' => 'Azione richiesta',
        'message' => '⚠️ Disinstallare i seguenti plugin dipendenti prima di disinstallare :name.',
    ],
    'data_impact' => [
        'title' => 'Impatto sui dati',
        'description' => 'Le seguenti tabelle del database contengono dati che verranno eliminati definitivamente.',
        'records' => ':count record',
    ],
];
