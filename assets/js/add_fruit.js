// Simple Add Fruit JavaScript - Clean & Easy to Understand
document.addEventListener("DOMContentLoaded", () => {
  // Get all the form elements we need
  const nameInput = document.querySelector('input[name="name"]')
  const photoInput = document.querySelector('input[name="photo"]')
  const priceInput = document.querySelector('input[name="price"]')
  const unitSelect = document.querySelector('select[name="unit"]')
  const quantityInput = document.querySelector('input[name="quantity"]')

  // Get preview and button elements
  const imagePreview = document.getElementById("imagePreview")
  const submitBtn = document.getElementById("submitBtn")
  const form = document.getElementById("addFruitForm")

  // 🖼️ Handle Image Preview
  function showImagePreview(file) {
    // Clear previous preview
    imagePreview.innerHTML = ""

    // If no file selected, show placeholder
    if (!file) {
      imagePreview.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #666; border: 2px dashed #ddd; border-radius: 10px;">
                    📷 No image selected
                </div>
            `
      return
    }

    // Check if file is an image
    if (!file.type.startsWith("image/")) {
      imagePreview.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #ff6b6b; border: 2px dashed #ff6b6b; border-radius: 10px;">
                    ❌ Please select an image file
                </div>
            `
      return
    }

    // Show the image preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.innerHTML = `
                <img src="${e.target.result}" 
                     alt="Fruit Preview" 
                     style="max-width: 100%; max-height: 200px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
            `
    }
    reader.readAsDataURL(file)
  }

  // 📝 Simple Form Validation
  function validateForm() {
    let isValid = true
    const errors = []

    // Check fruit name
    if (!nameInput.value.trim()) {
      errors.push("Fruit name is required")
      isValid = false
    }

    // Check price
    if (!priceInput.value || priceInput.value <= 0) {
      errors.push("Valid price is required")
      isValid = false
    }

    // Check quantity
    if (quantityInput && (!quantityInput.value || quantityInput.value <= 0)) {
      errors.push("Valid quantity is required")
      isValid = false
    }

    // Show errors if any
    if (!isValid) {
      alert("Please fix these errors:\n• " + errors.join("\n• "))
    }

    return isValid
  }

  // 🔄 Handle Loading State
  function setLoadingState(isLoading) {
    if (isLoading) {
      submitBtn.disabled = true
      submitBtn.innerHTML = "⏳ Adding Fruit..."
      submitBtn.style.opacity = "0.7"
    } else {
      submitBtn.disabled = false
      submitBtn.innerHTML = "🌟 Add Fruit"
      submitBtn.style.opacity = "1"
    }
  }

  // 📸 When user selects an image
  photoInput.addEventListener("change", () => {
    const file = photoInput.files[0]
    showImagePreview(file)
  })

  // 💰 Format price input (optional - makes it user-friendly)
  priceInput.addEventListener("input", function () {
    // Remove any non-numeric characters except decimal point
    let value = this.value.replace(/[^0-9.]/g, "")

    // Ensure only one decimal point
    const parts = value.split(".")
    if (parts.length > 2) {
      value = parts[0] + "." + parts.slice(1).join("")
    }

    this.value = value
  })

  // 🚀 Handle form submission
  form.addEventListener("submit", (e) => {
    e.preventDefault() // Stop default form submission

    // Validate the form first
    if (!validateForm()) {
      return
    }

    // Show loading state
    setLoadingState(true)

    // Simulate form submission (replace with actual submission logic)
    setTimeout(() => {
      // This is where you would normally send data to server
      console.log("Form data:", {
        name: nameInput.value,
        price: priceInput.value,
        unit: unitSelect.value,
        quantity: quantityInput ? quantityInput.value : null,
        hasPhoto: photoInput.files.length > 0,
      })

      // Show success message
      alert("✅ Fruit added successfully!")

      // Reset form
      form.reset()
      showImagePreview(null)

      // Reset loading state
      setLoadingState(false)

      // Optional: Redirect to fruits list
      // window.location.href = '/fruits';
    }, 2000) // 2 second delay to simulate server processing
  })

  // 🎯 Auto-capitalize fruit name
  nameInput.addEventListener("input", function () {
    // Capitalize first letter of each word
    this.value = this.value.replace(/\b\w/g, (char) => char.toUpperCase())
  })

  // 🔢 Ensure quantity is positive number
  if (quantityInput) {
    quantityInput.addEventListener("input", function () {
      if (this.value < 0) {
        this.value = 0
      }
    })
  }

  // 🎨 Add visual feedback for required fields
  function addFieldValidation() {
    const requiredFields = [nameInput, priceInput]
    if (quantityInput) requiredFields.push(quantityInput)

    requiredFields.forEach((field) => {
      field.addEventListener("blur", function () {
        if (!this.value.trim()) {
          this.style.borderColor = "#ff6b6b"
          this.style.boxShadow = "0 0 5px rgba(255, 107, 107, 0.3)"
        } else {
          this.style.borderColor = "#5a827e"
          this.style.boxShadow = "0 0 5px rgba(90, 130, 126, 0.3)"
        }
      })
    })
  }

  // Initialize field validation
  addFieldValidation()

  // Show initial placeholder for image
  showImagePreview(null)

  console.log("🍎 Add Fruit form initialized successfully!")
})
