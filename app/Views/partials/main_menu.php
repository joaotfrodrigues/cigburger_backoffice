<!-- main menu -->
<p class="menu-group mb-3"><?= session()->user['restaurant_name'] ?></p>
<a href="<?= site_url('/products') ?>"><i class="fa-solid fa-burger me-3"></i>Produtos</a>
<a href="<?= site_url('/stocks') ?>"><i class="fa-solid fa-layer-group me-3"></i>Stocks</a>
<!-- <a href="#"><i class="fa-solid fa-chart-column me-3"></i>Dados Estatísticos</a> -->