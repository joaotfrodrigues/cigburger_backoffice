<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CigBurger Backoffice - <?= !empty($title) ? $title : '' ?></title>

    <!-- favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" type="image/png">

    <!-- bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets/libs/bootstrap/bootstrap.min.css') ?>">

    <!-- fontawesome -->
    <link rel="stylesheet" href="<?= base_url('assets/libs/fontawesome/all.min.css') ?>">

    <!-- google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">

    <!-- flatpickr -->
    <link rel="stylesheet" href="<?= base_url('assets/libs/flatpickr/flatpickr.min.css') ?>">

    <!-- css -->
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
</head>
<body>

    <!-- top bar -->
    <?= $this->include('partials/top_bar') ?>

    <section class="d-flex">
        <!-- main menu -->
        <nav class="main-menu p-2">
            <?= $this->include('partials/main_menu') ?>
        </nav>

        <!-- render section -->
        <div class="content p-4 flex-fill">
            <?= $this->renderSection('content') ?>
        </div>
    </section>

    <!-- footer -->
    <?= $this->include('partials/footer') ?>

    <!-- bootstrap -->
    <script src="<?= base_url('assets/libs/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    
    <!-- flatpickr -->
    <script src="<?= base_url('assets/libs/flatpickr/flatpickr.min.js') ?>"></script>
    
    <script>
        document.querySelector(".btn-main-menu").addEventListener("click", () => {
            document.querySelector(".main-menu").classList.toggle("show");
            document.querySelector(".content").classList.toggle("show");
        })
    </script>
    
</body>
</html>