<?php

use App\Services\Ia\IaService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asistente de reportes IA y Chat IA.
 * - ia_configs: proveedor/modelo/clave (cifrada con APP_KEY). Una sola fila.
 * - ia_consultas: historial y favoritas del asistente de reportes (por usuario).
 * - vistas v_ia_*: capa de lectura que es lo ÚNICO que ve la IA.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ia_configs', function (Blueprint $table) {
            $table->id();
            $table->string('proveedor', 30)->default('gemini');
            $table->string('url')->nullable();
            $table->string('modelo', 100)->nullable();
            $table->text('clave')->nullable(); // cifrada
            $table->boolean('resumen')->default(true);
            $table->timestamps();
        });

        Schema::create('ia_consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('pregunta', 500);
            $table->string('titulo', 160)->nullable();
            $table->text('sql_texto');
            $table->string('grafico')->nullable();
            $table->boolean('favorito')->default(false);
            $table->unsignedInteger('veces')->default(1);
            $table->dateTime('ultimo_uso')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'favorito']);
        });

        IaService::instalarVistas(true);
    }

    public function down(): void
    {
        IaService::quitarVistas();
        Schema::dropIfExists('ia_consultas');
        Schema::dropIfExists('ia_configs');
    }
};
