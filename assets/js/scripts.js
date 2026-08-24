// mobile nav toggle
const navToggle = document.getElementById('navToggle');
const mobileNav = document.getElementById('mobileNav');
navToggle?.addEventListener('click', () => mobileNav.classList.toggle('hidden'));

// year
document.getElementById('year').textContent = new Date().getFullYear();

// form switcher dropdown
const switcherBtn = document.getElementById('formSwitcher');
const menu = document.getElementById('formMenu');
const title = document.getElementById('formTitle');
const formPermohonan = document.getElementById('formPermohonan');
const formKeberatan = document.getElementById('formKeberatan');

switcherBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.toggle('hidden');
});
document.addEventListener('click', () => menu.classList.add('hidden'));

menu.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', () => {
        const key = btn.dataset.form;
        title.textContent = btn.textContent;
        menu.classList.add('hidden');
        if (key === 'permohonan') {
            formPermohonan.classList.remove('hidden');
            formKeberatan.classList.add('hidden');
        } else {
            formPermohonan.classList.add('hidden');
            formKeberatan.classList.remove('hidden');
        }
        window.scrollTo({ top: document.getElementById('layanan').offsetTop - 20, behavior: 'smooth' });
    });
});

// document.addEventListener("DOMContentLoaded", () => {
//     const loader = document.getElementById("loading-screen");
//     const content = document.getElementById("main-content");

//     // Simulasi delay 1.5 detik biar kelihatan loading
//     setTimeout(() => {
//         loader.style.display = "none";
//         content.classList.remove("hidden");

//         // trigger animasi fade-in setiap elemen
//         document.querySelectorAll(".fade-in").forEach((el, i) => {
//             el.style.animationDelay = `${i * 0.3}s`; // delay bertahap
//         });
//     }, 1500);
// });

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.fade-section').forEach(section => {
        section.classList.add('animate-ready');
    });

    const sections = document.querySelectorAll(".fade-section.animate-ready");
    const options = { threshold: 0.2 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            } else {
                entry.target.classList.remove("show");
            }
        });
    }, options);

    sections.forEach(section => {
        observer.observe(section);
    });
});

// // Live Search functionality
// const searchInput = document.getElementById('searchInput');
// const cardsContainer = document.getElementById('cardsContainer');
// const searchResults = document.getElementById('searchResults');
// const resultsCount = document.getElementById('resultsCount');
// const noResults = document.getElementById('noResults');
// const allCards = document.querySelectorAll('.information-card');

// searchInput.addEventListener("input", performSearch);

// // Function to perform search
// function performSearch() {
//     const searchTerm = searchInput.value.toLowerCase().trim();
//     let visibleCount = 0;

//     allCards.forEach(card => {
//         const title = card.getAttribute('data-title').toLowerCase();
//         const content = card.getAttribute('data-content').toLowerCase();

//         // Check if search term matches title or content
//         const matches = title.includes(searchTerm) || content.includes(searchTerm);

//         if (searchTerm === '' || matches) {
//             card.style.display = 'block';
//             card.style.opacity = '1';
//             card.style.transform = 'scale(1)';
//             visibleCount++;
//         } else {
//             card.style.display = 'none';
//             card.style.opacity = '0';
//             card.style.transform = 'scale(0.95)';
//         }
//     });
// }

// let offset = 6;
// document.getElementById('loadMoreBtn').addEventListener('click', function () {
//     fetch('load-more.php', {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//         body: 'offset=' + offset
//     })
//         .then(res => res.text())
//         .then(data => {
//             if (data.trim() !== '') {
//                 document.getElementById('layanan-container').innerHTML += data;
//                 offset += 6;
//             } else {
//                 document.getElementById('loadMoreBtn').innerText = "Tidak ada lagi";
//                 document.getElementById('loadMoreBtn').disabled = true;
//             }
//         });
// });
