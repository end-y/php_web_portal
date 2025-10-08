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
  const tableBody = document.getElementById("taskTable");
  let timer = null;
  const DEBOUNCE_MS = 300;

  function buildUrl(search) {
    const url = new URL(window.location.href);
    if (search && search.length) url.searchParams.set("search", search);
    else url.searchParams.delete("search");
    return url;
  }

  function renderTasks(tasks) {
    while (tableBody.firstChild) tableBody.removeChild(tableBody.firstChild);
    tasks.forEach((task) => {
      const tr = document.createElement("tr");
      tr.className = "border-b border-gray-200 hover:bg-gray-50 transition";

      const td1 = document.createElement("td");
      td1.className = "px-4 py-3";
      if (task.colorCode) {
        td1.style.color = "#ffffff";
        td1.style.backgroundColor = task.colorCode;
      }
      td1.textContent = task.task || "";

      const td2 = document.createElement("td");
      td2.className = "px-4 py-3";
      if (task.colorCode) {
        td2.style.color = "#ffffff";
        td2.style.backgroundColor = task.colorCode;
      }
      td2.textContent = task.title || "";

      const td3 = document.createElement("td");
      td3.className = "px-4 py-3";
      if (task.colorCode) {
        td3.style.color = "#ffffff";
        td3.style.backgroundColor = task.colorCode;
      }
      td3.textContent = task.description || "";

      tr.appendChild(td1);
      tr.appendChild(td2);
      tr.appendChild(td3);
      tableBody.appendChild(tr);
    });
  }

  async function fetchAndUpdate(search) {
    const url = new URL(window.location.origin + window.location.pathname);
    if (search && search.length) url.searchParams.set("search", search);
    url.searchParams.set("partial", "1");
    const res = await fetch(url.toString(), {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) return;
    const data = await res.json();
    renderTasks(Object.values(data.tasks) || []);
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
})();

(function () {
  const AUTO_REFRESH_INTERVAL = 60 * 60 * 1000;

  async function autoRefreshTable() {
    try {
      // Mevcut URL parametrelerini al
      const currentUrl = new URL(window.location.href);
      const searchParam = currentUrl.searchParams.get("search") || "";
      const colorParam = currentUrl.searchParams.get("color") || "";
      const sortParam = currentUrl.searchParams.get("sort") || "";
      const sortypeParam = currentUrl.searchParams.get("sortype") || "";

      // API URL'ini oluştur
      const apiUrl = new URL(window.location.origin + window.location.pathname);
      if (searchParam) apiUrl.searchParams.set("search", searchParam);
      if (colorParam) apiUrl.searchParams.set("color", colorParam);
      if (sortParam) apiUrl.searchParams.set("sort", sortParam);
      if (sortypeParam) apiUrl.searchParams.set("sortype", sortypeParam);
      apiUrl.searchParams.set("partial", "1");

      // Veriyi fetch et
      const response = await fetch(apiUrl.toString(), {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      if (!response.ok) {
        console.error("Auto-refresh failed:", response.status);
        return;
      }

      const data = await response.json();
      const tableBody = document.getElementById("taskTable");

      // Tabloyu güncelle
      if (tableBody && data.tasks) {
        while (tableBody.firstChild)
          tableBody.removeChild(tableBody.firstChild);

        const tasks = Object.values(data.tasks);
        tasks.forEach((task) => {
          const tr = document.createElement("tr");
          tr.className = "border-b border-gray-200 hover:bg-gray-50 transition";

          const td1 = document.createElement("td");
          td1.className = "px-4 py-3";
          if (task.colorCode) {
            td1.style.color = "#ffffff";
            td1.style.backgroundColor = task.colorCode;
          }
          td1.textContent = task.task || "";

          const td2 = document.createElement("td");
          td2.className = "px-4 py-3";
          if (task.colorCode) {
            td2.style.color = "#ffffff";
            td2.style.backgroundColor = task.colorCode;
          }
          td2.textContent = task.title || "";

          const td3 = document.createElement("td");
          td3.className = "px-4 py-3";
          if (task.colorCode) {
            td3.style.color = "#ffffff";
            td3.style.backgroundColor = task.colorCode;
          }
          td3.textContent = task.description || "";

          tr.appendChild(td1);
          tr.appendChild(td2);
          tr.appendChild(td3);
          tableBody.appendChild(tr);
        });
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  // İlk yükleme sonrası 60 dakikada bir çalıştır
  setInterval(autoRefreshTable, AUTO_REFRESH_INTERVAL);
})();
