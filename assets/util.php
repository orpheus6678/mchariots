<?php

function refresh_with_cookie($cookie, $value = 'true', $location = null)
{
  $location = $location ?? $_SERVER['SCRIPT_NAME'];
  setcookie($cookie, $value);
  header("Location: $location");
  exit;
}

function delete_cookie($cookie)
{
  setcookie($cookie, null, -1);
}

function show_post($postid, $show_user = false)
{
  require 'db.php';

  [
    'username' => $username,
    'text' => $text,
    'id' => $postid
  ] = mysqli_fetch_assoc(mysqli_execute_query(
    $mysqli,
    'SELECT * FROM `posts` WHERE `id` = ?', [$postid]
  ));

  $result = mysqli_execute_query(
    $mysqli,
    'SELECT `path` FROM `images`
     JOIN `posts_images` ON `images`.`id` = `posts_images`.`imgid`
     JOIN `posts` ON `posts`.`id` = `posts_images`.`postid`
     WHERE `posts`.`id` = ?',
    [$postid]
  );
  
  ?>
  <div class=post>
    <div class=post-body>
  <?php
  if ($show_user)
    echo "<a href='profile.php?username=$username'>$username</a>";
  echo "<p>$text</p>";
  foreach ($result as $row)
  {
    echo '<img src="assets/img/' . $row['path'] . '">';
  }

  [ 'lc' => $likes_count ] = mysqli_fetch_assoc(mysqli_execute_query(
    $mysqli,
    'SELECT COUNT(*) AS `lc` FROM `likes` WHERE `postid` = ?',
    [$postid]
  ));

  [ 'cc' => $comments_count ] = mysqli_fetch_assoc(mysqli_execute_query(
    $mysqli,
    'SELECT COUNT(*) AS `cc` FROM `comments` WHERE `postid` = ?',
    [$postid]
  ));

  $curr_user = $_SESSION['username'];

  [ 'liked' => $liked ] = mysqli_fetch_assoc(mysqli_execute_query(
    $mysqli,
    'SELECT EXISTS(SELECT * FROM `likes` WHERE `postid` = ? AND `username` = ?) AS `liked`',
    [$postid, $curr_user]
  ));

  $like_word = ($liked)? 'Unlike' : 'Like';
  ?>
      <div class=like-comment>
        <div><?= $likes_count ?> Likes <?= $comments_count ?> Comments</div>
        <a class=like-toggle href="assets/like_toggle.php?id=<?= $postid ?>"><?= $like_word ?></a>
        <form action=assets/make_comment.php method=post>
          <input name=text type=text placeholder="Post a comment...">
          <input type=hidden name=postid value="<?= $postid ?>">
          <button>Comment</button>
        </form>
      </div>
    </div>
    <div class=comment-body>
<?php

$result = mysqli_execute_query(
  $mysqli,
  'SELECT `username`, `text` FROM `comments`
   NATURAL JOIN `users` WHERE `postid` = ?',
  [$postid]
);

if (!mysqli_num_rows($result))
  echo '<p>Comments will appear here.</p>';

foreach ($result as $row)
{
  $comment_user = $row['username'];
  echo '<div class=comment>';
  echo "<a href='profile.php?username=" . urlencode($comment_user) . "'>$comment_user</a> ";
  echo htmlspecialchars($row['text']);
  echo '</div>';
}

?>
    </div>
  </div>
  
  <?php
}
