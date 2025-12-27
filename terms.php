<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
?>
<?php require_once 'includes/header.php'; ?>

<div class="row" style="display: flex; justify-content: center; padding: 4rem 0;">
    <div class="glass-panel" style="width: 100%; max-width: 800px; padding: 3rem;">
        <h1 style="text-align: center; margin-bottom: 2rem; color: var(--accent-color);">Terms of Service</h1>

        <div
            style="background: rgba(255, 255, 255, 0.05); border-left: 4px solid var(--accent-color); padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="margin-bottom: 0.5rem;"><i class="fas fa-university"></i> Educational Project Disclaimer</h3>
            <p style="font-size: 1.1rem; line-height: 1.6;">
                <strong>Please Note:</strong> This website is a student project created for educational purposes only.
                It was developed to fulfill the requirements of the <strong>3rd Year, 1st Semester Web Design
                    Course</strong>.
            </p>
        </div>

        <div style="line-height: 1.8; opacity: 0.9;">
            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">1. No Real Services</h3>
            <p>GNK housing is a mock property management platform. No real properties are listed, and no real rental
                agreements or financial transactions occur on this site.</p>

            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">2. User Data</h3>
            <p>Any information entered into this website (names, emails, passwords) is stored in a local database for
                demonstration purposes only. Please do not use your real passwords or sensitive personal financial
                information.</p>

            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">3. Availability</h3>
            <p>As a course project, this website may not be maintained or available permanently. It serves as a
                portfolio piece and a demonstration of web development skills.</p>

            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">4. Contact</h3>
            <p>For any inquiries regarding the development of this project, please refer to the <a href="about.php"
                    style="color: var(--accent-color);">About Us</a> page to verify the development team.</p>
        </div>

        <div
            style="margin-top: 3rem; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem;">
            <p>&copy; <?php echo date('Y'); ?> GNK housing Student Project. All Rights Reserved.</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>