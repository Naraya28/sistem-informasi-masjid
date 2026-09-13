<?php
session_start();
$page_title = 'Kontak Masjid Al-Firdaus';
$old = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_old']);
include 'includes/header.php';
include 'includes/navbar.php';
?>
<section class="kontak-wrapper">
    <div class="kontak-box">
        <h1>Kontak Masjid</h1>
        <p class="subtitle">Hubungi pengurus Masjid Al-Firdaus untuk informasi kegiatan, donasi, atau pelayanan jamaah.</p>
        <a href="https://wa.me/628123456789" target="_blank" rel="noopener" class="btn-wa"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>WhatsApp Pengurus</span></a>
        <a href="mailto:MasjidAlFirdaus@gmail.com" class="btn-email"><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>MasjidAlFirdaus@gmail.com</span></a>
        <a href="https://instagram.com" target="_blank" rel="noopener" class="btn-instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i><span>Instagram Masjid Al-Firdaus</span></a>
    </div>

    <div class="kontak-box message-form-card" id="form-pesan">
        <h2>Kirim Pesan</h2>
        <?php if (!empty($_SESSION['contact_success'])): ?>
            <div class="form-alert success"><?= htmlspecialchars($_SESSION['contact_success'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['contact_success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['contact_error'])): ?>
            <div class="form-alert error"><?= htmlspecialchars($_SESSION['contact_error'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['contact_error']); ?>
        <?php endif; ?>
        <form action="pesan.php" method="POST" class="contact-form">
            <label>Nama <span>*</span></label>
            <input type="text" name="nama" maxlength="100" value="<?= htmlspecialchars($old['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            <div class="form-row">
                <div><label>Email</label><input type="email" name="email" maxlength="100" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
                <div><label>No. HP</label><input type="tel" name="no_hp" maxlength="20" pattern="[0-9+() .-]{6,20}" value="<?= htmlspecialchars($old['noHp'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            </div>
            <label>Subjek</label>
            <input type="text" name="subjek" maxlength="200" value="<?= htmlspecialchars($old['subjek'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <label>Pesan <span>*</span></label>
            <textarea name="pesan" rows="6" required><?= htmlspecialchars($old['isiPesan'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <button type="submit" class="btn-submit-message">Kirim Pesan</button>
        </form>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
