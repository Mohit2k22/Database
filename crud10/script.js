document.addEventListener("DOMContentLoaded", () => {
  const search = document.querySelector("[data-table-search]");
  const table = document.querySelector("[data-search-table]");
  if (search && table) {
    search.addEventListener("input", () => {
      const q = search.value.toLowerCase().trim();
      table.querySelectorAll("tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? "" : "none";
      });
    });
  }
  document.querySelectorAll("[data-confirm]").forEach(btn => {
    btn.addEventListener("click", e => {
      if (!confirm(btn.dataset.confirm)) e.preventDefault();
    });
  });
});
