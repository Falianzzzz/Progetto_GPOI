<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user']) || $_SESSION['user'] !== 'admin') {
    header('Location: login.html');
    exit;
}

$host = 'localhost';
$db   = 'palestra';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('Errore di connessione al database.');
}

$msg = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_user') {
        $user_id = (int) ($_POST['user_id'] ?? 0);
        if ($user_id > 0) {
            $stmt = $mysqli->prepare("DELETE FROM users WHERE username != 'admin' AND user_id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $msg = 'Utente eliminato con successo.';
                $msg_type = 'success';
            } else {
                $msg = 'Impossibile eliminare l\'utente.';
                $msg_type = 'error';
            }
            $stmt->close();
        }
    } elseif ($action === 'delete_all') {
        $stmt = $mysqli->prepare("DELETE FROM users WHERE username != 'admin'");
        $stmt->execute();
        $count = $stmt->affected_rows;
        $stmt->close();
        $msg = "Eliminati $count utenti.";
        $msg_type = 'success';
    } elseif ($action === 'reset_password') {
        $user_id = (int) ($_POST['user_id'] ?? 0);
        if ($user_id > 0) {
            $stmt = $mysqli->prepare("UPDATE users SET password = '1234@' WHERE username != 'admin' AND user_id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $msg = 'Password resettata a 1234@ per l\'utente selezionato.';
                $msg_type = 'success';
            } else {
                $msg = 'Impossibile resettare la password.';
                $msg_type = 'error';
            }
            $stmt->close();
        }
    }

    $query = $msg ? '?msg=' . urlencode($msg) . '&type=' . $msg_type : '';
    header('Location: admin.php' . $query);
    exit;
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    $msg_type = $_GET['type'] ?? 'success';
}

$result = $mysqli->query("SELECT COUNT(*) as count FROM users");
$total_users = (int) $result->fetch_assoc()['count'];
$result->free();

