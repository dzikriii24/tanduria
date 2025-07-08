document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("modal-overlay");
  const modalTitle = document.getElementById("modal-title");
  const modalText = document.getElementById("modal-text");
  const closeBtn = document.getElementById("modal-close");

  const buttons = document.querySelectorAll("[data-modal-title]");
  buttons.forEach(button => {
    button.addEventListener("click", () => {
      modalTitle.textContent = button.getAttribute("data-modal-title");
      modalText.textContent = button.getAttribute("data-modal-text");
      overlay.classList.remove("hidden");
      overlay.classList.add("flex");
    });
  });

  closeBtn.addEventListener("click", () => {
    overlay.classList.add("hidden");
    overlay.classList.remove("flex");
  });

  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) {
      overlay.classList.add("hidden");
      overlay.classList.remove("flex");
    }
  });
});
