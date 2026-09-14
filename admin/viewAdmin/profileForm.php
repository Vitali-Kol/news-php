<?php
// Account & Password Settings Form
?>
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-person-gear text-primary me-2"></i>Account Settings</h1>
    <a href="index.php?action=start" class="btn btn-outline-secondary rounded-pill px-3">&larr; Back to Dashboard</a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> Profile updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-person-lines-fill me-2 text-primary"></i> User Details
            </div>
            <div class="card-body p-4">
                <form action="index.php?action=profileSave" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Email (Login)</label>
                        <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled>
                        <div class="form-text">Email is a unique identifier and cannot be changed.</div>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control" id="username" name="username" required value="<?= htmlspecialchars($user['username'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        <div class="form-text">Minimum 6 characters if setting a new password.</div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white py-3 fw-semibold">
                <i class="bi bi-info-circle me-2"></i> Account Information
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>ID:</strong> #<?= (int)($user['id'] ?? 0) ?></p>
                <p class="mb-2"><strong>Role (Status):</strong> <span class="badge bg-danger"><?= htmlspecialchars($user['status'] ?? 'admin') ?></span></p>
                <p class="mb-0"><strong>Registration Date:</strong> <?= htmlspecialchars($user['registration_date'] ?? 'N/A') ?></p>
            </div>
        </div>
    </div>
</div>
