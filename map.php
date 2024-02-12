<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'navbar.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Display</title>
    <style>
        .image-container {
            padding: 20px;
        }
        .map-image {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Main content area -->
        <div class="col-md-12 image-container">
            <h1 class="text-center">Shopping District Map showing off the Adresses and Roads</h1>
            <div class="d-flex justify-content-center">
                <img src="sd-map.png" alt="Featured Map" class="map-image">
            </div>
        </div>
    </div>
</div>
</body>
</html>
