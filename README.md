# weather2mqtt
Ein kleines PHP Script um Wetterdaten auf einen MQTT Broker zu übertragen, um diese dann im Smarthome System weiterzuverwenden.

## Wetterdaten 

* Open-Meteo (https://open-meteo.com/) - Open Source Wetter-API mit freien nicht-kommerziellen Zugriff, kein API notwendig
* API Dokumentation https://open-meteo.com/en/docs#api-documentation
* Beispiel für Wetter für mitten in Berlin 

`https://api.open-meteo.com/v1/forecast?latitude=52.51&longitude=13.37&daily=weathercode,temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max,precipitation_sum,rain_sum,showers_sum,snowfall_sum,precipitation_hours,windspeed_10m_max,winddirection_10m_dominant,shortwave_radiation_sum&timezone=Europe%2FBerlin&current_weather=true` 

* die Parameter für den API-Abruf bei open-meteo in der API-Dokumentation "zusammengeklickt" werden ;-)

* Beispiel-Response

`{
  "latitude": 52.51,
  "longitude": 13.37,
  "generationtime_ms": 0.7330179214477539,
  "utc_offset_seconds": 3600,
  "timezone": "Europe/Berlin",
  "timezone_abbreviation": "CET",
  "elevation": 44,
  "current_weather": {
    "temperature": 5.3,
    "windspeed": 17.6,
    "winddirection": 191,
    "weathercode": 3,
    "time": "2023-03-12T15:00"
  },
  "daily_units": {
    "time": "iso8601",
    "weathercode": "wmo code",
    "temperature_2m_max": "°C",
    "temperature_2m_min": "°C",
    "precipitation_sum": "mm",
    "precipitation_hours": "h",
    "windspeed_10m_max": "km/h",
    "winddirection_10m_dominant": "°"
  },
  "daily": {
    "time": [
      "2023-03-12",
      "2023-03-13",
      "2023-03-14"
    ],
    "weathercode": [
      61,
      80,
      80
    ],
    "temperature_2m_max": [
      5.4,
      13.3,
      13.4
    ],
    "temperature_2m_min": [
      -1,
      4.6,
      2.5
    ],
    "precipitation_sum": [
      1.6,
      4.9,
      3.7
    ],
    "precipitation_hours": [
      6,
      8,
      5
    ],
    "windspeed_10m_max": [
      18.3,
      27.3,
      28
    ],
    "winddirection_10m_dominant": [
      223,
      209,
      237
    ]
  }
}
`
## Script

Das Script ruft folgende Wetterdaten ab

* aktuell (gerade jetzt)
  * Temperatur
  * Windgeschwindigkeit
  * Windrichtung
  * Wettercode
 
* Vorhersage (heute, morgen und übermorgen)
  * Temperatur min/max
  * Niederschlagsmenge
  * Windgeschwindigkeit und -richtung
  * Wettercode
 
* Windrichtung wird von Grad nach Windrichtung übersetzt
  * 0 = N
  * 1 = NO
  * 3 = O
  * 4 = SO
  * 5 = S
  * 6 = SW
  * 7 = W
  * 8 = NW
* Wettercode (WMO Weather interpretation codes) wird übersetzt
  * 0 = klar, Nacht
  * 1 = kalr, Tag
  * 2 = Wolken und Sonne
  * 3 = Wolken
  * 4 = Nebel, Nacht
  * 5 = Nebel, Tag
  * 6 = Regen
  * 7 = Schnee
  * 8 = Gewitter

## MQTT Topics

* die Topics beginnen mit einer eigenen ID (sollte lokal angepasst werden, damit es eindeutig bleibt)

Status

* Aktualisierungszeit __ac120002/weather/status/time__

aktuell

* Wochentag __ac120002/weather/current/weekday__
* Wettercode __ac120002/weather/current/weathercode__
* Windrichtung __ac120002/weather/current/winddirection__
* Windgeschwindigkeit __ac120002/weather/current/windspeed__
* Temperatur __ac120002/weather/current/temperature__

Vorschau - heute 

* Wochentag __ac120002/weather/today/weekday__
* Wettercode __ac120002/weather/today/weathercode__
* Niederschlagsmenge __ac120002/weather/today/precipitation__
* min. Temperatur __ac120002/weather/today/temperature_min__
* max. Temperatur __ac120002/weather/today/temperature_max__

Vorschau - morgen 

* Wochentag __ac120002/weather/tomorrow/weekday__
* Wettercode __ac120002/weather/tomorrow/weathercode__
* Niederschlagsmenge __ac120002/weather/tomorrow/precipitation__
* min. Temperatur __ac120002/weather/tomorrow/temperature_min__
* max. Temperatur __ac120002/weather/tomorrow/temperature_max__


 





