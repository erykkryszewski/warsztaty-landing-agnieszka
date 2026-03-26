const modal = document.getElementById('videoModal');
const player = document.getElementById('videoPlayer');

if (modal && player) {
  const openButtons = document.querySelectorAll('[data-video-open]');
  const closeButtons = modal.querySelectorAll('[data-video-close]');

  function openModal() {
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('is-active');
    player.play();
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    player.pause();
    modal.classList.remove('is-active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  openButtons.forEach((btn) => btn.addEventListener('click', openModal));
  closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('is-active')) {
      closeModal();
    }
  });
}
