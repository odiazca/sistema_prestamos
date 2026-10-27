<?php

namespace App\Services\Ia;

use App\Models\Configuracion;
use App\Models\IaConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

/**
 * Asistente de reportes con IA + Chat IA · Préstamos y Cobranzas Pro
 *
 * Seguridad:
 *  - La IA NUNCA toca las tablas reales: solo ve las vistas de lectura v_ia_*.
 *  - El SQL generado se valida (solo SELECT sobre v_ia_*), se ejecuta en una transacción de solo lectura,
 *    con límite de tiempo y de filas.
 *  - Las vistas no exponen contraseñas, tokens, credenciales SUNAT ni la tabla de usuarios.
 */
class IaService
{
    public const MAX_FILAS = 500;

    private string $moneda;
    private static array $colCache = [];
    private static ?array $reales = null;

    public function __construct(private User $user)
    {
        $this->moneda = (string) Configuracion::get('moneda', 'S/');
    }

    public static function presets(): array
    {
        return config('ia.presets');
    }

    /* =====================================================================
     *  Vistas de solo lectura (la «capa de inteligencia»)
     * ===================================================================== */
    public static function vistas(): array
    {
        $cli = "CONCAT(c.nombres,' ',c.apellidos)";

        return [
            'v_ia_prestamos' => [
                "Una fila por préstamo. estado: activo (al día), mora (tiene cuotas vencidas), pagado o cancelado. Cartera vigente = estados 'activo' y 'mora'. capital_prestado = dinero desembolsado; interes_total = interés total pactado; total_pagar = capital + interés; saldo_pendiente = lo que aún falta cobrar; total_pagado = lo ya cobrado. frecuencia: diario, semanal, quincenal o mensual. asesor = usuario que registró el préstamo.",
                "SELECT p.id AS prestamo_id, p.codigo, $cli AS cliente, c.documento AS cliente_documento, p.monto AS capital_prestado, p.tasa_interes, p.numero_cuotas, p.frecuencia, p.monto_cuota, p.total_pagar, p.interes_total, p.saldo AS saldo_pendiente, (p.total_pagar - p.saldo) AS total_pagado, p.fecha_inicio, p.estado, COALESCE(u.name,'Sin asesor') AS asesor, "
                . "(SELECT COUNT(*) FROM cuotas q WHERE q.prestamo_id=p.id AND q.estado='pagado') AS cuotas_pagadas, "
                . "(SELECT COUNT(*) FROM cuotas q WHERE q.prestamo_id=p.id AND q.estado<>'pagado' AND q.fecha_vencimiento<CURDATE()) AS cuotas_vencidas, "
                . "(SELECT COALESCE(MAX(DATEDIFF(CURDATE(),q.fecha_vencimiento)),0) FROM cuotas q WHERE q.prestamo_id=p.id AND q.estado<>'pagado' AND q.fecha_vencimiento<CURDATE()) AS max_dias_atraso, "
                . "p.created_at AS creado_en "
                . "FROM prestamos p JOIN clientes c ON c.id=p.cliente_id LEFT JOIN users u ON u.id=p.user_id",
            ],
            'v_ia_cuotas' => [
                "Una fila por cuota del cronograma de cada préstamo. estado: pendiente, parcial, pagado o vencido. USA SIEMPRE esta_vencida (1 = cuota no pagada cuya fecha ya pasó) y dias_atraso para mora, porque 'estado' puede no estar actualizado. saldo_cuota = lo que falta cobrar de esa cuota (monto - monto_pagado). Cartera por cobrar = SUM(saldo_cuota) con estado<>'pagado'. Cuotas por vencer = estado<>'pagado' y fecha_vencimiento >= hoy. Ignora estado_prestamo='cancelado' salvo que lo pidan.",
                "SELECT q.id AS cuota_id, p.id AS prestamo_id, p.codigo AS prestamo, $cli AS cliente, c.telefono AS cliente_telefono, q.numero AS numero_cuota, p.numero_cuotas AS total_cuotas, q.fecha_vencimiento, q.monto, q.capital, q.interes, q.mora, q.monto_pagado, GREATEST(q.monto - q.monto_pagado,0) AS saldo_cuota, q.fecha_pago, q.estado, "
                . "(q.estado<>'pagado' AND q.fecha_vencimiento<CURDATE()) AS esta_vencida, "
                . "CASE WHEN q.estado<>'pagado' AND q.fecha_vencimiento<CURDATE() THEN DATEDIFF(CURDATE(),q.fecha_vencimiento) ELSE 0 END AS dias_atraso, p.estado AS estado_prestamo "
                . "FROM cuotas q JOIN prestamos p ON p.id=q.prestamo_id JOIN clientes c ON c.id=p.cliente_id",
            ],
            'v_ia_pagos' => [
                "Una fila por pago (cobro) registrado. Un mismo abono grande puede aparecer en varias filas, una por cada cuota que cubrió (todas con la misma fecha). metodo: efectivo, transferencia, yape, plin o deposito. cobrador = usuario que registró el cobro. Para ingresos por cobranza usa SUM(monto).",
                "SELECT pg.id AS pago_id, pg.codigo, pg.fecha_pago, pg.monto, pg.metodo, pg.referencia, p.codigo AS prestamo, $cli AS cliente, qq.numero AS numero_cuota, COALESCE(u.name,'Sin usuario') AS cobrador "
                . "FROM pagos pg JOIN prestamos p ON p.id=pg.prestamo_id JOIN clientes c ON c.id=p.cliente_id LEFT JOIN cuotas qq ON qq.id=pg.cuota_id LEFT JOIN users u ON u.id=pg.user_id",
            ],
            'v_ia_clientes' => [
                "Clientes con el resumen de su cartera. estado: activo, inactivo o moroso. saldo_pendiente = deuda actual en préstamos activos o en mora. cuotas_vencidas = cuotas impagas ya vencidas. total_pagado = todo lo que el cliente ha cobrado el negocio en pagos.",
                "SELECT c.id AS cliente_id, c.codigo, $cli AS cliente, c.documento, c.tipo_documento, c.telefono, c.email, c.direccion, c.ocupacion, c.ingreso_mensual, c.estado, "
                . "(SELECT COUNT(*) FROM prestamos p WHERE p.cliente_id=c.id AND p.estado<>'cancelado') AS num_prestamos, "
                . "(SELECT COUNT(*) FROM prestamos p WHERE p.cliente_id=c.id AND p.estado IN ('activo','mora')) AS prestamos_vigentes, "
                . "(SELECT COALESCE(SUM(p.monto),0) FROM prestamos p WHERE p.cliente_id=c.id AND p.estado<>'cancelado') AS capital_prestado_total, "
                . "(SELECT COALESCE(SUM(p.saldo),0) FROM prestamos p WHERE p.cliente_id=c.id AND p.estado IN ('activo','mora')) AS saldo_pendiente, "
                . "(SELECT COUNT(*) FROM cuotas q JOIN prestamos p ON p.id=q.prestamo_id WHERE p.cliente_id=c.id AND q.estado<>'pagado' AND q.fecha_vencimiento<CURDATE()) AS cuotas_vencidas, "
                . "(SELECT COALESCE(SUM(pg.monto),0) FROM pagos pg JOIN prestamos p ON p.id=pg.prestamo_id WHERE p.cliente_id=c.id) AS total_pagado, "
                . "c.created_at AS creado_en "
                . "FROM clientes c",
            ],
            'v_ia_empenos' => [
                "Empeños (préstamos con prenda). estado: vigente, recuperado, vencido o rematado. dias_para_vencer < 0 = ya venció. monto_prestado = dinero entregado contra la prenda; valor_tasacion = valor estimado del artículo.",
                "SELECT e.id AS empeno_id, e.codigo, $cli AS cliente, e.articulo, e.descripcion, e.valor_tasacion, e.monto_prestado, e.tasa_interes, e.fecha_inicio, e.fecha_vencimiento, DATEDIFF(e.fecha_vencimiento,CURDATE()) AS dias_para_vencer, e.estado "
                . "FROM empenos e JOIN clientes c ON c.id=e.cliente_id",
            ],
            'v_ia_caja' => [
                "Movimientos de caja manuales (ingresos y egresos). tipo: ingreso o egreso. categoria p. ej. aporte_capital, gasto_operativo, desembolso. No incluye los cobros de cuotas (esos están en v_ia_pagos).",
                "SELECT m.id AS movimiento_id, m.codigo, m.fecha, m.tipo, m.categoria, m.concepto, m.monto, m.metodo, COALESCE(u.name,'Sin usuario') AS usuario "
                . "FROM movimientos_caja m LEFT JOIN users u ON u.id=m.user_id",
            ],
            'v_ia_cortes_caja' => [
                "Cortes (arqueos) de caja diarios. diferencia > 0 = sobrante, < 0 = faltante.",
                "SELECT k.id AS corte_id, k.fecha, COALESCE(u.name,'Sin usuario') AS usuario, k.total_cobros, k.total_ingresos, k.total_egresos, k.saldo_calculado, k.monto_contado, k.diferencia, k.observaciones "
                . "FROM cortes_caja k LEFT JOIN users u ON u.id=k.user_id",
            ],
            'v_ia_comprobantes' => [
                "Comprobantes electrónicos (SUNAT). tipo: '01' factura, '03' boleta. estado: pendiente, aceptado, rechazado, error o anulado. Los emitidos válidos son 'aceptado'.",
                "SELECT cp.id AS comprobante_id, cp.tipo, CONCAT(cp.serie,'-',LPAD(cp.correlativo,8,'0')) AS numero, DATE(cp.created_at) AS fecha, cp.cliente_nombre, cp.cliente_num_doc, pr.codigo AS prestamo, cp.moneda, cp.gravado, cp.exonerado, cp.inafecto, cp.igv, cp.total, cp.concepto, cp.estado "
                . "FROM comprobantes cp LEFT JOIN prestamos pr ON pr.id=cp.prestamo_id",
            ],
        ];
    }

