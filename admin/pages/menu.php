<div class="page-header">
    <h1>Kelola Menu</h1>
    <a href="?page=menu_tambah" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Menu
    </a>
</div>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="card">
    <div class="card-header">
        <h3>Daftar Menu</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $menu_list = $conn->query("SELECT * FROM menu ORDER BY kategori, nama");
                while($menu = $menu_list->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $menu['nama']; ?></td>
                    <td><?php echo ucfirst($menu['kategori']); ?></td>
                    <td>Rp <?php echo number_format($menu['harga']); ?></td>
                    <td><span class="status <?php echo $menu['status']; ?>"><?php echo ucfirst($menu['status']); ?></span></td>
                    <td class="actions">
                        <a href="?page=menu_edit&id=<?php echo $menu['id']; ?>" class="edit">Edit</a>
                        <a href="?page=menu&delete=<?php echo $menu['id']; ?>" class="delete" onclick="return confirm('Yakin hapus menu ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM menu WHERE id = $id");
    $_SESSION['message'] = 'Menu berhasil dihapus';
    header('Location: ?page=menu');
}
?>
