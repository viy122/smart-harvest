<?php
// PHP API endpoint to call the python engine and return JSON
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors in response, log them instead

// ✅ Absolute python executable path (Windows) - use your actual path
$python = 'C:\\Program Files\\Python310\\python.exe';

// Path to engine script
$script = __DIR__ . DIRECTORY_SEPARATOR . 'smart_care_engine.py';

// Read JSON from POST body or form-encoded fields
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!$input) {
    // Fallback to form data
    $input = [
        'N' => $_POST['N'] ?? null,
        'P' => $_POST['P'] ?? null,
        'K' => $_POST['K'] ?? null,
        'ph' => $_POST['ph'] ?? null,
        'city' => $_POST['city'] ?? null
    ];
}

// Validate inputs
$required = ['N','P','K','ph'];
foreach ($required as $r) {
    if (!isset($input[$r]) || $input[$r] === '') {
        http_response_code(400);
        echo json_encode(['error' => "Missing parameter: $r"]);
        exit;
    }
}

// Convert to floats safely
function to_float($v) {
    if (is_numeric($v)) return floatval($v);
    return null;
}
$N = to_float($input['N']);
$P = to_float($input['P']);
$K = to_float($input['K']);
$ph = to_float($input['ph']);
$city = isset($input['city']) ? $input['city'] : 'Balayan';

if ($N === null || $P === null || $K === null || $ph === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Parameters must be numeric: N,P,K,ph']);
    exit;
}

// Build payload for Python (engine expects JSON on stdin)
$payload = json_encode([
    'N' => $N,
    'P' => $P,
    'K' => $K,
    'ph' => $ph,
    'city' => $city
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// Ensure python and script exist
if (!file_exists($python)) {
    http_response_code(500);
    echo json_encode(['error' => "Python executable not found at: $python"]);
    exit;
}
if (!file_exists($script)) {
    http_response_code(500);
    echo json_encode(['error' => "Engine script not found at: $script"]);
    exit;
}

// ✅ Build command with proper escaping for Windows
$python_escaped = '"' . str_replace('"', '""', $python) . '"';
$script_escaped = '"' . str_replace('"', '""', $script) . '"';
$command = $python_escaped . ' -u ' . $script_escaped; // -u for unbuffered output

// Prepare descriptors
$descriptors = [
    0 => ["pipe", "r"], // stdin
    1 => ["pipe", "w"], // stdout
    2 => ["pipe", "w"]  // stderr
];

// ✅ Set up comprehensive environment variables for Python on Windows
$env = [];

// Critical Windows environment variables
$env['SYSTEMROOT'] = getenv('SYSTEMROOT') ?: 'C:\\Windows';
$env['SYSTEMDRIVE'] = getenv('SYSTEMDRIVE') ?: 'C:';
$env['WINDIR'] = getenv('WINDIR') ?: 'C:\\Windows';
$env['TEMP'] = getenv('TEMP') ?: 'C:\\Windows\\Temp';
$env['TMP'] = getenv('TMP') ?: 'C:\\Windows\\Temp';
$env['COMSPEC'] = getenv('COMSPEC') ?: 'C:\\Windows\\system32\\cmd.exe';

// Python-specific settings
$env['PYTHONHASHSEED'] = '0'; // Disable hash randomization
$env['PYTHONIOENCODING'] = 'utf-8';
$env['PYTHONUNBUFFERED'] = '1'; // Force unbuffered output
$env['PYTHONDONTWRITEBYTECODE'] = '1'; // Don't create .pyc files

// Path - include Python directory
$pythonDir = dirname($python);
$env['PATH'] = $pythonDir . ';' . (getenv('PATH') ?: '');

// Add OpenWeather API key
$owkey = getenv('OPENWEATHER_API_KEY');
if ($owkey !== false && strlen($owkey) > 0) {
    $env['OPENWEATHER_API_KEY'] = $owkey;
} else {
    // Fallback - you can hardcode it here for testing (remove in production)
    $env['OPENWEATHER_API_KEY'] = '4cac84b627ac52ac5a76e3b3e2349132';
}

// ✅ Set working directory to script location
$cwd = __DIR__;

// Launch process with explicit shell bypass on Windows
$proc = proc_open($command, $descriptors, $pipes, $cwd, $env, ['bypass_shell' => true]);

if (!is_resource($proc)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to start Python process', 'command' => $command]);
    exit;
}

// Write payload to python stdin
if (is_resource($pipes[0])) {
    fwrite($pipes[0], $payload);
    fclose($pipes[0]);
}

// Read stdout/stderr with better timeout handling
$stdout = '';
$stderr = '';
$timeout = 30; // 30 seconds max
$start = time();

// Set non-blocking mode
stream_set_blocking($pipes[1], false);
stream_set_blocking($pipes[2], false);

while (true) {
    // Read available data
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    
    if ($out !== false && $out !== '') $stdout .= $out;
    if ($err !== false && $err !== '') $stderr .= $err;
    
    // Check if process is still running
    $status = proc_get_status($proc);
    if (!$status['running']) {
        // Process finished, read any remaining output
        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);
        break;
    }
    
    // Check timeout
    if (time() - $start > $timeout) {
        proc_terminate($proc);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);
        
        http_response_code(500);
        echo json_encode(['error' => 'Python process timeout after ' . $timeout . ' seconds']);
        exit;
    }
    
    usleep(100000); // 0.1 second delay
}

