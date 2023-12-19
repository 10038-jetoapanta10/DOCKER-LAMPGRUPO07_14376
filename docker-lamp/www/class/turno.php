<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Turnos</title>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<form action="generar_turnos.php" method="post">
        <h1>Generador de Turnos</h1>

        <div>
            <label for="mes">Mes:</label>
            <select id="mes" name="mes" required>
                <?php
                $mesActual = date("n");
                $nombresMeses = [
                    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio",
                    7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
                ];

                for ($mes = 1; $mes <= 12; $mes++) {
                    echo "<option value=\"$mes\"";
                    if ($mes == $mesActual) {
                        echo " selected";
                    }
                    echo ">" . $nombresMeses[$mes] . "</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="anio">Año:</label>
            <select name="anio" required>
                <?php
                $anioActual = date("Y");
                for ($anio = 2000; $anio <= 2030; $anio++) {
                    echo "<option value=\"$anio\"";
                    if ($anio == $anioActual) {
                        echo " selected";
                    }
                    echo ">$anio</option>";
                }
                ?>
            </select>
        </div>

        <div class="calendario" id="calendario"></div>
                
        <div class="titulo-horas"> <br> Horarios Disponibles</div>
        <div class="horas" id="horas"></div>

        <button type="submit">Generar Turnos</button>
    </form>

    <script>
    const calendario = document.getElementById('calendario');
    const horasContainer = document.getElementById('horas');

    for (let dia = 1; dia <= 31; dia++) {
        const diaElemento = document.createElement('div');
        diaElemento.classList.add('dia', 'disponible');
        diaElemento.textContent = dia;

        diaElemento.addEventListener('click', () => {
            horasContainer.innerHTML = ''; // Limpiar las horas al cambiar el día
            const horasDisponibles = [
                '08:00 - 09:00', '09:00 - 10:00', '10:00 - 11:00', '11:00 - 12:00',
                '18:00 - 19:00', '19:00 - 20:00', '20:00 - 21:00', '21:00 - 22:00', '22:00 - 23:00', '23:00 - 00:00'
            ];

            horasDisponibles.forEach(hora => {
                const horaElemento = document.createElement('div');
                horaElemento.classList.add('hora', 'disponible');
                horaElemento.textContent = hora;

                // Simular algunas horas ocupadas (debes obtener esta información de tu base de datos)
                if (dia % 2 === 0) {
                    horaElemento.classList.remove('disponible');
                    horaElemento.classList.add('ocupada');
                }

                horaElemento.addEventListener('click', () => {
                    horaElemento.classList.toggle('seleccionado');
                });

                horasContainer.appendChild(horaElemento);
            });

            document.querySelectorAll('.dia').forEach(elemento => {
                elemento.classList.remove('ocupado', 'disponible', 'seleccionado');
                elemento.classList.add('disponible');
            });
            diaElemento.classList.add('seleccionado');
        });

        calendario.appendChild(diaElemento);
    }



    function enviarFormulario() {
        // Obtener fecha seleccionada desde el formulario original
        const fechaSeleccionadaElemento = document.querySelector('.dia.seleccionado');
        const fechaSeleccionada = fechaSeleccionadaElemento ? fechaSeleccionadaElemento.textContent : '';

        // Obtener horas seleccionadas desde el formulario original
        const horasSeleccionadasElementos = document.querySelectorAll('.hora.seleccionado');
        const horasSeleccionadas = Array.from(horasSeleccionadasElementos).map(horaElemento => horaElemento.textContent);

        // Actualizar los campos ocultos en el formulario
        document.getElementById('fechaSeleccionada').value = fechaSeleccionada;
        document.getElementById('horasSeleccionadas').value = horasSeleccionadas.join(', ');

        // Enviar el formulario
        document.getElementById('turnoForm').submit();
    }
</script>
</body>
</html>
