let inputEmail = document.querySelector('#contact_email')

if(inputEmail) {
    let form = document.querySelector('#contactForm')

    inputEmail.addEventListener('change', function(){
        validEmail(this)
    })
    
    form.addEventListener('submit', function(e){
        e.preventDefault();
        if(validEmail(inputEmail)){
        form.submit();
        }
    })

    /**** Validation email ****/
    const validEmail = function(mail){
        // regex pour lemail
        let emailRegExp = new RegExp('^[a-zA-Z0-9.-_]+[@]{1}[a-zA-Z0-9.-_]+[.]{1}[a-z]{2,10}$', 'g')
    
        // Ajout d'un message dans la balise small
        let smallEmail = document.querySelector('#email-message');
    
        if(emailRegExp.test(inputEmail.value)){
            smallEmail.innerHTML = ""
            smallEmail.classList.remove('register-fail')
            return true;
        }
        else{
            smallEmail.innerHTML = "Adresse non valide. "
            smallEmail.classList.remove('register-success')
            smallEmail.classList.add('register-fail')
    
            return false;
        }
    }

}