<?php
require_once __DIR__ . '/supabase_config.php';

class SupabaseResult {
    public $rows = [];
    public $insertId = null;
    public $affectedRows = 0;
    public $success = true;
    public $error = '';
    private $position = 0;

    public function __construct($rows = [], $insertId = null, $affectedRows = 0, $success = true, $error = '') {
        $this->rows = is_array($rows) ? $rows : [];
        foreach ($this->rows as &$row) {
            $row = self::normalizeRowKeys($row);
        }
        $this->insertId = $insertId;
        $this->affectedRows = $affectedRows;
        $this->success = $success;
        $this->error = $error;
    }

    public static function normalizeRowKeys($row) {
        if (!is_array($row)) return $row;
        // Mapping of lowercase column names from PostgreSQL to PHP case-sensitive array keys
        $mapping = [
            'id' => 'ID',
            'adminname' => 'AdminName',
            'username' => 'UserName',
            'mobilenumber' => 'MobileNumber',
            'email' => 'Email',
            'password' => 'Password',
            'adminregdate' => 'AdminRegdate',
            'aptnumber' => 'AptNumber',
            'name' => 'Name',
            'phonenumber' => 'PhoneNumber',
            'aptdate' => 'AptDate',
            'apttime' => 'AptTime',
            'services' => 'Services',
            'applydate' => 'ApplyDate',
            'remark' => 'Remark',
            'status' => 'Status',
            'stylistid' => 'StylistId',
            'stylistremark' => 'StylistRemark',
            'styliststatus' => 'StylistStatus',
            'remarkdate' => 'RemarkDate',
            'gender' => 'Gender',
            'details' => 'Details',
            'creationdate' => 'CreationDate',
            'updationdate' => 'UpdationDate',
            'userid' => 'Userid',
            'serviceid' => 'ServiceId',
            'billingid' => 'BillingId',
            'postingdate' => 'PostingDate',
            'pagetype' => 'PageType',
            'pagetitle' => 'PageTitle',
            'pagedescription' => 'PageDescription',
            'timing' => 'Timing',
            'servicename' => 'ServiceName',
            'description' => 'Description',
            'cost' => 'Cost',
            'stylistname' => 'StylistName',
            'specialty' => 'Specialty',
            'dateofsub' => 'DateofSub'
        ];
        foreach ($row as $key => $val) {
            $lowerKey = strtolower($key);
            if (isset($mapping[$lowerKey])) {
                $row[$mapping[$lowerKey]] = $val;
            }
            if ($lowerKey === 'id') {
                $row['ID'] = $val;
                $row['id'] = $val;
            }
        }
        return $row;
    }

    public function fetch_array() {
        if ($this->position < count($this->rows)) {
            $row = $this->rows[$this->position];
            $this->position++;
            return $row;
        }
        return false;
    }

    public function num_rows() {
        return count($this->rows);
    }
}

function supabase_escape($value) {
    if ($value === null) {
        return null;
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_numeric($value)) {
        return (string) $value;
    }
    return trim((string) $value);
}

