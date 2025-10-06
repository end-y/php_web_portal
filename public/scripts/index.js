// Dropdown toggle functionality
document.addEventListener("DOMContentLoaded", function () {
  const dropdownButtons = document.querySelectorAll("[data-dropdown-toggle]");

  dropdownButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const targetId = this.getAttribute("data-dropdown-toggle");
      const dropdown = document.getElementById(targetId);

      if (dropdown) {
        dropdown.classList.toggle("hidden");
      }
    });
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (event) {
    dropdownButtons.forEach((button) => {
      const targetId = button.getAttribute("data-dropdown-toggle");
      const dropdown = document.getElementById(targetId);

      if (
        dropdown &&
        !button.contains(event.target) &&
        !dropdown.contains(event.target)
      ) {
        dropdown.classList.add("hidden");
      }
    });
  });
});

// Arama fonksiyonu - güvenli: debounce, history.replaceState, fetch ile JSON al
(function () {
  const input = document.getElementById("searchInput");
  const taskTableBody = document.getElementById("taskTable"); // ID'yi taskTable olarak değiştirdim
  let timer = null;
  const DEBOUNCE_MS = 300;

  function buildUrl(search) {
    const url = new URL(window.location.href);
    if (search && search.length) url.searchParams.set("search", search);
    else url.searchParams.delete("search");
    return url;
  }

  function renderAndUpdateTasks(tasks) {
    if (!taskTableBody) {
      console.error("taskTableBody elementi bulunamadı.");
      return;
    }

    // Mevcut tablo içeriğini temizle
    taskTableBody.innerHTML = "";

    // Görevleri tabloya ekle
    tasks.forEach((task) => {
      const row = document.createElement("tr");
      row.className = "border-b hover:bg-gray-50 transition"; // Mevcut arama fonksiyonundan alındı
      row.innerHTML = `
        <td>${task.task || ""}</td>
        <td>${task.title || ""}</td>
        <td>${task.description || ""}</td>
        <td>${task.task_date || ""}</td>
        <td><span class="px-2 py-1 rounded-full text-white" style="background-color: ${
          task.color_code || "#CCCCCC"
        };">${task.color_name || "N/A"}</span></td>
        <td>
          <button class="edit-task" data-id="${task.id}" data-title="${
        task.title
      }" data-description="${task.description}" data-color="${
        task.color_code
      }">Düzenle</button>
          <button class="delete-task" data-id="${task.id}">Sil</button>
        </td>
      `;
      taskTableBody.appendChild(row);
    });
  }

  async function fetchTasksAndRender() {
    try {
      const response = await fetch("/api/tasks");
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      renderAndUpdateTasks(data.tasks);
    } catch (error) {
      console.error("Görevler getirilirken bir hata oluştu:", error);
    }
  }

  // Mevcut arama fonksiyonundaki fetchAndUpdate çağrısını güncelledim
  async function fetchAndUpdate(search) {
    const url = new URL(window.location.origin + window.location.pathname);
    if (search && search.length) url.searchParams.set("search", search);
    url.searchParams.set("partial", "1");
    const res = await fetch(url.toString(), {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) return;
    const data = await res.json();
    renderAndUpdateTasks(Object.values(data.tasks) || []); // renderTasks yerine renderAndUpdateTasks kullandım
  }
  function updateUrl(search) {
    const urls = document.querySelectorAll("#urls");

    urls.forEach((url) => {
      const urlObject = new URL(url.href);
      if (search.length === 0) {
        urlObject.searchParams.delete("search");
        url.href = urlObject.href;
        return;
      }
      urlObject.searchParams.set("search", search);
      url.href = urlObject.href;
    });
  }
  input.addEventListener("input", function (e) {
    const q = e.target.value;
    clearTimeout(timer);
    timer = setTimeout(() => {
      const newUrl = buildUrl(q);
      history.replaceState(null, "", newUrl.toString());
      updateUrl(q);
      fetchAndUpdate(q).catch(console.error);
    }, DEBOUNCE_MS);
  });

  // İlk yüklemede görevleri getir
  fetchTasksAndRender();

  // Her 60 dakikada bir görevleri otomatik yenile (3600000 milisaniye = 60 dakika)
  setInterval(fetchTasksAndRender, 3600000);
})();
