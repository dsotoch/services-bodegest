<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/loadEnv.php';
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<script src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js" kr-public-key=<?php echo PUBLIC_KEY ?> kr-post-url-success="paid.php" ; kr-language="es-ES">
</script>
<link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/neon-reset.min.css">
<script type="text/javascript" src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/neon.js">
</script>
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $_SESSION["token"]) {
    $firstName = $_POST['modal-auth-apellidos'];
    $email = $_POST['modal-auth-email'];
    $lastName = $_POST['modal-auth-name'];
    $dni = $_POST['modal-auth-dni'];
    $phone = $_POST['modal-auth-phone'];
    #VERIFICAR QUE EL EMAIL NO ESTE REGISTRADO

    require_once __DIR__ . '/connectivity.php';
    $connectivity = new Connectivity();
    if (!$connectivity->isConnectionActive()) {
      response("er", "Error de conexión con la base de datos.Intentalo mas tarde.");
      header("Location: checkout.php");
      die();
    }
    $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
    $sql = "SELECT email FROM users where email='$email'";
    $existEmail = $connectivity->getMysql($sql);
    if (!empty($existEmail)) {
      $connectivity->conn->commit();
      $connectivity->conn->close();
      response("er", "ERROR: El Email ya Se Encuentra Registrado.");
      header("Location: checkout.php");
      die();
    }

    #VERIFICAR QUE EL TELEFONO NO SE ENCUENTRE REGISTRADO
    $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
    $sql = "SELECT telefono FROM personas where telefono='$phone'";
    $existEmail = $connectivity->getMysql($sql);
    if (!empty($existEmail)) {
      $connectivity->conn->commit();
      $connectivity->conn->close();
      response("er", "ERROR: El Telefono ya Se Encuentra Registrado.");
      header("Location: checkout.php");
      die();
    }
     #VERIFICAR QUE EL DNI NO SE ENCUENTRE REGISTRADO
     $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
     $sql = "SELECT dni FROM users where dni='$dni'";
     $existEmail = $connectivity->getMysql($sql);
     if (!empty($existEmail)) {
       $connectivity->conn->commit();
       $connectivity->conn->close();
       response("er", "ERROR: El Dni ya Se Encuentra Registrado.");
       header("Location: checkout.php");
       die();
     }

    $store = array(
      "amount" => 2200,
      "currency" => "PEN",
      "formAction" => "REGISTER_PAY",
      "orderId" => "ORDER-" . random_int(1, 500000000),
      "overridePaymentCinematic" => "IMMEDIATE_CAPTURE",
      "customer" => [
        "email" => $email,
        "billingDetails" => [
          "firstName" => $firstName,
          "identityCode" => $dni,
          "lastName" => $lastName,
          "phoneNumber" => $phone

        ],

      ],
      "transactionOptions" => [
        "cardOptions" => [
          "installmentNumber" => 1,
        ]
      ],

    );
    $credentials = base64_encode(USERNAME . ':' . PASSWORD);
    $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

    $curl = curl_init();
    $body = array(
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true, // Para recibir la respuesta en lugar de imprimirla directamente
      CURLOPT_HTTPHEADER => array(
        "Authorization: Basic " . $credentials,
        "Content-Type: application/json"
      ),
      CURLOPT_POSTFIELDS => json_encode($store)
    );

    curl_setopt_array($curl, $body);
    $response = curl_exec($curl);
    $response = json_decode($response);
    if ($response->status != 'SUCCESS') {
      /* an error occurs, I throw an exception */
      $error = $response['answer'];
      response("er", $error['errorMessage']);
      header("Location: checkout.php");
      die();
    }
    $formToken = $response->answer->formToken;
    curl_close($curl);
    unset($_SESSION['csrf_token']);
  } else {
    response("er", "Error de seguridad. El formulario no puede procesarse.Intentalo Nuevamente");
    header("Location: checkout.php");
    exit();
  }
} else {
  response("er", "Método de solicitud no permitido.Intentalo Nuevamente");
  header("Location: checkout.php");
  exit();
}

?>
<center>
  <div class="kr-embedded" kr-popin kr-form-token="<?php echo $formToken; ?>"></div>
</center>
<?php
function response($key, $message)
{
  $_SESSION["response"] = array($key => $message);
}
exit();
