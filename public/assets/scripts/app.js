const menu = document.querySelector('header');

menu.addEventListener('click', (event) => {
    if (event.target === menu) {
        document.querySelector('nav')?.classList.toggle('open');
    }
});