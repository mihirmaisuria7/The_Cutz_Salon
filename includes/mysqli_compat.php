<?php
/**
 * mysqli compatibility layer over PostgreSQL (Supabase).
 * Allows existing MySQL/mysqli code to run against Supabase without rewriting every query.
 */

class MysqliCompatResult
{
    private $rows;
    private $position = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function fetchArray()
    {
        if ($this->position >= count($this->rows)) {
            return null;
        }
        return $this->rows[$this->position++];
    }

    public function numRows()
    {
        return count($this->rows);
    }
}

class MysqliCompatConnection
{
    public $pdo;
    public $connect_errno = 0;
    public $connect_error = '';
    public $lastInsertId = 0;
}

$GLOBALS['_mysqli_connect_errno'] = 0;
$GLOBALS['_mysqli_connect_error'] = '';

function mysqli_connect_errno()
{
    return $GLOBALS['_mysqli_connect_errno'];
}

function mysqli_connect_error()
{
    return $GLOBALS['_mysqli_connect_error'];
}

function mysqli_connect($host = null, $user = null, $pass = null, $db = null, $port = null)
{
    require_once __DIR__ . '/supabase_config.php';

    $con = new MysqliCompatConnection();

    if (SUPABASE_DB_PASSWORD === '') {
        $con->connect_errno = 1;
        $con->connect_error = 'Supabase database password is not set. Edit includes/supabase_config.php and set SUPABASE_DB_PASSWORD from your Supabase Dashboard → Settings → Database.';
        $GLOBALS['_mysqli_connect_errno'] = 1;
        $GLOBALS['_mysqli_connect_error'] = $con->connect_error;
        return $con;
    }

    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
        SUPABASE_DB_HOST,
        SUPABASE_DB_PORT,
        SUPABASE_DB_NAME
    );

    try {
        $con->pdo = new PDO($dsn, SUPABASE_DB_USER, SUPABASE_DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $GLOBALS['_mysqli_connect_errno'] = 0;
        $GLOBALS['_mysqli_connect_error'] = '';
    } catch (PDOException $e) {
        $con->connect_errno = 1;
        $con->connect_error = $e->getMessage();
        $GLOBALS['_mysqli_connect_errno'] = 1;
        $GLOBALS['_mysqli_connect_error'] = $con->connect_error;
    }

    return $con;
}

function msms_translate_sql($sql)
{
    $sql = trim($sql);

    // Backticks → double quotes (PostgreSQL identifiers)
    $sql = str_replace('`', '"', $sql);

    // INSERT IGNORE → ON CONFLICT DO NOTHING
    if (preg_match('/^INSERT\s+IGNORE\s+INTO\s+"?(\w+)"?\s*\(([^)]+)\)\s*VALUES\s*\(([^)]+)\)/i', $sql, $m)) {
        $table = $m[1];
        if (strcasecmp($table, 'tblstylist_services') === 0) {
            $sql = "INSERT INTO tblstylist_services ({$m[2]}) VALUES ({$m[3]}) ON CONFLICT (\"StylistId\", \"ServiceId\") DO NOTHING";
        } else {
            $sql = "INSERT INTO {$table} ({$m[2]}) VALUES ({$m[3]}) ON CONFLICT DO NOTHING";
        }
    }

    // SHOW TABLES LIKE 'name'
    if (preg_match('/^SHOW\s+TABLES\s+LIKE\s+[\'"](\w+)[\'"]/i', $sql, $m)) {
        return "SELECT tablename AS \"Tables_in_msmsdb\" FROM pg_catalog.pg_tables WHERE schemaname = 'public' AND tablename = '{$m[1]}'";
    }

    // SHOW COLUMNS FROM table LIKE 'col'
    if (preg_match('/^SHOW\s+COLUMNS\s+FROM\s+"?(\w+)"?\s+LIKE\s+[\'"](\w+)[\'"]/i', $sql, $m)) {
        return "SELECT column_name AS \"Field\" FROM information_schema.columns WHERE table_schema = 'public' AND table_name = '{$m[1]}' AND column_name = '{$m[2]}'";
    }

    // MySQL || as OR in WHERE (PostgreSQL uses || for string concat)
    if (preg_match('/\bWHERE\b/i', $sql) && preg_match('/\|\|/', $sql)) {
        $sql = preg_replace('/\s*\|\|\s*/', ' OR ', $sql);
    }

    // month(col) → EXTRACT(MONTH FROM col)
    $sql = preg_replace('/\bmonth\s*\(\s*([^)]+)\s*\)/i', 'EXTRACT(MONTH FROM $1)', $sql);

    // year(col) → EXTRACT(YEAR FROM col)
    $sql = preg_replace('/\byear\s*\(\s*([^)]+)\s*\)/i', 'EXTRACT(YEAR FROM $1)', $sql);

    // INSERT ... value( → VALUES(
    $sql = preg_replace('/\bvalue\s*\(/i', 'VALUES (', $sql);

    return $sql;
}

function mysqli_query($con, $sql)
{
    if (!$con instanceof MysqliCompatConnection || !$con->pdo) {
        return false;
    }

    $sql = msms_translate_sql($sql);

    try {
        $stmt = $con->pdo->query($sql);
        if ($stmt === false) {
            return false;
        }

        // Non-SELECT statements (INSERT/UPDATE/DELETE)
        if ($stmt->columnCount() === 0) {
            $con->lastInsertId = (int) $con->pdo->lastInsertId();
            return true;
        }

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return new MysqliCompatResult($rows);
    } catch (PDOException $e) {
        error_log('Supabase query error: ' . $e->getMessage() . ' | SQL: ' . $sql);
        return false;
    }
}

function mysqli_fetch_array($result)
{
    if ($result instanceof MysqliCompatResult) {
        return $result->fetchArray();
    }
    return null;
}

function mysqli_num_rows($result)
{
    if ($result instanceof MysqliCompatResult) {
        return $result->numRows();
    }
    if ($result === true) {
        return 0;
    }
    return 0;
}

function mysqli_real_escape_string($con, $string)
{
    if ($con instanceof MysqliCompatConnection && $con->pdo) {
        $q = $con->pdo->quote((string) $string);
        return substr($q, 1, -1);
    }
    return addslashes((string) $string);
}

function mysqli_insert_id($con)
{
    if ($con instanceof MysqliCompatConnection) {
        return $con->lastInsertId;
    }
    return 0;
}
