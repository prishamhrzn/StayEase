<?php // includes/footer.php — Shared footer ?>
<!-- ── Footer ──────────────────────────────────────────── -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-logo">
                <i class="fa-solid fa-key"></i> Stay<span>Ease</span>
            </div>
            <p>Discover and book extraordinary stays across Nepal. Your perfect getaway is just a click away.</p>
            <div class="footer-socials">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>

        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
                <li><a href="<?= SITE_URL ?>/hotels.php">All Hotels</a></li>
                <li><a href="<?= SITE_URL ?>/login.php">Login</a></li>
                <li><a href="<?= SITE_URL ?>/register.php">Register</a></li>
            </ul>
        </div>

        <div class="footer-links">
            <h4>Popular Destinations</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>/hotels.php?location=Kathmandu">Kathmandu</a></li>
                <li><a href="<?= SITE_URL ?>/hotels.php?location=Pokhara">Pokhara</a></li>
                <li><a href="<?= SITE_URL ?>/hotels.php?location=Chitwan">Chitwan</a></li>
                <li><a href="<?= SITE_URL ?>/hotels.php?location=Nagarkot">Nagarkot</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h4>Contact</h4>
            <p><i class="fas fa-map-marker-alt"></i> Thamel, Kathmandu, Nepal</p>
<<<<<<< HEAD
            <p><i class="fas fa-envelope"></i> @stayease123.com</p>
            <p><i class="fas fa-phone"></i> +977 1-5970300</p>
=======
            <p><i class="fas fa-envelope"></i> stayease123@gmail.com</p>
            <p><i class="fas fa-phone"></i> +977 01-5528758</p>
>>>>>>> d9b28141eed0cecf24288bbce1174a73361a72e4
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Built with <i class="fas fa-heart" style="color:#e74c3c"></i> for Nepal.</p>
    </div>
</footer>

<!-- Main JS -->
<script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
