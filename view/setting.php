<?php
if(isset($_GET['act']) AND $_GET['act'] == "email_notif"){
	require "email_setting.php";
}else{
	$params = array(':id' => 1);
	$CekSetting = $this->model->selectprepare("pengaturan", $field=null, $params, "id=:id");
	if($CekSetting->rowCount() >= 1){
		$dataCekSetting = $CekSetting->fetch(PDO::FETCH_OBJ);
		$title = $dataCekSetting->title;
		$deskripsi = $dataCekSetting->deskripsi;
		$logo = $dataCekSetting->logo;
		$email = 'value="'.$dataCekSetting->email .'"';
		$pass_email = 'value="'.$dataCekSetting->pass_email .'"';
		$no_agenda_sm_start = 'value="'.$dataCekSetting->no_agenda_sm_start .'"';
		$no_agenda_sm = 'value="'.$dataCekSetting->no_agenda_sm .'"';
		$no_agenda_sk_start = 'value="'.$dataCekSetting->no_agenda_sk_start .'"';
		$no_agenda_sk = 'value="'.$dataCekSetting->no_agenda_sk .'"';
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST"){
		if(isset($_GET['act']) AND isset($_GET['idkop'])){
			if($_GET['idkop'] == 1){
				$params1 = array(':idkop' => 1);
			}elseif($_GET['idkop'] == 2){
				$params1 = array(':idkop' => 2);
			}elseif($_GET['idkop'] == 3){
				$params1 = array(':idkop' => 3);
			}
			$kopdefault = htmlspecialchars($purifier->purify(trim($_POST['kopsurat'])), ENT_QUOTES);
			$status = htmlspecialchars($purifier->purify(trim($_POST['status'])), ENT_QUOTES);
			$layout = $_POST['layout'];
			$field = array('kopdefault' => $kopdefault, 'layout' => $layout, 'status' => $status);
			$update = $this->model->updateprepare("kop_setting", $field, $params1, "idkop=:idkop");
			if($update){
				echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=setting&act=kopterima&idkop=".$_GET['idkop']."\";</script>";
			}else{
				die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}else{
			$title = htmlspecialchars($purifier->purify(trim($_POST['title'])), ENT_QUOTES);
			$deskripsi = htmlspecialchars($purifier->purify(trim($_POST['deskripsi'])), ENT_QUOTES);
			$email = htmlspecialchars($purifier->purify(trim($_POST['email'])), ENT_QUOTES);
			$pass_email = htmlspecialchars($purifier->purify(trim($_POST['pass_email'])), ENT_QUOTES);
			$no_agenda_sm_start = htmlspecialchars($purifier->purify(trim($_POST['no_agenda_sm_start'])), ENT_QUOTES);
			$no_agenda_sm = htmlspecialchars($purifier->purify(trim($_POST['no_agenda_sm'])), ENT_QUOTES);
			$no_agenda_sk_start = htmlspecialchars($purifier->purify(trim($_POST['no_agenda_sk_start'])), ENT_QUOTES);
			$no_agenda_sk = htmlspecialchars($purifier->purify(trim($_POST['no_agenda_sk'])), ENT_QUOTES);

			$fileName = htmlspecialchars($_FILES['filelogo']['name'], ENT_QUOTES);
			$tipefile = pathinfo($fileName,PATHINFO_EXTENSION);
			$extensionList = array("jpg","jpeg","png");
			$namaDir = 'foto/';
			$upfileLogo = $namaDir."KOP"."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
			$dbfileLogo= "KOP"."_". date("d-m-Y_H-i-s", time()) .".".$tipefile;
			$tgl_upload = date("Y-m-d H:i:s", time());
			
			/*update Konseptor*/
			if(empty($fileName)){
				$field = array('title' => $title, 'deskripsi' => $deskripsi, 'email' => $email, 'pass_email' => $pass_email, 'no_agenda_sm_start' => $no_agenda_sm_start, 'no_agenda_sm' => $no_agenda_sm, 'no_agenda_sk_start' => $no_agenda_sk_start, 'no_agenda_sk' => $no_agenda_sk);
				$update = $this->model->updateprepare("pengaturan", $field, $params, "id=:id");
				if($update){
					echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=setting\";</script>";
				}else{
					die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}else{
				//echo "Update File $fileName";
				if(in_array($tipefile, $extensionList)){
					if(move_uploaded_file($_FILES['filelogo']['tmp_name'], $upfileLogo)){
						@unlink($namaDir.$logo);
						$field = array('title' => $title, 'deskripsi' => $deskripsi, 'logo' => $dbfileLogo, 'email' => $email, 'pass_email' => $pass_email, 'no_agenda_sm_start' => $no_agenda_sm_start, 'no_agenda_sm' => $no_agenda_sm, 'no_agenda_sk_start' => $no_agenda_sk_start, 'no_agenda_sk' => $no_agenda_sk);
						$update = $this->model->updateprepare("pengaturan", $field, $params, "id=:id");
						if($update){
							echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=setting\";</script>";
						}else{
							die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
						}
					}else{
						echo "<script type=\"text/javascript\">alert('File gagal di Upload ke Folder, Silahkan ulangi!!!');window.history.go(-1);</script>";
					}
				}else{
					echo "<script type=\"text/javascript\">alert('File gagal di Upload, Format file tidak di dukung!!!');window.history.go(-1);</script>";
				}
			}
		}
		
	}else{?>
		<div class="widget-box"><?php
			$VarDinamis = "";
			$VarSetting = $this->model->selectprepare("kop_variabel", $field=null, $params=null, $where=null);
			while($dataVarSetting = $VarSetting->fetch(PDO::FETCH_OBJ)){
				$dinamasiVar = explode(',', $dataVarSetting->id_kop);
				//print_r($dinamasiVar);
				if($dinamasiVar[0] == $_GET['idkop'] OR $dinamasiVar[1] == $_GET['idkop'] OR $dinamasiVar[2] == $_GET['idkop']){
					$VarDinamis .= $dataVarSetting->variabel .' | '.$dataVarSetting->ket .'<br/>';
				}
			}
			if(isset($_GET['act']) AND isset($_GET['idkop'])){
				if($_GET['idkop'] == 1){
					$params = array(':idkop' => 1);
				}elseif($_GET['idkop'] == 2){
					$params = array(':idkop' => 2);
				}elseif($_GET['idkop'] == 3){
					$params = array(':idkop' => 3);
				}
				$KopTerima = $this->model->selectprepare("kop_setting", $field=null, $params, "idkop=:idkop");
				if($KopTerima->rowCount() >= 1){
					$dataKopTerima = $KopTerima->fetch(PDO::FETCH_OBJ);?>
					<div class="widget-header">
						<h4 class="widget-title">Konfigurasi <?php echo $dataKopTerima->ket;?></h4>
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
						<div class="widget-main"><?php?>
							<script src="./tinymce/tinymce.min.js"></script>
							<script>
								tinymce.init({
								  selector: 'textarea',
								  height: 200,
								  theme: 'modern',
								  plugins: [
									'advlist autolink lists link image charmap print preview hr anchor pagebreak',
									'searchreplace wordcount visualblocks visualchars code fullscreen',
									'insertdatetime media nonbreaking save table contextmenu directionality',
									'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
								  ],
								  toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
								  toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
								  image_advtab: true,
								  templates: [
									{ title: 'Test template 1', content: 'Test 1' },
									{ title: 'Test template 2', content: 'Test 2' }
								  ]
								 });
							</script>
							<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" name="formku" action="<?php echo $_SESSION['url'];?>">
								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Aktifkan Layout*</label>
									<div class="radio"><?php
										if($dataKopTerima->status == "Y"){?>
											<label>
												<input name="status" type="radio" value="Y" class="ace" required="required" checked />
												<span class="lbl"> Ya</span>
											</label>
											<label>
												<input name="status" type="radio" value="N" class="ace" required="required" />
												<span class="lbl"> Tidak</span>
											</label><?php
										}else{?>
											<label>
												<input name="status" type="radio" value="Y" class="ace" required="required" />
												<span class="lbl"> Ya</span>
											</label>
											<label>
												<input name="status" type="radio" value="N" class="ace" required="required" checked />
												<span class="lbl"> Tidak</span>
											</label><?php
										}?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Kop Default*</label>
									<div class="radio"><?php
										if($dataKopTerima->kopdefault == "Y"){?>
											<label>
												<input name="kopsurat" type="radio" value="1" class="ace" required="required" checked />
												<span class="lbl"> Ya</span>
											</label>
											<label>
												<input name="kopsurat" type="radio" value="0" class="ace" required="required" />
												<span class="lbl"> Tidak</span>
											</label><?php
										}else{?>
											<label>
												<input name="kopsurat" type="radio" value="1" class="ace" required="required" />
												<span class="lbl"> Ya</span>
											</label>
											<label>
												<input name="kopsurat" type="radio" value="0" class="ace" required="required" checked />
												<span class="lbl"> Tidak</span>
											</label><?php
										}?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Format Kop Surat Terima*</label>
									<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan deskripsi aplikasi/perusahaan" title="Deskripsi">?</span>
									<div class="col-sm-8">
										<textarea class="form-control limited" name="layout"/><?php echo $dataKopTerima->layout;?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Variabel dinamis yang tersedia*</label>
									<div class="col-sm-6">
										<?php echo $VarDinamis;?>
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
										&nbsp; &nbsp; &nbsp;
										<div class="col-sm-2">
										<button class="btn" type="reset">
											<i class="ace-icon fa fa-undo bigger-110"></i>
											Reset
										</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div><?php
				}
			}else{?>
				<div class="widget-header">
					<h4 class="widget-title">Konfigurasi SIAS</h4>
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
						<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" name="formku" action="<?php echo $_SESSION['url'];?>">
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Title*</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan nama aplikasi/perusahaan." title="Nama">?</span>
								<div class="col-sm-6">
									<textarea class="form-control limited" name="title" id="form-field-mask-1" required/><?php if(isset($title)){ echo $title; }?></textarea>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Deskripsi*</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan deskripsi aplikasi/perusahaan" title="Deskripsi">?</span>
								<div class="col-sm-8">
									<textarea class="form-control limited" name="deskripsi" required/><?php if(isset($deskripsi)){ echo $deskripsi; }?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Gambar Kop Surat*</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih kop surat yang ingin di upload. Caranya klik menu Pilih File. Tipe file : .jpg, .png" title="File kop surat">?</span>
								<div class="col-sm-4">
									<input type="file" class="form-control" name="filelogo" value="<?php echo $logo;?>" id="id-input-file-1"/>
								</div>
								
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Kop Surat</label>
								<div class="col-sm-2">
									<ul class="ace-thumbnails clearfix">
										<li>
											<a href="./foto/<?php echo $logo;?>" data-rel="colorbox">
												<img width="700" height="100" src="./foto/<?php echo $logo;?>" />
												<div class="text">
													<div class="inner"><?php echo $logo;?></div>
												</div>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Email</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan alamat email yang akan digunakan untuk mengirim notifikasi." title="Email">?</span>
								<div class="col-sm-5">
									<input type="email" class="form-control" placeholder="Isi dengan alamat email pengirim notifikasi" name="email" <?php if(isset($email)){ echo $email; }?> id="form-field-mask-1"/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Password Email</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan password login email." title="Email">?</span>
								<div class="col-sm-4">
									<input type="text" class="form-control" placeholder="password email pengirim notifikasi" name="pass_email" <?php if(isset($pass_email)){ echo $pass_email; }?> id="form-field-mask-1"/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nilai awal no agenda surat masuk</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Isi dengan nomor agenda surat masuk. Jika nilai kosong maka nomor agenda otomatis dimulai dari 1" title="Nilai Awal Agenda Surat Masuk">?</span>
								<div class="col-sm-4">
									<input type="text" class="form-control" placeholder="Nomor agenda surat masuk dimulai dari" name="no_agenda_sm_start" <?php if(isset($no_agenda_sm_start)){ echo $no_agenda_sm_start; }?> id="form-field-mask-1"/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Kode tambahan no agenda surat masuk</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Isi dengan kode tambahan untuk nomor agenda surat masuk (optional)." title="Kode Tambahan Agenda Surat Masuk">?</span>
								<div class="col-sm-4">
									<input type="text" class="form-control" placeholder="Kode tambahan untuk nomor agenda surat masuk" name="no_agenda_sm" <?php if(isset($no_agenda_sm)){ echo $no_agenda_sm; }?> id="form-field-mask-1"/>
									<span class="help-block">Note Variabel: <br/><b>=Tahun=</b> | untuk nilai tahun surat masuk otomatis <br/><b>=KodeSurat=</b> | untuk kode jenis surat masuk Otomatis</span>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nilai awal no agenda surat keluar</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Isi dengan nomor agenda surat keluar. Jika nilai kosong maka nomor agenda otomatis dimulai dari 1" title="Nilai Awal Agenda Surat Keluar">?</span>
								<div class="col-sm-4">
									<input type="text" class="form-control" placeholder="Nomor agenda surat keluar dimulai dari" name="no_agenda_sk_start" <?php if(isset($no_agenda_sk_start)){ echo $no_agenda_sk_start; }?> id="form-field-mask-1"/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Kode tambahan no agenda surat keluar</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Isi dengan kode tambahan untuk nomor agenda surat keluar (optional)" title="Kode Tambahan Agenda Surat Keluar">?</span>
								<div class="col-sm-4">
									<input type="text" class="form-control" placeholder="Kode tambahan untuk nomor agenda surat keluar" name="no_agenda_sk" <?php if(isset($no_agenda_sk)){ echo $no_agenda_sk; }?> id="form-field-mask-1"/>
									<span class="help-block">Note Variabel: <br/><b>=Tahun=</b> | untuk nilai tahun surat keluar otomatis <br/><b>=KodeSurat=</b> | untuk kode jenis surat keluar otomatis</span>
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
									&nbsp; &nbsp; &nbsp;
									<div class="col-sm-2">
									<button class="btn" type="reset">
										<i class="ace-icon fa fa-undo bigger-110"></i>
										Reset
									</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div><?php
			}?>
			<div class="space-4"></div>
			<hr/>
			<a href="./index.php?op=setting" title="Pengaturan Group User">
				<button class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-cog bigger-120 blue"></i>Konfigurasi Umum
				</button>
			</a>
			<a href="./index.php?op=setting&act=kopterima&idkop=1" title="Pengaturan Kop Terima Surat">
				<button class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-cog bigger-120 blue"></i>Kop Terima
				</button>
			</a>
			<a href="./index.php?op=setting&act=kopdetail&idkop=2" title="Pengaturan Kop Detail Surat">
				<button class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-cog bigger-120 blue"></i>Kop Detail Surat
				</button>
			</a>
			<a href="./index.php?op=setting&act=kopdisposisi&idkop=3" title="Pengaturan Kop Disposisi Surat">
				<button class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-cog bigger-120 blue"></i>Kop Disposisi
				</button>
			</a>
			<a href="./index.php?op=setting&act=email_notif" title="Pengaturan Email Notifikasi Surat">
				<button class="btn btn-white btn-info btn-bold">
					<i class="ace-icon fa fa-cog bigger-120 blue"></i>Email Notifikasi
				</button>
			</a>
		</div><?php
	}
}?>