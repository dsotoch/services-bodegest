<?php
require_once __DIR__ . '/config.php';
session_start();
if (!checkHash($_POST, SHA_KEY)) {
    response("er", 'Invalid signature.');
    header("Location: checkout.php");
    die();
}
$answer = $_POST['kr-answer'];

function checkHash($data, $key)
{
    $supported_sign_algos = array('sha256_hmac');
    if (!in_array($data['kr-hash-algorithm'], $supported_sign_algos)) {
        return false;
    }
    $kr_answer = str_replace('\/', '/', $data['kr-answer']);
    $hash = hash_hmac('sha256', $kr_answer, $key);
    return ($hash == $data['kr-hash']);
}
$responseData = json_decode($answer, true);

// Acceder a algunos datos
$orderId = $responseData['orderDetails']['orderId'];
$orderTotalAmount = $responseData['orderDetails']['orderTotalAmount'];
$customerEmail = $responseData['customer']['email'];
$customerPhone = $responseData["customer"]["billingDetails"]["phoneNumber"];
$customerLastName = $responseData["customer"]["billingDetails"]["lastName"];
$customerFirstName = $responseData["customer"]["billingDetails"]["firstName"];
$transactionStatus = $responseData['transactions'][0]['status'];
$token = $responseData['transactions'][0]['paymentMethodToken'];
$detailedStatus = $responseData['transactions'][0]['detailedStatus'];
$identityCode = $responseData["customer"]["billingDetails"]["identityCode"];
$sus_client = $identityCode;


#VERIFICAR SI YA CUENTA CON UNA SUSCRIPCION
require_once __DIR__ . '/connectivity.php';
$connectivity = new Connectivity();
if (!$connectivity->isConnectionActive()) {
    response("er", "Error de conexión con la base de datos.Intentalo mas tarde.");
    header("Location: checkout.php");
    die();
}

$sql = "SELECT id FROM users WHERE email = '$customerEmail'";
$userId = $connectivity->getMysql($sql);
if (!empty($userId)) {
    suscription($connectivity, $userId, $token, $orderId,$suscripcion_id, $sus_client);
} else {
    // El usuario no existe    
    if (insertUserAndPersonaBD($connectivity, $identityCode, $customerLastName, $customerEmail, $customerFirstName, $customerPhone)) {
        suscription($connectivity, $userId, $token, $orderId,$suscripcion_id, $sus_client);
    } else {
        response("er", "Datos proporcionados Invalidos.Intentalo mas tarde.");
        $connectivity->conn->close();
        header("Location: checkout.php");
        die();
    }
}


