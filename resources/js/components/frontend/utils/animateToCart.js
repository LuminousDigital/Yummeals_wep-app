export function animateToCart(buttonEl, cartEl, onComplete) {
  if (!buttonEl || !cartEl) return;

  const buttonRect = buttonEl.getBoundingClientRect();

  // Ensure we have the visible cart element
  let visibleCartEl = cartEl;
  if (getComputedStyle(cartEl).display === 'none') {
    // If the selected cart is hidden, find the visible one
    const allCarts = document.querySelectorAll('[data-cart-icon]');
    for (let cart of allCarts) {
      if (getComputedStyle(cart).display !== 'none') {
        visibleCartEl = cart;
        break;
      }
    }
  }

  const cartIcon = visibleCartEl.querySelector('i');
  const targetRect = cartIcon ? cartIcon.getBoundingClientRect() : visibleCartEl.getBoundingClientRect();

  const dot = document.createElement('div');
  dot.style.position = 'fixed';
  dot.style.width = '12px';
  dot.style.height = '12px';
  dot.style.borderRadius = '50%';
  dot.style.background = '#facc15';
  dot.style.left = `${buttonRect.left + buttonRect.width / 2 - 8}px`;
  dot.style.top = `${buttonRect.top + buttonRect.height / 2 - 8}px`;
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
        transform: translate(${ (targetRect.left - buttonRect.left) / 2 }px, ${ (targetRect.top - buttonRect.top) / 2 - 50 }px) scale(1.2);
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
    if (visibleCartEl) {
      const icon = visibleCartEl.querySelector('i') || visibleCartEl; // Use visibleCartEl if no <i> inside (for mobile)
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
