<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!is_logged_in() || $_SESSION['role'] !== 'seller') {
    redirect('login.php');
}

$seller_id = $_SESSION['user_id'];
$season = $_GET['season'] ?? 'summer';
$action = $_GET['action'] ?? 'add';
$fruit_id = $_GET['id'] ?? null;

$valid_seasons = ['summer', 'rainy', 'winter'];
$valid_units = ['kg', 'dozen', 'item'];
$allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$maxFileSize = 5 * 1024 * 1024;

$error = '';
$success = '';

if (!in_array($season, $valid_seasons)) {
    $season = 'summer';
}
$tableName = $season . '_fruits';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle deletion
if ($action === 'delete' && $fruit_id) {
    $stmt = $pdo->prepare("DELETE FROM `$tableName` WHERE id = ? AND seller_id = ?");
    if ($stmt->execute([$fruit_id, $seller_id])) {
        $success = "Fruit deleted successfully.";
    } else {
        $error = "Failed to delete fruit.";
    }
    $action = 'add';
}

// Load data for editing
$editData = null;
if ($action === 'edit' && $fruit_id) {
    $stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE id = ? AND seller_id = ?");
    $stmt->execute([$fruit_id, $seller_id]);
    $editData = $stmt->fetch();
    if (!$editData) {
        $error = "Fruit not found or unauthorized.";
        $action = 'add';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token. Please reload the page.";
    } else {
        $name = trim($_POST['name'] ?? '');
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
        $unit = $_POST['unit'] ?? '';

        if (empty($name) || strlen($name) < 2) {
            $error = "Fruit name must be at least 2 characters.";
        } elseif ($quantity === false) {
            $error = "Enter valid quantity.";
        } elseif ($price === false) {
            $error = "Enter valid price.";
        } elseif (!in_array($unit, $valid_units)) {
            $error = "Select a valid unit.";
        } else {
            if ($action === 'edit' && $fruit_id) {
                $stmt = $pdo->prepare("UPDATE `$tableName` SET name = ?, quantity = ?, price = ?, unit = ? WHERE id = ? AND seller_id = ?");
                $stmt->execute([$name, $quantity, $price, $unit, $fruit_id, $seller_id]);
                $success = "Fruit updated successfully.";
                $editData = null;
                $action = 'add';
            } else {
                // Validate and upload photo
                if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                    $error = "Please upload a fruit photo.";
                } else {
                    $fileTmpPath = $_FILES['photo']['tmp_name'];
                    $fileName = $_FILES['photo']['name'];
                    $fileSize = $_FILES['photo']['size'];

                    if ($fileSize > $maxFileSize) {
                        $error = "File too large. Max size is 5MB.";
                    } else {
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));

                        if (!in_array($fileExtension, $allowedfileExtensions)) {
                            $error = "Invalid file type.";
                        } else {
                            $imageInfo = getimagesize($fileTmpPath);
                            if ($imageInfo === false) {
                                $error = "Invalid image file.";
                            } else {
                                $uploadDir = './uploads/';
                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }

                                $newFileName = uniqid() . '_' . $seller_id . '.' . $fileExtension;
                                $dest_path = $uploadDir . $newFileName;

                                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                    $stmt = $pdo->prepare("INSERT INTO `$tableName` (seller_id, name, photo, quantity, price, unit, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                                    $stmt->execute([$seller_id, $name, $dest_path, $quantity, $price, $unit]);
                                    $success = "Fruit added successfully!";
                                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                } else {
                                    $error = "Failed to move uploaded file.";
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// Fetch fruits for current seller to show in UI
$stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE seller_id = ? ORDER BY created_at DESC");
$stmt->execute([$seller_id]);
$sellerFruits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fruit Management - <?= ucfirst($season) ?> Season - Fruitopia</title>
    <link rel="stylesheet" href="assets/css/add_fruit.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Back Button -->
    <a href="seller_dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <div style="background: linear-gradient(135deg, #1f7d53, #255f38); color: white; padding: 1rem; border-radius: 12px; margin: 1rem 0; text-align: center; box-shadow: 0 10px 30px rgba(31, 125, 83, 0.3);">
            ✅ <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 1rem; border-radius: 12px; margin: 1rem 0; text-align: center; box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);">
            ❌ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Existing Fruits Gallery Section -->
    <?php if ($sellerFruits): ?>
        <div class="existing-fruits">
            <h2 style="color: var(--accent-glow); text-align: center; font-size: 2.5rem; margin-bottom: 2rem; text-shadow: 0 0 20px rgba(61, 255, 136, 0.3);">
                🍎 Your <?= ucfirst($season) ?> Fruits Collection
            </h2>
            <div class="fruit-list">
                <?php foreach ($sellerFruits as $fruit): ?>
                    <div class="fruit-item">
                        <img src="<?= htmlspecialchars($fruit['photo']) ?>" alt="<?= htmlspecialchars($fruit['name']) ?>" />
                        <h4><?= htmlspecialchars($fruit['name']) ?></h4>
                        <p><?= $fruit['quantity'] . ' ' . $fruit['unit'] ?> | ₹<?= $fruit['price'] ?></p>
                        <div class="fruit-actions">
                            <a href="?season=<?= $season ?>&action=edit&id=<?= $fruit['id'] ?>" class="btn-edit">
                                ✏️ Edit
                            </a>
                            <a href="?season=<?= $season ?>&action=delete&id=<?= $fruit['id'] ?>" 
                               class="btn-delete"
                               onclick="return confirm('Are you sure you want to delete this fruit?')">
                                🗑️ Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: rgba(39, 57, 28, 0.3); border-radius: 16px; margin: 2rem 0; backdrop-filter: blur(10px);">
            <h3 style="color: var(--text-secondary); font-size: 1.5rem; margin-bottom: 1rem;">
                🌱 No fruits listed yet for <?= ucfirst($season) ?> season
            </h3>
            <p style="color: var(--text-muted);">Add your first fruit below to get started!</p>
        </div>
    <?php endif; ?>

    <!-- Form for Adding/Editing Fruits -->
    <form method="POST" enctype="multipart/form-data">
        <h2><?= $editData ? '✏️ Edit Fruit' : '🍎 Add New Fruit' ?></h2>
        
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <label for="name">Fruit Name</label>
        <input type="text" 
               id="name" 
               name="name" 
               placeholder="Enter fruit name (e.g., Red Apple, Banana)" 
               value="<?= htmlspecialchars($editData['name'] ?? '') ?>" 
               required>
        
        <label for="quantity">Quantity Available</label>
        <input type="number" 
               id="quantity" 
               name="quantity" 
               placeholder="How many units do you have?" 
               value="<?= htmlspecialchars($editData['quantity'] ?? '') ?>" 
               min="1" 
               required>
        
        <label for="price">Price per Unit (₹)</label>
        <input type="number" 
               id="price" 
               step="0.01" 
               name="price" 
               placeholder="Enter price in rupees" 
               value="<?= htmlspecialchars($editData['price'] ?? '') ?>" 
               min="0" 
               required>
        
        <label for="unit">Selling Unit</label>
        <select id="unit" name="unit" required>
            <option value="">Select how you sell this fruit</option>
            <?php foreach ($valid_units as $unit): ?>
                <option value="<?= $unit ?>" <?= (isset($editData['unit']) && $editData['unit'] === $unit) ? 'selected' : '' ?>>
                    <?= ucfirst($unit) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if (!$editData): ?>
            <label for="photo">Fruit Photo</label>
            <input type="file" 
                   id="photo" 
                   name="photo" 
                   accept="image/*" 
                   required 
                   style="padding: 0.8rem; background: rgba(15, 20, 25, 0.8); border: 2px dashed var(--border-color);">
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: -1rem; margin-bottom: 1.5rem;">
                📸 Upload a clear photo of your fruit (Max 5MB, JPG/PNG/GIF/WebP)
            </p>
        <?php endif; ?>
        
        <button type="submit">
            <?= $editData ? '🔄 Update Fruit' : '➕ Add Fruit' ?>
        </button>
    </form>

    <!-- Season Navigation -->
    <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: rgba(39, 57, 28, 0.2); border-radius: 16px; backdrop-filter: blur(10px);">
        <h3 style="color: var(--accent-green); margin-bottom: 1.5rem; font-size: 1.4rem;">
            🌍 Manage Fruits by Season
        </h3>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <?php foreach ($valid_seasons as $s): ?>
                <a href="?season=<?= $s ?>" 
                   style="padding: 0.8rem 1.5rem; 
                          background: <?= $s === $season ? 'var(--accent-green)' : 'transparent' ?>; 
                          color: <?= $s === $season ? 'white' : 'var(--text-secondary)' ?>; 
                          border: 2px solid var(--accent-green); 
                          border-radius: 12px; 
                          text-decoration: none; 
                          font-weight: 600; 
                          transition: var(--transition-smooth);
                          text-transform: capitalize;">
                    <?php 
                    $seasonEmojis = ['summer' => '☀️', 'rainy' => '🌧️', 'winter' => '❄️'];
                    echo $seasonEmojis[$s] . ' ' . ucfirst($s);
                    ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        /* Action buttons for fruit items */
        .fruit-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            opacity: 0;
            transform: translateY(10px);
            transition: var(--transition-smooth);
        }
        
        .fruit-item:hover .fruit-actions {
            opacity: 1;
            transform: translateY(0);
        }
        
        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, var(--accent-green), var(--primary-green));
            color: white;
            border: 1px solid var(--accent-green);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: 1px solid #dc3545;
        }
        
        .btn-edit:hover {
            background: linear-gradient(135deg, var(--accent-glow), var(--accent-green));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(31, 125, 83, 0.4);
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #e85d6b, #dc3545);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }
        
        .btn-edit:active, .btn-delete:active {
            transform: translateY(0);
        }
        
        /* Make buttons always visible on mobile */
        @media (max-width: 768px) {
            .fruit-actions {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>
        // Preview uploaded image
        document.getElementById('photo')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create preview if it doesn't exist
                    let preview = document.querySelector('.fruit-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'fruit-preview';
                        e.target.parentNode.insertBefore(preview, e.target.nextSibling);
                    }
                    
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Fruit Preview" style="max-width: 200px; max-height: 200px;">
                        <h3>Preview</h3>
                        <p>Your fruit photo looks great! 📸</p>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        // Auto-calculate total value
        const quantityInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price');
        
        function updateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            
            let totalDisplay = document.querySelector('.total-value');
            if (!totalDisplay && quantity > 0 && price > 0) {
                totalDisplay = document.createElement('p');
                totalDisplay.className = 'total-value';
                totalDisplay.style.cssText = 'color: var(--accent-glow); font-weight: 600; font-size: 1.1rem; text-align: center; margin-top: 1rem;';
                priceInput.parentNode.insertBefore(totalDisplay, priceInput.nextSibling);
            }
            
            if (totalDisplay && quantity > 0 && price > 0) {
                totalDisplay.textContent = `💰 Total Inventory Value: ₹${total.toFixed(2)}`;
                totalDisplay.style.display = 'block';
            } else if (totalDisplay) {
                totalDisplay.style.display = 'none';
            }
        }
        
        quantityInput?.addEventListener('input', updateTotal);
        priceInput?.addEventListener('input', updateTotal);
        
        // Initial calculation
        updateTotal();

        // Form submission loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            button.textContent = '⏳ Processing...';
            button.disabled = true;
            
            // Re-enable button after 3 seconds in case of errors
            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
            }, 3000);
        });
    </script>
</body>
</html>