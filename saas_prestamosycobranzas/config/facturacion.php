<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Drivers de emisión disponibles
    |--------------------------------------------------------------------------
    | Cada driver implementa App\Services\Facturacion\Contracts\EmisorDriver.
    | 'ninguno' deja los comprobantes en estado pendiente (no emite).
    | 'sunat'   emite comprobantes electrónicos reales contra SUNAT (Greenter).
    */
    'drivers' => [
        'ninguno' => \App\Services\Facturacion\Drivers\NingunoDriver::class,
        'sunat'   => \App\Services\Facturacion\Drivers\SunatDriver::class,
    ],

    'driver_labels' => [
        'ninguno' => 'Ninguno (no emite, deja pendiente)',
        'sunat'   => 'SUNAT (emisión directa · UBL 2.1)',
    ],

    'entornos' => [
        'beta'       => 'Beta (homologación / pruebas)',
        'produccion' => 'Producción',
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoints de los Web Services de SUNAT
    |--------------------------------------------------------------------------
    | Se usan según el entorno seleccionado en la configuración.
    */
    'endpoints' => [
        'beta' => [
            'factura' => 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl',
            'guia'    => 'https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService?wsdl',
            'retencion' => 'https://e-beta.sunat.gob.pe/ol-ti-itemision-otroscpe-gem-beta/billService?wsdl',
        ],
        'produccion' => [
            'factura' => 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl',
            'guia'    => 'https://api-cpe.sunat.gob.pe',
            'retencion' => 'https://e-factura.sunat.gob.pe/ol-ti-itemision-otroscpe-gem/billService?wsdl',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Credenciales del entorno Beta de SUNAT (homologación)
    |--------------------------------------------------------------------------
    | RUC de pruebas 20000000001 con usuario MODDATOS / clave MODDATOS.
    */
    'beta_demo' => [
        'ruc'     => '20000000001',
        'usuario' => 'MODDATOS',
        'clave'   => 'MODDATOS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carpeta (disco 'local' privado) donde se guarda el certificado .pem
    |--------------------------------------------------------------------------
    */
    'certificado_dir' => 'facturacion/pe',
];
