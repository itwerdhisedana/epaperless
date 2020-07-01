<?php
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$params = array(':id_sm' => trim($_GET['smid']));
$memo = $this->model->selectprepare("arsip_sm", $field=null, $params, "id_sm=:id_sm", $order=null);
if($memo->rowCount() >= 1){
	$data_memo = $memo->fetch(PDO::FETCH_OBJ);
	$tgl_memo = $data_memo->tgl_surat;

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
	
	$ListUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
	$TujuanSurat = "";
	while($dataListUser = $ListUser->fetch(PDO::FETCH_OBJ)){
		if(false !== array_search($dataListUser->id_user, json_decode($data_memo->tujuan_surat, true))){
			$TujuanSurat .= '- '.$dataListUser->nama .'<br/>';
		}
	}
	
	$kopSet = $this->model->selectprepare("kop_setting", $field=null, $params=null, $where=null, "WHERE idkop='1'");
	$dataKopSet= $kopSet->fetch(PDO::FETCH_OBJ);
	$layout = $dataKopSet->layout;
	$Rlayout = $layout;
	
	$arr = array("=NoAgenda=" => $data_memo->custom_noagenda, "=NoSurat=" => $data_memo->no_sm, "=Perihal=" => $data_memo->perihal, "=TujuanSurat=" => $TujuanSurat, "=TglSurat=" =>tgl_indo($data_memo->tgl_surat), "=TglTerima=" => tgl_indo($data_memo->tgl_terima), "=AsalSurat=" =>$data_memo->pengirim, "=Keterangan=" => $data_memo->ket, "=Penerima=" => $_SESSION['nama']);
	foreach($arr as $nama => $value){
		if(strpos($layout, $nama) !== false) {
			$Rlayout = str_replace($nama, $value, $layout);
			$layout = $Rlayout;
		}
	}?>
	<html>
		<head>
			<title><?php echo $title;?></title>
			<meta name="description" content="<?php echo $deskripsi;?>" />
			<meta http-equiv="Content-Language" content="en-us">
			<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
			<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
			<meta name="ProgId" content="FrontPage.Editor.Document">
			
		</head>
		<body><?php
			if($dataKopSet->status == "Y"){
				if($dataKopSet->kopdefault == "Y"){?>
					<p style="text-align:center;"><img src="./<?php echo "foto/$kop";?>" width="795"></p><?php
				}?>
				<?php echo $Rlayout; ?><?php
			}else{
				if($dataKopSet->kopdefault == "Y"){?>
					<p style="text-align:center;"><img src="./<?php echo "foto/$kop";?>" width="795"></p><?php
				}?>
				<h3 style="text-align:center;">TANDA TERIMA SURAT</h3>
				<table border="1" width="700" style='border-collapse:collapse;' align="center">
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Telah terima dari</td>
						<td nowrap style="padding: 5px; vertical-align: top;"><?php echo $data_memo->pengirim;?></td>
					</tr>
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Nomor Surat </td>
						<td style="padding: 5px;"><?php echo $data_memo->no_sm;?></td>
					</tr>
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Nomor Agenda </td>
						<td style="padding: 5px;"><?php echo $data_memo->custom_noagenda;?></td>
					</tr>
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Tanggal Surat </td>
						<td style="padding: 5px;"><?php echo tgl_indo($data_memo->tgl_surat);?></td>
					</tr>
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Tujuan Surat </td>
						<td style="padding: 5px;"><?php echo $TujuanSurat;?></td>
					</tr>
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Tanggal Terima </td>
						<td style="padding: 5px;"><?php echo tgl_indo($data_memo->tgl_terima);?></td>
					</tr>
					<tr align=left height="70">
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">Perihal </td>
						<td style="padding: 5px; vertical-align: top;"><?php echo $data_memo->perihal;?></td>
					</tr>
				</table><br/>
				<table border="0" width=400 style='border-collapse:collapse;' align="center">
					<tr align=left>
						<td style="padding: 5px; vertical-align: top; width: 238.776px;" nowrap="nowrap">
							Yang Menyerahkan<br/><br/><br/><br/><br/><br/><b><u><?php echo $data_memo->pengirim;?></u></b>
						</td>
						<td width="160" nowrap style="padding: 5px;">
							Yang Menerima<br/><br/><br/><br/><br/><br/><b><u><?php echo $_SESSION['nama'];?></u></b>
						</td>
					</tr>
				</table><?php
			}?>
		</body>
	</html><?php
}else{
	echo "Belum ada data";	
}

/*Cetak Direct PDF*/
if(isset($_GET['act']) AND $_GET['act'] == "pdf"){
	$filename="Letter-".$data_memo->id_sm.".pdf"; //ubah untuk menentukan nama file pdf yang dihasilkan nantinya
	$content = ob_get_clean();
	$content = '<page style="font-family: freeserif">'.nl2br($content).'</page>';
	require_once 'html2pdf/html2pdf.class.php';
	try{
		$html2pdf = new HTML2PDF('P','A4','en', false, 'ISO-8859-15',array(0, 5, 0, 0));
		$html2pdf->setDefaultFont('Arial');
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($filename);
	}catch(HTML2PDF_exception $e){ 
		echo "Terjadi Error kerena : ".$e; 
	}
}
?>