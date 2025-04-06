$(document).ready(function () {
    console.log("configlogin.js cargado correctamente");

    // Cambio entre formularios
    $("#register-link").click(function (event) {
        event.preventDefault();
        console.log("Clic en Registrarse detectado.");
        $("#login-form").hide();
        $("#register-form").show();
    });

    $("#login-link").click(function (event) {
        event.preventDefault();
        console.log("Clic en Iniciar sesión detectado.");
        $("#register-form").hide();
        $("#login-form").show();
    });

    // Manejo del registro de usuario
    $("#register-form").submit(function (event) {
        event.preventDefault();
        console.log("Formulario de registro enviado.");

        var formData = $(this).serialize();
        console.log("Datos del formulario:", formData);

        $.ajax({
            url: "backend/register.php",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                console.log("Respuesta del servidor:", response);

                if (response.status === "success") {
                    alert("Registro exitoso. Inicia sesión.");
                    $("#register-form").hide();
                    $("#login-form").show();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en AJAX:", xhr.responseText);
                alert("Error al conectar con el servidor.");
            }
        });
    });

    // Manejo del login
    $("#login-form").submit(function (event) {
        event.preventDefault();
        console.log("Formulario de login enviado.");

        var username = $.trim($('input[name="username"]').val());
        var password = $.trim($('input[name="password"]').val());

        if (username && password) {
            loginUser(username, password);
        } else {
            alert("Por favor, complete todos los campos.");
        }
    });
});

function loginUser(username, password) {
    console.log("Intentando iniciar sesión con:", username, password);

    $.ajax({
        url: "backend/login.php",
        method: "POST",
        data: { username: username, password: encodeURIComponent(password) },
        dataType: "json",
        success: function (response) {
            console.log("Respuesta del servidor:", response);

            if (response.status === "success") {
                window.location.href = "views/home.html"; 
            } else {
                alert("Error: " + response.message);
                $('input[name="username"]').val("");
                $('input[name="password"]').val("");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en AJAX:", xhr.responseText);
            alert("Error al conectar con el servidor: " + xhr.responseText);
        }
    });
}
