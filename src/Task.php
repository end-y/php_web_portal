<?php
namespace PHPWebPortal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Task {
  private Client $client;
  private User $user;

  public function __construct(User $user)
  {
    $this->user = $user;

    // .env dosyasını yükle (eğer henüz yüklenmemişse)
    if (!isset($_ENV["API_URL"])) {
      $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
      $dotenv->load();
    }

    $this->client = new Client([
        "base_uri" => $_ENV["API_URL"],
        "timeout" => 30
    ]);
  }
  public function getTasks(): array {
    if (!$this->user->isTokenValid()) {
      throw new \Exception("Token geçersiz veya süresi dolmuş. Lütfen tekrar login olun.");
    }

    $token = $this->user->getToken();
    
    try {
      $response = $this->client->request("GET", "/v1/tasks/select", [
          "headers" => [
              "Authorization" => "Bearer " . $token,
              "Content-Type" => "application/json"
          ]
      ]);
      return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
      throw new \Exception("Task'lar çekilirken hata oluştu: " . $e->getMessage());
    }
  }
  public function filterTasksByColor(string $color): array {
    $tasks = $this->getTasks();
    return array_filter($tasks, function($task) use ($color) {
      return $task["colorCode"] == $color;
    });
  }
  public function getColorTasks(): array {
    $tasks = $this->getTasks();

    // Renk kodlarını saymak için
    $colorCounts = [];
    foreach ($tasks as $task) {
      $colorCode = $task["colorCode"] ?? '';
      if ($colorCode) {
        $colorCounts[$colorCode] = ($colorCounts[$colorCode] ?? 0) + 1;
      }
    }

    // Her renk için adet bilgisi ile birlikte döndür
    $result = [];
    foreach ($colorCounts as $colorCode => $count) {
      $result[] = [
        "colorCode" => $colorCode,
        "amount" => $count
      ];
    }

    // Renk koduna göre sırala (opsiyonel)
    usort($result, function($a, $b) {
      return strcmp($a['colorCode'], $b['colorCode']);
    });

    return $result;
  }

}