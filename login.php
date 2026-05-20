<?php
session_start();

function generate_csrf_token() {
	// genera token CSRF se non presente
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
	return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

function validate_username(string $u, array &$errors) {
	$u = trim($u);
	if ($u === '') {
		$errors[] = 'Username richiesto.';
		return;
	}
	if (strlen($u) < 3 || strlen($u) > 20) {
		$errors[] = 'Username deve essere lungo tra 3 e 20 caratteri.';
	}
	if (!preg_match('/^[a-zA-Z0-9_.]+$/', $u)) {
		$errors[] = 'Username può contenere solo lettere, numeri, underscore e punto.';
	}
}

function validate_password(string $p, array &$errors) {
	if ($p === '') {
		$errors[] = 'Password richiesta.';
		return;
	}
	$len = strlen($p);
	if ($len < 8) {
		$errors[] = 'Password deve avere almeno 8 caratteri.';
	}
	// bcrypt limit di fatto ~72: evitare password più lunghe per compatibilità
	if ($len > 72) {
		$errors[] = 'Password troppo lunga.';
	}
	if (!preg_match('/[a-z]/', $p)) {
		$errors[] = 'La password deve contenere almeno una lettera minuscola.';
	}
	if (!preg_match('/[A-Z]/', $p)) {
		$errors[] = 'La password deve contenere almeno una lettera maiuscola.';
	}
	if (!preg_match('/\d/', $p)) {
		$errors[] = 'La password deve contenere almeno una cifra.';
	}
	if (!preg_match('/[\W_]/', $p)) {
		$errors[] = 'La password deve contenere almeno un carattere speciale.';
	}
	if (preg_match('/\s/', $p)) {
		$errors[] = 'La password non può contenere spazi.';
	}
}

function json_response($data, int $http_status = 200) {
	http_response_code($http_status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

// CONFIGURAZIONE DB: sostituire con i valori reali o caricarli da variabili d'ambiente
function get_pdo(): ?PDO {
	$host = '127.0.0.1';
	$port = 3306;
	$db   = 'your_database';
	$user = 'your_user';
	$pass = 'your_password';
	$dsn  = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

	try {
		$pdo = new PDO($dsn, $user, $pass, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		]);
		return $pdo;
	} catch (PDOException $e) {
		// ...eventuale logging...
		return null;
	}
}

/**
 * Controlla username/password usando una connessione MySQL (tabella "users" con colonne "username","password").
 * Supporta password hashate con password_verify (bcrypt/argon2) o password in chiaro.
 */
function check_credentials_in_db(string $username, string $password): bool {
	$pdo = get_pdo();
	if (!$pdo) {
		return false;
	}

	try {
		$stmt = $pdo->prepare('SELECT password FROM users WHERE username = :username LIMIT 1');
		$stmt->execute([':username' => $username]);
		$row = $stmt->fetch();
		// dummy hash per mitigare differenze di timing se l'utente non esiste
		$dummyHash = password_hash('dummy_password', PASSWORD_BCRYPT);

		if (!$row) {
			password_verify($password, $dummyHash);
			return false;
		}

		$stored = (string)$row['password'];

		// se sembra un hash compatibile con password_verify
		if (preg_match('/^\$2[axy]\$/', $stored) || stripos($stored, '$argon2') === 0) {
			return password_verify($password, $stored);
		}

		// confronto diretto in modo sicuro
		return hash_equals($stored, $password);
	} catch (PDOException $e) {
		// ...eventuale logging...
		return false;
	}
}

// Aggiungi gestione POST e redirect su index.html
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$errors = [];

	// Recupero sicuro dei campi
	$username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
	$password = isset($_POST['password']) ? (string)$_POST['password'] : '';
	$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

	// CSRF
	if (!verify_csrf_token($csrf_token)) {
		$errors[] = 'Token CSRF non valido. Ricaricare la pagina e riprovare.';
	}

	// Validazioni
	validate_username($username, $errors);
	validate_password($password, $errors);

	if (!empty($errors)) {
		json_response(['success' => false, 'errors' => $errors], 400);
	}

	// Qui i controlli sono passati.
	// Controlla credenziali su DB MySQL
	$auth_success = check_credentials_in_db($username, $password);

	if ($auth_success) {
		$_SESSION['user'] = $username;
		$_SESSION['logged_in'] = true;

		// Redirect a index.html
		header('Location: /index.html');
		exit;
	} else {
		json_response(['success' => false, 'errors' => ['Credenziali non valide.']], 401);
	}
}


?>