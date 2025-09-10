<?php session_start(); ?>
<head>

</head>

<header>
  <nav class="flex justify-between items-center bg-white border-b border-gray-200 px-6 py-4">
    <a href="index.php" class="text-xl font-semibold text-black">Flashcards</a>
    <div class="flex space-x-6">
      <?php if (!isset($_SESSION['user_id'])) { ?>
        <a href="login.php" class="hover:underline text-black">Sign in</a>
      <?php } else { ?>
        <a href="logout.php" class="hover:underline text-black">Log out</a>
      <?php } ?>
      <a href="profile.php" class="hover:underline text-black">Profile</a>
    </div>
  </nav>
</header>
