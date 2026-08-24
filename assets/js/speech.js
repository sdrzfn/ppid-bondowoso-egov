document.addEventListener("DOMContentLoaded", function () {
    // Fungsi untuk membaca teks
    function speakText(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel(); // hentikan suara sebelumnya
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = "id-ID"; // bahasa Indonesia
            window.speechSynthesis.speak(utterance);
        } else {
            console.warn("Browser tidak mendukung Speech Synthesis API.");
        }
    }

    // Pilih elemen teks yang akan dibacakan
    const textElements = document.querySelectorAll(
        "p, span, h1, h2, h3, h4, h5, h6, a, button, td, th, li, label"
    );

    textElements.forEach(el => {
        el.addEventListener("mouseenter", () => {
            let text = el.innerText.trim();
            if (text) speakText(text);
        });

        el.addEventListener("mouseleave", () => {
            window.speechSynthesis.cancel();
        });
    });
});