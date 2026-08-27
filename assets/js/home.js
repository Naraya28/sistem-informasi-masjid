const hero = document.querySelector('.hero');

window.addEventListener('scroll', () => {

    if(window.scrollY > 100){
        hero.classList.add('blur');
    } else {
        hero.classList.remove('blur');
    }

});