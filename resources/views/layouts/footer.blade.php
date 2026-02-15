<!-- resources/views/layouts/footer.blade.php -->
<footer class="main-footer">
    <div class="footer-grid">
        <div class="footer-col" style="flex: 1;">
            <h3>Contact Information</h3>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> Brgy. F. De Jesus, Unisan, Quezon</li>
                <li><i class="fas fa-phone"></i> +63 123 456 7890</li>
                <li><i class="fas fa-envelope"></i> Unisan.LGU@email.com</li>
                <li><i class="far fa-clock"></i> Mon-Fri, 8:00AM - 5:00PM</li>
            </ul>
        </div>

        <div class="footer-col" style="flex: 1;">
            <h3>Resources</h3>
            <ul>
                <li><a href="#">Features</a></li>
                <li><a href="#">How It Works</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>

        <div class="footer-col" style="flex: 1;">
            <h3>Feedback</h3>
            <p style="margin-bottom: 15px;">Share your experience while using this website.</p>
            <form class="footer-form">
                <input type="email" placeholder="Your Email Address">
                <button type="submit" class="btn-submit">Submit</button>
            </form>
        </div>
    </div>
    <div style="text-align: center; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
        &copy; {{ date('Y') }} Unisan, Quezon. All Rights Reserved.
    </div>
</footer>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<!-- Common JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>

