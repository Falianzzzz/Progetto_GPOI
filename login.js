document.addEventListener("DOMContentLoaded", function () {

  // ─── ANNO FOOTER ───
  const yearEl = document.getElementById("year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  // ─── GESTIONE ERRORI DA URL (PHP redirect con ?error=...) ───
  const urlParams = new URLSearchParams(window.location.search);
  const errorParam = urlParams.get("error");
  if (errorParam) {
    const messages = {
      invalid: "Credenziali non valide.",
      missing: "Inserisci email e password.",
      blocked: "Account bloccato. Contatta l'amministratore.",
    };
    showFormAlert(messages[errorParam] || "Errore sconosciuto.");
    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
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

  // ─── RIPRISTINO DATI DA LOCALSTORAGE ───
  const emailInput       = document.getElementById("email");
  const rememberCheckbox = document.querySelector('input[name="remember"]');
  const savedEmail       = localStorage.getItem("falianz_email");
  const savedPassword    = localStorage.getItem("falianz_password");

  if (savedEmail) {
    emailInput.value         = savedEmail;
    rememberCheckbox.checked = true;
    if (savedPassword) {
      passwordInput.value = savedPassword;
    }
  }

  // ─── GESTIONE FORM LOGIN ───
  const form = document.querySelector(".login__form");

  form.addEventListener("submit", function (event)  {
    event.preventDefault();

    const email    = emailInput.value.trim();
    const password = passwordInput.value;
    const remember = rememberCheckbox.checked;

    // ── Stato pulsante durante il caricamento ──
    const submitBtn = form.querySelector(".login__submit");
    const originalText = submitBtn.textContent;
    submitBtn.disabled    = true;
    submitBtn.textContent = "Accesso in corso...";

   
});
});