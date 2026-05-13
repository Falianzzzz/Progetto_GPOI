const nome = document.getElementById("nome");
const password = document.getElementById("password");
const submit = document.getElementById("submit"); 

try{
    const formData = new FormData();
    formData.append("nome", nome);
    formData.append("password", password);

    const response = await fetch('login.php', {          // da cambiare il nome del file php probabilmente 
        method: "post",                                  // (login.php è un file inventato)
        body: formData
    });

    const dati = await response.json();

    if (data.success) {
            showMessage(data.message, 'success');
            
            // Salva token/sessione (opzionale)
            if (data.username) {
                localStorage.setItem('username', data.username);
            }
            
           

    }
}catch (errore){

}
