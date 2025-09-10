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
      <a href="flashcard_list.php" class="hover:underline text-black">Flashcard List</a>
      <a href="create_flashcards.php" class="hover:underline text-black">Create Set</a>
    </div>
  </nav>
</header>
