<?php
$rootPath = (basename(dirname($_SERVER['PHP_SELF'])) == 'pages') ? '../' : './';
?>
    </main>
    <footer class="footer-section">
        <div class="container">
            <div class="info-cards-grid">
                <div class="info-card-group">
                    <h3>Contact Us</h3>
                    <div class="contact-item">
                        <i class="material-icons">location_on</i>
                        <p>PILIPINS</p>
                    </div>
                    <div class="contact-item">
                        <i class="material-icons">phone</i>
                        <p><a href="tel:+6391276589876">+63 9127658987</a></p>
                    </div>
                    <div class="contact-item">
                        <i class="material-icons">mail</i>
                        <p><a href="mailto:mrjahabibi@gmail.com">MR Jahabibi@gmail.com</a></p>
                    </div>
                </div>

                <div class="info-card-group footer-middle-content">
                    <h3>MR Jahabibi</h3>
                    <p>every bite feels like a celebration. 
                From the crispy Chickenjoy that can start family debates over the last piece,
                to the sweet-style spaghetti that somehow heals bad moods faster than a motivational speech, 
                Jahabibi doesn’t just serve food — it serves happiness on a tray.</p>
                    <div class="social-links">
                        <a href="#facebook" class="social-link" title="Facebook" aria-label="Facebook">
                            <i class="material-icons">facebook</i>
                        </a>
                        <a href="#tiktok" class="social-link" title="TikTok" aria-label="TikTok">
                            <i class="material-icons">music_note</i>
                        </a>
                        <a href="#instagram" class="social-link" title="Instagram" aria-label="Instagram">
                            <i class="material-icons">camera_alt</i>
                        </a>
                    </div>
                </div>

                <div class="info-card-group">
                    <h3>Opening Hours</h3>
                    <div class="hours-item">
                        <p>Monday - Friday</p>
                        <p>Everyday if you pay kiss</p>
                    </div>
                </div>
            </div>

            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> MR Jahabibi. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                navbar.classList.toggle('scrolled', window.scrollY > 50);
            }
        });
    </script>
    <script src="<?php echo $rootPath; ?>assets/js/global.js"></script>
</body>
</html>