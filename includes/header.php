<?php session_start(); ?>
<head>

</head>

<header>
  <nav class="flex justify-between items-center bg-gray-900 text-white px-6 py-4">
    <a href="index.php" class="text-xl font-semibold">😎</a>
    <div class="flex space-x-6">
      <?php if (!isset($_SESSION['user_id'])) { ?>
        <a href="login.php" class="hover:underline">Sign in</a>
      <?php } else { ?>
        <a href="logout.php" class="hover:underline hover:text-red-500">Log out</a>
      <?php } ?>

    </div>
  </nav>
</header>
