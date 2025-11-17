
			(function(){
			  const form = document.getElementById('loginForm');
			  if(!form) return;
			  form.addEventListener('submit', function(e){
				e.preventDefault();
				window.location.href = 'homepage.html';
			  });
			})();