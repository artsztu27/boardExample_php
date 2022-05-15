<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Board</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
  session_start();
  require_once("conn.php");
  require_once("utils.php");

  $page = 1;
  if (!empty($_GET['page'])) {
    $page = intval($_GET['page']);
  }
  $items_per_page = 10;
  $offset = ($page - 1) * $items_per_page;
   /*
    1. 從 cookie 裡面讀取 PHPSESSID(token)
    2. 從檔案裡面讀取 session id 的內容
    3. 放到 $_SESSION
  */
  $username = NULL;
  $user = NULL;
  if(!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user = getUserFromUsername($username);
  }
  $stmt = $conn->prepare(
    'select '.
      'C.id as id, C.content as content, '.
      'C.create_at as create_at, U.nickname as nickname, U.username as username '.
    'from comments as C ' .
    'left join users as U on C.username = U.username '.
    'where C.is_deleted IS NULL '.
    'order by C.id desc '.
    'limit ? offset ? '
  );
  $stmt->bind_param('ii', $items_per_page, $offset);
  $result = $stmt->execute();
  if (!$result) {
    die('Error:' . $conn->error);
  }
  $result = $stmt->get_result();
?>
  <main class="board">
      <div>
        <?php if (!$username) { ?>
          <a class="board__btn" href="register.php">Register</a>
          <a class="board__btn" href="login.php">Login</a>
        <?php } else { ?>
          <a class="board__btn" href="logout.php">Logout</a>
          <span class="board__btn update-nickname">EditNickname</span>
          <form class="hide board__nickname-form board__new-comment-form" method="POST" action="update_user.php">
            <div class="board__nickname">
              <span>New NickName：</span>
              <input type="text" name="nickname" />
            </div>
            <input class="board__submit-btn" type="submit" />
          </form>
          <h3>Hello！<?php echo $user['nickname']; ?></h3>
        <?php } ?>
      </div>
      <h1 class="board__title">Comments</h1>
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
      <form class="board__new-comment-form" method="POST" action="handle_add_comment.php">
          <textarea name="content" rows="5"></textarea>
          <?php if ($username) { ?>
            <input class="board__submit-btn" type="submit" value="Submit"/>
          <?php } else { ?>
            <h3>Please log in to post a message</h3>
          <?php } ?>
      </form>
      <div class="board__hr"></div>
      <section>
        <?php
          while($row = $result->fetch_assoc()) {
        ?>
          <div class="card">
            <div class="card__avatar"></div>
            <div class="card__body">
                <div class="card__info">
                  <span class="card__author">
                    <?php echo escape($row['nickname']); ?>
                    (@<?php echo escape($row['username']); ?>)
                  </span>
                  <span class="card__time">
                    <?php echo $row['create_at']; ?>
                  </span>
                  <?php if ($row['username'] === $username) { ?>
                    <a href="update_comment.php?id=<?php echo $row['id'] ?>">Edit</a>
                    <a href="delete_comment.php?id=<?php echo $row['id'] ?>">delete</a>
                  <? } ?>
                </div>
                <p class="card__content">
                    <?php echo escape($row['content']); ?>
                </p>
            </div>
          </div>
        </div>
        <?php } ?>
      </section>
      <div class="board__hr"></div>
      <?php
        $stmt = $conn->prepare(
          'select count(id) as count from comments where is_deleted IS NULL'
        );
        $result = $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'];
        $total_page = ceil($count / $items_per_page);
      ?>
      <div class="page-info">
        <span>Total Count <?php echo $count ?> comments，Page：</span>
        <span><?php echo $page ?> / <?php echo $total_page ?></span>
      </div>
      <div class="paginator">
        <?php if ($page != 1) { ?>
          <a href="index.php?page=1">first page </a>
          <a href="index.php?page=<?php echo $page - 1 ?>">previous page</a>
        <?php } ?>
        <?php if ($page != $total_page) { ?>
          <a href="index.php?page=<?php echo $page + 1 ?>">next page</a>
          <a href="index.php?page=<?php echo $total_page ?>">last page</a>
        <?php } ?>

      </div>
  </main>

  <script>
    var btn = document.querySelector('.update-nickname')
    btn.addEventListener('click', function() {
      var form = document.querySelector('.board__nickname-form')
      form.classList.toggle('hide')
    })
  </script>
</body>
</html>