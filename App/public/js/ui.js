document.addEventListener('DOMContentLoaded', function(){
  var modalEl = document.getElementById('imageModal');
  if (!modalEl) return;
  var modalImg = document.getElementById('imageModalImg');
  var bsModal = new bootstrap.Modal(modalEl);

  // Attach modal to small table thumbnails (keep existing behavior for tables)
  function attachImageClick(img){
    img.style.cursor = img.style.cursor || 'zoom-in';
    img.addEventListener('click', function(e){
      e.stopPropagation();
      var src = img.getAttribute('data-full-src') || img.src;
      modalImg.setAttribute('src', src);
      modalImg.setAttribute('alt', img.getAttribute('alt') || 'Imagen');
      bsModal.show();
    });
  }

  var tableImgs = document.querySelectorAll('.table img');
  tableImgs.forEach(function(i){ attachImageClick(i); });

  // New: make whole dashboard/product card open the image modal when clicked
  function attachCardClick(card){
    card.addEventListener('click', function(e){
      // ignore clicks on actionable elements inside the card
      if (e.target.closest('a, button, form, input, textarea, select')) return;
      var img = card.querySelector('.card-img-top');
      if (!img) return;
      var src = img.getAttribute('data-full-src') || img.src;
      modalImg.setAttribute('src', src);
      modalImg.setAttribute('alt', img.getAttribute('alt') || 'Imagen');
      bsModal.show();
    });
  }

  var cards = document.querySelectorAll('.card.h-100');
  cards.forEach(function(c){ attachCardClick(c); });

  modalEl.addEventListener('hidden.bs.modal', function(){ modalImg.setAttribute('src', ''); });
});
