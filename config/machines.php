<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Estados das máquinas
    |--------------------------------------------------------------------------
    |
    | Fonte única de verdade para os estados possíveis de uma máquina e os
    | respetivos rótulos em português. Usado na validação (controllers),
    | nos formulários e na apresentação (views e JS do backoffice).
    |
    */
    'statuses' => [
        'available' => 'Disponível',
        'reserved'  => 'Reservada',
        'sold'      => 'Vendida',
        'inactive'  => 'Indisponível',
    ],
];
