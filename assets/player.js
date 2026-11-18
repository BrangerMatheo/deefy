(function () {
  var audio = document.getElementById('audio');
  if (!audio) return;

  var player = document.getElementById('player');
  var seek = document.getElementById('seek');
  var vol = document.getElementById('vol');
  var cur = document.getElementById('cur');
  var dur = document.getElementById('dur');
  var title = document.getElementById('now-title');
  var artist = document.getElementById('now-artist');
  var current = null;

  function fmt(s) {
    if (!isFinite(s)) return '0:00';
    var m = Math.floor(s / 60), r = Math.floor(s % 60);
    return m + ':' + (r < 10 ? '0' : '') + r;
  }
  function fill(input) {
    var max = parseFloat(input.max) || 1;
    input.style.setProperty('--fill', (input.value / max * 100) + '%');
  }
  function mark(row) {
    document.querySelectorAll('.track.is-playing').forEach(function (r) { r.classList.remove('is-playing'); });
    if (row) row.classList.add('is-playing');
  }
  function load(row) {
    current = row;
    audio.src = row.dataset.src;
    title.textContent = row.dataset.title;
    artist.textContent = row.dataset.artist;
    audio.play();
  }
  function toggle() {
    if (!current) {
      var first = document.querySelector('.track');
      if (first) load(first);
      return;
    }
    audio.paused ? audio.play() : audio.pause();
  }
  function step(dir) {
    if (!current) return;
    var rows = Array.prototype.slice.call(current.closest('.tracks').querySelectorAll('.track'));
    var next = rows[rows.indexOf(current) + dir];
    if (next) load(next);
  }

  document.addEventListener('click', function (e) {
    var row = e.target.closest('.track');
    if (!row) return;
    row === current ? toggle() : load(row);
  });
  document.getElementById('btn-play').addEventListener('click', toggle);
  document.getElementById('btn-next').addEventListener('click', function () { step(1); });
  document.getElementById('btn-prev').addEventListener('click', function () {
    if (audio.currentTime > 3) { audio.currentTime = 0; } else { step(-1); }
  });

  audio.addEventListener('play', function () { player.classList.add('is-playing'); mark(current); });
  audio.addEventListener('pause', function () { player.classList.remove('is-playing'); if (current) current.classList.remove('is-playing'); });
  audio.addEventListener('ended', function () { step(1); });
  audio.addEventListener('loadedmetadata', function () { seek.max = audio.duration; dur.textContent = fmt(audio.duration); });
  audio.addEventListener('timeupdate', function () {
    seek.value = audio.currentTime; cur.textContent = fmt(audio.currentTime); fill(seek);
  });
  seek.addEventListener('input', function () { audio.currentTime = seek.value; fill(seek); });
  vol.addEventListener('input', function () { audio.volume = vol.value; fill(vol); });
  audio.volume = vol.value; fill(vol);
})();
