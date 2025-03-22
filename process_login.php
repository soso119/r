<?php
    $servername = "localhost";
    $username = "root";
    $password = ""; // تأكد من تعيين كلمة مرور آمنة!
    $dbname = "barbershop";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    $full_name = isset($_POST["full_name"]) ? $_POST["full_name"] : "";
    $phone_number = isset($_POST["phone_number"]) ? $_POST["phone_number"] : "";

    $full_name = mysqli_real_escape_string($conn, $full_name);
    $phone_number = mysqli_real_escape_string($conn, $phone_number);

    $sql = "INSERT INTO users (full_name, phone_number) VALUES ('$full_name', '$phone_number')";

    if ($conn->query($sql) === TRUE) {
        echo "تم تسجيل الدخول بنجاح.";
    } else {
        echo "خطأ: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


    ?>
    