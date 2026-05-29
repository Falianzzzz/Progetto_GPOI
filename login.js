document.addEventListener("DOMContentLoaded", function () {

    const yearEl = document.getElementById("year");
    if (yearEl) yearEl.textContent = new Date().getFullYear();

    // ─── ERRORI DA URL ───
    const urlParams = new URLSearchParams(window.location.search);
    const errorParam = urlParams.get("error");
    if (errorParam) {
        const messages = {
            invalid: "Credenziali non valide.",
            empty:   "Inserisci username e password.",
            db:      "Errore di connessione al database.",
            blocked: "Account bloccato. Contatta l'amministratore.",
        };
        showFormAlert(messages[errorParam] || "Errore sconosciuto.");
        if (window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    // ─── TOGGLE PASSWORD ───
    const toggleBtn    = document.getElementById("togglePwd");
    const passwordInput = document.getElementById("password");

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            const isHidden = passwordInput.type === "password";
            passwordInput.type = isHidden ? "text" : "password";
            toggleBtn.innerHTML = isHidden ? "👀" : "👁";
            toggleBtn.setAttribute(
                "aria-label",
                isHidden ? "Nascondi password" : "Mostra password"
            );
        });
    }

    // ─── RIPRISTINO DATI DA LOCALSTORAGE ───
    const usernameInput    = document.getElementById("username");   // ✅ ID corretto
    const rememberCheckbox = document.querySelector('input[name="remember"]');

    const savedUsername = localStorage.getItem("falianz_email");
    if (savedUsername) {
        usernameInput.value    = savedUsername;
        rememberCheckbox.checked = true;
        // ⚠️ Non salvare la password in localStorage!
    }

    // ─── GESTIONE SUBMIT ───
    const form = document.querySelector(".login__form");

    form.addEventListener("submit", function () {
        const remember = rememberCheckbox.checked;
        if (remember) {
            localStorage.setItem("falianz_email", usernameInput.value.trim());
        } else {
            localStorage.removeItem("falianz_email");
        }

        const submitBtn     = form.querySelector(".login__submit");
        submitBtn.disabled    = true;
        submitBtn.textContent = "Accesso in corso...";
    });
});