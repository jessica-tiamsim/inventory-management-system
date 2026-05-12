const modal = document.getElementById('recordMovementModal');

function openModal() {
  modal.style.display = 'flex';
}

function closeModal() {
  modal.style.display = 'none';
}

// Close modal when clicking outside the modal content box
window.onclick = function (e) {
  if (e.target === modal) closeModal();
};