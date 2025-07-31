import * as yup from "https://cdn.jsdelivr.net/npm/yup@1.6.1/+esm";
const employeeApiUrl = "/api/emp";

let table;
let employeeData = [];
let deleteEmpId = null;
const fields = ["name", "age", "skills", "address", "designation"];

function showLoader() {
  document.getElementById("loaderOverlay").style.display = "flex";
}

function hideLoader() {
  document.getElementById("loaderOverlay").style.display = "none";
}

function showAlert(message, type = "success") {
  const alertBox = document.getElementById("alertBox");
  alertBox.textContent = message;
  alertBox.className = `alert alert-${type}`;
  alertBox.classList.remove("d-none");

  setTimeout(() => {
    alertBox.classList.add("d-none");
  }, 3000);
}

const formValidationSchema = yup.object().shape({
  name: yup
    .string()
    .trim()
    .required("Name is required")
    .matches(/^[a-zA-Z\s]+$/, "Name must contain only letters and spaces"),
  age: yup
    .number()
    .typeError("Age must be a number")
    .required("Age is required")
    .min(1, "Age must be at least 1")
    .max(120, "Age must be 120 or less"),
  skills: yup.string().required("Skills are required"),
  address: yup.string().required("Address is required"),
  designation: yup
    .string()
    .required("Designation is required")
    .matches(
      /^[a-zA-Z0-9\s,./#\-()]+$/,
      "Address can contain letters, numbers, commas, dots, slashes, hyphens, and parentheses"
    ),
});

async function validateField(fieldName) {
  const value = document.getElementById(fieldName).value;
  try {
    await formValidationSchema.validateAt(fieldName, { [fieldName]: value });
    document.getElementById(fieldName).classList.remove("is-invalid");
    document.getElementById(fieldName + "Error").textContent = "";
  } catch (err) {
    document.getElementById(fieldName).classList.add("is-invalid");
    document.getElementById(fieldName + "Error").textContent = err.message;
  }
}

function setupRealTimeValidation() {
  fields.forEach((field) => {
    const input = document.getElementById(field);
    input.addEventListener("input", () => validateField(field));
  });
}

async function loadTable() {
  if (!table) {
    table = $("#employeeTable").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: employeeApiUrl,
        dataSrc: "data",
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) =>
            meta.row + meta.settings._iDisplayStart + 1,
        },
        { data: "name" },
        { data: "age" },
        { data: "skills" },
        { data: "address" },
        { data: "designation" },
        {
          data: null,
          render: (data, type, row) => `
            <button class="btn btn-warning btn-sm editBtn" data-id="${row.id}">Update</button>
            <button class="btn btn-danger btn-sm deleteBtn" data-id="${row.id}">Delete</button>
          `,
        },
      ],
    });
    $("#employeeTable").on("xhr.dt", function (e, settings, json, xhr) {
      employeeData = json.data || [];
    });
    $("#employeeTable").on("preXhr.dt", () => showLoader());
    $("#employeeTable").on("xhr.dt", () => hideLoader());
    $("#employeeTable").on("error.dt", () => hideLoader());
  } else {
    showLoader();
    table.ajax.reload(() => {
      hideLoader();
    }, false);
  }
}

async function logout() {
  try {
    const res = await fetch("/api/auth/logout");
    if (res.ok) {
     Swal.fire("Logged out successfully!");
      setTimeout(() => window.location.href="/login",2000);
    } else {
      alert("Logout failed");
    }
  } catch {
    alert("Logout failed");
  }
}

function openAddModal() {
  document.getElementById("empForm").reset();
  document.getElementById("empId").value = "";
  $("#empModal").modal("show");
}

async function submitEmployeeForm(e) {
  e.preventDefault();
  const id = document.getElementById("empId").value;
  const empData = {};
  fields.forEach((field) => {
    empData[field] = document.getElementById(field).value;
  });

  fields.forEach((field) => {
    const input = document.getElementById(field);
    const error = document.getElementById(field + "Error");
    input.classList.remove("is-invalid");
    error.textContent = "";
  });

  if (id) empData.id = id;

  try {
    await formValidationSchema.validate(empData, { abortEarly: false });
    showLoader();
    const res = await fetch(employeeApiUrl, {
      method: id ? "PUT" : "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(empData),
    });

    if (res.ok) {
      showAlert(
        id ? "Employee updated successfully!" : "Employee added successfully!",
        "success"
      );
    } else {
      showAlert("Operation failed. Please try again.", "danger");
    }

    $("#empModal").modal("hide");
    await loadTable();
  } catch (err) {
    console.log(err);
    if (err.inner) {
      err.inner.forEach((validationError) => {
        const input = document.getElementById(validationError.path);
        const error = document.getElementById(validationError.path + "Error");
        if (input && error) {
          input.classList.add("is-invalid");
          error.textContent = validationError.message;
        }
      });
    } else {
      showAlert("Operation failed. Please try again.", "danger");
    }
  } finally {
    hideLoader();
  }
}

