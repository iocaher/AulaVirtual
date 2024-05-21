var codigosecreto ='kursaal';

document.addEventListener('DOMContentLoaded', function() {
    var formulario = document.getElementById('registro');
    var oculto = document.getElementById('oculto');
    var rolProfesor = document.getElementById('rol_profesor');
    var rolAlumno = document.getElementById('rol_alumno');
    var claveCole = document.querySelector('input[name="clavecole"]');
    var errorMensaje = document.getElementById('errorMensaje');

    // Mostrar u ocultar el campo de clave según el tipo de usuario seleccionado
    function toggleClaveCole() {
        if (rolProfesor.checked) {
            oculto.style.display = 'block';
            claveCole.required = true;
        } else {
            oculto.style.display = 'none';
            claveCole.required = false;
        }
    }

    // Inicializar la visibilidad del campo de clave
    toggleClaveCole();

    // Event listener para cambiar la visibilidad del campo de clave al cambiar el rol
    rolProfesor.addEventListener('change', toggleClaveCole);
    rolAlumno.addEventListener('change', toggleClaveCole);

    formulario.addEventListener('submit', function(event) {
        if (rolProfesor.checked) {
            if (claveCole.value !== codigosecreto) {
                errorMensaje.style.display = 'block';
                event.preventDefault();
            }
        }
    });
});