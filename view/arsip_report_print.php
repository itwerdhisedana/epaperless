<?php
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
if(isset($_GET['filtertgl']) && $_GET['filtertgl'] == 1){
	$filterTgl = htmlspecialchars($purifier->purify(trim($_GET['filtertgl'])), ENT_QUOTES);
}
$tglfrom = htmlspecialchars($purifier->purify(trim($_GET['start'])), ENT_QUOTES);
$tglto = htmlspecialchars($purifier->purify(trim($_GET['to'])), ENT_QUOTES);
$noarsip = htmlspecialchars($purifier->purify(intval(trim($_GET['noarsip']))), ENT_QUOTES);
$OtherWhere = "";
$klasifikasi = htmlspecialchars($purifier->purify(trim($_GET['klas'])), ENT_QUOTES);
if(isset($_GET['filno']) && $_GET['filno'] == 1){
	$OtherWhere .= "AND a.no_arsip = '$noarsip'";
}
if(isset($_GET['fil_klas']) && $_GET['fil_klas'] == 1){
	$OtherWhere .= "AND a.id_klasifikasi = '$klasifikasi'";
}
if(isset($_GET['fil_tgl'])  && $_GET['fil_tgl'] == 1){
	$OtherWhere .= "AND a.tgl_arsip between '$tglfrom' and '$tglto'";
}
$ArsipFile = $this->model->selectprepare("arsip_file a JOIN klasifikasi_arsip b on a.id_klasifikasi=b.id_klasifikasi", $field=null, $params=null, $where=null, "where a.id_klasifikasi !='' $OtherWhere order by a.tgl_arsip DESC");

if($ArsipFile->rowCount() >= 1){
	while($dataArsipFile = $ArsipFile->fetch(PDO::FETCH_OBJ)){
		$dump_ArsipFile[]=$dataArsipFile;
	}
	$params = array(':id' => 1);
	$pengaturan = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id", $other=null);
	if($pengaturan->rowCount() >= 1){
		$data_pengaturan= $pengaturan->fetch(PDO::FETCH_OBJ);
		$kop = $data_pengaturan->logo;
		$title = $data_pengaturan->title;
		$deskripsi = $data_pengaturan->deskripsi;
	}else{
		$kop = "default.jpg";
		$title = "SIAS - Sistem Informasi Arsip Surat";
		$deskripsi = "SIAS merupakan aplikasi pengelolaan arsip surat";
	}?>
	<html>
		<head>
			<meta http-equiv="Content-Language" content="en-us">
			<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
			<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
			<meta name="ProgId" content="FrontPage.Editor.Document">
		</head>
		<body onload="window.print()">
			<p style="text-align:center;"><img src="./<?php echo "foto/$kop";?>" width="795"></p>
			<div id="container">
				<div id="row">
					<h4 style="text-align:center;">
						Data Arsip : <?php
						if(isset($_GET['klas']) AND $_GET['klas'] !=''){
							$params = array(':id_klasifikasi' => trim($_GET['klas']));
							$DataKlas = $this->model->selectprepare("klasifikasi_arsip", $field=null, $params, "id_klasifikasi=:id_klasifikasi", $other=null);
							$dataDataKlas = $DataKlas->fetch(PDO::FETCH_OBJ);?>
								(Klasifikasi : <b><?php echo $dataDataKlas->nama_klasifikasi;?></b>)<br/><?php
						}
						if(isset($_GET['fil_tgl'])){?>
							(Tanggal <b><?php echo tgl_indo($tglfrom);?></b> s/d <b><?php echo tgl_indo($tglto);?></b>)<?php
						}?>
					</h4>
					<table  width=600 border="1" cellspacing="0" cellpadding="0" style='border-collapse:collapse;' align="center">
						<tr>
							<td style="padding: 5px; vertical-align: top;">No</td>
							<td style="padding: 5px; vertical-align: top;">Nomor Arsip</td>
							<td style="padding: 5px; vertical-align: top;">Keamanan</td>
							<td style="padding: 5px; vertical-align: top;">Klasifikasi</td>
							<td style="padding: 5px; vertical-align: top;">Tanggal Arsip</td>
							<td style="padding: 5px; vertical-align: top;">Tanggal Upload</td>
							<td style="padding: 5px; vertical-align: top;">Ket</td>
						</tr><?php
						$no=1;
						foreach($dump_ArsipFile as $key => $object){
							$tglFile = explode("-", $object->tgl_upload);
							$tglFile = $tglFile[2]."-".$tglFile[1]."-".$tglFile[0];
							$tgl_arsip = explode("-", $object->tgl_arsip);
							$tgl_arsip = $tgl_arsip[2]."-".$tgl_arsip[1]."-".$tgl_arsip[0];
							/* $UnitKerja = $this->model->selectprepare("unit_kerja", $field=null, $params=null, $where=null, "where id_unit='$object->id_unit'");
							$dataUnitKerja = $UnitKerja->fetch(PDO::FETCH_OBJ); */?>
							<tr>
								<td style="padding: 5px; vertical-align: top;"><?php echo $no;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo sprintf("%04d", $object->no_arsip);?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->keamanan;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->nama_klasifikasi;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $tgl_arsip;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $tglFile;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->ket;?></td>
							</tr><?php
						$no++;
						}?>
					</table>
				</div>
			</div>
		</body>
	</html><?php
}else{
	echo "Belum ada data";	
}
/*Cetak Direct PDF*/
if(isset($_GET['act']) AND $_GET['act'] == "pdf"){
	$filename="Report-Disposisi.pdf";
	$content = ob_get_clean();
	$content = '<page style="font-family: Verdana,Arial,Helvetica,sans-serif"">'.nl2br($content).'</page>';
	require_once 'html2pdf/html2pdf.class.php';
	try{
		$html2pdf = new HTML2PDF('P','A4','en', false, 'ISO-8859-15',array(0, 5, 0, 0));
		$html2pdf->setDefaultFont('Arial');
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($filename);
	}catch(HTML2PDF_exception $e){ 
		echo "Terjadi Error kerena : ".$e; 
	}
}?>