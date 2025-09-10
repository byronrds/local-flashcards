<?php session_start(); ?>
<head>

</head>

<header>
  <nav class="flex justify-between items-center bg-gray-900 border-b border-yellow-400 px-6 py-4" style="background-color: #323437; border-bottom: 1px solid #FFD700;">
    <a href="index.php" class="text-xl font-semibold" style="color: #FFD700;">FlashMaster</a>
    <div class="flex space-x-6">
      <?php if (!isset($_SESSION['user_id'])) { ?>
        <a href="login.php" class="hover:underline" style="color: #e2e2e2;">Sign in</a>
      <?php } else { ?>
        <a href="logout.php" class="hover:underline" style="color: #e2e2e2;">Log out</a>
      <?php } ?>
      <a href="profile.php" class="hover:underline" style="color: #e2e2e2;">Profile</a>
    </div>
  </nav>
</header>
