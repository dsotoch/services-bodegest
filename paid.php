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
    suscription($connectivity, $userId, $token, $orderId, $suscripcion_id, $sus_client);
} else {
    // El usuario no existe    
    if (insertUserAndPersonaBD($connectivity, $identityCode, $customerLastName, $customerEmail, $customerFirstName, $customerPhone)) {
        $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
        $sqll = "SELECT id FROM users WHERE email = '$customerEmail'";
        $userId = $connectivity->getMysql($sqll);
        $connectivity->conn->commit();
        suscription($connectivity, $userId, $token, $orderId, $sus_client);
    } else {
        response("er", "Datos proporcionados Invalidos.Intentalo mas tarde.");
        $connectivity->conn->close();
        header("Location: checkout.php");
        die();
    }
}


function suscription(Connectivity $connectivity, $userId, $token, $orderId, $sus_client)
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
        $fecha = new DateTime("now",new DateTimeZone("America/Lima"));

        $effectDate = $fecha->format("y-m-d");
        $amount = "22";
        $suscripcion_id = random_int(1, 500);
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

        $connectivity->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $sql3 = "INSERT INTO suscripcions(card_id, suscripcion_id, cancelado_al_finalizar_periodo, fecha_cargo, fecha_creacion, numero_periodo_actual, fecha_fin_periodo, estado, fecha_fin_prueba, cantidad_cargo_predeterminada, id_plan, id_cliente, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sql4 = "INSERT INTO pagos(monto, descripcion, suscripcion_id,periodo,estado,fechaCargo) VALUES (?, ?, ?,?,?,?)";
        $stmt4 = $connectivity->conn->prepare($sql3);
        $stmt5 = $connectivity->conn->prepare($sql4);

        if ($stmt4 && $stmt5) {
            $cncl = true;
            $np = 1;
            $p_state = "ACTIVO";
            $id_plan = 1;
            $DateCreated= new DateTime("now",new DateTimeZone("America/Lima"));

            $stmt4->bind_param("ssssssssssisi", $token, $suscripcion_id, $cncl, $effectDate, $DateCreated, $np, $effectDate, $p_state, $effectDate, $amount, $id_plan, $sus_client, $userId["id"]);

            if ($stmt4->execute()) {
                // Obtener el último ID insertado después de la ejecución de la primera consulta
                $lastInsertedID = $stmt4->insert_id;

                $stmt4->close();
                $descripcion = "plan basico";
                $periods = ChronogramGenerate();
                $p = 1;
                try {
                    foreach ($periods as $fe) {
                        $status = ($p == 1) ? true : false;
                        $fec= $fe;
                        $stmt5->bind_param("ssssis", $amount, $descripcion, $lastInsertedID, $p, $status, $fec);

                        if (!$stmt5->execute()) {
                            throw new Exception("Error al registrar pago: " . $stmt5->error);
                        }
                        $p++;
                    }

                    $connectivity->conn->commit(); // Confirmar la transacción
                    $stmt5->close();
                    response("sus", "Suscripción creada Correctamente. En breve recibirás un Correo con todos los Datos.");
                } catch (Exception $e) {
                    $stmt5->close();
                    $connectivity->conn->rollback(); // Revertir la transacción en caso de error
                    response("er", $e->getMessage());
                }
            } else {
                $stmt4->close();
                $connectivity->conn->rollback(); // Revertir la transacción en caso de error
                response("er", "Error al registrar suscripcion: " . $stmt4->error);
            }
        } else {
            response("er", "Error al preparar la sentencia SQL: " . $connectivity->conn->error);
        }
    }
}
function ChronogramGenerate()
{
    $StartDate = new DateTime("now", new DateTimeZone("America/Lima"));
    $interval = new DateInterval("P1M");
    $array = [];
    $day=$StartDate->format('d');
    if($day>28){
        for ($i = 0; $i < 12; $i++) {
        
            $StartDate->add($interval);
            $LastDayOfMonth = clone $StartDate;
            $LastDayOfMonth->modify('last day of this month');
            $array[] = $LastDayOfMonth->format('Y-m-d');
        }
    }else{
        for ($i = 0; $i < 12; $i++) {
            $array[] = $StartDate->format('Y-m-d');
            $StartDate->add($interval);
        }
    }
    

    return $array;
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
