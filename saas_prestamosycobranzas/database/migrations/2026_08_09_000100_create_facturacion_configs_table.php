<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración de Facturación Electrónica (Perú - SUNAT).
 * Tabla de una sola fila (singleton) que guarda el estado, los datos del
 * emisor y las credenciales para la emisión de comprobantes electrónicos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_configs', function (Blueprint $table) {
            $table->id();

            // Estado y modo
            $table->boolean('habilitado')->default(false);
            $table->boolean('emitir_automatico')->default(true);
            $table->string('driver', 30)->default('ninguno');   // ninguno | sunat
            $table->string('entorno', 20)->default('beta');      // beta | produccion

            // Datos del emisor
            $table->string('ruc', 11)->nullable();
            $table->string('razon_social', 150)->nullable();
            $table->string('nombre_comercial', 150)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('departamento', 60)->nullable();
            $table->string('provincia', 60)->nullable();
            $table->string('distrito', 60)->nullable();

            // Series por defecto (para emisión futura)
            $table->string('serie_factura', 4)->default('F001');
            $table->string('serie_boleta', 4)->default('B001');

            // Tratamiento de IGV para los conceptos emitidos.
            // Por defecto exonerado (20), habitual para intereses de préstamo.
            $table->string('afectacion_igv', 2)->default('20'); // 10 gravado, 20 exonerado, 30 inafecto

            // Credenciales SUNAT (los campos sensibles se encriptan en el modelo)
            $table->string('sol_usuario', 60)->nullable();
            $table->text('sol_clave')->nullable();
            $table->string('certificado_path', 255)->nullable();
            $table->text('certificado_clave')->nullable();

            $table->timestamps();
        });

        // Fila inicial por defecto (singleton)
        DB::table('facturacion_configs')->insert([
            'habilitado'        => false,
            'emitir_automatico' => true,
            'driver'            => 'ninguno',
            'entorno'           => 'beta',
            'serie_factura'     => 'F001',
            'serie_boleta'      => 'B001',
            'afectacion_igv'    => '20',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_configs');
    }
};