    /** Crea (o recrea) las vistas si faltan. Con $force siempre las recrea. */
    public static function instalarVistas(bool $force = false): void
    {
        $vistas = self::vistas();
        if (! $force) {
            $n = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.views WHERE table_schema = DATABASE() AND table_name LIKE 'v\\_ia\\_%'");
            if ((int) ($n->c ?? 0) === count($vistas)) {
                return;
            }
        }
        foreach ($vistas as $v => [, $sql]) {
            DB::unprepared('CREATE OR REPLACE VIEW `' . $v . '` AS ' . $sql);
        }
        self::$colCache = [];
        self::$reales = null;
    }

    public static function quitarVistas(): void
    {
        foreach (array_keys(self::vistas()) as $v) {
            DB::unprepared('DROP VIEW IF EXISTS `' . $v . '`');
        }
    }

    private function columnas(string $vista): array
    {
        if (! isset(self::$colCache[$vista])) {
            self::$colCache[$vista] = [];
            foreach (DB::select("SHOW COLUMNS FROM `$vista`") as $c) {
                self::$colCache[$vista][$c->Field] = strtolower(preg_replace('/\(.*/', '', $c->Type));
            }
        }

        return self::$colCache[$vista];
    }

    public function esquemaTexto(): string
    {
        $t = '';
        foreach (self::vistas() as $v => [$desc]) {
            $cols = [];
            foreach ($this->columnas($v) as $campo => $tipo) {
                $cols[] = "$campo $tipo";
            }
            $t .= "$v: $desc\n  columnas: " . implode(', ', $cols) . "\n";
        }

        return $t;
    }

