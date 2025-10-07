<?php

use PHPWebPortal\Utils;

/**
 * TaskColorList Component
 * Görev renk listesini dropdown olarak gösterir
 * 
 * @param array $colorTasks Renk verileri dizisi
 * @return void
 */
function renderTaskColorList(array $colorTasks): void {
    ?>
    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b border-gray-200">
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center border border-gray-300" type="button">
            Tasks <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        <div id="dropdown" class="absolute z-10 mt-2 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-32 border border-gray-200">
            <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
                <?php if(empty($colorTasks)): ?>
                    <li>
                        <span class="block px-4 py-2 text-gray-500">Henüz renk verisi yok</span>
                    </li>
                <?php else: ?>
                    <?php foreach($colorTasks as $colorTask): ?>
                        <?php if(!empty($colorTask["colorCode"])): ?>
                            <li>
                                <a id="urls" href="<?php echo Utils::updateQueryParams(["color" => $colorTask["colorCode"]]); ?>" class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-md border border-gray-300" style="background-color: <?php echo Utils::e($colorTask["colorCode"]); ?>;"></div>
                                    <span class="font-medium">Tasks</span>
                                    <span class="ml-auto text-xs bg-gray-100 px-2 py-1 rounded-full"><?php echo (int)$colorTask["amount"]; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </th>
    <?php
}
