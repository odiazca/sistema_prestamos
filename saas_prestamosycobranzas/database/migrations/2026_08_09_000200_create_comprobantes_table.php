<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comprobantes electrónicos emitidos (boletas / facturas) asociados a los
 * pagos de préstamos. Guarda el snapshot del cliente, los importes con su
 * desglose de IGV y el resultado del envío a SUNAT.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();

            // Identificación del comprobante
            $table->string('tipo', 2)->default('03');        // 01 factura, 03 boleta
            $table->string('serie', 4);
            $table->unsignedInteger('correlativo');

            // Relaciones de origen
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();

            // Snapshot del cliente (por si cambian sus datos luego)
            $table->string('cliente_tipo_doc', 1)->default('0'); // catálogo 06 SUNAT
            $table->string('cliente_num_doc', 20)->nullable();
            $table->string('cliente_nombre', 200)->nullable();

            // Importes
            $table->string('moneda', 3)->default('PEN');
            $table->string('afectacion', 2)->default('10');   // 10 gravado, 20 exonerado, 30 inafecto
            $table->decimal('gravado', 12, 2)->default(0);
            $table->decimal('exonerado', 12, 2)->default(0);
            $table->decimal('inafecto', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('concepto', 200)->nullable();

            // Resultado SUNAT
            $table->string('estado', 15)->default('pendiente'); // pendiente|aceptado|rechazado|error|anulado
            $table->string('sunat_codigo', 10)->nullable();
            $table->text('mensaje')->nullable();
            $table->string('hash', 100)->nullable();
            $table->string('xml_path', 255)->nullable();
            $table->string('cdr_path', 255)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tipo', 'serie', 'correlativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
