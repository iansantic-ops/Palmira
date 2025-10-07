<?php
session_start();
if (isset($_SESSION['idUsuario'])) {
     header("location:eventos_disponibles.php");
    exit();
}
require_once "assets/sentenciasSQL/usuarios.php";

if (isset($_POST['registrar'])) {
    if(!empty($_POST['nombre']) && !empty($_POST['apellidos']) && !empty($_POST['telefono']) && !empty($_POST['correo'])
    && !empty($_POST['medio']) && !empty($_POST['origen']) && !empty($_POST['pais'])) {

        $nombre    = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
        $apellidos = htmlspecialchars(trim($_POST['apellidos']), ENT_QUOTES, 'UTF-8');
        $lada      = htmlspecialchars(trim($_POST['lada']), ENT_QUOTES, 'UTF-8');
        $telefono  = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
        $correo    = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
        $medio = $_POST['medio'];
        if ($medio === "OTRO" && !empty($_POST['otro_medio'])) {
            $medio = $_POST['otro_medio']; // guardar lo que escribió el usuario
        }
        
        $origen    = htmlspecialchars(trim($_POST['origen']), ENT_QUOTES, 'UTF-8');
        $pais      = htmlspecialchars(trim($_POST['pais']), ENT_QUOTES, 'UTF-8');

        $registro = new Usuarios();

        //verifica si ya existe un usuario con los mismos atos 
        $usuarioExistente = $registro->buscarUsuarioRegistrado($correo, $lada, $telefono);

        if ($usuarioExistente) {
            echo <<<HTML
<div id="qrModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('qrModal').style.display='none'">&times;</span>
    <h2>Usuario ya registrado</h2>
    <p>te invitamos a iniciar sesion <a href="index.php">aqui</a></p>
    <img src="assets/img/error.png" alt="Error" width="150">
  </div>
</div>
<script>
  document.getElementById('qrModal').style.display = 'block';
</script>
HTML;

        } else {
            //movi esto a un else
            $idUsuario = random_int(10000000, 99999999);
            $registrarUsu = $registro->darAlta($idUsuario, $nombre, $apellidos, $lada, $telefono, $correo, $medio, $origen, $pais);

            if($registrarUsu === true) {
                if (session_status() !== PHP_SESSION_ACTIVE) session_start();

                $_SESSION['idUsuario'] = $idUsuario;
                $_SESSION['nombre']    = $nombre;
                $_SESSION['correo']    = $correo;
                $_SESSION['telefono']  = $telefono;

                
                echo <<<HTML
<div id="successModal" class="modal">
  <div class="modal-content">
    <h2>Registro exitoso</h2>
    <p>Bienvenido, $nombre. Serás redirigido a los eventos disponibles.</p>
  </div>
</div>
<script>
  var modal = document.getElementById('successModal');
  modal.style.display = 'block';
  setTimeout(function() {
      window.location.href = 'eventos_disponibles.php';
  }, 4000);
</script>
HTML;
            } else {
//error del registro Bv
                echo <<<HTML
<div id="qrModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('qrModal').style.display='none'">&times;</span>
    <h2>No se pudo completar el registro</h2>
    <p>Revisa tus datos.</p>
    <img src="assets/img/error.png" alt="Error" width="150">
  </div>
</div>
<script>
  document.getElementById('qrModal').style.display = 'block';
</script>
HTML;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/formularios.css">
    <title>Registro</title>
    <script src="assets/js/validacion.js"></script>
</head>
<body>
<div class="wrapper">        
    <form action="registro.php" method="post">
        <h1>Registrate</h1>
        <br>
        <h2>Ingresa tus datos:</h2>

        <div class="input-box">
            <label for="nombre">Nombre(s):</label>
            <input type="text" name="nombre" class="formulario_input" id="nombre" value="<?= isset($nombre) ? $nombre : '' ?>" required>
        </div>

        <div class="input-box">
            <label for="apellidos">Apellidos:</label>
            <input type="text" name="apellidos" class="formulario_input" id="apellidos" value="<?= isset($apellidos) ? $apellidos : '' ?>" required>
        </div>
        
       


<div class="##">
    <label for="telefono">Número Whatsapp:</label>
    <div class="numero">
           <select name="lada" id="lada" required>
    <!-- América del Norte y Caribe -->
    <option value="+52">+52 (México)</option>
    <option value="+1">+1 (Estados Unidos / Canadá)</option>
    <option value="+53">+53 (Cuba)</option>
    <option value="+1">+1 (República Dominicana)</option>
    <option value="+1">+1 (Jamaica)</option>
    <option value="+1">+1 (Puerto Rico)</option>
    <option value="+1">+1 (Trinidad y Tobago)</option>
    <option value="+1">+1 (Barbados)</option>
    <option value="+1">+1 (Bahamas)</option>
    <option value="+1">+1 (Granada)</option>
    <option value="+1">+1 (Santa Lucía)</option>
    <option value="+1">+1 (San Vicente y las Granadinas)</option>
    <option value="+1">+1 (Antigua y Barbuda)</option>
    <option value="+1">+1 (Dominica)</option>
    <option value="+1">+1 (San Cristóbal y Nieves)</option>
    <option value="+509">+509 (Haití)</option>

    <!-- América Central -->
    <option value="+502">+502 (Guatemala)</option>
    <option value="+503">+503 (El Salvador)</option>
    <option value="+504">+504 (Honduras)</option>
    <option value="+505">+505 (Nicaragua)</option>
    <option value="+506">+506 (Costa Rica)</option>
    <option value="+507">+507 (Panamá)</option>
    <option value="+508">+508 (San Pedro y Miquelón)</option>

    <!-- América del Sur -->
    <option value="+54">+54 (Argentina)</option>
    <option value="+591">+591 (Bolivia)</option>
    <option value="+55">+55 (Brasil)</option>
    <option value="+56">+56 (Chile)</option>
    <option value="+57">+57 (Colombia)</option>
    <option value="+593">+593 (Ecuador)</option>
    <option value="+595">+595 (Paraguay)</option>
    <option value="+51">+51 (Perú)</option>
    <option value="+598">+598 (Uruguay)</option>
    <option value="+58">+58 (Venezuela)</option>
    <option value="+597">+597 (Surinam)</option>
    <option value="+592">+592 (Guyana)</option>
    <option value="+594">+594 (Guayana Francesa)</option>

    <!-- Europa -->
    <option value="+34">+34 (España)</option>
    <option value="+33">+33 (Francia)</option>
    <option value="+49">+49 (Alemania)</option>
    <option value="+39">+39 (Italia)</option>
    <option value="+44">+44 (Reino Unido)</option>
    <option value="+31">+31 (Países Bajos)</option>
    <option value="+351">+351 (Portugal)</option>
    <option value="+41">+41 (Suiza)</option>
    <option value="+46">+46 (Suecia)</option>
    <option value="+43">+43 (Austria)</option>
    <option value="+47">+47 (Noruega)</option>
    <option value="+45">+45 (Dinamarca)</option>
    <option value="+48">+48 (Polonia)</option>
    <option value="+420">+420 (Chequia)</option>
    <option value="+421">+421 (Eslovaquia)</option>
    <option value="+36">+36 (Hungría)</option>
    <option value="+30">+30 (Grecia)</option>
    <option value="+32">+32 (Bélgica)</option>
    <option value="+40">+40 (Rumanía)</option>
    <option value="+380">+380 (Ucrania)</option>
    <option value="+7">+7 (Rusia / Kazajistán)</option>
    <option value="+372">+372 (Estonia)</option>
    <option value="+371">+371 (Letonia)</option>
    <option value="+370">+370 (Lituania)</option>
    <option value="+356">+356 (Malta)</option>
    <option value="+357">+357 (Chipre)</option>
    <option value="+358">+358 (Finlandia)</option>
    <option value="+386">+386 (Eslovenia)</option>
    <option value="+385">+385 (Croacia)</option>
    <option value="+382">+382 (Montenegro)</option>
    <option value="+381">+381 (Serbia)</option>
    <option value="+387">+387 (Bosnia y Herzegovina)</option>
    <option value="+389">+389 (Macedonia del Norte)</option>
    <option value="+373">+373 (Moldavia)</option>
    <option value="+354">+354 (Islandia)</option>
    <option value="+423">+423 (Liechtenstein)</option>
    <option value="+352">+352 (Luxemburgo)</option>
    <option value="+376">+376 (Andorra)</option>
    <option value="+377">+377 (Mónaco)</option>
    <option value="+378">+378 (San Marino)</option>
    <option value="+500">+500 (Islas Malvinas)</option>

    <!-- Asia -->
    <option value="+81">+81 (Japón)</option>
    <option value="+86">+86 (China)</option>
    <option value="+91">+91 (India)</option>
    <option value="+92">+92 (Pakistán)</option>
    <option value="+880">+880 (Bangladés)</option>
    <option value="+94">+94 (Sri Lanka)</option>
    <option value="+977">+977 (Nepal)</option>
    <option value="+975">+975 (Bután)</option>
    <option value="+960">+960 (Maldivas)</option>
    <option value="+82">+82 (Corea del Sur)</option>
    <option value="+850">+850 (Corea del Norte)</option>
    <option value="+855">+855 (Camboya)</option>
    <option value="+856">+856 (Laos)</option>
    <option value="+66">+66 (Tailandia)</option>
    <option value="+65">+65 (Singapur)</option>
    <option value="+60">+60 (Malasia)</option>
    <option value="+62">+62 (Indonesia)</option>
    <option value="+63">+63 (Filipinas)</option>
    <option value="+670">+670 (Timor Oriental)</option>
    <option value="+90">+90 (Turquía)</option>
    <option value="+98">+98 (Irán)</option>
    <option value="+964">+964 (Irak)</option>
    <option value="+962">+962 (Jordania)</option>
    <option value="+961">+961 (Líbano)</option>
    <option value="+972">+972 (Israel)</option>
    <option value="+970">+970 (Palestina)</option>
    <option value="+963">+963 (Siria)</option>
    <option value="+968">+968 (Omán)</option>
    <option value="+967">+967 (Yemen)</option>
    <option value="+965">+965 (Kuwait)</option>
    <option value="+973">+973 (Baréin)</option>
    <option value="+974">+974 (Catar)</option>
    <option value="+971">+971 (Emiratos Árabes Unidos)</option>
    <option value="+996">+996 (Kirguistán)</option>
    <option value="+993">+993 (Turkmenistán)</option>
    <option value="+992">+992 (Tayikistán)</option>
    <option value="+994">+994 (Azerbaiyán)</option>
    <option value="+995">+995 (Georgia)</option>
    <option value="+976">+976 (Mongolia)</option>
    <option value="+993">+993 (Turkmenistán)</option>

    <!-- Oceanía -->
    <option value="+61">+61 (Australia)</option>
    <option value="+64">+64 (Nueva Zelanda)</option>
    <option value="+679">+679 (Fiyi)</option>
    <option value="+675">+675 (Papúa Nueva Guinea)</option>
    <option value="+682">+682 (Islas Cook)</option>
    <option value="+685">+685 (Samoa)</option>
    <option value="+686">+686 (Kiribati)</option>
    <option value="+687">+687 (Nueva Caledonia)</option>
    <option value="+688">+688 (Tuvalu)</option>
    <option value="+689">+689 (Polinesia Francesa)</option>
    <option value="+690">+690 (Tokelau)</option>
    <option value="+691">+691 (Micronesia)</option>
    <option value="+692">+692 (Islas Marshall)</option>
    <option value="+674">+674 (Nauru)</option>
    <option value="+676">+676 (Tonga)</option>
    <option value="+677">+677 (Islas Salomón)</option>
    <option value="+678">+678 (Vanuatu)</option>

    <!-- África -->
    <option value="+27">+27 (Sudáfrica)</option>
    <option value="+20">+20 (Egipto)</option>
    <option value="+234">+234 (Nigeria)</option>
    <option value="+212">+212 (Marruecos)</option>
    <option value="+213">+213 (Argelia)</option>
    <option value="+216">+216 (Túnez)</option>
    <option value="+218">+218 (Libia)</option>
    <option value="+221">+221 (Senegal)</option>
    <option value="+225">+225 (Costa de Marfil)</option>
    <option value="+226">+226 (Burkina Faso)</option>
    <option value="+233">+233 (Ghana)</option>
    <option value="+231">+231 (Liberia)</option>
    <option value="+232">+232 (Sierra Leona)</option>
    <option value="+237">+237 (Camerún)</option>
    <option value="+243">+243 (República Democrática del Congo)</option>
    <option value="+260">+260 (Zambia)</option>
    <option value="+263">+263 (Zimbabue)</option>
    <option value="+251">+251 (Etiopía)</option>
    <option value="+256">+256 (Uganda)</option>
    <option value="+254">+254 (Kenia)</option>
    <option value="+250">+250 (Ruanda)</option>
    <option value="+255">+255 (Tanzania)</option>
    <option value="+252">+252 (Somalia)</option>
    <option value="+258">+258 (Mozambique)</option>
    <option value="+265">+265 (Malaui)</option>
    <option value="+266">+266 (Lesoto)</option>
    <option value="+268">+268 (Esuatini)</option>
    <option value="+269">+269 (Comoras)</option>
    <option value="+248">+248 (Seychelles)</option>
    <option value="+290">+290 (Santa Elena)</option>
</select>
           <input type="number" name="telefono" class="formulario_input" id="telefono" value="<?= isset($telefono) ? $telefono : '' ?>" required>
       </div>
    
</div>


        <div class="input-box">
            <label for="correo">Correo electrónico:</label>
            <input type="text" name="correo" class="formulario_input" id="correo" value="<?= isset($correo) ? $correo : '' ?>" required>
        </div>

        <div class="input-box">
    <label for="medio">Medio por el que se entera de los eventos:</label>
    <select name="medio" id="medio" required onchange="toggleOtroMedio()">
        <?php
        $medios = [
            "REDES SOCIALES",
            "CORREO ELECTRÓNICO",
            "UNIVERSIDAD / INSTITUCIÓN",
            "AMIGOS / FAMILIA",
            "POSTER / CARTEL",
            "PÁGINA WEB",
            "OTRO"
        ];

        foreach ($medios as $opcion) {
            $selected = (isset($medio) && $medio === $opcion) ? 'selected' : '';
            echo "<option value=\"$opcion\" $selected>$opcion</option>";
        }
        ?>
    </select>

    
</div>
    <!-- Campo oculto que solo aparece si elige "OTRO" -->
        <div id="otroWrapper" class="otro-wrapper">
            <label for="otro_medio">¿Cuál?</label>
            <input type="text" name="otro_medio" id="otro_medio" 
                   placeholder="Especifique otro medio"
                   value="<?= isset($otro_medio) ? $otro_medio : '' ?>">
        </div>


        <div class="input-box">
            <label for="origen">Institución de origen:</label>
            <input type="text" name="origen" class="formulario_input" id="origen" value="<?= isset($origen) ? $origen : '' ?>" required>
        </div>

        <div class="input-box">
            <label for="pais">País:</label>
            <select name="pais" id="pais" required>
                <?php
                $paises = [
                    // América del Norte y Central
                    "CANADÁ", "ESTADOS UNIDOS", "MÉXICO", "GUATEMALA", "EL SALVADOR", 
                    "HONDURAS", "NICARAGUA", "COSTA RICA", "PANAMÁ", "CUBA", 
                    "REPÚBLICA DOMINICANA", "JAMAICA", "HAITÍ",
                
                    // América del Sur
                    "ARGENTINA", "BOLIVIA", "BRASIL", "CHILE", "COLOMBIA", 
                    "ECUADOR", "PARAGUAY", "PERÚ", "URUGUAY", "VENEZUELA",
                
                    // Europa
                    "ESPAÑA", "FRANCIA", "ALEMANIA", "ITALIA", "REINO UNIDO",
                    "PAÍSES BAJOS", "RUSIA", "PORTUGAL", "SUIZA", "SUECIA",
                
                    // Asia
                    "JAPÓN", "CHINA", "INDIA", "COREA DEL SUR", "ARABIA SAUDITA",
                
                    // Oceanía
                    "AUSTRALIA", "NUEVA ZELANDA",
                
                    // África
                    "SUDÁFRICA", "EGIPTO", "NIGERIA", "MARRUECOS",
                
                    // Otros
                    "OTRO..."
                ];

                sort($paises);

                foreach ($paises as $opcion) {
                    $selected = (isset($pais) && $pais === $opcion) ? 'selected' : '';
                    echo "<option value=\"$opcion\" $selected>$opcion</option>";
                }
                ?>
            </select>
        </div>

        <br>
        <div class="button-group">
            <button type="submit" id="enviar" name="registrar" value="guardar" class="btn">Registrarse</button>
            
        </div>
        <br>
        <p>¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a></p>
    </form>
</div>

</body>
    <script>
function toggleOtroMedio() {
    const select = document.getElementById('medio');
    const wrapper = document.getElementById('otroWrapper');
    const inputOtro = document.getElementById('otro_medio');

    if (select.value === "OTRO") {
        wrapper.classList.add("active");
        inputOtro.required = true;
    } else {
        wrapper.classList.remove("active");
        inputOtro.required = false;
        inputOtro.value = ""; // limpiar si cambia de opinión
    }
}
</script>

</html>


