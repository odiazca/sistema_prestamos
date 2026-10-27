<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AsistenteController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ChatIaController;
use App\Http\Controllers\CobranzaController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CorteCajaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpenoController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas publicas (invitado)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Recuperación de contraseña
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequest'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : view('landing'))->name('home');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (autenticadas)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Búsqueda global (todos los roles)
    Route::get('/buscar', [SearchController::class, 'index'])->name('buscar');

    // Panel exclusivo del Super Administrador
    Route::get('/super-admin', [SuperAdminController::class, 'index'])->name('superadmin.index')->middleware('rol:superadmin');

    // Modulos funcionales (CRUD)
    Route::resource('clientes', ClienteController::class)->except('show')->middleware('rol:admin,gerente,operador,cobrador');
    Route::resource('prestamos', PrestamoController::class)->middleware('rol:admin,gerente,operador');

    // Pagos
    Route::middleware('rol:admin,gerente,operador,cobrador')->group(function () {
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/prestamos/{prestamo}/pagar', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('/prestamos/{prestamo}/pagar', [PagoController::class, 'store'])->name('pagos.store');
        Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');

        // Cobranzas y Mora
        Route::get('/cobranzas', [CobranzaController::class, 'index'])->name('cobranzas.index');
        Route::get('/mora', [CobranzaController::class, 'mora'])->name('mora.index');

        // Comprobantes electrónicos (historial)
        Route::get('/comprobantes', [ComprobanteController::class, 'index'])->name('comprobantes.index');
        Route::post('/comprobantes/{comprobante}/reemitir', [ComprobanteController::class, 'reemitir'])->name('comprobantes.reemitir');
        Route::get('/comprobantes/{comprobante}/xml', [ComprobanteController::class, 'xml'])->name('comprobantes.xml');
    });

    // Empenos
    Route::middleware('rol:admin,gerente,operador')->group(function () {
        Route::resource('empenos', EmpenoController::class);
        Route::patch('/empenos/{empeno}/estado', [EmpenoController::class, 'cambiarEstado'])->name('empenos.estado');

        // Caja
        Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
        Route::post('/caja', [CajaController::class, 'store'])->name('caja.store');
        Route::delete('/caja/{movimiento}', [CajaController::class, 'destroy'])->name('caja.destroy');
    });

    // Corte de caja, reportes y auditoria (gestión)
    Route::middleware('rol:admin,gerente')->group(function () {
        Route::get('/corte-caja', [CorteCajaController::class, 'index'])->name('corte.index');
        Route::post('/corte-caja', [CorteCajaController::class, 'store'])->name('corte.store');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/{tipo}', [ReporteController::class, 'ver'])->name('reportes.ver');
        Route::get('/reportes/{tipo}/excel', [ReporteController::class, 'excel'])->name('reportes.excel');

        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    });

    // Administracion (solo admin / superadmin)
    Route::middleware('rol:admin')->group(function () {
        Route::resource('usuarios', UserController::class)->parameters(['usuarios' => 'usuario'])->except('show');
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('config.index');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('config.update');

        // Facturación Electrónica (Perú - SUNAT)
        Route::get('/facturacion', [FacturacionController::class, 'index'])->name('facturacion.config');
        Route::put('/facturacion', [FacturacionController::class, 'update'])->name('facturacion.update');
        Route::post('/facturacion/probar', [FacturacionController::class, 'probarConexion'])->name('facturacion.probar');
        Route::post('/facturacion/prueba', [FacturacionController::class, 'emitirPrueba'])->name('facturacion.prueba');
    });
    // Asistente IA y Chat IA (solo administrador)
    Route::middleware('admin')->group(function () {
        Route::get('/asistente', [AsistenteController::class, 'index'])->name('asistente.index');
        Route::prefix('asistente')->name('asistente.')->group(function () {
            Route::get('/estado', [AsistenteController::class, 'estado'])->name('estado');
            Route::post('/config', [AsistenteController::class, 'guardarConfig'])->name('config');
            Route::post('/probar', [AsistenteController::class, 'probar'])->name('probar');
            Route::post('/preguntar', [AsistenteController::class, 'preguntar'])->name('preguntar');
            Route::post('/ejecutar', [AsistenteController::class, 'ejecutar'])->name('ejecutar');
            Route::post('/resumir', [AsistenteController::class, 'resumir'])->name('resumir');
            Route::post('/favorito', [AsistenteController::class, 'favorito'])->name('favorito');
            Route::post('/borrar', [AsistenteController::class, 'borrar'])->name('borrar');
            Route::get('/csv/{id}', [AsistenteController::class, 'csv'])->whereNumber('id')->name('csv');
            Route::get('/esquema', [AsistenteController::class, 'esquema'])->name('esquema');
        });

        Route::get('/chat-ia', [ChatIaController::class, 'index'])->name('chat.index');
        Route::prefix('chat-ia')->name('chat.')->group(function () {
            Route::get('/estado', [ChatIaController::class, 'estado'])->name('estado');
            Route::post('/enviar', [ChatIaController::class, 'enviar'])->name('enviar');
            Route::post('/limpiar', [ChatIaController::class, 'limpiar'])->name('limpiar');
        });
    });

    // Perfil del usuario
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::put('/perfil', [ProfileController::class, 'updatePerfil'])->name('perfil.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');
    Route::post('/perfil/foto', [ProfileController::class, 'updateFoto'])->name('perfil.foto');
    Route::delete('/perfil/foto', [ProfileController::class, 'deleteFoto'])->name('perfil.foto.delete');
});
