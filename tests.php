<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Plataforma de Pruebas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            margin-top: 30px;
        }
        .question {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
        .question-number {
            padding: 5px;
            margin: 5px 0;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            display: inline-block;
            width: 25px;
        }
        .correct {
            background-color: #28a745;
            color: #fff;
        }
        .incorrect {
            background-color: #dc3545;
            color: #fff;
        }
        .not-answered {
            background-color: #ffc107;
            color: #fff;
        }
        .feedback {
            margin-top: 10px;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                margin-bottom: 20px;
            }
        }
        .reset-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h5>Preguntas</h5>
    <div id="question-numbers">
        <!-- Los números de las preguntas se cargarán aquí -->
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12 col-md-10">
            <h1 class="text-center mb-4">Plataforma de Pruebas</h1>
            <div id="questions-container" class="row">
                <?php
                $servername = "localhost"; 
                $username = "u170576779_pauserranoo1"; 
                $password = "TestREBT2024"; 
                $dbname = "u170576779_Preguntes_TEST"; 

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                $sql = "SELECT id, question, option1, option2, option3, option4, correctAnswer FROM questions";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="col-12 col-lg-6">';
                        echo '<div class="question">';
                        echo '<h5>' . $row["id"] . ': ' . $row["question"] . '</h5>';

                        // Definir las opciones
                        $options = [
                            "option1" => $row["option1"],
                            "option2" => $row["option2"],
                            "option3" => $row["option3"],
                            "option4" => $row["option4"]
                        ];

                        // Mostrar las opciones dinámicamente
                        foreach ($options as $key => $option) {
                            $isCorrect = ($option == $row["correctAnswer"]) ? "correct" : "incorrect";
                            echo '<div class="form-check">';
                            echo '<input class="form-check-input" type="radio" name="q' . $row["id"] . '" id="q' . $row["id"] . $key . '" value="' . $isCorrect . '">';
                            echo '<label class="form-check-label" for="q' . $row["id"] . $key . '">' . $option . '</label>';
                            echo '</div>';
                        }

                        echo '<div id="q' . $row["id"] . '-feedback" class="feedback"></div>';
                        echo '</div></div>';
                    }
                } else {
                    echo "<p>No se encontraron preguntas.</p>";
                }
                $conn->close();
                ?>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-success" onclick="calcularNota()">Validar</button>
                <div id="finalResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<button class="btn btn-danger reset-btn">Resetear Respuestas</button>

<script>
    // Cargar los números de las preguntas en la tabla lateral
    document.addEventListener("DOMContentLoaded", function() {
        const totalPreguntas = document.querySelectorAll('.question').length;
        const sidebar = document.getElementById('question-numbers');

        for (let i = 1; i <= totalPreguntas; i++) {
            const questionNumDiv = document.createElement('div');
            questionNumDiv.id = `q-number-${i}`;
            questionNumDiv.classList.add('question-number', 'not-answered');
            questionNumDiv.textContent = i;
            sidebar.appendChild(questionNumDiv);
        }
    });

    function calcularNota() {
        const totalPreguntas = document.querySelectorAll('.question').length;
        let correctas = 0;

        for (let i = 1; i <= totalPreguntas; i++) {
            const selected = document.querySelector(`input[name="q${i}"]:checked`);
            const questionNumberDiv = document.getElementById(`q-number-${i}`);
            const feedbackDiv = document.getElementById(`q${i}-feedback`);

            if (selected) {
                if (selected.value === "correct") {
                    correctas++;
                    questionNumberDiv.classList.add('correct');
                    questionNumberDiv.classList.remove('incorrect', 'not-answered');
                    feedbackDiv.innerHTML = "<span class='text-success'>¡Correcto!</span>";
                } else {
                    questionNumberDiv.classList.add('incorrect');
                    questionNumberDiv.classList.remove('correct', 'not-answered');
                    feedbackDiv.innerHTML = "<span class='text-danger'>Incorrecto.</span>";
                }
            } else {
                questionNumberDiv.classList.add('not-answered');
                questionNumberDiv.classList.remove('correct', 'incorrect');
                feedbackDiv.innerHTML = "<span class='text-warning'>No has respondido.</span>";
            }
        }

        const porcentaje = (correctas / totalPreguntas) * 100;
        const nota = (correctas / totalPreguntas) * 10;

        const resultDiv = document.getElementById('finalResult');
        resultDiv.innerHTML = `<h4>Nota final: ${nota.toFixed(2)}/10<br> Porcentaje de acierto: ${porcentaje.toFixed(2)}%</h4>`;
    }

    document.querySelector('.reset-btn').onclick = function() {
        document.querySelectorAll('.feedback').forEach(div => div.innerHTML = '');
        document.querySelectorAll('.question-number').forEach(div => {
            div.classList.remove('correct', 'incorrect', 'not-answered');
            div.classList.add('not-answered');
        });
        document.getElementById('finalResult').innerHTML = '';
        document.querySelectorAll('input[type="radio"]:checked').forEach(input => input.checked = false);
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
