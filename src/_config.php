<?php
// rename the file to config.php
// adjust your settings

// MQTT 
// user/pass: null or username and password 
$mqtt_server = '192.168.1.200';
$mqtt_port = 1883;
$mqtt_user = null; // TODO needs to be implemented
$mqtt_pass = null; // TODO needs to be implemented
$mqtt_publish = true; // set false to skip publishing 
$mqtt_clientid = 'ac120002'; // own ID also used for topic

// https://open-meteo.com/
// Open-Meteo is an open-source weather API with free access for non-commercial use. No API key is required.
// https://open-meteo.com/en/docs#api-documentation

$openmeteo_online = true; // set false to read local sample forecast
$openmeteo_url = "http://api.open-meteo.com/v1/forecast";
// adjust latitude and longitude to requested location
$openmeteo_query = "?latitude=52.51&longitude=13.37&daily=weathercode,temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max,precipitation_sum,rain_sum,showers_sum,snowfall_sum,precipitation_hours,windspeed_10m_max,winddirection_10m_dominant,shortwave_radiation_sum&timezone=Europe%2FBerlin&current_weather=true";
