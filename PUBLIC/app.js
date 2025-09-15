// small UI helpers
(function(){
  const t = document.createElement('button'); t.textContent='Toggle Dark'; t.style.marginLeft='12px';
  t.onclick = ()=> document.body.classList.toggle('dark');
  const header = document.querySelector('.site-header .container') || document.querySelector('.site-header');
  if (header) header.appendChild(t);
})();
