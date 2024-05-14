function showOverlay() {
  document.getElementById('newpost-overlay').classList.remove('hidden');
  document.getElementById('newpost-overlay').classList.add('visible');
}

function hideOverlay() {
  document.getElementById('newpost-overlay').classList.add('hidden');
  document.getElementById('newpost-overlay').classList.remove('visible');
}