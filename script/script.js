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
    .then(data => {
      const Xdata = data.map(row => row.species_name);
      const Ydata = data.map(row => row.sighting_count);
      const type = 'bar';
      const Xlabel = 'Species';
      const Ylabel = 'Sightings';
      const title = 'Tick Sightings by Species';
      createChart(barCanvas, data, Xdata, Ydata, Xlabel, Ylabel, title, type);
    })
    .catch(err => console.error('Error fetching chart data:', err));
});

//Monthly Trend chart 
document.addEventListener("DOMContentLoaded", () => {
  const monthlyTrendCanvas = document.getElementById("MonthlyTrendChart");
  if (!monthlyTrendCanvas) return;

  // Use data-range if available, otherwise default to 0
  const range = monthlyTrendCanvas.dataset.range || 0;

  fetch(`../functions/monthly-trend-function.php?months=${range}`)
    .then(response => response.json())
    .then(data => {

      const Xdata = data.map(row => row.month);
      const Ydata = data.map(row => row.sighting_count);
      const type = 'line';
      const Xlabel = 'Months';
      const Ylabel = 'Monthly Sightings';
      const title = 'Monthly Tick Sightings';
      createChart(monthlyTrendCanvas, data, Xdata, Ydata, Xlabel, Ylabel, title, type,range);
    })
    .catch(err => console.error('Error fetching chart data:', err));
});

