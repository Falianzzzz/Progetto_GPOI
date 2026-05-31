++document.addEventListener("DOMContentLoaded", function () {

  const yearEl = document.getElementById("year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  function showFormAlert(message) {
    const oldAlert = document.querySelector(".form-alert");
    if (oldAlert) oldAlert.remove();

    const form = document.querySelector(".login__form");
    if (!form) return;

    const alertEl = document.createElement("div");
    alertEl.className = "form-alert";
    alertEl.textContent = message;
    form.prepend(alertEl);
  }

  const urlParams = new URLSearchParams(window.location.search);
  const errorParam = urlParams.get("error");
  if (errorParam) {
    const messages = {
      missing: "Compila tutti i campi.",
      username_taken: "Nome utente già in uso.",
      email_taken: "Email già registrata.",
      db: "Errore di connessione al database.",
    };
    showFormAlert(messages[errorParam] || "Errore sconosciuto.");
    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  }

  const form          = document.querySelector(".login__form");
  const usernameInput = document.getElementById("username");
  const emailInput    = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const firstNameInput = document.getElementById("first_name");
  const lastNameInput  = document.getElementById("last_name");
  const toggleBtn     = document.getElementById("togglePwd");

  if (toggleBtn && passwordInput) {
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

  if (form) {
    form.addEventListener("submit", function (event) {
      const username   = usernameInput ? usernameInput.value.trim() : "";
      const email      = emailInput ? emailInput.value.trim() : "";
      const password   = passwordInput ? passwordInput.value : "";
      const first_name = firstNameInput ? firstNameInput.value.trim() : "";
      const last_name  = lastNameInput ? lastNameInput.value.trim() : "";

      if (username === "" || email === "" || password === "" || first_name === "" || last_name === "") {
        event.preventDefault();
        showFormAlert("Compila tutti i campi.");
        return;
      }

      const submitBtn = form.querySelector(".login__submit");
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Registrazione in corso...";
      }
    });
  }

});
