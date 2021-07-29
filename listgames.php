<?php

include 'conn.php';

$sql = "SELECT * FROM GameStates";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo $row;
  }
} else {
  echo "0 results";
}
$conn->close();
?>