    /* =====================================================================
     *  Configuración y clave cifrada
     * ===================================================================== */
    public function cfg(): array
    {
        $presets = self::presets();
        $row = IaConfig::first();
        $p = $row->proveedor ?? 'gemini';
        if (! isset($presets[$p])) {
            $p = 'gemini';
        }
        $clave = '';
        if ($row && $row->clave) {
            try {
                $clave = Crypt::decryptString($row->clave);
            } catch (Throwable) {
                $clave = '';
            }
        }

        return [
            'proveedor' => $p,
            'url' => $row->url ?? $presets[$p]['url'],
            'modelo' => $row->modelo ?? $presets[$p]['modelo'],
            'clave' => $clave,
            'resumen' => $row ? (bool) $row->resumen : true,
        ];
    }

    public function listo(?array $c = null): bool
    {
        $c ??= $this->cfg();

        return $c['url'] !== '' && $c['modelo'] !== '' && (! self::presets()[$c['proveedor']]['clave'] || $c['clave'] !== '');
    }

    public function guardarConfig(string $proveedor, string $url, string $modelo, bool $resumen, string $clave): void
    {
        $datos = ['proveedor' => $proveedor, 'url' => $url, 'modelo' => $modelo, 'resumen' => $resumen];
        if ($clave === '__borrar__') {
            $datos['clave'] = null;
        } elseif ($clave !== '') {
            $datos['clave'] = Crypt::encryptString($clave);
        }
        $row = IaConfig::first();
        $row ? $row->update($datos) : IaConfig::create($datos);
    }

