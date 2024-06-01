<!-- main menu -->
<p class="menu-group mb-3"><?= session()->user['restaurant_name'] ?></p>
<a href="<?= site_url('/products') ?>"><i class="fa-solid fa-burger me-3"></i>Produtos</a>
<a href="<?= site_url('/stocks') ?>"><i class="fa-solid fa-layer-group me-3"></i>Stocks</a>

<a href="<?= site_url('/consumptions') ?>"><i class="fa-solid fa-chart-column me-3"></i>Consumos</a>
<a href="<?= site_url('/sales') ?>"><i class="fa-solid fa-chart-line me-3"></i>Vendas</a>
<a href="<?= site_url('/api_restaurant') ?>"><i class="fa-solid fa-network-wired me-3"></i>API</a>

<hr>
<a href="<?= site_url('/users_management') ?>"><i class="fa-solid fa-user-gear me-3"></i>Gestão de Utilizadores</a>
<hr>

<a href="<?= site_url('/auth/logout') ?>"><i class="fa-solid fa-right-from-bracket me-3"></i>Sair</a>