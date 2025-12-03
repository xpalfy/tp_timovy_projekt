<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../checkType.php';
use Firebase\JWT\JWT;


// Setup Google Client
$client = new Google\Client();
try {
    $client->setAuthConfig('credentials.json');
} catch (\Google\Exception $e) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Google Client error: ' . $e->getMessage()];
    header('Location: ../login.php');
    exit();
}

$client->setRedirectUri("https://test.egytolnyolcig.uk/tp_timovy_projekt/Google/redirect.php");
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        $oauth = new Google\Service\Oauth2($client);
        $account_info = $oauth->userinfo->get();

        if ($account_info) {
            $g_email = $account_info->email;
            $g_name = $account_info->givenName;
            $g_fullname = $account_info->name;

            $_SESSION['email'] = $g_email;
            $_SESSION['fullname'] = $g_fullname;
            $_SESSION['name'] = $g_name;
            $_SESSION['access_token'] = $token['access_token'];

            $conn = getDatabaseConnection();

            // Check if user already exists
            $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
            $stmt->bind_param('s', $g_email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                // Existing user
                $stmt->bind_result($userId, $username);
                $stmt->fetch();
            } else {
                // Register new user
                $stmt->close();
                $username = strtolower($g_name) . rand(1000, 9999);

                $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, NULL)");
                $insert->bind_param('ss', $username, $g_email);

                if ($insert->execute()) {
                    $userId = $insert->insert_id;
                    $insert->close();
                } else {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => 'There was an error while creating your account.'];
                    header('Location: ../login.php');
                    exit();
                }
            }

            $conn->close();

            // Generate and store JWT token
            $jwt = generateToken($userId, $username, $g_email);
            $_SESSION['token'] = $jwt;

            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Login through Google was successful.'];
            header('Location: ../logged_in/main.php');
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Google login error: ' . $e->getMessage()];
        header('Location: ../login.php');
        exit();
    }
} else {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'No code received from Google.'];
    header('Location: ../login.php');
    exit();
}
