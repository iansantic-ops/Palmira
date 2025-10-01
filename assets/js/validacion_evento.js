document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formEvento");

    const nombreEvento = document.getElementById("nombre_evento");
    const descripcion = document.getElementById("descripcion");
    const fecha = document.getElementById("fecha");
    const hora = document.getElementById("hora");
    const lugar = document.getElementById("lugar");
    const aforoMax = document.getElementById("aforo_max");

    // Expresiones regulares
    const regexNombre = /^[a-zA-ZÀ-ÿ0-9\s]{3,50}$/;
    const regexDescripcion = /^.{1,300}$/; 
   const regexLugar = /^.{3,100}$/;
    // Función para validar campo
    const validarCampo = (input, regex) => {
        if (regex.test(input.value.trim())) {
            input.classList.add("valid");
            input.classList.remove("invalid");
            return true;
        } else {
            input.classList.add("invalid");
            input.classList.remove("valid");
            return false;
        }
    };

    // Eventos de validación en vivo
    nombreEvento.addEventListener("input", () => validarCampo(nombreEvento, regexNombre));
    descripcion.addEventListener("input", () => validarCampo(descripcion, regexDescripcion));
    lugar.addEventListener("input", () => validarCampo(lugar, regexLugar));
    aforoMax.addEventListener("input", () => {
        if (aforoMax.value > 0) {
            aforoMax.classList.add("valid");
            aforoMax.classList.remove("invalid");
        } else {
            aforoMax.classList.add("invalid");
            aforoMax.classList.remove("valid");
        }
    });

    // Validación de fecha y hora (mínimo hoy y hora no vacía)
    fecha.addEventListener("input", () => {
        const today = new Date().toISOString().split("T")[0];
        if (fecha.value >= today) {
            fecha.classList.add("valid");
            fecha.classList.remove("invalid");
        } else {
            fecha.classList.add("invalid");
            fecha.classList.remove("valid");
        }
    });

    hora.addEventListener("input", () => {
        if (hora.value) {
            hora.classList.add("valid");
            hora.classList.remove("invalid");
        } else {
            hora.classList.add("invalid");
            hora.classList.remove("valid");
        }
    });

    // Validación final antes de enviar
    form.addEventListener("submit", (e) => {
        if (
            !validarCampo(nombreEvento, regexNombre) ||
            !validarCampo(descripcion, regexDescripcion) ||
            !validarCampo(lugar, regexLugar) ||
            aforoMax.value <= 0 ||
            fecha.classList.contains("invalid") ||
            hora.classList.contains("invalid")
        ) {
            e.preventDefault();
            alert("Por favor, corrige los campos marcados en rojo.");
        }
    });
});
