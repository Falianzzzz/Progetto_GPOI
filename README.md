# Falianz Theory

Progetto di gruppo  — realizzato da **Tommaso Faliani, Marco Brinati, Neri Calamai, Diego Stoppioni**.

Sito web di una palestra/wellness center fittizia situata a Milano ("Via della Natura 67, Milano").  
Sviluppato con **XAMPP** (Apache + PHP + MySQL).

---

## Funzionalità

| Funzionalità | Descrizione |
|-------------|-------------|
| **Landing page** | Hero section, statistiche (12+ anni, 3400+ membri, 60+ classi), sezione "About", galleria fotografica, prezzi, CTA contatti, footer. |
| **Registrazione** | Form con nome, cognome, username, email, password. Validazione client-side (JS) e server-side (PHP). Controllo duplicati su username/email. |
| **Login / Logout** | Login con "Remember Me" (localStorage). Sessione PHP. Reindirizzamento admin a pannello admin, utenti normali a homepage. |
| **Calcolatore Calorie** | Calcolo BMR (formula Mifflin-St Jeor), TDEE e macronutrienti (proteine 2g/kg, grassi 1g/kg, carboidrati resto). Animazioni e progress bar. |
| **Abbonamenti** | 3 piani: Flessibile (1 mese, €49/mese), Semestrale (6 mesi, €39/mese), Annuale (12 mesi, €29/mese). Acquisto via AJAX con controllo abbonamenti attivi. |
| **Il Mio Account** | Visualizzazione dati personali e abbonamento attivo (nome, email, data registrazione, dettagli piano, date inizio/fine, sessioni PT, piano nutrizionale). |
| **Pannello Admin** | Accessibile solo all'utente "admin". Conteggio utenti totali. Eliminazione singoli utenti, reset password a `1234@`, eliminazione di massa utenti non-admin. |
| **Design Responsive** | Media query a 1024px, 900px, 768px, 600px, 480px. Tema scuro con accenti verdi. |

---

## Struttura dei file

### Frontend (HTML + CSS + JS)

| File | Scopo |
|------|-------|
| `index.html` | Landing page principale — hero, about, pricing, gallery, CTA, footer |
| `style.css` | Stili globali — variabili CSS, nav, hero, about, pricing, gallery, footer, responsive |
| `login.html` | Pagina di login — layout a due colonne |
| `login.js` | Logica login — validazione, Remember Me, toggle password, errori da URL |
| `login.css` | Stili per login e register |
| `register.html` | Pagina di registrazione |
| `register.js` | Logica registrazione — validazione, toggle password |
| `calcolatore.html` | Calcolatore calorie e macronutrienti |
| `calcolatore.js` | Logica calcolo BMR/TDEE/macros con animazioni |
| `calcolatore.css` | Stili calcolatore — card, KPI, progress bar |
| `form.html` | Pagina "Il Mio Account" |
| `form.js` | Fetch dati utente/abbonamento, popolamento form |
| `form.css` | Stili account page |

### Backend (PHP)

| File | Scopo |
|------|-------|
| `index.php` | Guardia sessione — reindirizza al login se non autenticato, include `index.html` |
| `login.php` | Gestione POST login — verifica credenziali, avvia sessione |
| `register.php` | Gestione POST registrazione — inserimento nuovo utente nel database |
| `logout.php` | Distruzione sessione e redirect al login |
| `form.php` | API JSON (GET) — restituisce dati utente e abbonamento attivo |
| `subscribe.php` | API JSON (POST) — acquisto abbonamento con controllo duplicati |
| `check_subscription.php` | API JSON (GET) — verifica se l'utente ha un abbonamento attivo |
| `admin.php` | Pannello admin — gestione utenti (elimina, reset password, svuota) |

### Database e configurazione

| File | Scopo |
|------|-------|
| `init.sql` | Schema completo del database — 6 tabelle con dati di test |
| `.htaccess` | Blocco accesso diretto a `index.html` (forza passaggio da `index.php`) |
| `assets/` | Immagini e risorse statiche |

---

## Database

**Nome:** `palestra`

| Tabella | Descrizione |
|---------|-------------|
| `users` | Utenti registrati (user_id, username, email, password, first_name, last_name, created_at, updated_at) |
| `membership_plans` | Piani abbonamento (plan_id, name, duration_months, price_per_month, has_pt_session, pt_sessions_count, has_nutritional_plan) |
| `user_subscriptions` | Abbonamenti acquistati (subscription_id, user_id, plan_id, start_date, end_date, status) |
| `classes` | Classi disponibili (class_id, name, description, capacity) |
| `class_schedule` | Orari settimanali delle classi (schedule_id, class_id, day_of_week, start_time, end_time, trainer_name) |
| `class_bookings` | Prenotazioni classi (booking_id, user_id, schedule_id, booking_date) |

---

## Tecnologie utilizzate

- **XAMPP** — Ambiente di sviluppo locale (Apache, PHP, MySQL/MariaDB, phpMyAdmin)
- **HTML5** — Markup pagine
- **CSS3** — Custom properties, Flexbox, Grid, animazioni, media query (nessun framework CSS)
- **JavaScript Vanilla** — ES5/ES6+, Fetch API, DOM manipulation, localStorage (nessun framework JS)
- **PHP 7/8** — Session management, MySQLi (prepared statements), API JSON
- **MySQL** — Database relazionale
- **Google Fonts** — Bebas Neue (titoli) e Inter (corpo)
- **Apache .htaccess** — Regole di accesso

---

## Come eseguire il progetto

1. Installa [XAMPP](https://www.apachefriends.org/)
2. Avvia Apache e MySQL dal XAMPP Control Panel
3. Copia la cartella `Progetto_GPOI` in `C:\xampp\htdocs\`
4. Apri phpMyAdmin, crea un database chiamato `palestra` e importa `init.sql`
5. Visita `http://localhost/Progetto_GPOI/` nel browser

**Utenti di test (da init.sql):**
- `admin` / `admin` — accesso pannello admin
- `marco_rossi` / `marco_rossi` — utente normale
- `elena_bianchi` / `elena_bianchi` — utente normale
- `luca_verdi` / `luca_verdi` — utente normale
