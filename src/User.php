<?php
namespace PHPWebPortal;

use GuzzleHttp\Client;
use Dotenv\Dotenv;
class User {
  private Client $client;
  private ?string $token = null;

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      // Session klasörünü proje içinde ayarla
      $sessionPath = __DIR__ . '/../storage/sessions';
      if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0777, true);
      }
      session_save_path($sessionPath);
      session_start();
    }

    // .env dosyasını yükle
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $this->client = new Client([
        "base_uri" => $_ENV["API_URL"],
        "timeout" => 30
    ]);
  }
  public function login(string $username, string $password) {
    $response = $this->client->request("POST", "/login", [
        "headers" => [
            "Authorization" => "Basic " . $_ENV["AUTH_KEY"],
            "Content-Type" => "application/json"
        ],
        "json" => [
            "username" => $username,
            "password" => $password
        ]
    ]);
    $data = json_decode($response->getBody(), true);
    if(isset($data["oauth"]["access_token"])) {
      $this->token = $data["oauth"]["access_token"];
      $_SESSION['access_token'] = $data["oauth"]["access_token"];
      $_SESSION['token_expires_at'] = time() + ($data["oauth"]["expires_in"] ?? 1200);
    }
    return $data;
  }
  public function getToken(): ?string {
    return $_SESSION['access_token'] ?? $this->token;
  }
  public function isTokenValid(): bool {
    if (!isset($_SESSION['access_token']) || !isset($_SESSION['token_expires_at'])) {
      return false;
    }
    return time() < $_SESSION['token_expires_at'];
  }
  public function logout(): void {
    unset($_SESSION['access_token']);
    unset($_SESSION['token_expires_at']);
    $this->token = null;
  }
}