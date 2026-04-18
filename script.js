const STORAGE_KEY = "concesionario.cars";
const FUTURE_YEAR_BUFFER = 1;

const carForm = document.getElementById("car-form");
const formMessage = document.getElementById("form-message");
const carsBody = document.getElementById("cars-body");
const emptyMessage = document.getElementById("empty-message");
const count = document.getElementById("count");
const totalValue = document.getElementById("total-value");
const searchInput = document.getElementById("search");
const statusFilter = document.getElementById("status-filter");

let cars = readCars();

function readCars() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveCars() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(cars));
}

function getFilters() {
  return {
    text: searchInput.value.trim().toLowerCase(),
    status: statusFilter.value,
  };
}

function applyFilters(list) {
  const { text, status } = getFilters();
  return list.filter((car) => {
    const byText = !text || `${car.brand} ${car.model}`.toLowerCase().includes(text);
    const byStatus = !status || car.status === status;
    return byText && byStatus;
  });
}

function formatCurrency(value) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 2,
  }).format(value);
}

function render() {
  const filtered = applyFilters(cars);
  carsBody.innerHTML = "";

  for (const car of filtered) {
    const row = document.createElement("tr");

    [car.brand, car.model, car.year, car.type, car.status, formatCurrency(car.price)].forEach((cellValue) => {
      const cell = document.createElement("td");
      cell.textContent = String(cellValue);
      row.appendChild(cell);
    });

    const actionCell = document.createElement("td");
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = "Eliminar";
    button.addEventListener("click", () => {
      cars = cars.filter((item) => item.id !== car.id);
      saveCars();
      render();
    });
    actionCell.appendChild(button);
    row.appendChild(actionCell);

    carsBody.appendChild(row);
  }

  emptyMessage.hidden = filtered.length > 0;
  count.textContent = String(cars.length);
  const total = cars.reduce((acc, car) => acc + car.price, 0);
  totalValue.textContent = formatCurrency(total);
}

function showMessage(text, isError = false) {
  formMessage.textContent = text;
  formMessage.className = isError ? "error" : "ok";
}

carForm.addEventListener("submit", (event) => {
  event.preventDefault();

  const formData = new FormData(carForm);
  const brand = String(formData.get("brand") || "").trim();
  const model = String(formData.get("model") || "").trim();
  const year = Number(formData.get("year"));
  const price = Number(formData.get("price"));
  const type = String(formData.get("type") || "").trim();
  const status = String(formData.get("status") || "").trim();

  const currentYear = new Date().getFullYear() + FUTURE_YEAR_BUFFER;
  if (!brand || !model || !type || !status || !Number.isFinite(year) || !Number.isFinite(price)) {
    showMessage("Completá todos los campos obligatorios.", true);
    return;
  }

  if (year < 1900 || year > currentYear || price < 0) {
    showMessage("Verificá año y precio.", true);
    return;
  }

  cars.unshift({
    id: crypto.randomUUID(),
    brand,
    model,
    year,
    type,
    status,
    price,
  });

  saveCars();
  carForm.reset();
  showMessage("Vehículo guardado correctamente.");
  render();
});

searchInput.addEventListener("input", render);
statusFilter.addEventListener("change", render);

render();
