<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$noagenda = htmlspecialchars($purifier->purify(trim($_POST['noagenda'])), ENT_QUOTES);
	$noagenda_custom = trim($_POST['noagenda_custom']);
	$nosm = htmlspecialchars($purifier->purify(trim($_POST['nosm'])), ENT_QUOTES);
	$tglsm = htmlspecialchars($purifier->purify(trim($_POST['tglsm'])), ENT_QUOTES);
	$tglsm = explode("-",$tglsm);
	$tglsmdb = $tglsm[2]."-".$tglsm[1]."-".$tglsm[0];
	$tgl_terima = htmlspecialchars($purifier->purify(trim($_POST['tgl_terima'])), ENT_QUOTES);
	$tgl_terima = explode("-",$tgl_terima);
	$tgl_terimadb = $tgl_terima[2]."-".$tgl_terima[1]."-".$tgl_terima[0];
	$pengirim = htmlspecialchars($purifier->purify(trim($_POST['pengirim'])), ENT_QUOTES);
	$tujuan = json_encode($_POST['tujuan']);
	$perihal = htmlspecialchars($purifier->purify(trim($_POST['perihal'])), ENT_QUOTES);
	$ket = htmlspecialchars($purifier->purify(trim($_POST['ket'])), ENT_QUOTES);
	$klasifikasi = htmlspecialchars($purifier->purify(trim($_POST['id_klasifikasi'])), ENT_QUOTES);
	
	$fileName = htmlspecialchars($_FILES['filesm']['name'], ENT_QUOTES);
	$tipefile = pathinfo($fileName,PATHINFO_EXTENSION);
	$extensionList = array("pdf","jpg","jpeg","png");
	$namaDir = 'berkas/';
	$filesm = $namaDir."SM"."_".$tgl_terima[0]."-".$tgl_terima[1]."-".$tgl_terima[2]."_". slugify($perihal)."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
	if(empty($fileName)){
		$filedb = "";
	}else{
		$filedb = "SM"."_".$tgl_terima[0]."-".$tgl_terima[1]."-".$tgl_terima[2]."_". slugify($perihal)."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
	}
	$tgl_upload = date("Y-m-d H:i:s", time());
	
	//print_r($_POST);
	if(isset($_GET['smid'])){
		$smid = htmlspecialchars($purifier->purify(trim($_GET['smid'])), ENT_QUOTES);
		$params = array(':id_sm' => $smid);
		$lihat_sm = $this->model->selectprepare("arsip_sm", $field=null, $params, "id_sm=:id_sm");
		if($lihat_sm->rowCount() >= 1){
			$data_lihat_sm = $lihat_sm->fetch(PDO::FETCH_OBJ);
			$idsm = $data_lihat_sm->id_sm;
			if(empty($fileName)){
				//echo "No Update File";
				$field = array('no_sm' => $nosm, 'tgl_terima' => $tgl_terimadb, 'no_agenda' => $noagenda, 'custom_noagenda' => $noagenda_custom, 'tgl_surat' => $tglsmdb, 'pengirim' => $pengirim, 'klasifikasi' => $klasifikasi, 'tujuan_surat' => $tujuan, 'perihal' => $perihal, 'ket' => $ket);
			}else{
				//if(in_array($tipefile, $extensionList)){
					@unlink($namaDir.$data_lihat_sm->file);
					$field = array('no_sm' => $nosm, 'tgl_terima' => $tgl_terimadb, 'tgl_surat' => $tglsmdb, 'pengirim' => $pengirim, 'klasifikasi' => $klasifikasi, 'tujuan_surat' => $tujuan, 'perihal' => $perihal, 'ket' => $ket, 'file' => $filedb);
					move_uploaded_file($_FILES['filesm']['tmp_name'], $filesm);
				/*}else{
					echo "<script type=\"text/javascript\">alert('File gagal di Upload, Format file tidak di dukung!!!');window.location.href=\"./index.php?op=add_sm&smid=$idsm\";</script>";
				}*/
			}
			$params = array(':id_sm' => $idsm);
			$update = $this->model->updateprepare("arsip_sm", $field, $params, "id_sm=:id_sm");
			if($update){
				echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=sm&smid=$idsm\";</script>";
			}
		}
	}else{
		//echo $tujuan;
		$params = array(':no_agenda' => $noagenda);
		/*$cek_nosm = $this->model->selectprepare("arsip_sm", $field=null, $params, "no_agenda=:no_agenda");
		if($cek_nosm->rowCount() <= 0){*/
			$field = array('id_user' => $_SESSION['id_user'], 'no_sm'=>$nosm, 'tgl_terima'=>$tgl_terimadb, 'no_agenda'=>$noagenda, 'custom_noagenda' => $noagenda_custom, 'tgl_surat'=>$tglsmdb, 'pengirim'=>$pengirim, 'klasifikasi' => $klasifikasi, 'perihal'=>$perihal, 'tujuan_surat'=>$tujuan, 'ket'=>$ket, 'file'=>$filedb, 'created'=>$tgl_upload);
			$params = array(':id_user' => $_SESSION['id_user'], ':no_sm'=>$nosm, ':tgl_terima'=>$tgl_terimadb, ':no_agenda'=>$noagenda, ':custom_noagenda' => $noagenda_custom, ':tgl_surat'=>$tglsmdb, ':pengirim'=>$pengirim, 'klasifikasi' => $klasifikasi, ':perihal'=>$perihal, ':tujuan_surat'=>$tujuan, ':ket'=>$ket, ':file'=>$filedb, ':created'=>$tgl_upload);
			if(empty($fileName)){
				$insert = $this->model->insertprepare("arsip_sm", $field, $params);
				if($insert->rowCount() >= 1){
					//Kirim Email
					$EmailAccount = $this->model->selectprepare("pengaturan", $field=null, $params=null, $where=null, "WHERE id='1' AND email !='' AND pass_email !=''");
					$AktifEmail = $this->model->selectprepare("email_setting", $field=null, $params=null, $where=null, "WHERE id_kop='2' AND status='Y'");
					if($EmailAccount->rowCount() >= 1 AND $AktifEmail->rowCount() >= 1){
						$dataEmailAccount = $EmailAccount->fetch(PDO::FETCH_OBJ);
						$dataAktifEmail = $AktifEmail->fetch(PDO::FETCH_OBJ);
						
						$TujuanSurat = "";
						if($tujuan != ''){
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
						$arr = array("=NoAgenda=" =>$noagenda_custom, "=NoSurat=" => $nosm, "=Perihal=" => $perihal, "=TujuanSurat=" => $TujuanSurat, "=TglSurat=" =>tgl_indo($tglsmdb), "=TglTerima=" => tgl_indo($tgl_terimadb), "=AsalSurat=" =>$pengirim, "=Keterangan=" => $ket, "=Penerima=" =>$_SESSION['nama']);
						foreach($arr as $nama => $value){
							if(strpos($isi, $nama) !== false) {
								$Rlayout = str_replace($nama, $value, $isi);
								$isi = $Rlayout;
							}
						}
						
						if($tujuan != '' OR $tujuan != 'null'){
							$mail = new PHPMailer;
							$mail->SMTPDebug = 0;                               
							$mail->isSMTP();                                 
							$mail->Host = "smtp.gmail.com";
							$mail->SMTPAuth = true;  
							$mail->Username = $dataEmailAccount->email;
							$mail->Password = $dataEmailAccount->pass_email;                      
							//If SMTP requires TLS encryption then set it
							//$mail->SMTPSecure = "tls";                           
							$mail->Port = 587;                                   
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
							$topik = "Surat Masuk: ".$perihal;
							$mail->Subject = $topik;
							$mail->Body = $isi;
							$mail->AltBody = $perihal;
							if(!$mail->send()) {
								//echo "Mailer Error: " . $mail->ErrorInfo;
								echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan. Email notifikasi gagal dikirim!');window.location.href=\"./index.php?op=add_sm\";</script>";
							}else{
								echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan, Email notifikasi dikirim!');window.location.href=\"./index.php?op=add_sm\";</script>";
							}
						}else{
							echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan!');window.location.href=\"./index.php?op=add_sm\";</script>";
						}
					}else{
						echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan!');window.location.href=\"./index.php?op=add_sm\";</script>";
					}
				}else{
					die("<script>alert('Data Gagal di simpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}else{
				//if(in_array($tipefile, $extensionList)){
					if(move_uploaded_file($_FILES['filesm']['tmp_name'], $filesm)){
						$insert = $this->model->insertprepare("arsip_sm", $field, $params);
						if($insert->rowCount() >= 1){
							//Kirim Email
							$EmailAccount = $this->model->selectprepare("pengaturan", $field=null, $params=null, $where=null, "WHERE id='1' AND email !='' AND pass_email !=''");
							$AktifEmail = $this->model->selectprepare("email_setting", $field=null, $params=null, $where=null, "WHERE id_kop='2' AND status='Y'");
							if($EmailAccount->rowCount() >= 1 AND $AktifEmail->rowCount() >= 1){
								$dataEmailAccount = $EmailAccount->fetch(PDO::FETCH_OBJ);
								$dataAktifEmail = $AktifEmail->fetch(PDO::FETCH_OBJ);
								
								$TujuanSurat = "";
								if($tujuan != ''){
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
								$arr = array("=NoAgenda=" =>$noagenda_custom, "=NoSurat=" => $nosm, "=Perihal=" => $perihal, "=TujuanSurat=" => $TujuanSurat, "=TglSurat=" =>tgl_indo($tglsmdb), "=TglTerima=" => tgl_indo($tgl_terimadb), "=AsalSurat=" =>$pengirim, "=Keterangan=" => $ket, "=Penerima=" =>$_SESSION['nama']);
								foreach($arr as $nama => $value){
									if(strpos($isi, $nama) !== false) {
										$Rlayout = str_replace($nama, $value, $isi);
										$isi = $Rlayout;
									}
								}
								
								if($tujuan != '' OR $tujuan != 'null'){
									$mail = new PHPMailer;
									$mail->SMTPDebug = 0;                               
									$mail->isSMTP();                                 
									$mail->Host = "smtp.gmail.com";
									$mail->SMTPAuth = true;  
									$mail->Username = $dataEmailAccount->email;
									$mail->Password = $dataEmailAccount->pass_email;                      
									//If SMTP requires TLS encryption then set it
									//$mail->SMTPSecure = "tls";                           
									$mail->Port = 587;                                   
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
									$topik = "Surat Masuk: ".$perihal;
									$mail->Subject = $topik;
									$mail->Body = $isi;
									$mail->AltBody = $perihal;
									$lokasi = "berkas/$filedb";
									if(file_exists($lokasi)){
										$mail->addAttachment($lokasi);
									}
									if(!$mail->send()) {
										//echo "Mailer Error: " . $mail->ErrorInfo;
										echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan. Email notifikasi gagal dikirim!');window.location.href=\"$_SESSION[url]\";</script>";
									}else{
										echo "<script type=\"text/javascript\">alert('Data Berhasil diSimpan, Email notifikasi dikirim!');window.location.href=\"$_SESSION[url]\";</script>";
									}
								}else{
									echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"$_SESSION[url]\";</script>";
								}
							}else{
								echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"$_SESSION[url]\";</script>";
							}
						}else{
							die("<script>alert('Data Gagal di simpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
						}
					}else{
						echo "<script type=\"text/javascript\">alert('File gagal di Upload ke Folder, Silahkan ulangi!!!');window.history.go(-1);</script>";
					}
				/*}else{
					echo "<script type=\"text/javascript\">alert('File gagal di Upload, Format file tidak di dukung!!!');window.history.go(-1);</script>";
				}*/
			}
		/*}else{
			echo "<script type=\"text/javascript\">alert('PERHATIAN..! Nomor Agenda Surat Masuk yang dimasukkan sudah pernah terdata di Sistem. Silahkan Ulangi.');window.history.go(-1);</script>";
		}*/
	}
}else{
	if(isset($_GET['id_klasifikasi'])){
		$id_klasifikasi = htmlspecialchars($purifier->purify(trim($_GET['id_klasifikasi'])), ENT_QUOTES);
	}

	if(isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != ''){ 
		$kode_jenis_surat = htmlspecialchars($purifier->purify(trim($_GET['id_klasifikasi'])), ENT_QUOTES);
		$kondisi = array(':klasifikasi' => $kode_jenis_surat);
		$cek_noaagenda = $this->model->selectprepare("arsip_sm", $field=null, $kondisi,  "klasifikasi=:klasifikasi", "ORDER BY id_sm DESC LIMIT 1");

		$kondisi1 = array(':id_klas' => $kode_jenis_surat);
		$klasifikasi_surat = $this->model->selectprepare("klasifikasi", $field=null, $kondisi1,  "id_klas=:id_klas", $order=null);
		if($klasifikasi_surat->rowCount() >= 1){
			$data_klasifikasi_surat = $klasifikasi_surat->fetch(PDO::FETCH_OBJ);
		}else{
			echo "<script type=\"text/javascript\">alert('Klasifikasi surat tidak ditemukan, silahkan ulangi..!!');window.history.go(-1);</script>";
		}
	}
	if(isset($_GET['smid'])){
		$smid = htmlspecialchars($purifier->purify(trim($_GET['smid'])), ENT_QUOTES);
		$params = array(':id_sm' => $smid);
		$cek_sm = $this->model->selectprepare("arsip_sm", $field=null, $params, "id_sm=:id_sm");
		if($cek_sm->rowCount() >= 1){
			$data_sm = $cek_sm->fetch(PDO::FETCH_OBJ);
			$title= "Edit Data Surat Masuk";
			$ketfile = "File Surat ";
			$noagenda = 'value="'.$data_sm->no_agenda .'"';
			$nosm = 'value="'.$data_sm->no_sm .'"';
			$tgl_surat = explode("-", $data_sm->tgl_surat);
			$tgl_surat = $tgl_surat[2]."-".$tgl_surat[1]."-".$tgl_surat[0];
			$tgl_surat = 'value="'.$tgl_surat.'"';
			$tgl_terima = explode("-", $data_sm->tgl_terima);
			$tgl_terima = $tgl_terima[2]."-".$tgl_terima[1]."-".$tgl_terima[0];
			$tgl_terima = 'value="'.$tgl_terima.'"';
			$pengirim = 'value="'.$data_sm->pengirim .'"';
			//$tujuan = 'value="'.$data_sm->tujuan_surat .'"';
			$perihal = $data_sm->perihal;
			$ket = $data_sm->ket;
			$id_klasifikasi = $data_sm->klasifikasi;			
			if(isset($data_sm->tujuan_surat) == '' OR $data_sm->tujuan_surat == "null"){
				$dummy_arr = '[""]';
				$cekDiteruskan = json_decode($dummy_arr, true);
			}else{
				$cekDiteruskan = json_decode($data_sm->tujuan_surat, true);
			}
			$noAgenda = sprintf("%04d", $data_sm->no_agenda);
			$nonoAgendaShow = $data_sm->custom_noagenda;
		}else{
			$title= "Entri Surat Masuk";
			$ketfile = "File Surat ";
			$validasifile = "required";
			$dummy_arr = '[""]';
			$cekDiteruskan = json_decode($dummy_arr, true);
			$params = array(':id' => 1);
			$cek_noagenda_custom = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id")->fetch(PDO::FETCH_OBJ);
			
			if(isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != ''){ 
				$DataValueSet = array(
					"=KodeSurat=" => $data_klasifikasi_surat->kode,
					"=Tahun=" => date("Y"));

				$format_no_agenda = $cek_noagenda_custom->no_agenda_sm;
				if(isset($DataValueSet)){
					foreach($DataValueSet as $nama => $value){
						if(strpos($format_no_agenda, $nama) !== false) {
							$Rlayout = str_replace($nama, $value, $format_no_agenda);
							$format_no_agenda = $Rlayout;
						}
					}
				}

				if($cek_noaagenda->rowCount() >= 1){
					$data_cek_noaagenda = $cek_noaagenda->fetch(PDO::FETCH_OBJ);
					$thn_data_surat = substr($data_cek_noaagenda->created,0,4); 
					if($thn_data_surat == date('Y')){
						$noAgenda = sprintf("%04d", $data_cek_noaagenda->no_agenda+1);
						$nonoAgendaShow = $data_cek_noaagenda->no_agenda+1;
					}else{					
						if($cek_noagenda_custom->no_agenda_sm_start != ''){
							$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sm_start);
							$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sm_start;
						}else{
							$noAgenda = sprintf("%04d", 1);
							$nonoAgendaShow = 1;
						}
					}				
				}else{
					if($cek_noagenda_custom->no_agenda_sm_start != ''){
						$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sm_start);
						$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sm_start;
					}else{
						$noAgenda = sprintf("%04d", 1);
						$nonoAgendaShow = 1;
					}
				}
			}
		}
	}else{
		$title= "Entri Surat Masukk";
		$validasifile = "required";
		$ketfile = "File Surat ";
		$dummy_arr = '[""]';
		$cekDiteruskan = json_decode($dummy_arr, true);
		$params = array(':id' => 1);
		$cek_noagenda_custom = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id")->fetch(PDO::FETCH_OBJ);

		if(isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != ''){ 
			$DataValueSet = array(
					"=KodeSurat=" => $data_klasifikasi_surat->kode,
					"=Tahun=" => date("Y"));

			$format_no_agenda = $cek_noagenda_custom->no_agenda_sm;
			if(isset($DataValueSet)){
				foreach($DataValueSet as $nama => $value){
					if(strpos($format_no_agenda, $nama) !== false) {
						$Rlayout = str_replace($nama, $value, $format_no_agenda);
						$format_no_agenda = $Rlayout;
					}
				}
			}
			if($cek_noaagenda->rowCount() >= 1){
				$data_cek_noaagenda = $cek_noaagenda->fetch(PDO::FETCH_OBJ);
				$thn_data_surat = substr($data_cek_noaagenda->created,0,4); 
				if($thn_data_surat == date('Y')){
					$noAgenda = sprintf("%04d", $data_cek_noaagenda->no_agenda+1);
					$nonoAgendaShow = $data_cek_noaagenda->no_agenda+1;
				}else{					
					if($cek_noagenda_custom->no_agenda_sm_start != ''){
						$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sm_start);
						$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sm_start;
					}else{
						$noAgenda = sprintf("%04d", 1);
						$nonoAgendaShow = 1;
					}
				}
			}else{
				if($cek_noagenda_custom->no_agenda_sm_start != ''){
					$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sm_start);
					$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sm_start;
				}else{
					$noAgenda = sprintf("%04d", 1);
					$nonoAgendaShow = 1;
				}
			}
		}
	}
	//print_r($cekDiteruskan);?>
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
			<div class="widget-main">
				<form class="form-horizontal" role="form" enctype="multipart/form-data" method="GET" name="formku" action="./index.php?op=add_sm">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1">Jenis Surat *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih klasifikasi arsip." title="Klasifikasi">?</span>
						<div class="col-sm-3">
							<input type="hidden" name="op" value="add_sm"/>
							<select class="form-control" id="form-field-select-3" name="id_klasifikasi" data-placeholder="Pilih Klasifikasi..." required onchange="this.form.submit()" <?php if(isset($_GET['smid'])){ echo 'disabled'; } ?>>
								<option value="" >Pilih Klasifikasi</option><?php
								$KlasArsip= $this->model->selectprepare("klasifikasi", $field=null, $params=null, $where=null, "ORDER BY nama ASC");
								if($KlasArsip->rowCount() >= 1){
									while($dataKlasArsip = $KlasArsip->fetch(PDO::FETCH_OBJ)){
										if(isset($id_klasifikasi) && $id_klasifikasi == $dataKlasArsip->id_klas){?>
											<option value="<?php echo $dataKlasArsip->id_klas;?>" selected><?php echo $dataKlasArsip->nama;?></option><?php
										}else{?>
											<option value="<?php echo $dataKlasArsip->id_klas;?>"><?php echo $dataKlasArsip->nama;?></option><?php
										}
									}
								}else{?>
									<option value="">Data klasifikasi belum ada</option><?php
								}?>
							</select>
						</div>
					</div>
					</form> <?php
					if((isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != '') OR (isset($_GET['smid'])  && $_GET['smid'] != '')){ ?>
						<form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> No. Agenda *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan nomor agenda surat masuk." title="Nomor Agenda">?</span>
							<div class="col-sm-3"> <?php 
								$params = array(':id' => 1);
								$cek_noagenda_custom = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id")->fetch(PDO::FETCH_OBJ);
								if($cek_noagenda_custom->no_agenda_sm != '' && !isset($data_sm->no_agenda)){
									//$noAgendaDb = $noAgenda;
									$noAgenda = $noAgenda."/$format_no_agenda";								
								}else{
									$noAgenda = $nonoAgendaShow;
								} ?>
								<input class="form-control" placeholder="Nomor Agenda Surat" type="text" name="noagenda" <?php if(isset($noAgenda)){ echo 'value="'.$noAgenda .'"'; } ?> id="form-field-mask-1" required disabled />
								<input type="hidden" name="noagenda" value="<?php echo $nonoAgendaShow;?>"/>
								<input type="hidden" name="noagenda_custom" value="<?php echo $noAgenda;?>"/>
								<input type="hidden" name="id_klasifikasi" value="<?php echo $id_klasifikasi;?>"/>
								<?php /*
								<input class="form-control" data-rel="tooltip" type="text" name="noagenda" <?php if(isset($noagenda)){ echo $noagenda; }?> id="form-field-1" placeholder="Nomor Agenda Surat" title="Diisi dengan nomor agenda surat masuk!" data-placement="bottom" required/>*/?>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nomor Surat *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan nomor surat masuk yang diterima." title="Nomor Surat Masuk">?</span>
							<div class="col-sm-4">
								<input class="form-control" placeholder="Nomor surat masuk" type="text" name="nosm" <?php if(isset($nosm)){ echo $nosm; }?> id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tanggal Surat *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan tanggal pada surat masuk. ex. 01-12-2015" title="Tanggal Surat">?</span>
							<div class="col-sm-3">
								<input class="form-control date-picker" id="id-date-picker-1" data-date-format="dd-mm-yyyy" placeholder="Tanggal pada surat masuk" type="text" name="tglsm" <?php if(isset($tgl_surat)){ echo $tgl_surat; }?> id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tanggal Terima *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan tanggal surat masuk diterima. ex. 01-12-2015" title="Tanggal Terima">?</span>
							<div class="col-sm-3">
								<input class="form-control date-picker" id="id-date-picker-1" data-date-format="dd-mm-yyyy" placeholder="Tanggal terima surat masuk" type="text" name="tgl_terima" <?php if(isset($tgl_terima)){ echo $tgl_terima; }?> id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Surat Dari *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai sumber pengirim surat (nama lembaga/peroranngan)." title="Sumber Surat">?</span>
							<div class="col-sm-8">
								<input class="form-control" data-rel="tooltip" placeholder="Sumber surat/pengirim" type="text" name="pengirim" <?php if(isset($pengirim)){ echo $pengirim; }?> title="Di isi sesuai sumber pengirim surat (nama lembaga atau peroranngan)" data-placement="bottom" id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Diteruskan ke *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih tujuan Surat dieruskan (support multiple choise)." title="Diteruskan ke *">?</span>
							<div class="col-sm-8">
								<div class="space-2"></div>
								<select multiple="" class="chosen-select form-control" name="tujuan[]" id="form-field-select-3" data-placeholder="Pilih user..." required><?php
									$Diteruskan = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
									if($Diteruskan->rowCount() >= 1){
										while($dataDiteruskan = $Diteruskan->fetch(PDO::FETCH_OBJ)){
											$DiteruskanSurat = $dataDiteruskan->nama ." (".$dataDiteruskan->nama_jabatan .")";
											if(false !== array_search($dataDiteruskan->id_user, $cekDiteruskan)){?>
												<option value="<?php echo $dataDiteruskan->id_user;?>" selected><?php echo $DiteruskanSurat;?></option><?php
											}else{?>
												<option value="<?php echo $dataDiteruskan->id_user;?>"><?php echo $DiteruskanSurat;?></option><?php
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
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Perihal *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai perihal atau subjek surat masuk." title="Perihal">?</span>
							<div class="col-sm-9">
								<textarea class="form-control limited" placeholder="Perihal/subjek surat" name="perihal" id="form-field-9" maxlength="150" required><?php if(isset($perihal)){ echo $perihal; }?></textarea>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Keterangan </label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan keterangan tambahan jika ada (seperti jadwal undangan, tempat, tanggal penting dsb)." title="Keterangan">?</span>
							<div class="col-sm-9">
								<textarea class="form-control limited" placeholder="Keterangan tambahan (jika ada)" name="ket" id="form-field-9" maxlength="150"><?php if(isset($ket)){ echo $ket; }?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> <?php echo $ketfile;?></label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih File surat masuk yang ingin di upload. Caranya klik menu Pilih File. Tipe file : .pdf, .jpg, .png" title="File surat masuk">?</span>
							<div class="col-sm-4">
								<input type="file" class="form-control" name="filesm" id="id-input-file-1"/>
							</div>
						</div>
						<div class="clearfix form-actions">
							<div class="col-md-offset-3 col-md-9">
								<div class="col-sm-2">
									<button type="submit" class="btn btn-info" type="button">
										<i class="ace-icon fa fa-check bigger-110"></i>
										Submit
									</button>
								</div>
							</div>
						</div>
					</form> <?php
				} ?>
			</div>
		</div>
	</div><?php
}?>