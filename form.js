document.addEventListener("DOMContentLoaded", function () {

  // ─────────────────────────────────────────────────────────────────
  // CARICA DATI ACCOUNT DELL'UTENTE DA form.php
  // ─────────────────────────────────────────────────────────────────

  async function loadAccountData() {
    try {
      const response = await fetch('form.php', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      });   

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const data = await response.json();

      if (data.error) {
        showAlert(data.error, 'error');
        return;
      }

      populateForm(data);

    } catch (error) {
      console.error('Errore durante il caricamento dei dati:', error);
      showAlert('Errore nel caricamento dei dati account', 'error');
    }
  }


  // ─────────────────────────────────────────────────────────────────
  // POPOLA I CAMPI DEL FORM CON I DATI RECUPERATI
  // ─────────────────────────────────────────────────────────────────

  function populateForm(data) {
    const user = data.user;
    const subscription = data.subscription;

    // Popola campi utente
    setFieldValue('username', user.username);
    setFieldValue('email', user.email);
    setFieldValue('firstName', user.first_name);
    setFieldValue('lastName', user.last_name);

    if (document.getElementById('createdAt')) {
      const date = new Date(user.created_at);
      document.getElementById('createdAt').value = date.toLocaleDateString('it-IT') || '';
    }

    // Popola campi abbonamento
    if (subscription) {
      setFieldValue('subscriptionName', subscription.name);
      setFieldValue('subscriptionStatus', subscription.status);

      if (document.getElementById('subscriptionStartDate')) {
        const startDate = new Date(subscription.start_date);
        document.getElementById('subscriptionStartDate').value = startDate.toLocaleDateString('it-IT') || '';
      }

      if (document.getElementById('subscriptionEndDate')) {
        const endDate = new Date(subscription.end_date);
        document.getElementById('subscriptionEndDate').value = endDate.toLocaleDateString('it-IT') || '';
      }

      if (document.getElementById('pricePerMonth')) {
        document.getElementById('pricePerMonth').value = `€ ${parseFloat(subscription.price_per_month).toFixed(2)}`;
      }

      setFieldValue('ptSessions', subscription.pt_sessions_count || '0');
      setFieldValue('nutritionalPlan', subscription.has_nutritional_plan ? 'Sì' : 'No');

    } else {
      setFieldValue('subscriptionName', 'Nessun abbonamento attivo');
    }
  }

  /**
   * Helper: imposta il valore di un campo input per ID
   */
  function setFieldValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
      element.value = value || '';
    }
  }


  // ─────────────────────────────────────────────────────────────────
  // GESTISCE L'INVIO DEL FORM
  // ─────────────────────────────────────────────────────────────────

  function setupFormSubmit() {
    const form = document.querySelector('.account-form');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(form);
      const data = Object.fromEntries(formData);

      try {
        const response = await fetch('form.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (result.error) {
          showAlert(result.error, 'error');
        } else {
          showAlert('Dati aggiornati con successo!', 'success');
          setTimeout(() => loadAccountData(), 1000);
        }

      } catch (error) {
        console.error('Errore durante l\'invio:', error);
        showAlert('Errore durante l\'invio dei dati', 'error');
      }
    });
  }


  // ─────────────────────────────────────────────────────────────────
  // VISUALIZZA MESSAGGI ALERT
  // ─────────────────────────────────────────────────────────────────

  function showAlert(message, type = 'info') {
    const oldAlert = document.querySelector('.account-alert');
    if (oldAlert) oldAlert.remove();

    const container = document.querySelector('.account-form') || document.body;
    const alertEl = document.createElement('div');
    alertEl.className = 'account-alert';

    const colors = {
      error: '#fee; color: #c33; border: 1px solid #fcc',
      success: '#efe; color: #3c3; border: 1px solid #cfc',
      info: '#eef; color: #33c; border: 1px solid #ccf'
    };

    alertEl.style.cssText = `
      background: ${colors[type] || colors.info};
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 14px;
      font-weight: 500;
    `;

    alertEl.textContent = message;
    container.insertBefore(alertEl, container.firstChild);

    setTimeout(() => {
      if (alertEl && alertEl.parentNode) {
        alertEl.remove();
      }
    }, 5000);
  }


  // ─────────────────────────────────────────────────────────────────
  // ANNO FOOTER
  // ─────────────────────────────────────────────────────────────────

  const yearEl = document.getElementById('year');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }


  // ─────────────────────────────────────────────────────────────────
  // INIZIALIZZAZIONE
  // ─────────────────────────────────────────────────────────────────

  loadAccountData();
  setupFormSubmit();

});