    /* =====================================================================
     *  Llamada al modelo (API compatible con OpenAI)
     * ===================================================================== */
    public function llamar(array $mensajes, int $maxTokens = 900): string
    {
        $c = $this->cfg();
        if ($c['url'] === '' || $c['modelo'] === '') {
            throw new RuntimeException('Configura el proveedor de IA (URL y modelo).');
        }
        if (self::presets()[$c['proveedor']]['clave'] && $c['clave'] === '') {
            throw new RuntimeException('Falta la clave (API key) del proveedor de IA. Configúrala con el botón «Configurar IA».');
        }
        $url = rtrim($c['url'], '/') . '/chat/completions';
        if (! preg_match('#^https?://#i', $url)) {
            throw new RuntimeException('La URL del proveedor debe empezar por http:// o https://');
        }

        try {
            $req = Http::timeout(60)->connectTimeout(10)->acceptJson()->asJson();
            if ($c['clave'] !== '') {
                $req = $req->withToken($c['clave']);
            }
            $res = $req->post($url, ['model' => $c['modelo'], 'messages' => $mensajes, 'temperature' => 0, 'max_tokens' => $maxTokens]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('No se pudo conectar con el proveedor de IA (' . mb_substr($e->getMessage(), 0, 140) . '). Revisa tu internet y la URL.');
        }

        $code = $res->status();
        $j = $res->json();
        if ($code === 401 || $code === 403) {
            throw new RuntimeException('El proveedor rechazó la clave (API key). Revisa que sea correcta.');
        }
        if ($code === 429) {
            throw new RuntimeException('Se alcanzó el límite del plan gratuito. Espera un minuto e inténtalo de nuevo.');
        }
        if ($code === 404) {
            throw new RuntimeException('Modelo o URL no encontrados (' . $c['modelo'] . '). Google renueva sus modelos: prueba con gemini-3.5-flash-lite o gemini-3.5-flash.');
        }
        if ($code >= 400 || ! is_array($j)) {
            $m = is_array($j) ? ($j['error']['message'] ?? ($j[0]['error']['message'] ?? '')) : '';
            throw new RuntimeException('Error del proveedor de IA (' . $code . ')' . ($m ? ': ' . mb_substr((string) $m, 0, 200) : '.'));
        }
        $txt = $j['choices'][0]['message']['content'] ?? '';
        if (is_array($txt)) {
            $txt = implode('', array_map(fn ($p) => $p['text'] ?? '', $txt));
        }
        if (trim((string) $txt) === '') {
            throw new RuntimeException('La IA no devolvió respuesta. Intenta reformular la pregunta.');
        }

        return (string) $txt;
    }

    public static function extraerJson(string $t): ?array
    {
        $t = trim($t);
        if (preg_match('/```(?:json)?\s*(.*?)```/is', $t, $m)) {
            $t = trim($m[1]);
        }
        $j = json_decode($t, true);
        if (is_array($j)) {
            return $j;
        }
        $a = strpos($t, '{');
        $b = strrpos($t, '}');
        if ($a !== false && $b > $a) {
            $j = json_decode(substr($t, $a, $b - $a + 1), true);
            if (is_array($j)) {
                return $j;
            }
        }

        return null;
    }

    /* =====================================================================
     *  Validación del SQL (defensa en profundidad)
     * ===================================================================== */
    public function validarSql(string $sql): string
    {
        $sql = rtrim(trim($sql), "; \t\r\n");
        if ($sql === '' || strlen($sql) > 4000) {
            throw new InvalidArgumentException('La consulta está vacía o es demasiado larga.');
        }
        // Quita literales de texto para analizar solo la estructura
        $s = preg_replace("/'(?:[^'\\\\]|\\\\.|'')*'/s", ' 0 ', $sql);
        $s = preg_replace('/"(?:[^"\\\\]|\\\\.|"")*"/s', ' 0 ', $s);
        if (strpbrk($s, "'\"") !== false) {
            throw new InvalidArgumentException('Comillas sin cerrar en la consulta.');
        }
        if (str_contains($s, ';')) {
            throw new InvalidArgumentException('Solo se permite una consulta.');
        }
        if (preg_match('/--|\/\*|\*\/|#|@/', $s)) {
            throw new InvalidArgumentException('No se permiten comentarios ni variables.');
        }
        $l = strtolower(str_replace('`', ' ', $s));
        if (! preg_match('/^\s*(select|with)\b/', $l)) {
            throw new InvalidArgumentException('Solo se permiten consultas SELECT.');
        }
        $prohib = 'insert|update|delete|drop|alter|create|truncate|grant|revoke|call|execute|prepare|handler|load|outfile|dumpfile|into|lock|unlock|set|rename|use|show|describe|explain|optimize|analyze|sleep|benchmark|load_file|get_lock|release_lock|information_schema|performance_schema|mysql|sys|user|current_user|session_user|system_user|database|schema|version|connection_id|row_count|found_rows|last_insert_id|uuid|procedure|kill|shutdown|flush|reset|purge|install|uninstall|import|binlog|xa|savepoint|commit|rollback|begin|start|recursive';
        if (preg_match('/\b(' . $prohib . ')\b/', $l, $m)) {
            throw new InvalidArgumentException('Palabra no permitida en la consulta: ' . strtoupper($m[1]));
        }
        if (preg_match('/for\s+update|lock\s+in\s+share/', $l)) {
            throw new InvalidArgumentException('Bloqueos no permitidos.');
        }
        // Ninguna tabla/vista real salvo las v_ia_* (que son lo único permitido)
        if (self::$reales === null) {
            self::$reales = [];
            foreach (DB::connection()->getPdo()->query('SHOW FULL TABLES')->fetchAll(PDO::FETCH_NUM) as $r) {
                self::$reales[strtolower($r[0])] = true;
            }
            self::$reales[strtolower((string) config('database.connections.' . config('database.default') . '.database'))] = true;
        }
        if (preg_match_all('/(?<![\w.])(?:(as)\s+)?([a-z_][a-z0-9_]*)\b/', $l, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $t) {
                if ($t[1] === '' && isset(self::$reales[$t[2]]) && ! str_starts_with($t[2], 'v_ia_')) {
                    throw new InvalidArgumentException('Solo puedes consultar las vistas v_ia_* (no la tabla «' . $t[2] . '»). Si es un alias, usa otro nombre.');
                }
            }
        }
        if (preg_match('/\b[a-z_][a-z0-9_]*\s*\.\s*[a-z_][a-z0-9_]*\s*\./', $l)) {
            throw new InvalidArgumentException('No se permiten nombres con base de datos.');
        }

        return $sql;
    }

    /** Ejecuta en solo lectura con límite de tiempo y de filas. Devuelve columnas, filas, truncado, ms y sql. */
    public function ejecutar(string $sql): array
    {
        $sql = $this->validarSql($sql);
        self::instalarVistas();
        $pdo = DB::connection()->getPdo();
        $t0 = microtime(true);
        foreach (['SET SESSION MAX_EXECUTION_TIME=8000', 'SET SESSION max_statement_time=8'] as $x) {
            try {
                $pdo->exec($x);
            } catch (Throwable) {
            }
        }
        $buf = $pdo->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
        try {
            $pdo->exec('START TRANSACTION READ ONLY');
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $st = $pdo->query($sql);
            $n = $st->columnCount();
            $cols = $tipos = [];
            for ($i = 0; $i < $n; $i++) {
                $m = $st->getColumnMeta($i);
                $cols[] = $m['name'] ?? ('col' . $i);
                $tipos[] = strtoupper((string) ($m['native_type'] ?? ''));
            }
            $filas = [];
            $trunc = false;
            while ($r = $st->fetch(PDO::FETCH_NUM)) {
                if (count($filas) >= self::MAX_FILAS) {
                    $trunc = true;
                    break;
                }
                foreach ($r as $i => $v) {
                    if ($v !== null && is_string($v) && in_array($tipos[$i], ['NEWDECIMAL', 'DECIMAL', 'LONG', 'LONGLONG', 'SHORT', 'TINY', 'INT24', 'FLOAT', 'DOUBLE'], true) && is_numeric($v)) {
                        $r[$i] = $v + 0;
                    }
                }
                $filas[] = $r;
            }
            $st->closeCursor();
        } catch (PDOException $e) {
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $buf);
            try {
                $pdo->exec('ROLLBACK');
            } catch (Throwable) {
            }
            throw new RuntimeException('Error SQL: ' . preg_replace('/^SQLSTATE\[[^\]]*\]:?\s*(\d+\s*)?/', '', $e->getMessage()));
        }
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $buf);
        try {
            $pdo->exec('ROLLBACK');
        } catch (Throwable) {
        }

