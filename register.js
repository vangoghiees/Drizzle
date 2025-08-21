const sindicoBtn = document.getElementById("sindicoBtn");
const moradorBtn = document.getElementById("moradorBtn");
const hideables = document.querySelectorAll(".hideable");

sindicoBtn.addEventListener("click", () => {
  sindicoBtn.classList.add("active");
  moradorBtn.classList.remove("active");
  hideables.forEach(el => el.style.display = "none");
});

moradorBtn.addEventListener("click", () => {
  moradorBtn.classList.add("active");
  sindicoBtn.classList.remove("active");
  hideables.forEach(el => el.style.display = "block");
});
