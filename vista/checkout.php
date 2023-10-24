<?php
session_start();
$tok = bin2hex(random_bytes(32));
$_SESSION["token"] = $tok;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Bodegest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="checkout.css">
    <script src="https://testcheckout.izipay.pe/payments/v1/js/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


</head>

<body>
    <div class="container">
        <div class="p-3" id="div_check">
            <div class="flex">
                <?php
                $key = "er";

                // Supongamos que $_SESSION["response"] es el arreglo en la sesión
                if (isset($_SESSION["response"])) {
                    foreach ($_SESSION["response"] as $clave => $mensaje) {
                        if ($clave === $key) {
                ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                            <br>
                            <center>
                                <div class="row" style="background-color: white;border-radius: 5px;width: 60%;">
                                    <div class="col-12 p-4">
                                        <br>
                                        <input type="button" value="Volver a Inicio" class="btn btn-success" id="back">
                                    </div>
                                    <div class="col-12">
                                        <a href="index.php" style="text-decoration: none;"><img src="images/logo.png" alt="" width="200" height="150"></a>
                                    </div>

                                </div>
                            </center>
                        <?php
                        } else {
                        ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                            <br>
                            <center>
                                <div class="container" style="background-color: white;border-radius: 5px;">
                                    <a href="index.php" style="text-decoration: none; color: black;">Volver a Inicio <img src="images/logo.png" alt="" width="200" height="150"></a>

                                </div>
                            </center>
                <?php
                        }
                    }
                    unset($_SESSION["response"]); // Mueve esta línea fuera del bucle
                }
                ?>
            </div>

            <div class="row">
                <div class=" card col-12">
                    <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                        <div class="position-relative z-index-1 light">
                            <h4 class="mb-0 text-primary">Registrarse</h4>
                            <p class="fs--1 mb-0 text-black">Por favor crea tu cuenta BodeGest </p>
                        </div>
                    </div>
                    <form action="request.php" method="post" class="py-4 px-5">
                        <input type="hidden" name="csrf_token" value="<?php echo $tok; ?>">

                        <div class="mb-3">
                            <label class="form-label" for="modal-auth-name">Nombres</label>
                            <input class="form-control" type="text" autocomplete="on" id="modal-auth-name" name="modal-auth-name" required="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="modal-auth-apellidos">Apellidos</label>
                            <input class="form-control" type="text" autocomplete="on" id="modal-auth-apellidos" name="modal-auth-apellidos" required="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="modal-auth-dni">Dni</label>
                            <input class="form-control" type="number" autocomplete="on" id="modal-auth-dni" name="modal-auth-dni" required="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="modal-auth-email">Email </label>
                            <input class="form-control" type="email" autocomplete="on" id="modal-auth-email" name="modal-auth-email" required="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="modal-auth-phone">Telefono</label>
                            <input class="form-control" type="number" autocomplete="on" id="modal-auth-phone" name="modal-auth-phone" required="">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modal-auth-register-checkbox" checked="" required>
                            <label class="form-label" for="modal-auth-register-checkbox">Yo Acepto los <button style="border: none;color: blue;background-color: #151515;" type="button" data-bs-target="#exampleModal" data-bs-toggle="modal">Terminos y Condiciones
                                </button></label>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-block w-100 mt-3" type="submit" id="btn-registrarse" name="button" value="Registrarse">Proceder con el Pago</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0 text-primary">Términos y Condiciones</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="bkng-tb-cntnt p-2">
                        <p><strong>Fecha de Actualización</strong> </p>
                        <p>Estos Términos y Condiciones fueron actualizados por última vez el 24/10/2023. Al utilizar nuestra aplicación web, usted acepta estar sujeto a la versión más reciente de estos términos.</p>

                        <p><strong>Aceptación de los términos y condiciones:</strong></p>
                        <p>Al utilizar nuestra aplicación web de proceso de ventas en la nube (en adelante, "la Aplicación"), el usuario acepta los siguientes términos y condiciones. Si no está de acuerdo con estos términos, por favor, absténgase de utilizar la Aplicación.</p>

                        <h5>Licencia de uso</h5>

                        <p> Se otorga al usuario una licencia no exclusiva, intransferible y limitada para utilizar la Aplicación de acuerdo con los términos establecidos en este documento. Esta licencia está sujeta al pago de la suscripción mensual correspondiente.
                        </p>
                        <h5> Propiedad intelectual</h5>

                        <p> Todos los derechos de propiedad intelectual de la Aplicación, incluyendo pero no limitándose a software, diseños, logotipos y contenido, son propiedad exclusiva de nuestra empresa. Queda estrictamente prohibida la reproducción, distribución o modificación no autorizada de la Aplicación.
                        </p>
                        <h5>Limitaciones de responsabilidad</h5>

                        <p> El uso de la Aplicación es bajo la responsabilidad exclusiva del usuario. No nos hacemos responsables por cualquier daño, pérdida o perjuicio derivado del uso de la Aplicación, incluyendo pero no limitándose a errores, interrupciones o inexactitudes en el funcionamiento de la misma.
                        </p>
                        <h5>Garantías</h5>

                        <p> La Aplicación se proporciona "tal cual" sin garantías de ningún tipo, ya sean explícitas o implícitas. No garantizamos la disponibilidad ininterrumpida o libre de errores de la Aplicación, ni la precisión o confiabilidad de su contenido.
                        </p>
                        <h5> Actualizaciones y soporte</h5>

                        <p> Nos reservamos el derecho de realizar actualizaciones, mejoras o modificaciones en la Aplicación en cualquier momento. Podemos ofrecer soporte técnico para la resolución de problemas relacionados con la Aplicación de acuerdo con los términos y condiciones adicionales establecidos para dicho soporte.
                        </p>
                        <h5> Uso permitido</h5>

                        <p> El usuario se compromete a utilizar la Aplicación de acuerdo con la legislación aplicable y estos términos y condiciones. Queda prohibido realizar actividades ilegales, no autorizadas o que puedan afectar la seguridad o integridad de la Aplicación o de otros usuarios.
                        </p>
                        <h5> Privacidad</h5>

                        <p>Recopilamos, utilizamos y protegemos la información personal del usuario de acuerdo con nuestra política de privacidad. Al utilizar la Aplicación, el usuario acepta nuestra política de privacidad y el procesamiento de sus datos personales de acuerdo con la misma.
                        <p></p>
                        </p>
                        <h5> Terminación</h5>

                        <p> Nos reservamos el derecho de terminar o suspender el acceso del usuario a la Aplicación en caso de incumplimiento de estos términos y condiciones o por cualquier otro motivo justificado a nuestra discreción.</p>

                        <h5>Cobro de la suscripción</h5>
                        <p>Al iniciar la suscripción a nuestra aplicación web de proceso de ventas en la nube, se realizará un cobro adicional de 1 sol peruano. Este cobro se realizará al comienzo del período de suscripción.</p>

                        <h5>Periodo de prueba gratuito</h5>
                        <p>No ofrecemos un período de prueba gratuito. El uso de la aplicación conlleva un cargo desde el inicio de la suscripción. No se efectuará ningún reembolso despues de haber realizado el pago inicial.</p>

                        <h5>Cobros recurrentes</h5>
                        <p>Al proporcionar los datos de pago y completar el proceso de suscripción, el usuario autoriza a nuestra empresa a realizar los cobros recurrentes mensuales utilizando los datos de pago proporcionados. El cobro se realizará de forma automática al inicio de cada período de suscripción.</p>

                        <h5>Cancelación de la suscripción</h5>
                        <p>El usuario puede cancelar la suscripción en cualquier momento antes de la renovación automática del período de suscripción. Para cancelar la suscripción, el usuario deberá seguir los procedimientos especificados en la aplicación. La cancelación de la suscripción impedirá futuros cobros y el acceso a las funcionalidades exclusivas de la aplicación.</p>

                        <h5>Reembolsos</h5>
                        <p>No se realizarán reembolsos por pagos realizados previamente, incluyendo el pago inicial al iniciar la suscripción. </p>
                        <h5>Planes de Suscripción</h5>

                        <p>Actualmente ofrecemos dos planes de suscripción: el Plan Básico y el Plan Facturador. Ambos planes permiten registrar un solo usuario.</p>
                        <h5>Acuerdo Legal</h5>
                        <p>Al utilizar nuestra aplicación web y aceptar estos Términos y Condiciones, usted acepta y reconoce que ha leído y comprendido los términos establecidos anteriormente. Estos términos constituyen un acuerdo legal entre usted y nuestra empresa.</p>

                    </div>

                </div>


            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="./js/paypal.js"></script>
    <script src="./js/urls.js"></script>
</body>

</html>