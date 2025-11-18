const role = document.getElementById('role');
const providerFields = document.getElementById('providerFields');

role.addEventListener('change', () => {
    providerFields.style.display = (role.value === 'provider') ? 'block' : 'none';
});
providerFields.style.display = (role.value === 'provider') ? 'block' : 'none';