<?php
$id_sm = htmlspecialchars($purifier->purify(trim($_GET['smid'])), ENT_QUOTES);
$field = array("a.id_user as userDis","a.*","b.*");
$params = array(':id_sm' => $id_sm);
$CekTembusan = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params, "a.id_sm=:id_sm", "AND a.tembusan LIKE '%\"$_SESSION[id_user]\"%'");
if($CekTembusan->rowCount() >= 1){
	$dataTembusan = $CekTembusan->fetch(PDO::FETCH_OBJ);
	$tgl = substr($dataTembusan->tgl,0,10);
	$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$dataTembusan->userDis'");
	$DataUser = $CekUser->fetch(PDO::FETCH_OBJ);
	$params = array(':id_user' => $_SESSION['id_user'], ':id_sm' => $dataTembusan->id_sm, ':kode' => 'CC');
	$lihat_sm = $this->model->selectprepare("surat_read", $field=null, $params, "id_sm=:id_sm AND id_user=:id_user AND kode=:kode");
	if($lihat_sm->rowCount() <= 0){
		$field = array('id_user' => $_SESSION['id_user'], 'id_sm' => $dataTembusan->id_sm, 'kode' => 'CC');
		$insert2 = $this->model->insertprepare("surat_read", $field, $params);
	}?>
	<div class="widget-box">
		<div class="message-header clearfix">
			<div class="pull-left" style="padding:0 9px;">
				<span class="blue bigger-125"> Tembusan surat dari: <?php echo $dataTembusan->pengirim;?>, Ref: <?php echo $dataTembusan->custom_noagenda;?></span>
				<div class="space-4"></div>
				<img class="middle" alt="<?php echo $DataUser->nama;?>" src="assets/images/avatars/<?php echo $DataUser->picture;?>" width="32" />
				<a href="#" class="sender"><?php echo $DataUser->nama;?></a>
				<i class="ace-icon fa fa-clock-o bigger-110 orange middle"></i>
				<span class="time grey"><?php echo tgl_indo($tgl);?>, <?php echo substr($dataTembusan->tgl,-9,-3);?> WIB</span>
			</div>
		</div>
		<div class="hr hr-double"></div>
		<div class="message-body">
			<p>
				Tgl terima/No agenda: <br/><b><?php echo tgl_indo($dataTembusan->tgl_terima);?> | <?php echo $dataTembusan->custom_noagenda;?></b>
			</p>
			<p>
				Dari: <br/><b><?php echo $dataTembusan->pengirim;?></b>
			</p>
			<p>
				Tgl/No surat: <br/><b><?php echo tgl_indo($dataTembusan->tgl_surat);?> | <?php echo $dataTembusan->no_sm;?></b>
			</p>
			<p>
				Perihal: <br/><b><?php echo $dataTembusan->perihal;?></b>
			</p>
			<p>
				Tanggal Tembusan : <br/><b><?php echo tgl_indo($tgl);?></b>
			</p>
			<p>
				<a href="./berkas/<?php echo $dataTembusan->file;?>" target="_blank"><button class="btn btn-success btn-minier ">Lihat File Surat<i class="ace-icon fa fa-book align-top bigger-125 icon-on-right"></i></button></a>
			</p>
			<p>
				<a href="./index.php?op=memoprint&memoid=<?php echo $dataTembusan->id_sm;?>" target="_blank"><button class="btn btn-primary btn-minier ">Detail Surat <i class="ace-icon fa fa-external-link align-center bigger-100 icon-on-right"></i></button></a>
			</p>
			<?php
			$params = array(':id_sm' => $dataTembusan->id_sm);
			$StatSurat = $this->model->selectprepare("status_surat a join user b on a.id_user=b.id_user", $field=null, $params, "a.id_sm=:id_sm", "ORDER BY a.id_status DESC");
			if($StatSurat->rowCount() >= 1){?>
				<div class="widget-body">
					<div class="widget-main padding-0">
						<div id="profile-feed-1" class="profile-feed">
						
						</div>
					</div>
				</div><?php
				$no=1;
				while($dataStatSurat= $StatSurat->fetch(PDO::FETCH_OBJ)){?>
					<div class="profile-activity clearfix"><?php
						if($no == 1){?>
							<b>PROGRES SURAT</b><?php
						}
						if($dataStatSurat->statsurat == 1){
							$statusSirat = "Sedang diproses";
						}elseif($dataStatSurat->statsurat == 2){
							$statusSirat = "Selesai";
						}elseif($dataStatSurat->statsurat == 0){
							$statusSirat = "Dibatalkan";
						}?>
						<div>
							<a class="user" href="#"><?php echo $dataStatSurat->nama; ?></a>
							update status surat : <span class="text-primary"><?php echo $statusSirat; ?></span><?php
							if($dataStatSurat->ket !==''){?>
								<?php echo '('.$dataStatSurat->ket .')';;
							}?>
							<div class="time">
								<?php echo tgl_indo1($dataStatSurat->created);?>, <?php echo substr($dataStatSurat->created,-9,-3);?> WIB
							</div>
						</div><?php
						if($dataStatSurat->id_user == $_SESSION['id_user']){?>
							<div class="tools action-buttons">
								<a href="./index.php?op=memo&memoid=<?php echo $data_memo->id_sm;?>&act=progres&id=<?php echo $dataStatSurat->id_status;?>" class="blue">
									<i class="ace-icon fa fa-pencil bigger-125"></i>
								</a>
								<a href="./index.php?op=memo&memoid=<?php echo $data_memo->id_sm;?>&act=progres&id=<?php echo $dataStatSurat->id_status;?>&do=delete" class="red">
									<i class="ace-icon fa fa-times bigger-125"></i>
								</a>
							</div><?php
						}?>
					</div>	
					<?php
				$no++;
				}
			}?>
		</div>

	</div><?php
}else{
	echo "Belum ada data";	
}
?>