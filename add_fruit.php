// Determine table name based on season
                            $table_name = '';
                            switch($season) {
                                case 'summer':
                                    $table_name = 'summer_fruits';
                                    break;
                                case 'winter':
                                    $table_name = 'winter_fruits';
                                    break;
                                case 'rainy':
                                    $table_name = 'rainy_fruits';
                                    break;<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'seller') {
    redirect('login.php');
}

$seller_id = $_SESSION['user_id'];
$season = $_GET['season'] ?? 'summer';
$error = '';
$success = '';
$added_fruit = null;

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate season parameter
$valid_seasons = ['summer', 'rainy', 'winter'];
if (!in_array($season, $valid_seasons)) {
    $season = 'summer';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        // Sanitize inputs
        $name = trim($_POST['name'] ?? '');
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
        $unit = $_POST['unit'] ?? '';

        // Validate inputs
        $valid_units = ['kg', 'dozen', 'item'];
        
        if (empty($name) || strlen($name) < 2) {
            $error = "Fruit name must be at least 2 characters long.";
        } elseif ($quantity === false) {
            $error = "Please enter a valid quantity (minimum 1).";
        } elseif ($price === false) {
            $error = "Please enter a valid price (minimum 0).";
        } elseif (!in_array($unit, $valid_units)) {
            $error = "Please select a valid unit.";
        } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $error = "Please upload a fruit photo.";
        } else {
            // Handle file upload
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileSize = $_FILES['photo']['size'];
            $fileType = $_FILES['photo']['type'];
            
            // File size limit (5MB)
            $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
            
            if ($fileSize > $maxFileSize) {
                $error = "File size too large. Maximum allowed size is 5MB.";
            } else {
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($fileExtension, $allowedfileExtensions)) {
                    $error = "Upload failed. Allowed file types: " . implode(", ", $allowedfileExtensions);
                } else {
                    // Verify file type using getimagesize for extra security
                    $imageInfo = getimagesize($fileTmpPath);
                    if ($imageInfo === false) {
                        $error = "Invalid image file.";
                    } else {
                        // Create uploads directory if not exists
                        $uploadFileDir = './uploads/';
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        // Generate unique file name
                        $newFileName = uniqid() . '_' . $seller_id . '_' . time() . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            try {
                                // Insert into database
                                $stmt = $pdo->prepare("INSERT INTO add_fruits (seller_id, name, photo, quantity, price, unit, season, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                                $stmt->execute([$seller_id, $name, $dest_path, $quantity, $price, $unit, $season]);

                                $success = "Fruit added successfully!";
                                $added_fruit = [
                                    'photo' => $dest_path,
                                    'name' => $name,
                                    'price' => $price,
                                    'unit' => $unit,
                                    'quantity' => $quantity
                                ];
                                
                                // Generate new CSRF token for next request
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                
                            } catch (PDOException $e) {
                                error_log("Add fruit error for {$season} table: " . $e->getMessage());
                                $error = "Something went wrong. Please try again.";
                                // Remove uploaded file if DB insert fails
                                if (file_exists($dest_path)) {
                                    unlink($dest_path);
                                }
                            } catch (Exception $e) {
                                error_log("Season validation error: " . $e->getMessage());
                                $error = "Invalid season selected.";
                                // Remove uploaded file if season is invalid
                                if (file_exists($dest_path)) {
                                    unlink($dest_path);
                                }
                            }
                        } else {
                            $error = "There was an error moving the uploaded file.";
                        }
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Fruit - <?php echo ucfirst($season); ?> | Fruitopia</title>
    <link rel="stylesheet" href="assets/css/add_fruit.css" />
    <style>
        .loading {
            display: none;
            color: #666;
            font-style: italic;
        }
        
        .image-preview {
            margin-top: 10px;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .error-message {
            color: #dc3545;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .success-message {
            color: #28a745;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .added-fruit-display {
            margin-top: 20px;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #28a745;
        }
        
        .added-fruit-display img {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .price-display {
            font-weight: bold;
            font-size: 1.2rem;
            color: #28a745;
        }
        
        .quantity-display {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <main class="auth-container">
        <div class="auth-card">
            <h2>Add Fruit (<?php echo ucfirst($season); ?> Season)</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php if ($added_fruit): ?>
                    <div class="added-fruit-display">
                        <img src="<?php echo htmlspecialchars($added_fruit['photo']); ?>" alt="<?php echo htmlspecialchars($added_fruit['name']); ?>" />
                        <h3 style="margin: 15px 0 5px 0;"><?php echo htmlspecialchars($added_fruit['name']); ?></h3>
                        <div class="quantity-display">Quantity: <?php echo htmlspecialchars($added_fruit['quantity']); ?></div>
                        <div class="price-display">
                            ₹<?php echo number_format($added_fruit['price'], 2); ?>
                            <?php 
                                $units_map = [
                                    'kg' => 'per kg',
                                    'dozen' => 'per dozen', 
                                    'item' => 'per item',
                                ];
                                echo $units_map[$added_fruit['unit']] ?? '';
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" autocomplete="off" id="addFruitForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="name">Fruit Name:</label>
                    <input type="text" id="name" name="name" required minlength="2" maxlength="100" 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
                </div>

                <div class="form-group">
                    <label for="photo">Photo Upload:</label>
                    <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.gif,.webp" required />
                    <small style="color: #666; font-size: 0.8rem;">Max file size: 5MB. Supported formats: JPG, PNG, GIF, WebP</small>
                    <div id="imagePreview" class="image-preview"></div>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="10000" required 
                           value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>" />
                </div>

                <div class="form-group">
                    <label for="price">Price (₹):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" max="100000" required 
                           value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" />
                </div>

                <div class="form-group">
                    <label for="unit">Unit:</label>
                    <select id="unit" name="unit" required>
                        <option value="">Select Unit</option>
                        <option value="kg" <?php echo (isset($_POST['unit']) && $_POST['unit'] === 'kg') ? 'selected' : ''; ?>>Per Kg</option>
                        <option value="dozen" <?php echo (isset($_POST['unit']) && $_POST['unit'] === 'dozen') ? 'selected' : ''; ?>>Per Dozen</option>
                        <option value="item" <?php echo (isset($_POST['unit']) && $_POST['unit'] === 'item') ? 'selected' : ''; ?>>Per Item</option>
                    </select>
                </div>

                <button type="submit" class="btn-auth" id="submitBtn">
                    <span id="submitText">Add Fruit</span>
                    <span id="loadingText" class="loading">Adding...</span>
                </button>
            </form>

            <p style="margin-top: 1rem;">
                <a href="seller_dashboard.php">← Back to Dashboard</a>
            </p>
        </div>
    </main>

    <script>
        // Image preview functionality
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size too large. Maximum allowed size is 5MB.');
                    e.target.value = '';
                    preview.innerHTML = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });

        // Form submission loading state
        document.getElementById('addFruitForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingText = document.getElementById('loadingText');
            
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            loadingText.style.display = 'inline';
        });

        // Client-side validation
        document.getElementById('name').addEventListener('input', function(e) {
            const value = e.target.value.trim();
            if (value.length < 2) {
                e.target.setCustomValidity('Fruit name must be at least 2 characters long.');
            } else {
                e.target.setCustomValidity('');
            }
        });

        document.getElementById('quantity').addEventListener('input', function(e) {
            const value = parseInt(e.target.value);
            if (value < 1) {
                e.target.setCustomValidity('Quantity must be at least 1.');
            } else {
                e.target.setCustomValidity('');
            }
        });

        document.getElementById('price').addEventListener('input', function(e) {
            const value = parseFloat(e.target.value);
            if (value < 0) {
                e.target.setCustomValidity('Price cannot be negative.');
            } else {
                e.target.setCustomValidity('');
            }
        });
    </script>
</body>
</html>