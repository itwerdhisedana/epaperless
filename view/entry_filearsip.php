<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_GET['id_arsip'])){
		$id_arsip = htmlspecialchars($purifier->purify(trim($_GET['id_arsip'])), ENT_QUOTES);
	}
	$id_klasifikasi = htmlspecialchars($purifier->purify(trim($_POST['id_klasifikasi'])), ENT_QUOTES);
	$ket = htmlspecialchars($purifier->purify(trim($_POST['ket'])), ENT_QUOTES);
	
	$fileName = htmlspecialchars($_FILES['file_arsip']['name'], ENT_QUOTES);
	$tipefile = pathinfo($fileName,PATHINFO_EXTENSION);
	$extensionList = array("pdf","doc","docx","xls","xlsx","ppt","pptx","jpg","jpeg","png","zip","rar","gif");
	$namaDir = 'berkas/';
	$fileArsip = $namaDir."ARSIP"."_". slugify($fileName)."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
	$filedb = "ARSIP"."_". slugify($fileName)."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
	$tgl_upload = date("Y-m-d");
	
	$no_arsip = htmlspecialchars($purifier->purify(trim($_POST['no_arsip'])), ENT_QUOTES);
	$tgl_arsip = htmlspecialchars($purifier->purify(trim($_POST['tgl_arsip'])), ENT_QUOTES);
	$tgl_arsip = explode("-",$tgl_arsip);
	$tgl_arsipdb = $tgl_arsip[2]."-".$tgl_arsip[1]."-".$tgl_arsip[0];
	$keamanan = htmlspecialchars($purifier->purify(trim($_POST['keamanan'])), ENT_QUOTES);
	
	//echo "$filesk <br/>";
	//print_r($_POST);
	if(isset($_GET['id_arsip'])){
		$id_arsip = htmlspecialchars($purifier->purify(trim($_GET['id_arsip'])), ENT_QUOTES);
		$params = array(':id_arsip' => $id_arsip);
		$DataArsip = $this->model->selectprepare("arsip_file", $field=null, $params, "id_arsip=:id_arsip");
		if($DataArsip->rowCount() >= 1){
			$LihatDataArsip = $DataArsip->fetch(PDO::FETCH_OBJ);
			$idArsip = $LihatDataArsip->id_arsip;
			if(empty($fileName)){
				$field = array('no_arsip' => $no_arsip, 'tgl_arsip' => $tgl_arsipdb, 'no_arsip' => $no_arsip, 'keamanan' => $keamanan, 'id_klasifikasi' => $id_klasifikasi, 'ket' => $ket);
				$params = array(':id_arsip' => $idArsip);
				$update = $this->model->updateprepare("arsip_file", $field, $params, "id_arsip=:id_arsip");
				if($update){
					echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=arsip_file&id_arsip=$idArsip\";</script>";
				}else{
					die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}else{
				//echo "Update File $fileName";
				//if(in_array($tipefile, $extensionList)){
					@unlink($namaDir.$LihatDataArsip->file_arsip);
					//'pengolah' => $pengolah,
					$field = array('id_klasifikasi' => $id_klasifikasi, 'ket' => $ket, 'file_arsip' => $filedb, 'no_arsip' => $no_arsip, 'tgl_arsip' => $tgl_arsipdb, 'no_arsip' => $no_arsip, 'keamanan' => $keamanan);
					move_uploaded_file($_FILES['file_arsip']['tmp_name'], $fileArsip);
					$params = array(':id_arsip' => $idArsip);
					$update = $this->model->updateprepare("arsip_file", $field, $params, "id_arsip=:id_arsip");
					if($update){
						echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=arsip_file&id_arsip=$idArsip\";</script>";
					}else{
						die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
					}
				/*}else{
					echo "<script type=\"text/javascript\">alert('File gagal di Upload, Format file tidak di dukung!!! Format yang didukung adalah PDF');window.history.go(-1);</script>";
				}*/
			}
		}
	}else{
		$field = array('id_user' => $_SESSION['id_user'],'id_klasifikasi'=>$id_klasifikasi, 'ket'=>$ket, 'file_arsip'=>$filedb, 'tgl_upload'=>$tgl_upload, 'no_arsip' => $no_arsip, 'tgl_arsip' => $tgl_arsipdb, 'no_arsip' => $no_arsip, 'keamanan' => $keamanan);
		$params = array(':id_user' => $_SESSION['id_user'], ':id_klasifikasi'=>$id_klasifikasi, ':ket'=>$ket, ':file_arsip'=>$filedb, ':tgl_upload'=>$tgl_upload, ':no_arsip' => $no_arsip, ':tgl_arsip' => $tgl_arsipdb, ':no_arsip' => $no_arsip, ':keamanan' => $keamanan);
		//if(in_array($tipefile, $extensionList)){
			if(move_uploaded_file($_FILES['file_arsip']['tmp_name'], $fileArsip)){
				$insert = $this->model->insertprepare("arsip_file", $field, $params);
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
	if(isset($_GET['id_arsip'])){
		$id_arsip = htmlspecialchars($purifier->purify(trim($_GET['id_arsip'])), ENT_QUOTES);
		$params = array(':id_arsip' => $id_arsip);
		$cekArsip = $this->model->selectprepare("arsip_file", $field=null, $params, "id_arsip=:id_arsip");
		if($cekArsip->rowCount() >= 1){
			$dataCekArsip = $cekArsip->fetch(PDO::FETCH_OBJ);
			if(isset($_GET['act']) && $_GET['act'] == "del"){
				@unlink('berkas/'.$dataCekArsip->file_arsip);
				$params = array(':id_arsip' => $id_arsip);
				$delete = $this->model->hapusprepare("arsip_file", $params, "id_arsip=:id_arsip");
				if($delete){
					echo "<script type=\"text/javascript\">alert('Data Berhasil di Hapus...!!');window.location.href=\"./index.php?op=arsip_file\";</script>";
				}else{
					die("<script>alert('Gagal menghapus data surat keluar, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}
			$title= "Edit File Arsip";
			$ketfile = "File Arsip";
			$id_klasifikasi = $dataCekArsip->id_klasifikasi;
			$ket = $dataCekArsip->ket;
			$no_arsip = 'value="'.$dataCekArsip->no_arsip.'"';
			$keamanan = $dataCekArsip->keamanan;
			$tgl_arsip = explode("-", $dataCekArsip->tgl_arsip);
			$tgl_arsip = $tgl_arsip[2]."-".$tgl_arsip[1]."-".$tgl_arsip[0];
			$tgl_arsip = 'value="'.$tgl_arsip.'"';
		}else{
			$title= "Entri File Arsip";
			$ketfile = "File File Arsip *";
			$validasifile = "required";
		}
	}else{
		$title= "Entri File Arsip";
		$ketfile = "File Arsip *";
		$validasifile = "required";
	}
	$cek_noarsip = $this->model->selectprepare("arsip_file", $field=null, $params=null, $where=null, "ORDER BY id_arsip DESC LIMIT 1");
	if($cek_noarsip->rowCount() >= 1){
		$data_cek_noarsip = $cek_noarsip->fetch(PDO::FETCH_OBJ);
		if(isset($_GET['id_arsip']) AND $_GET['id_arsip'] == $data_cek_noarsip->id_arsip){
			$noArsip = 'value="'.sprintf("%04d", $data_cek_noarsip->id_arsip).'"';
			$noArsipShow = $data_cek_noarsip->id_arsip;
		}else{
			$noArsip = 'value="'.sprintf("%04d", $data_cek_noarsip->id_arsip+1).'"';
			$noArsipShow = $data_cek_noarsip->id_arsip+1;
		}
	}else{
		$noArsip = 'value="'.sprintf("%04d", 1).'"';
		$noArsipShow = 1;
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
				<form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nomor Arsip *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Isi dengan nomor arsip." title="Nomor Arsip">?</span>
						<div class="col-sm-2">
							<input class="form-control" placeholder="Nomor arsip" type="text" name="no_arsip" <?php if(isset($noArsip)){ echo $noArsip; }?> id="form-field-mask-1" required disabled />
							<input type="hidden" name="no_arsip" value="<?php echo $noArsipShow;?>"/>
						</div>
					</div>
					<div class="space-4"></div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tanggal Surat *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih tanggal arsip." title="Tanggal Arsip">?</span>
						<div class="col-sm-2">
							<input class="form-control date-picker" id="id-date-picker-1" data-date-format="dd-mm-yyyy" placeholder="Tanggal arsip" type="text" name="tgl_arsip" <?php if(isset($tgl_arsip)){ echo $tgl_arsip; }?> id="form-field-mask-1" required />
						</div>
					</div>
					<div class="space-4"></div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Tingkat Keamanan *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih tingkat keamanan arsip." title="Tingkat keamanan">?</span>
						<div class="col-sm-2">
							<select class="form-control" id="form-field-select-1" name="keamanan" required><?php
								$Arr_keamanan = array("Biasa/Terbuka", "Terbatas", "Rahasia", "Sangat Rahasia");
								foreach($Arr_keamanan as $tingkat){
									if(isset($keamanan) AND $keamanan == $tingkat){?>
										<option value="<?php echo $tingkat;?>" selected><?php echo $tingkat;?></option><?php
									}else{?>
										<option value="<?php echo $tingkat;?>"><?php echo $tingkat;?></option><?php
									}
								}?>
							</select>
						</div>
					</div>
					<div class="space-4"></div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1">Klasifikasi File *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih klasifikasi arsip." title="Klasifikasi">?</span>
						<div class="col-sm-3">
							<select class="form-control" id="form-field-select-3" name="id_klasifikasi" data-placeholder="Pilih Klasifikasi..." required>
								<option value="">Pilih Klasifikasi</option><?php
								$KlasArsip= $this->model->selectprepare("klasifikasi_arsip", $field=null, $params=null, $where=null, "ORDER BY nama_klasifikasi ASC");
								if($KlasArsip->rowCount() >= 1){
									while($dataKlasArsip = $KlasArsip->fetch(PDO::FETCH_OBJ)){
										if(isset($id_klasifikasi) && $id_klasifikasi == $dataKlasArsip->id_klasifikasi){?>
											<option value="<?php echo $dataKlasArsip->id_klasifikasi;?>" selected><?php echo $dataKlasArsip->nama_klasifikasi;?></option><?php
										}else{?>
											<option value="<?php echo $dataKlasArsip->id_klasifikasi;?>"><?php echo $dataKlasArsip->nama_klasifikasi;?></option><?php
										}
									}
								}else{?>
									<option value="">Data klasifikasi belum ada</option><?php
								}?>
							</select>
						</div>
					</div>
					<div class="space-4"></div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> <?php echo $ketfile;?></label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih file yang ingin di upload. Caranya klik menu Pilih File. Tipe file : .pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .jpg, .png, .zip, .rar" title="File surat keluar">?</span>
						<div class="col-sm-4">
							<input class="form-control" type="file" name="file_arsip" id="id-input-file-1" <?php if(isset($validasifile)){ echo $validasifile; }?>/>
						</div>
					</div>
					<div class="space-4"></div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Keterangan</label>
						<div class="col-sm-6">
							<textarea class="form-control limited" placeholder="Keterangan" name="ket" id="form-field-9" maxlength="150"><?php if(isset($ket)){ echo $ket; }?></textarea>
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
				</form>
			</div>
		</div>
	</div><?php
}?>