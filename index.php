<?php
// Definir la ciudad y la URL de la API
$weather_buttons = [
    "Soleado" => "sunny-button",
    "Lluvia" => "rainy-button",
    "Nublado" => "cloudy-button",
    "Nieve" => "snowy-button",
    "Parcialmente nublado" => "partly-cloudy-button",
    "Viento" => "wind-button",
    "Despejado" => "clear-sky-button",
    "Cubierto" => "overcast-sky-button",
    "Chubascos" => "boton-chubasco",
    "Tormenta eléctrica" => "boton-tormenta",
    "Niebla espesa" => "boton-niebla-espesa",
    "Granizo" => "boton-granizo",
    "Parche de niebla" => "parche_de_niebla",
    "Tormenta de nieve" => "tormenta_de_nieve",
    "Aguacero congelado" => "aguacero_congelado",
    "Llovizna" => "llovizna",
    "Lluvia helada" => "",
    "Tornado" => "",
    "Huracán" => "",
    "Sequía" => "",
    "Polvo" => "",
    "Tormenta de arena" => "",
    "Hielo" => "",
    "Ráfaga" => "",
    "Tormenta tropical" => "",
    "Frío" => "",
    "Caluroso" => "",
    "Tormenta" => "",
    "Ola de calor" => "",
    "Lluvia ligera, niebla" => "",
    "Lluvia ligera" => "",
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city = $_POST['city'] ?? 'Santiago';
    $api_key = "410f454b9ebb63e2a46e6dc2aa04554e";
    $api_url = "https://api.weatherstack.com/current?access_key=$api_key&query=$city";

    // Obtener los datos JSON de la API
    $response = @file_get_contents($api_url);
    if ($response === FALSE) {
        die('Error al obtener datos de la API');
    }



    $weather_data = json_decode($response, true);
    // Verificar que la respuesta de la API contenga los datos necesarios
    if (isset($weather_data['current'])) {
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
                // Si el rango cruza medianoche, compararlo
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

        // Aquí puedes utilizar $temperature, $weather_icon, $weather_description, etc., según lo necesites

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
            'Heatwave' => 'Ola de calor',  // Período de calor extremo. Temperatura generalmente por encima de los 35°C.
            'Light snow' => 'Nieve ligera', // Caída suave de nieve, sin acumulación significativa. Rango de temperatura: -5°C a 2°C.
            'Light Rain, Mist' => 'Lluvia ligera, niebla',
            'Light Rain' => 'Lluvia ligera',
        ];



        // Traducir la descripción
        $weather_description_es = $translations[$weather_description] ?? $weather_description;  // Si no está en el mapa, se mantiene la descripción original

        // Determinar qué botón se debe mostrar
        $current_button_class = isset($weather_buttons[$weather_description_es]) ? $weather_buttons[$weather_description_es] : null;

        $slider_active = $current_button_class;
    }
} else {
    $slider_active = "";
    $weather_description_es = null;
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hora y Clima</title>
    <link rel="stylesheet" href="style.css?v=<?= filemtime('style.css') ?>">

</head>

<body>

    <section>
        <h2 class="section-title">Momentos del Día</h2>
        <!-- Contenido para momentos del día -->
        <div class="day-moments">
            <?php
            if (isset($_POST['get_weather'])) {
                // Inicio del contenedor principal
                echo '<div class="weather-container">';

                // Sección de fecha y hora
                echo '<div class="date-time-section">';
                echo "<h3>Fecha y Hora Actual: <span class='highlight'>$formatted_date</span></h3>";
                echo '</div>';

                if ($temperature !== 'N/A') {
                    // Sección principal del clima
                    echo '<div class="weather-card">';
                    echo "<h3>Clima actual en <span class='city-name'>$city</span></h3>";

                    // Contenedor flexible para la información del clima
                    echo '<div class="weather-info">';

                    // Columna izquierda con la temperatura
                    echo '<div class="weather-temp">';
                    echo "<p class='temp-value'>$temperature<span class='temp-unit'>°C</span></p>";
                    echo '</div>';

                    // Columna derecha con la descripción e icono
                    echo '<div class="weather-details">';
                    if ($weather_icon) {
                        echo "<img src='$weather_icon' alt='Clima' class='weather-icon'>";
                    }
                    echo "<p class='weather-desc'>$weather_description_es</p>";
                    echo '</div>';

                    echo '</div>'; // Cierre de weather-info
                    echo '</div>'; // Cierre de weather-card
                } else {
                    // Mensaje de error
                    echo '<div class="error-message">';
                    echo "<p>No se encontraron datos de clima disponibles para <span class='city-name'>$city</span>.</p>";
                    echo '</div>';
                }

                echo '</div>'; // Cierre del contenedor principal
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


            <?php

            echo '<div class="iframes-container">';

            // Recorremos el arreglo y mostramos el iframe para cada botón no vacío
            foreach ($weather_buttons as $clima => $button_id) {
                if (!empty($button_id)) {


                    echo '<iframe src="climas/' . $button_id . '.html" 
                    title="' . $clima . '" 
                    width="250" 
                    height="150" 
                    frameborder="0" 
                    style="display: ' . (!isset($weather_description_es) || $weather_description_es === $clima ? 'inline' : 'none') . ';">
              </iframe>';
                }
            }

            echo '</div>';
            ?>

    </section>
</body>

</html>