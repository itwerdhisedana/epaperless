<?php
$id_sm = htmlspecialchars($purifier->purify(trim($_GET['smid'])), ENT_QUOTES);
$id_user = htmlspecialchars($purifier->purify(trim($_GET['id_user'])), ENT_QUOTES);
$field = array("a.id_user as userDis","a.*","b.*");
$params = array(':id_sm' => $id_sm, ':id_user' => $id_user);
$CekDisposisi = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params, "a.id_sm=:id_sm AND a.id_user=:id_user", "AND a.disposisi LIKE '%\"$_SESSION[id_user]\"%'");
$CekLevelDispo = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$_SESSION[id_user]'")->fetch(PDO::FETCH_OBJ);
if($CekDisposisi->rowCount() >= 1){
	$data_Disposisi = $CekDisposisi->fetch(PDO::FETCH_OBJ);
	$tgl_disposisi = substr($data_Disposisi->tgl,0,10);
	$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$data_Disposisi->userDis'");
	$DataUser = $CekUser->fetch(PDO::FETCH_OBJ);
	$params = array(':id_user' => $_SESSION['id_user'], ':id_sm' => $data_Disposisi->id_sm, ':kode' => 'DIS');
	$lihat_sm = $this->model->selectprepare("surat_read", $field=null, $params, "id_sm=:id_sm AND id_user=:id_user AND kode=:kode");
	if($lihat_sm->rowCount() <= 0){
		$field = array('id_user' => $_SESSION['id_user'], 'id_sm' => $data_Disposisi->id_sm, 'kode' => 'DIS');
		$insert2 = $this->model->insertprepare("surat_read", $field, $params);
	}
	
	$ListUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "WHERE a.id_user != '$_SESSION[id_user]' ORDER BY a.nama ASC");
	while($dataListUser = $ListUser->fetch(PDO::FETCH_OBJ)){
		$dumpListUser[] = $dataListUser;
	}
	if(isset($CekLevelDispo->rule_disposisi) == '' OR $CekLevelDispo->rule_disposisi == "null"){
		$dummy_arr = '[""]';
		$RuleDisposisi = json_decode($dummy_arr, true);
	}else{
		$RuleDisposisi = json_decode($CekLevelDispo->rule_disposisi, true);
	}
	
	$params = array(':id_user' => $_SESSION['id_user'], ':id_sm' => $data_Disposisi->id_sm);
	$cekDisposisiku = $this->model->selectprepare("memo", $field=null, $params, "id_user=:id_user AND id_sm=:id_sm");
	$dataDispoKu= $cekDisposisiku->fetch(PDO::FETCH_OBJ);?>
	<div class="widget-box">
		<div class="message-header clearfix">
			<div class="pull-left" style="padding:0 9px;">
				<span class="blue bigger-125"> Disposisi surat : <?php echo $data_Disposisi->perihal;?></span>
				<div class="space-4"></div>
				<img class="middle" alt="<?php echo $DataUser->nama;?>" src="assets/images/avatars/<?php echo $DataUser->picture;?>" width="32" />
				<a href="#" class="sender"><?php echo $DataUser->nama;?></a>
				<i class="ace-icon fa fa-clock-o bigger-110 orange middle"></i>
				<span class="time grey"><?php echo tgl_indo($tgl_disposisi);?>, <?php echo substr($data_Disposisi->tgl,-9,-3);?> WIB</span>
			</div>
		</div>
		<div class="hr hr-double"></div>
		<div class="message-body">
			<p>
				Tgl terima/No agenda: <br/><b><?php echo tgl_indo($data_Disposisi->tgl_terima);?> | <?php echo $data_Disposisi->custom_noagenda;?></b>
			</p>
			<p>
				Dari: <br/><b><?php echo $data_Disposisi->pengirim;?></b>
			</p>
			<p>
				Tgl/No surat: <br/><b><?php echo tgl_indo($data_Disposisi->tgl_surat);?> | <?php echo $data_Disposisi->no_sm;?></b>
			</p>
			<p>
				Perihal: <br/><b><?php echo $data_Disposisi->perihal;?></b>
			</p>
			<p>
				Keterangan: <br/><b><?php echo $data_Disposisi->ket;?></b>
			</p>
			<p>Detail Disposisi: <br/>
				<span class="label label-xs label-primary label-white middle">
					<a href="./index.php?op=disposisiprint&smid=<?php echo $data_Disposisi->id_sm;?>&iduser=<?php echo $_SESSION['id_user']; ?>&dispo=<?php echo $DataUser->id_user;?>" target="_blank"><b>Lihat</b></a>
				</span>
				<span class="label label-xs label-danger label-white middle">
					<a href="./index.php?op=disposisiprint&smid=<?php echo $data_Disposisi->id_sm;?>&iduser=<?php echo $_SESSION['id_user']; ?>&dispo=<?php echo $DataUser->id_user;?>&act=pdf" target="_blank"><b>Cetak</b> <i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
				</span>
			</p>
			
			<hr/>
			<p>
				<a href="./index.php?op=disposisi&smid=<?php echo $data_Disposisi->id_sm;?>&id_user=<?php echo $DataUser->id_user;?>&act=progres"><button class="btn btn-primary btn-minier ">ENTRI PROGRESS SURAT <i class="ace-icon fa fa-arrow-right align-center bigger-100 icon-on-right"></i></button></a>
			</p><?php
			$params = array(':id_sm' => $data_Disposisi->id_sm);
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
								<?php echo '('.$dataStatSurat->ket .')';
								if($dataStatSurat->file_progress != ''){ ?>							
									&nbsp;<a href="./berkas/<?php echo $dataStatSurat->file_progress;?>" title="Dokumen Pendukung" target="_blank"><i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a> <?php
								}
							}?>
							<div class="time">
								<?php echo tgl_indo1($dataStatSurat->created);?>, <?php echo substr($dataStatSurat->created,-9,-3);?> WIB
							</div>
						</div><?php
						if($dataStatSurat->id_user == $_SESSION['id_user']){?>
							<div class="tools action-buttons">
								<a href="./index.php?op=disposisi&smid=<?php echo $data_Disposisi->id_sm;?>&id_user=<?php echo $_GET['id_user'];?>&act=progres&id=<?php echo $dataStatSurat->id_status;?>" class="blue">
									<i class="ace-icon fa fa-pencil bigger-125"></i>
								</a>
								<a href="./index.php?op=disposisi&smid=<?php echo $data_Disposisi->id_sm;?>&id_user=<?php echo $_GET['id_user'];?>&act=progres&id=<?php echo $dataStatSurat->id_status;?>&do=delete" class="red">
									<i class="ace-icon fa fa-times bigger-125"></i>
								</a>
							</div><?php
						}?>
					</div>	
					<?php
				$no++;
				}
			}			
			if(isset($_GET['act']) && $_GET['act'] == "progres"){
				if($_SERVER["REQUEST_METHOD"] == "POST"){
					$tgl = date("Y-m-d H:i:s", time());
					$progres = htmlspecialchars($purifier->purify(trim($_POST['progres'])), ENT_QUOTES);
					$ket = htmlspecialchars($purifier->purify(trim($_POST['ket'])), ENT_QUOTES);
					$file_progres_old = htmlspecialchars($purifier->purify(trim($_POST['file_progres_old'])), ENT_QUOTES);
					$fileName = htmlspecialchars($_FILES['file_progres']['name'], ENT_QUOTES);
					$tipefile = pathinfo($fileName,PATHINFO_EXTENSION);
					$extensionList = array("pdf","jpg","jpeg","png","PNG", "JPG", "JPEG","PDF");
					$namaDir = 'berkas/';
					$file_progress = $namaDir."Progress"."_".$data_Disposisi->id_sm."_".$_SESSION['id_user']."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
					if(empty($fileName)){
						$filedb = "";
					}else{
						$filedb = "Progress"."_".$data_Disposisi->id_sm."_".$_SESSION['id_user']."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
						if(in_array($tipefile, $extensionList)){
							move_uploaded_file($_FILES['file_progres']['tmp_name'], $file_progress);
							$field1 = array('file_progress' => $filedb);
							$params1 = array(':file_progress' => $filedb);
						}else{
							echo "<script type=\"text/javascript\">alert('File gagal di Upload, Format file tidak di dukung!!!');window.history.go(-1);</script>";
						}
					}

					if(isset($_GET['id'])){
						$id = htmlspecialchars($purifier->purify(trim($_GET['id'])), ENT_QUOTES);
						$params2 = array(':id_status' => $id, ':id_sm' => $data_Disposisi->id_sm, ':id_user' => $_SESSION['id_user']);
						if(isset($params1)){
							@unlink($namaDir.$file_progres_old);
							$params = array_merge($params2,$params1);
						}
						$CekStatSurat = $this->model->selectprepare("status_surat", $field=null, $params2, "id_status=:id_status AND id_sm=:id_sm AND id_user=:id_user");
						if($CekStatSurat->rowCount() >= 1){
							$field = array('statsurat' => $progres, 'ket' => $ket);
							if(isset($field1)){
								$field = array_merge($field,$field1);
							}
							//$params = array(':id_sm' => $data_Disposisi->id_sm, ':statsurat' => $progres, ':id_user' => $_SESSION['id_user'], ':ket' => $ket, ':created' => $tgl);
							$update = $this->model->updateprepare("status_surat", $field, $params2, "id_status=:id_status AND id_sm=:id_sm AND id_user=:id_user");
							if($update){
								echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=disposisi&smid=$data_Disposisi->id_sm&id_user=$_GET[id_user]\";</script>";
							}else{
								die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
							}
						}
					}else{							
						$field = array('id_sm' => $data_Disposisi->id_sm, 'statsurat' => $progres, 'id_user' => $_SESSION['id_user'], 'ket' => $ket, 'created' => $tgl);
						if(isset($field1)){
							$field = array_merge($field,$field1);
						}
						$params = array(':id_sm' => $data_Disposisi->id_sm, ':statsurat' => $progres, ':id_user' => $_SESSION['id_user'], ':ket' => $ket, ':created' => $tgl);
						if(isset($params1)){
							$params = array_merge($params,$params1);
						}
						$insert = $this->model->insertprepare("status_surat", $field, $params);
						if($insert){
							echo "<script type=\"text/javascript\">alert('Data Berhasil disimpan...!!');window.location.href=\"./index.php?op=disposisi&smid=$data_Disposisi->id_sm&id_user=$_GET[id_user]\";</script>";
						}else{
							die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
						}
					}
					//print_r($_POST);
				}else{
					if(isset($_GET['id'])){
						$id = htmlspecialchars($purifier->purify(trim($_GET['id'])), ENT_QUOTES);
						$params1 = array(':id_status' => $id, ':id_sm' => $data_Disposisi->id_sm, ':id_user' => $_SESSION['id_user']);
						$CekStatSurat = $this->model->selectprepare("status_surat", $field=null, $params1, "id_status=:id_status AND id_sm=:id_sm AND id_user=:id_user");
						if($CekStatSurat->rowCount() >= 1){
							$dataCekStatSurat= $CekStatSurat->fetch(PDO::FETCH_OBJ);
							$DbStatProgres = $dataCekStatSurat->statsurat;
							$dbKetProgres =  $dataCekStatSurat->ket;
							$db_file_progress =  $dataCekStatSurat->file_progress;
						}
						$title = "EDIT PROGRES SURAT";
					}else{
						$title = "ISI PROGRES SURAT";
					}
					if(isset($_GET['id']) AND isset($_GET['do']) AND $_GET['do'] == "delete"){
						$id = htmlspecialchars($purifier->purify(trim($_GET['id'])), ENT_QUOTES);
						$params1 = array(':id_status' => $id, ':id_sm' => $data_Disposisi->id_sm, ':id_user' => $_SESSION['id_user']);
						$CekStatSurat = $this->model->selectprepare("status_surat", $field=null, $params1, "id_status=:id_status AND id_sm=:id_sm AND id_user=:id_user");
						if($CekStatSurat->rowCount() >= 1){
							$params1 = array(':id_sm' => $data_Disposisi->id_sm, ':statsurat' => 2);
							$CekStatFinish = $this->model->selectprepare("status_surat", $field=null, $params1, "id_sm=:id_sm AND statsurat=:statsurat");
								/* if($CekStatFinish->rowCount() >= 1){?>
									<div class="alert alert-danger">
										<button type="button" class="close" data-dismiss="alert">
											<i class="ace-icon fa fa-times"></i>
										</button>
										<p>
											<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
											Anda tidak dapat menghapus progress surat karena progress surat sudah bersatus selesai.
										</p>
									</div><?php									
								}else{ */
								$params3 = array(':id_status' => $id);
								$delete = $this->model->hapusprepare("status_surat", $params3, "id_status=:id_status");
								if($delete){
									echo "<script type=\"text/javascript\">alert('Data Berhasil dihapus...!!');window.location.href=\"./index.php?op=disposisi&smid=$data_Disposisi->id_sm&id_user=$_GET[id_user]\";</script>";
								}else{
									die("<script>alert('Data gagal dihapus, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
								}
								//}
						}else{?>
							<div class="alert alert-danger">
								<button type="button" class="close" data-dismiss="alert">
									<i class="ace-icon fa fa-times"></i>
								</button>
								<p>
									<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
									Anda hanya dibolehkan menghapus progress yang anda entri
								</p>
							</div><?php
						}
					}else{?>
						<hr/>
						<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
							<label for="form-field-1"> <b><?php echo $title;?></b></label> <?php
							if(isset($db_file_progress) && $db_file_progress != ''){ ?>
								<input type="hidden" name="file_progres_old" value="<?php echo $db_file_progress;?>"/> <?php
							} ?>							
							<div class="form-group">
								<div class="col-sm-4">
									<select class="form-control" id="form-field-select-3" name="progres" data-placeholder="Pilih Progres..." required>
										<option value="">Pilih Progres...</option><?php
										$ArrStatProgres = array("Sedang diproses" => 1, "Selesai" => 2, "Dibatalkan" => 0);
										foreach($ArrStatProgres as $field => $value){
											if(isset($DbStatProgres) AND $DbStatProgres == $value){?>
												<option value="<?php echo $value;?>" selected><?php echo $field;?></option><?php
											}else{?>
												<option value="<?php echo $value;?>"><?php echo $field;?> </option><?php
											}
										}?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<textarea class="form-control limited" placeholder="Keterangan progress" name="ket" id="form-field-9" maxlength="150" required><?php if(isset($dbKetProgres)){ echo $dbKetProgres;}?></textarea>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<input class="form-control" type="file" name="file_progres" id="id-input-file-1" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-2">
									<button class="btn btn-white btn-info btn-bold">
										Submit <i class="ace-icon fa fa-arrow-right icon-on-right"></i>
									</button>
								</div>
							</div>
						</form>
						<hr/><?php
					}
				}
			}
			/*End Feedback Surat*/
			?>
			<p></p>
			<p><b>RIWAYAT DISPOSISI</b></p>
			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th width="200">Disposisi dari</th>
						<th width="200">Tujuan Disposisi</th>
						<th>Catatan</th>
						<th width="200">Waktu</th>
					</tr>
				</thead>
				<tbody><?php
						$params = array(':id_sm' => $data_Disposisi->id_sm);
						$CekDispoLevel = $this->model->selectprepare("memo a join user b on a.id_user=b.id_user", $field=null, $params, "a.id_sm=:id_sm", "ORDER BY a.tgl ASC");
						if($CekDispoLevel->rowCount() >= 1){
							while($DataCekDispoLevel = $CekDispoLevel->fetch(PDO::FETCH_OBJ)){
								$ListDisposisi2 = json_decode($DataCekDispoLevel->disposisi, true);
								$tgl_dispolevel = substr($DataCekDispoLevel->tgl,0,10);?>
								<tr>
									<td><?php echo $DataCekDispoLevel->nama;?></td>
									<td><?php
										foreach($ListDisposisi2 as $listdispo){
											$TampilUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user='$listdispo'")->fetch(PDO::FETCH_OBJ);
											echo "- ".$TampilUser->nama ."<br/>";
										}?>
									</td>
									<td><?php echo $DataCekDispoLevel->note;?></td>
									<td><?php echo tgl_indo($tgl_dispolevel);?>, <?php echo substr($DataCekDispoLevel->tgl,-9,-3);?> WIB</td>
								</tr><?php							
							}							
						}?>
				</tbody>
			</table>
			
			<?php
			if($CekLevelDispo->rule_disposisi != 'null'){
				if(isset($_GET['act']) && $_GET['act'] == "disposisi"){		
					if(isset($dataDispoKu->tembusan) == '' OR $dataDispoKu->tembusan == "null"){
						$dummy_arr = '[""]';
						$cekTembusan = json_decode($dummy_arr, true);
					}else{
						$cekTembusan = json_decode($dataDispoKu->tembusan, true);
					}
					if($_SERVER["REQUEST_METHOD"] == "POST"){
						$tgl = date("Y-m-d H:i:s", time());
						$tgl_memo = substr($tgl,0,10);
						$note = htmlspecialchars($purifier->purify(trim($_POST['note'])), ENT_QUOTES);
						$tujuan = json_encode($_POST['tujuan']);
						$tembusan = json_encode($_POST['tembusan']);
						if($cekDisposisiku->rowCount() >= 1){
							$field = array('disposisi' => $tujuan, 'note' => $note, 'tembusan' => $tembusan);
							$params = array(':id_status' => $dataDispoKu->id_status);
							$update = $this->model->updateprepare("memo", $field, $params, "id_status=:id_status");
							if($update){
								echo "<script type=\"text/javascript\">alert('Data Disposisi berhasil diperbaharui...!!');window.location.href=\"$_SESSION[url]\";</script>";
							}else{
								die("<script>alert('Gagal memperbaharui data, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
							}
						}else{
							$field = array('id_user' => $_SESSION['id_user'], 'id_sm' => $data_Disposisi->id_sm, 'disposisi' => $tujuan, 'note' => $note, 'tembusan' => $tembusan, 'tgl' => $tgl);
							$params = array(':id_user' => $_SESSION['id_user'], ':id_sm' => $data_Disposisi->id_sm, ':disposisi' => $tujuan, ':note'=>$note, ':tgl' => $tgl);
							$insert = $this->model->insertprepare("memo", $field, $params);
							if($insert){
								//Kirim Email
								$EmailAccount = $this->model->selectprepare("pengaturan", $field=null, $params=null, $where=null, "WHERE id='1' AND email !='' AND pass_email !=''");
								$AktifEmail = $this->model->selectprepare("email_setting", $field=null, $params=null, $where=null, "WHERE id_kop='3' AND status='Y'");
								
								$AktifEmai2 = $this->model->selectprepare("email_setting", $field=null, $params=null, $where=null, "WHERE id_kop='1' AND status='Y'");
								if($EmailAccount->rowCount() >= 1 AND $AktifEmail->rowCount() >= 1){
									$dataEmailAccount = $EmailAccount->fetch(PDO::FETCH_OBJ);
									$dataAktifEmail = $AktifEmail->fetch(PDO::FETCH_OBJ);
									$dataAktifEmail2 = $AktifEmai2->fetch(PDO::FETCH_OBJ);
									
									$TujuanSurat = "";
									$TargetDisposisi = "";
									$DataTembusanVer = "";
									$DataTembusanHor = "";
									if($tujuan != ''){
										$ListUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
										while($dataListUser = $ListUser->fetch(PDO::FETCH_OBJ)){
											if(is_array(json_decode($data_Disposisi->tujuan_surat))){
												if(false !== array_search($dataListUser->id_user, json_decode($data_Disposisi->tujuan_surat, true))){
													$TujuanSurat .= '- '.$dataListUser->nama .' ('.$dataListUser->nama_jabatan .')<br/>';
												}
											}
											if(is_array($_POST['tujuan'])){
												if(false !== array_search($dataListUser->id_user, json_decode($tujuan, true))){
													$TargetDisposisi .= '- '.$dataListUser->nama .' ('.$dataListUser->nama_jabatan .')<br/>';
												}
											}
											if(is_array($_POST['tembusan'])){
												if(false !== array_search($dataListUser->id_user, json_decode($tembusan, true))){
													$DataTembusanVer .= '- '.$dataListUser->nama .' ('.$dataListUser->nama_jabatan .')<br/>';
													$DataTembusanHor .='- '.$dataListUser->nama .' ('.$dataListUser->nama_jabatan .'), ';
												}
											}
										}
									}
						
									$isi = $dataAktifEmail->layout;
									$Rlayout = $isi;
									
									$isi2 = $dataAktifEmail2->layout;
									$Rlayout2 = $isi2;
									
									$arr = array("=NoAgenda=" => $data_Disposisi->custom_noagenda, "=NoSurat=" => $data_Disposisi->no_sm, "=Perihal=" => $data_Disposisi->perihal, "=Disposisi=" => $TargetDisposisi, "=TglSurat=" =>tgl_indo($data_Disposisi->tgl_surat), "=TglTerima=" => tgl_indo($data_Disposisi->tgl_terima), "=AsalSurat=" =>$data_Disposisi->pengirim, "=Keterangan=" => $data_Disposisi->ket, "=DisposisiOleh=" =>$_SESSION['nama'], "=NoteDisposisi=" => $note, "=TglDisposisi=" =>tgl_indo($tgl_memo), "=TembusanH=" => $DataTembusanHor, "=TembusanV=" => $DataTembusanVer, "=TujuanSurat=" => $TujuanSurat);
									foreach($arr as $nama => $value){
										if(strpos($isi, $nama) !== false) {
											$Rlayout = str_replace($nama, $value, $isi);
											$isi = $Rlayout;
										}
									}
									
									$arr2 = array("=NoAgenda=" => $data_Disposisi->custom_noagenda, "=NoSurat=" => $data_Disposisi->no_sm, "=Perihal=" => $data_Disposisi->perihal, "=Disposisi=" => $TargetDisposisi, "=TglSurat=" =>tgl_indo($data_Disposisi->tgl_surat), "=TglTerima=" => tgl_indo($data_Disposisi->tgl_terima), "=AsalSurat=" =>$data_Disposisi->pengirim, "=Keterangan=" => $data_Disposisi->ket, "=DisposisiOleh=" =>$_SESSION['nama'], "=NoteDisposisi=" => $note, "=TglDisposisi=" =>tgl_indo($tgl_memo), "=TembusanH=" => $DataTembusanHor, "=TembusanV=" => $DataTembusanVer, "=TujuanSurat=" => $TujuanSurat);
									foreach($arr2 as $nama2 => $value2){
										if(strpos($isi2, $nama2) !== false) {
											$Rlayout2 = str_replace($nama2, $value2, $isi2);
											$isi2 = $Rlayout2;
										}
									}
									//$CekTujuan = array_filter($tujuan);
									if(is_array($_POST['tujuan'])){
										$mail = new PHPMailer;
										$mail->isSMTP();
										$mail->SMTPDebug = 0;
										$mail->Debugoutput = 'html';
										$mail->Host = 'localhost';
										$mail->SMTPAuth = true;
										$mail->Username = $dataEmailAccount->email;
										$mail->Password = $dataEmailAccount->pass_email;
										
										$mail->From = $dataEmailAccount->email;
										$mail->FromName = $_SESSION['nama'];
										$mail->smtpConnect(
											array(
												"ssl" => array(
													"verify_peer" => false,
													"verify_peer_name" => false,
													"allow_self_signed" => true
												)
											)
										);
										$dataTujuan = json_decode($tujuan, true);
										foreach($dataTujuan as $id_tujuan){
											$params = array(':id_user' => $id_tujuan);
											$user_tujuan = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $other=null);
											$data_user_tujuan= $user_tujuan->fetch(PDO::FETCH_OBJ);
											if($data_user_tujuan->email != ''){
												$mail->AddAddress($data_user_tujuan->email, $data_user_tujuan->nama);
											}
										}
										$mail->isHTML(true);
										$topik = "Disposisi Surat : ".$data_Disposisi->perihal;
										$mail->Subject = $topik;
										$mail->Body = $isi;
										$mail->AltBody = $data_Disposisi->perihal;
										$lokasi = "berkas/$data_Disposisi->file";
										if(file_exists($lokasi)){
											$mail->addAttachment($lokasi);
										}
										if(!$mail->send()) {
											echo "Mailer Error: " . $mail->ErrorInfo;
											echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan. Email notifikasi gagal dikirim!');window.location.href=\"$_SESSION[url]\";</script>";
										}else{
											if(is_array($_POST['tembusan'])){
												$mail = new PHPMailer;
												$mail->isSMTP();
												$mail->SMTPDebug = 0;
												$mail->Debugoutput = 'html';
												$mail->Host = 'localhost';
												$mail->SMTPAuth = true;
												$mail->Username = $dataEmailAccount->email;
												$mail->Password = $dataEmailAccount->pass_email;
												
												$mail->From = $dataEmailAccount->email;
												$mail->FromName = $_SESSION['nama'];
												$mail->smtpConnect(
													array(
														"ssl" => array(
															"verify_peer" => false,
															"verify_peer_name" => false,
															"allow_self_signed" => true
														)
													)
												);
												$dataTembusan = json_decode($tembusan, true);
												foreach($dataTembusan as $id_tembusan){
													$params = array(':id_user' => $id_tembusan);
													$user_tujuan = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $other=null);
													$data_user_tujuan= $user_tujuan->fetch(PDO::FETCH_OBJ);
													if($data_user_tujuan->email != ''){
														$mail->AddAddress($data_user_tujuan->email, $data_user_tujuan->nama);
													}
												}
												$mail->isHTML(true);
												$topik = "Tembusan Surat: ".$data_Disposisi->perihal;
												$mail->Subject = $topik;
												$mail->Body = $isi2;
												$mail->AltBody = $data_Disposisi->perihal;
												$lokasi = "berkas/$data_Disposisi->file";
												if(file_exists($lokasi)){
													$mail->addAttachment($lokasi);
												}
												if(!$mail->send()) {
													//echo "Mailer Error: " . $mail->ErrorInfo;
													echo "<script type=\"text/javascript\">alert('Data Disposisi Berhasil diSimpan. Email notifikasi gagal dikirim!!');window.location.href=\"$_SESSION[url]\";</script>";
												}else{
													echo "<script type=\"text/javascript\">alert('Data Disposisi Berhasil diSimpan, Email notifikasi dikirim!');window.location.href=\"$_SESSION[url]\";</script>";
												}
											}else{
												echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan, Email notifikasi dikirim!');window.location.href=\"$_SESSION[url]\";</script>";
											}
										}
									}
								}
								echo "<script type=\"text/javascript\">alert('Data Disposisi berhasil disimpan...!!');window.location.href=\"$_SESSION[url]\";</script>";
							}else{
								die("<script>alert('Gagal menyimpan data disposisi, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
							}
						}
					}else{?>
						<div class="widget-box">
							<div class="widget-header">
								<h5 class="widget-title">Disposisi Surat</h5>
							</div>
							<div class="widget-body">
								<div class="widget-main">
									<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
										<div class="form-group">
											<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Disposisikan ke *</label>
											<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan pilihan user yang tersedia">?</span>
											<div class="col-sm-6">
												<select multiple="" class="chosen-select form-control" name="tujuan[]" id="form-field-select-3" data-placeholder="Pilih user..." required><?php
												foreach($dumpListUser as $key => $object){
													if(false !== array_search($object->id_user, $RuleDisposisi)){
														if($cekDisposisiku->rowCount() >= 1){
															if(isset($dataDispoKu->disposisi) == '' OR $dataDispoKu->disposisi == "null"){
																$dummy_arr = '[""]';
																$TujuanDisposisi = json_decode($dummy_arr, true);
															}else{
																$TujuanDisposisi = json_decode($dataDispoKu->disposisi, true);
															}
															if(false !== array_search($object->id_user, $TujuanDisposisi)){?>
																<option value="<?php echo $object->id_user;?>" selected><?php echo $object->nama;?></option><?php
															}else{?>
																<option value="<?php echo $object->id_user;?>"><?php echo $object->nama;?></option><?php															
															}
														}else{?>
															<option value="<?php echo $object->id_user;?>"><?php echo $object->nama;?></option><?php
														}
													}
												}?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tembusan</label>
											<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih tujuan tembusan surat (support multiple choise)." title="Ditembuskan ke ">?</span>
											<div class="col-sm-8">
												<div class="space-2"></div>
												<select multiple="" class="chosen-select form-control" name="tembusan[]" id="form-field-select-3" data-placeholder="Pilih user..."><?php
													$Ditembuskan = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
													if($ListUser->rowCount() >= 1){
														//while($dataTembusan = $Ditembuskan->fetch(PDO::FETCH_OBJ)){
														foreach($dumpListUser as $key => $object){
															$DitembuskanSurat = $object->nama ." (".$object->nama_jabatan .")";
															if(false !== array_search($object->id_user, $cekTembusan)){?>
																<option value="<?php echo $object->id_user;?>" selected><?php echo $DitembuskanSurat;?></option><?php
															}else{?>
																<option value="<?php echo $object->id_user;?>"><?php echo $DitembuskanSurat;?></option><?php
															}
														}								
													}else{?>
														<option value="">Not Found</option><?php
													}?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Catatan *</label>
											<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi berupa keterangan/catatan tambahan terhadap surat yang di disposisi." title="Catatan">?</span>
											<div class="col-sm-7">
												<textarea class="form-control limited" placeholder="Catatan/keterangan disposisi surat" name="note" id="form-field-9" maxlength="450" required><?php if(isset($dataDispoKu->note)){ echo $dataDispoKu->note; }?></textarea>
											</div>
										</div>
										<div class="clearfix form-actions">
											<div class="col-md-offset-1 col-md-9">
												<div class="col-sm-2">
													<button type="submit" class="btn btn-info" type="button">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Submit
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div><?php
					}
				}else{?>
					<ul class="pager"><?php 
						if($data_Disposisi->file != ''){?>
							<li class="previous">
								<a href="./berkas/<?php echo $data_Disposisi->file;?>" target="_blank" class="btn btn-primary">Lihat Surat<i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
							</li><?php
						}
						if($cekDisposisiku->rowCount() >= 1){?>
							<li class="next"><a href="./index.php?op=disposisi&smid=<?php echo $data_Disposisi->id_sm;?>&id_user=<?php echo $DataUser->id_user;?>&act=disposisi" class="btn btn-success">Telah di-Disposisi<i class="ace-icon fa fa-pencil align-top bigger-125 icon-on-right"></i></a>
							</li><?php
						}else{?>
							<li class="next"><a href="./index.php?op=disposisi&smid=<?php echo $data_Disposisi->id_sm;?>&id_user=<?php echo $DataUser->id_user;?>&act=disposisi" class="btn btn-danger">Disposisi<i class="ace-icon fa fa-share align-top bigger-125 icon-on-right"></i></a>
							</li><?php
						}?>
					</ul><?php
				}
			}?>		
		</div>

	</div><?php
}else{
	echo "Belum ada data";	
}
?>