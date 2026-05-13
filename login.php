<?php 

echo "
<form action='login.php' method='post'>
Username: <input type='text' name='username' required><br>
Password: <input type='text' name='password' required><br>
<buttton type='submit'>Invia</button>
</form>";


if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);

}

?>