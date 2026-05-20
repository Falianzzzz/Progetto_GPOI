const form = document.getElementById("loginForm");
const usernameInput = document.getElementById("username");   // l'input per l'username deve avere l'id "username"
const passwordInput = document.getElementById("password");   // l'input per la password deve avere l'id "password"
const messageEl = document.getElementById("message");       // area per i messaggi
const checkbox = document.getElementById("checkbox");    // la checkbox per salvare i dati nel localStorage deve avere id "checkbox"
const submitBtn = document.getElementById("submit");     // opzionale: bottone submit

function showMessage(txt, type = "info") {
	// semplice visualizzazione; type può essere 'success' o 'error'
	if (messageEl) {
		messageEl.textContent = txt;
		messageEl.style.color = type === "success" ? "green" : "red";
	} else {
		// fallback
		alert(txt);
	}
}

// Carica username salvato se presente
(function restoreSaved() {
	try {
		const saved = localStorage.getItem("savedUsername");
		if (saved && usernameInput) {
			usernameInput.value = saved;
			if (checkbox) checkbox.checked = true;
		}
	} catch (e) {
		// ignore localStorage errors
	}
})();

// Funzione che esegue la chiamata al server
async function performLogin(username, password) {
	const formData = new FormData();
	formData.append("username", username);
	formData.append("password", password);

	const resp = await fetch("login.php", {
		method: "POST",
		body: formData
	});

	// tenteremo di parsare JSON; se non valido lancia errore
	if (!resp.ok) {
		// prova comunque a leggere il JSON se presente
		let body = null;
		try { body = await resp.json(); } catch (e) { /* noop */ }
		const msg = body && body.message ? body.message : `Errore server: ${resp.status}`;
		throw new Error(msg);
	}

	return resp.json();
}

// Handler principale (usato sia dal form che dal bottone)
async function handleSubmit(ev) {
	if (ev && ev.preventDefault) ev.preventDefault();

	if (!usernameInput || !passwordInput) {
		showMessage("Campi login non trovati nella pagina", "error");
		return;
	}

	const username = usernameInput.value.trim();
	const password = passwordInput.value;

	if (!username || !password) {
		showMessage("Inserisci username e password", "error");
		return;
	}

	try {
		showMessage("Eseguo il login...", "info");
		const data = await performLogin(username, password);

		if (data && data.success) {
			showMessage(data.message || "Login riuscito", "success");

			// salva username se checkbox è attiva (non salvare password)
			try {
				if (checkbox && checkbox.checked) {
					localStorage.setItem("savedUsername", username);
				} else {
					localStorage.removeItem("savedUsername");
				}
			} catch (e) { /* noop */ }

			// salva username autenticato (opzionale)
			if (data.username) {
				localStorage.setItem("username", data.username);
			}

			// redirect opzionale dopo login (decommentare e cambiare destinazione)
			// window.location.href = "/dashboard.html";
		} else {
			showMessage((data && data.message) ? data.message : "Credenziali non valide", "error");
		}
	} catch (err) {
		showMessage(err.message || "Errore di connessione", "error");
		console.error(err);
	}
}

// Collega gli eventi in modo robusto
if (form) {
	form.addEventListener("submit", handleSubmit);
} else if (submitBtn) {
	submitBtn.addEventListener("click", handleSubmit);
} else {
	// se nessun form o bottone, prova a eseguire automaticamente (utile per test)
	// non eseguire automaticamente se mancano i campi
	if (usernameInput && passwordInput) {
		// noop: attendi interazione utente
	}
}
