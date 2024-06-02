<?php

session_start();

if (is_null($_SESSION['username'])) {
  header('Location: login.php');
  die;
}

require 'assets/db.php';
require 'assets/util.php';

$username = $_SESSION['username'];
$row = mysqli_fetch_assoc(mysqli_execute_query(
  $mysqli,
  'SELECT `fullname`, `gender`, `joindate`, `path` AS `pfppath`
   FROM `users` LEFT JOIN `images` ON `users`.`pfp` = `images`.`id`
   WHERE `username` = ?',
  [$username]
));

$fullname = $row['fullname'];
$gender = $row['gender'];
$joindate = $row['joindate'];
$pfppath = $row['pfppath'];

if (is_null($pfppath)) {
  if ($gender === 'male')
    $pfppath = 'male.jpg';
  else if ($gender === 'female')
    $pfppath = 'female.jpg';
  else
    $pfppath = 'andro.jpg';

  $pfppath = "assets/$pfppath";
} else {
  $pfppath = "assets/img/$pfppath";
}

?>

<!DOCTYPE html>
<html>
  <head>
    <link rel=stylesheet href=normalize.css>
    <link rel=stylesheet href=global.css>
    <style>
      .logout {
        display: block;
        margin-block: 1em;
      }

      .profile-banner { display: flex; }
      .immovable-part {
        display: flex;
        flex-direction: column;
        margin-right: 1em;
      }

      .profile-banner > img {
        width: 200px;
        height: 200px;
        object-fit: cover;
        margin-bottom: 1em;
      }

      .profile-banner > ul { list-style: none; }
      .username { font-size: 1.5em; }
      #change-pfp { margin-left: 1em; }
      .main { display: flex; }
      .make-post > * { display: block; }
      .make-post > button { padding: .5em; }

      .image-post {
        display: inline-block;
        margin-block: .7em;
      }

      .posts {
        height: 90vh;
        overflow: auto;
      }

      .post {
        width: 70%;
        margin: 0 auto;
        margin-block: 1em 0;
        border: solid 1px black;
        padding: 1em;
      }

      .post > img {
        max-width: 50%;
      }

      .post > p {
        margin-block: 0 1em;
      }
    </style>
  </head>
  <body>
    <?php require 'assets/navbar.html'; ?>
    <div class=main>
      <div class=immovable-part>
      <div class=profile-banner>
        <img src="<?= $pfppath ?>">
        <ul>
          <li class=username><?= $username ?></li>
          <?php if (isset($fullname)): ?>
            <li><?= $fullname ?></li>
          <?php endif; ?>
          <li>Joined in <?= idate('Y') ?></li>
        </ul>
      </div>
      <form class=pfp-submit action=assets/change_pfp.php method=post enctype=multipart/form-data>
        <label for=change-pfp>Change Profile Picture</label>
        <input type=file id=change-pfp name=pfp accept="image/*">
      </form>
      <form class=make-post action="assets/make_post.php" method=post enctype=multipart/form-data>
        <label for=post-text><h3>What's on your mind?</h3></label>
        <textarea name=text id=post-text rows=7 cols=40
          placeholder="Write something to post here..."></textarea>
        <div class=image-post>
          <label for=post-images>Optionally post images?</label>
          <input id=post-images type=file name=images[] accept="image/*" multiple>
        </div>
        <button>Post</button>
      </form>
      </div>
      <div class=posts>
        <?php

        $result = mysqli_execute_query(
          $mysqli,
          'SELECT `id` FROM `posts` WHERE `username` = ?',
          [$username]  
        );

        foreach ($result as $row)
          show_post($row['id']);

        ?>
      </div>
    </div>
    
    <script>
      document
        .querySelector('#change-pfp')
        .addEventListener('change', e => e.target.form.requestSubmit() )
    </script>
  </body>
</html>
