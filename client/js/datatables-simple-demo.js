window.addEventListener("DOMContentLoaded", (event) => {
  // Simple-DataTables
  // https://github.com/fiduswriter/Simple-DataTables/wiki

  const datatablesSimple = document.getElementById("datatablesSimple");
  if (datatablesSimple) {
    new simpleDatatables.DataTable(datatablesSimple);
  }

  const datatablesCompleted = document.getElementById("datatablesCompleted");
  if (datatablesCompleted) {
    new simpleDatatables.DataTable(datatablesCompleted);
  }

  const datatablesThird = document.getElementById("datatablesThird");
  if (datatablesThird) {
    new simpleDatatables.DataTable(datatablesThird); // Corrected to use datatablesThird
  }
});
