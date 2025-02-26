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
        'Heatwave' => 'Ola de calor',  // Período de calor extremo. Temperatura generalmente por encima de los 35°C.
        'Light snow' => 'Nieve ligera', // Caída suave de nieve, sin acumulación significativa. Rango de temperatura: -5°C a 2°C.

    ];



    // Traducir la descripción
    $weather_description_es = $translations[$weather_description] ?? $weather_description;  // Si no está en el mapa, se mantiene la descripción original

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
        "Niebla" => "",
        "Niebla espesa" => "",
        "Granizo" => "",
        "Parche de niebla" => "",
        "Tormenta de nieve" => "",
        "Aguacero congelado" => "",
        "Llovizna" => "",
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
    ];

    // Determinar qué botón se debe mostrar
    $current_button_class = isset($weather_buttons[$weather_description_es]) ? $weather_buttons[$weather_description_es] : null;

    $slider_active = $current_button_class;
} else {
    $slider_active = "";
    $current_button_class="";
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

                    <button class="partly-cloudy-button" style="<?php echo ($current_button_class == 'partly-cloudy-button') ? '' : 'display: none;'; ?>">
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

                    <button class="boton-chubasco" style="<?php echo ($current_button_class == 'boton-chubasco') ? '' : 'display: none;'; ?>" onclick="animarChubasco(this)">
                        Chubasco
                        <div class="gotas"></div>
                    </button>

                    <button id="botonTormenta" class="boton-tormenta" style="<?php echo ($current_button_class == 'boton-tormenta') ? '' : 'display: none;'; ?>">
                        <span class="texto">Tormenta eléctrica</span>
                    </button>
                    <div class="fog-container">
                        <div class="fog"></div>
                        <div class="fog fog-2"></div>
                        <div class="fog fog-3"></div>
                        <button class="fog-button">Niebla</button>
                    </div>
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
            <script>
                function crearGotas(boton) {
                    const contGotas = boton.querySelector('.gotas');
                    contGotas.innerHTML = '';

                    // Crear gotas de lluvia
                    for (let i = 0; i < 15; i++) {
                        const gota = document.createElement('div');
                        gota.className = 'gota';
                        gota.style.left = Math.random() * 100 + '%';
                        gota.style.animationDelay = Math.random() * 1 + 's';
                        gota.style.animationDuration = 0.5 + Math.random() * 0.5 + 's';
                        contGotas.appendChild(gota);
                    }
                }

                function animarChubasco(boton) {
                    crearGotas(boton);
                }

                // Inicializar gotas al cargar
                document.addEventListener('DOMContentLoaded', function() {
                    const boton = document.querySelector('.boton-chubasco');
                    crearGotas(boton);
                });
            </script>
            <script>
                const boton = document.getElementById('botonTormenta');

                function crearRelampagos() {
                    // Eliminar relámpagos anteriores
                    const relampagosExistentes = document.querySelectorAll('.relampago');
                    relampagosExistentes.forEach(rel => rel.remove());

                    // Crear nuevos relámpagos
                    const numRelampagos = 3 + Math.floor(Math.random() * 3);

                    for (let i = 0; i < numRelampagos; i++) {
                        const relampago = document.createElement('div');
                        relampago.className = 'relampago';

                        // Posición y tamaño aleatorios
                        const ancho = 3 + Math.random() * 4;
                        const alto = 10 + Math.random() * 30;
                        const izquierda = 20 + Math.random() * 160;
                        const arriba = 5 + Math.random() * 40;
                        const rotacion = -30 + Math.random() * 60;

                        relampago.style.width = ancho + 'px';
                        relampago.style.height = alto + 'px';
                        relampago.style.left = izquierda + 'px';
                        relampago.style.top = arriba + 'px';
                        relampago.style.transform = `rotate(${rotacion}deg)`;

                        // Animación
                        relampago.style.animation = `flash ${1 + Math.random() * 2}s ease-out`;
                        relampago.style.animationDelay = `${Math.random() * 0.5}s`;

                        boton.appendChild(relampago);
                    }

                    // Programar siguiente destello
                    setTimeout(crearRelampagos, 3000 + Math.random() * 2000);
                }

                // Iniciar animación cuando la página cargue
                document.addEventListener('DOMContentLoaded', crearRelampagos);

                // Reiniciar animación al hacer clic
                boton.addEventListener('click', () => {
                    crearRelampagos();
                });
            </script>
            <script>
                document.querySelector('.fog-button').addEventListener('mouseover', function() {
                    document.querySelectorAll('.fog').forEach(fog => {
                        fog.style.opacity = '0.5';
                    });
                });

                document.querySelector('.fog-button').addEventListener('mouseout', function() {
                    document.querySelectorAll('.fog').forEach(fog => {
                        fog.style.opacity = '';
                    });
                });
            </script>
        </div>
    </section>
</body>

</html>