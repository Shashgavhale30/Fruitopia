// assets/js/add_fruit.js

document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.querySelector('input[name="name"]');
    const photoInput = document.querySelector('input[name="photo"]');
    const priceInput = document.querySelector('input[name="price"]');
    const unitSelect = document.querySelector('select[name="unit"]');

    const imagePreview = document.getElementById('imagePreview');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');

    function updatePreviewImage(file) {
        if (!file) {
            imagePreview.innerHTML = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.innerHTML = `
                <img src="${e.target.result}" alt="Fruit Image Preview" style="max-height: 150px; border-radius: 10px;" />
            `;
        };
        reader.readAsDataURL(file);
    }

    photoInput.addEventListener('change', () => {
        const file = photoInput.files[0];
        updatePreviewImage(file);
    });

    // Optional: Disable submit button while loading
    const form = document.getElementById('addFruitForm');
    form.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitText.style.display = 'none';
        loadingText.style.display = 'inline-block';
    });
});