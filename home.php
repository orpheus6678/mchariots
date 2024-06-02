<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href=global.css>
    <title>Home | MChariots</title>
  </head>
  <body>
    <h1>Welcome to Modern Chariots Homepage</h1>
    <button>Logout</button>

    <script>
      document
        .querySelector('button')
        .addEventListener('click', () => window.location = 'assets/logout.php');
    </script>
  </body>
</html>
