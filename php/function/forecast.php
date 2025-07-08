<?php
function getForecast($city, $apikey) {
    $url = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$apikey&units=metric&lang=id";
    $json = file_get_contents($url);
    return json_decode($json, true);
}
?>