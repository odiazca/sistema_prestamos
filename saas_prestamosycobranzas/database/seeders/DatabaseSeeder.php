<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Empeno;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Usuarios ----
        User::updateOrCreate(
            ['email' => 'superadmin@prestamospro.test'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'rol' => 'superadmin',
                'telefono' => '999000111',
                'activo' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@prestamospro.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'rol' => 'admin',
                'telefono' => '999111222',
                'activo' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'cobrador@prestamospro.test'],
            [
                'name' => 'Carlos Cobrador',
                'password' => Hash::make('password'),
                'rol' => 'cobrador',
                'activo' => true,
            ]
        );

        // ---- Clientes ----
        $nombres = [
            ['Maria', 'Lopez Quispe'], ['Juan', 'Perez Rojas'], ['Ana', 'Torres Diaz'],
            ['Luis', 'Garcia Mamani'], ['Rosa', 'Flores Vega'], ['Pedro', 'Ramos Soto'],
            ['Carmen', 'Castro Nina'], ['Jorge', 'Vargas Leon'], ['Lucia', 'Mendoza Cruz'],
            ['Miguel', 'Chavez Pino'], ['Elena', 'Salas Ruiz'],
        ];

        $clientes = [];
        foreach ($nombres as $i => $n) {
            $clientes[] = Cliente::create([
                'codigo' => 'CLI-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'nombres' => $n[0],
                'apellidos' => $n[1],
                'tipo_documento' => 'DNI',
                'documento' => (string) (40000000 + rand(100000, 999999)),
                'telefono' => '9'.rand(10000000, 99999999),
                'email' => strtolower($n[0]).'@correo.test',
                'direccion' => 'Av. Principal '.rand(100, 999),
                'ocupacion' => ['Comerciante', 'Independiente', 'Empleado', 'Agricultor'][rand(0, 3)],
                'ingreso_mensual' => rand(1000, 5000),
                'estado' => 'activo',
            ]);
        }

        // ---- Prestamos + Cuotas + Pagos ----
        $totalCobrado = 0;
        foreach ($clientes as $idx => $cliente) {
            // No todos tienen prestamo
            if ($idx % 4 === 3) {
                continue;
            }

            $monto = rand(5, 30) * 100;            // 500 - 3000
            $tasa = [10, 12, 15, 20][rand(0, 3)];  // % total simple
            $cuotasN = [4, 6, 8, 12][rand(0, 3)];
            $interesTotal = round($monto * $tasa / 100, 2);
            $totalPagar = $monto + $interesTotal;
            $montoCuota = round($totalPagar / $cuotasN, 2);
            $fechaInicio = Carbon::now()->subMonths(rand(0, 4))->subDays(rand(0, 20));

            $prestamo = Prestamo::create([
                'codigo' => 'PRE-'.str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'cliente_id' => $cliente->id,
                'monto' => $monto,
                'tasa_interes' => $tasa,
                'numero_cuotas' => $cuotasN,
                'frecuencia' => 'mensual',
                'monto_cuota' => $montoCuota,
                'total_pagar' => $totalPagar,
                'interes_total' => $interesTotal,
                'saldo' => $totalPagar,
                'fecha_inicio' => $fechaInicio,
                'estado' => 'activo',
                'user_id' => 1,
            ]);

            $saldo = $totalPagar;
            $pagadas = rand(0, $cuotasN); // cuantas cuotas ya pagadas
            for ($c = 1; $c <= $cuotasN; $c++) {
                $venc = (clone $fechaInicio)->addMonths($c);
                $estado = 'pendiente';
                $montoPagado = 0;
                $fechaPago = null;

                if ($c <= $pagadas) {
                    $estado = 'pagado';
                    $montoPagado = $montoCuota;
                    $fechaPago = (clone $venc)->subDays(rand(0, 5));
                    $saldo -= $montoCuota;
                    $totalCobrado += $montoCuota;
                } elseif ($venc->isPast()) {
                    $estado = 'vencido';
                }

                $cuota = Cuota::create([
                    'prestamo_id' => $prestamo->id,
                    'numero' => $c,
                    'fecha_vencimiento' => $venc,
                    'monto' => $montoCuota,
                    'capital' => round($monto / $cuotasN, 2),
                    'interes' => round($interesTotal / $cuotasN, 2),
                    'monto_pagado' => $montoPagado,
                    'fecha_pago' => $fechaPago,
                    'estado' => $estado,
                ]);

                if ($estado === 'pagado') {
                    Pago::create([
                        'codigo' => 'PAG-'.$prestamo->id.'-'.$c,
                        'prestamo_id' => $prestamo->id,
                        'cuota_id' => $cuota->id,
                        'monto' => $montoCuota,
                        'fecha_pago' => $fechaPago,
                        'metodo' => ['efectivo', 'yape', 'transferencia'][rand(0, 2)],
                        'user_id' => 1,
                    ]);
                }
            }

            $prestamo->saldo = max($saldo, 0);
            if ($saldo <= 0) {
                $prestamo->estado = 'pagado';
            } elseif ($prestamo->cuotas()->where('estado', 'vencido')->exists()) {
                $prestamo->estado = 'mora';
            }
            $prestamo->save();
        }

        // ---- Empenos ----
        $articulos = ['Laptop HP', 'Television Samsung 50"', 'Anillo de oro 18k', 'Moto Honda', 'Celular iPhone'];
        for ($i = 0; $i < 4; $i++) {
            $valor = rand(8, 40) * 100;
            Empeno::create([
                'codigo' => 'EMP-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'cliente_id' => $clientes[array_rand($clientes)]->id,
                'articulo' => $articulos[$i % count($articulos)],
                'descripcion' => 'Articulo en garantia',
                'valor_tasacion' => $valor,
                'monto_prestado' => round($valor * 0.6, 2),
                'tasa_interes' => 15,
                'fecha_inicio' => Carbon::now()->subDays(rand(5, 60)),
                'fecha_vencimiento' => Carbon::now()->addDays(rand(5, 60)),
                'estado' => 'vigente',
            ]);
        }
    }
}
