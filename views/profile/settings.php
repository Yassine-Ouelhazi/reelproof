<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="form-page container">
    <div class="form-card">
        <h1>Account Settings</h1>

        <?php if (hasFlash('success')): ?>
            <div class="alert" style="background:rgba(82,199,122,0.1);border:1px solid rgba(82,199,122,0.3);color:var(--green);margin-bottom:20px;padding:12px 16px;border-radius:var(--radius)">
                <?= e(flash('success')) ?>
            </div>
        <?php endif; ?>

        <?php $errors = Session::getFlash('errors') ?? []; ?>

        <form method="POST" action="<?= url('settings') ?>" enctype="multipart/form-data">
            <?= csrf() ?>

            <!-- Avatar -->
            <div class="form-group" style="display:flex;align-items:center;gap:20px;margin-bottom:28px">
                <img src="<?= asset($user['avatar'] ?? 'uploads/avatars/default.png') ?>"
                     id="avatarPreview"
                     style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border)">
                <div>
                    <label style="display:block;margin-bottom:6px">Profile Photo</label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" style="font-size:0.85rem;color:var(--text-dim)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= e($user['username']) ?>" disabled
                           style="opacity:0.5;cursor:not-allowed">
                    <small style="color:var(--text-muted);font-size:0.75rem">Username cannot be changed.</small>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= e($user['email']) ?>" disabled
                           style="opacity:0.5;cursor:not-allowed">
                </div>
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name"
                       value="<?= e($user['full_name'] ?? '') ?>"
                       placeholder="Your display name">
            </div>

            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="3"
                          placeholder="Tell people a bit about yourself…"><?= e($user['bio'] ?? '') ?></textarea>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:28px 0">

            <h3 style="font-family:var(--font-display);font-size:1rem;margin-bottom:16px">Change Password</h3>

            <div class="form-group">
                <label>New Password <small style="color:var(--text-muted)">(leave blank to keep current)</small></label>
                <div class="input-with-toggle">
                    <input type="password" id="new_password" name="new_password" placeholder="Min 8 characters">
                    <button type="button" class="toggle-password" data-target="new_password">👁</button>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= url('dashboard') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// Avatar preview
document.getElementById('avatarInput')?.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
