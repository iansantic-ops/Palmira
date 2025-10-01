document.addEventListener("DOMContentLoaded", () => {
    const inputs = {
        nombre: {
            regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/,
            mensaje: "❌ Solo letras y espacios (2-40 caracteres)"
        },
        apellidos: {
            regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,60}$/,
            mensaje: "❌ Solo letras y espacios (2-60 caracteres)"
        },
        telefono: {
            regex: /^[0-9]{10}$/,
            mensaje: "❌ Debe contener 10 dígitos"
        },
        correo: {
            regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/,
            mensaje: "❌ Formato de correo inválido"
        },
        origen: {
            regex: /^.{2,100}$/,
            mensaje: "❌ Debe tener entre 2 y 100 caracteres"
        }
        // El campo país y lada ya están en select, no requieren regex aquí.
    };

    for (let campo in inputs) {
        const inputElement = document.getElementById(campo);
        const info = inputs[campo];

        if (inputElement) {
            // Evita duplicar el <small>
            if (
                !inputElement.nextElementSibling ||
                !inputElement.nextElementSibling.classList.contains("error-msg")
            ) {
                const errorMsg = document.createElement("small");
                errorMsg.classList.add("error-msg");
                inputElement.insertAdjacentElement("afterend", errorMsg);
            }

            const errorMsg = inputElement.nextElementSibling;

            inputElement.addEventListener("input", () => {
                if (info.regex.test(inputElement.value.trim())) {
                    inputElement.classList.remove("invalid");
                    inputElement.classList.add("valid");
                    errorMsg.textContent = "✅";
                    errorMsg.style.color = "green";
                } else {
                    inputElement.classList.remove("valid");
                    inputElement.classList.add("invalid");
                    errorMsg.textContent = info.mensaje;
                    errorMsg.style.color = "red";
                }
            });
        }
    }

    // Validación final antes de enviar el formulario
    const form = document.querySelector("form");
    form.addEventListener("submit", (e) => {
        let valido = true;

        for (let campo in inputs) {
            const inputElement = document.getElementById(campo);
            const info = inputs[campo];

            if (inputElement && !info.regex.test(inputElement.value.trim())) {
                valido = false;
                inputElement.classList.add("invalid");
                inputElement.nextElementSibling.textContent = info.mensaje;
                inputElement.nextElementSibling.style.color = "red";
            }
        }

        if (!valido) {
            e.preventDefault();
            alert("⚠️ Revisa los campos marcados en rojo antes de enviar.");
        }
    });
});
