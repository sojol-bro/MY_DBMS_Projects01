// JavaScript for slideshow functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.slides img');
const totalSlides = slides.length;

function changeSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    const slideWidth = slides[0].clientWidth;
    const slidesContainer = document.querySelector('.slides');
    slidesContainer.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
}

setInterval(changeSlide, 3000); // Change slide every 3 seconds
