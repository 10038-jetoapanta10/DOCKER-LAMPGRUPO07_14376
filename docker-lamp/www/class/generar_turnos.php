

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Agendamiento</title>
    <link rel="stylesheet" href="../css/style1.css">

</head>
<body>
    <div class="confirmacion">
        <h1>Turno Agendado</h1>

        <div id="mensajeAgendado" class="mensaje-agendado"></div>

        <a href="../index.html"><br> regresar</a>

    </div>

    <script>
        // Lógica para obtener la fecha y hora seleccionadas y mostrar el mensaje
        const mensajeAgendado = document.getElementById('mensajeAgendado');
        mensajeAgendado.textContent = 'Turno agendado correctamente';

        mensajeAgendado.textContent += `día ${fechaSeleccionada} `;
        
        if (horasSeleccionadas.length > 0) {
            mensajeAgendado.textContent += 'a las siguientes horas: ';
            horasSeleccionadas.forEach(hora => {
                mensajeAgendado.textContent += `${hora}, `;
            });
            mensajeAgendado.textContent = mensajeAgendado.textContent.slice(0, -2); // Eliminar la última coma
        }

        // Mostrar el mensaje
        mensajeAgendado.style.display = 'block';
    </script>
</body>
</html>