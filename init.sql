-- ==========================================================================
-- 1. UTENTI E AUTENTICAZIONE
-- ==========================================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Contiene la password cifrata (es. bcrypt)
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================================================
-- 2. ABBONAMENTI DISPONIBILI (Configurati dall'HTML)
-- ==========================================================================
CREATE TABLE membership_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,        -- 'Flessibile', 'Più scelto', 'Massimo risparmio'
    duration_months INT NOT NULL,     -- 1, 6, 12
    price_per_month DECIMAL(5,2) NOT NULL, -- 49.00, 39.00, 29.00
    has_pt_session BOOLEAN DEFAULT FALSE,
    pt_sessions_count INT DEFAULT 0,  -- 0, 1, 2 sessioni incluse
    has_nutritional_plan BOOLEAN DEFAULT FALSE
);

-- Inserimento dei piani definiti nella pagina web
INSERT INTO membership_plans (name, duration_months, price_per_month, has_pt_session, pt_sessions_count, has_nutritional_plan) 
VALUES 
('Mensile Flessibile', 1, 49.00, FALSE, 0, FALSE),
('Semestrale (6 Mesi)', 6, 39.00, TRUE, 1, FALSE),
('Annuale (Massimo Risparmio)', 12, 29.00, TRUE, 2, TRUE);

-- ==========================================================================
-- 3. ABBONAMENTI ATTIVI DEGLI UTENTI
-- ==========================================================================
CREATE TABLE user_subscriptions (
    subscription_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'canceled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(plan_id)
);

-- ==========================================================================
-- 4. GESTIONE CORSI SETTIMANALI (60+ Corsi menzionati nell'HTML)
-- ==========================================================================
CREATE TABLE classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,       -- Es: 'Crossfit', 'Yoga Flow', 'Powerlifting'
    description TEXT,
    capacity INT NOT NULL DEFAULT 20  -- Posti massimi disponibili per classe
);

CREATE TABLE class_schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    day_of_week ENUM('Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    trainer_name VARCHAR(100),
    FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE
);

-- Tabella di prenotazione (un utente prenota un corso specifico nel calendario)
CREATE TABLE class_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    schedule_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES class_schedule(schedule_id) ON DELETE CASCADE,
    UNIQUE(user_id, schedule_id) -- Impedisce di prenotare due volte la stessa identica lezione
);


-- ==========================================================================
-- 1. INSERIMENTO UTENTI DI TEST
-- Note: Le password inserite sono stringhe hash simulate (es. generate con bcrypt)
-- ==========================================================================
INSERT INTO users (username, email, password, first_name, last_name) VALUES
('marco_rossi', 'marco.rossi@email.it', 'marco', 'Marco', 'Rossi'),
('elena_bianchi', 'elena.bianchi@email.com', 'elena', 'Elena', 'Bianchi'),
('luca_verdi', 'luca.verdi@gmail.com', 'luca', 'Luca', 'Verdi'),
('admin', 'admin@falianztheory.it', 'admin', 'Amministratore', 'Sistema');


-- ==========================================================================
-- 2. INSERIMENTO PIANI ABBONAMENTO (Con dettagli estratti dall'HTML)
-- ==========================================================================
INSERT INTO membership_plans (name, duration_months, price_per_month, has_pt_session, pt_sessions_count, has_nutritional_plan) VALUES 
('Flessibile', 1, 49.00, FALSE, 0, FALSE),
('Più scelto', 6, 39.00, TRUE, 1, FALSE),
('Massimo risparmio', 12, 29.00, TRUE, 2, TRUE);


-- ==========================================================================
-- 3. ASSEGNAZIONE ABBONAMENTI AGLI UTENTI
-- ==========================================================================
INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status) VALUES
(1, 1, '2026-05-01', '2026-06-01', 'active'),   -- Marco ha il Mensile Flessibile
(2, 2, '2026-01-15', '2026-07-15', 'active'),   -- Elena ha il Semestrale (Più scelto)
(3, 3, '2026-03-01', '2027-03-01', 'active');   -- Luca ha l'Annuale (Massimo risparmio)


-- ==========================================================================
-- 4. INSERIMENTO DEI CORSI DISPONIBILI (Tipologie di classi)
-- ==========================================================================
INSERT INTO classes (name, description, capacity) VALUES
('Functional Training', 'Allenamento ad alta intensità basato su movimenti naturali e calistenici.', 15),
('Yoga Flow', 'Rilascio dello stress e potenziamento della flessibilità muscolare e mentale.', 20),
('Powerlifting Baseline', 'Sviluppo della forza pura attraverso squat, panca e stacco da terra.', 10),
('Calisthenics Skills', 'Controllo del corpo a corpo libero ed elementi di ginnastica avanzata.', 12);


-- ==========================================================================
-- 5. CONFIGURAZIONE ORARIO SETTIMANALE (Class Schedule)
-- ==========================================================================
INSERT INTO class_schedule (class_id, day_of_week, start_time, end_time, trainer_name) VALUES
(1, 'Lunedì', '08:30:00', '09:30:00', 'Coach Alessio'),
(1, 'Mercoledì', '18:30:00', '19:30:00', 'Coach Alessio'),
(2, 'Martedì', '19:00:00', '20:00:00', 'Coach Elena'),
(2, 'Giovedì', '13:00:00', '14:00:00', 'Coach Elena'),
(3, 'Lunedì', '17:30:00', '19:00:00', 'Coach Matteo'),
(3, 'Venerdì', '17:30:00', '19:00:00', 'Coach Matteo'),
(4, 'Mercoledì', '14:30:00', '15:30:00', 'Coach Chiara');


-- ==========================================================================
-- 6. PRENOTAZIONI DEI CORSI DA PARTE DEGLI UTENTI (Esempi di test)
-- ==========================================================================
INSERT INTO class_bookings (user_id, schedule_id) VALUES
(1, 2), -- Marco si prenota per Functional Training di Mercoledì sera
(2, 3), -- Elena si prenota per Yoga Flow di Martedì sera
(3, 5); -- Luca si prenota per Powerlifting di Lunedì pomeriggio
