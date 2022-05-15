<?php
  session_start();
  require_once("conn.php");
  require_once("utils.php");

  $id = $_GET['id'];

  $username = NULL;
  $user = NULL;
  if(!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user = getUserFromUsername($username);
  }

  $stmt = $conn->prepare(
    'select * from comments where id = ?'
  );
  $stmt->bind_param("i", $id);
  $result = $stmt->execute();
  if (!$result) {
    die('Error:' . $conn->error);
  }
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Board</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <main class="board">
      <h1 class="board__title">Edit Content</h1>
      <?php
        if (!empty($_GET['errCode'])) {
          $code = $_GET['errCode'];
          $msg = 'Error';
          if ($code === '1') {
            $msg = 'Incomplete information';
          }
          echo '<h2 class="error">Error：' . $msg . '</h2>';
        }
      ?>
        <form class="board__new-comment-form" method="POST" action="handle_update_comment.php">
          <textarea name="content" rows="5"><?php echo $row['content'] ?></textarea>
          <input type="hidden" name="id" value="<?php echo $row['id'] ?>" />
          <input class="board__submit-btn" type="submit" />
        </form>
  </main>
</body>
</html>