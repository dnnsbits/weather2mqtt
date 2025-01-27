<?php
/*
https://github.com/dnnsbits/wetter2mqtt

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR 
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE 
USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

require 'config.php';

// https://github.com/php-mqtt
require('vendor/autoload.php');
use \PhpMqtt\Client\MqttClient;

echo date(DT_RFC822())." read forecast file\n";

if ($openmeteo_online) {
	$file = $openmeteo_url.$openmeteo_query;
} else {
	$file = 'forecast.json';
}

$query_time = date(DATE_ATOM);

$data = file_get_contents($file); 

if ($data == "") {
	echo date(DT_RFC822())." ERROR reading file\n";
	exit;
}

$obj = json_decode($data); 

echo date(DT_RFC822())." current weather \n";

$current_time=$obj->current_weather->time;
$current_temp=$obj->current_weather->temperature;
$current_windspeed=$obj->current_weather->windspeed;
$current_winddir=$obj->current_weather->winddirection;
$current_weathercode=$obj->current_weather->weathercode;
$current_time=$obj->current_weather->time;

echo date(DT_RFC822())." time ".$current_time."\n";
echo date(DT_RFC822())." temperature ".$current_temp."\n";
echo date(DT_RFC822())." windspeed ".$current_windspeed."\n";
echo date(DT_RFC822())." winddirection ".$current_winddir."\n";
echo date(DT_RFC822())." weathercode ".$current_weathercode."\n";
echo date(DT_RFC822())." time ".$current_time."\n";
 
echo date(DT_RFC822())." translated \n";

$current_winddir = translate_winddirection($current_winddir);
$current_weathercode = translate_weathercode($current_weathercode);
$current_time = date("w",strtotime($current_time));

echo date(DT_RFC822())." winddirection ".$current_winddir."\n";
echo date(DT_RFC822())." weathercode ".$current_weathercode."\n";
echo date(DT_RFC822())." time ".$current_time."\n";

echo date(DT_RFC822())." today's weather \n";

$today_date = $obj->daily->time[0];
$today_temp_min = $obj->daily->temperature_2m_min[0];
$today_temp_max = $obj->daily->temperature_2m_max[0];
$today_precipitation = $obj->daily->precipitation_sum[0];
$today_weather_code = $obj->daily->weathercode[0];
$today_time = $obj->daily->time[0];


echo date(DT_RFC822())." date ".$today_date."\n";
echo date(DT_RFC822())." temperature min ".$today_temp_min."\n";
echo date(DT_RFC822())." temperature max ".$today_temp_max."\n";
echo date(DT_RFC822())." precipitation ".$today_precipitation."\n";
echo date(DT_RFC822())." weathercode ".$today_weather_code."\n";
echo date(DT_RFC822())." time ".$today_time."\n";


echo date(DT_RFC822())." translated \n";

$today_weather_code = translate_weathercode($today_weather_code);
$today_time = date("w",strtotime($today_time));

echo date(DT_RFC822())." weathercode ".$today_weather_code."\n";
echo date(DT_RFC822())." time ".$today_time."\n";

echo date(DT_RFC822())." tomorrow's weather \n";

$tommorow_date = $obj->daily->time[1];
$tommorow_temp_min = $obj->daily->temperature_2m_min[1];
$tommorow_temp_max = $obj->daily->temperature_2m_max[1];
$tommorow_precipitation = $obj->daily->precipitation_sum[1];
$tommorow_weather_code = $obj->daily->weathercode[1];
$tommorow_time = $obj->daily->time[1];

echo date(DT_RFC822())." date ".$tommorow_date."\n";
echo date(DT_RFC822())." temperature min ".$tommorow_temp_min."\n";
echo date(DT_RFC822())." temperature max ".$tommorow_temp_max."\n";
echo date(DT_RFC822())." precipitation ".$tommorow_precipitation."\n";
echo date(DT_RFC822())." weathercode ".$tommorow_weather_code."\n";
echo date(DT_RFC822())." time ".$tommorow_time."\n";

echo date(DT_RFC822())." translated \n";

$tommorow_weather_code = translate_weathercode($tommorow_weather_code);
$tommorow_time = date("w",strtotime($tommorow_time));

echo date(DT_RFC822())." weathercode ".$tommorow_weather_code."\n";
echo date(DT_RFC822())." time ".$tommorow_time."\n";

if ($mqtt_publish) {

	echo date(DT_RFC822())." send mqtt message\n";

	$server   = $mqtt_server;
	$port     = $mqtt_port;
	$clientId = $mqtt_clientid; 
	$topic 	  = $mqtt_clientid;

	$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
	$mqtt->connect();

	$mqtt->publish($topic.'/weather/status/time', $query_time, 0);

	$mqtt->publish($topic.'/weather/current/temperature', $current_temp, 0);
	$mqtt->publish($topic.'/weather/current/windspeed', $current_windspeed, 0);
	$mqtt->publish($topic.'/weather/current/winddirection', $current_winddir, 0);
	$mqtt->publish($topic.'/weather/current/weathercode', $current_weathercode, 0);
	$mqtt->publish($topic.'/weather/current/weekday', $current_time, 0);

	$mqtt->publish($topic.'/weather/today/temperature_min', $today_temp_min, 0);
	$mqtt->publish($topic.'/weather/today/temperature_max', $today_temp_max, 0);
	$mqtt->publish($topic.'/weather/today/precipitation', $today_precipitation, 0);
	$mqtt->publish($topic.'/weather/today/weathercode', $today_weather_code, 0);
	$mqtt->publish($topic.'/weather/today/weekday', $today_time, 0);

	$mqtt->publish($topic.'/weather/tomorrow/temperature_min', $tommorow_temp_min, 0);
	$mqtt->publish($topic.'/weather/tomorrow/temperature_max', $tommorow_temp_max, 0);
	$mqtt->publish($topic.'/weather/tomorrow/precipitation', $tommorow_precipitation, 0);
	$mqtt->publish($topic.'/weather/tomorrow/weathercode', $tommorow_weather_code, 0);
	$mqtt->publish($topic.'/weather/tomorrow/weekday', $tommorow_time, 0);

	$mqtt->disconnect();

}
else {
	echo date(DT_RFC822())." not sent to mqtt\n";
}

echo date(DT_RFC822())." done.\n";

function DT_RFC822() {
	// date("D, d M Y H:i:s O")
	return "D, d M Y H:i:s O";
}

function translate_winddirection($winddirection) {

	/*

	0
	22,5
	45
	67,5
	90
	112,5
	135
	157,5
	180
	202,5
	225
	247,5
	270
	292,5
	315
	337,5
	360

	*/

	if ($winddirection >= 0 and $winddirection < 22.5) {
		$winddirection = 0; // N 
	}
	elseif ($winddirection >= 22.5 and $winddirection < 67.5) {
		$winddirection = 1; // NO
	}
	elseif ($winddirection >= 67.5 and $winddirection< 112.5) {
		$winddirection = 3; // O 
	}
	elseif ($winddirection >= 112.5 and $winddirection < 157.5) {
		$winddirection = 4; // SO 
	}
	elseif ($winddirection >= 157.5 and $winddirection < 202.5) {
		$winddirection = 5; // S 
	} 
	elseif ($winddirection >= 202.5 and $winddirection < 247.5) {
		$winddirection = 6; // SW 
	}
	elseif ($winddirection >= 247.5 and $winddirection < 292.5) {
		$winddirection = 7; // W 
	}
	elseif ($winddirection >= 292.5 and $winddirection < 337.5) {
		$winddirection = 8; // NW 
	}
	elseif ($winddirection >= 337.5 and $winddirection <= 360) {
		$winddirection = 0; // N 
	}	
	return $winddirection;
	
}

