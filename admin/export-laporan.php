<?php
require_once "../config/config.php"; adminLogin();
$bulan=(int)($_GET['bulan']??date('m'));$tahun=(int)($_GET['tahun']??date('Y'));
if($bulan<1||$bulan>12||$tahun<2000||$tahun>2100){http_response_code(400);exit('Periode tidak valid.');}
$awal=sprintf('%04d-%02d-01',$tahun,$bulan);$akhir=date('Y-m-d',strtotime($awal.' +1 month'));
header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="laporan-bumdes-'.$tahun.'-'.sprintf('%02d',$bulan).'.csv"');
$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");
function csvSafe($v){$v=(string)$v;return preg_match('/^[=+@\-\t\r]/',$v)?"'".$v:$v;}
fputcsv($out,['Jenis','Kode Transaksi','Tanggal','Kategori','Keterangan','Jumlah (Rp)']);
foreach(['pendapatan'=>'PENDAPATAN','pengeluaran'=>'PENGELUARAN'] as $table=>$label){$stmt=$pdo->prepare("SELECT kode_transaksi,tanggal,kategori,keterangan,jumlah FROM {$table} WHERE tanggal>=? AND tanggal<? ORDER BY tanggal,id");$stmt->execute([$awal,$akhir]);while($r=$stmt->fetch()){fputcsv($out,[$label,csvSafe($r['kode_transaksi']),$r['tanggal'],csvSafe($r['kategori']),csvSafe($r['keterangan']),$r['jumlah']]);}}
fclose($out);auditLog($pdo,'EXPORT_LAPORAN','Ekspor laporan '.$tahun.'-'.$bulan);exit;
