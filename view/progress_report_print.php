<?php
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$start = htmlspecialchars($purifier->purify(trim($_GET['start'])), ENT_QUOTES);
$to = htmlspecialchars($purifier->purify(trim($_GET['to'])), ENT_QUOTES);
$noagenda = htmlspecialchars($purifier->purify(trim($_GET['noagenda'])), ENT_QUOTES);
$wherekCon = '';
if(isset($start) AND $start !='' AND isset($to) AND $to !=''){
	$wherekCon .= "WHERE tgl_surat between '$start' AND '$to'";
}
if(isset($noagenda) AND $noagenda !=''){
	if(isset($start) AND $start !='' AND isset($to) AND $to !=''){
		$wherekCon .= "AND custom_noagenda = '$noagenda' OR no_sm = '$noagenda'";
	}else{
		$wherekCon .= " WHERE custom_noagenda = '$noagenda' OR no_sm = '$noagenda'";
	}
}
$ArsipSM = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, "$wherekCon order by tgl_terima ASC");
if($ArsipSM->rowCount() >= 1){
	while($dataArsipSM = $ArsipSM->fetch(PDO::FETCH_OBJ)){
		$dump_sm[]=$dataArsipSM;
	}
	$kopSet = $this->model->selectprepare(" pengaturan", $field=null, $params=null, $where=null, "WHERE id='1'");
	$dataKopSet= $kopSet->fetch(PDO::FETCH_OBJ);?>
	<html>
		<head>
			<meta http-equiv="Content-Language" content="en-us">
			<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
			<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
			<meta name="ProgId" content="FrontPage.Editor.Document">
			
		</head>
		<body onload="window.print()">
			<p style="text-align:center;"><img src="./<?php echo "foto/$dataKopSet->logo";?>" width="795"></p>
			<div id="container">
				<div id="row">
					<h3 style="text-align:center;">Laporan Progress Surat<?php
					if(isset($start) AND $start !='' AND isset($to) AND $to !=''){?><br/>
						<b><?php echo tgl_indo($start);?></b> s/d <b><?php echo tgl_indo($to);?></b><?php
					}?></h3>
					<table width="100%" border="1" cellspacing="3" cellpadding="3" style='border-collapse:collapse;' align="center">
						<tr align=left>
							<td nowrap style="padding: 5px; vertical-align: top;">No</td>
							<td nowrap style="padding: 5px; vertical-align: top;">No Agenda</td>
							<td nowrap style="padding: 5px; vertical-align: top;">No Surat</td>
							<td nowrap style="padding: 5px; vertical-align: top;" width="200">Perihal</td>
							<td nowrap style="padding: 5px; vertical-align: top;" width="140">Sumber Surat</td>
							<td nowrap style="padding: 5px; vertical-align: top;" width="400">Progress</td>
						</tr><?php
						$no=1;
						foreach($dump_sm as $key => $object){
							$CekSM = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, "WHERE id_sm = '$object->id_sm'");
							if($CekSM->rowCount() >= 1){
								$tglsurat = explode("-", $object->tgl_surat);
								$tglsurat = $tglsurat[2]."/".$tglsurat[1]."/".$tglsurat[0];
								$tgltrm = explode("-", $object->tgl_terima);
								$tgltrm = $tgltrm[2]."/".$tgltrm[1]."/".$tgltrm[0];?>
								<tr>
									<td><?php echo $no;?></td>
									<td><?php echo $object->custom_noagenda;?></td>
									<td><?php echo $object->no_sm;?></td>
									<td><?php echo $object->perihal;?></td>
									<td><?php echo $object->pengirim;?></td>
									<td><?php
										$CekProgress = $this->model->selectprepare("status_surat", $field=null, $params=null, $where=null, "WHERE id_sm = '$object->id_sm' order by statsurat ASC");
										while($dataCekProgress = $CekProgress->fetch(PDO::FETCH_OBJ)){
											$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$dataCekProgress->id_user'")->fetch(PDO::FETCH_OBJ);
											if($dataCekProgress->statsurat == 1){
												$statusSirat = "Sedang diproses";
											}elseif($dataCekProgress->statsurat == 2){
												$statusSirat = "Selesai";
											}elseif($dataCekProgress->statsurat == 0){
												$statusSirat = "Dibatalkan";
											}?>
											<ul><li><b><?php echo $CekUser->nama;?></b> status : <b><?php echo $statusSirat;?></b> <br/><?php echo $dataCekProgress->ket;?><br/><?php echo tgl_indo1($dataCekProgress->created);?>, <?php echo substr($dataCekProgress->created,-9,-3);?> WIB</ul></li><?php
										}?>
									</td>
								</tr><?php
							}
						$no++;
						}?>
					</table>
				</div>
			</div>
		</body>
	</html><?php
}else{?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<p>
			<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
			Data tidak ditemukan. Terimakasih.
		</p>
		<p>
			<a href="./index.php?op=report_disposisi"><button class="btn btn-minier btn-danger">Kembali</button></a>
		</p>
	</div><?php
}?>