        return ['columnas' => $cols, 'filas' => $filas, 'truncado' => $trunc, 'ms' => (int) round((microtime(true) - $t0) * 1000), 'sql' => $sql];
    }

    /* =====================================================================
     *  Reglas de negocio compartidas por el Asistente y el Chat
     * ===================================================================== */
    private function reglas(): string
    {
        $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $hoy = Carbon::now();

        return "REGLAS:\n- Genera un único SELECT (o WITH ... SELECT) usando SOLO las vistas v_ia_*. Sin punto y coma, sin comentarios, sin variables.\n"
            . '- Hoy es ' . $dias[(int) $hoy->format('w')] . ' ' . $hoy->format('Y-m-d') . " (usa CURDATE()/NOW() para fechas relativas). La semana empieza el lunes. «Este mes» = desde el día 1 del mes actual hasta hoy; «mes pasado» = mes calendario anterior.\n"
            . "- Los estados, métodos de pago y frecuencias están en minúscula. Cartera vigente = préstamos con estado 'activo' o 'mora' (se excluyen 'pagado' y 'cancelado').\n"
            . "- Mora / cuotas vencidas: usa v_ia_cuotas con esta_vencida=1 (y estado_prestamo<>'cancelado'); la antigüedad de la deuda es dias_atraso. Cartera por cobrar = SUM(saldo_cuota) de cuotas con estado<>'pagado'. Cobranza = SUM(monto) de v_ia_pagos agrupando por fecha_pago, cobrador o metodo.\n"
            . "- Para buscar por nombre de cliente usa LIKE '%texto%' sobre LOWER(cliente), con el texto en minúsculas.\n"
            . '- Moneda: ' . $this->moneda . ". Redondea importes con ROUND(x,2). Pon alias de columnas legibles en español (sin espacios: usa guion bajo), por ejemplo cliente, saldo_pendiente, total_cobrado.\n"
            . '- Si piden un ranking sin cantidad, usa LIMIT 10; en cualquier caso nunca más de ' . self::MAX_FILAS . " filas. Ordena de forma útil.\n"
            . '- Si la pregunta no se puede responder con estas vistas, no inventes.';
    }

    /* =====================================================================
     *  Asistente de reportes: pregunta en lenguaje natural → SQL
     * ===================================================================== */
    private function promptSistema(): string
    {
        return "Eres el analista de datos de una empresa de préstamos y cobranzas (con empeños). Conviertes preguntas en español en UNA consulta SQL (MySQL 8) de solo lectura.\n\n"
            . "VISTAS DISPONIBLES (no existe nada más):\n" . $this->esquemaTexto() . "\n"
            . $this->reglas() . " Si no se puede responder, devuelve sql null y explica en «mensaje».\n\n"
            . "Responde SOLO con un objeto JSON, sin texto adicional ni markdown:\n"
            . '{"titulo":"título corto del reporte","sql":"SELECT ...","grafico":{"tipo":"bar|line|pie|none","x":"alias_columna_etiquetas","y":"alias_columna_numerica"},"mensaje":null}' . "\n"
            . 'grafico.tipo: line para series por fecha, bar para rankings/comparaciones, pie para participación (máx. 8 categorías), none si no tiene sentido graficar.';
    }

    public function preguntar(string $pregunta, ?array $previo = null): array
    {
        $m = [['role' => 'system', 'content' => $this->promptSistema()]];
        if ($previo && ! empty($previo['pregunta']) && ! empty($previo['sql'])) {
            $m[] = ['role' => 'user', 'content' => mb_substr((string) $previo['pregunta'], 0, 300)];
            $m[] = ['role' => 'assistant', 'content' => json_encode(['titulo' => '', 'sql' => mb_substr((string) $previo['sql'], 0, 1500), 'grafico' => ['tipo' => 'none'], 'mensaje' => null], JSON_UNESCAPED_UNICODE)];
        }
        $m[] = ['role' => 'user', 'content' => $pregunta];
        $ultimoError = '';
        for ($intento = 0; $intento < 2; $intento++) {
            $raw = $this->llamar($m);
            $j = self::extraerJson($raw);
            if (! $j) {
                $m[] = ['role' => 'assistant', 'content' => mb_substr($raw, 0, 800)];
                $m[] = ['role' => 'user', 'content' => 'Tu respuesta no era JSON válido. Responde SOLO el objeto JSON indicado.'];
                $ultimoError = 'La IA respondió en un formato inesperado.';
                continue;
            }
            if (empty($j['sql'])) {
                return ['mensaje' => (string) (($j['mensaje'] ?? '') ?: 'No puedo responder esa pregunta con los datos disponibles. Prueba con préstamos, cuotas, mora, pagos, clientes, empeños o caja.')];
            }
            try {
                $r = $this->ejecutar((string) $j['sql']);
                $g = is_array($j['grafico'] ?? null) ? $j['grafico'] : [];

                return $r + [
                    'titulo' => mb_substr(trim((string) ($j['titulo'] ?? '')) ?: $pregunta, 0, 160),
                    'grafico' => [
                        'tipo' => in_array($g['tipo'] ?? '', ['bar', 'line', 'pie', 'none'], true) ? $g['tipo'] : 'none',
                        'x' => (string) ($g['x'] ?? ''),
                        'y' => (string) ($g['y'] ?? ''),
                    ],
                ];
            } catch (Throwable $e) {
                $ultimoError = $e->getMessage();
                $m[] = ['role' => 'assistant', 'content' => $raw];
                $m[] = ['role' => 'user', 'content' => 'Esa consulta falló: ' . $ultimoError . '. Corrígela usando solo las vistas y columnas listadas y responde de nuevo SOLO el JSON.'];
            }
        }
        throw new RuntimeException('No pude generar una consulta válida para esa pregunta. Reformúlala con más detalle. (' . $ultimoError . ')');
    }

    public function resumir(string $pregunta, array $cols, array $filas): string
    {
        $muestra = array_slice($filas, 0, 25);
        $txt = $this->llamar([
            ['role' => 'system', 'content' => 'Eres un analista de una empresa de préstamos y cobranzas. Responde en español, en 1 o 2 frases claras, mencionando las cifras más relevantes. No uses listas, ni markdown, ni inventes datos que no estén en la tabla. Moneda: ' . $this->moneda . '.'],
            ['role' => 'user', 'content' => "Pregunta: $pregunta\nColumnas: " . implode(', ', $cols) . "\nFilas (" . count($filas) . ' en total, se muestran ' . count($muestra) . "):\n" . json_encode($muestra, JSON_UNESCAPED_UNICODE)],
        ], 250);

        return trim(strip_tags($txt));
    }

    /* =====================================================================
     *  Chat conversacional
     * ===================================================================== */
    private const GUIA = 'Panel Principal: resumen y gráficos. Clientes: directorio (alta, edición, estado activo/inactivo/moroso). Préstamos: crear un préstamo (monto, tasa de interés, número de cuotas y frecuencia diaria/semanal/quincenal/mensual); se genera el cronograma de cuotas automáticamente. Cobranzas: bandeja de cuotas por vencer, de hoy y vencidas. Pagos / Cuotas: para cobrar, entra al préstamo y pulsa «Pagar» (o /prestamos/{id}/pagar); el monto se aplica en cascada a las cuotas pendientes y puede emitir comprobante. Mora: cuotas vencidas y préstamos en mora. Empeños: registrar prendas, tasación, vencimiento y cambiar estado (vigente, recuperado, vencido, rematado). Comprobantes: boletas y facturas electrónicas SUNAT del historial, con reemisión y XML. Reportes: reportes de la operación exportables a CSV/Excel. Caja: ingresos y egresos manuales. Corte de Caja: arqueo diario (calculado vs contado). Auditoría: bitácora de acciones de usuarios. Usuarios, Facturación Electrónica y Configuración (moneda, tasa por defecto, mora diaria, días de gracia): solo administrador. Roles: admin, gerente, operador y cobrador. Asistente IA (reportes con gráficos) y Chat IA: solo administrador.';

    private function promptChat(): string
    {
        $nombre = (string) Configuracion::get('empresa_nombre', 'la empresa');

        return 'Eres el chatbot de «' . $nombre . "», un sistema de gestión de préstamos, cobranzas y empeños. Conversas en español con el administrador, con tono cercano y breve.\n"
            . "Puedes: (1) consultar datos del negocio (préstamos, cuotas, mora, pagos, clientes, empeños, caja, comprobantes); (2) explicar cómo usar el sistema; (3) saludar o aclarar. NO puedes registrar, modificar ni borrar nada: si te lo piden, explica en qué módulo se hace.\n\n"
            . "VISTAS DISPONIBLES (no existe nada más):\n" . $this->esquemaTexto() . "\n" . $this->reglas() . "\n"
            . "- Para datos de un cliente o préstamo concretos devuelve pocas columnas útiles y LIMIT 20.\n"
            . "- Si el mensaje es una continuación («¿y el mes pasado?», «¿y por cobrador?»), reutiliza y adapta la consulta anterior.\n\n"
            . 'GUÍA DEL SISTEMA: ' . self::GUIA . "\n\n"
            . "Responde SOLO con un objeto JSON, sin texto adicional ni markdown, en una de dos formas:\n"
            . '{"accion":"consulta","sql":"SELECT ..."}  → cuando necesites datos.' . "\n"
            . '{"accion":"respuesta","texto":"..."}  → cuando no necesites datos (saludo, ayuda de uso, aclaración, o si algo no es posible).';
    }

    /** Un turno de chat. $hist = [['q'=>..., 'sql'=>?, 'a'=>...], ...]. Devuelve ['texto'=>..., 'tabla'=>?, 'sql'=>?] */
    public function chat(string $msg, array $hist): array
    {
        $m = [['role' => 'system', 'content' => $this->promptChat()]];
        foreach (array_slice($hist, -6) as $h) {
            $m[] = ['role' => 'user', 'content' => (string) $h['q']];
            $m[] = ['role' => 'assistant', 'content' => json_encode(! empty($h['sql']) ? ['accion' => 'consulta', 'sql' => $h['sql']] : ['accion' => 'respuesta', 'texto' => mb_substr((string) $h['a'], 0, 300)], JSON_UNESCAPED_UNICODE)];
        }
        $m[] = ['role' => 'user', 'content' => $msg];
        $err = '';
        for ($i = 0; $i < 2; $i++) {
            $raw = $this->llamar($m, 700);
            $j = self::extraerJson($raw);
            if (! $j) {
                $t = trim(strip_tags($raw));
                if ($t !== '' && $t[0] !== '{') {
                    return ['texto' => $t];
                }
                $m[] = ['role' => 'assistant', 'content' => mb_substr($raw, 0, 500)];
                $m[] = ['role' => 'user', 'content' => 'Responde SOLO con el objeto JSON indicado.'];
                continue;
            }
            if (($j['accion'] ?? '') !== 'consulta' || empty($j['sql'])) {
                return ['texto' => trim((string) ($j['texto'] ?? $j['respuesta'] ?? '')) ?: 'No entendí bien. ¿Puedes darme más detalle?'];
            }
            try {
                $r = $this->ejecutar((string) $j['sql']);
            } catch (Throwable $e) {
                $err = $e->getMessage();
                $m[] = ['role' => 'assistant', 'content' => $raw];
                $m[] = ['role' => 'user', 'content' => 'Esa consulta falló: ' . $err . '. Corrígela usando solo las vistas y columnas listadas y responde SOLO el JSON.'];
                continue;
            }
            $muestra = array_slice($r['filas'], 0, 25);
            $texto = $this->llamar([
                ['role' => 'system', 'content' => 'Eres el chatbot de una empresa de préstamos y cobranzas. Responde en español con tono cercano y breve (máximo 4 frases o una lista corta con guiones). Usa las cifras EXACTAS de los datos, sin inventar nada. Importes con el símbolo ' . $this->moneda . ' (ejemplo de formato: ' . $this->moneda . ' 1,234.50). Si no hay filas, dilo claramente. No menciones SQL, vistas ni columnas técnicas.'],
                ['role' => 'user', 'content' => "Pregunta: $msg\nColumnas: " . implode(', ', $r['columnas']) . "\nFilas (" . count($r['filas']) . ' en total' . ($r['truncado'] ? ', hay más' : '') . ', se muestran ' . count($muestra) . "):\n" . json_encode($muestra, JSON_UNESCAPED_UNICODE)],
            ], 450);

            return [
                'texto' => trim(strip_tags($texto)),
                'sql' => $r['sql'],
                'tabla' => ['columnas' => $r['columnas'], 'filas' => array_slice($r['filas'], 0, 10), 'total' => count($r['filas'])],
            ];
        }
        throw new RuntimeException('No pude consultar eso con los datos disponibles. Prueba reformulando la pregunta. (' . $err . ')');
    }
}
