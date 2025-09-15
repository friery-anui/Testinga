const toggle = document.createElement('button');
toggle.innerText = "🌙 Dark Mode";
document.querySelector("header").appendChild(toggle);

toggle.addEventListener("click", ()=>{
  document.body.classList.toggle("dark");
});
