document.addEventListener("DOMContentLoaded", function () {

  // ─── ANNO FOOTER ───
  const yearEl = document.getElementById("year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  // ─── FUNZIONE HELPER PER MOSTRARE ALERT ───
  function showFormAlert(message) {
    // Rimuovi eventuali alert precedenti
    const oldAlert = document.querySelector(".form-alert");
    if (oldAlert) oldAlert.remove();

    const form = document.querySelector(".login__form");
    if (!form) return;

    const alertEl = document.createElement("div");
    alertEl.className = "form-alert";
    alertEl.style.cssText = `
      background: #fee;
      color: #c33;
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      border: 1px solid #fcc;
      font-size: 14px;
    `;
    alertEl.textContent = message;
    form.prepend(alertEl);
  }

  // ─── GESTIONE ERRORI DA URL (PHP redirect con ?error=...) ───
  const urlParams = new URLSearchParams(window.location.search);
  const errorParam = urlParams.get("error");
  if (errorParam) {
    const messages = {
      invalid: "Credenziali non valide.",
      missing: "Inserisci nome utente e password.",
      blocked: "Account bloccato. Contatta l'amministratore.",
      db: "Errore di connessione al database.",
    };
    showFormAlert(messages[errorParam] || "Errore sconosciuto.");
    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  }

  // ─── RIFERIMENTI AGLI ELEMENTI DEL FORM ───
  const form             = document.querySelector(".login__form");
  const usernameInput    = document.getElementById("username");
  const passwordInput    = document.getElementById("password");
  const toggleBtn        = document.getElementById("togglePwd");
  const rememberCheckbox = document.querySelector('input[name="remember"]');

  // ─── TOGGLE VISIBILITÀ PASSWORD ───
  if (toggleBtn && passwordInput) {
    // Forza il tipo button per evitare il submit del form
    toggleBtn.setAttribute("type", "button");

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

  // ─── RIPRISTINO DATI DA LOCALSTORAGE (solo username per sicurezza) ───
  const savedUsername = localStorage.getItem("falianz_username");

  if (savedUsername && usernameInput) {
    usernameInput.value = savedUsername;
    if (rememberCheckbox) rememberCheckbox.checked = true;
  }

  // ─── GESTIONE FORM LOGIN ───
  if (form) {
    form.addEventListener("submit", function (event) {
      const username = usernameInput.value.trim();
      const password = passwordInput.value;
      const remember = rememberCheckbox ? rememberCheckbox.checked : false;

      // ── Validazione client-side ──
      if (username === "" || password === "") {
        event.preventDefault();
        showFormAlert("Inserisci nome utente e password.");
        return;
      }

      // ── Salvataggio in localStorage (SOLO username, mai password) ──
      if (remember) {
        localStorage.setItem("falianz_username", username);
      } else {
        localStorage.removeItem("falianz_username");
      }

      // ── Stato pulsante durante il caricamento ──
      const submitBtn = form.querySelector(".login__submit");
      if (submitBtn) {
        submitBtn.disabled    = true;
        submitBtn.textContent = "Accesso in corso...";
      }

      // NON chiamiamo event.preventDefault() qui:
      // il form viene inviato normalmente a login.php
    });
  }

});