function supabase_http_request($method, $path, $payload = [], $headers = []) {
    $url = rtrim(SUPABASE_URL, '/') . '/rest/v1/' . ltrim($path, '/');
    $ch = curl_init($url);

    $requestHeaders = [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    if (!empty($headers)) {
        foreach ($headers as $key => $value) {
            $requestHeaders[] = $key . ': ' . $value;
        }
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    // Disable SSL verification for local development compatibility (fixes Windows CA issuer issues)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if ($method !== 'GET' && $method !== 'DELETE') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    if ($method === 'DELETE' && !empty($payload)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['success' => false, 'error' => $err, 'status' => 0];
    }

    $decoded = json_decode($response, true);
    if ($status >= 400) {
        $message = is_array($decoded) && isset($decoded['message']) ? $decoded['message'] : $response;
        return ['success' => false, 'error' => $message, 'status' => $status];
    }

    return ['success' => true, 'data' => $decoded, 'status' => $status];
}

function supabase_build_query_string($params) {
    if (empty($params)) {
        return '';
    }

    $parts = [];
    foreach ($params as $key => $value) {
        if ($value === null) {
            continue;
        }
        if (is_array($value)) {
            $value = implode(',', array_map('strval', $value));
        }
        $parts[] = urlencode($key) . '=' . urlencode((string) $value);
    }

    return implode('&', $parts);
}

function supabase_select($table, $columns = ['*'], $where = [], $orderBy = null, $limit = null, $offset = 0) {
    $table = strtolower($table);
    $columns = array_map('strtolower', $columns);

    $path = $table . '?';
    if ($columns !== ['*']) {
        $cleanColumns = [];
        foreach ($columns as $col) {
            $parts = explode('.', $col);
            $cleanColumns[] = strtolower(end($parts));
        }
        $path = $table . '?select=' . implode(',', array_map('urlencode', $cleanColumns)) . '&';
    } else {
        $path = $table . '?select=*';
    }

    $params = [];
    if (!empty($where)) {
        foreach ($where as $column => $value) {
            $column = strtolower($column);
            if (is_array($value)) {
                $params[] = $column . '=in.(' . implode(',', array_map('urlencode', array_map('strval', $value))) . ')';
            } else {
                $params[] = $column . '=eq.' . urlencode((string) $value);
            }
        }
    }

    if ($orderBy) {
        $orderBy = trim(strtolower($orderBy));
        $parts = explode('.', $orderBy);
        $cleanOrderBy = end($parts);
        $cleanOrderBy = preg_replace('/\s+/', '.', $cleanOrderBy);
        $params[] = 'order=' . urlencode($cleanOrderBy);
    }
    if ($limit !== null) {
        $params[] = 'limit=' . (int) $limit;
    }
    if ($offset > 0) {
        $params[] = 'offset=' . (int) $offset;
    }

    $query = $path;
    if (!empty($params)) {
        $query .= (strpos($path, '?') === false ? '?' : '') . implode('&', $params);
    }

    $query = str_replace('?&', '?', $query);
    $query = rtrim($query, '&?');

    $result = supabase_http_request('GET', $query);
    if (!$result['success']) {
        return new SupabaseResult([], null, 0, false, $result['error']);
    }
    return new SupabaseResult($result['data'] ?? []);
}

function supabase_insert($table, $data) {
    $table = strtolower($table);
    $lowercasedData = [];
    foreach ($data as $key => $value) {
        $parts = explode('.', $key);
        $lowercasedData[strtolower(end($parts))] = $value;
    }

    $result = supabase_http_request('POST', $table, $lowercasedData, ['Prefer' => 'return=representation']);
    if (!$result['success']) {
        return new SupabaseResult([], null, 0, false, $result['error']);
    }
    $rows = $result['data'] ?? [];
    $insertId = null;
    if (!empty($rows) && is_array($rows[0] ?? null)) {
        $firstRow = $rows[0];
        $insertId = $firstRow['id'] ?? $firstRow['ID'] ?? null;
    }
    $GLOBALS['supabase_last_insert_id'] = $insertId;
    return new SupabaseResult($rows, $insertId, count($rows));
}

function supabase_update($table, $data, $where = []) {
    $table = strtolower($table);
    $lowercasedData = [];
    foreach ($data as $key => $value) {
        $parts = explode('.', $key);
        $lowercasedData[strtolower(end($parts))] = $value;
    }

    $path = $table;
    if (!empty($where)) {
        $filters = [];
        foreach ($where as $column => $value) {
            $parts = explode('.', $column);
            $column = strtolower(end($parts));
            if (is_array($value)) {
                $filters[] = $column . '=in.(' . implode(',', array_map('urlencode', array_map('strval', $value))) . ')';
            } else {
                $filters[] = $column . '=eq.' . urlencode((string) $value);
            }
        }
        $path .= '?' . implode('&', $filters);
    }

    $result = supabase_http_request('PATCH', $path, $lowercasedData);
    if (!$result['success']) {
        return new SupabaseResult([], null, 0, false, $result['error']);
    }
    return new SupabaseResult($result['data'] ?? [], null, count($result['data'] ?? []));
}

function supabase_delete($table, $where = []) {
    $table = strtolower($table);
    $path = $table;
    if (!empty($where)) {
        $filters = [];
        foreach ($where as $column => $value) {
            $parts = explode('.', $column);
            $column = strtolower(end($parts));
            if (is_array($value)) {
                $filters[] = $column . '=in.(' . implode(',', array_map('urlencode', array_map('strval', $value))) . ')';
            } else {
                $filters[] = $column . '=eq.' . urlencode((string) $value);
            }
        }
        $path .= '?' . implode('&', $filters);
    }

    $result = supabase_http_request('DELETE', $path);
    if (!$result['success']) {
        return new SupabaseResult([], null, 0, false, $result['error']);
    }
    return new SupabaseResult($result['data'] ?? [], null, count($result['data'] ?? []));
}

function supabase_fetch_array($result) {
    if ($result instanceof SupabaseResult) {
        return $result->fetch_array();
    }
    if (is_array($result)) {
        $row = current($result);
        next($result);
        return $row;
    }
    return false;
}

function supabase_num_rows($result) {
    if ($result instanceof SupabaseResult) {
        return $result->num_rows();
    }
    if (is_array($result)) {
        return count($result);
    }
    return 0;
}

function supabase_insert_id($resource = null) {
    return $GLOBALS['supabase_last_insert_id'] ?? null;
}

function parse_where_clause($whereClause) {
    if ($whereClause === '') {
        return [];
    }

    $whereClause = trim($whereClause);
    $whereClause = preg_replace('/^\((.*)\)$/s', '$1', $whereClause);

    $segments = preg_split('/\s+(AND|OR)\s+/i', $whereClause, -1, PREG_SPLIT_DELIM_CAPTURE);

    $parts = [];
    for ($i = 0; $i < count($segments); $i++) {
        $segment = trim($segments[$i]);
        if ($segment === '') {
            continue;
        }
        if (strtoupper($segment) === 'AND' || strtoupper($segment) === 'OR') {
            continue;
        }
        $parts[] = ['type' => 'and', 'condition' => $segment];
    }
    return $parts;
}

function apply_where_conditions($rows, $whereClause) {
    if ($whereClause === '' || $whereClause === null) {
        return $rows;
    }

    $conditions = parse_where_clause($whereClause);
    if (empty($conditions)) {
        return $rows;
    }

    $filtered = [];
    foreach ($rows as $row) {
        $matched = true;
        foreach ($conditions as $cond) {
            if (!match_where_condition($row, $cond['condition'])) {
                $matched = false;
                break;
            }
        }
        if ($matched) {
            $filtered[] = $row;
        }
    }
    return $filtered;
}

function match_where_condition($row, $condition) {
    $condition = trim($condition);

    if (preg_match('/^\((.*)\)$/s', $condition, $matches)) {
        $condition = trim($matches[1]);
    }

    if (preg_match('/^([A-Za-z0-9_.]+)\s*(=|!=|<=|>=|<|>)\s*(.+)$/', $condition, $m)) {
        $column = trim($m[1]);
        $parts = explode('.', $column);
        $column = end($parts); // Strip table prefix e.g. tblcustomers.ID -> ID
        
        $operator = $m[2];
        $value = trim($m[3]);
        $value = trim($value, "'\"");
        
        // Match both lowercase and normalized keys in the row
        $actual = isset($row[$column]) ? $row[$column] : (isset($row[strtolower($column)]) ? $row[strtolower($column)] : null);
        
        $actualTrimmed = is_string($actual) ? trim($actual) : $actual;
        $valueTrimmed = is_string($value) ? trim($value) : $value;
        
        if ($operator === '=') {
            return (string) $actualTrimmed === (string) $valueTrimmed;
        }
        if ($operator === '!=') {
            return (string) $actualTrimmed !== (string) $valueTrimmed;
        }
        if ($operator === '>') {
            return (float) $actualTrimmed > (float) $valueTrimmed;
        }
        if ($operator === '<') {
            return (float) $actualTrimmed < (float) $valueTrimmed;
        }
        if ($operator === '>=') {
            return (float) $actualTrimmed >= (float) $valueTrimmed;
        }
        if ($operator === '<=') {
            return (float) $actualTrimmed <= (float) $valueTrimmed;
        }
    }

    if (preg_match('/^([A-Za-z0-9_.]+)\s+IS\s+NULL$/i', $condition, $m)) {
        $column = trim($m[1]);
        $parts = explode('.', $column);
        $column = end($parts);
        $actual = isset($row[$column]) ? $row[$column] : (isset($row[strtolower($column)]) ? $row[strtolower($column)] : null);
        return $actual === null || $actual === '';
    }

    if (preg_match('/^([A-Za-z0-9_.]+)\s+IS\s+NOT\s+NULL$/i', $condition, $m)) {
        $column = trim($m[1]);
        $parts = explode('.', $column);
        $column = end($parts);
        $actual = isset($row[$column]) ? $row[$column] : (isset($row[strtolower($column)]) ? $row[strtolower($column)] : null);
        return $actual !== null && $actual !== '';
    }

    return true;
}

// --------------------------------------------------------
// Custom JOIN resolver functions for Supabase HTTP interface
// --------------------------------------------------------

function handle_join_invoice_customer($sql) {
    $billingId = null;
    $userId = null;
    if (preg_match('/BillingId\s*=\s*\'?(\d+)\'?/i', $sql, $m)) {
        $billingId = $m[1];
    }
    if (preg_match('/Userid\s*=\s*\'?(\d+)\'?/i', $sql, $m)) {
        $userId = $m[1];
    }

    $invWhere = ['billingid' => $billingId];
    if ($userId) {
        $invWhere['userid'] = $userId;
    }
    
    $invRes = supabase_select('tblinvoice', ['*'], $invWhere, null, 1);
    if (!$invRes->success || empty($invRes->rows)) {
        return new SupabaseResult([]);
    }
    $invoice = $invRes->rows[0];

    $custRes = supabase_select('tblcustomers', ['*'], ['id' => $invoice['userid']], null, 1);
    if (!$custRes->success || empty($custRes->rows)) {
        return new SupabaseResult([$invoice]);
    }
    $customer = $custRes->rows[0];

    $merged = array_merge($invoice, $customer);
    return new SupabaseResult([$merged]);
}

function handle_join_invoice_services($sql) {
    $billingId = null;
    $userId = null;
    if (preg_match('/BillingId\s*=\s*\'?(\d+)\'?/i', $sql, $m)) {
        $billingId = $m[1];
    }
    if (preg_match('/Userid\s*=\s*\'?(\d+)\'?/i', $sql, $m)) {
        $userId = $m[1];
    }

    $invWhere = ['billingid' => $billingId];
    if ($userId) {
        $invWhere['userid'] = $userId;
    }

    $invRes = supabase_select('tblinvoice', ['*'], $invWhere);
    if (!$invRes->success || empty($invRes->rows)) {
        return new SupabaseResult([]);
    }

    $serviceIds = [];
    foreach ($invRes->rows as $row) {
        if (!empty($row['serviceid'])) {
            $serviceIds[] = $row['serviceid'];
        }
    }

    if (empty($serviceIds)) {
        return new SupabaseResult([]);
    }

    $srvRes = supabase_select('tblservices', ['*'], ['id' => $serviceIds]);
    return $srvRes;
}

function handle_join_invoices_customers_list($sql) {
    $invRes = supabase_select('tblinvoice', ['*']);
    if (!$invRes->success || empty($invRes->rows)) {
        return new SupabaseResult([]);
    }

    $custRes = supabase_select('tblcustomers', ['*']);
    $customers = [];
    if ($custRes->success) {
        foreach ($custRes->rows as $c) {
            $customers[$c['id']] = $c;
        }
    }

    $billingLike = null;
    if (preg_match('/BillingId\s+like\s+\'\%(.*?)\%\'/i', $sql, $m)) {
        $billingLike = $m[1];
    }

    $fdate = null;
    $tdate = null;
    if (preg_match('/between\s*\'([^\']+)\'\s*and\s*\'([^\']+)\'/i', $sql, $m)) {
        $fdate = $m[1];
        $tdate = $m[2];
    }

    $results = [];
    $seenBillings = [];

    foreach ($invRes->rows as $inv) {
        $bId = $inv['billingid'];
        if (isset($seenBillings[$bId])) {
            continue;
        }

        if ($billingLike !== null && strpos((string)$bId, $billingLike) === false) {
            continue;
        }

        if ($fdate && $tdate) {
            $postTime = strtotime($inv['postingdate']);
            $fTime = strtotime($fdate . ' 00:00:00');
            $tTime = strtotime($tdate . ' 23:59:59');
            if ($postTime < $fTime || $postTime > $tTime) {
                continue;
            }
        }

        $cName = 'Unknown';
        if (isset($customers[$inv['userid']])) {
            $cName = $customers[$inv['userid']]['name'];
        }

        $results[] = [
            'id' => $inv['id'],
            'Name' => $cName,
            'BillingId' => $bId,
            'PostingDate' => $inv['postingdate']
        ];

        $seenBillings[$bId] = true;
    }

    if (stripos($sql, 'order by') !== false) {
        usort($results, function($a, $b) {
            return $b['id'] - $a['id'];
        });
    }

    return new SupabaseResult($results);
}

function handle_join_sales_reports($sql) {
    $fdate = null;
    $tdate = null;
    if (preg_match('/between\s*\'([^\']+)\'\s*and\s*\'([^\']+)\'/i', $sql, $m)) {
        $fdate = $m[1];
        $tdate = $m[2];
    }

    $invRes = supabase_select('tblinvoice', ['*']);
    if (!$invRes->success || empty($invRes->rows)) {
        return new SupabaseResult([]);
    }

    $srvRes = supabase_select('tblservices', ['*']);
    $services = [];
    if ($srvRes->success) {
        foreach ($srvRes->rows as $s) {
            $services[$s['id']] = $s;
        }
    }

    $fTime = $fdate ? strtotime($fdate . ' 00:00:00') : 0;
    $tTime = $tdate ? strtotime($tdate . ' 23:59:59') : PHP_INT_MAX;

    $grouped = [];
    $isMonthly = (stripos($sql, 'lmonth') !== false);

    foreach ($invRes->rows as $inv) {
        $postTime = strtotime($inv['postingdate']);
        if ($postTime < $fTime || $postTime > $tTime) {
            continue;
        }

        $serviceId = $inv['serviceid'];
        $cost = isset($services[$serviceId]) ? intval($services[$serviceId]['cost']) : 0;

        $year = date('Y', $postTime);
        $month = date('n', $postTime);

        if ($isMonthly) {
            $key = $year . '-' . $month;
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['lmonth' => $month, 'lyear' => $year, 'totalprice' => 0];
            }
            $grouped[$key]['totalprice'] += $cost;
        } else {
            $key = $year;
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['lyear' => $year, 'totalprice' => 0];
            }
            $grouped[$key]['totalprice'] += $cost;
        }
    }

    return new SupabaseResult(array_values($grouped));
}