function suscription(Connectivity $connectivity, $userId, $token,$orderId,$suscripcion_id, $sus_client)
{

    $sql2 = "SELECT * FROM suscripcions WHERE user_id = ? AND estado = 'ACTIVO'";
    $stmt2 = $connectivity->conn->prepare($sql2);
    $stmt2->bind_param("i", $userId["id"]); // "i" indica que es un entero
    // Ejecutar la consulta de suscripciones
    $stmt2->execute();
    // Vincular el resultado
    $stmt2->store_result();
    // Verificar si el usuario tiene suscripciones activas
    if ($stmt2->num_rows > 0) {
        // El usuario ya tiene suscripciones activas
        $stmt2->close();
        response("er", "Ya Cuentas con una Suscripción Activa.");
        $connectivity->conn->close();
        header("Location: checkout.php");
        die();
    } else {
        // El usuario no tiene suscripciones activas
        $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreateSubscription";
        $credentials = base64_encode(USERNAME . ':' . PASSWORD);

        $curl = curl_init();
        $fecha = date("Y-m-d\TH:i:sP");
        $dia = date("d", strtotime($fecha));
        $rule = "";
        switch ($dia) {
            case 28:
                $dia_inicio = "28";
                $rule = "RRULE:FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=$dia_inicio;";
                break;
            case 29:
                $dia_inicio = "29";
                $rule = "RRULE:FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=$dia_inicio;";
                break;
            case 30:
                $dia_inicio = "30";
                $rule = "RRULE:FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=$dia_inicio;";
                break;
            case 31:
                $dia_inicio = "31";
                $rule = "RRULE:FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=$dia_inicio;";
                break;

            default:
                $dia_inicio = $dia;

                $rule = "RRULE:FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=$dia_inicio;";
                break;
        }
        $data = array(
            "amount" => intval(AMOUNT),
            "description" => "Plan Bodegest" . "-" . ENTERPRISE,
            "currency" => "PEN",
            "effectDate" => date("c"),
            "paymentMethodToken" => $token,
            "orderId" => $orderId,
            "rrule" => $rule,
            "transactionOptions" => [
                "cardOptions" => [
                    "installmentNumber" => 1,
                ]
            ],
            "initialAmount" => 100,
            "initialAmountNumber" => 1,
        );

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic " . $credentials,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);

        $response = json_decode($response, true);
        curl_close($curl);
        $effectDate = date($response["answer"]["effectDate"]);
        $amount = $response["answer"]["amount"];
        $suscripcion_id = $response["answer"]["subscriptionId"];


        #ACTUALIZAR PLAN DE LA PERSONA DB
        $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $sql3 = "UPDATE personas SET plan=? WHERE user_id=? ";
        $stmt3 = $connectivity->conn->prepare($sql3);

        if ($stmt3) {
            $state = "PLAN BASICO"; // Estado deseado
            $stmt3->bind_param("si", $state, $userId["id"]); // "si" indica que los valores son de tipo string e integer

            // Ejecutar la sentencia SQL
            if ($stmt3->execute()) {
                $connectivity->conn->commit(); // Confirmar la transacción
                $stmt3->close(); // Cerrar la sentencia
                //echo "PLAN BASICO actualizado correctamente";
            } else {
                $connectivity->conn->rollback(); // Revertir la transacción en caso de error
                //echo "Error al actualizar el estado: " . $stmt3->error;
            }
        } else {
            //echo "Error al preparar la sentencia SQL: " . $connectivity->conn->error;
        }

        #INSERTAR SUSCRIPCION  DB
        $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $sql3 = "INSERT INTO suscripcions(card_id,suscripcion_id,cancelado_al_finalizar_periodo,fecha_cargo,fecha_creacion,numero_periodo_actual,fecha_fin_periodo,estado,fecha_fin_prueba,cantidad_cargo_predeterminada,id_plan,id_cliente,user_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?) ";
        $stmt4 = $connectivity->conn->prepare($sql3);

        if ($stmt4) {
            $cncl = true;
            $np = 1;
            $p_state = "ACTIVO";
            $id_plan = 1;
            $amount2 = number_format($amount / 100, 2);
            $stmt4->bind_param("ssssssssssisi", $token, $suscripcion_id, $cncl, $effectDate, $effectDate, $np, $effectDate, $p_state, $effectDate, $amount2, $id_plan, $sus_client, $userId["id"]);
            // Ejecutar la sentencia SQL
            if ($stmt4->execute()) {
                $connectivity->conn->commit(); // Confirmar la transacción
                $stmt4->close(); // Cerrar la sentencia
                response("sus", "Suscripción creada Correctamente.En breve recibiras un Correo con todos los Datos.");
            } else {
                $connectivity->conn->rollback(); // Revertir la transacción en caso de error
                response("er", "Error al registrar suscripcion: " . $stmt4->error);
            }
        } else {
            response("er","Error al preparar la sentencia SQL: " . $connectivity->conn->error);
        }
    }
}
function insertUserAndPersonaBD(Connectivity $con, $identityCode, $customerLastName, $customerEmail, $customerFirstName, $customerPhone)
{
    $con->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    // La consulta de inserción debe ser preparada y ejecutada usando la declaración preparada (mysqli_stmt)
    $sqlInsertUsers = "INSERT INTO users (name, email, password, dni, pass) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->conn->prepare($sqlInsertUsers);

    if ($stmt) {
        // Hashear la contraseña utilizando password_hash
        $hash_password = password_hash($identityCode, PASSWORD_DEFAULT);

        // Vincular los parámetros a la declaración preparada
        $stmt->bind_param("sssss", $customerLastName, $customerEmail, $hash_password, $identityCode, $identityCode);

        // Ejecutar la declaración preparada
        if ($stmt->execute()) {
            // Confirmar la transacción si la inserción tuvo éxito
            $con->conn->commit();
            $user = $stmt->insert_id; //recuperar usuario insertado; 
            $stmt->close();
            if (insertPerson($con, $customerFirstName, $customerPhone, $user)) {
                return true; // Indicar que la inserción fue exitosa
            } else {
                return false;
            }
        } else {
            // Revertir la transacción si hubo un error
            $con->conn->rollback();
            $stmt->close();
            return false; // Indicar que la inserción falló
        }
    } else {
        // Revertir la transacción si no se pudo preparar la declaración
        $con->conn->rollback();
        return false; // Indicar que la inserción falló
    }
}
function insertPerson(Connectivity $con, $customerFirstName, $customerPhone, $user_id)
{
    $con->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    // La consulta de inserción debe ser preparada y ejecutada usando la declaración preparada (mysqli_stmt)
    $sqlInsertUsers = "INSERT INTO personas (apellidos, telefono, estado, user_id) VALUES ( ?, ?, ?, ?)";
    $stmt = $con->conn->prepare($sqlInsertUsers);

    if ($stmt) {
        $state = "sin-verificar";
        // Vincular los parámetros a la declaración preparada
        $stmt->bind_param("sssi", $customerFirstName, $customerPhone, $state, $user_id);

        // Ejecutar la declaración preparada
        if ($stmt->execute()) {
            // Confirmar la transacción si la inserción tuvo éxito
            $con->conn->commit();

            $stmt->close();
            return true; // Indicar que la inserción fue exitosa
        } else {
            // Revertir la transacción si hubo un error
            $con->conn->rollback();
            $stmt->close();
            return false; // Indicar que la inserción falló
        }
    } else {
        // Revertir la transacción si no se pudo preparar la declaración
        $con->conn->rollback();
        return false; // Indicar que la inserción falló
    }
}
function response($key, $message)
{
    $_SESSION["response"] = array($key => $message);
}
$connectivity->conn->close();
header("Location: checkout.php");
exit();
