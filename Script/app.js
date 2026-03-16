// navbar
function loadNavbar() {
  fetch("navbar.php", { cache: "no-store" })
    .then(response => response.text())
    .then(data => {
      document.getElementById("navbar-container").innerHTML = data;
    })
    .catch(error => console.error("Error loading navbar:", error));
}

document.addEventListener("DOMContentLoaded", () => {
  // mobile nav toggle
  const toggleBtn = document.querySelector(".nav-toggle");
  const navLinks = document.querySelector(".nav-links");

  if (toggleBtn && navLinks) {
    toggleBtn.addEventListener("click", () => {
      const isOpen = navLinks.classList.toggle("is-open");
      toggleBtn.setAttribute("aria-expanded", String(isOpen));
    });
  }

  // chart.js trend chart
  // reference: https://www.chartjs.org/docs/latest/charts/line.html
  const canvas = document.getElementById("trendChart");
  if (!canvas || typeof Chart === "undefined") return; // making sure canvas exist and chart.js is loaded

  new Chart(canvas, {
    type: "line",
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
      datasets: [
        {
          label: "Sightings",
          data: [120, 190, 300, 500, 420, 610, 720],
          tension: 0.4, // makes the line curved
          fill: true,
          borderColor: "#2c7da0",
          backgroundColor: "rgba(44,125,160,0.2)",
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: { beginAtZero: true },
      },
    },
  });
});

// drag and drop upload
// reference: https://css-tricks.com/drag-and-drop-file-uploading/ and https://developer.mozilla.org/en-US/docs/Web/API/DataTransfer/files

document.addEventListener("DOMContentLoaded", () => {
  const uploadBox = document.getElementById("uploadBox");
  const fileInput = document.getElementById("csv-file-upload");

  if (uploadBox && fileInput) { // checking both exists
    uploadBox.addEventListener("dragover", (e) => {
      e.preventDefault();
      uploadBox.classList.add("dragover");
    });
    uploadBox.addEventListener("dragleave", () => {
      uploadBox.classList.remove("dragover"); // removing default browser behaviour (blocks dropping files)
    });
    uploadBox.addEventListener("drop", (e) => {
      e.preventDefault();
      uploadBox.classList.remove("dragover");
      const files = e.dataTransfer.files; // getting the files that were dragged and dropped
      if (files.length > 0) { // checking atleast 1 file is dropped
        fileInput.files = files;
      }
    });
  }

});

// delete popup confirmation
document.addEventListener("DOMContentLoaded", function () {

  const popup = document.getElementById("rejectPopup");
  if (!popup) return;

  const confirmBtn = popup.querySelector(".confirm-reject");
  const cancelBtn = popup.querySelector(".cancel-reject");

  let currentForm = null;

  document.querySelectorAll("input[name='reject']").forEach(btn => {
    btn.addEventListener("click", function () {
        currentForm = this.closest("form");
        popup.style.display = "flex";
    });
  });

  // confirm delete
  confirmBtn.addEventListener("click", function () {

      if (!currentForm) return;
      const hiddenReject = document.createElement("input");
      hiddenReject.type = "hidden";
      hiddenReject.name = "reject";
      hiddenReject.value = "1";

      currentForm.appendChild(hiddenReject);

      popup.style.display = "none";
      currentForm.submit();
  });

  // cancel delete
  cancelBtn.addEventListener("click", function () {
      popup.style.display = "none";
      currentForm = null;
  });

});