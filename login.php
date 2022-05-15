<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Board</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <main class="board">
      <div>
        <a class="board__btn" href="index.php">Back to board</a>
        <a class="board__btn" href="register.php">Register</a>
      </div>
      <h1 class="board__title">Login</h1>
      <?php
        if (!empty($_GET['errCode'])) {
          $code = $_GET['errCode'];
          $msg = 'Error';
          if ($code === '1') {
            $msg = 'Incomplete information';
          } else if ($code === '2') {
            $msg = 'Username or password entered incorrectly';
          }
          echo '<h2 class="error">Error：' . $msg . '</h2>';
        }
      ?>
      <form class="board__new-comment-form" method="POST" action="handle_login.php">
         <div class="board__nickname">
          <span>User Name:</span>
          <input type="text" name="username" />
        </div>
        <div class="board__nickname">
          <span>Password:</span>
          <input type="password" name="password" />
        </div>
        <input class="board__submit-btn" type="submit" value="Submit"/>
      </form>
  </main>
</body>
</html>