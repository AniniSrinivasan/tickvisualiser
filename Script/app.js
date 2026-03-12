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
          tension: 0.4, // make the line curved
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