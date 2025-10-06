(function () {
  const openBtn = document.getElementById("openTaskModal");
  const modal = document.getElementById("taskModal");
  const closeFooter = document.getElementById("closeTaskModalFooter");
  const chooseBtn = document.getElementById("chooseImageButton");
  const fileInput = document.getElementById("imageInput");
  const previewWrapper = document.getElementById("previewWrapper");
  const previewImg = document.getElementById("imagePreview");

  function openModal() {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  }

  function closeModal() {
    modal.classList.remove("flex");
    modal.classList.add("hidden");
  }

  openBtn.addEventListener("click", openModal);
  closeFooter.addEventListener("click", closeModal);

  // dışarı tıklayınca modal kapat
  modal.addEventListener("click", function (e) {
    if (e.target === modal) closeModal();
  });

  chooseBtn.addEventListener("click", function () {
    fileInput.click();
  });

  fileInput.addEventListener("change", function (e) {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
      previewImg.src = ev.target.result;
      previewWrapper.classList.remove("hidden");
    };
    reader.readAsDataURL(file);
  });
})();
