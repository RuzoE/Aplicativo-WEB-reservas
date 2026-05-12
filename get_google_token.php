<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;

// Cargar variables de entorno manualmente para simplicidad
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['GOOGLE_DRIVE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_DRIVE_CLIENT_SECRET'];

// La URL de redirección debe coincidir con la configurada en Google Cloud Console
// Para apps web locales se suele usar http://localhost o la URL del proyecto
$redirectUri = 'http://localhost'; 

$client = new Client();
$client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$client->addScope(Google\Service\Drive::DRIVE);

if (!isset($argv[1])) {
    $authUrl = $client->createAuthUrl();
    echo "\n1. Visita esta URL en tu navegador:\n\n" . $authUrl . "\n\n";
    echo "2. Inicia sesión y autoriza la aplicación.\n";
    echo "3. Serás redirigido a una URL que falla o a localhost. COPIA el parámetro 'code' de la URL.\n";
    echo "4. Ejecuta este comando con el código:\n\n   php get_google_token.php TU_CODIGO_AQUI\n\n";
} else {
    $authCode = $argv[1];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
    if (isset($accessToken['error'])) {
        echo "Error al obtener el token: " . $accessToken['error_description'] . "\n";
    } else {
        echo "\n¡ÉXITO! Tu nuevo Refresh Token es:\n\n";
        echo $accessToken['refresh_token'] . "\n\n";
        echo "Copia este valor y pégalo en tu archivo .env en GOOGLE_DRIVE_REFRESH_TOKEN\n";
    }
}
