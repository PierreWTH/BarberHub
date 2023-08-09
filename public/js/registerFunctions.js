let form = document.querySelector('#register-form')
let inputEmail = document.querySelector('#registration_form_email')
let inputPassword = document.querySelector('#registration_form_plainPassword_first')
// On écoute la modification de l'email

inputEmail.addEventListener('change', function(){
   validEmail(this)
})
// On écoute la modification du mdp
inputPassword.addEventListener('change', function(){
   validPassword(this)
})
// On écoute la modificati
form.addEventListener('submit', function(e){
   e.preventDefault();
   if(validEmail(inputEmail) && validPassword(inputPassword)){
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
       smallEmail.innerHTML = "Adresse mail valide. "
       smallEmail.classList.remove('email-fail')
       smallEmail.classList.add('email-success')

       return true;
   }
   else{
       smallEmail.innerHTML = "Adresse non valide. "
       smallEmail.classList.remove('email-success')
       smallEmail.classList.add('email-fail')

       return false;
   }
}
/**** Validation password ****/
const validPassword = function(password){
   let msg;
   let valid = false
   // au moins 3 caractère
   if(inputPassword.value.length < 8){
       msg ='Le mot de passe doit contenir au moins 8 caractères'
   }
   //au moins 1 maj
   else if(!/[A-Z]/.test(inputPassword.value)){
       msg = 'Le mot de passe doit contenr au moins 1 majuscule'
   }
   //au moins 1 min
   else if(!/[a-z]/.test(inputPassword.value)){
       msg = 'Le mot de passe doit contenr au moins 1 minuscule'
   }
   //au moins 1 chiffre
   else if(!/[0-9]/.test(inputPassword.value)){
       msg = 'Le mot de passe doit contenr au moins 1 chiffre'
   }
   else if(!/[#?!@$%^&*-]/.test(inputPassword.value)){
        msg = 'Le mot de passe doit contenr un caractère spécial'
   }
   else{
       msg = 'Mot de passe valide. '
       valid = true;
   }

   let smallPassword = document.querySelector('#password-message');

   if(valid){
       smallPassword.innerHTML = "Mot de passe valid. "
       smallPassword.classList.remove('email-fail')
       smallPassword.classList.add('email-success')

       return true;
   }
   else{
       smallPassword.innerHTML = msg
       smallPassword.classList.remove('email-success')
       smallPassword.classList.add('email-fail')
       
       return false;
   }
}
// SHOW PASSWORD ON CLICK
function showPassword() {
    var inputPassword = document.getElementById("registration_form_plainPassword_first");
    var icon = document.getElementById("password-toggle");

    if (inputPassword.type === "password") {
        inputPassword.type = "text";
        // Remplacez la classe pour afficher l'icône "fa-eye"
        icon.classList.remove("fa-solid", "fa-eye-slash");
        icon.classList.add("fa-regular", "fa-eye");
    } else {
        inputPassword.type = "password";
        // Remplacez la classe pour afficher l'icône "fa-eye-slash"
        icon.classList.remove("fa-regular", "fa-eye");
        icon.classList.add("fa-solid", "fa-eye-slash");
    }
}