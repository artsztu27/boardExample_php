<?php
  require_once("conn.php");

  $page = 1;
  if (!empty($_GET['page'])) {
    $page = intval($_GET['page']);
  }
  $items_per_page = 10;
  $offset = ($page - 1) * $items_per_page;

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
  $comments = array();

  while($row = $result->fetch_assoc()) {
    array_push($comments, array(
      "id"=> $row['id'],
      "username" => $row['username'],
      "nickname" => $row['nickname'],
      "content" => $row['content'],
      "create_at" => $row['create_at']
    ));
  }

  $json = array(
    "comments" => $comments
  );

  $response = json_encode($json);
  header('Content-type:application/json;charset=utf-8');
  echo $response;
?>