$users_list = [];
$result = $mysqli->query("SELECT user_id, username, email FROM users WHERE username != 'admin' ORDER BY username");
while ($row = $result->fetch_assoc()) {
    $users_list[] = $row;
}
$result->free();
$non_admin_count = count($users_list);

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="login.css" />
    <title>Pannello Admin — Falianz Theory</title>
    <style>
        .admin-container {
            max-width: 900px;
            margin: 48px auto;
            padding: 0 24px 64px;
        }

        .admin-header {
            margin-bottom: 40px;
        }

        .admin-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.5rem;
            font-weight: 400;
            color: var(--neutral-50);
            margin-bottom: 8px;
            letter-spacing: 0.03em;
        }

        .admin-header p {
            font-size: 0.9rem;
            color: var(--neutral-400);
        }

        .admin-header strong {
            color: var(--green-300);
        }

        .admin-msg {
            padding: 14px 20px;
            border-radius: 6px;
            margin-bottom: 28px;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }

        .admin-msg.success {
            background: rgba(42, 105, 54, 0.15);
            border: 1px solid var(--green-700);
            color: var(--green-300);
        }

        .admin-msg.error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .admin-card {
            background: var(--neutral-800);
            border: 1px solid var(--neutral-700);
            border-radius: 8px;
            padding: 28px;
            transition: border-color 0.2s ease;
        }

        .admin-card:hover {
            border-color: var(--green-700);
        }

        .admin-card.full {
            grid-column: 1 / -1;
        }

        .admin-card h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.3rem;
            font-weight: 400;
            color: var(--green-300);
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }

        .admin-card-desc {
            font-size: 0.82rem;
            color: var(--neutral-400);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .admin-card-desc strong {
            color: var(--green-400);
            font-weight: 600;
        }

        .admin-stat {
            text-align: center;
            padding: 40px 28px;
        }

        .admin-stat-number {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 4.5rem;
            font-weight: 400;
            color: var(--green-400);
            line-height: 1;
        }

        .admin-stat-label {
            font-size: 0.85rem;
            color: var(--neutral-300);
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .admin-stat-sub {
            font-size: 0.78rem;
            color: var(--neutral-500);
            margin-top: 6px;
        }

        .admin-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .admin-form select {
            flex: 1;
            padding: 12px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            color: var(--neutral-100);
            background: var(--neutral-900);
            border: 1px solid var(--neutral-700);
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s ease;
            appearance: auto;
        }

        .admin-form select:focus {
            border-color: var(--green-600);
            box-shadow: 0 0 0 3px rgba(42, 105, 54, 0.12);
        }

        .admin-form select:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .admin-form .btn {
            white-space: nowrap;
        }

        .admin-form .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn--danger {
            color: #e74c3c;
            background: transparent;
            border: 1px solid #e74c3c;
            transition: all 0.2s ease;
        }

        .btn--danger:hover:not(:disabled) {
            background: rgba(231, 76, 60, 0.1);
            box-shadow: 0 4px 20px rgba(231, 76, 60, 0.2);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 24px auto;
                padding: 0 16px 48px;
            }

            .admin-header h1 {
                font-size: 2rem;
            }

            .admin-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .admin-card.full {
                grid-column: 1;
            }

            .admin-card {
                padding: 24px;
            }

            .admin-form {
                flex-direction: column;
                align-items: stretch;
            }

            .admin-form .btn {
                width: 100%;
            }

            .admin-stat {
                padding: 32px 24px;
            }

            .admin-stat-number {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav__logo">
        <span class="nav__logo-mark">F</span>
        <span class="nav__logo-text">FALIANZ<em>THEORY</em></span>
    </div>
    <a href="logout.php" class="btn btn--outline">Logout</a>
</nav>

<main class="admin-container">
    <header class="admin-header">
        <h1>Pannello di Amministrazione</h1>
        <p>Benvenuto, <strong><?= htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8') ?></strong>. Gestisci gli utenti del sistema.</p>
    </header>

    <?php if ($msg): ?>
        <div class="admin-msg <?= $msg_type ?>"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="admin-grid">
        <!-- Card 1: Conteggio utenti -->
        <div class="admin-card full admin-stat">
            <div class="admin-stat-number"><?= $total_users ?></div>
            <div class="admin-stat-label">utenti registrati</div>
            <?php if ($non_admin_count > 0): ?>
                <div class="admin-stat-sub">di cui <?= $non_admin_count ?> non amministratori</div>
            <?php endif; ?>
        </div>

        <!-- Card 2: Elimina singolo utente -->
        <div class="admin-card">
            <h2>Elimina Utente</h2>
            <p class="admin-card-desc">Seleziona un utente da eliminare definitivamente dal sistema.</p>
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="delete_user">
                <select name="user_id" <?= $non_admin_count === 0 ? 'disabled' : '' ?>>
                    <?php if ($non_admin_count === 0): ?>
                        <option value="">Nessun utente disponibile</option>
                    <?php else: ?>
                        <option value="">— Seleziona utente —</option>
                        <?php foreach ($users_list as $u): ?>
                            <option value="<?= $u['user_id'] ?>">
                                <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit" class="btn btn--danger" <?= $non_admin_count === 0 ? 'disabled' : '' ?>>Elimina</button>
            </form>
        </div>

        <!-- Card 3: Reset password -->
        <div class="admin-card">
            <h2>Resetta Password</h2>
            <p class="admin-card-desc">Reimposta la password di un utente al valore predefinito <strong>1234@</strong>.</p>
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="reset_password">
                <select name="user_id" <?= $non_admin_count === 0 ? 'disabled' : '' ?>>
                    <?php if ($non_admin_count === 0): ?>
                        <option value="">Nessun utente disponibile</option>
                    <?php else: ?>
                        <option value="">— Seleziona utente —</option>
                        <?php foreach ($users_list as $u): ?>
                            <option value="<?= $u['user_id'] ?>">
                                <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit" class="btn btn--primary" <?= $non_admin_count === 0 ? 'disabled' : '' ?>>Resetta</button>
            </form>
        </div>

        <!-- Card 4: Elimina tutti -->
        <div class="admin-card full">
            <h2>Elimina Tutti gli Utenti</h2>
            <p class="admin-card-desc">Rimuove definitivamente tutti gli utenti non amministratori dal sistema. Questa azione è irreversibile.</p>
            <form method="post" onsubmit="return confirm('Sei sicuro di voler eliminare TUTTI gli utenti ad eccezione dell&#39;amministratore? Questa azione è irreversibile.');">
                <input type="hidden" name="action" value="delete_all">
                <button type="submit" class="btn btn--danger" <?= $non_admin_count === 0 ? 'disabled' : '' ?>>
                    Elimina tutti gli utenti<?= $non_admin_count > 0 ? " ($non_admin_count)" : '' ?>
                </button>
            </form>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="footer__top">
        <div class="footer__brand">
            <div class="footer__logo">
                <span class="nav__logo-mark">F</span>
                <span class="nav__logo-text">FALIANZ<em>THEORY</em></span>
            </div>
            <p>Via della Natura 67, Milano<br />Aperto 6:00 am &mdash; 1:00 am ogni giorno</p>
        </div>
        <div class="footer__links">
            <div class="footer__col">
                <h4>Legale</h4>
                <a href="#">Privacy Policy</a>
                <a href="#">Termini di servizio</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <p>&copy; 2026 Falianz Theory. Tutti i diritti riservati.</p>
    </div>
</footer>

</body>
</html>
