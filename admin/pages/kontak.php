<div class="page-header">
    <h1>Pesan & Kontak</h1>
</div>

<?php
if (isset($_POST['update_kontak'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $conn->query("UPDATE kontak SET status='$status' WHERE id=$id");
    echo '<div class="alert alert-success">Status pesan berhasil diupdate</div>';
}
?>

<div class="card">
    <div class="card-header">
        <h3>Daftar Pesan</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kontak</th>
                    <th>Pesan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $kontak = $conn->query("SELECT * FROM kontak ORDER BY tanggal_kirim DESC");
                if ($kontak->num_rows > 0):
                    while($k = $kontak->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $k['nama']; ?></td>
                    <td>
                        <?php 
                        echo $k['email'] ? $k['email'] : '-'; 
                        echo $k['no_wa'] ? '<br><small>' . $k['no_wa'] . '</small>' : '';
                        ?>
                    </td>
                    <td style="max-width: 300px;">
                        <div style="max-height: 80px; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo substr($k['pesan'], 0, 100); ?>...
                        </div>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                            <select name="status" onchange="this.form.submit()" style="padding:5px; border-radius:5px; border:1px solid #ddd;">
                                <option value="baru" <?php echo $k['status'] == 'baru' ? 'selected' : ''; ?>>Baru</option>
                                <option value="dibaca" <?php echo $k['status'] == 'dibaca' ? 'selected' : ''; ?>>Dibaca</option>
                                <option value="dibalas" <?php echo $k['status'] == 'dibalas' ? 'selected' : ''; ?>>Dibalas</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($k['tanggal_kirim'])); ?></td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Belum ada pesan</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