function handle_join_stylists_for_service($sql) {
    $serviceName = null;
    if (preg_match('/ServiceName\s*=\s*\'([^\']+)\'/i', $sql, $m)) {
        $serviceName = $m[1];
    }

    if (!$serviceName) {
        return new SupabaseResult([]);
    }

    $srvRes = supabase_select('tblservices', ['*'], ['servicename' => $serviceName]);
    if (!$srvRes->success || empty($srvRes->rows)) {
        return new SupabaseResult([]);
    }
    $serviceId = $srvRes->rows[0]['id'];

    $mapRes = supabase_select('tblstylist_services', ['*'], ['serviceid' => $serviceId]);
    if (!$mapRes->success || empty($mapRes->rows)) {
        return new SupabaseResult([]);
    }

    $stylistIds = [];
    foreach ($mapRes->rows as $r) {
        $stylistIds[] = $r['stylistid'];
    }

    $styRes = supabase_select('tblstylists', ['*'], ['id' => $stylistIds]);
    if (!$styRes->success || empty($styRes->rows)) {
        return new SupabaseResult([]);
    }

    $rows = $styRes->rows;
    usort($rows, function($a, $b) {
        return strcasecmp($a['stylistname'] ?? '', $b['stylistname'] ?? '');
    });

    return new SupabaseResult($rows);
}