function translate_weathercode ($weathercode) {

	/*
	WMO Weather interpretation codes (WW)
	Code	Description
	0			Clear sky
	1, 2, 3		Mainly clear, partly cloudy, and overcast		Überwiegend klar, teilweise bewölkt und bewölkt
	45, 48		Fog and depositing rime fog						Nebel und abgelagerter Reifnebel
	51, 53, 55	Drizzle: Light, moderate, and dense intensity	Nieselregen: Leichte, mäßige und dichte Intensität
	56, 57		Freezing Drizzle: Light and dense intensity		Gefrierender Nieselregen: Leichte und dichte Intensität
	61, 63, 65	Rain: Slight, moderate and heavy intensity		Regen: Leichte, mäßige und starke Intensität
	66, 67		Freezing Rain: Light and heavy intensity		Gefrierender Regen: Leichte und starke Intensität
	71, 73, 75	Snow fall: Slight, moderate, and heavy intensitySchneefall: Leichte, mittlere und starke Intensität
	77			Snow grains										Schneekörner
	80, 81, 82	Rain showers: Slight, moderate, and violent		Regenschauer: Leicht, mäßig und heftig
	85, 86		Snow showers slight and heavy					Leichte und starke Schneeschauer
	95 *		Thunderstorm: Slight or moderate				Gewitter: Leicht oder mäßig
	96, 99 *	Thunderstorm with slight and heavy hail			Gewitter mit leichtem und schwerem Hagel

	*/

	$timestamp = time();
	$stunde = date("H", $timestamp);

	if ($weathercode == 0 and ($stunde >= 0 and $stunde <6 or $stunde > 20 and $stunde <= 24)) {
		$weathercode = 0; // klar, Mond
	}
	elseif ($weathercode == 0 and ($stunde >= 6 and $stunde <= 20)) {
		$weathercode = 1; // klar, Sonne
	}
	elseif ($weathercode == 1 or $weathercode == 2 ) {
		$current_weather = 2; // Sonne+Wolke
	}
	elseif ($weathercode == 3 ) {
		$current_weather = 3; // Wolke
	}
	elseif (($weathercode == 45 or $weathercode == 48)  and ($stunde >= 0 and $stunde <6 or $stunde > 20 and $stunde <= 24)) {
		$current_weather = 4; // Nebel+Mond
	}
	elseif (($weathercode == 45 or $weathercode == 48)  and ($stunde >= 6 and $stunde <= 20)) {
		$weathercode = 5; // Nebel+Sonne 
	}
	elseif (
	 $weathercode == 51 or $weathercode == 53 or $weathercode == 55 or 
	 $weathercode == 61 or $weathercode == 63 or $weathercode == 65 or 
	 $weathercode == 80 or $weathercode == 81 or $weathercode == 82) {
		 $weathercode = 6; // Regen 
	}
	elseif (
	 $weathercode == 56 or $weathercode == 57 or 
	 $weathercode == 66 or $weathercode == 67 or 
	 $weathercode == 71 or $weathercode == 73 or $weathercode == 75 or $weathercode == 77 or
	 $weathercode == 85 or $weathercode == 86 
	)
	{
		$weathercode = 7; // Snow
	}
	elseif (
	 $weathercode == 95 or $weathercode == 96 or $weathercode == 99
	)
	{
		$weathercode = 8; // Gewitter
	}
	return $weathercode;
}

