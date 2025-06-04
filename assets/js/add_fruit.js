document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.querySelector('input[name="name"]');
    const photoInput = document.querySelector('input[name="photo"]');
    const priceInput = document.querySelector('input[name="price"]');
    const unitSelect = document.querySelector('select[name="unit"]');

    const previewDiv = document.createElement('div');
    previewDiv.classList.add('fruit-preview');

    const form = document.querySelector('form');
    form.after(previewDiv);

    function updatePreview() {
        const name = nameInput.value.trim();
        const photo = photoInput.value.trim();
        const price = priceInput.value.trim();
        const unit = unitSelect.options[unitSelect.selectedIndex].text;

        if (name && photo && price) {
            previewDiv.innerHTML = `
                <img src="${photo}" alt="${name}">
                <h3>${name}</h3>
                <p>₹${price} ${unit}</p>
            `;
        } else {
            previewDiv.innerHTML = '';
        }
    }

    nameInput.addEventListener('input', updatePreview);
    photoInput.addEventListener('input', updatePreview);
    priceInput.addEventListener('input', updatePreview);
    unitSelect.addEventListener('change', updatePreview);
});
