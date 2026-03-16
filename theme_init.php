<?php
/**
 * Theme setter - Include this in all pages
 */

// Default theme from localStorage or 'light'
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
?>

<script>
// Initialize theme
(function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    document.body.classList.remove('light-theme', 'dark-theme');
    document.body.classList.add(savedTheme + '-theme');
    
    // Store in session too
    fetch('set_theme.php?theme=' + savedTheme, { method: 'GET' })
        .catch(e => {}); // Silent fail
})();

function setTheme(theme) {
    // Update DOM
    document.body.classList.remove('light-theme', 'dark-theme');
    document.body.classList.add(theme + '-theme');
    document.documentElement.setAttribute('data-theme', theme);
    
    // Save to localStorage
    localStorage.setItem('theme', theme);
    
    // Save to server session
    fetch('set_theme.php?theme=' + theme, { method: 'GET' })
        .catch(e => {}); // Silent fail
}

// Keyboard shortcut: Alt + T to toggle theme
document.addEventListener('keydown', function(e) {
    if (e.altKey && e.key === 't') {
        e.preventDefault();
        const current = localStorage.getItem('theme') || 'light';
        setTheme(current === 'light' ? 'dark' : 'light');
    }
});
</script>
