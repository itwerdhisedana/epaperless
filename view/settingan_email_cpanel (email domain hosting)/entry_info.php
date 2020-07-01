<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$judul = htmlspecialchars($purifier->purify(trim($_POST['judul'])), ENT_QUOTES);
	$isiMemo = htmlspecialchars($purifier->purify(trim($_POST['isi'])), ENT_QUOTES);
	$tujuan = json_encode($_POST['tujuan']);
	$tgl = date("Y-m-d H:i:s", time());
	//print_r($_POST);
	if(isset($_GET['infoid'])){
		$id_info = htmlspecialchars($purifier->purify(trim($_GET['infoid'])), ENT_QUOTES);
		$params = array(':id_info' => $id_info);
		$CekInfo = $this->model->selectprepare("info", $field=null, $params, "id_info=:id_info");
		if($CekInfo->rowCount() >= 1){
			$DataCekInfo = $CekInfo->fetch(PDO::FETCH_OBJ);
			$field = array('tujuan_info' => $tujuan, 'judul_info' => $judul, 'ket_info' => $isiMemo);
			$params = array(':id_info' => $id_info);
			$update = $this->model->updateprepare("info", $field, $params, "id_info=:id_info");
			if($update){
				echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=data_memo\";</script>";
			}else{
				die("<script>alert('Gagal menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}
	}else{
		$field = array('pengirim_info' => $_SESSION['id_user'], 'tujuan_info' => $tujuan, 'judul_info' => $judul, 'ket_info' => $isiMemo, 'tgl_info' => $tgl);
		$params = array(':pengirim_info' => $_SESSION['id_user'], ':tujuan_info'=>$tujuan, ':judul_info' => $judul, ':ket_info'=>$isiMemo, ':tgl_info'=>$tgl);
		$insert = $this->model->insertprepare("info", $field, $params);
		if($insert->rowCount() >= 1){
			//Kirim Email
			$EmailAccount = $this->model->selectprepare("pengaturan", $field=null, $params=null, $where=null, "WHERE id='1' AND email !='' AND pass_email !=''");
			$AktifEmail = $this->model->selectprepare("email_setting", $field=null, $params=null, $where=null, "WHERE id_kop='4' AND status='Y'");
			if($EmailAccount->rowCount() >= 1 AND $AktifEmail->rowCount() >= 1){
				$dataEmailAccount = $EmailAccount->fetch(PDO::FETCH_OBJ);
				$dataAktifEmail = $AktifEmail->fetch(PDO::FETCH_OBJ);
				
				$TujuanSurat = "";
				if(is_array($_POST['tujuan'])){
					$dataTujuan = json_decode($tujuan, true);
					$ListUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
					while($dataListUser = $ListUser->fetch(PDO::FETCH_OBJ)){
						if(false !== array_search($dataListUser->id_user, json_decode($tujuan, true))){
							$TujuanSurat .= '- '.$dataListUser->nama .' ('.$dataListUser->nama_jabatan .')<br/>';
						}
					}
				}
	
				$isi = $dataAktifEmail->layout;
				$Rlayout = $isi;
				$arr = array("=PerihalMemo=" => $judul, "=TujuanMemo=" => $TujuanSurat, "=TglMemo=" => date("d-m-Y H:i", time()), "=IsiMemo=" =>$isiMemo);
				foreach($arr as $nama => $value){
					if(strpos($isi, $nama) !== false) {
						$Rlayout = str_replace($nama, $value, $isi);
						$isi = $Rlayout;
					}
				}
				
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
					foreach($dataTujuan as $id_tujuan){
						$params = array(':id_user' => $id_tujuan);
						$user_tujuan = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $other=null);
						$data_user_tujuan= $user_tujuan->fetch(PDO::FETCH_OBJ);
						if($data_user_tujuan->email != ''){
							$mail->AddAddress($data_user_tujuan->email, $data_user_tujuan->nama);
						}
					}
					$mail->isHTML(true);
					$topik = "Memo : ".$judul;
					$mail->Subject = $topik;
					$mail->Body = $isi;
					$mail->AltBody = $judul;
					if(!$mail->send()) {
						//echo "Mailer Error: " . $mail->ErrorInfo;
						echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan. Email notifikasi gagal dikirim!');window.location.href=\"./index.php?op=add_sm\";</script>";
					}else{
						echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan, Email notifikasi dikirim!');window.location.href=\"./index.php?op=add_sm\";</script>";
					}
				}
			}else{
				echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"$_SESSION[url]\";</script>";
			}
		}else{
			die("<script>alert('Data Gagal di simpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
		}
	}
}else{
	if(isset($_GET['infoid'])){
		$id_info = htmlspecialchars($purifier->purify(trim($_GET['infoid'])), ENT_QUOTES);
		if(isset($_GET['act']) && $_GET['act'] == "del"){
			$params = array(':id_info' => $id_info);
			$delete = $this->model->hapusprepare("info", $params, "id_info=:id_info");
			$params = array(':id_sm' => $id_info, ':kode'=> 'INFO');
			$delete2 = $this->model->hapusprepare("surat_read", $params, "id_sm=:id_sm AND kode=:kode");
			if($delete && $delete2){
				echo "<script type=\"text/javascript\">alert('Data Berhasil di Hapus...!!');window.location.href=\"./index.php?op=add_memo\";</script>";
			}else{
				die("<script>alert('Gagal menghapus data, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}else{
			$params = array(':id_info' => $id_info);
			$CekInfo = $this->model->selectprepare("info", $field=null, $params, "id_info=:id_info");
			if($CekInfo->rowCount() >= 1){
				$DataCekInfo = $CekInfo->fetch(PDO::FETCH_OBJ);
				$title= "EDIT DATA MEMO";
				$judul = 'value="'.$DataCekInfo->judul_info .'"';
				$isi = $DataCekInfo->ket_info;
				if(isset($DataCekInfo->tujuan_info) == '' OR $DataCekInfo->tujuan_info == "null"){
					$dummy_arr = '[""]';
					$CekTujuan = json_decode($dummy_arr, true);
				}else{
					$CekTujuan = json_decode($DataCekInfo->tujuan_info, true);
				}
			}else{
				$title= "ENTRI MEMO";
				$validasifile = "required";
				$dummy_arr = '[""]';
				$CekTujuan = json_decode($dummy_arr, true);
			}
		}
	}else{
		$title= "ENTRI MEMO";
		$dummy_arr = '[""]';
		$CekTujuan = json_decode($dummy_arr, true);
	}?>
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title"><?php echo $title;?></h4>
			<div class="widget-toolbar">
				<a href="#" data-action="collapse">
					<i class="ace-icon fa fa-chevron-up"></i>
				</a>
				<a href="#" data-action="close">
					<i class="ace-icon fa fa-times"></i>
				</a>
			</div>
		</div>
		<div class="widget-body">
			<form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
				<div class="space-4"></div>
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Judul Memo *</label>
					<div class="col-sm-9">
						<input class="form-control" data-rel="tooltip" placeholder="Perihal / judul memo" type="text" name="judul" <?php if(isset($judul)){ echo $judul; }?> data-placement="bottom" id="form-field-mask-1" required/>
					</div>
				</div>
				<div class="space-4"></div>				
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Isi Memo *</label>
					<div class="col-sm-9">
						<textarea class="form-control limited" placeholder="Isi memo" name="isi" id="form-field-9" required><?php if(isset($isi)){ echo $isi; }?></textarea>
					</div>
				</div>
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tujuan Memo *</label>
					<div class="col-sm-7">
						<select class="form-control" id="form-field-select-3" multiple name="tujuan[]" data-placeholder="Pilih user..." required ><?php
							$CekUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
							if($CekUser->rowCount() >= 1){
								while($DataCekUser = $CekUser->fetch(PDO::FETCH_OBJ)){
									$DiteruskanSurat = $DataCekUser->nama ." (".$DataCekUser->nama_jabatan .")";
									if(false !== array_search($DataCekUser->id_user, $CekTujuan)){?>
										<option value="<?php echo $DataCekUser->id_user;?>" selected><?php echo $DiteruskanSurat;?></option><?php
									}else{?>
										<option value="<?php echo $DataCekUser->id_user;?>"><?php echo $DiteruskanSurat;?></option><?php
									}
								}								
							}else{?>
								<option value="">Not Found</option><?php
							}?>
						</select>
					</div>
				</div>
				<div class="space-4"></div>
				<div class="form-group">
					<div class="col-md-offset-2 col-md-10">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div><?php
}?>