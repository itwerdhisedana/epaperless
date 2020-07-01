<?php
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$id_sm = htmlspecialchars($purifier->purify(trim($_GET['smid'])), ENT_QUOTES);
$id_dispo = htmlspecialchars($purifier->purify(trim($_GET['dispo'])), ENT_QUOTES);
if(isset($_GET['iduser']) && isset($_GET['dispo'])){
	$iduser = htmlspecialchars($purifier->purify(trim($_GET['iduser'])), ENT_QUOTES);
	$params = array(':id_sm' => $id_sm, ':id_user' => $id_dispo,);
	$field = array("a.id_user as userDis","a.*","b.*");
	$memo = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params, "a.id_sm=:id_sm AND a.id_user=:id_user", "AND a.disposisi LIKE '%\"$iduser\"%'");
}else{
	$params = array(':id_sm' => $id_sm, ':id_user' => $id_dispo);
	$field = array("a.id_user as userDis","a.*","b.*");
	$memo = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params, "a.id_sm=:id_sm AND a.id_user=:id_user", "AND a.disposisi LIKE '%\"$_SESSION[id_user]\"%'");
}
if($memo->rowCount() >= 1){
	$data_memo = $memo->fetch(PDO::FETCH_OBJ);
	$tgl_memo = substr($data_memo->tgl,0,10);
	
	$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$data_memo->userDis'");
	$DataUser = $CekUser->fetch(PDO::FETCH_OBJ);
	
	$ListUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
	$TujuanSurat = "";
	$TargetDisposisi = "";
	$DataTembusanVer = "";
	$DataTembusanHor = "";
	while($dataListUser = $ListUser->fetch(PDO::FETCH_OBJ)){
		if(is_array(json_decode($data_memo->disposisi))){
			if(false !== array_search($dataListUser->id_user, json_decode($data_memo->disposisi, true))){
				$TargetDisposisi .= '- '.$dataListUser->nama .'<br/>';
			}
		}
		if(is_array(json_decode($data_memo->tujuan_surat))){
			if(false !== array_search($dataListUser->id_user, json_decode($data_memo->tujuan_surat, true))){
				$TujuanSurat .= '- '.$dataListUser->nama .'<br/>';
			}
		}
		if(is_array(json_decode($data_memo->tembusan))){
			if(false !== array_search($dataListUser->id_user, json_decode($data_memo->tembusan, true))){
				$DataTembusanVer .= '- '.$dataListUser->nama .'<br/>';
				$DataTembusanHor .='- '.$dataListUser->nama .', ';
			}
		}
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
	}
	
	$kopSet = $this->model->selectprepare("kop_setting", $field=null, $params=null, $where=null, "WHERE idkop='3'");
	$dataKopSet= $kopSet->fetch(PDO::FETCH_OBJ);
	$layout = $dataKopSet->layout;
	$Rlayout = $layout;
	
	/* $DataTembusanVer = "";
	$DataTembusanHor = "";
	if($data_memo->tembusan != ''){
		$data_tembusan = json_decode($data_memo->tembusan, true);
		foreach($data_tembusan as $id_tembusan){
			$params = array(':id_bag' => $id_tembusan);
			$user_tembusan = $this->model->selectprepare("bagian", $field=null, $params, "id_bag=:id_bag", $other=null);
			$data_user_tembusan= $user_tembusan->fetch(PDO::FETCH_OBJ);
			$DataTembusanVer .='- '.$data_user_tembusan->nama_bagian .'<br/>';
			$DataTembusanHor .='- '.$data_user_tembusan->nama_bagian .', ';
		}
	} */
	
	$arr = array("=NoAgenda=" => $data_memo->custom_noagenda, "=NoSurat=" => $data_memo->no_sm, "=Perihal=" => $data_memo->perihal, "=TujuanSurat=" => $TujuanSurat, "=TglSurat=" =>tgl_indo($data_memo->tgl_surat), "=TglTerima=" => tgl_indo($data_memo->tgl_terima), "=AsalSurat=" =>$data_memo->pengirim, "=DisposisiOleh=" => $DataUser->nama, "=Disposisi=" => $TargetDisposisi, "=TglDisposisi=" => tgl_indo($data_memo->tgl), "=Keterangan=" => $data_memo->ket, "=TembusanV=" => $DataTembusanVer, "=TembusanH=" => $DataTembusanHor,  "=NoteDisposisi=" =>$data_memo->note);
	foreach($arr as $nama => $value){
		if(strpos($layout, $nama) !== false) {
			$Rlayout = str_replace($nama, $value, $layout);
			$layout = $Rlayout;
		}
	}?>
	<html>
		<head>
			<meta http-equiv="Content-Language" content="en-us">
			<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
			<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
			<meta name="ProgId" content="FrontPage.Editor.Document">
			
		</head>
		<body><?php
			if($dataKopSet->status == "Y"){
				if($dataKopSet->kopdefault == "Y"){?>
					<p style="text-align:center;"><img src="./<?php echo "foto/$kop";?>" width="795"></p><?php
				}
				echo $Rlayout;
			}else{
				if($dataKopSet->kopdefault == "Y"){?>
					<p style="text-align:center;"><img src="./<?php echo "foto/$kop";?>" width="795"></p><?php
				}?>
				<div id="container">
					<div id="row">
						<h3 style="text-align:center;">DISPOSISI SURAT</h3>
						<table width=700 border="1" cellspacing="0" cellpadding="0" style='border-collapse:collapse;' align="center">
							<tr align=left>
								<td nowrap style="padding: 5px; vertical-align: top;">Surat Dari</td>
								<td nowrap style="padding: 5px; vertical-align: top; width:250"><?php echo $data_memo->pengirim;?></td>
								<td nowrap style="padding: 5px; vertical-align: top;">Diterima Tanggal </td>
								<td nowrap style="padding: 5px; vertical-align: top; width:225"><?php echo tgl_indo($data_memo->tgl_terima);?></td>
							</tr>
							<tr align=left>
								<td nowrap style="padding: 5px; vertical-align: top;">Tanggal Surat</td>
								<td style="padding: 5px; vertical-align: top;"><?php echo tgl_indo($data_memo->tgl_surat);?></td>
								<td nowrap style="padding: 5px; vertical-align: top;">Nomor Agenda</td>
								<td nowrap style="padding: 5px; vertical-align: top;" ><?php echo $data_memo->custom_noagenda;?></td>
							</tr> 
							<tr align=left>
								<td nowrap style="padding: 5px; vertical-align: top;">Nomor Surat </td>
								<td style="padding: 5px;"><?php echo $data_memo->no_sm;?></td>
								<td nowrap style="padding: 5px; vertical-align: top;">Disposisi dari/ke </td>
								<td nowrap style="padding: 5px; vertical-align: top;"><?php echo "<b>".$DataUser->nama ."</b> ke:<br/>".$TargetDisposisi;?></td>
							</tr>
							<tr align=left>
								<td nowrap style="padding: 5px; vertical-align: top;">Tujuan Surat</td>
								<td nowrap style="padding: 5px; vertical-align: top;" ><?php echo $TujuanSurat;?></td>
								<td nowrap style="padding: 5px; vertical-align: top;">Tgl Disposisi</td>
								<td nowrap style="padding: 5px; vertical-align: top;" ><?php echo tgl_indo($data_memo->tgl);?></td>
							</tr>
							<tr align=left height="100">
								<td nowrap style="padding: 5px; vertical-align: top;">Perihal </td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $data_memo->perihal;?></td>
								<td nowrap style="padding: 5px; vertical-align: top;">Ket </td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $data_memo->ket;?></td>
							</tr>
						</table>
						<p></p>
						<table width=700 border="1" cellspacing="0" cellpadding="0" style='border-collapse:collapse;' align="center">
							<tr align=left>
								<td nowrap width="350" style="padding: 5px;">
									Tembusan:
								</td>
								<td width="350" nowrap style="padding: 5px;">
									Isi Disposisi:
								</td>
							</tr>
							<tr>
								<td style="padding: 5px;"><?php
									echo $DataTembusanVer;?>
								</td>
								<td style="padding: 5px; vertical-align: top;">
									<?php echo $data_memo->note;?>
								</td>
							</tr>
						</table>
					</div>
				</div><?php
			}?>
		</body>
	</html><?php
}else{
	echo "Belum ada data";	
}
/*Cetak Direct PDF*/
if(isset($_GET['act']) AND $_GET['act'] == "pdf"){
	$filename="DisposisiLetter-".$data_memo->id_sm.".pdf"; //ubah untuk menentukan nama file pdf yang dihasilkan nantinya
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
}