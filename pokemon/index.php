<?php
$pokemonData = null;
$error = null;
$busqueda = '';

if (isset($_GET['pokemon']) && !empty(trim($_GET['pokemon']))) {
    $busqueda = strtolower(trim($_GET['pokemon']));
    $url = "https://pokeapi.co/api/v2/pokemon/{$busqueda}";

    // Opciones para manejar posibles errores HTTP de la API sin interrumpir la ejecución
    $options = [
        "http" => [
            "ignore_errors" => true,
            "method" => "GET",
            "header" => "User-Agent: PHP-Pokedex\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        // Verificar el código de respuesta HTTP
        if (isset($http_response_header[0]) && strpos($http_response_header[0], '200 OK') !== false) {
            $pokemonData = json_decode($response, true);
        } else {
            $error = "Pokémon no encontrado";
        }
    } else {
        $error = "Error al conectar con la PokéAPI";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex Básica (PHP)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            padding-top: 40px;
        }

        .pokedex {
            background-color: #e74c3c;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
            color: white;
        }

        .search-box {
            margin-bottom: 15px;
        }

        input {
            padding: 8px;
            border: none;
            border-radius: 5px;
            width: 60%;
        }

        button {
            padding: 8px 12px;
            border: none;
            background-color: #2c3e50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .screen {
            background-color: #ecf0f1;
            color: #333;
            border-radius: 10px;
            padding: 15px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .screen img {
            width: 120px;
            height: 120px;
        }

        .error {
            color: #c0392b;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="pokedex">
        <h2>Mini Pokédex</h2>

        <form method="GET" action="" class="search-box">
            <input type="text" name="pokemon" placeholder="Nombre o ID..." value="<?= htmlspecialchars($busqueda) ?>" required>
            <button type="submit">Buscar</button>
        </form>

        <div class="screen">
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($pokemonData): ?>
                <?php
                    $nombre = strtoupper($pokemonData['name']);
                    $id = $pokemonData['id'];
                    $imagen = $pokemonData['sprites']['front_default'];
                    $tipos = implode(', ', array_map(function($t) {
                        return $t['type']['name'];
                    }, $pokemonData['types']));
                    $altura = $pokemonData['height'] / 10;
                    $peso = $pokemonData['weight'] / 10;
                ?>
                <h3>#<?= $id ?> <?= htmlspecialchars($nombre) ?></h3>
                <?php if ($imagen): ?>
                    <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($nombre) ?>">
                <?php endif; ?>
                <p><strong>Tipo:</strong> <?= htmlspecialchars($tipos) ?></p>
                <p><strong>Altura:</strong> <?= $altura ?> m</p>
                <p><strong>Peso:</strong> <?= $peso ?> kg</p>
            <?php else: ?>
                <p>Escribe el nombre o número de un Pokémon para consultar la PokéAPI.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>