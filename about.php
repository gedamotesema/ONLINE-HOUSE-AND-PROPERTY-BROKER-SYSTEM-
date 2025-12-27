<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
?>
<?php require_once 'includes/header.php'; ?>

<style>
    .team-section {
        padding: 4rem 0;
    }

    .team-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .team-title {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, #fff, var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .team-subtitle {
        font-size: 1.2rem;
        opacity: 0.8;
        max-width: 600px;
        margin: 0 auto;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
    }

    .team-card {
        overflow: hidden;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    .team-photo-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .team-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }

    .team-photo-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.8));
        pointer-events: none;
    }

    .team-info {
        padding: 2rem;
    }

    .team-name {
        color: var(--accent-color);
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
    }

    .team-role {
        font-weight: bold;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1rem;
    }

    .team-skills {
        list-style: none;
        padding: 0;
        margin: 0;
        opacity: 0.9;
        line-height: 1.6;
    }

    .team-skills i {
        color: var(--accent-color);
        margin-right: 10px;
    }
</style>

<div class="team-section">
    <div class="team-header">
        <h1 class="team-title">Meet Our Team</h1>
        <p class="team-subtitle">The dedicated professionals behind GNK housing, working together to revolutionize your
            rental experience.</p>
    </div>

    <div class="team-grid">
        <div class="glass-panel team-card">
            <div class="team-photo-container">
                <img src="uploads/team/gedamo.jpg" alt="Photo of Gedamo Tesema" class="team-photo">
                <div class="team-photo-overlay"></div>
            </div>
            <div class="team-info">
                <h3 class="team-name">Gedamo Tesema</h3>
                <p class="team-role">Project Manager & Lead Back-End Developer</p>
                <ul class="team-skills">
                    <li><i class="fas fa-check"></i>Lead project workflow & assign tasks</li>
                    <li><i class="fas fa-code"></i>Design & develop PHP back-end logic</li>
                    <li><i class="fas fa-database"></i>Integrate MySQL & data flow</li>
                    <li><i class="fas fa-server"></i>Manage deployment & repositories</li>
                    <li><i class="fas fa-clipboard-check"></i>Review final integrations</li>
                </ul>
            </div>
        </div>

        <div class="glass-panel team-card">
            <div class="team-photo-container">
                <img src="uploads/team/naod.jpg" alt="Photo of Naod Hailu" class="team-photo">
                <div class="team-photo-overlay"></div>
            </div>
            <div class="team-info">
                <h3 class="team-name">Naod Hailu</h3>
                <p class="team-role">Lead Front-End Developer</p>
                <ul class="team-skills">
                    <li><i class="fas fa-laptop-code"></i>Lead front-end (HTML/CSS/JS)</li>
                    <li><i class="fas fa-magic"></i>Create responsive, dynamic interfaces</li>
                    <li><i class="fas fa-sync"></i>Integrate front-end with back-end APIs</li>
                    <li><i class="fas fa-tachometer-alt"></i>Optimize performance & UX</li>
                </ul>
            </div>
        </div>

        <div class="glass-panel team-card">
            <div class="team-photo-container">
                <img src="uploads/team/kidist.jpg" alt="Photo of Kidist Zewde" class="team-photo">
                <div class="team-photo-overlay"></div>
            </div>
            <div class="team-info">
                <h3 class="team-name">Kidist Zewde</h3>
                <p class="team-role">Cross-Team Support & Front-End Developer</p>
                <ul class="team-skills">
                    <li><i class="fas fa-hands-helping"></i>Support front-end & back-end teams</li>
                    <li><i class="fas fa-code-branch"></i>Assist with HTML/CSS/JS development</li>
                    <li><i class="fas fa-bug"></i>Debugging & testing across modules</li>
                    <li><i class="fas fa-file-alt"></i>Coordinate documentation accuracy</li>
                </ul>
            </div>
        </div>

        <div class="glass-panel team-card">
            <div class="team-photo-container">
                <img src="uploads/team/amir.jpg" alt="Photo of Amir Detamo" class="team-photo">
                <div class="team-photo-overlay"></div>
            </div>
            <div class="team-info">
                <h3 class="team-name">Amir Detamo</h3>
                <p class="team-role">Documentation Lead & UI/UX Designer</p>
                <ul class="team-skills">
                    <li><i class="fas fa-book"></i>Co-lead documentation & manuals</li>
                    <li><i class="fas fa-paint-brush"></i>Design UI/UX layouts & mockups</li>
                    <li><i class="fas fa-layer-group"></i>Convert designs to HTML/CSS</li>
                    <li><i class="fas fa-history"></i>Maintain change logs</li>
                </ul>
            </div>
        </div>

        <div class="glass-panel team-card">
            <div class="team-photo-container">
                <img src="uploads/team/adimasu.jpg" alt="Photo of Adimasu Robito" class="team-photo">
                <div class="team-photo-overlay"></div>
            </div>
            <div class="team-info">
                <h3 class="team-name">Adimasu Robito</h3>
                <p class="team-role">Documentation Lead & Front-End Designer</p>
                <ul class="team-skills">
                    <li><i class="fas fa-pen-nib"></i>Partner on documentation writing</li>
                    <li><i class="fas fa-code"></i>Develop front-end (HTML/CSS)</li>
                    <li><i class="fas fa-ruler-combined"></i>Ensure design consistency</li>
                    <li><i class="fas fa-vial"></i>Test UI & prepare demos</li>
                </ul>
            </div>
        </div>

        <div class="glass-panel team-card">
            <div class="team-photo-container">
                <img src="uploads/team/yosef.jpg" alt="Photo of Yosef Eliyas" class="team-photo">
                <div class="team-photo-overlay"></div>
            </div>
            <div class="team-info">
                <h3 class="team-name">Yosef Eliyas</h3>
                <p class="team-role">Back-End Developer & Database Assistant</p>
                <ul class="team-skills">
                    <li><i class="fas fa-server"></i>Support back-end (PHP)</li>
                    <li><i class="fas fa-database"></i>Design & maintain MySQL schemas</li>
                    <li><i class="fas fa-link"></i>Link front-end & back-end systems</li>
                    <li><i class="fas fa-bug"></i>Debug database-driven features</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>