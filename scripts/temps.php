<?php

// Funcions
function getClau()
{
    // Clau de l'API
    return "e06d892660b3ede20300eb4b1d3855e7";
}

function getCoordenades($poblacio)
{
    // URL de Geocoding API
    $url = "http://api.openweathermap.org/geo/1.0/direct?q=$poblacio,ES&limit=1&appid=" . getClau();

    return getJSON($url);
}

function getTemps($lat, $lon)
{
    // URL de Weather API
    $url = "http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=" . getClau() . "&lang=ca&units=metric";

    return getJSON($url);
}

function getJSON($url)
{
    // Inicialitzar cURL
    $curl = curl_init();

    // Establir les opcions de cURL
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Executar una petició cURL
    $result = curl_exec($curl);

    // Comprovar si hi ha errors
    if (curl_errno($curl)) {
        die('Error:' . curl_error($curl));
    }

    // Tancar la sessió cURL
    curl_close($curl);

    // Decodificar les dades JSON
    return json_decode($result, true);
}

// Llògica
if (isset($_POST['poblacio'])) {
    // Escapament per seguretat
    $poblacio = htmlspecialchars($_POST['poblacio']);
    $coordenades = getCoordenades($poblacio);

    // Comprovar si la població existeix
    if (!empty($coordenades)) {
        $lat = $coordenades[0]['lat'];
        $lon = $coordenades[0]['lon'];
        $temps = getTemps($lat, $lon);
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!empty($temps)): ?>
        <title>El Temps a <?php echo $poblacio; ?></title>
    <?php else: ?>
        <title>Consulta el Temps d'alguna d'aquestes poblacions:</title>
    <?php endif; ?>

    <!-- Disseny Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php if (!empty($temps)): ?>

        <div class="container mt-5">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-center text-primary">El Temps a <?php echo $poblacio; ?></h3>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <h4>Temperatura</h4>
                            <p class="lead"><?php echo $temps['main']['temp']; ?>°C</p>
                        </div>
                        <div class="col-md-6 text-center">
                            <h4>Descripció</h4>
                            <p class="lead"><?php echo ucfirst($temps['weather'][0]['description']); ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Humitat</h5>
                            <p><?php echo $temps['main']['humidity']; ?>%</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Velocitat del vent</h5>
                            <p><?php echo $temps['wind']['speed']; ?> m/s</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php if (!empty($coordenades)): ?>
        <script>console.log(JSON.parse('<?php echo json_encode($coordenades[0], JSON_UNESCAPED_UNICODE); ?>'));</script>
    <?php endif; ?>

    <?php if (!empty($temps)): ?>
        <script>console.log(JSON.parse('<?php echo json_encode($temps, JSON_UNESCAPED_UNICODE); ?>'));</script>
    <?php endif; ?>

</body>

</html>

<?php if (!empty($coordenades)): ?>
    <!-- Coordenades: <?php echo json_encode($coordenades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>-->
<?php endif; ?>

<?php if (!empty($temps)): ?>
    <!-- Temps: <?php echo json_encode($temps, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>-->
<?php endif; ?>