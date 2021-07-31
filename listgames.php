<?php

include 'conn.php';

$sql = "SELECT * FROM GameSessions";

$result = $conn->query($sql);

if($result) {
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      ?>
      <a href=<?php echo '"game.php?id=' .  $row["GameSessionID"] . '"'; ?>>
      <?php
      echo "Game " . $row["GameSessionID"] . " started on " . $row["StartDate"];
      ?>
      </a><br>
      <?php
    }
  }
}
$conn->close();

?>