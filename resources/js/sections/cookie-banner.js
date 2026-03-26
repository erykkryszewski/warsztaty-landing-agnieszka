const banner = document.getElementById('cookieBanner');
const acceptBtn = document.getElementById('cookieAccept');

if (banner && acceptBtn) {
  if (!localStorage.getItem('cookies_accepted')) {
    banner.style.display = '';
  }

  acceptBtn.addEventListener('click', () => {
    localStorage.setItem('cookies_accepted', '1');
    banner.style.display = 'none';
  });
}
