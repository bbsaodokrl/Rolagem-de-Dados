<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$clientId = '1315821366066544670';
$clientSecret = 'hTIqCJLAyxA_XJxn4QdXWuM3ZKMxVzWC';
$redirectUri = 'http://localhost/rolagem-dados/discord-callback.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $url = 'https://discord.com/api/oauth2/token';
    $data = [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirectUri,
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseData = json_decode($response, true);

    if (isset($responseData['access_token'])) {
        $accessToken = $responseData['access_token'];

        $userInfoUrl = 'https://discord.com/api/users/@me';
        $userOptions = [
            'http' => [
                'header' => "Authorization: Bearer $accessToken\r\n",
                'method' => 'GET',
            ],
        ];
        $userContext = stream_context_create($userOptions);
        $userResponse = file_get_contents($userInfoUrl, false, $userContext);
        $user = json_decode($userResponse, true);

        echo json_encode([
            'username' => $user['username'],
        ]);
    } else {
        echo json_encode(["erro" => "Erro ao obter o token de acesso."]);
    }
} else {
    echo json_encode(["erro" => "Código de autorização não recebido."]);
}
?>
