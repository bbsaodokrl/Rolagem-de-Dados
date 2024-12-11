<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "historico";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $dado = $_GET['dado'] ?? '';
    if (!preg_match('/^(\\d+)d(\\d+)$/', $dado, $matches)) {
        echo json_encode(["erro" => "Formato inválido. Use 'XdY', ex.: 1d20."]);
        exit;
    }
    $numDados = (int)$matches[1];
    $maxVal = (int)$matches[2];

    if ($numDados <= 0 || $maxVal <= 0) {
        echo json_encode(["erro" => "Valores devem ser maiores que zero."]);
        exit;
    }

    $rolagens = [];
    for ($i = 0; $i < $numDados; $i++) {
        $rolagens[] = rand(1, $maxVal);
    }
    $userId = $_SERVER['REMOTE_ADDR'];
    $comando = $dado;
    $resultado = implode(",", $rolagens);

    $stmt = $conn->prepare("INSERT INTO historico (user_id, comando, resultado) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $userId, $comando, $resultado);
    $stmt->execute();

    $listaNumerada = '';
    foreach ($rolagens as $index => $valor) {
        $listaNumerada .= ($index + 1) . ". " . $valor . "\n";
    }
    $webhookUrl = 'https://discord.com/api/webhooks/1315831523215278100/tXlFNzmPWb7vlc71RUufhr0nFNZB2H3RWl4BISvuyszLHuDA17QiYd8E1OZUTIxVsOab';
    $message = [
        "content" => ":game_die: **Resultado da rolagem** :game_die:\n" . $listaNumerada
    ];

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["resultado" => $rolagens]);

    $stmt->close();
    $conn->close();
}
?>
