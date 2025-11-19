(function () {

  // === KEEP YOUR MODAL SYSTEM EXACTLY THE SAME ===
  function showModal(title, message) {
    const backdrop = document.createElement('div');
    backdrop.style.cssText =
      'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;';

    const modal = document.createElement('div');
    modal.style.cssText =
      'background:#fff;border-radius:12px;padding:32px;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.3);';

    const titleEl = document.createElement('h2');
    titleEl.style.cssText = 'margin:0 0 16px;font-size:20px;color:#333;';
    titleEl.textContent = title;

    const msgEl = document.createElement('p');
    msgEl.style.cssText =
      'margin:0 0 24px;font-size:14px;color:#666;line-height:1.5;white-space:pre-wrap;';
    msgEl.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.style.cssText =
      'background:#e11a24;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-weight:600;width:100%;';
    closeBtn.textContent = 'Close';
    closeBtn.addEventListener('click', () => {
      document.body.removeChild(backdrop);
    });

    modal.appendChild(titleEl);
    modal.appendChild(msgEl);
    modal.appendChild(closeBtn);
    backdrop.appendChild(modal);
    document.body.appendChild(backdrop);
  }

  // ==== REAL DB PETS ====

  const animalsEl = document.getElementById("animals");
  const selector = document.querySelector(".selector");

  function loadPets(type) {
    fetch("../includes/load_pets.php?type=" + type)
      .then(res => res.json())
      .then(list => {
        animalsEl.innerHTML = "";

        if (list.length === 0) {
          animalsEl.innerHTML = "<p>No animals found.</p>";
          return;
        }

        for (const pet of list) {
          const card = document.createElement("div");
          card.className = "animal";

          const avatar = document.createElement("div");
          avatar.className = "avatar";
          avatar.textContent = "🐾";

          const meta = document.createElement("div");
          meta.className = "meta";

          const h3 = document.createElement("h3");
          h3.textContent = pet.pet_name;

          const p = document.createElement("p");
          p.textContent = `${pet.breed || ""} • ${pet.age || ""}`;

          meta.appendChild(h3);
          meta.appendChild(p);

          const actions = document.createElement("div");
          actions.className = "actions";

          // Adopt redirect
          const btnAdopt = document.createElement("button");
          btnAdopt.className = "btn-adopt";
          btnAdopt.textContent = "Adopt";
          btnAdopt.addEventListener("click", () => {
            window.location.href = "application.php?pet_id=" + pet.pet_id;
          });

          // Details modal
          const btnDetails = document.createElement("button");
          btnDetails.className = "btn-details";
          btnDetails.textContent = "Details";
          btnDetails.addEventListener("click", () => {
            fetch("../includes/pet_details.php?pet_id=" + pet.pet_id)
              .then(r => r.json())
              .then(info => {
                const msg =
                  `Breed: ${info.breed}\n` +
                  `Age: ${info.age}\n` +
                  `Gender: ${info.gender}\n` +
                  `Size: ${info.size}\n\n` +
                  `${info.description || "No description."}`;

                showModal(info.pet_name + " - Details", msg);
              });
          });

          actions.appendChild(btnAdopt);
          actions.appendChild(btnDetails);

          card.appendChild(avatar);
          card.appendChild(meta);
          card.appendChild(actions);

          animalsEl.appendChild(card);
        }
      });
  }

  // Initialize with dogs
  loadPets("dogs");

  selector.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-type]");
    if (!btn) return;

    selector.querySelectorAll("button").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    loadPets(btn.dataset.type);
  });

})();
