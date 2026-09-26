<?php
$page_title = $page_title ?? 'Dashboard';
?>
<header class="topbar">
  <div class="topbar-left">
    <button class="menu-toggle" id="menuToggle" type="button" aria-label="Open menu">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>
    <h1><?= htmlspecialchars($page_title) ?></h1>
  </div>
  <div class="user-info">
    <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
    <div class="avatar" title="<?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
  </div>
</header>
