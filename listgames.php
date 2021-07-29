<?php

include 'conn.php';

$sql = "SELECT * FROM GameSessions";

$result = $conn->query($sql);

if($result) {
  echo $result->num_rows;
  echo "\n";
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo json_encode($row);
      echo "\n";
    }
  }
}
$conn->close();

?>