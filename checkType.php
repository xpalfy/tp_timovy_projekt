<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Firebase\JWT\JWT;

use Firebase\JWT\Key;

require 'vendor/autoload.php';

function getJwtSecret(): string {
    $secret = include 'jwt.php';
    if (empty($secret['secret'])) {
        throw new Exception('JWT secret is not configured');
    }
    return $secret['secret'];
}

function generateToken($userId, $username, $email): string {
    $secret = getJwtSecret();

    $hash = hash('sha256', strtolower(trim($email)));
    $intValue = hexdec(substr($hash, 0, 8)); 
    $avatarId = $intValue % 1000;

    $payload = [
        'iss' => 'https://test.tptimovyprojekt.software/tp_timovy_projekt',
        'aud' => 'https://test.tptimovyprojekt.software/tp_timovy_projekt',
        'iat' => time(),
        'exp' => time() + 3600,
        'data' => [
            'id' => $userId,
            'username' => $username,
            'email' => $email,
            'avatarId' => $avatarId
        ],
    ];

    return JWT::encode($payload, $secret, 'HS256');
}


function validateToken() {
    $token = $_SESSION['token'] ?? null;

    if (!$token) {
        http_response_code(401);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to login first'];
        throw new Exception('You have to login first');
        exit();
    }

    try {
        $secret = getJwtSecret();
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));

        if (
            $decoded->iss !== 'https://test.tptimovyprojekt.software/tp_timovy_projekt' ||
            $decoded->aud !== 'https://test.tptimovyprojekt.software/tp_timovy_projekt'
        ) {
            throw new Exception('Invalid issuer or audience');
        }

        return (array)$decoded->data;
    } catch (Exception $e) {
        http_response_code(401);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Unauthorized: Invalid token'];
        throw new Exception('Unauthorized: Invalid token');
        exit();
    }
}

function isJson($string) {
    return ((is_string($string) &&
            (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
}
