<?php
$servername = "localhost";
$username = "root";
$password = ""; // تأكد من تعيين كلمة مرور آمنة!
$dbname = "barbershop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$full_name = isset($_POST["full_name"]) ? trim($_POST["full_name"]) : "";
$phone_number = isset($_POST["phone_number"]) ? trim($_POST["phone_number"]) : "";

// التحقق من صحة البيانات على الخادم
if (empty($full_name)) {
    echo "الاسم الكامل مطلوب.";
    exit;
}

if (empty($phone_number)) {
    echo "رقم الهاتف مطلوب.";
    exit;
} elseif (!preg_match('/^[0-9]{10}$/', $phone_number)) {
    echo "رقم الهاتف يجب أن يحتوي على 10 أرقام.";
    exit;
}

// استخدام عبارات مُجهزة
$stmt = $conn->prepare("INSERT INTO users (full_name, phone_number) VALUES (?, ?)");
$stmt->bind_param("ss", $full_name, $phone_number);

if ($stmt->execute()) {
    echo "تم تسجيل الدخول بنجاح.";
} else {
    echo "حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.";
    error_log("خطأ في قاعدة البيانات: " . $stmt->error); // تسجيل الخطأ
}

$stmt->close();
$conn->close();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>