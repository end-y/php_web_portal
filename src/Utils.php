<?php
namespace PHPWebPortal;

use PHPWebPortal\Controllers\TaskController;

class Utils {

	public static function currentUrl(): string {
		$isSecure = false;
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			$isSecure = true;
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
			$isSecure = true;
		} elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
			$isSecure = true;
		}

		$scheme = $isSecure ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
		$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

		return $scheme . '://' . $host . $requestUri;
	}
	public static function e(string $value): string {
		return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	public static function updateQueryParams(array $params): string {
		$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
		$parts = parse_url($requestUri);
		$path = $parts['path'] ?? '/';
		parse_str($parts['query'] ?? '', $currentQuery);

		if (isset($params['filter']) && !is_array($params['filter'])) {
			$params['filter'] = [$params['filter']];
		}

		if (isset($params['search'])) {
			$params['search'] = (string)$params['search'];
		}

		if (isset($params['sort'])) {
			$lower = strtolower((string)$params['sort']);
			$params['sort'] = $lower === 'asc' ? 'asc' : 'desc';
		}

		if (isset($params['sortype'])) {
			$allowed = ['title', 'description'];
			$params['sortype'] = in_array((string)$params['sortype'], $allowed, true) ? (string)$params['sortype'] : 'title';
		}

		foreach ($params as $key => $value) {
			if ($value === null) {
				unset($currentQuery[$key]);
				continue;
			}

			if ($key === 'filter') {
				$currentQuery[$key] = $value;
				continue;
			}

			$currentQuery[$key] = $value;
		}

		$newQuery = http_build_query($currentQuery);
		return $path . ($newQuery !== '' ? '?' . $newQuery : '');
	}
	/**
 * Görevleri URL parametrelerine göre filtreler ve sıralar
 * 
 * @param TaskController $controller
 * @param array $tasks Başlangıç görev listesi
 * @return array Filtrelenmiş ve sıralanmış görevler
 */
public static function applyFiltersAndSort(TaskController $controller, array $tasks): array {
	$result = $tasks;
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $result = array_values(array_filter($result, function($task) {
            $search = trim($_GET['search']);
            $searchLower = mb_strtolower($search, 'UTF-8');
            $taskField = mb_strtolower((string)($task["task"] ?? ''), 'UTF-8');
            $titleField = mb_strtolower((string)($task["title"] ?? ''), 'UTF-8');
            $descField = mb_strtolower((string)($task["description"] ?? ''), 'UTF-8');
            return mb_strpos($taskField, $searchLower) !== false 
                || mb_strpos($titleField, $searchLower) !== false 
                || mb_strpos($descField, $searchLower) !== false;
        }));
    }
	if (isset($_GET['color']) && $_GET['color'] !== '') {
        $result = $controller->filterTasksByColor($_GET['color'], $result);
    }
    if (isset($_GET['sort']) && isset($_GET['sortype'])) {
        $result = $controller->sortList($result, $_GET['sort'], $_GET['sortype']);
    }
    
    return $result;
}
}
