const modal = document.getElementById("addPetModal");
const openBtn = document.getElementById("openAddPetModal");
const closeBtn = document.querySelector(".close");

if (openBtn) {
    openBtn.onclick = (e) => {
        e.preventDefault();
        modal.style.display = "block";
    };
}

if (closeBtn) {
    closeBtn.onclick = () => {
        modal.style.display = "none";
    };
}

window.onclick = (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
};