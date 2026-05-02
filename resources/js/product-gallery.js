// Product gallery, thumbnails, lightbox + zoom & pan
// Module is imported from resources/js/app.js

const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

function initProductGallery() {
  const mainImg = document.getElementById('product-main-image');
  const thumbsContainer = document.querySelector('.product-thumbs-container');
  const thumbs = document.querySelectorAll('.product-thumb');
  const prevBtn = document.querySelector('.product-thumb-prev');
  const nextBtn = document.querySelector('.product-thumb-next');

  const lightbox = document.getElementById('product-lightbox');
  const lightboxImg = document.getElementById('product-lightbox-img');
  const closeBtn = document.getElementById('product-lightbox-close');

  if (!mainImg) return;

  // Helper: set active thumb by element
  function setActiveThumb(active) {
    thumbs.forEach(t => t.classList.remove('ring', 'ring-primary'));
    if (active) active.classList.add('ring', 'ring-primary');
  }

  // Initialize active to first thumb matching mainImg.src if present
  let activeIndex = 0;
  thumbs.forEach((t, i) => {
    const src = t.dataset.src || t.querySelector('img')?.src;
    if (src && src === mainImg.src) activeIndex = i;
  });
  if (thumbs[activeIndex]) setActiveThumb(thumbs[activeIndex]);

  // Thumbnail click -> update main image and active state
  thumbs.forEach((btn, i) => {
    btn.addEventListener('click', () => {
      const src = btn.dataset.src || btn.querySelector('img')?.src;
      if (!src) return;
      mainImg.src = src;
      setActiveThumb(btn);
      activeIndex = i;
      // ensure visible
      btn.scrollIntoView({behavior: 'smooth', inline: 'center'});
    });
  });

  // Prev/Next scroll controls for thumbnail strip
  if (prevBtn && nextBtn && thumbsContainer) {
    const scrollAmount = Math.max(thumbsContainer.clientWidth * 0.6, 200);
    prevBtn.classList.remove('hidden');
    nextBtn.classList.remove('hidden');
    prevBtn.addEventListener('click', () => thumbsContainer.scrollBy({left: -scrollAmount, behavior: 'smooth'}));
    nextBtn.addEventListener('click', () => thumbsContainer.scrollBy({left: scrollAmount, behavior: 'smooth'}));
  }

  // Lightbox open/close with zoom & pan
  let scale = 1;
  let minScale = 1;
  let maxScale = 4;
  let isPanning = false;
  let startX = 0, startY = 0, translateX = 0, translateY = 0;

  function applyTransform() {
    lightboxImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
  }

  function openLightbox(src) {
    if (!lightbox || !lightboxImg) return;
    lightboxImg.src = src;
    scale = 1; translateX = 0; translateY = 0; applyTransform();
    lightbox.classList.remove('hidden');
    document.documentElement.style.overflow = 'hidden';
  }

  function closeLightbox() {
    if (!lightbox || !lightboxImg) return;
    lightbox.classList.add('hidden');
    lightboxImg.src = '';
    document.documentElement.style.overflow = '';
  }

  mainImg.addEventListener('click', () => openLightbox(mainImg.src));

  if (lightbox) {
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox || e.target === closeBtn) closeLightbox();
    });
    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);

    // Wheel to zoom
    lightboxImg.addEventListener('wheel', (e) => {
      e.preventDefault();
      const delta = -e.deltaY || e.wheelDelta || -e.detail;
      const zoomStep = 0.1;
      const prevScale = scale;
      scale = clamp(scale + (delta > 0 ? zoomStep : -zoomStep), minScale, maxScale);
      // reduce translate proportionally so zoom centers on image center
      // keep translate within reasonable bounds
      translateX = clamp(translateX, -2000, 2000);
      translateY = clamp(translateY, -2000, 2000);
      applyTransform();
    }, { passive: false });

    // Pointer-based pan (works for mouse and touch)
    lightboxImg.addEventListener('pointerdown', (e) => {
      if (scale <= 1) return; // only pan if zoomed
      isPanning = true;
      lightboxImg.setPointerCapture(e.pointerId);
      startX = e.clientX;
      startY = e.clientY;
      lightboxImg.style.cursor = 'grabbing';
    });

    window.addEventListener('pointermove', (e) => {
      if (!isPanning) return;
      const dx = e.clientX - startX;
      const dy = e.clientY - startY;
      startX = e.clientX; startY = e.clientY;
      translateX += dx; translateY += dy;
      // Optionally clamp the translate to avoid moving image completely away
      translateX = clamp(translateX, -5000, 5000);
      translateY = clamp(translateY, -5000, 5000);
      applyTransform();
    });

    window.addEventListener('pointerup', (e) => {
      if (!isPanning) return;
      isPanning = false;
      lightboxImg.releasePointerCapture?.(e.pointerId);
      lightboxImg.style.cursor = '';
    });

    // Double click to reset zoom
    lightboxImg.addEventListener('dblclick', () => {
      scale = 1; translateX = 0; translateY = 0; applyTransform();
    });

    // Keyboard navigation & escape
    document.addEventListener('keydown', (e) => {
      if (lightbox && lightbox.classList.contains('hidden')) return;
      if (e.key === 'Escape') return closeLightbox();
      if (e.key === 'ArrowRight') {
        // next image
        activeIndex = (activeIndex + 1) % thumbs.length;
        const next = thumbs[activeIndex];
        if (next) {
          const src = next.dataset.src || next.querySelector('img')?.src;
          if (src) {
            lightboxImg.src = src;
            mainImg.src = src;
            setActiveThumb(next);
            next.scrollIntoView({behavior: 'smooth', inline: 'center'});
          }
        }
      }
      if (e.key === 'ArrowLeft') {
        activeIndex = (activeIndex - 1 + thumbs.length) % thumbs.length;
        const prev = thumbs[activeIndex];
        if (prev) {
          const src = prev.dataset.src || prev.querySelector('img')?.src;
          if (src) {
            lightboxImg.src = src;
            mainImg.src = src;
            setActiveThumb(prev);
            prev.scrollIntoView({behavior: 'smooth', inline: 'center'});
          }
        }
      }
    });

    // When a thumb is clicked while lightbox is open, navigate
    thumbs.forEach((t, i) => {
      t.addEventListener('click', () => {
        const src = t.dataset.src || t.querySelector('img')?.src;
        if (!src) return;
        mainImg.src = src;
        if (!lightbox.classList.contains('hidden')) {
          lightboxImg.src = src;
        }
      });
    });
  }
}

// Auto-init on DOMContentLoaded (only if module included)
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initProductGallery);
} else {
  initProductGallery();
}
