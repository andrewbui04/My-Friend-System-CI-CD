<?php
session_start();
if (!isset($_SESSION['friend_id'])) {
    header("Location: login.php");
    exit();
}

require_once("settings.php");

// Connect to database
$conn = @mysqli_connect($host, $user, $pswd, $dbmn) or die('Failed to connect to server');

$friend_id = $_SESSION['friend_id'];

// Handle unfriending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['friend_id2'])) {
    $friend_id2 = $_POST['friend_id2'];
    $queryUnfriend1 = "DELETE FROM myfriends WHERE friend_id1 = '$friend_id' AND friend_id2 = '$friend_id2'";
    $queryUnfriend2 = "DELETE FROM myfriends WHERE friend_id1 = '$friend_id2' AND friend_id2 = '$friend_id'";
    $resultUnfriend1 = mysqli_query($conn, $queryUnfriend1);
    $resultUnfriend2 = mysqli_query($conn, $queryUnfriend2);

    if ($resultUnfriend1 && $resultUnfriend2) {
        $updateFriendQuery = "UPDATE friends SET num_of_friends = (
            SELECT COUNT(*) FROM myfriends 
            WHERE myfriends.friend_id1 = friends.friend_id OR myfriends.friend_id2 = friends.friend_id)";
        mysqli_query($conn, $updateFriendQuery);
        $message = "Successfully unfriended.";
    } else {
        $message = "Failed to unfriend.";
    }
}

// Fetch profile name
$queryName = "SELECT profile_name FROM friends WHERE friend_id = '$friend_id'"; 
$result = mysqli_query($conn, $queryName);
$row = mysqli_fetch_assoc($result);
$_SESSION['profile_name'] = $row['profile_name'] ?? "Unknown";

// Fetch total friends
$queryTotalFriends = "SELECT num_of_friends FROM friends WHERE friend_id = '$friend_id'";
$resultTotalFriends = mysqli_query($conn, $queryTotalFriends);
$total_friends = mysqli_fetch_assoc($resultTotalFriends)['num_of_friends'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Web application development">
    <meta name="keywords" content="PHP">
    <meta name="author" content="Huy Vu Tran">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Friend List Page</title>
</head>
<body>
    <div class="container-fluid p-5 text-black">
        <h2>Friend List Page</h2>
        <p>Welcome <?= $_SESSION['profile_name'] ?> : <?= $friend_id ?>!</p>
        <p>Total number of friends: <?= $total_friends ?></p>

        <?php if (isset($message)) echo "<p>$message</p>"; ?>

        <table class="table table-striped table-bordered">
            <tr><th>Friend</th><th>Action</th></tr>
            <?php
            // Fetch friend list
            $query = "(SELECT friend_id2 FROM myfriends WHERE friend_id1 = '$friend_id') 
                      UNION 
                      (SELECT friend_id1 FROM myfriends WHERE friend_id2 = '$friend_id')";
            $queryResult = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_row($queryResult)) {
                $queryFriend = "SELECT profile_name FROM friends WHERE friend_id = '{$row[0]}'"; 
                $resultFriend = mysqli_query($conn, $queryFriend);
                $rowFriend = mysqli_fetch_assoc($resultFriend);
                $profile_friend = $rowFriend['profile_name'] ?? "Unknown";

                echo "<tr><td>$profile_friend</td>
                      <td>
                        <form method='post'>
                            <input type='hidden' name='friend_id2' value='{$row[0]}'>
                            <input type='submit' class='btn btn-warning' value='Unfriend'>
                        </form>
                      </td></tr>";
            }

            mysqli_free_result($queryResult);
            mysqli_close($conn);
            ?>
        </table>

        <div>
            <a href="friendadd.php" class="btn btn-dark">Add Friends</a>
            <a href="logout.php" class="btn btn-danger">Log Out</a>
        </div>
    </div>
</body>
</html>
