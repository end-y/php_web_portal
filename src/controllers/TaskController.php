<?php

namespace PHPWebPortal\Controllers;

use PHPWebPortal\User;
use PHPWebPortal\Task;

class TaskController
{
    private User $user;
    private Task $task;

    public function __construct()
    {
        $this->user = new User();
        $this->validateAuth();
        $this->task = new Task($this->user);
    }
    private function validateAuth(): void
    {
        if (!$this->user->isTokenValid()) {
            header("Location: /login");
            exit;
        }
    }
    public function index(): array
    {
        return [
            'tasks' => $this->task->getTasks(),
            'colorTasks' => $this->task->getColorTasks(),
            'title' => 'Task List',
            'showNav' => true,      
            'isLoggedIn' => true
        ];
    }
    public function filterBySearch(string $search): array {
        return $this->task->filterBySearch($search);
    }
    public function filterTasksByColor(string $color, array $tasks): array {
        return $this->task->filterTasksByColor($color, $tasks);
    }
    public function sortList(array $tasks, string $sort, string $sortype): array {
        $allowedSort = ['asc', 'desc'];
        $allowedSortype = ['title', 'description', 'task'];
        
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'asc';
        }
        if (!in_array($sortype, $allowedSortype, true)) {
            $sortype = 'title';
        }

        usort($tasks, function($a, $b) use ($sort, $sortype) {
            $valA = mb_strtolower((string)($a[$sortype] ?? ''), 'UTF-8');
            $valB = mb_strtolower((string)($b[$sortype] ?? ''), 'UTF-8');
            
            $comparison = strcmp($valA, $valB);
            return $sort === 'desc' ? -$comparison : $comparison;
        });

        return $tasks;
    }

    public static function getColorClass(string $colorCode): string
    {
        $text = $colorCode ? "#ffffff" : "#000000";
        return "text-[$text] bg-[$colorCode]";
    }
}

