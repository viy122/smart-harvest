<?php
// backend/api/get_weather.php
header('Content-Type: application/json');

// Include your API key
$API_KEY = "4cac84b627ac52ac5a76e3b3e2349132";

// Get coordinates (if not provided, use default location)
$lat = isset($_GET['lat']) ? $_GET['lat'] : '13.9449'; // default: Batangas area
$lon = isset($_GET['lon']) ? $_GET['lon'] : '120.7517';

// API endpoint for current weather
$url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$API_KEY&units=metric";

// Fetch from OpenWeather
$response = @file_get_contents($url);

if ($response === FALSE) {
    echo json_encode(["error" => "Failed to fetch weather data."]);
    exit;
}

// Decode response
$data = json_decode($response, true);

// Extract important info
$weatherInfo = [
    "location" => $data['name'] ?? "Unknown",
    "temperature" => $data['main']['temp'] ?? null,
    "humidity" => $data['main']['humidity'] ?? null,
    "description" => $data['weather'][0]['description'] ?? "No data",
    "rain" => isset($data['rain']['1h']) ? "yes" : "no",
    "timestamp" => date("Y-m-d H:i:s")
];

// Return as JSON
echo json_encode($weatherInfo, JSON_PRETTY_PRINT);
?>
