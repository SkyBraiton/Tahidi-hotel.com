 
   <?php
$user_name = "root";
$password = "";
$database = "tahidihotel";
$host_name = "localhost"; 
$con = new mysqli ($host_name, $user_name, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
echo "Connected successfully<br>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $n1 = $con->real_escape_string($_POST['fname']);
    $n2 = $con->real_escape_string($_POST['lname']);
    $n3 = $con->real_escape_string($_POST['phone']);
    $n4 = $con->real_escape_string($_POST['email']);
    $n5 = $con->real_escape_string($_POST['check_in_date']);
    $n6 = $con->real_escape_string($_POST['check_in_month']);
    $n7 = $con->real_escape_string($_POST['check_out_date']);
    $n8 = $con->real_escape_string($_POST['check_out_month']);
    $n9 = $con->real_escape_string($_POST['adults']);
    $n10 = $con->real_escape_string($_POST['children']);
    $n11 = $con->real_escape_string($_POST['suite']);

    //performing insert query execution
    //here our table is bookinglist
    $sql = "INSERT INTO bookinglist(fname, lname, phone, email, check_in_date, check_in_month, check_out_date, check_out_month, adults, children, suite)
             VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssssssssss", $n1, $n2, $n3, $n4, $n5, $n6, $n7, $n8, $n9, $n10, $n11);
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $con->close();
};
if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = array_fill(0, 100, ['isAvailable' => true, 'roomNumber' => 0]);
    foreach ($_SESSION['rooms'] as $index => &$room) {
        $room['roomNumber'] = $index + 1;
    }
}
function bookRoom($customerName) {
    foreach ($_SESSION['rooms'] as &$room) {
        if ($room['isAvailable']) {
            $room['isAvailable'] = false;
            $reservation = [
                "id" => uniqid(),
                "customerName" => $customerName,
                "roomNumber" => $room['roomNumber'],
                "ticket" => 'TICKET-' . uniqid()
            ];
            $_SESSION['reservations'][] = $reservation;
            return [
                "message" => "Room booked successfully!",
                "roomNumber" => $reservation["roomNumber"],
                "ticket" => $reservation["ticket"]
            ];
        }
    }
    return ["message" => "No rooms available"];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $customerName = $data['name'] ?? '';
    if (!empty($customerName)) {
        $response = bookRoom($customerName);
    } else {
        $response = ["message" => "Customer name is required"];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}
 
   ?>
   
 
 
 