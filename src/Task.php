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
  /**
   * Görevleri arama terimine göre filtreler.
   * Case-insensitive, null-safe ve boş arama teriminde tüm sonuçları döndürür.
   * 
   * @param string $search Arama terimi
   * @return array Filtrelenmiş görev listesi
   */
  public function filterBySearch(string $search): array {
    $tasks = $this->getTasks();
    
    // Boş arama terimi: tüm görevleri döndür
    $search = trim($search);
    if ($search === '') {
      return $tasks;
    }

    // Case-insensitive arama için küçük harfe çevir
    $searchLower = mb_strtolower($search, 'UTF-8');

    return array_filter($tasks, function($task) use ($searchLower) {
      // Her alanı güvenli şekilde al ve küçük harfe çevir
      $taskField = mb_strtolower((string)($task["task"] ?? ''), 'UTF-8');
      $titleField = mb_strtolower((string)($task["title"] ?? ''), 'UTF-8');
      $descField = mb_strtolower((string)($task["description"] ?? ''), 'UTF-8');

      // Herhangi bir alanda arama terimi geçiyorsa true döndür
      return mb_strpos($taskField, $searchLower) !== false 
          || mb_strpos($titleField, $searchLower) !== false 
          || mb_strpos($descField, $searchLower) !== false;
    });
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
  public function filterTasksByColor(string $color, array $tasks): array {
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