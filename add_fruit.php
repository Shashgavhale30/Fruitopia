<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'seller') {
    redirect('login.php');
}

$seller_id = $_SESSION['user_id'];
$season = $_GET['season'] ?? 'summer';
$error = '';
$success = '';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$valid_seasons = ['summer', 'rainy', 'winter'];
if (!in_array($season, $valid_seasons)) {
    $season = 'summer';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        $name = trim($_POST['name'] ?? '');
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
        $unit = $_POST['unit'] ?? '';

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
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileSize = $_FILES['photo']['size'];

            $maxFileSize = 5 * 1024 * 1024;

            if ($fileSize > $maxFileSize) {
                $error = "File size too large. Maximum allowed size is 5MB.";
            } else {
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($fileExtension, $allowedfileExtensions)) {
                    $error = "Upload failed. Allowed file types: " . implode(", ", $allowedfileExtensions);
                } else {
                    $imageInfo = getimagesize($fileTmpPath);
                    if ($imageInfo === false) {
                        $error = "Invalid image file.";
                    } else {
                        $uploadFileDir = './uploads/';
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        $newFileName = uniqid() . '_' . $seller_id . '_' . time() . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            try {
                                $tableName = $season . '_fruits';
                                $stmt = $pdo->prepare("INSERT INTO `$tableName` (seller_id, name, photo, quantity, price, unit, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                                $stmt->execute([$seller_id, $name, $dest_path, $quantity, $price, $unit]);

                                $success = "Fruit added successfully!";
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                            } catch (PDOException $e) {
                                error_log("Add fruit error for $tableName: " . $e->getMessage());
                                $error = "Something went wrong. Please try again.";
                                if (file_exists($dest_path)) unlink($dest_path);
                            }
                        } else {
                            $error = "Error moving the uploaded file.";
                        }
                    }
                }
            }
        }
    }
}

try {
    $tableName = $season . '_fruits';
    $stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$seller_id]);
    $existing_fruits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $existing_fruits = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Fruit - <?php echo ucfirst($season); ?> | Fruitopia</title>
    <link rel="stylesheet" href="assets/css/add_fruit.css" />
</head>
<body>
    <main class="auth-container">
        <div class="auth-card">
            <h2>Add Fruit (<?php echo ucfirst($season); ?> Season)</h2>

            <?php if (!empty($existing_fruits)): ?>
                <section class="existing-fruits">
                    <h3>Your Added Fruits (<?php echo ucfirst($season); ?>)</h3>
                    <div class="fruit-list">
                        <?php foreach ($existing_fruits as $fruit): ?>
                            <div class="fruit-item">
                                <img src="<?php echo htmlspecialchars($fruit['photo']); ?>" alt="<?php echo htmlspecialchars($fruit['name']); ?>" />
                                <h4><?php echo htmlspecialchars($fruit['name']); ?></h4>
                                <p>Quantity: <?php echo (int)$fruit['quantity'] . ' ' . htmlspecialchars($fruit['unit']); ?></p>
                                <p>Price: ₹<?php echo number_format($fruit['price'], 2); ?></p>
                                <small>Added on: <?php echo htmlspecialchars($fruit['created_at']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php else: ?>
                <p>No fruits added yet for <?php echo ucfirst($season); ?> season.</p>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
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
                    <small>Max file size: 5MB. Supported formats: JPG, PNG, GIF, WebP</small>
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
                    <span id="loadingText" class="loading" style="display:none;">Adding...</span>
                </button>
            </form>

            <p>
                <a class="back-btn" href="seller_dashboard.php">←Back</a>
            </p>
        </div>
    </main>

    <script src="assets/js/add_fruit.js"></script>
</body>
</html>
