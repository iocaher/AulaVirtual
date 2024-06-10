document.getElementById('email').addEventListener('blur', function() {

    var email = this.value;

    if (!email.includes('@')) {

        this.value = '';
        
        alert('Por favor, introduce un email válido que contenga "@"');
    }
});