function handleTableButtons(e) {
  const target = e.target;

  if (target.classList.contains("editBtn")) {
    const id = target.getAttribute("data-id");
    console.log(employeeData);
    const employee = employeeData.find((emp) => emp.id == id);
    if (!employee) return alert("Employee not found.");

    document.getElementById("empId").value = employee.id;
    document.getElementById("name").value = employee.name;
    document.getElementById("age").value = employee.age;
    document.getElementById("skills").value = employee.skills;
    document.getElementById("address").value = employee.address;
    document.getElementById("designation").value = employee.designation;
    $("#empModal").modal("show");
  }

  if (target.classList.contains("deleteBtn")) {
    const id = parseInt(target.getAttribute("data-id"));
    deleteEmpId = id;
    $("#deleteModal").modal("show");
  }
}

async function confirmDelete() {
  if (deleteEmpId !== null) {
    try {
      showLoader();
      const res = await fetch(employeeApiUrl, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: deleteEmpId }),
      });
      $("#deleteModal").modal("hide");
      if (res.ok) {
        showAlert("Record deleted successfully!", "success");
        await loadTable();
      } else {
        showAlert("Failed to delete record.", "danger");
      }
    } catch {
      showAlert("Failed to delete record.", "danger");
    } finally {
      hideLoader();
      deleteEmpId = null;
    }
  }
}

async function startExport() {
  const which = document.querySelector(
    'input[name="whichExport"]:checked'
  ).value;
  const format = document.getElementById("exportFormat").value;

  showLoader();
  try {
    let exportData = [];

    if (which === "all") {
      const res = await fetch(employeeApiUrl + "?limit=-1");
      const json = await res.json();
      exportData = json.data;
    } else {
      exportData = table.rows({ filter: "applied" }).data().toArray();
    }

    if (format === "xls") {
      exportToXLSX(exportData);
    } else if (format === "pdf") {
      await exportToPDF(exportData);
    }

    $("#exportModal").modal("hide");
  } catch {
    alert("Export failed");
  } finally {
    hideLoader();
  }
}

function exportToXLSX(data) {
  const sheetData = [
    ["#", "Name", "Age", "Skills", "Address", "Designation"],
    ...data.map((emp, i) => [
      i + 1,
      emp.name,
      emp.age,
      emp.skills,
      emp.address,
      emp.designation,
    ]),
  ];

  const worksheet = XLSX.utils.aoa_to_sheet(sheetData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Employees");

  const timestamp = new Date().toISOString().replace(/[-:.]/g, "").slice(0, 14);
  const filename = `exported-data-${timestamp}.xlsx`;

  XLSX.writeFile(workbook, filename);
}

async function exportToPDF(data) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const headers = ["#", "Name", "Age", "Skills", "Address", "Designation"];
  const rows = data.map((emp, i) => [
    i + 1,
    emp.name,
    emp.age,
    emp.skills,
    emp.address,
    emp.designation,
  ]);

  doc.autoTable({
    head: [headers],
    body: rows,
    styles: {
      fontSize: 9,
      cellPadding: 3,
    },
    theme: "grid",
    margin: { top: 20 },
    didDrawPage: function (data) {
      const title = "Employee Records";
      const pageWidth = doc.internal.pageSize.getWidth();
      const textWidth = doc.getTextWidth(title);
      const x = (pageWidth - textWidth) / 2;

      doc.setFontSize(12);
      doc.text(title, x, 10);
    },
    startY: 20,
  });

  const timestamp = new Date().toISOString().replace(/[-:.]/g, "").slice(0, 14);
  const filename = `exported-data-${timestamp}.pdf`;

  doc.save(filename);
}

// --- Event listeners setup after DOM is loaded ---
document.addEventListener("DOMContentLoaded", () => {
  loadTable();
  setupRealTimeValidation();
  document.getElementById("logoutBtn").addEventListener("click", logout);
  document.getElementById("addNewBtn").addEventListener("click", openAddModal);
  document
    .getElementById("empForm")
    .addEventListener("submit", submitEmployeeForm);
  document
    .querySelector("#employeeTable tbody")
    .addEventListener("click", handleTableButtons);
  document
    .getElementById("confirmDeleteBtn")
    .addEventListener("click", confirmDelete);
  document
    .getElementById("startExportBtn")
    .addEventListener("click", startExport);
});
