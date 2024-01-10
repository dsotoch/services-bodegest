<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/loadEnv.php';
$adminurl = $_ENV['AdminURL'];
$contact = $_ENV['CONTACT'] . 'Hola, Estoy Interesado en BodeGest ,Solicito una Demostración.';

if (isset($_COOKIE["contact"])) {
  unset($_COOKIE["contact"]);
  setcookie("contact", $contact, time() + (60 * 60 * 24));
} else {
  setcookie("contact", $contact, time() + (60 * 60 * 24));
}
?>
<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="/images/logo_pequeño.png" type="image/x-icon">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>


  <!-- Bootstrap CSS -->

  <title>Bodegest!</title>

</head>

<body class="body">

  <div class="container" id="leading">
    <div class="row">
      <div class="col-2 col-md-3 logo">
        <a href="<?php echo $adminurl; ?>" target="_blank"> <img src="images/logo_pequeño.png" id="logo" width="100px"></a>
      </div>
      <div class="col-10 col-md-9">
        <h5 class="title"><span class="bode">Bode</span><span class="gest">Gest</span> ¡Tu bodega más rentable!</h5>
        </h5>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 card descr">
        <div class="card-body ">
          <p class="p-description"><span class="icon"><img src="images/Captura de pantalla 2023-10-14 130722.png" alt=""><br> </span> Herramienta de gestión de ventas en la nube que ayuda a las bodegas a
            aumentar sus ventas, mejorar su eficiencia y reducir sus costes.</p>
          <p class="p-description">BodeGest es un software SaaS de gestión de bodegas que te ayudará a optimizar todos
            los procesos de tu negocio, desde la gestión de clientes y proveedores hasta el control de inventarios y
            ventas.</p>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <h6>Con <span class="bode">Bode</span><span class="gest">Gest</span> puedes:</h6>
        <ul>

          <li><i class="fa-solid fa-check-double check"></i>
            Gestionar tu cartera de clientes de forma centralizada
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Tener el Control Absoluto de tu Inventario
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Seguir el rendimiento de tus ventas

          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Generar Reportes por Fechas
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Gestionar Deudas de tus Clientes
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Notificar a tus Clientes por WhatsApp y Email
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Imprimir tus comprobantes de pago</li>

          <li><i class="fa-solid fa-check-double check"></i>
            Tener la información 100% en tiempo real
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Realizar tu Balance (Semanal,Mensual,Trimestral,etc)
          </li>
          <li><i class="fa-solid fa-check-double check"></i>
            Gestionar todas tus Compras y Ventas.
          </li>
        </ul>
      </div>
    </div>
    <h4>Algunas Capturas de Pantalla</h4>
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="images/1.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/2.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/3.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/4.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/5.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/6.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/7.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/8.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="images/9.png" class="d-block w-100" alt="...">
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-12 elegi">
        <div class="container elegir">
          <h3 class="text-danger">¿Por qué elegir BodeGest?</h3>
          <h6>BodeGest es la solución perfecta para bodegas de todos los tamaños.</h6>
        </div>
        <br>
        <ul class="el">
          <li> <i class="fa-solid fa-plug-circle-check check2"></i><span class="sub">Fácil de usar :</span> BodeGest es
            un software intuitivo y fácil de usar, incluso para usuarios sin experiencia en
            gestión de bodegas.</li>
          <li><i class="fa-solid fa-plug-circle-check check2"></i>
            <span class="sub">Accesible :</span> BodeGest ofrece planes de precios flexibles para adaptarse a cualquier
            presupuesto.
          </li>
          <li><i class="fa-solid fa-plug-circle-check check2"></i>
            <span class="sub">Escalable :</span> BodeGest puede crecer con tu negocio, por lo que no tendrás que
            preocuparte de cambiar de
            software a medida que tu negocio se expanda.
          </li>
          <li><i class="fa-solid fa-plug-circle-check check2"></i>
            <span class="sub">Seguridad y estabilidad :</span> BodeGest se aloja en servidores de alta calidad que están
            protegidos contra ataques de malware, intrusiones y otros tipos de amenazas. Los servidores están diseñados
            para soportar cargas de trabajo pesadas y funcionar sin problemas durante largos períodos de tiempo.
          </li>
          <li><i class="fa-solid fa-plug-circle-check check2"></i>
            <span class="sub">Soporte y asistencia :</span> BodeGest ofrece soporte y asistencia de alta calidad a los
            usuarios. Puedes comunicarte con el equipo de soporte a través del chat en vivo, el correo electrónico o el
            teléfono.
          </li>
        </ul>
      </div>
    </div>
    <br>
    <div class="row ">
      <h5>Aquí hay algunos detalles adicionales sobre el servidor donde se encuentra alojado BodeGest:</h5>
      <div class="col-12 col-md-6 elegi2">
        <img src="images/0914_globe_with_servers_for_global_technology_solutions_stock_photo_Slide01-removebg-preview.png" alt="" id="img-datacenter">
      </div>
      <div class="col-12 col-md-6 elegi2">
        <img src="images/elastika.png" alt="" id="img-elastika">
        <h4>Datacenter Tier-3 en Perú
        </h4>
        <p>Con el Respaldo de Elastika.</p>
        <p> Nuestro centro de datos Tier III en Lima ofrece una latencia menor y una disponibilidad y redundancia de
          nivel internacional. Con una superficie de 8,094 pies cuadrados y capacidad de 0.7 MW, garantizamos un entorno
          seguro y confiable para sus servidores y aplicaciones.</p>
        <p>Además, practicamos los estándares más altos en protección de datos y privacidad. También contamos con un
          segundo datacenter alterno ubicado a más de 10 kilómetros de distancia para garantizar la continuidad del
          servicio.</p>
      </div>
    </div>
    <div class="row">
      <center>
        <h4>Nuestros Planes</h4>
      </center>
      <br>
      <div class="col-12 col-md-6">
        <img src="images/basico.png" alt="" id="plan">
        <br><br>
        <center><button class="btn btn-success" id="btn-hirePlan">
            <i class="fa-solid fa-cart-shopping "></i> Contratar Plan
          </button>
          <button class="btn btn-primary" id="btn-contact">
            <i class="fa-brands fa-whatsapp"></i> Solicitar Demostración
          </button>

        </center>
        <br>
      </div>
      <div class="col-12 col-md-6">
        <img src="images/fact.png" alt="" id="plan">
        <br>
        <center><span class="btn-danger fact"> En Desarrollo</span></center>
      </div>
    </div>

  </div>
  <br>
  <footer class="bg-dark text-light text-center py-4">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 text-lg-left">
          <!-- Logo o ícono de "pago seguro" -->
          <img src="./images/secure.png" alt="Pago Seguro" height="50">
        </div>
        <div class="col-lg-6 text-lg-right">
          <!-- Logo de iZiPay -->
          <img src="./images/izipay.png" alt="iZiPay" height="50">
        </div>
      </div>
    </div>
  </footer>
  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="js/urls.js"></script>
  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>