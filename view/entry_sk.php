<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$noagenda = htmlspecialchars($purifier->purify(trim($_POST['noagenda'])), ENT_QUOTES);
	$noagenda_custom = trim($_POST['noagenda_custom']);
	$nosk = htmlspecialchars($purifier->purify(trim($_POST['nosk'])), ENT_QUOTES);
	$tglsk = htmlspecialchars($purifier->purify(trim($_POST['tglsk'])), ENT_QUOTES);
	$tglsk = explode("-",$tglsk);
	$tglskdb = $tglsk[2]."-".$tglsk[1]."-".$tglsk[0];
	$pengolah = htmlspecialchars($purifier->purify(trim($_POST['pengolah'])), ENT_QUOTES);
	$klasifikasi = htmlspecialchars($purifier->purify(trim($_POST['id_klasifikasi'])), ENT_QUOTES);
	$tujuan = htmlspecialchars($purifier->purify(trim($_POST['tujuan'])), ENT_QUOTES);
	$perihal = htmlspecialchars($purifier->purify(trim($_POST['perihal'])), ENT_QUOTES);
	$ket = htmlspecialchars($purifier->purify(trim($_POST['ket'])), ENT_QUOTES);
	
	$fileName = htmlspecialchars($_FILES['filesk']['name'], ENT_QUOTES);
	$tipefile = pathinfo($fileName,PATHINFO_EXTENSION);
	$extensionList = array("pdf","jpg","jpeg","png","PNG", "JPG", "JPEG","PDF");
	$namaDir = 'berkas/';
	$filesk = $namaDir."SK"."_".$tglskdb."_". slugify($perihal)."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
	if(empty($fileName)){
		$filedb = "";
	}else{
		$filedb = "SK"."_".$tglskdb."_". slugify($perihal)."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
	}
	$tgl_upload = date("Y-m-d H:i:s", time());
	
	//echo "$filesk <br/>";
	//print_r($_POST);
	if(isset($_GET['skid'])){
		$skid = htmlspecialchars($purifier->purify(trim($_GET['skid'])), ENT_QUOTES);
		$params = array(':id_sk' => $skid);
		$lihat_sk = $this->model->selectprepare("arsip_sk", $field=null, $params, "id_sk=:id_sk");
		if($lihat_sk->rowCount() >= 1){
			$data_lihat_sk = $lihat_sk->fetch(PDO::FETCH_OBJ);
			$idsk = $data_lihat_sk->id_sk;
			if(empty($fileName)){
				//echo "No Update File";
				$field = array('no_sk' => $nosk, 'tgl_surat' => $tglskdb, 'pengolah' => $pengolah, 'tujuan_surat' => $tujuan, 'perihal' => $perihal, 'ket' => $ket);
			}else{
				//if(in_array($tipefile, $extensionList)){
					@unlink($namaDir.$data_lihat_sk->file);
					$field = array('no_sk' => $nosk, 'tgl_surat' => $tglskdb, 'pengolah' => $pengolah, 'tujuan_surat' => $tujuan, 'perihal' => $perihal, 'ket' => $ket, 'file' => $filedb);
					move_uploaded_file($_FILES['filesk']['tmp_name'], $filesk);
				/*}else{
					echo "<script type=\"text/javascript\">alert('File gagal di Upload, Format file tidak di dukung!!!');window.location.href=\"./index.php?op=add_sk&skid=$idsk\";</script>";
				}*/
			}
			$params = array(':id_sk' => $idsk);
			$update = $this->model->updateprepare("arsip_sk", $field, $params, "id_sk=:id_sk");
			if($update){
				echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=sk&skid=$idsk\";</script>";
			}else{
				die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}
	}else{
		$params = array(':no_sk' => $nosk);
		$cek_nosk = $this->model->selectprepare("arsip_sk", $field=null, $params, "no_sk=:no_sk");
		if($cek_nosk->rowCount() <= 0){
			$field = array('id_user' => $_SESSION['id_user'], 'no_sk'=>$nosk, 'klasifikasi' => $klasifikasi, 'tgl_surat'=>$tglskdb, 'pengolah'=>$pengolah, 'tujuan_surat'=>$tujuan, 'perihal'=>$perihal, 'no_agenda' => $noagenda, 'custom_noagenda' => $noagenda_custom, 'ket'=>$ket, 'file'=>$filedb, 'created'=>$tgl_upload);
			$params = array(':id_user' => $_SESSION['id_user'], ':no_sk'=>$nosk, ':klasifikasi' => $klasifikasi, ':tgl_surat'=>$tglskdb, ':pengolah'=>$pengolah, ':tujuan_surat'=>$tujuan, ':perihal'=>$perihal, ':no_agenda' => $noagenda, ':custom_noagenda' => $noagenda_custom, ':ket'=>$ket, ':file'=>$filedb, ':created'=>$tgl_upload);
			if(empty($fileName)){
				$insert = $this->model->insertprepare("arsip_sk", $field, $params);
				if($insert->rowCount() >= 1){
					echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"$_SESSION[url]\";</script>";
				}else{
					die("<script>alert('Data Gagal di simpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}else{
				//if(in_array($tipefile, $extensionList)){
					if(move_uploaded_file($_FILES['filesk']['tmp_name'], $filesk)){
						$insert = $this->model->insertprepare("arsip_sk", $field, $params);
						if($insert->rowCount() >= 1){
							echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"$_SESSION[url]\";</script>";
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
		}else{
			echo "<script type=\"text/javascript\">alert('PERHATIAN..! Nomor Surat Keluar yang dimasukkan sudah pernah terdata di Sistem. Silahkan Ulangi.');window.history.go(-1);</script>";
		}
	}
}else{
	if(isset($_GET['skid'])){
		$skid = htmlspecialchars($purifier->purify(trim($_GET['skid'])), ENT_QUOTES);
		$params = array(':id_sk' => $skid);
		$cek_sk = $this->model->selectprepare("arsip_sk", $field=null, $params, "id_sk=:id_sk");
		if($cek_sk->rowCount() >= 1){
			$data_sk = $cek_sk->fetch(PDO::FETCH_OBJ);
			$title= "Edit Data Surat Keluar";
			$ketfile = "File Surat ";
			$pengolah = 'value="'.$data_sk->pengolah .'"';
			$nosk = 'value="'.$data_sk->no_sk .'"';
			$noagenda = $data_sk->no_agenda;
			$id_klasifikasi = $data_sk->klasifikasi;
			$tgl_surat = explode("-", $data_sk->tgl_surat);
			$tgl_surat = $tgl_surat[2]."-".$tgl_surat[1]."-".$tgl_surat[0];
			$tgl_surat = 'value="'.$tgl_surat.'"';
			$tujuan_surat = 'value="'.$data_sk->tujuan_surat .'"';
			$perihal = $data_sk->perihal;
			$ket = $data_sk->ket;
		}else{
			$title= "Entri Surat Keluar";
			$ketfile = "File Surat";
			$validasifile = "required";
		}
	}else{
		$title= "Entri Surat Keluar";
		$ketfile = "File Surat";
	}

	$params = array(':id' => 1);
	$cek_noagenda_custom = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id")->fetch(PDO::FETCH_OBJ);

	if(isset($_GET['id_klasifikasi'])){
		$id_klasifikasi = htmlspecialchars($purifier->purify(trim($_GET['id_klasifikasi'])), ENT_QUOTES);
	}

		/*if(isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != ''){ 
		$kode_jenis_surat = htmlspecialchars($purifier->purify(trim($_GET['id_klasifikasi'])), ENT_QUOTES);
		$kondisi = array(':klasifikasi' => $kode_jenis_surat);
		$cek_noaagenda = $this->model->selectprepare("arsip_sk", $field=null, $kondisi,  "klasifikasi=:klasifikasi", "ORDER BY id_sk DESC LIMIT 1");

		$kondisi1 = array(':id_klas' => $kode_jenis_surat);
		$klasifikasi_surat = $this->model->selectprepare("klasifikasi", $field=null, $kondisi1,  "id_klas=:id_klas", $order=null);
		if($klasifikasi_surat->rowCount() >= 1){
			$data_klasifikasi_surat = $klasifikasi_surat->fetch(PDO::FETCH_OBJ);
		}else{
			echo "<script type=\"text/javascript\">alert('Klasifikasi surat tidak ditemukan, silahkan ulangi..!!');window.history.go(-1);</script>";
		}*/
	//}
	
	if(isset($data_sk->no_agenda)){
		$noAgenda = sprintf("%04d", $data_sk->no_agenda);
		$nonoAgendaShow = $data_sk->custom_noagenda;
	}else{
		if(isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != ''){ 
			$kode_jenis_surat = htmlspecialchars($purifier->purify(trim($_GET['id_klasifikasi'])), ENT_QUOTES);
			
			$kondisi1 = array(':id_klas' => $kode_jenis_surat);
			$klasifikasi_surat = $this->model->selectprepare("klasifikasi_sk", $field=null, $kondisi1,  "id_klas=:id_klas", $order=null);
			$data_klasifikasi_surat = $klasifikasi_surat->fetch(PDO::FETCH_OBJ);

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

			$kondisi = array(':klasifikasi' => $kode_jenis_surat);
			$cek_noaagenda = $this->model->selectprepare("arsip_sk", $field=null, $kondisi,  "klasifikasi=:klasifikasi", "ORDER BY id_sk DESC LIMIT 1");
			if($cek_noaagenda->rowCount() >= 1){
				$data_cek_noaagenda = $cek_noaagenda->fetch(PDO::FETCH_OBJ);
				if(isset($data_cek_noaagenda->no_agenda)){
					$thn_data_surat = substr($data_cek_noaagenda->created,0,4);
					if($thn_data_surat == date('Y')){
						$noAgenda = sprintf("%04d", $data_cek_noaagenda->no_agenda+1);
						$nonoAgendaShow = $data_cek_noaagenda->no_agenda+1;
					}else{
						if($cek_noagenda_custom->no_agenda_sk_start != ''){
							$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sk_start);
							$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sk_start;
						}else{
							$noAgenda = sprintf("%04d", 1);
							$nonoAgendaShow = 1;
						}
					}
				}else{
					if($cek_noagenda_custom->no_agenda_sk_start != ''){
						$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sk_start);
						$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sk_start;
					}else{
						$noAgenda = sprintf("%04d", 1);
						$nonoAgendaShow = 1;
					}
				}
			}else{
				if($cek_noagenda_custom->no_agenda_sk_start != ''){
					$noAgenda = sprintf("%04d", $cek_noagenda_custom->no_agenda_sk_start);
					$nonoAgendaShow = $cek_noagenda_custom->no_agenda_sk_start;
				}else{
					$noAgenda = sprintf("%04d", 1);
					$nonoAgendaShow = 1;
				}
			}
		}
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
			<div class="widget-main">
				<form class="form-horizontal" role="form" enctype="multipart/form-data" method="GET" name="formku" action="./index.php?op=add_sk">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1">Jenis Naskah *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih klasifikasi arsip." title="Klasifikasi">?</span>
						<div class="col-sm-4">
							<input type="hidden" name="op" value="add_sk"/>
							<select class="form-control" id="form-field-select-3" name="id_klasifikasi" data-placeholder="Pilih Klasifikasi..." required onchange="this.form.submit()" <?php if(isset($_GET['skid'])){ echo 'disabled'; } ?>>
								<option value="">Pilih Klasifikasi</option><?php
								$KlasArsip= $this->model->selectprepare("klasifikasi_sk", $field=null, $params=null, $where=null, "ORDER BY nama ASC");
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
					<div class="space-4"></div>
				</form><?php
				if((isset($_GET['id_klasifikasi']) && $_GET['id_klasifikasi'] != '') OR (isset($_GET['skid'])  && $_GET['skid'] != '')){ ?>
					<form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> No. Agenda *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan nomor agenda surat masuk." title="Nomor Agenda">?</span>
							<div class="col-sm-3"><?php 
								$params = array(':id' => 1);
								$cek_noagenda_custom = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id")->fetch(PDO::FETCH_OBJ);
								if($cek_noagenda_custom->no_agenda_sm != '' && !isset($data_sk->no_agenda)){
									$noAgenda = $noAgenda."/$format_no_agenda";								
								}else{
									$noAgenda = $nonoAgendaShow;
								} ?>
								<input class="form-control" placeholder="Nomor Agenda Surat" type="text" name="noagenda" <?php if(isset($noAgenda)){ echo 'value="'.$noAgenda .'"'; } ?> id="form-field-mask-1" required disabled />
								<input type="hidden" name="noagenda" value="<?php echo $nonoAgendaShow;?>"/>
								<input type="hidden" name="noagenda_custom" value="<?php echo $noAgenda;?>"/>
								<input type="hidden" name="id_klasifikasi" value="<?php echo $id_klasifikasi;?>"/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nomor Surat *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan nomor surat keluar." title="Nomor Surat Keluar">?</span>
							<div class="col-sm-4">
								<input class="form-control" placeholder="Nomor surat keluar" type="text" name="nosk" <?php if(isset($nosk)){ echo $nosk; }?> id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tanggal Surat *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan tanggal pada surat keluar. ex. 01-12-2015" title="Tanggal Surat">?</span>
							<div class="col-sm-3">
								<input class="form-control date-picker" id="id-date-picker-1" data-date-format="dd-mm-yyyy" placeholder="Tanggal surat keluar" type="text" name="tglsk" <?php if(isset($tgl_surat)){ echo $tgl_surat; }?> id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Pengolah *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan nama/bagian pengolah surat(nama perorangan/bagian)." title="Pengolah Surat">?</span>
							<div class="col-sm-8">
								<input class="form-control" data-rel="tooltip" placeholder="Nama atau bagian pengolah surat" type="text" name="pengolah" <?php if(isset($pengolah)){ echo $pengolah; }?> title="Di isi dengan nama atau bagian yang mengolah surat(nama perorangan/bagian)." data-placement="bottom" id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tujuan Surat *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai dengan tujuan surat (nama lembaga atau perorangan)." title="Tujuan Surat">?</span>
							<div class="col-sm-8">
								<input class="form-control" placeholder="Nama lembaga / Perorangan" type="text" name="tujuan" <?php if(isset($tujuan_surat)){ echo $tujuan_surat; }?> id="form-field-mask-1" required/>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Perihal *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi sesuai perihal atau subjek surat keluar." title="Perihal">?</span>
							<div class="col-sm-9">
								<textarea class="form-control limited" placeholder="Perihal/subjek surat" name="perihal" id="form-field-9" maxlength="150" required><?php if(isset($perihal)){ echo $perihal; }?></textarea>
							</div>
						</div>
						<div class="space-4"></div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Keterangan </label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan keterangan tambahan jika ada." title="Keterangan">?</span>
							<div class="col-sm-9">
								<textarea class="form-control limited" placeholder="Keterangan tambahan (jika ada)" name="ket" id="form-field-9" maxlength="150"><?php if(isset($ket)){ echo $ket; }?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> <?php echo $ketfile;?></label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih File surat keluar yang ingin di upload. Caranya klik menu Pilih File. Tipe file : .pdf, .jpg, .png" title="File surat keluar">?</span>
							<div class="col-sm-4">
								<input class="form-control" type="file" name="filesk" id="id-input-file-1" <?php if(isset($validasifile)){ echo $validasifile; }?>/>
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