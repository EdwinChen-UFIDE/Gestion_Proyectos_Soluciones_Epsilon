<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirigir al login
    exit();
}
require_once 'db_config.php';
include 'Plantilla.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        #container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border-radius: 8px;
            background: #f1f1f1;
        }

        .add-button {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-button:hover {
            background: #218838;
        }

        #file-list {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            transition: background 0.3s;
        }

        .file-item:hover {
            background: #f0f0f0;
        }

        .file-item i {
            font-size: 20px;
            color: #007bff;
            margin-right: 10px;
        }

        .file-item a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            flex-grow: 1;
        }

        .file-item a:hover {
            text-decoration: underline;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <?php MostrarNavbar(); ?>

    <div id="container">
        <h1>Bienvenido a la Página con Plantilla</h1>
        <p>Aquí va el contenido específico de cada página.</p>

        <h2>Subir un Archivo</h2>
        <form id="upload-form" enctype="multipart/form-data">
            <input type="file" name="file" id="file" required>
            <button class="add-button"type="submit">Subir</button>
        </form>

        <div id="upload-status"></div>

        <h2>Archivos Subidos</h2>
        <div id="file-list">
            <!-- Aquí se mostrarán los archivos subidos -->
        </div>
    </div>

    <script>
        document.getElementById("upload-form").addEventListener("submit", function(e) {
            e.preventDefault();
            
            let formData = new FormData();
            formData.append("file", document.getElementById("file").files[0]);

            fetch("upload.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("upload-status").innerHTML = data;
                loadFiles();
            })
            .catch(error => console.error("Error:", error));
        });

        function loadFiles() {
            fetch("list_files.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById("file-list").innerHTML = data;
            });
        }

        function deleteFile(filename) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción eliminará el archivo de forma permanente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("delete_file.php?file=" + filename, {
                        method: "GET"
                    })
                    .then(response => response.text())
                    .then(data => {
                        Swal.fire("Eliminado", data, "success");
                        loadFiles();
                    })
                    .catch(error => console.error("Error:", error));
                }
            });
        }

        loadFiles();
    </script>

</body>
</html>
