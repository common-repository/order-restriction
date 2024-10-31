// List of all free Font Awesome icons
const icons = [
    "fa-x", "fa-xmark","fa-circle-xmark", "fa-envelope", "fa-search", "fa-heart", 
    "fa-star", "fa-rectangle-xmark", "fa-check", "fa-circle-check", "fa-music", "fa-clipboard-check",
    // Add all free Font Awesome icon classes here
];

// Function to load icons into the modal
function loadIcons() {
    const iconsContainer = document.getElementById('iconsContainer');
    iconsContainer.innerHTML = ''; // Clear previous icons if any
    icons.forEach(iconClass => {
        const col = document.createElement('div');
        col.className = 'col-3 text-center mb-4 icon-item';
        col.innerHTML = `<i class="fa-solid ${iconClass}" data-icon-class="${iconClass}"></i><p>${iconClass}</p>`;
        iconsContainer.appendChild(col);
    });

    // Add click event listener to each icon
    document.querySelectorAll('.icon-item').forEach(item => {
        item.addEventListener('click', function() {
            const iconClass = this.querySelector('i').getAttribute('data-icon-class');
            document.getElementById('iconClassInput').value = iconClass;
            document.getElementById('iconClassshow').classList.remove(iconClass);
            document.getElementById('iconClassshow').classList.add(iconClass);
            console.log(iconClass);
            document.getElementById('iconsModal').style.display = 'none';
        });
    });
}

// Event listener for button click
document.getElementById('showIconsBtn').addEventListener('click', () => {
    loadIcons();
    document.getElementById('iconsModal').style.display = 'block';
});

document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('iconsModal').style.display = 'none';
});



// copy shortcode
document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copyShortcodeBtn');
    const shortcodeElement = document.getElementById('ortnShortcode');

    copyButton.addEventListener('click', function() {
        const shortcode = shortcodeElement.textContent;
        navigator.clipboard.writeText(shortcode).then(function() {
            document.getElementById('copyShortcodeBtn').innerHTML = 'Copied!';
        }).catch(function(err) {
            document.getElementById('copyShortcodeBtn').innerHTML = 'Copy Failed!';
        });
    });
});
