const mobile_menu = document.getElementById('mobile_menu');
mobile_menu.addEventListener('click', e=>{
    mobile_menu.classList.remove('hidden');
})
const menuBtn = document.getElementById('menu_btn');
const mobileMenu = document.getElementById('mobile_menu');
menuBtn.addEventListener('click', () => {
    if (mobileMenu.hasAttribute('hidden')) {
      mobileMenu.removeAttribute('hidden');
      menuBtn.setAttribute('aria-expanded', 'true');
    } else {
      mobileMenu.setAttribute('hidden', '');
      menuBtn.setAttribute('aria-expanded', 'false');
    }
});