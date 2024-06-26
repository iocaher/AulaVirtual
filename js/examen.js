
//Función que añade las preguntas al pulsar el botón de añadir pregunta
function anadirPregunta() {

    var preguntaNueva = document.getElementById('preguns');
    // Contar el número de preguntas actuales
    const questionCount = document.getElementsByClassName('question-block').length + 1;

    // Crear un nuevo bloque de pregunta
    const questionBlock = document.createElement('div');
    questionBlock.className = 'question-block';
    questionBlock.id = `question-block-${questionCount}`;

    // Añadir el HTML para la nueva pregunta añadiendo el bloque numerico para controlar mejor los valores.
    questionBlock.innerHTML = `
        <div >
            <label id="campos">Pregunta ${questionCount}: </label>
            <input type="text" name="preguntas[${questionCount}][texto]" required>
            <br>
            <label id="radios">Tipo:</label>
            <input type="radio" name="preguntas[${questionCount}][tipo]" value="test" onclick="showTestOptions(${questionCount})" required> Test
            <input type="radio" name="preguntas[${questionCount}][tipo]" value="desarrollo" onclick="showTestOptions(${questionCount})" required> Desarrollo
            <div id="test-options-${questionCount}" class="test-options" style="display: none;">
            <br>
                <div class="test-options-container" id="test-options-container-${questionCount}"></div><br>
                <button type="button" onclick="addTestOption(${questionCount})">Añadir opción</button>

            </div>
            <br>
            <button type="button" class="remove-question" onclick="removeQuestion(${questionCount})">Eliminar Pregunta</button>
        </div><br>
    `;

    // Añadir el nuevo bloque de pregunta al contenedor
    preguntaNueva.appendChild(questionBlock);
}

//Función que mostrará las distintas configuraciones de las preguntas tipo test y las de desarrollo en funcion de lo que se elija en el formulario
function showTestOptions(questionId) {
    const testOptionsDiv = document.getElementById(`test-options-${questionId}`);
    const radioValue = document.querySelector(`input[name="preguntas[${questionId}][tipo]"]:checked`).value;

    if (radioValue === 'test') {
        testOptionsDiv.style.display = 'block';
    } else {
        testOptionsDiv.style.display = 'none';
        document.getElementById(`test-options-container-${questionId}`).innerHTML = '';
    }
}

// función para añadir opciones si es un tipo test.
function addTestOption(questionId) {
    const optionsContainer = document.getElementById(`test-options-container-${questionId}`);
    const optionCount = optionsContainer.children.length + 1;

    // Crear un nuevo bloque de opción
    const optionBlock = document.createElement('div');
    optionBlock.className = 'option-block';

    // Añadir el HTML para la nueva opción
    optionBlock.innerHTML = `
        <label>Opción ${optionCount}: </label>
        <input type="text" name="preguntas[${questionId}][opciones][${optionCount}][texto]" required>
        <br>
    `;

    // Añadir el nuevo bloque de opción al contenedor de opciones
    optionsContainer.appendChild(optionBlock);
}

// Función para eliminar una pregunta del formulario
function removeQuestion(questionId) {
    const questionBlock = document.getElementById(`question-block-${questionId}`);
    questionBlock.remove();
}