// Shared fetch helpers for the menu and the dashboard

// Read one JSON document from the API
async function apiGet(url) {
  const response = await fetch(url);
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.error || "HTTP " + response.status);
  }
  return data;
}

// Send a JSON document to the API
async function apiSend(url, method, body) {
  const response = await fetch(url, {
    method: method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body || {})
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.error || "HTTP " + response.status);
  }
  return data;
}

// Show quantities without trailing zeros
function formatQuantity(value) {
  return String(Math.round(Number(value) * 100) / 100);
}

// Announce the result of an action in the status region
function showStatus(message) {
  const status = document.getElementById("status");
  if (status) {
    status.textContent = message;
  }
}

// Tell the user that something failed
function showError(error) {
  showStatus("Error: " + error.message);
}
