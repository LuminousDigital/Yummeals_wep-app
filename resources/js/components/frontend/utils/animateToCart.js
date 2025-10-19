export function animateToCart(buttonEl, cartEl, onComplete) {
  if (!buttonEl || !cartEl) return;

  const buttonRect = buttonEl.getBoundingClientRect();

  const cartIcon = cartEl.querySelector('i');
  const targetRect = cartIcon ? cartIcon.getBoundingClientRect() : cartEl.getBoundingClientRect();

  const dot = document.createElement('div');
  dot.style.position = 'fixed';
  dot.style.width = '12px';
  dot.style.height = '12px';
  dot.style.borderRadius = '50%';
  dot.style.background = '#facc15'; // yellow-400
  dot.style.left = `${buttonRect.left + buttonRect.width / 2 - 6}px`;
  dot.style.top = `${buttonRect.top + buttonRect.height / 2 - 6}px`;
  dot.style.zIndex = 9999;
  dot.style.pointerEvents = 'none';
  dot.style.animation = 'moveToCart 1.2s cubic-bezier(0.25,0.46,0.45,0.94) forwards';

  document.body.appendChild(dot);

  const keyframes = `
    @keyframes moveToCart {
      0% {
        transform: translate(0, 0) scale(1);
        opacity: 1;
      }
      50% {
        transform: translate(${targetRect.left - buttonRect.left}px, ${targetRect.top - buttonRect.top}px) scale(1.2);
        opacity: 0.8;
      }
      100% {
        transform: translate(${targetRect.left - buttonRect.left}px, ${targetRect.top - buttonRect.top}px) scale(0.3);
        opacity: 0;
      }
    }
  `;
  const style = document.createElement('style');
  style.innerHTML = keyframes;
  document.head.appendChild(style);

  dot.addEventListener('animationend', () => {
    dot.remove();
    // Add pop effect to cart icon only
    if (cartEl) {
      const icon = cartEl.querySelector('i');
      if (icon) {
        icon.classList.add('cart-icon-pop');
        setTimeout(() => {
          icon.classList.remove('cart-icon-pop');
        }, 400);
      }
    }
    onComplete && onComplete();
  });
}
