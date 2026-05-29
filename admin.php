<?php
// Admin 
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

$host = 'localhost';
$db   = 'palestra';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host,$user, $pass, $db)
    or die("Errore di connessione al db " . mysqli_connect_error());


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista Clienti</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            margin: 0;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background-color: #4e73df;
            color: white;
        }

        th, td {
            padding: 12px 15px;
        }

        th {
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
        }
    </style>
</head>

<body>



<h1>Lista Clienti</h1>

<div class="table-container">
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Nome</th>
            <th>Cognome</th>
            <th>Creazione Account</th>
        </tr>
    </thead>
    <tbody>

<?php
$query = "SELECT * FROM users";
$result = mysqli_query($connection, $query)
    or die("Errore query " . mysqli_error($connection));

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['username']}</td>
            <td>{$row['email']}</td>
            <td>{$row['first_name']}</td>
            <td>{$row['last_name']}</td>
            <td>{$row['created_at']}</td>
          </tr>";
}

mysqli_close($connection);
?>

    </tbody>
</table>
</div>

</body>
</html>