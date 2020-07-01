<?php
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$wherekCon = '';
if((isset($_GET['start']) && $_GET['start'] != '') && (isset($_GET['to']) && $_GET['to'] != '')){
	$start = htmlspecialchars($purifier->purify(trim($_GET['start'])), ENT_QUOTES);
	$to = htmlspecialchars($purifier->purify(trim($_GET['to'])), ENT_QUOTES);
	$wherekCon .= "WHERE a.tgl between '$start' AND '$to'";
}else{
	$start = $to = '';
}
if(isset($_GET['user']) && $_GET['user'] != ''){
	$user = htmlspecialchars($purifier->purify(trim($_GET['user'])), ENT_QUOTES);
	$TampilUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$user'")->fetch(PDO::FETCH_OBJ);
	$namaUser = $TampilUser->nama;
	if($wherekCon == ''){
		$wherekCon .= "WHERE a.disposisi LIKE '%\"$user\"%'";
	}else{
		$wherekCon .= " AND a.disposisi LIKE '%\"$user\"%'";
	}
}else{
	$user = $namaUser = '';
}
$field = array("a.id_user as iduser_dis","a.*","b.*");
$memo = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm join user c on a.id_user=c.id_user", $field, $params=null, $where=null, "$wherekCon order by a.tgl DESC");
if($memo->rowCount() >= 1){
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
	while($data_memo = $memo->fetch(PDO::FETCH_OBJ)){
		$dump_sm[]=$data_memo;
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
						Data Disposisi surat <?php if($namaUser != ''){?> ke : <b><?php echo $namaUser;?></b><?php }
						if($start != '' && $to !=''){?><br/>
							<b><?php echo tgl_indo($start);?></b> s/d <b><?php echo tgl_indo($to);?></b><?php
						}?>
					</h4>
					<table  width="100%" border="1" cellspacing="0" cellpadding="0" style='border-collapse:collapse;' align="center">
						<tr>
							<th style="padding: 5px; vertical-align: top;">No</th>
							<th style="padding: 5px; vertical-align: top;">Tgl Disposisi</th>
							<th style="padding: 5px; vertical-align: top;">Tujuan Disposisi</th>
							<th style="padding: 5px; vertical-align: top;">Isi Disposisi</th>
							<th style="padding: 5px; vertical-align: top;">Didisposisi oleh</th>
							<th style="padding: 5px; vertical-align: top;">No Agenda</th>
							<th style="padding: 5px; vertical-align: top;">No Surat</th>
							<th style="padding: 5px; vertical-align: top;">Perihal</th>
							<th style="padding: 5px; vertical-align: top;">Sumber Surat</th>
						</tr><?php
						$no=1;
						foreach($dump_sm as $key => $object){
							$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$object->iduser_dis'");
							$DataUser = $CekUser->fetch(PDO::FETCH_OBJ);
							$tglsurat = explode("-", $object->tgl_surat);
							$tglsurat = $tglsurat[2]."/".$tglsurat[1]."/".$tglsurat[0];
							$tgltrm = explode("-", $object->tgl_terima);
							$tgltrm = $tgltrm[2]."/".$tgltrm[1]."/".$tgltrm[0];
							$tgl_memo = substr($object->tgl,0,10);
							$tgl_memo = explode("-", $tgl_memo);
							$tgl_memo = $tgl_memo[2]."/".$tgl_memo[1]."/".$tgl_memo[0];
							$targetDis = json_decode($object->disposisi, true);?>
							<tr>
								<td style="padding: 5px; vertical-align: top;"><?php echo $no;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $tgl_memo;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php
									foreach($targetDis as $field => $value){
										$CekUserDis = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$value'")->fetch(PDO::FETCH_OBJ);
										echo "-".$CekUserDis->nama ."<br/>";
									}?>
								</td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->note;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $DataUser->nama;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->custom_noagenda;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->no_sm;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->perihal;?></td>
								<td style="padding: 5px; vertical-align: top;"><?php echo $object->pengirim;?></td>
							</tr><?php
						$no++;
						}?>
					</table>
				</div>
			</div>
		</body>
	</html><?php
}else{
	echo "Belum ada data $wherekCon";	
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