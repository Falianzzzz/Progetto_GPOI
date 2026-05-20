const username = document.getElementById("username");   //l'input per l'username deve avere l'id "username"
const password = document.getElementById("password");   //l'input per la password deve avere l'id "password"
const checkbox = document.getElementById("checkbox")    //la chekbox per salvare i dati nel local storeage deve avere come id "checkbox"
const submit = document.getElementById("submit");       //il pulsante submit deve avere l'id submit

try{
    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

    const response = await fetch('login.php', {          // da cambiare il nome del file php probabilmente 
        method: "post",                                  // (login.php è un file inventato)
        body: formData
    });

    const dati = await response.json();

    if (data.success) {
            showMessage(data.message, 'success');
            
            // Salva token/sessione 
            if (data.username) {
                localStorage.setItem('username', data.username);
            }
            
           

    }else{
        allert("Errore! Verifica che i dati siano inseriti correttamente");
    }
}catch (err){
    allert("Errore di connessione");
}
