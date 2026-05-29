document.addEventListener("DOMContentLoaded", function () {

  // ─── ANNO FOOTER ───
  const yearEl = document.getElementById("year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  // ─── TOGGLE VISIBILITÀ PASSWORD ───
  const toggleBtn     = document.getElementById("togglePwd");
  const passwordInput = document.getElementById("password");

  if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener("click", function () {
      const isHidden = passwordInput.type === "password";
      passwordInput.type = isHidden ? "text" : "password";
      toggleBtn.innerHTML = isHidden ? "&#128064;" : "&#128065;";
      toggleBtn.setAttribute(
        "aria-label",
        isHidden ? "Nascondi password" : "Mostra password"
      );
    });
  }

  // ─── RIPRISTINO EMAIL DA LOCALSTORAGE ───
  const emailInput       = document.getElementById("email");
  const rememberCheckbox = document.querySelector('input[name="remember"]');
  const savedEmail       = localStorage.getItem("falianz_email");

  if (savedEmail) {
    emailInput.value         = savedEmail;
    rememberCheckbox.checked = true;
  }

  // ─── GESTIONE FORM LOGIN ───
  const form = document.querySelector(".login__form");

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const email    = emailInput.value.trim();
    const password = passwordInput.value;
    const remember = rememberCheckbox.checked;

    // ── Pulisci errori precedenti ──
    clearError("email");
    clearError("password");

    // ── Validazione ──
    let valid = true;

    if (!validateEmail(email)) {
      showError("email", "Inserisci un indirizzo email valido.");
      valid = false;
    }

    if (password.length < 6) {
      showError("password", "La password deve contenere almeno 6 caratteri.");
      valid = false;
    }

    if (!valid) return;

    // ── Stato pulsante durante il caricamento ──
    const submitBtn = form.querySelector(".login__submit");
    const originalText = submitBtn.textContent;
    submitBtn.disabled    = true;
    submitBtn.textContent = "Accesso in corso...";

    // ── Invio dati a login.php ──
    const formData = new FormData();
    formData.append("email",    email);
    formData.append("password", password);
    formData.append("remember", remember ? "1" : "0");

    fetch("login.php", {
      method: "POST",
      body:   formData,
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Errore del server: " + response.status);
        }
        return response.json();
      })
      .then(function (data) {
        if (data.status === "success") {

          // ✅ SALVA/RIMUOVI EMAIL SOLO SE LOGIN RIUSCITO
          if (remember) {
            localStorage.setItem("falianz_email", email);
          } else {
            localStorage.removeItem("falianz_email");
          }

          // Reindirizza
          window.location.href = data.redirect || "dashboard.php";

        } else {
          // Login fallito
          showFormAlert(data.message || "Credenziali non valide. Riprova.");
          submitBtn.disabled    = false;
          submitBtn.textContent = originalText;
        }
      })
      .catch(function (error) {
        console.error("Errore:", error);
        showFormAlert("Si è verificato un errore. Riprova più tardi.");
        submitBtn.disabled    = false;
        submitBtn.textContent = originalText;
      });
  });

  // ─── UTILITY: validazione email ───
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // ─── UTILITY: mostra errore sotto il campo ───
  function showError(fieldId, message) {
    const field  = document.getElementById(fieldId);
    const group  = field.closest(".form-group");
    
    // Rimuovi errore esistente
    const existing = group.querySelector(".form-error");
    if (existing) existing.remove();
    
    // Crea nuovo errore
    const error = document.createElement("span");
    error.className   = "form-error";
    error.textContent = message;
    
    // Inserisci dopo il wrapper o dopo l'input
    const wrapper = field.closest(".form-input-wrap");
    if (wrapper) {
      wrapper.insertAdjacentElement("afterend", error);
    } else {
      field.insertAdjacentElement("afterend", error);
    }
    
    field.classList.add("form-input--error");
  }

  // ─── UTILITY: rimuove errore dal campo ───
  function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const group = field.closest(".form-group");
    const existingError = group ? group.querySelector(".form-error") : null;
    if (existingError) existingError.remove();
    field.classList.remove("form-input--error");
  }

  // ─── UTILITY: alert generale sopra il bottone ───
  function showFormAlert(message) {
    const existing = form.querySelector(".form-alert");
    if (existing) existing.remove();

    const alert = document.createElement("div");
    alert.className   = "form-alert";
    alert.textContent = message;

    const submitBtn = form.querySelector(".login__submit");
    form.insertBefore(alert, submitBtn);

    setTimeout(function () {
      if (alert && alert.parentNode) alert.remove();
    }, 5000);
  }

});
