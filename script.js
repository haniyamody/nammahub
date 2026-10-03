const slides = document.querySelector(".slides");
const images = document.querySelectorAll(".slides img");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");

let index = 0;

function showSlide(i) {
  if (i < 0) index = images.length - 1;
  else if (i >= images.length) index = 0;
  else index = i;

  slides.style.transform = `translateX(${-index * 100}%)`;
}

nextBtn.addEventListener("click", () => {
  showSlide(index + 1);
});

prevBtn.addEventListener("click", () => {
  showSlide(index - 1);
});

/* Swipe Support */

let startX = 0;

slides.addEventListener("touchstart", (e) => {
  startX = e.touches[0].clientX;
});

slides.addEventListener("touchend", (e) => {
  let endX = e.changedTouches[0].clientX;

  if (startX - endX > 50) {
    showSlide(index + 1);
  } else if (endX - startX > 50) {
    showSlide(index - 1);
  }
});