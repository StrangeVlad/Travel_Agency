document.addEventListener('DOMContentLoaded', () => {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.drop-down');

    dropdownToggle.addEventListener('click', (e) => {
        e.preventDefault();
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
        if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
});


 // Retrieve the name from localStorage and display it
 const userName = localStorage.getItem('userName');
 if (userName) {
     document.getElementById('userName').textContent = userName;
     document.getElementById('displayCircle').style.display = 'block';
 }


// for contct o button submit 
 document.getElementById('contactForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const name = document.getElementById('name').value;
    localStorage.setItem('userName', name);
    window.location.href = 'index.html';
});



// عناصر التحكم
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');

// وظيفة إظهار/إخفاء القائمة
menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('hidden'); 
    navLinks.classList.toggle('show');
});


function changeLanguage(lang) {
    // Remove active class from all buttons
    document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));

    // Set active class to clicked button
    if (lang === 'fr') {
        document.querySelector('.lang-btn:nth-child(1)').classList.add('active');
        // Redirect or load French content
    } else {
        document.querySelector('.lang-btn:nth-child(2)').classList.add('active');
        // Redirect or load English content
    }
}
