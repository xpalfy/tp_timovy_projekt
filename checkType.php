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

function validateToken() {
    $token = $_SESSION['token'] ?? null;

    if (!$token) {
        http_response_code(401);
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Unauthorized: Token not found'];
        header('Location: login.php');
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
        header('Location: ../login.php');
        exit();
    }
}
