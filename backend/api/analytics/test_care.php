<?php
// test_care.php — test runner for smart_care_engine.py

header('Content-Type: application/json');

// Sample soil + weather input (adjust as needed)
$data = [
    "N" => 90,
    "P" => 40,
    "K" => 60,
    "ph" => 6.5,
    "city" => "Batangas"
];

$input = json_encode($data);

$python = "python";
$script = "C:\\xampp\\htdocs\\Agrilink\\backend\\api\\analytics\\smart_care_engine.py";

$descriptorspec = [
   0 => ["pipe", "r"],  // stdin
   1 => ["pipe", "w"],  // stdout
   2 => ["pipe", "w"]   // stderr
];

$process = proc_open([$python, $script], $descriptorspec, $pipes, __DIR__);

if (is_resource($process)) {
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $return_code = proc_close($process);

    if ($return_code !== 0) {
        echo json_encode(["error" => "Python error", "stderr" => $error]);
    } else {
        echo $output;
    }
} else {
    echo json_encode(["error" => "Failed to start Python process"]);
}
?>
