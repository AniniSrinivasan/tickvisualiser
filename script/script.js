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
  })
})
//Bar chart 
document.addEventListener("DOMContentLoaded", () => {
  const barCanvas = document.getElementById("BarChart");
  if (!barCanvas) return;

  fetch('../functions/chart-functions.php')
    .then(response => response.json())
    .then(data => createChart(barCanvas, data))
    .catch(err => console.error('Error fetching chart data:', err));
});

function createChart(canvas, chartData) {
  new Chart(canvas, {
    type: 'bar',
    data: {
      // labels: chartData.map(row => `${row.species_name} (${row.species_latin_name})`),
      labels: chartData.map(row => row.species_name),
      datasets: [{
        label: 'Sightings',
        data: chartData.map(row => row.sighting_count),
        backgroundColor: 'rgba(44,125,160,0.6)',
        borderColor: '#2c7da0',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: true, text: 'Tick Sightings by Species' }
      },
      scales: {
        x: {
          title: { display: true, text: 'Species' },
          ticks: {
            maxRotation: 45,
            minRotation: 30
          }
          },
        y: {
          title: { display: true, text: 'Number of Sightings' },
          beginAtZero: true,
          ticks: {
            precision: 0 // ensures whole numbers
          }
        }
      }
    }
  });
}


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

// delete/reject popup confirmation
document.addEventListener("DOMContentLoaded", function () {

  const popup = document.getElementById("popup-confirmation");
  if (!popup) return;

  const confirmBtn = popup.querySelector(".confirm");
  const cancelBtn = popup.querySelector(".cancel");

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

// upload file function 
document.addEventListener('DOMContentLoaded', function () {
  const fileUpload = document.getElementById('fileUpload');
  const uploadDropCard = document.getElementById('uploadDropCard');
  const uploadError = document.getElementById('uploadError');
  const csvUploadForm = document.getElementById('csvUploadForm');

  if (!fileUpload || !uploadDropCard || !uploadError || !csvUploadForm) {
    return;
  }

  function isCsvFile(file) {
    if (!file) return false;
    return file.name.toLowerCase().endsWith('.csv'); // checks if csv
  }

  function showError(message) {
    uploadError.textContent = message;
    uploadError.style.display = 'block';
  }

  function hideError() {
    uploadError.textContent = '';
    uploadError.style.display = 'none';
  }

  // validates the uploaded file before submitting it
  function validateAndSubmit(fileCollection) {
    const files = Array.from(fileCollection || []);

    if (!files.length) {
      return;
    }

    if (files.length > 1) { // to make surre only 1 file is uploaded
      showError('Please upload one CSV file at a time.');
      fileUpload.value = '';
      return;
    }

    const hasInvalidFile = files.some((file) => !isCsvFile(file));
    if (hasInvalidFile) {
      showError('Only CSV files can be attached.');
      fileUpload.value = '';
      return;
    }

    hideError();
    csvUploadForm.submit();
  }

  fileUpload.addEventListener('change', function (event) {
    validateAndSubmit(event.target.files);
  });

  // for the drag and drop option
  uploadDropCard.addEventListener('dragover', (event) => {
    event.preventDefault();
    event.stopPropagation();
    uploadDropCard.classList.add('drag-active');
  });

  uploadDropCard.addEventListener('dragleave', (event) => {
    event.preventDefault();
    event.stopPropagation();
    uploadDropCard.classList.remove('drag-active');
  });

  // retrieves dropped file, assigns it to file input, sends for validation and upload
  uploadDropCard.addEventListener('drop', (event) => {
    event.preventDefault();
    event.stopPropagation();
    uploadDropCard.classList.remove('drag-active');

    const files = event.dataTransfer ? event.dataTransfer.files : null;
    if (!files || !files.length) {
      return;
    }

    fileUpload.files = files;
    validateAndSubmit(files);
  });
});

// search filter for the search in browse data
function searchBrowswData(input) {
  const query = input.value.toLowerCase();
  const tableRows = document.querySelectorAll(".manage-table tbody tr");

  tableRows.forEach(row => {
    const dataID = row.cells[0].textContent.toLowerCase();
    const location = row.cells[2].textContent.toLowerCase();
    const speciesName = row.cells[3].textContent.toLowerCase();
    const latinName = row.cells[4].textContent.toLowerCase();

    if (dataID.includes(query) || location.includes(query) ||  speciesName.includes(query) ||  latinName.includes(query)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}