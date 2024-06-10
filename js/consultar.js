
//Evento que coloreará los contenedores de las respuestas segun si son correctas o no
document.addEventListener('DOMContentLoaded', function() {
    var respuestas = document.querySelectorAll('.respuesta');
    respuestas.forEach(function(respuesta) {
        var correcta = respuesta.getAttribute('data-correcta');
        if (correcta === 'SI') {
            respuesta.style.backgroundColor = 'rgba(0, 255, 0, 0.2)';
        } else if (correcta === 'NO') {
            respuesta.style.backgroundColor = 'rgba(255, 0, 0, 0.2)';
        }
    });
});