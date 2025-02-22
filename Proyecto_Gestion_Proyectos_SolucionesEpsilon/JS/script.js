function mostrarFormulario() {
    document.getElementById("formulario-tarea").style.display = "block";
}

function agregarTarea() {
    let nombre = document.getElementById("nombre").value.trim();
    let descripcion = document.getElementById("descripcion").value.trim();
    let estado = document.getElementById("estado").value;
    let usuario = document.getElementById("usuario").value;

    if (!nombre || !descripcion) {
        alert("Todos los campos son obligatorios.");
        return;
    }

    fetch("agregar_tarea.php", {
        method: "POST",
        body: new URLSearchParams({ nombre, descripcion, estado, usuario }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            let nuevaTarea = document.createElement("div");
            nuevaTarea.classList.add("kanban-item");
            nuevaTarea.setAttribute("data-id", data.tarea.id);
            nuevaTarea.innerHTML = `<strong>${data.tarea.nombre}</strong><p>${data.tarea.descripcion}</p>`;

            let columna = document.querySelector(`.kanban-column[data-estado="${data.tarea.estado_id}"]`);
            columna.appendChild(nuevaTarea);

            document.getElementById("formulario-tarea").style.display = "none";
            document.getElementById("nombre").value = "";
            document.getElementById("descripcion").value = "";
            document.getElementById("usuario").value = "";
        }
    })
    .catch(error => console.error("Error:", error));
}