fclose($pipes[1]);
fclose($pipes[2]);

// Get exit code
$exitCode = proc_close($proc);

// Debug logging
$debug_log = __DIR__ . DIRECTORY_SEPARATOR . 'debug_php.log';
$log_entry = 
    "[" . date('Y-m-d H:i:s') . "] === New Request ===\n" .
    "[" . date('Y-m-d H:i:s') . "] Command: $command\n" .
    "[" . date('Y-m-d H:i:s') . "] Working Dir: $cwd\n" .
    "[" . date('Y-m-d H:i:s') . "] Payload: $payload\n" .
    "[" . date('Y-m-d H:i:s') . "] Exit code: $exitCode\n" .
    "[" . date('Y-m-d H:i:s') . "] STDOUT length: " . strlen($stdout) . "\n" .
    "[" . date('Y-m-d H:i:s') . "] STDOUT: $stdout\n" .
    "[" . date('Y-m-d H:i:s') . "] STDERR length: " . strlen($stderr) . "\n" .
    "[" . date('Y-m-d H:i:s') . "] STDERR: $stderr\n" .
    "[" . date('Y-m-d H:i:s') . "] === End Request ===\n\n";
file_put_contents($debug_log, $log_entry, FILE_APPEND);

// Handle errors
if ($exitCode !== 0) {
    http_response_code(500);
    
    // Try to parse stdout as JSON error first
    $stdout_trim = trim($stdout);
    if ($stdout_trim && strpos($stdout_trim, '{') !== false) {
        $pydata = json_decode($stdout_trim, true);
        if ($pydata && isset($pydata['error'])) {
            echo json_encode($pydata);
            exit;
        }
    }
    
    $msg = 'Python process failed (exit code: ' . $exitCode . ')';
    if ($stderr) $msg .= ': ' . trim($stderr);
    
    echo json_encode([
        'error' => $msg, 
        'python_stdout' => $stdout,
        'python_stderr' => $stderr,
        'exit_code' => $exitCode,
        'debug_log' => 'Check ' . $debug_log . ' for details'
    ]);
    exit;
}

// Parse stdout JSON
$stdout_trim = trim($stdout);
if ($stdout_trim === '') {
    http_response_code(500);
    echo json_encode([
        'error' => 'Empty response from Python engine', 
        'python_stderr' => $stderr,
        'exit_code' => $exitCode,
        'debug_log' => 'Check ' . $debug_log . ' for details'
    ]);
    exit;
}

$pydata = json_decode($stdout_trim, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid JSON from Python engine: ' . json_last_error_msg(), 
        'raw_output' => substr($stdout_trim, 0, 500), // First 500 chars
        'python_stderr' => $stderr,
        'debug_log' => 'Check ' . $debug_log . ' for full output'
    ]);
    exit;
}

// Check for Python-level errors
if (isset($pydata['error'])) {
    http_response_code(500);
    echo json_encode($pydata);
    exit;
}

// Success - relay Python data
http_response_code(200);
echo json_encode($pydata);
exit;
?>
