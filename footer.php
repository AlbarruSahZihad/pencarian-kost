<script>
    // Mobile Navbar Toggle
    const navToggle = document.createElement('button');
    navToggle.className = 'nav-toggle';
    navToggle.innerHTML = '☰';
    navToggle.setAttribute('aria-label', 'Menu');
    
    const navbarContainer = document.querySelector('.navbar .container');
    const navLinks = document.querySelector('.nav-links');
    
    if (navbarContainer && navLinks) {
        // Insert toggle button before nav-links
        navbarContainer.insertBefore(navToggle, navLinks);
        
        navToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navbarContainer.contains(e.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
            }
        });
    }
</script>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>PK Pencarian Kost</h4>
                <p>Platform terpercaya untuk menemukan kost impianmu dengan mudah dan cepat.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <p><a href="index.php">Home</a></p>
                <p><a href="daftar-kost.php">Daftar Kost</a></p>
                <p><a href="favorit.php">Favorit</a></p>
                <p><a href="profil.php">Profil</a></p>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p>📧 info@pencarikost.id</p>
                <p>📞 +62 812-3456-7890</p>
                <p>📍 Jakarta, Indonesia</p>
            </div>
            <div class="footer-col">
                <h4>Ikuti Kami</h4>
                <p>Instagram</p>
                <p>Facebook</p>
                <p>Twitter</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
            <p>&copy; 2026 Pencarian Kost. All rights reserved.</p>
        </div>
    </div>
</footer>