function createChart(canvas, chartData, Xdata, Ydata, Xlabel, Ylabel, title, type, range) {
  new Chart(canvas, {
    type: type || 'line', // default to line if type not provided
    data: {
      labels: Xdata,
      datasets: [{
        data: Ydata,
        tension: 0.4, // makes the line curved
        fill: type === 'line' ? true : false, // only fill for line charts
        borderColor: "#2c7da0",
        backgroundColor: "rgba(44,125,160,0.2)",
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: false, text: [title,"(All Documented Years)" ] }
      },
      scales: {
        x: {
          title: { display: true, text: Xlabel },
          ticks: {
            maxRotation: 45,
            minRotation: 30
          }
        },
        y: {
          title: { display: true, text: Ylabel },
          // min: 0, // can be set to 0 if you want the y-axis to always start at 0
          suggestedMin: Math.min(...Ydata) - 5, // adds some space below the shortest bar/point
          suggestedMax: Math.max(...Ydata) + 5, // adds some space above the tallest bar/point
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

    if (dataID.includes(query) || location.includes(query) || speciesName.includes(query) || latinName.includes(query)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

function escapeHtml(value) {
  const div = document.createElement("div");
  div.textContent = value ?? "";
  return div.innerHTML;
}

function clearRowStatus(row) {
  if (!row) return;

  const existingStatus = row.querySelector(".row-status-message");
  if (existingStatus) {
    existingStatus.remove();
  }
}

function showRowStatus(row, message, type = "error") {
  if (!row) return;

  clearRowStatus(row);

  const actionCell = row.querySelector(".col-action");
  if (!actionCell) return;

  const status = document.createElement("span");
  status.className = `row-status-message row-status-${type}`;
  status.style.marginLeft = "8px";
  status.style.fontSize = "18px";
  status.style.verticalAlign = "middle";
  status.style.fontWeight = "bold";
  status.title = message;
  status.textContent = type === "error" ? "⚠" : "✓";

  actionCell.appendChild(status);

  setTimeout(() => clearRowStatus(row), 2000);
}

function enableInlineEditFromRow(row) {
  if (!row || row.dataset.editing === "1") return;

  const editButton = row.querySelector(".approve-button-in-list");
  if (!editButton) return;

  enableInlineEdit(editButton);
}

document.addEventListener("DOMContentLoaded", function () {
  const tableRows = document.querySelectorAll(".manage-table tbody tr");

  tableRows.forEach(row => {
    row.addEventListener("dblclick", function (event) {
      const clickedElement = event.target;

      if (
        clickedElement.closest("button") ||
        clickedElement.closest("input") ||
        clickedElement.closest("select") ||
        clickedElement.closest("textarea")
      ) {
        return;
      }

      enableInlineEditFromRow(row);
    });
  });
});

function enableInlineEdit(button) {
  const row = button.closest("tr");
  if (!row || row.dataset.editing === "1") return;

  row.dataset.editing = "1";
  clearRowStatus(row);

  const id = row.querySelector(".col-id").textContent.trim();
  const date = row.querySelector(".col-date").textContent.trim();
  const location = row.querySelector(".col-location").textContent.trim();
  // const county = row.querySelector(".col-county").textContent.trim();
  const species = row.querySelector(".col-species").textContent.trim();
  const latin = row.querySelector(".col-latin").textContent.trim();

  row.dataset.originalId = id;
  row.dataset.originalDate = date;
  row.dataset.originalLocation = location;
  // row.dataset.originalCounty = county;
  row.dataset.originalSpecies = species;
  row.dataset.originalLatin = latin;

  row.querySelector(".col-id").innerHTML =
    `<input type="text" value="${escapeHtml(id)}">`;

  row.querySelector(".col-date").innerHTML =
    `<input type="text" value="${escapeHtml(date)}">`;

  row.querySelector(".col-location").innerHTML =
    `<input type="text" value="${escapeHtml(location)}">`;

  // row.querySelector(".col-county").innerHTML =
  //   `<input type="text" value="${escapeHtml(county)}">`;

  row.querySelector(".col-species").innerHTML =
    `<input type="text" value="${escapeHtml(species)}">`;

  row.querySelector(".col-latin").innerHTML =
    `<input type="text" value="${escapeHtml(latin)}">`;

  row.querySelector(".col-action").innerHTML = `
    <button type="button" class="approve-button-in-list" onclick="saveInlineEdit(this)">Save</button>
    <button type="button" class="reject-button-in-list" onclick="cancelInlineEdit(this)">Cancel</button>
  `;

  const editInputs = row.querySelectorAll("input");

  editInputs.forEach(input => {
    input.addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        const saveButton = row.querySelector(".approve-button-in-list");
        if (saveButton) saveInlineEdit(saveButton);
      }

      if (event.key === "Escape") {
        event.preventDefault();
        const cancelButton = row.querySelector(".reject-button-in-list");
        if (cancelButton) cancelInlineEdit(cancelButton);
      }
    });
  });
}

function cancelInlineEdit(button) {
  const row = button.closest("tr");
  if (!row) return;

  row.querySelector(".col-id").textContent = row.dataset.originalId || "";
  row.querySelector(".col-date").textContent = row.dataset.originalDate || "";
  row.querySelector(".col-location").textContent = row.dataset.originalLocation || "";
  // row.querySelector(".col-county").textContent = row.dataset.originalCounty || "";
  row.querySelector(".col-species").textContent = row.dataset.originalSpecies || "";
  row.querySelector(".col-latin").textContent = row.dataset.originalLatin || "";

  row.querySelector(".col-action").innerHTML = `
    <button type="button" class="approve-button-in-list" onclick="enableInlineEdit(this)">Edit</button>
    <button type="button" class="reject-button-in-list" onclick="openDeletePopup(this)">Delete</button>
    <button type="button" class="approve-button" onclick="moveToSightings(this)">Approve</button>
  `;

  row.dataset.editing = "0";
  clearRowStatus(row);
}

async function saveInlineEdit(button) {
  const row = button.closest("tr");
  if (!row) return;

  const table = row.closest(".manage-table");
  const uploadId = table ? table.dataset.uploadId : "";
  const rowNum = row.dataset.tickid;

  if (!rowNum) {
    showRowStatus(row, "Missing row id.", "error");
    return;
  }

  if (!uploadId) {
    showRowStatus(row, "Missing upload id.", "error");
    return;
  }

  const id = row.querySelector(".col-id input").value.trim();
  const dateTime = row.querySelector(".col-date input").value.trim();
  const location = row.querySelector(".col-location input").value.trim();
  const species = row.querySelector(".col-species input").value.trim();
  const latinName = row.querySelector(".col-latin input").value.trim();

  const formData = new FormData();
  formData.append("ajax_action", "save-row");
  formData.append("upload_id", uploadId);
  formData.append("row_num", rowNum);
  formData.append("row_id", id);
  formData.append("date_time", dateTime);
  formData.append("location_name", location);
  formData.append("species_name", species);
  formData.append("species_latin_name", latinName);

  const checkbox = document.getElementById('show-inaccurate-only');
  const showInaccurateOnly = checkbox && checkbox.checked ? 1 : 0;
  formData.append("show-inaccurate-only", showInaccurateOnly);

  try {
    const response = await fetch("../functions/upload-functions.php", {
      method: "POST",
      body: formData
    });

    const result = await response.json();

    if (!result.success) {
      showRowStatus(row, result.message || "Unable to update row.", "error");
      return;
    }

    row.querySelector(".col-id").textContent = id;
    row.querySelector(".col-date").textContent = dateTime;
    row.querySelector(".col-location").textContent = location;
    row.querySelector(".col-species").textContent = species;
    row.querySelector(".col-latin").textContent = latinName;

    row.querySelector(".col-action").innerHTML = `
      <button type="button" class="approve-button-in-list" onclick="enableInlineEdit(this)">Edit</button>
      <button type="button" class="reject-button-in-list" onclick="openDeletePopup(this)">Delete</button>
      <button type="button" class="approve-button" onclick="moveToSightings(this)">Approve</button>
    `;

    row.dataset.editing = "0";

    delete row.dataset.originalId;
    delete row.dataset.originalDate;
    delete row.dataset.originalLocation;
    delete row.dataset.originalSpecies;
    delete row.dataset.originalLatin;

    showRowStatus(row, "Row updated successfully.", "success");
  } catch (error) {
    showRowStatus(row, "Something went wrong while saving.", "error");
    console.error(error);
  }
}

let currentDeleteRow = null;

function openDeletePopup(button) {
  const popup = document.getElementById("popup-confirmation");
  if (!popup) return;

  currentDeleteRow = button.closest("tr");
  popup.style.display = "flex";
}

async function deleteInlineRow(row) {
  if (!row) return;

  const table = row.closest(".manage-table");
  const uploadId = table ? table.dataset.uploadId : "";
  const rowNum = row.dataset.tickid;

  if (!rowNum) {
    showRowStatus(row, "Missing row id.", "error");
    return;
  }

  if (!uploadId) {
    showRowStatus(row, "Missing upload id.", "error");
    return;
  }

  const formData = new FormData();
  formData.append("ajax_action", "delete-row");
  formData.append("upload_id", uploadId);
  formData.append("row_num", rowNum);

  const checkbox = document.getElementById('show-inaccurate-only');
  const showInaccurateOnly = checkbox && checkbox.checked ? 1 : 0;
  formData.append("show-inaccurate-only", showInaccurateOnly);

  try {
    const response = await fetch("../functions/upload-functions.php", {
      method: "POST",
      body: formData
    });

    const result = await response.json();

    if (!result.success) {
      showRowStatus(row, result.message || "Unable to delete row.", "error");
      return;
    }

    row.remove();
  } catch (error) {
    showRowStatus(row, "Something went wrong while deleting.", "error");
    console.error(error);
  }
}
async function moveToSightings(button) {
  const row = button.closest("tr");
  if (!row) return;

  const table = row.closest(".manage-table");
  const uploadId = table ? table.dataset.uploadId : "";
  const rowNum = row.dataset.tickid;

  if (!uploadId || !rowNum) {
    showRowStatus(row, "Missing upload or row ID.", "error");
    return;
  }

  const formData = new FormData();
  formData.append("ajax_action", "approve-row");
  formData.append("upload_id", uploadId);
  formData.append("row_num", rowNum);

  formData.append("row_id", row.querySelector(".col-id").textContent.trim());
  formData.append("date_time", row.querySelector(".col-date").textContent.trim());
  formData.append("location_name", row.querySelector(".col-location").textContent.trim());
  formData.append("species_name", row.querySelector(".col-species").textContent.trim());
  formData.append("species_latin_name", row.querySelector(".col-latin").textContent.trim());

  const checkbox = document.getElementById('show-inaccurate-only');
  const showInaccurateOnly = checkbox && checkbox.checked ? 1 : 0;
  formData.append("show-inaccurate-only", showInaccurateOnly);

  try {
    const res = await fetch("../functions/upload-functions.php", {
      method: "POST",
      body: formData
    });

    const result = await res.json();

    if (!result.success) {
      showRowStatus(row, result.message || "Approval failed.", "error");
      return;
    }
    showRowStatus(row, "Moved to sightings.", "success");
    setTimeout(() => {
      row.remove();
    }, 500);

  } catch (err) {
    console.error(err);
    showRowStatus(row, "Server error.", "error");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const popup = document.getElementById("popup-confirmation");
  if (!popup) return;

  const confirmBtn = popup.querySelector(".confirm");
  const cancelBtn = popup.querySelector(".cancel");

  confirmBtn.addEventListener("click", function () {
    popup.style.display = "none";

    if (currentDeleteRow) {
      deleteInlineRow(currentDeleteRow);
      currentDeleteRow = null;
    }
  });

  cancelBtn.addEventListener("click", function () {
    popup.style.display = "none";
    currentDeleteRow = null;
  });
});

// for map functionality
document.addEventListener("DOMContentLoaded", function () {
  const mapElement = document.getElementById("map");

  // stop if this page does not contain the map
  if (!mapElement) {
    return;
  }

  if (typeof L === "undefined") {
    console.error("Leaflet did not load.");
    return;
  }

  const densityByName = window.densityByName || {};

  const ukBounds = [
    [49.5, -8.8],
    [60.95, 2.2]
  ];

  const palette = [
    "#f6d5cc",
    "#ee9b7f",
    "#e1644a",
    "#cc3a2b",
    "#8f1d14"
  ];

  const codeKey = "LAD23CD";
  const nameKey = "LAD23NM";
  const LAD_GEOJSON_URL = "../cache/map-boundary.json";

  function calculateGrades() {
    const values = Object.values(densityByName)
      .filter(v => v != null && !Number.isNaN(v))
      .sort((a, b) => a - b);

    if (!values.length) {
      return [0, 1, 2, 3, 4, 5];
    }

    const min = values[0];
    const max = values[values.length - 1];
    const bucketCount = palette.length;
    const span = max - min + 1;
    const step = Math.ceil(span / bucketCount);

    const grades = [min];

    for (let i = 1; i < bucketCount; i++) {
      grades.push(min + (step * i));
    }

    grades.push(max);
    return grades;
  }

  const grades = calculateGrades();

  function getColor(d) {
    if (d == null || Number.isNaN(d)) {
      return "#f5f5f5";
    }

    for (let i = grades.length - 2; i >= 0; i--) {
      if (d >= grades[i]) {
        return palette[i];
      }
    }

    return palette[0];
  }

  function getDensity(props) {
    const name = props[nameKey];
    return densityByName[name] !== undefined ? densityByName[name] : null;
  }

  function style(feature) {
    const props = feature.properties || {};
    const density = getDensity(props);

    return {
      weight: 0.8,
      opacity: 1,
      color: "#4b5563",
      fillOpacity: 0.78,
      fillColor: getColor(density)
    };
  }

  const map = L.map("map", {
    zoomControl: true,
    preferCanvas: true,
    maxBounds: ukBounds,
    maxBoundsViscosity: 1.0,
    minZoom: 5,
    worldCopyJump: false,
    zoomSnap: 0.5
  });

  function fitMapProperly() {
    map.invalidateSize();

    map.fitBounds(ukBounds, {
      paddingTopLeft: [20, 20],
      paddingBottomRight: [20, 20]
    });
  }
  map.attributionControl.setPrefix("");

  L.control.attribution({ position: "bottomright" })
    .addTo(map)
    .addAttribution("UK boundary data: Office for National Statistics.");

  let geoLayer = null;

  const info = L.control({ position: "topright" });

  info.onAdd = function () {
    this._div = L.DomUtil.create("div", "map-info-card");
    this.update();
    return this._div;
  };

  info.update = function (props) {
    if (!props) {
      this._div.style.display = "none";
      return;
    }

    this._div.style.display = "block";

    const name = props[nameKey] || "Unknown area";
    const code = props[codeKey] || "";
    const density = getDensity(props);

    this._div.innerHTML =
      `<div class="map-title">${name}</div>` +
      `<div class="map-code">Code: ${code}</div>` +
      `<div><strong>Tick sightings:</strong> ${density == null ? "No data" : density.toLocaleString()}</div>`;
  };

  info.addTo(map);

  const legend = L.control({ position: "bottomright" });

  legend.onAdd = function () {
    const div = L.DomUtil.create("div", "map-legend-card");
    div.innerHTML = `<div class="legend-heading">Tick sightings</div>`;

    for (let i = 0; i < palette.length; i++) {
      const from = grades[i];
      const to = grades[i + 1];

      div.innerHTML += `
              <div class="map-legend-row">
                  <span class="map-legend-swatch" style="background:${palette[i]}"></span>
                  <span>${from.toLocaleString()}&ndash;${to.toLocaleString()}</span>
              </div>
          `;
    }

    div.innerHTML += `
          <div class="map-legend-row">
              <span class="map-legend-swatch" style="background:#f5f5f5"></span>
              <span>No data</span>
          </div>
      `;

    return div;
  };

  legend.addTo(map);

  function highlightFeature(e) {
    const layer = e.target;
    layer.setStyle({
      weight: 1.6,
      color: "#7f1d1d",
      fillOpacity: 0.95
    });
    info.update(layer.feature.properties);
  }

  function resetHighlight(e) {
    if (geoLayer) {
      geoLayer.resetStyle(e.target);
    }
    info.update();
  }

  function zoomToFeature(e) {
    map.fitBounds(e.target.getBounds(), { padding: [20, 20] });
  }

  function onEachFeature(feature, layer) {
    layer.on({
      mouseover: highlightFeature,
      mouseout: resetHighlight,
      click: zoomToFeature
    });
  }

  async function initMap() {
    try {
      const res = await fetch(LAD_GEOJSON_URL);

      if (!res.ok) {
        throw new Error("Failed to load GeoJSON: " + res.status);
      }

      const data = await res.json();

      geoLayer = L.geoJSON(data, {
        style,
        onEachFeature,
        smoothFactor: 1.5
      }).addTo(map);

      setTimeout(() => {
        fitMapProperly();
      }, 100);

      window.addEventListener("load", () => {
        setTimeout(() => {
          fitMapProperly();
        }, 150);
      });

      window.addEventListener("resize", () => {
        fitMapProperly();
      });

    } catch (err) {
      console.error("Map failed:", err);
      mapElement.innerHTML = "<p style='padding:16px;'>Map failed to load.</p>";
    }
  }
  fitMapProperly();
  initMap();
});

document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("dashboard-search-input");
  const suggestionsBox = document.getElementById("dashboard-search-suggestions");
  const searchForm = document.getElementById("dashboard-search-form");

  if (!searchInput || !suggestionsBox || !searchForm) {
    return;
  }

  const columns = [
    { value: "location_name", label: "location" },
    { value: "species_name", label: "species" },
    { value: "species_latin_name", label: "species_latin" }
  ];

  const operators = [
    { value: "AND", label: "AND" },
    { value: "OR", label: "OR" }
  ];

  let activeIndex = -1;
  let currentItems = [];

  function hideSuggestions() {
    suggestionsBox.style.display = "none";
    suggestionsBox.innerHTML = "";
    currentItems = [];
    activeIndex = -1;
  }

  function renderSuggestions(items, type) {
    currentItems = items;
    activeIndex = -1;

    if (!items.length) {
      hideSuggestions();
      return;
    }

    suggestionsBox.innerHTML = items.map(function (item, index) {
      return '<button type="button" data-index="' + index + '" data-type="' + type + '" style="display:block; width:100%; text-align:left; padding:12px 14px; border:0; background:#fff; cursor:pointer; font:inherit;">' + escapeHtml(item.label) + '</button>';
    }).join("");

    suggestionsBox.style.display = "block";
  }

  function updateActiveItem() {
    const buttons = suggestionsBox.querySelectorAll("button");

    buttons.forEach(function (button, index) {
      button.style.background = index === activeIndex ? "#f1f5f9" : "#fff";
    });
  }

  function getLastClauseInfo() {
    const value = searchInput.value;
    const operatorMatch = value.match(/(?:^|\s)(AND|OR)\s+(@[^@]*)$/i);

    if (operatorMatch) {
      return {
        prefix: value.slice(0, value.length - operatorMatch[2].length),
        segment: operatorMatch[2]
      };
    }

    const clauseMatch = value.match(/(^|.*\s(?:AND|OR)\s)(@[^@]*)$/i);

    if (clauseMatch) {
      return {
        prefix: clauseMatch[1],
        segment: clauseMatch[2]
      };
    }

    return {
      prefix: "",
      segment: value
    };
  }

  function replaceLastClause(nextValue) {
    const clauseInfo = getLastClauseInfo();
    searchInput.value = clauseInfo.prefix + nextValue;
  }

  function getColumnSuggestions() {
    const clauseInfo = getLastClauseInfo();
    const value = clauseInfo.segment;

    if (!value.startsWith("@")) {
      hideSuggestions();
      return;
    }

    if (value.includes(":")) {
      fetchValueSuggestions();
      return;
    }

    const searchText = value.slice(1).trim().toLowerCase();
    const items = columns
      .filter(function (column) {
        return column.label.toLowerCase().includes(searchText);
      })
      .map(function (column) {
        return {
          value: column.value,
          label: column.label
        };
      });

    renderSuggestions(items, "column");
  }

  function getOperatorSuggestions() {
    const trimmedRight = searchInput.value.replace(/\s+$/, "");
    const operatorText = trimmedRight.match(/(?:\s+)(A|AN|AND|O|OR)?$/i);

    if (!/@[a-zA-Z_][a-zA-Z0-9_]*\s*:\s*.+$/i.test(trimmedRight)) {
      hideSuggestions();
      return;
    }

    if (!operatorText) {
      renderSuggestions(operators, "operator");
      return;
    }

    const current = (operatorText[1] || "").toUpperCase();
    const items = operators.filter(function (operator) {
      return operator.label.indexOf(current) === 0;
    });

    renderSuggestions(items, "operator");
  }

  async function fetchValueSuggestions() {
    const clauseInfo = getLastClauseInfo();
    const value = clauseInfo.segment;

    if (!value.startsWith("@") || !value.includes(":")) {
      hideSuggestions();
      return;
    }

    try {
      const response = await fetch("../functions/search-functions.php?ajax=dashboard-search-values&term=" + encodeURIComponent(value), {
        cache: "no-store"
      });

      if (!response.ok) {
        hideSuggestions();
        return;
      }

      const data = await response.json();
      const items = (data.suggestions || []).map(function (item) {
        return {
          value: item,
          label: item
        };
      });

      renderSuggestions(items, "value");
    } catch (error) {
      hideSuggestions();
    }
  }

  function applySuggestion(item, type) {
    if (type === "column") {
      replaceLastClause("@" + item.value + ":");
      fetchValueSuggestions();
    } else if (type === "operator") {
      searchInput.value = searchInput.value.replace(/\s*$/, "") + " " + item.value + " @";
      getColumnSuggestions();
    } else {
      const clauseInfo = getLastClauseInfo();
      const colonIndex = clauseInfo.segment.indexOf(":");

      if (colonIndex === -1) {
        replaceLastClause(item.value);
      } else {
        replaceLastClause(clauseInfo.segment.slice(0, colonIndex + 1) + item.value);
      }

      renderSuggestions(operators, "operator");
    }

    searchInput.focus();
    const valueLength = searchInput.value.length;
    searchInput.setSelectionRange(valueLength, valueLength);
  }

  function updateSuggestions() {
    const value = searchInput.value;
    const trimmed = value.trim();

    if (!trimmed) {
      hideSuggestions();
      return;
    }

    if (/\s+(AND|OR)\s*@[^:]*$/i.test(value) || /^@[^:]*$/i.test(trimmed)) {
      getColumnSuggestions();
      return;
    }

    if (/\s+(AND|OR)\s*$/i.test(value) || /@[a-zA-Z_][a-zA-Z0-9_]*\s*:\s*.+\s+(A|AN|AND|O|OR)?$/i.test(value)) {
      getOperatorSuggestions();
      return;
    }

    if (/@[a-zA-Z_][a-zA-Z0-9_]*\s*:[^@]*$/i.test(value)) {
      fetchValueSuggestions();
      return;
    }

    hideSuggestions();
  }

  searchInput.addEventListener("input", function () {
    updateSuggestions();
  });

  searchInput.addEventListener("focus", function () {
    updateSuggestions();
  });

  searchInput.addEventListener("keydown", function (event) {
    if (suggestionsBox.style.display === "none" || !currentItems.length) {
      return;
    }

    if (event.key === "ArrowDown") {
      event.preventDefault();
      activeIndex = (activeIndex + 1) % currentItems.length;
      updateActiveItem();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      activeIndex = activeIndex <= 0 ? currentItems.length - 1 : activeIndex - 1;
      updateActiveItem();
    } else if (event.key === "Enter" && activeIndex >= 0) {
      event.preventDefault();
      const activeButton = suggestionsBox.querySelector('button[data-index="' + activeIndex + '"]');

      if (activeButton) {
        applySuggestion(currentItems[activeIndex], activeButton.dataset.type);
      }
    } else if (event.key === "Escape") {
      hideSuggestions();
    }
  });

  suggestionsBox.addEventListener("mousedown", function (event) {
    const button = event.target.closest("button");

    if (!button) {
      return;
    }

    event.preventDefault();
    const index = Number(button.dataset.index);
    applySuggestion(currentItems[index], button.dataset.type);
  });

  document.addEventListener("click", function (event) {
    if (!searchForm.contains(event.target)) {
      hideSuggestions();
    }
  });
});
