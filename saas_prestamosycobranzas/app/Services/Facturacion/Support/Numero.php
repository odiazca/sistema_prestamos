<?php

namespace App\Services\Facturacion\Support;

/**
 * Conversión de importes a letras para la leyenda 1000 exigida por SUNAT.
 * Ej: 1234.50 PEN => "SON MIL DOSCIENTOS TREINTA Y CUATRO CON 50/100 SOLES".
 */
class Numero
{
    protected static array $unidades = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
        'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE',
        'DIECIOCHO', 'DIECINUEVE', 'VEINTE',
    ];

    protected static array $decenas = [
        '', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA',
    ];

    protected static array $centenas = [
        '', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
        'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS',
    ];

    public static function enLetras(float $monto, string $moneda = 'PEN'): string
    {
        $entero = (int) floor($monto);
        $decimal = (int) round(($monto - $entero) * 100);

        $palabra = $entero === 0 ? 'CERO' : trim(self::convertir($entero));
        $unidadMoneda = $moneda === 'USD' ? 'DOLARES AMERICANOS' : 'SOLES';

        return sprintf('SON %s CON %02d/100 %s', $palabra, $decimal, $unidadMoneda);
    }

    protected static function convertir(int $n): string
    {
        if ($n < 0) {
            return 'MENOS '.self::convertir(-$n);
        }
        if ($n <= 20) {
            return self::$unidades[$n];
        }
        if ($n < 30) {
            $veinti = [
                21 => 'VEINTIUNO', 22 => 'VEINTIDOS', 23 => 'VEINTITRES', 24 => 'VEINTICUATRO',
                25 => 'VEINTICINCO', 26 => 'VEINTISEIS', 27 => 'VEINTISIETE', 28 => 'VEINTIOCHO',
                29 => 'VEINTINUEVE',
            ];

            return $veinti[$n];
        }
        if ($n < 100) {
            $d = intdiv($n, 10);
            $u = $n % 10;

            return self::$decenas[$d].($u ? ' Y '.self::$unidades[$u] : '');
        }
        if ($n === 100) {
            return 'CIEN';
        }
        if ($n < 1000) {
            $c = intdiv($n, 100);
            $r = $n % 100;

            return self::$centenas[$c].($r ? ' '.self::convertir($r) : '');
        }
        if ($n < 1000000) {
            $miles = intdiv($n, 1000);
            $r = $n % 1000;
            $pref = $miles === 1 ? 'MIL' : self::convertir($miles).' MIL';

            return $pref.($r ? ' '.self::convertir($r) : '');
        }

        $millones = intdiv($n, 1000000);
        $r = $n % 1000000;
        $pref = $millones === 1 ? 'UN MILLON' : self::convertir($millones).' MILLONES';

        return $pref.($r ? ' '.self::convertir($r) : '');
    }
}
