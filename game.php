<?php

include_once 'auth.php';
include_once 'crud.php';

$gameInfo = fetch_game_by_id($_GET["id"]);

if(!$gameInfo) {
    header('HTTP/1.1 404 Not found');
    die('Game not found!');
}

$gameAgeHours = floor((strtotime("now") - strtotime($gameInfo["StartDate"])) / (60*60));
$actionPointsGiven = floor($gameAgeHours / $gameInfo["ActionHours"]);

$tanks = fetch_tanks_by_game($_GET["id"]);
$myTank = fetch_tank_by_game_and_player($_GET["id"], $myUserInfo["PlayerID"]);

?>

<!DOCTYPE html>
<html>
<head>
<style>

.LeftPanel {
    float: Left;
}

.RightPanel {
    float: Right;
}

.FormPanel {
    width : 240px;
    border: 1px solid black;
    padding: 4px;
}

table, th, td {
  border: 1px solid black;
}

th, td {
	width : 48px;
    height : 48px;
    font-size : 14pt;
}

td {
    font-size : 12pt;
    position: relative;
}

td input {
    position: absolute;
    top: 0px;
    right: 0px;
}

<?php

foreach($tanks as $tank) {
    $currentAP = $actionPointsGiven + $tank["ExtraAP"] - $tank["SpentAP"];
    printf("table tr:nth-child(%d) td:nth-child(%d) { \n", $tank['Y']+1, $tank['X']+1);
    if($tank['Player'] == $myUserInfo['PlayerID']) {
        echo "\tbackground: green;\n";
    } else {
        echo "\tbackground: red;\n";
    }
    echo "}\n";
    printf("table tr:nth-child(%d) td:nth-child(%d) span:after { \n", $tank['Y']+1, $tank['X']+1);
    printf("\tcontent: \"❤%d \\a⚡%d\";\n", $tank['HP'], $currentAP);
    echo "}\n";
}

$numRows = 12;
$numColumns = 20;

?>

</style>
</head>
<body>
<div class="LeftPanel">
<form action="action.php">
    <table>
    <tr>
        <th><span></span></th>
        <?php
            $letter = 'A';
            for($c = 0; $c < $numColumns; $c++) {
                ?><th><span><?php
                echo $letter;
                ?></span></th><?php
                ++$letter;
            }
        ?>
    </tr>
    <?php
    for($r = 1; $r <= $numRows; $r++) {
        ?><tr><th><span><?php
            echo $r;
            ?></span></th><?php
        for($c = 0; $c < $numColumns; $c++) {
            if($myTank) {
            ?><td><input type="radio" required id="pos" name="gridpos" value="<?php echo $c . '-' . $r; ?>"><span></span></td><?php
            } else {
                ?><td><span></span></td><?php
            }
        }
        ?></tr><?php
    }
    ?>
    </table>
    <br>
    <?php
    if(!$myTank) {
    ?>
    <form action="join.php">
        <input type="hidden" name="id" value=<?php echo '"' . $_GET["id"] .'"' ?>>
        <input type="submit" value="Join!">
    </form>
    <?php
    } else {
    ?>
    <div class="LeftPanel FormPanel">
    <input type="hidden" name="id" value=<?php echo '"' . $_GET["id"] .'"' ?>>
    <input type="radio" id="move" name="action_type" value="move" required>
     <label for="move">Move</label>
     <input type="radio" id="attack" name="action_type" value="attack" required>
     <label for="attack">Attack</label>
     <input type="radio" id="gift" name="action_type" value="gift" required>
     <label for="gift">Gift</label><br><br>
    <input type="submit" value="Go">
    </div>
    </form>
    <?php
    }
    ?>

</div>
<div class="RightPanel">
    Player List:
    <ul>
<?php
foreach($tanks as $tank) {
    $tankPlayer = fetch_user_by_id($tank["Player"]);
    echo '<li>';
    if($tankPlayer["DisplayName"]) {
        echo $tankPlayer["DisplayName"];
    } else {
        echo $tankPlayer["LoginName"];
    }
    echo '</li>';
}
?>
    </ul>
</div>
</body>
</html>

<?php
    $conn->close();
?>