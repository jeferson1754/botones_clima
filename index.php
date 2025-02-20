<?php
if (isset($_POST['get_weather'])) {

    // Definir la ciudad y la URL de la API
    $city = $_POST['city'] ?? 'Santiago';
    $api_key = "410f454b9ebb63e2a46e6dc2aa04554e";
    $api_url = "https://api.weatherstack.com/current?access_key=$api_key&query=$city";

    // Obtener los datos JSON de la API
    $weather_data = json_decode(file_get_contents($api_url), true);

    // Extraer la información de la temperatura, el icono y la descripción
    $temperature = $weather_data['current']['temperature'] ?? 'N/A';
    $weather_icon = $weather_data['current']['weather_icons'][0] ?? '';
    $weather_description = $weather_data['current']['weather_descriptions'][0] ?? 'No disponible';

    // Obtener la hora actual de la API o asignar valor predeterminado
    $hora_actual = $weather_data['location']['localtime'] ?? '00-00-0000 00:00';

    // Crear objeto DateTime desde el formato adecuado
    $date = DateTime::createFromFormat('Y-m-d H:i', $hora_actual);

    // Verificar si la fecha fue creada correctamente
    if ($date) {
        // Hora actual en formato 24h
        $current_time = $date->format('H:i');

        // Fecha formateada en formato 12h
        $formatted_date = $date->format('d-m-Y h:i A');
    } else {
        // Si la fecha no es válida, asignar valores predeterminados
        $current_time = '00:00';
        $formatted_date = '00-00-0000 00:00 AM';
    }

    // Definir los rangos de tiempo
    $time_ranges = [
        "06:00-12:00" => "sunrise-button",
        "12:00-18:00" => "day-button",
        "18:00-20:00" => "sunset-button",
        "20:00-06:00" => "night-button"
    ];

    // Función para verificar si la hora actual está dentro de un rango específico
    function isTimeInRange($current_time, $range)
    {
        list($start, $end) = explode("-", $range);

        // Convertir horas a minutos para comparación más fácil
        $start_minutes = strtotime($start);
        $end_minutes = strtotime($end);
        $current_minutes = strtotime($current_time);

        // Manejo de los rangos que cruzan medianoche (20:00-06:00)
        if ($start_minutes > $end_minutes) {
            return ($current_minutes >= $start_minutes || $current_minutes < $end_minutes);
        } else {
            return ($current_minutes >= $start_minutes && $current_minutes < $end_minutes);
        }
    }

    // Verificar cuál es el rango actual
    $current_button = '';
    foreach ($time_ranges as $range => $button_class) {
        if (isTimeInRange($current_time, $range)) {
            $current_button = $button_class;
            break;
        }
    }



    // Mapa de traducciones
    $translations = [
        'Sunny' => 'Soleado',  // Despejado, con sol directo. Temperatura alta, típicamente entre 25°C y 35°C.
        'Partly cloudy' => 'Parcialmente nublado',  // Algunas nubes, pero con sol. Temperatura moderada, generalmente entre 18°C y 28°C.
        'Cloudy' => 'Nublado',  // El cielo está completamente cubierto de nubes. Temperatura moderada, usualmente entre 15°C y 25°C.
        'Rainy' => 'Lluvia',  // Precipitación continua. Temperatura variable, entre 10°C y 20°C.
        'Windy' => 'Viento',  // Condiciones ventosas, sin necesariamente lluvia o sol. Temperatura entre 10°C y 20°C.
        'Clear' => 'Despejado',  // Sin nubes, con cielo completamente visible. Temperatura generalmente entre 20°C y 30°C.
        'Overcast' => 'Cubierto',  // Cielo completamente gris, sin sol visible. Temperatura entre 15°C y 22°C.
        'Showers' => 'Chubascos',  // Lluvias breves pero intensas. Temperatura generalmente entre 10°C y 20°C.
        'Thunderstorm' => 'Tormenta eléctrica',  // Lluvias con rayos y truenos. Temperatura entre 15°C y 30°C.
        'Mist' => 'Niebla',  // Visibilidad reducida por niebla densa. Temperatura entre 5°C y 15°C.
        'Fog' => 'Niebla espesa',  // Niebla densa que reduce visibilidad. Temperatura entre 5°C y 15°C.
        'Snow' => 'Nieve',  // Precipitación en forma de nieve. Temperatura generalmente entre -5°C y 5°C.
        'Hail' => 'Granizo',  // Lluvia con granizo (bultos de hielo). Temperatura generalmente entre 0°C y 10°C.
        'Patches Of Fog' => 'Parche de niebla',  // Niebla en áreas limitadas. Temperatura entre 0°C y 15°C.
        'Blizzard' => 'Tormenta de nieve',  // Nieve fuerte acompañada de vientos intensos. Temperatura entre -10°C y -20°C.
        'Sleet' => 'Aguacero congelado',  // Lluvia congelada o nieve derretida que se congela al caer. Temperatura entre -1°C y 2°C.
        'Drizzle' => 'Llovizna',  // Lluvia ligera y continua. Temperatura entre 5°C y 15°C.
        'Freezing rain' => 'Lluvia helada',  // Lluvia que se congela al tocar superficies frías. Temperatura entre 0°C y 5°C.
        'Tornado' => 'Tornado',  // Viento extremadamente fuerte y en espiral. Temperatura variable, entre 15°C y 30°C.
        'Hurricane' => 'Huracán',  // Tormenta tropical con vientos muy fuertes. Temperatura entre 20°C y 30°C.
        'Drought' => 'Sequía',  // Falta de lluvias durante un largo período. Temperatura muy alta, entre 30°C y 40°C.
        'Dust' => 'Polvo',  // Tormentas de polvo, normalmente en áreas áridas. Temperatura entre 20°C y 35°C.
        'Sandstorm' => 'Tormenta de arena',  // Tormentas con partículas de arena suspendidas. Temperatura entre 20°C y 40°C.
        'Ice' => 'Hielo',  // Temperaturas muy frías con superficies congeladas. Temperatura entre -10°C y -5°C.
        'Squall' => 'Ráfaga',  // Viento fuerte y repentino con lluvia o nieve ligera. Temperatura entre 5°C y 15°C.
        'Tropical storm' => 'Tormenta tropical',  // Tormenta en áreas tropicales, con lluvias intensas. Temperatura entre 25°C y 30°C.
        'Cold' => 'Frío',  // Temperatura baja, generalmente por debajo de los 10°C.
        'Hot' => 'Caluroso',  // Temperatura alta, generalmente por encima de los 30°C.
        'Storm' => 'Tormenta',  // Condiciones meteorológicas extremas, incluyendo lluvia, viento y relámpagos. Temperatura variable.
        'Heatwave' => 'Ola de calor'  // Período de calor extremo. Temperatura generalmente por encima de los 35°C.
    ];



    // Traducir la descripción
    $weather_description_es = $translations[$weather_description] ?? $weather_description;  // Si no está en el mapa, se mantiene la descripción original

    $weather_buttons = [
        "Soleado" => "sunny-button",
        "Lluvia" => "rainy-button",
        "Nublado" => "cloudy-button",
        "Nieve" => "snowy-button"
    ];

    // Determinar qué botón se debe mostrar
    $current_button_class = isset($weather_buttons[$weather_description_es]) ? $weather_buttons[$weather_description_es] : null;

    $slider_active = $current_button_class;
} else {
    $slider_active = "";
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hora y Clima</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #e0e6ed;
            padding-bottom: 10px;
            margin-top: 0;
        }

        .weather-form {
            margin-top: 20px;
        }

        .weather-input {
            display: flex;
            gap: 10px;
            max-width: 500px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .weather-results {
            margin-top: 30px;
        }

        .sunrise-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        .sunrise-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top,
                    #ff7e00,
                    /* Naranja intenso */
                    #ffb86c,
                    /* Naranja claro */
                    #ffd89b,
                    /* Amarillo pálido */
                    #b993d6,
                    /* Violeta */
                    #8ca6db
                    /* Azul claro */
                );
            z-index: -1;
        }

        .sunrise-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .sun {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: #ffe600;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
            box-shadow: 0 0 20px 5px rgba(255, 230, 0, 0.5);
        }

        .day-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #333;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.7);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }

        .day-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    #87CEEB,
                    /* Azul cielo claro */
                    #B5E8FF
                    /* Azul cielo más claro */
                );
            z-index: -1;
        }

        .day-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .sun-day {
            display: inline-block;
            width: 24px;
            height: 24px;
            background: #FFD700;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
            box-shadow: 0 0 15px 5px rgba(255, 215, 0, 0.6);
        }

        .cloud {
            position: absolute;
            background: white;
            border-radius: 10px;
            opacity: 0.9;
        }

        .cloud-1 {
            width: 60px;
            height: 12px;
            top: 10px;
            right: 20px;
        }

        .cloud-2 {
            width: 40px;
            height: 8px;
            bottom: 15px;
            right: 30px;
        }

        .sunset-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        .sunset-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    rgb(147, 125, 162),
                    /* Índigo - parte superior del cielo */
                    #9370DB,
                    /* Violeta medio */
                    #FF8C00,
                    /* Naranja oscuro */
                    #FF4500
                    /* Rojo-naranja intenso - horizonte */
                );
            z-index: -1;
        }

        .sunset-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .sunset-sun {
            display: inline-block;
            width: 22px;
            height: 22px;
            background: linear-gradient(to bottom, #FF8C00, #FF4500);
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
            box-shadow: 0 0 10px 2px rgba(255, 69, 0, 0.6);
            position: relative;
            top: -2px;
        }

        .horizon {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(to top,
                    rgba(0, 0, 0, 0.5),
                    transparent);
            z-index: -1;
        }

        .button-container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }

        .time-label {
            margin-bottom: 10px;
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #444;
        }

        .night-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #E6E6FA;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.5);
            transform: translateY(-3px);
        }

        .night-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    #0C0032,
                    /* Azul noche profundo */
                    #190A45,
                    /* Índigo oscuro */
                    #282A75
                    /* Azul marino oscuro */
                );
            z-index: -1;
        }

        .night-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.4);
        }

        .moon {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: #E6E6FA;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
            box-shadow: 0 0 10px 3px rgba(230, 230, 250, 0.4);
            position: relative;
        }

        .moon:before {
            content: '';
            position: absolute;
            top: 2px;
            right: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #0C0032;
            transform: translateX(3px) translateY(-3px);
        }

        .stars {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 2px 1px rgba(255, 255, 255, 0.5);
        }

        .star-1 {
            top: 10px;
            right: 20px;
        }

        .star-2 {
            top: 25px;
            right: 40px;
        }

        .star-3 {
            top: 15px;
            right: 60px;
        }

        .star-4 {
            top: 30px;
            right: 30px;
        }

        .star-5 {
            top: 20px;
            left: 25px;
        }

        .star-6 {
            top: 12px;
            left: 60px;
        }

        .star-7 {
            bottom: 15px;
            right: 15px;
        }

        .sunny-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #663300;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);
            box-shadow: 0 7px 20px rgba(255, 180, 0, 0.4);
            transform: translateY(-3px);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .sunny-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg,
                    #FFEB3B,
                    /* Amarillo brillante */
                    #FFD700,
                    /* Dorado */
                    #FFC107,
                    /* Ámbar */
                    #FF9800
                    /* Naranja */
                );
            z-index: -1;
        }

        .sunny-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(255, 180, 0, 0.3);
        }

        .sun-rays {
            position: absolute;
            top: 50%;
            left: 30px;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
        }

        .sun-center {
            position: absolute;
            width: 20px;
            height: 20px;
            background: #FF9800;
            border-radius: 50%;
            left: 9px;
            top: 5px;
            box-shadow: 0 0 15px 5px rgba(255, 152, 0, 0.7);
        }

        .ray {
            position: absolute;
            background: #FFEB3B;
            height: 3px;
            width: 14px;
            top: 13px;
            left: 13px;
            box-shadow: 0 0 5px 1px rgba(255, 235, 59, 0.6);
        }

        .ray:nth-child(1) {
            transform: rotate(0deg) translateX(-20px);
        }

        .ray:nth-child(2) {
            transform: rotate(45deg) translateX(-20px);
        }

        .ray:nth-child(3) {
            transform: rotate(90deg) translateX(-20px);
        }

        .ray:nth-child(4) {
            transform: rotate(135deg) translateX(-20px);
        }

        .ray:nth-child(5) {
            transform: rotate(180deg) translateX(-20px);
        }

        .ray:nth-child(6) {
            transform: rotate(225deg) translateX(-20px);
        }

        .ray:nth-child(7) {
            transform: rotate(270deg) translateX(-20px);
        }

        .ray:nth-child(8) {
            transform: rotate(315deg) translateX(-20px);
        }

        .button-text {
            margin-left: 35px;
        }

        .heat-wave {
            position: absolute;
            opacity: 0.3;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            animation: pulse 3s infinite alternate;
        }

        .heat-wave-1 {
            width: 100px;
            height: 100px;
            top: -20px;
            right: -20px;
        }

        .heat-wave-2 {
            width: 70px;
            height: 70px;
            bottom: -10px;
            right: 40px;
        }

        @keyframes pulse {
            from {
                transform: scale(0.8);
                opacity: 0.2;
            }

            to {
                transform: scale(1.1);
                opacity: 0.4;
            }
        }

        .rainy-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        .rainy-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    #2C3E50,
                    /* Azul grisáceo oscuro */
                    #34495E,
                    /* Azul pizarra */
                    #5D8CAE
                    /* Azul medio */
                );
            z-index: -1;
        }

        .rainy-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .cloud {
            position: absolute;
            left: 16px;
            top: 38%;
            transform: translateY(-60%);
            width: 34px;
            height: 12px;
            background: #D3D3D3;
            border-radius: 12px;
        }

        .cloud:before {
            content: '';
            position: absolute;
            top: -6px;
            left: 6px;
            width: 12px;
            height: 12px;
            background: #D3D3D3;
            border-radius: 50%;
        }

        .cloud:after {
            content: '';
            position: absolute;
            top: -10px;
            left: 16px;
            width: 16px;
            height: 16px;
            background: #D3D3D3;
            border-radius: 50%;
        }

        .rain {
            position: absolute;
            width: 2px;
            background: #A9CCE3;
            border-radius: 2px;
            animation: rain-fall linear infinite;
        }

        .rain-1 {
            height: 8px;
            left: 28px;
            top: 25px;
            animation-duration: 0.6s;
            animation-delay: 0.1s;
        }

        .rain-2 {
            height: 10px;
            left: 35px;
            top: 25px;
            animation-duration: 0.8s;
            animation-delay: 0.3s;
        }

        .rain-3 {
            height: 7px;
            left: 42px;
            top: 25px;
            animation-duration: 0.7s;
            animation-delay: 0.5s;
        }

        .rain-4 {
            height: 9px;
            left: 22px;
            top: 25px;
            animation-duration: 0.9s;
            animation-delay: 0.2s;
        }

        .button-text {
            margin-left: 30px;
        }

        @keyframes rain-fall {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            100% {
                transform: translateY(30px) scale(0.7);
                opacity: 0;
            }
        }

        .puddle {
            position: absolute;
            bottom: 8px;
            left: 35px;
            width: 20px;
            height: 4px;
            background: rgba(173, 216, 230, 0.4);
            border-radius: 50%;
        }

        .splash {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(173, 216, 230, 0.6);
            border-radius: 50%;
            opacity: 0;
            animation: splash 1s infinite;
        }

        .splash-1 {
            left: 35px;
            bottom: 10px;
            animation-delay: 0.2s;
        }

        .splash-2 {
            left: 44px;
            bottom: 10px;
            animation-delay: 0.5s;
        }

        .splash-3 {
            left: 40px;
            bottom: 10px;
            animation-delay: 0.8s;
        }

        @keyframes splash {
            0% {
                transform: scale(0.5, 0.5) translateY(0);
                opacity: 0;
            }

            20% {
                opacity: 0.6;
            }

            50% {
                transform: scale(1, 1) translateY(-5px);
                opacity: 0;
            }

            100% {
                opacity: 0;
            }
        }

        .cloudy-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #333;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }

        .cloudy-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    #B5B5B5,
                    /* Gris claro */
                    #CCCCCC,
                    /* Gris más claro */
                    #E0E0E0
                    /* Casi blanco */
                );
            z-index: -1;
        }

        .cloudy-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        .cloud-group {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        .cloud-1 {
            position: relative;
            width: 36px;
            height: 12px;
            background: #FFFFFF;
            border-radius: 10px;
            box-shadow: inset 0 -3px 3px rgba(0, 0, 0, 0.1);
        }

        .cloud-1:before {
            content: '';
            position: absolute;
            top: -6px;
            left: 5px;
            width: 15px;
            height: 15px;
            background: #FFFFFF;
            border-radius: 50%;
            box-shadow: inset 0 -2px 3px rgba(0, 0, 0, 0.1);
        }

        .cloud-1:after {
            content: '';
            position: absolute;
            top: -10px;
            left: 17px;
            width: 18px;
            height: 18px;
            background: #FFFFFF;
            border-radius: 50%;
            box-shadow: inset 0 -2px 3px rgba(0, 0, 0, 0.1);
        }

        .cloud-2 {
            position: absolute;
            width: 25px;
            height: 8px;
            background: #F0F0F0;
            border-radius: 8px;
            left: 20px;
            top: -15px;
            opacity: 0.7;
        }

        .cloud-2:before {
            content: '';
            position: absolute;
            top: -4px;
            left: 3px;
            width: 10px;
            height: 10px;
            background: #F0F0F0;
            border-radius: 50%;
        }

        .cloud-2:after {
            content: '';
            position: absolute;
            top: -6px;
            left: 11px;
            width: 12px;
            height: 12px;
            background: #F0F0F0;
            border-radius: 50%;
        }

        .cloud-3 {
            position: absolute;
            width: 22px;
            height: 7px;
            background: #E8E8E8;
            border-radius: 7px;
            left: -5px;
            top: 5px;
            opacity: 0.8;
        }

        .cloud-3:before {
            content: '';
            position: absolute;
            top: -3px;
            left: 3px;
            width: 8px;
            height: 8px;
            background: #E8E8E8;
            border-radius: 50%;
        }

        .cloud-3:after {
            content: '';
            position: absolute;
            top: -5px;
            left: 10px;
            width: 10px;
            height: 10px;
            background: #E8E8E8;
            border-radius: 50%;
        }

        .button-text {
            margin-left: 35px;
        }

        .light-beam {
            position: absolute;
            width: 60px;
            height: 20px;
            background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0) 70%);
            top: 0;
            right: 20px;
            transform: rotate(-20deg);
            opacity: 0.6;
            filter: blur(3px);
        }


        .slider-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .slider {
            animation: scroll 30s linear infinite;
            min-width: 200%;
            /* Asegura espacio suficiente */
        }

        @keyframes scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-240%);
            }
        }

        .slider:hover {
            animation-play-state: paused;
        }

        .snowy-button {
            position: relative;
            width: 200px;
            height: 60px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 18px;
            color: #2C3E50;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.8);
            box-shadow: 0 7px 20px rgba(200, 200, 255, 0.4), inset 0 -2px 5px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }

        .snowy-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    #F5F7FA,
                    /* Blanco azulado */
                    #E4EBF5,
                    /* Gris muy claro */
                    #D7DDE8
                    /* Gris azulado claro */
                );
            z-index: -1;
        }

        .snowy-button:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(200, 200, 255, 0.3), inset 0 -1px 3px rgba(0, 0, 0, 0.1);
        }

        .snowflake {
            position: absolute;
            color: white;
            text-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
            user-select: none;
            animation: snowfall linear infinite;
        }

        @keyframes snowfall {
            0% {
                transform: translateY(-10px) rotate(0deg);
                opacity: 0;
            }

            20% {
                opacity: 1;
            }

            100% {
                transform: translateY(60px) rotate(360deg);
                opacity: 0;
            }
        }

        .snow-1 {
            left: 20px;
            font-size: 14px;
            animation-duration: 3s;
            animation-delay: 0.2s;
        }

        .snow-2 {
            left: 35px;
            font-size: 10px;
            animation-duration: 2.5s;
            animation-delay: 0.5s;
        }

        .snow-3 {
            left: 50px;
            font-size: 12px;
            animation-duration: 3.2s;
            animation-delay: 0.1s;
        }

        .snow-4 {
            left: 170px;
            font-size: 14px;
            animation-duration: 2.8s;
            animation-delay: 0.3s;
        }

        .snow-5 {
            left: 150px;
            font-size: 9px;
            animation-duration: 3.5s;
            animation-delay: 0.7s;
        }

        .snow-6 {
            left: 130px;
            font-size: 11px;
            animation-duration: 3s;
            animation-delay: 0.4s;
        }

        .snow-pile {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 12px;
            background: white;
            border-radius: 0 0 30px 30px;
            box-shadow: inset 0 2px 7px rgba(0, 0, 0, 0.07);
        }

        .snow-pile:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 30px;
            width: 30px;
            height: 18px;
            background: white;
            border-radius: 30px 30px 0 0;
            transform: translateY(-50%);
        }

        .snow-pile:after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 40px;
            width: 25px;
            height: 15px;
            background: white;
            border-radius: 25px 25px 0 0;
            transform: translateY(-40%);
        }

        .button-text {
            position: relative;
            z-index: 2;
        }

        .winter-icon {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            margin-right: 8px;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5);
            position: relative;
            top: 1px;
        }

        .day-moments {
            margin: 0 auto;
            text-align: center;
        }

        .partly-cloudy-button {
            position: relative;
            width: 200px;
            height: 60px;
            background: linear-gradient(to bottom, #87CEEB, #5b9bd5);
            border: none;
            border-radius: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .partly-cloudy-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
        }

        .partly-cloudy-button.sun-container {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
        }

        .partly-cloudy-button.sun-center {
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: #FFC107;
            border-radius: 50%;
            top: 10px;
            left: 10px;
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.8);
            animation: glow 3s infinite alternate;
        }

        .partly-cloudy-button.ray {
            position: absolute;
            background-color: #FFC107;
            height: 3px;
            width: 20px;
            top: 23.5px;
            left: 45px;
            transform-origin: left center;
        }

        .ray:nth-child(1) {
            transform: rotate(0deg);
        }

        .partly-cloudy-button.ray:nth-child(2) {
            transform: rotate(45deg);
        }

        .partly-cloudy-button.ray:nth-child(3) {
            transform: rotate(90deg);
        }

        .partly-cloudy-button.ray:nth-child(4) {
            transform: rotate(135deg);
        }

        .partly-cloudy-button.ray:nth-child(5) {
            transform: rotate(180deg);
        }

        .partly-cloudy-button.ray:nth-child(6) {
            transform: rotate(225deg);
        }

        .partly-cloudy-button.ray:nth-child(7) {
            transform: rotate(270deg);
        }

        .partly-cloudy-button.ray:nth-child(8) {
            transform: rotate(315deg);
        }

        .partly-cloudy-button.cloud {
            position: absolute;
            background-color: white;
            border-radius: 50px;
            filter: drop-shadow(2px 5px 5px rgba(0, 0, 0, 0.2));
            animation: float 5s infinite alternate ease-in-out;
        }

        .partly-cloudy-button.cloud-1 {
            width: 70px;
            height: 30px;
            top: 40px;
            right: 20px;
        }

        .partly-cloudy-button.cloud-2 {
            width: 40px;
            height: 20px;
            top: 30px;
            right: 30px;
            opacity: 0.8;
            animation-delay: 0.5s;
        }

        .partly-cloudy-button.cloud-3 {
            width: 60px;
            height: 25px;
            top: 60px;
            right: 40px;
            opacity: 0.9;
            animation-delay: 1s;
        }

        .partly-cloudy-button.cloud:before,
        .partly-cloudy-button.cloud:after {
            content: '';
            position: absolute;
            background-color: white;
            border-radius: 50%;
        }

        .partly-cloudy-button.cloud-1:before {
            width: 25px;
            height: 25px;
            top: -10px;
            left: 15px;
        }

        .partly-cloudy-button.cloud-1:after {
            width: 25px;
            height: 25px;
            top: -8px;
            right: 15px;
        }

        .partly-cloudy-button.cloud-2:before {
            width: 15px;
            height: 15px;
            top: -8px;
            left: 10px;
        }

        .partly-cloudy-button.cloud-2:after {
            width: 15px;
            height: 15px;
            top: -6px;
            right: 10px;
        }

        .partly-cloudy-button.cloud-3:before {
            width: 20px;
            height: 20px;
            top: -8px;
            left: 12px;
        }

        .partly-cloudy-button.cloud-3:after {
            width: 20px;
            height: 20px;
            top: -7px;
            right: 12px;
        }

        .partly-cloudy-button.button-text {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: white;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
            left: 0;
        }

        @keyframes glow {
            0% {
                box-shadow: 0 0 10px rgba(255, 193, 7, 0.6);
            }

            100% {
                box-shadow: 0 0 25px rgba(255, 193, 7, 0.9);
            }
        }

        @keyframes float {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-10px);
            }
        }

        /* Contenedor principal del botón */
        .wx-wind-btn-container {
            display: inline-block;
            position: relative;
        }

        /* Estilo base del botón de viento */
        .wx-wind-btn {
            position: relative;
            width: 200px;
            height: 60px;
            background: linear-gradient(to right, #e8f4f8, #c9e6f2);
            border-radius: 30px;
            border: none;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* Efecto hover del botón */
        .wx-wind-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        /* Contenedor de los elementos de viento */
        .wx-wind-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        /* Líneas de viento */
        .wx-wind-line {
            position: absolute;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
        }

        .wx-wind-line-1 {
            width: 60px;
            top: 30px;
            left: 20px;
            animation: wx-wind-blow 3s infinite;
        }

        .wx-wind-line-2 {
            width: 40px;
            top: 50px;
            left: 15px;
            animation: wx-wind-blow 3s infinite 0.2s;
        }

        .wx-wind-line-3 {
            width: 70px;
            top: 70px;
            left: 25px;
            animation: wx-wind-blow 3s infinite 0.4s;
        }

        .wx-wind-line-4 {
            width: 50px;
            top: 90px;
            left: 10px;
            animation: wx-wind-blow 3s infinite 0.6s;
        }

        /* Hojas flotando en el viento */
        .wx-wind-leaf {
            position: absolute;
            width: 10px;
            height: 6px;
            background-color: #8BC34A;
            border-radius: 50% 20% 50% 20%;
            opacity: 0.7;
        }

        .wx-wind-leaf-1 {
            top: 40px;
            left: -10px;
            animation: wx-wind-leaf-blow 6s infinite linear;
        }

        .wx-wind-leaf-2 {
            top: 65px;
            left: -10px;
            animation: wx-wind-leaf-blow 7s infinite 1s linear;
            transform: rotate(30deg);
        }

        .wx-wind-leaf-3 {
            top: 85px;
            left: -10px;
            animation: wx-wind-leaf-blow 8s infinite 2s linear;
            transform: rotate(-20deg);
        }

        /* Texto del botón */
        .wx-wind-btn-text {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: #546E7A;
            font-weight: bold;
            left: 0;
            font-size: 14px;
        }

        /* Animaciones */
        @keyframes wx-wind-blow {
            0% {
                transform: translateX(0) scaleX(1);
                opacity: 0;
            }

            5% {
                opacity: 0.8;
            }

            95% {
                opacity: 0.8;
            }

            100% {
                transform: translateX(100px) scaleX(1.2);
                opacity: 0;
            }
        }

        @keyframes wx-wind-leaf-blow {
            0% {
                transform: translateX(0) translateY(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.7;
            }

            90% {
                opacity: 0.7;
            }

            100% {
                transform: translateX(160px) translateY(10px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Contenedor principal del botón */
        .wx-clear-container {
            display: inline-block;
            position: relative;
        }

        /* Estilo base del botón de cielo despejado */
        .wx-clear-btn {
            position: relative;
            width: 200px;
            height: 60px;
            background: linear-gradient(to bottom, #87CEEB, #1E90FF);
            border-radius: 30px;
            border: none;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* Efecto hover del botón */
        .wx-clear-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        /* Sol central */
        .wx-clear-sun {
            position: absolute;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #FFEF00, #FFA500);
            border-radius: 50%;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.8);
            animation: wx-clear-sun-pulse 4s infinite alternate;
        }

        /* Rayos de sol */
        .wx-clear-ray {
            position: absolute;
            background: linear-gradient(to right, rgba(255, 215, 0, 0.8), rgba(255, 215, 0, 0));
            height: 3px;
            width: 30px;
            top: 60px;
            left: 50%;
            transform-origin: left center;
        }

        .wx-clear-ray-1 {
            transform: translateX(-1px) rotate(0deg);
        }

        .wx-clear-ray-2 {
            transform: translateX(-1px) rotate(45deg);
        }

        .wx-clear-ray-3 {
            transform: translateX(-1px) rotate(90deg);
        }

        .wx-clear-ray-4 {
            transform: translateX(-1px) rotate(135deg);
        }

        .wx-clear-ray-5 {
            transform: translateX(-1px) rotate(180deg);
        }

        .wx-clear-ray-6 {
            transform: translateX(-1px) rotate(225deg);
        }

        .wx-clear-ray-7 {
            transform: translateX(-1px) rotate(270deg);
        }

        .wx-clear-ray-8 {
            transform: translateX(-1px) rotate(315deg);
        }

        /* Pequeñas estrellas (representando el brillo y claridad) */
        .wx-clear-sparkle {
            position: absolute;
            width: 3px;
            height: 3px;
            background-color: white;
            border-radius: 50%;
        }

        .wx-clear-sparkle-1 {
            top: 20px;
            left: 40px;
            animation: wx-clear-twinkle 2s infinite 0.2s;
        }

        .wx-clear-sparkle-2 {
            top: 35px;
            left: 110px;
            animation: wx-clear-twinkle 2s infinite 0.5s;
        }

        .wx-clear-sparkle-3 {
            top: 70px;
            left: 30px;
            animation: wx-clear-twinkle 2s infinite 0.8s;
        }

        .wx-clear-sparkle-4 {
            top: 60px;
            left: 120px;
            animation: wx-clear-twinkle 2s infinite 1.1s;
        }

        .wx-clear-sparkle-5 {
            top: 90px;
            left: 80px;
            animation: wx-clear-twinkle 2s infinite 1.4s;
        }

        /* Texto del botón */
        .wx-clear-text {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: white;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            left: 0;
        }

        /* Animaciones */
        @keyframes wx-clear-sun-pulse {
            0% {
                box-shadow: 0 0 20px rgba(255, 215, 0, 0.7);
                transform: translateX(-50%) scale(1);
            }

            100% {
                box-shadow: 0 0 40px rgba(255, 215, 0, 0.9);
                transform: translateX(-50%) scale(1.05);
            }
        }

        @keyframes wx-clear-twinkle {
            0% {
                opacity: 0.2;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.5);
            }

            100% {
                opacity: 0.2;
                transform: scale(1);
            }
        }

        /* Contenedor principal del botón */
        .wx-overcast-container {
            display: inline-block;
            position: relative;
        }

        /* Estilo base del botón de cielo cubierto */
        .wx-overcast-btn {
            position: relative;
            width: 200px;
            height: 60px;
            background: linear-gradient(to bottom, #a3a3a3, #6e6e6e);
            border-radius: 30px;
            border: none;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* Efecto hover del botón */
        .wx-overcast-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        /* Nubes de fondo */
        .wx-overcast-cloud {
            position: absolute;
            background-color: #8a8a8a;
            border-radius: 50px;
        }

        .wx-overcast-cloud-1 {
            width: 100px;
            height: 40px;
            top: 30px;
            left: 20px;
            animation: wx-overcast-float 15s infinite alternate ease-in-out;
            opacity: 0.8;
        }

        .wx-overcast-cloud-2 {
            width: 70px;
            height: 30px;
            top: 20px;
            right: 25px;
            animation: wx-overcast-float 18s infinite alternate-reverse ease-in-out;
            opacity: 0.7;
        }

        .wx-overcast-cloud-3 {
            width: 120px;
            height: 45px;
            top: 60px;
            left: 10px;
            animation: wx-overcast-float 20s infinite alternate ease-in-out;
            opacity: 0.9;
        }

        .wx-overcast-cloud-4 {
            width: 90px;
            height: 35px;
            top: 70px;
            right: 10px;
            animation: wx-overcast-float 17s infinite alternate-reverse ease-in-out;
            opacity: 0.75;
        }

        /* Formas redondeadas para las nubes */
        .wx-overcast-cloud:before,
        .wx-overcast-cloud:after {
            content: '';
            position: absolute;
            background-color: inherit;
            border-radius: 50%;
        }

        .wx-overcast-cloud-1:before {
            width: 35px;
            height: 35px;
            top: -15px;
            left: 20px;
        }

        .wx-overcast-cloud-1:after {
            width: 35px;
            height: 35px;
            top: -10px;
            right: 25px;
        }

        .wx-overcast-cloud-2:before {
            width: 25px;
            height: 25px;
            top: -10px;
            left: 15px;
        }

        .wx-overcast-cloud-2:after {
            width: 25px;
            height: 25px;
            top: -8px;
            right: 15px;
        }

        .wx-overcast-cloud-3:before {
            width: 40px;
            height: 40px;
            top: -18px;
            left: 25px;
        }

        .wx-overcast-cloud-3:after {
            width: 40px;
            height: 40px;
            top: -15px;
            right: 30px;
        }

        .wx-overcast-cloud-4:before {
            width: 30px;
            height: 30px;
            top: -12px;
            left: 20px;
        }

        .wx-overcast-cloud-4:after {
            width: 30px;
            height: 30px;
            top: -10px;
            right: 20px;
        }

        /* Indicador de temperatura */
        .wx-overcast-temp {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #f0f0f0;
            background-color: rgba(60, 60, 60, 0.7);
            padding: 3px 8px;
            border-radius: 10px;
        }

        /* Texto del botón */
        .wx-overcast-text {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: #f0f0f0;
            font-weight: bold;
            left: 0;
        }

        /* Animaciones */
        @keyframes wx-overcast-float {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(10px);
            }
        }
    </style>
</head>

<body>

    <section>
        <h2 class="section-title">Momentos del Día</h2>
        <!-- Contenido para momentos del día -->
        <div class="day-moments">
            <?php
            if (isset($_POST['get_weather'])) {

                echo "<h3>Fecha y Hora Actual: $formatted_date</h3>";
                if ($temperature !== 'N/A') {
                    echo "<h3>Clima actual en $city</h3>";
                    echo "<p><strong>Temperatura: $temperature °C</strong></p>";
                    echo "<p><strong>Descripción: $weather_description_es</strong></p>";
                    if ($weather_icon) {
                        echo "<p><img src='$weather_icon' alt='Clima icon'></p>";
                    }
                } else {
                    echo "<p>No se encontraron datos de clima disponibles para $city.</p>";
                }
            }
            ?>

            <div class="button-container-wrapper">
                <div class="button-container" style="<?php echo ($current_button == 'sunrise-button') ? '' : 'display: none;'; ?>">
                    <div class="time-label">06:00 - 12:00</div>
                    <button class="sunrise-button">
                        <span class="sun"></span>
                        Amanecer
                    </button>
                </div>

                <div class="button-container" style="<?php echo ($current_button == 'day-button') ? '' : 'display: none;'; ?>">
                    <div class="time-label">12:00 - 18:00</div>
                    <button class="day-button">
                        <span class="sun-day"></span>
                        Día Soleado
                        <span class="cloud cloud-1"></span>
                        <span class="cloud cloud-2"></span>
                    </button>
                </div>

                <div class="button-container" style="<?php echo ($current_button == 'sunset-button') ? '' : 'display: none;'; ?>">
                    <div class="time-label">18:00 - 20:00</div>
                    <button class="sunset-button">
                        <span class="sunset-sun"></span>
                        Atardecer
                        <span class="horizon"></span>
                    </button>
                </div>

                <div class="button-container" style="<?php echo ($current_button == 'night-button') ? '' : 'display: none;'; ?>">
                    <div class="time-label">20:00 - 6:00</div>
                    <button class="night-button">
                        <span class="moon"></span>
                        Noche
                        <span class="stars star-1"></span>
                        <span class="stars star-2"></span>
                        <span class="stars star-3"></span>
                        <span class="stars star-4"></span>
                        <span class="stars star-5"></span>
                        <span class="stars star-6"></span>
                        <span class="stars star-7"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>



    <section>
        <h2 class="section-title">Clima</h2>
        <div class="weather-form">
            <form method="post">
                <div class="weather-input">
                    <input type="text" name="city" placeholder="Ingresa una ciudad" value="Santiago" class="city-input">
                    <button type="submit" name="get_weather">Obtener Clima</button>
                </div>
            </form>
        </div>
        <div class="weather-results">
            <div class="slider-container">
                <div class="<?php echo ($slider_active != '') ? 'day-moments' : 'slider'; ?>">
                    <button class="sunny-button" style="<?php echo ($current_button_class == 'sunny-button') ? '' : 'display: none;'; ?>">
                        <div class="sun-rays">
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="sun-center"></div>
                        </div>
                        <span class="button-text">Soleado</span>
                        <div class="heat-wave heat-wave-1"></div>
                        <div class="heat-wave heat-wave-2"></div>
                    </button>

                    <button class="rainy-button" style="<?php echo ($current_button_class == 'rainy-button') ? '' : 'display: none;'; ?>">
                        <div class="cloud"></div>
                        <div class="rain rain-1"></div>
                        <div class="rain rain-2"></div>
                        <div class="rain rain-3"></div>
                        <div class="rain rain-4"></div>
                        <div class="puddle"></div>
                        <div class="splash splash-1"></div>
                        <div class="splash splash-2"></div>
                        <div class="splash splash-3"></div>
                        <span class="button-text">Lluvioso</span>
                    </button>

                    <button class="cloudy-button" style="<?php echo ($current_button_class == 'cloudy-button') ? '' : 'display: none;'; ?>">
                        <div class="cloud-group">
                            <div class="cloud-1"></div>
                            <div class="cloud-2"></div>
                            <div class="cloud-3"></div>
                        </div>
                        <span class="button-text">Nublado</span>
                        <div class="light-beam"></div>
                    </button>

                    <button class="snowy-button" style="<?php echo ($current_button_class == 'snowy-button') ? '' : 'display: none;'; ?>">
                        <div class="snow-pile"></div>
                        <span class="snowflake snow-1">❄</span>
                        <span class="snowflake snow-2">❅</span>
                        <span class="snowflake snow-3">❆</span>
                        <span class="snowflake snow-4">❄</span>
                        <span class="snowflake snow-5">❅</span>
                        <span class="snowflake snow-6">❆</span>
                        <span class="button-text"><span class="winter-icon"></span>Nevado</span>
                    </button>

                    <button class="partly-cloudy-button">
                        <div class="sun-container">
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="ray"></div>
                            <div class="sun-center"></div>
                        </div>

                        <div class="cloud cloud-1"></div>
                        <div class="cloud cloud-2"></div>
                        <div class="cloud cloud-3"></div>
                        <span class="button-text">Parcialmente nublado</span>
                    </button>
                    <div class="wx-wind-btn-container">
                        <button class="wx-wind-btn" style="<?php echo ($current_button_class == 'wind-button') ? '' : 'display: none;'; ?>">
                            <div class="wx-wind-elements">
                                <div class="wx-wind-line wx-wind-line-1"></div>
                                <div class="wx-wind-line wx-wind-line-2"></div>
                                <div class="wx-wind-line wx-wind-line-3"></div>
                                <div class="wx-wind-line wx-wind-line-4"></div>

                                <div class="wx-wind-leaf wx-wind-leaf-1"></div>
                                <div class="wx-wind-leaf wx-wind-leaf-2"></div>
                                <div class="wx-wind-leaf wx-wind-leaf-3"></div>
                            </div>
                            <span class="wx-wind-btn-text">Ventoso</span>
                        </button>
                    </div>
                    <div class="wx-clear-container">
                        <button class="wx-clear-btn" style="<?php echo ($current_button_class == 'clear-sky-button') ? '' : 'display: none;'; ?>">
                            <div class="wx-clear-sun"></div>
                            <div class="wx-clear-ray wx-clear-ray-1"></div>
                            <div class="wx-clear-ray wx-clear-ray-2"></div>
                            <div class="wx-clear-ray wx-clear-ray-3"></div>
                            <div class="wx-clear-ray wx-clear-ray-4"></div>
                            <div class="wx-clear-ray wx-clear-ray-5"></div>
                            <div class="wx-clear-ray wx-clear-ray-6"></div>
                            <div class="wx-clear-ray wx-clear-ray-7"></div>
                            <div class="wx-clear-ray wx-clear-ray-8"></div>

                            <div class="wx-clear-sparkle wx-clear-sparkle-1"></div>
                            <div class="wx-clear-sparkle wx-clear-sparkle-2"></div>
                            <div class="wx-clear-sparkle wx-clear-sparkle-3"></div>
                            <div class="wx-clear-sparkle wx-clear-sparkle-4"></div>
                            <div class="wx-clear-sparkle wx-clear-sparkle-5"></div>

                            <span class="wx-clear-text">Cielo Despejado</span>
                        </button>
                    </div>
                    <div class="wx-overcast-container">
                        <button class="wx-overcast-btn" style="<?php echo ($current_button_class == 'overcast-sky-button') ? '' : 'display: none;'; ?>">
                            <div class="wx-overcast-cloud wx-overcast-cloud-1"></div>
                            <div class="wx-overcast-cloud wx-overcast-cloud-2"></div>
                            <div class="wx-overcast-cloud wx-overcast-cloud-3"></div>
                            <div class="wx-overcast-cloud wx-overcast-cloud-4"></div>
                            <span class="wx-overcast-text">Cielo Cubierto</span>
                        </button>
                    </div>
                    <button>Chubascos</button>
                    <button>Tormenta eléctrica</button>
                    <button>Niebla</button>
                    <button>Niebla espesa</button>
                    <button>Granizo</button>
                    <button>Parche de niebla</button>
                    <button>Tormenta de nieve</button>
                    <button>Aguacero congelado</button>
                    <button>Llovizna</button>
                    <button>Lluvia helada</button>
                    <button>Tornado</button>
                    <button>Huracán</button>
                    <button>Sequía</button>
                    <button>Polvo</button>
                    <button>Tormenta de arena</button>
                    <button>Hielo</button>
                    <button>Ráfaga</button>
                    <button>Tormenta tropical</button>
                    <button>Frío</button>
                    <button>Caluroso</button>
                    <button>Tormenta</button>
                    <button>Ola de calor</button>

                </div>

            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    let slider = document.querySelector(".slider");
                    let clone = slider.innerHTML;
                    slider.innerHTML += clone; // Duplicamos el contenido para el efecto continuo
                });
            </script>
        </div>
    </section>
</body>

</html>