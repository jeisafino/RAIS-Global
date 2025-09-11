<link rel="stylesheet" href="style.css">
<header class="header">
    <div class="header-brand d-flex align-items-center">
        <img src="../img/logoulit.png" alt="RAIS Logo Light" class="header-logo-img light-mode-logo">
        <img src="../img/logowhite.png" alt="RAIS Logo Dark" class="header-logo-img dark-mode-logo">
        <span class="header-title d-none d-sm-block">Roman & Associates Immigration Services</span>
    </div>
    <div class="user-status d-flex align-items-center gap-2">
        <div class="theme-switch-wrapper">
            <i class="bi bi-sun-fill"></i>
            <label class="theme-switch" for="theme-checkbox">
                <input type="checkbox" id="theme-checkbox" />
                <div class="slider round"></div>
            </label>
            <i class="bi bi-moon-fill"></i>
        </div>
        <span class="badge">ADMIN</span>

        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="bi bi-power"></i>
        </button>
    </div>
</header>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <a href="../logout.php" class="btn btn-danger rounded-pill">Logout</a>
      </div>
    </div>
  </div>
</div>