function handle_join_services_for_stylist($sql) {
    $stylistId = null;
    if (preg_match('/StylistId\s*=\s*\'?(\d+)\'?/i', $sql, $m)) {
        $stylistId = $m[1];
    }

    if (!$stylistId) {
        return new SupabaseResult([]);
    }

    $mapRes = supabase_select('tblstylist_services', ['*'], ['stylistid' => $stylistId]);
    if (!$mapRes->success || empty($mapRes->rows)) {
        return new SupabaseResult([]);
    }

    $serviceIds = [];
    foreach ($mapRes->rows as $r) {
        $serviceIds[] = $r['serviceid'];
    }

    $srvRes = supabase_select('tblservices', ['*'], ['id' => $serviceIds]);
    if (!$srvRes->success || empty($srvRes->rows)) {
        return new SupabaseResult([]);
    }

    $rows = $srvRes->rows;
    usort($rows, function($a, $b) {
        return strcasecmp($a['servicename'] ?? '', $b['servicename'] ?? '');
    });

    return new SupabaseResult($rows);
}

// --------------------------------------------------------
// SQL to API Router
// --------------------------------------------------------

function supabase_query($sql) {
    $sql = trim($sql);

    // Route SQL JOIN statements to dedicated in-memory resolvers
    if (stripos($sql, 'JOIN') !== false) {
        if (stripos($sql, 'tblcustomers') !== false && stripos($sql, 'tblinvoice') !== false) {
            if (stripos($sql, 'distinct') !== false || stripos($sql, 'tblcustomers.Name,tblinvoice.BillingId') !== false) {
                return handle_join_invoices_customers_list($sql);
            } else {
                return handle_join_invoice_customer($sql);
            }
        }

        if (stripos($sql, 'tblinvoice') !== false && stripos($sql, 'tblservices') !== false) {
            if (stripos($sql, 'sum(Cost)') !== false || stripos($sql, 'totalprice') !== false) {
                return handle_join_sales_reports($sql);
            } else {
                return handle_join_invoice_services($sql);
            }
        }

        if (stripos($sql, 'tblstylist_services') !== false) {
            if (stripos($sql, 'tblstylists') !== false && stripos($sql, 'tblservices') !== false) {
                return handle_join_stylists_for_service($sql);
            } else {
                return handle_join_services_for_stylist($sql);
            }
        }
    }

    // Standard SELECT query routing
    if (preg_match('/^SELECT\s+(DISTINCT\s+)?(.+?)\s+FROM\s+([A-Za-z0-9_.]+)(.*)$/i', $sql, $m)) {
        $columns = preg_replace('/\s+/', ' ', trim($m[2]));
        $tableParts = explode('.', $m[3]);
        $table = end($tableParts);
        $rest = trim($m[4]);
        
        $whereClause = '';
        $orderBy = null;
        $limit = null;

        if (preg_match('/\bWHERE\b(.+?)(?=\bORDER\s+BY\b|\bLIMIT\b|$)/is', $rest, $whereMatch)) {
            $whereClause = trim($whereMatch[1]);
        }
        if (preg_match('/\bORDER\s+BY\b(.+?)(?=\bLIMIT\b|$)/is', $rest, $orderMatch)) {
            $orderBy = trim($orderMatch[1]);
        }
        if (preg_match('/\bLIMIT\s+(\d+)/i', $rest, $limitMatch)) {
            $limit = (int) $limitMatch[1];
        }

        $result = supabase_select($table, $columns === '*' ? ['*'] : array_map('trim', explode(',', $columns)), [], $orderBy, $limit);
        $rows = $result->rows;
        if ($whereClause !== '') {
            $rows = apply_where_conditions($rows, $whereClause);
        }
        return new SupabaseResult($rows);
    }

    // Standard INSERT query routing
    if (preg_match('/^INSERT\s+INTO\s+([A-Za-z0-9_.]+)\s*\((.*?)\)\s*VALUES\s*\((.*?)\)$/is', $sql, $m)) {
        $tableParts = explode('.', $m[1]);
        $table = end($tableParts);
        $columns = array_map('trim', explode(',', $m[2]));
        $values = array_map('trim', explode(',', $m[3]));
        $data = [];
        foreach ($columns as $i => $column) {
            $column = trim($column, '"`');
            $value = trim($values[$i] ?? '', "'\"");
            $data[$column] = $value;
        }
        return supabase_insert($table, $data);
    }

    // Standard UPDATE query routing
    if (preg_match('/^UPDATE\s+([A-Za-z0-9_.]+)\s+SET\s+(.+)\s+WHERE\s+(.+)$/is', $sql, $m)) {
        $tableParts = explode('.', $m[1]);
        $table = end($tableParts);
        $setClause = $m[2];
        $whereClause = $m[3];
        $data = [];
        
        foreach (explode(',', $setClause) as $pair) {
            $parts = explode('=', trim($pair), 2);
            if (count($parts) !== 2) {
                continue;
            }
            $column = trim($parts[0], '"` ');
            $value = trim($parts[1], "'\"");
            $data[$column] = $value;
        }
        
        $where = [];
        if (preg_match('/^([A-Za-z0-9_.]+)\s*(=|!=|<=|>=|<|>)\s*(.+)$/', $whereClause, $whereParts)) {
            $colName = trim($whereParts[1]);
            $colParts = explode('.', $colName);
            $where[end($colParts)] = trim($whereParts[3], "'\"");
        }
        return supabase_update($table, $data, $where);
    }

    // Standard DELETE query routing
    if (preg_match('/^DELETE\s+FROM\s+([A-Za-z0-9_.]+)(?:\s+WHERE\s+(.+))?$/is', $sql, $m)) {
        $tableParts = explode('.', $m[1]);
        $table = end($tableParts);
        $whereClause = $m[2] ?? '';
        $where = [];
        
        if ($whereClause !== '') {
            if (preg_match('/^([A-Za-z0-9_.]+)\s*(=|!=|<=|>=|<|>)\s*(.+)$/', $whereClause, $whereParts)) {
                $colName = trim($whereParts[1]);
                $colParts = explode('.', $colName);
                $where[end($colParts)] = trim($whereParts[3], "'\"");
            }
        }
        return supabase_delete($table, $where);
    }

    return new SupabaseResult([], null, 0, false, 'Unsupported SQL statement');
}

$con = true;
$GLOBALS['supabase_last_insert_id'] = null;

function supabase_connect() {
    return true;
}

supabase_connect();

$GLOBALS['supabase_connection'] = true;

if (!function_exists('db_query')) {
    function db_query($sql) {
        return supabase_query($sql);
    }
}

if (!function_exists('db_fetch_array')) {
    function db_fetch_array($result) {
        return supabase_fetch_array($result);
    }
}

if (!function_exists('db_num_rows')) {
    function db_num_rows($result) {
        return supabase_num_rows($result);
    }
}

if (!function_exists('db_real_escape_string')) {
    function db_real_escape_string($string) {
        return supabase_escape($string);
    }
}

if (!function_exists('db_insert_id')) {
    function db_insert_id() {
        return supabase_insert_id();
    }
}
?>
