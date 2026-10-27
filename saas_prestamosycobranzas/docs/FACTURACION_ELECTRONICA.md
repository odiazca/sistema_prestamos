# Facturación Electrónica (Perú · SUNAT)

Módulo de configuración y emisión de comprobantes electrónicos (UBL 2.1) contra
SUNAT, integrado en el estilo del sistema.

## Puesta en marcha

1. **Instalar la librería de emisión** (genera el XML, lo firma y lo envía a SUNAT):

   ```bash
   composer require greenter/lite
   ```

   Extensiones PHP requeridas: `soap`, `openssl`, `dom`, `mbstring`, `zip`.

2. **Ejecutar la migración nueva** (crea la tabla `facturacion_configs`):

   ```bash
   php artisan migrate
   ```

3. **Crear el enlace de storage** (el certificado se guarda en disco privado):

   ```bash
   php artisan storage:link
   ```

4. Entrar como **administrador** → menú **Administración › Facturación Electrónica**
   (`/facturacion`).

## Configuración

- **Estado y modo**: habilitar la facturación, emisión automática, *driver*
  (`Ninguno` = no emite / `SUNAT` = emisión real) y entorno (`Beta` o `Producción`).
- **Datos del emisor**: RUC, razón social, dirección fiscal, ubigeo y series.
- **Credenciales SUNAT**: usuario y clave SOL, y certificado digital `.pem`.

### Pruebas en Beta (homologación)

Puedes usar el RUC `20000000001` con usuario y clave `MODDATOS`. El botón
**Probar conexión con SUNAT** verifica certificado, credenciales y conectividad.
Con el driver `SUNAT` seleccionado aparece **Emitir boleta de prueba (Beta)**,
que envía una boleta demo real al entorno de homologación.

### Certificado digital

Sube el certificado en formato **PEM** (certificado + llave privada en un solo
archivo). Se almacena de forma privada en `storage/app/private/facturacion/pe/certificate.pem`.
Si tienes un `.pfx`/`.p12`, conviértelo:

```bash
openssl pkcs12 -in certificado.pfx -out certificate.pem -nodes
```

## Arquitectura

```
app/Services/Facturacion/
├── Contracts/EmisorDriver.php      Interfaz de los drivers de emisión
├── Drivers/NingunoDriver.php       No emite (deja pendiente)
├── Drivers/SunatDriver.php         Emisión real a SUNAT vía Greenter
├── Support/Numero.php              Importe a letras (leyenda 1000)
└── FacturacionManager.php          Resuelve el driver y delega
```

La configuración es un *singleton* (`FacturacionConfig::actual()`). Las claves SOL
y del certificado se guardan **cifradas** (`APP_KEY`).

### Emisión automática al registrar un pago

Al registrar un pago (`PagoController@store`), si la facturación está
**habilitada** y en modo **automático**, se genera un comprobante por el total
pagado. El tipo se decide por el cliente: **factura** si tiene RUC (11 dígitos),
**boleta** en los demás casos. El IGV se desglosa según la **afectación**
configurada (gravado / exonerado / inafecto) de modo que el total del
comprobante coincida con lo pagado. Un fallo de SUNAT nunca bloquea el pago:
el comprobante queda como `pendiente`/`error` para reintentar.

Tabla `comprobantes`: guarda serie/correlativo, snapshot del cliente, desglose
de importes, estado (`pendiente|aceptado|rechazado|error|anulado`), mensaje de
SUNAT y ruta del XML firmado.

### Historial de comprobantes

Menú **Finanzas › Comprobantes** (`/comprobantes`): listado con filtros por
estado, descarga del XML y botón **reintentar envío** para los pendientes,
rechazados o con error.

### Emitir un comprobante desde código

```php
$comprobante = app(\App\Services\Facturacion\ComprobanteService::class)
    ->emitirDesdePrestamo($prestamo, 100.00);
// $comprobante->estado ∈ aceptado | rechazado | pendiente | error
```
