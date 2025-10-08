<?php

use PHPWebPortal\Utils;

function renderTaskOrderList($sortype, $title): void {
    $currentSort = $_GET['sort'] ?? 'asc';
    $nextSort = $currentSort === 'asc' ? 'desc' : 'asc';
    $currentSortype = $_GET['sortype'] ?? '';
    $isActive = $currentSortype === $sortype;
    ?>
    <th id="urls-sort" class="px-4 py-5 text-left font-semibold text-gray-700 border-b border-gray-200">
        <a class="float-left mr-[5px]" href="<?php echo Utils::updateQueryParams(["sort" => $nextSort,"sortype" => $sortype]); ?>"><?php echo Utils::e($title); ?></a>
        <?php if($isActive): ?>
            <?php if($currentSort === 'asc'): ?>
                <svg class="w-3 h-3 text-gray-800 float-left mt-[5px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 8">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 5.326 5.7a.909.909 0 0 0 1.348 0L13 1"/>
                </svg>
            <?php else: ?>
                <svg class="w-3 h-3 text-gray-800 float-left mt-[5px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 8">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7 7.674 1.3a.91.91 0 0 0-1.348 0L1 7"/>
                </svg>
            <?php endif; ?>
        <?php endif; ?>
    </th>
    <?php
}

