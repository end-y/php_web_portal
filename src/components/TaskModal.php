<?php

use PHPWebPortal\Utils;

/**
 * TaskModal Component
 * Dışarıda bir buton render eder; butona tıklanınca modal açılır.
 * Modal içinde dosya seçici ile resim seçilip önizlemesi gösterilir.
 * Bu bileşen mevcut veri ile ilişkilendirilmemiştir.
 */
function renderTaskModal(): void {
    ?>
    <div class="mb-4">
        <!-- Açma butonu -->
        <button id="openTaskModal" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Resim Seç ve Önizle</button>

        <!-- Modal -->
        <div id="taskModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Choose Image and Preview</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <button id="chooseImageButton" class="bg-gray-100 border border-gray-300 px-3 py-2 rounded-md">Dosya Seç</button>
                        <input id="imageInput" type="file" accept="image/*" class="hidden">
                    </div>

                    <div id="previewWrapper" class="hidden">
                        <p class="text-sm text-gray-500 mb-2">Selected image preview:</p>
                        <div class="border border-gray-200 rounded-md p-2">
                            <img id="imagePreview" src="" alt="Seçilen resim" class="max-h-64 w-auto mx-auto block" />
                        </div>
                    </div>

                    <div class="text-right">
                        <button id="closeTaskModalFooter" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}


