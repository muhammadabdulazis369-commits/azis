<?php
require_once "../config/config.php";adminLogin();
$data=$pdo->query('SELECT l.*,a.nama FROM audit_log l LEFT JOIN admin a ON a.id=l.admin_id ORDER BY l.id DESC LIMIT 300')->fetchAll();
require '_header.php';?>
<div class="page-heading"><div><h1>Aktivitas Administrator</h1><p>Riwayat 300 aktivitas terakhir untuk membantu penelusuran perubahan data.</p></div></div>
<div class="content-panel"><div class="panel-heading"><h3>Log Aktivitas</h3><input type="search" id="searchTable" placeholder="Cari aktivitas..."></div><div class="table-responsive"><table class="data-table" id="tableData"><thead><tr><th>Waktu</th><th>Administrator</th><th>Aktivitas</th><th>Detail</th><th>Alamat IP</th></tr></thead><tbody><?php foreach($data as $r):?><tr><td><?=e(date('d-m-Y H:i:s',strtotime($r['created_at'])))?></td><td><?=e($r['nama']??'Akun dihapus')?></td><td><?=e($r['aksi'])?></td><td><?=e($r['detail'])?></td><td><?=e($r['ip_address'])?></td></tr><?php endforeach;?><?php if(!$data):?><tr><td colspan="5">Belum ada aktivitas tercatat.</td></tr><?php endif;?></tbody></table></div></div><?php require '_footer.php';?>
