const slides = document.querySelectorAll('.slide');
let current = 0;

function changeSlide(){
    slides.forEach(slide => slide.classList.remove('active'));

    current++;
    if(current >= slides.length){
        current = 0;
    }

    slides[current].classList.add('active');
}

setInterval(changeSlide, 2500);