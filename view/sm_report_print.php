<?php
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$tglfrom = htmlspecialchars($purifier->purify(trim($_GET['start'])), ENT_QUOTES);
$tglto = htmlspecialchars($purifier->purify(trim($_GET['to'])), ENT_QUOTES);
$arsip_sm = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, "where tgl_terima between '$tglfrom' and '$tglto' order by tgl_terima ASC");
if($arsip_sm->rowCount() >= 1){
	
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
	}
	if(isset($_GET['act']) AND $_GET['act'] == "pdf"){
		$width="700";
		$size="";
	}else{
		$width="100%";
		$size="250";
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
					<h3 style="text-align:center;">Data Surat Masuk <b><?php echo tgl_indo($tglfrom);?></b> s/d <b><?php echo tgl_indo($tglto);?></b></h3>
					<table  width="<?php echo $width;?>" border="1" cellspacing="3" cellpadding="3" style='border-collapse:collapse;' align="center">
						<tr>
							<td style="padding: 5px; vertical-align: top;">No</td>
							<td style="padding: 5px; vertical-align: top;">No Agenda</td>
							<td style="padding: 5px; vertical-align: top;">No Surat</td>
							<td style="padding: 5px; vertical-align: top;">Tgl Surat</td>
							<td style="padding: 5px; vertical-align: top;">Pengirim</td>
							<td style="padding: 5px; vertical-align: top;">Perihal</td>
							<td style="padding: 5px; vertical-align: top;">Tgl Terima</td>
							<td style="padding: 5px; vertical-align: top;">Diteruskan ke</td>
						</tr><?php
						while($data_sm = $arsip_sm->fetch(PDO::FETCH_OBJ)){
							$dump_sm[]=$data_sm;
						}
						$no=1;
						foreach($dump_sm as $key => $object){
							$tglsurat = explode("-", $object->tgl_surat);
							$tglsurat = $tglsurat[2]."/".$tglsurat[1]."/".$tglsurat[0];
							$tgltrm = explode("-", $object->tgl_terima);
							$tgltrm = $tgltrm[2]."/".$tgltrm[1]."/".$tgltrm[0];
							$ListUser = json_decode($object->tujuan_surat, true);?>
							<tr>
								<td style="padding: 5px; vertical-align: top;"><?php echo $no;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->custom_noagenda;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->no_sm;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $tglsurat;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->pengirim;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->perihal;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $tgltrm;?></td>
								<td><?php 
									foreach($ListUser as $ValueUser){
										$CekUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "WHERE a.id_user='$ValueUser' ORDER BY a.nama ASC")->fetch(PDO::FETCH_OBJ);
										echo '-'.$CekUser->nama .'<br/>';
									}?>
								</td>
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
	$filename="DisposisiLetter-".$data_memo->id_sm .".pdf"; //ubah untuk menentukan nama file pdf yang dihasilkan nantinya
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