(function(){
  function showModal(title, message) {
    // Create backdrop
    const backdrop = document.createElement('div');
    backdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;';

    // Create modal box
    const modal = document.createElement('div');
    modal.style.cssText = 'background:#fff;border-radius:12px;padding:32px;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.3);';

    // Title
    const titleEl = document.createElement('h2');
    titleEl.style.cssText = 'margin:0 0 16px;font-size:20px;color:#333;';
    titleEl.textContent = title;

    // Message
    const msgEl = document.createElement('p');
    msgEl.style.cssText = 'margin:0 0 24px;font-size:14px;color:#666;line-height:1.5;white-space:pre-wrap;';
    msgEl.textContent = message;

    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.style.cssText = 'background:#e11a24;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-weight:600;width:100%;';
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

  // Sample data for each group.
  const data = {
    dogs: [
      { id: 1, name: 'Milo', breed: 'Beagle', age: '2 years', emoji: '🐶' },
      { id: 2, name: 'Roxy', breed: 'Pug', age: '3 years', emoji: '🐶' },
      { id: 3, name: 'Zeus', breed: 'Labrador', age: '1 year', emoji: '🐶' },
      { id: 4, name: 'Suki', breed: 'Golden Retriever', age: '2 years', emoji: '🐶' },
      { id: 5, name: 'Daisy', breed: 'German Shepherd', age: '3 years', emoji: '🐶' },
      { id: 6, name: 'Lucy', breed: 'Siberian Husky', age: '1 year', emoji: '🐶' },
      { id: 7, name: 'Max', breed: 'Chihuahua', age: '2 years', emoji: '🐶' },
      { id: 8, name: 'Min', breed: 'Dachshund', age: '3 years', emoji: '🐶' },
      { id: 9, name: 'Cooper', breed: 'Yorkshire Terrier', age: '1 year', emoji: '🐶' }

    ],
    cats: [
      { id: 11, name: 'Luna', breed: 'Siamese', age: '1 year', emoji: '🐱' },
      { id: 12, name: 'Simba', breed: 'Maine Coon', age: '4 years', emoji: '🐱' },
      { id: 13, name: 'Nala', breed: 'Domestic Shorthair', age: '6 months', emoji: '🐱' },
      { id: 14, name: 'Milo', breed: 'Persian', age: '1 year', emoji: '🐱' },
      { id: 15, name: 'Oliver', breed: 'Maine Coon', age: '4 years', emoji: '🐱' },
      { id: 16, name: 'Leo', breed: 'Domestic Shorthair', age: '6 months', emoji: '🐱' },
      { id: 17, name: 'Lily', breed: 'Siamese', age: '1 year', emoji: '🐱' },
      { id: 18, name: 'Bella', breed: 'MainAmerican Shorthaire Coon', age: '4 years', emoji: '🐱' },
      { id: 19, name: 'Cutie', breed: 'Domestic Shorthair', age: '6 months', emoji: '🐱' }
    ],
    rabbits: [
      { id: 21, name: 'Thumper', breed: 'Dutch', age: '9 months', emoji: '🐰' },
      { id: 22, name: 'BunBun', breed: 'Lionhead', age: '2 years', emoji: '🐰' },
      { id: 23, name: 'Oreo', breed: 'Dutch', age: '9 months', emoji: '🐰' },
      { id: 24, name: 'Cocoa', breed: 'Lionhead', age: '2 years', emoji: '🐰' },
      { id: 25, name: 'Luna', breed: 'Dutch', age: '9 months', emoji: '🐰' },
      { id: 26, name: 'Peter', breed: 'Lionhead', age: '2 years', emoji: '🐰' },
      { id: 27, name: 'Flopsy', breed: 'Dutch', age: '9 months', emoji: '🐰' },
      { id: 28, name: 'Peanut', breed: 'Lionhead', age: '2 years', emoji: '🐰' },
      { id: 29, name: 'Muffin', breed: 'Lionhead', age: '2 years', emoji: '🐰' }
    ]
  };

  const animalsEl = document.getElementById('animals');
  const selector = document.querySelector('.selector');

  function renderList(type){
    const list = data[type] || [];
    animalsEl.innerHTML = '';
    if(list.length === 0){
      animalsEl.innerHTML = '<p>No animals found in this category.</p>';
      return;
    }

    for(const a of list){
      const card = document.createElement('div');
      card.className = 'animal';

      const avatar = document.createElement('div');
      avatar.className = 'avatar';
      avatar.textContent = a.emoji || '🐾';

      const meta = document.createElement('div');
      meta.className = 'meta';
      const h3 = document.createElement('h3');
      h3.textContent = a.name;
      const p = document.createElement('p');
      p.textContent = a.breed + ' • ' + a.age;
      meta.appendChild(h3);
      meta.appendChild(p);

      const actions = document.createElement('div');
      actions.className = 'actions';
      const btnAdopt = document.createElement('button');
      btnAdopt.className = 'btn-adopt';
      btnAdopt.textContent = 'Adopt';
      btnAdopt.addEventListener('click', ()=>{
        showModal('Adoption Request', 'You have requested to adopt ' + a.name + '.\n\nPlease proceed with the adoption flow to complete your application.');
      });
      const btnDetails = document.createElement('button');
      btnDetails.className = 'btn-details';
      btnDetails.textContent = 'Details';
      btnDetails.addEventListener('click', ()=>{
        showModal(a.name + ' - Details', 'Breed: ' + a.breed + '\nAge: ' + a.age + '\n\nThis ' + a.breed + ' is available for adoption.');
      });
      actions.appendChild(btnAdopt);
      actions.appendChild(btnDetails);

      card.appendChild(avatar);
      card.appendChild(meta);
      card.appendChild(actions);

      animalsEl.appendChild(card);
    }
  }

  // Initialize with dogs selected
  renderList('dogs');

  selector.addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-type]');
    if(!btn) return;
    const type = btn.getAttribute('data-type');

    selector.querySelectorAll('button').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    renderList(type);
  });

})();
