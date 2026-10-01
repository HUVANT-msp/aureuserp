<?php

return [
    // E-mail new colleagues the link to set their password when they are added from
    // Employees. Off while the data is being entered; send later with `huvant:invite`.
    'send_invites' => (bool) env('HUVANT_SEND_INVITES', false),
];
