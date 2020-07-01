<?php
$created = date("Y-m-d H:i:s", time());
if(isset($_GET['act']) && $_GET['act'] == "jabatan"){
	$nama_jab = $purifier->purify(trim($_POST['jabatan']));
	if(isset($_GET['id_jab'])){
		$id_jab = htmlspecialchars($purifier->purify(trim($_GET['id_jab'])), ENT_QUOTES);
		$params = array(':id_jab' => $id_jab);
		$CekJab = $this->model->selectprepare("user_jabatan", $field=null, $params, "id_jab=:id_jab");
		if($CekJab->rowCount() >= 1){
			$DataJab = $CekJab->fetch(PDO::FETCH_OBJ);
			$title= "Edit Data Jabatan";
			$valueNamaJab = 'value="'.$DataJab->nama_jabatan .'"';
			if($_SERVER["REQUEST_METHOD"] == "POST" AND !isset($_GET['act2'])){
				if($nama_jab == ''){
					die("<script>alert('Nama jabatan tidak boleh kosong..!!');window.history.go(-1);</script>");
				}else{
					$field = array('nama_jabatan' => $nama_jab);
					$params = array(':id_jab' => $id_jab);
					$update = $this->model->updateprepare("user_jabatan", $field, $params, "id_jab=:id_jab");
					if($update){
						echo "<script type=\"text/javascript\">alert('Data Jabatan Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=user&act=jabatan\";</script>";
					}else{
						die("<script>alert('Gagal Update Data, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
					}
				}
			}
		}else{
			$title= "Entri Data Jabatan";
		}
	}else{
		$title= "Entri Data Jabatan";
		if($_SERVER["REQUEST_METHOD"] == "POST" AND !isset($_GET['act2'])){
			if($nama_jab == ''){
				die("<script>alert('Nama jabatan tidak boleh kosong..!!');window.history.go(-1);</script>");
			}else{
				$field = array('nama_jabatan' => $nama_jab, 'created' => $created);
				$params = array(':nama_jabatan' => $nama_jab, ':created' => $created);
				$insert = $this->model->insertprepare("user_jabatan", $field, $params);
				if($insert->rowCount() >= 1){
					echo "<script type=\"text/javascript\">alert('Data Jabatan Berhasil Tersimpan...!!');window.location.href=\"./index.php?op=user&act=jabatan\";</script>";
				}else{
					die("<script>alert('Data Gagal disimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}
		}
	}
	
	if(isset($_GET['act2']) && $_GET['act2'] == "del" && isset($_GET['id_jab'])){
		$params = array(':jabatan' => $_GET['id_jab']);
		$CekUser = $this->model->selectprepare("user", $field=null, $params, "jabatan=:jabatan");
		if($CekUser->rowCount() >= 1){
			die("<script>alert('Nama Jabatan ini tidak dapat dihapus karena terkait dengan user. Jika tetap ingin menghapus, silahkan hapus data user yang terkait dengan jabatan ini terlebih dahulu. Terimakasih');window.history.go(-1);</script>");
		}else{
			$params = array(':id_jab' => $_GET['id_jab']);
			$delete = $this->model->hapusprepare("user_jabatan", $params, "id_jab=:id_jab");
			if($delete){
				echo "<script type=\"text/javascript\">alert('Data Berhasil di Hapus...!!');window.location.href=\"./index.php?op=user&act=jabatan\";</script>";
			}else{
				die("<script>alert('Gagal menghapus data User, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}
	}else{?>
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
					<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nama Jabatan *</label>
							<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan nama jabatan." title="Nama Level">?</span>
							<div class="col-sm-5">
								<input class="form-control" placeholder="Nama Jabatan" type="text" name="jabatan" <?php if(isset($valueNamaJab)){ echo $valueNamaJab; }?> id="form-field-mask-1" required />
							</div>
						</div>
						<div class="space-4"></div>
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
			</div><?php
	}
	$GetJab = $this->model->selectprepare("user_jabatan", $field=null, $params=null, $where=null, "order by nama_jabatan ASC");
	if($GetJab->rowCount() >= 1){
		while($DataGetJab = $GetJab->fetch(PDO::FETCH_OBJ)){
			$dump_jab[]=$DataGetJab;
		}?>
		<div class="widget-body">
			<div class="widget-main">
				<a href="./index.php?op=user" title="Data User">
					<button class="btn btn-white btn-info btn-bold">
						<i class="ace-icon fa fa-users bigger-120 blue"></i>User
					</button>
				</a>
				<a href="./index.php?op=user&act=jabatan" title="Pengaturan Unit Kerja User">
					<button class="btn btn-white btn-info btn-bold">
						<i class="ace-icon fa fa-users bigger-120 blue"></i>Jabatan
					</button>
				</a>
				<div class="space-4"></div>
				<table id="simple-table" class="table  table-bordered table-hover">
					<thead>
						<tr>
							<th width=10>No</th>
							<th width=150>Nama Jabatan</th>
							<th width=10>ACT</th>
						</tr>
					</thead>
					<tbody><?php
						$no=1;
						foreach($dump_jab as $key => $object){?>
							<tr>
								<td><?php echo $no;?></td>
								<td><?php echo $object->nama_jabatan;?></td>
								<td>
									<div class="hidden-sm hidden-xs btn-group">
										<a href="./index.php?op=user&act=jabatan&id_jab=<?php echo $object->id_jab;?>">						
											<button class="btn btn-minier btn-info">
												<i class="ace-icon fa fa-pencil bigger-100"></i>
											</button>
										</a>
										<a href="./index.php?op=user&act=jabatan&id_jab=<?php echo $object->id_jab;?>&act2=del" onclick="return confirm('Anda yakin akan menghapus data ini??')">
											<button class="btn btn-minier btn-danger">
												<i class="ace-icon fa fa-trash-o bigger-110"></i>
											</button>
										</a>
									</div>
								</td>
							</tr><?php
						$no++;
						}?>
					</tbody>
				</table>
			</div>
		</div>
		</div><?php
	}
}else{
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$nik = htmlspecialchars($purifier->purify(trim($_POST['nik'])), ENT_QUOTES);
		$nama = htmlspecialchars($purifier->purify(trim($_POST['nama'])), ENT_QUOTES);
		$uname = htmlspecialchars($purifier->purify(trim($_POST['uname'])), ENT_QUOTES);
		$upass = htmlspecialchars($purifier->purify(trim($_POST['upass'])), ENT_QUOTES);
		$disposisi = json_encode($_POST['disposisi']);
		$email = htmlspecialchars($purifier->purify(trim($_POST['email'])), ENT_QUOTES);
		$jabatan = htmlspecialchars($purifier->purify(trim($_POST['jabatan'])), ENT_QUOTES);
		$akses_sm = htmlspecialchars($purifier->purify(trim($_POST['akses_sm'])), ENT_QUOTES);
		$akses_sk = htmlspecialchars($purifier->purify(trim($_POST['akses_sk'])), ENT_QUOTES);
		$akses_arsip = htmlspecialchars($purifier->purify(trim($_POST['akses_arsip'])), ENT_QUOTES);
		$akses_cari_surat_masuk = htmlspecialchars($purifier->purify(trim($_POST['akses_carism'])), ENT_QUOTES);
		//$akses_cari_surat_keluar = htmlspecialchars($purifier->purify(trim($_POST['akses_carisk'])), ENT_QUOTES);
		//$akses_template_surat = htmlspecialchars($purifier->purify(trim($_POST['akses_template_surat'])), ENT_QUOTES);
		//$akses_arsip = htmlspecialchars($purifier->purify(trim($_POST['akses_arsip'])), ENT_QUOTES);
		$akses_report_sm = htmlspecialchars($purifier->purify(trim($_POST['akses_reportsm'])), ENT_QUOTES);
		$akses_report_sk = htmlspecialchars($purifier->purify(trim($_POST['akses_reportsk'])), ENT_QUOTES);
		$akses_report_arsip = htmlspecialchars($purifier->purify(trim($_POST['akses_reportarsip'])), ENT_QUOTES);
		$akses_reportprogress = htmlspecialchars($purifier->purify(trim($_POST['akses_reportprogress'])), ENT_QUOTES);
		$akses_reportprogress = htmlspecialchars($purifier->purify(trim($_POST['akses_reportprogress'])), ENT_QUOTES);
		$akses_atur_layout = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_layout'])), ENT_QUOTES);
		$akses_report_dispo = htmlspecialchars($purifier->purify(trim($_POST['akses_reportdispo'])), ENT_QUOTES);
		$akses_atur_klasifikasi_sm = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_klas_sm'])), ENT_QUOTES);
		$akses_atur_klasifikasi_sk = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_klas_sk'])), ENT_QUOTES);
		$akses_atur_info = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_info'])), ENT_QUOTES);
		$akses_atur_klasifikasi_arsip = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_klas_arsip'])), ENT_QUOTES);
		$akses_atur_user = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_user'])), ENT_QUOTES);
		//$akses_atur_infoapp = htmlspecialchars($purifier->purify(trim($_POST['akses_atur_infoapp'])), ENT_QUOTES);
		$picture = "sekretaris.png";
		if(isset($_GET['userid'])){
			$id_user = htmlspecialchars($purifier->purify(trim($_GET['userid'])), ENT_QUOTES);
			$params = array(':id_user' => $id_user);
			$user = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user");
			if($user->rowCount() >= 1){
				$data_user = $user->fetch(PDO::FETCH_OBJ);
				$id_user = $data_user->id_user;
				if(empty($upass)){
					$field = array('nama' => $nama, 'nik' => $nik, 'uname' => $uname, 'jabatan' => $jabatan, 'email' => $email, 'rule_disposisi' => $disposisi);
				}else{
					$upass = md5($upass);
					$field = array('nama' => $nama, 'nik' => $nik, 'uname' => $uname,  'upass' => $upass, 'email' => $email, 'rule_disposisi' => $disposisi, 'jabatan' => $jabatan);
				}
				$params = array(':id_user' => $id_user);
				$update = $this->model->updateprepare("user", $field, $params, "id_user=:id_user");
				if($update){
					$CekLevel = $this->model->selectprepare("user_level", $field=null, $params, "id_user=:id_user");
					if($CekLevel->rowCount() >= 1){
						$field = array('sm' => $akses_sm, 'sk' => $akses_sk, 'arsip' => $akses_arsip, 'atur_layout' => $akses_atur_layout, 'atur_klasifikasi_sm' => $akses_atur_klasifikasi_sm, 'atur_klasifikasi_sk' => $akses_atur_klasifikasi_sk, 'atur_klasifikasi_arsip' => $akses_atur_klasifikasi_arsip, 'atur_user' => $akses_atur_user, 'report_sm' => $akses_report_sm, 'report_sk' => $akses_report_sk, 'report_arsip' => $akses_report_arsip,'report_dispo' => $akses_report_dispo,'report_progress' => $akses_reportprogress,'info' => $akses_atur_info);
						$params = array(':id_user' => $id_user);
						$updateLevel = $this->model->updateprepare("user_level", $field, $params, "id_user=:id_user");
					}else{
						$field = array('id_user' => $id_user, 'sm' => $akses_sm, 'sk' => $akses_sk, 'arsip' => $akses_arsip, 'atur_layout' => $akses_atur_layout, 'atur_klasifikasi_sm' => $akses_atur_klasifikasi_sm, 'atur_klasifikasi_arsip' => $akses_atur_klasifikasi_arsip, 'atur_user' => $akses_atur_user, 'report_sm' => $akses_report_sm, 'report_sk' => $akses_report_sk, 'report_arsip' => $akses_report_arsip,'report_dispo' => $akses_report_dispo,'report_progress' => $akses_reportprogress,'info' => $akses_atur_info);
						$params = array(':id_user' => $id_user, ':sm' => $akses_sm, ':sk' => $akses_sk, ':arsip' => $akses_arsip, ':atur_layout' => $akses_atur_layout, ':atur_klasifikasi_sm' => $akses_atur_klasifikasi_sm, ':atur_klasifikasi_sk' => $akses_atur_klasifikasi_sk, ':atur_klasifikasi_arsip' => $akses_atur_klasifikasi_arsip, ':atur_user' => $akses_atur_user, ':report_sm' => $akses_report_sm, ':report_sk' => $akses_report_sk, ':report_arsip' => $akses_report_arsip,':report_dispo' => $akses_report_dispo,':report_progress' => $akses_reportprogress,':info' => $akses_atur_info);
						$insertLevel = $this->model->insertprepare("user_level", $field, $params);
						//print_r($_POST);
					}
					echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=user&userid=$_GET[userid]&do=entri\";</script>";
				}else{
					die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
				}
			}
		}else{
			$upass = md5($upass);
			$field = array('nama' => $nama, 'nik' => $nik, 'uname' => $uname,  'upass' => $upass, 'email' => $email, 'jabatan' => $jabatan, 'rule_disposisi' => $disposisi, 'picture' => $picture);
			$params = array(':nama' => $nama, ':nik' => $nik, ':uname'=>$uname, ':upass' => $upass, ':email' => $email, ':jabatan' => $jabatan, 'rule_disposisi' => $disposisi,  ':picture' => $picture);
			$insert = $this->model->insertprepare("user", $field, $params);
			if($insert->rowCount() >= 1){
				$CekIdUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "ORDER BY id_user DESC LIMIT 1");
				$dataCekIdUser = $CekIdUser->fetch(PDO::FETCH_OBJ);
				
				$params = array(':id_user' => $dataCekIdUser->id_user);
				$CekLevel = $this->model->selectprepare("user_level", $field=null, $params, "id_user=:id_user");
				if($CekLevel->rowCount() >= 1){
					$field = array('sm' => $akses_sm, 'sk' => $akses_sk, 'arsip' => $akses_arsip, 'atur_layout' => $akses_atur_layout, 'atur_klasifikasi_sm' => $akses_atur_klasifikasi_sm, 'atur_klasifikasi_sk' => $akses_atur_klasifikasi_sk, 'atur_klasifikasi_arsip' => $akses_atur_klasifikasi_arsip, 'atur_user' => $akses_atur_user, 'report_sm' => $akses_report_sm, 'report_sk' => $akses_report_sk, 'report_arsip' => $akses_report_arsip,'report_dispo' => $akses_report_dispo,'report_progress' => $akses_reportprogress,'info' => $akses_atur_info);
					$params = array(':id_user' => $dataCekIdUser->id_user);
					$updateLevel = $this->model->updateprepare("user_level", $field, $params, "id_user=:id_user");
					//echo "update";
				}else{
					$field = array('id_user' => $dataCekIdUser->id_user, 'sm' => $akses_sm, 'sk' => $akses_sk, 'arsip' => $akses_arsip, 'atur_layout' => $akses_atur_layout, 'atur_klasifikasi_sm' => $akses_atur_klasifikasi_sm, 'atur_klasifikasi_sk' => $akses_atur_klasifikasi_sk, 'atur_klasifikasi_arsip' => $akses_atur_klasifikasi_arsip, 'atur_user' => $akses_atur_user, 'report_sm' => $akses_report_sm, 'report_sk' => $akses_report_sk, 'report_arsip' => $akses_report_arsip,'report_dispo' => $akses_report_dispo,'report_progress' => $akses_reportprogress,'info' => $akses_atur_info);
					$params = array(':id_user' => $dataCekIdUser->id_user, ':sm' => $akses_sm, ':sk' => $akses_sk, ':arsip' => $akses_arsip, ':atur_layout' => $akses_atur_layout, ':atur_klasifikasi_sm' => $akses_atur_klasifikasi_sm, ':atur_klasifikasi_sk' => $akses_atur_klasifikasi_sk, ':atur_klasifikasi_arsip' => $akses_atur_klasifikasi_arsip, ':atur_user' => $akses_atur_user, ':report_sm' => $akses_report_sm, ':report_sk' => $akses_report_sk, ':report_arsip' => $akses_report_arsip,':report_dispo' => $akses_report_dispo,':report_progress' => $akses_reportprogress,':info' => $akses_atur_info);
					
					$insertLevel = $this->model->insertprepare("user_level", $field, $params);
					//echo "entri".$dataCekIdUser->id_user;
					//print_r($_POST);
				}
				echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"./index.php?op=user\";</script>";
			}else{
				die("<script>alert('Data Gagal di simpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}
	}else{
		if(isset($_GET['userid']) && empty($_GET['act'])){
			$id_user = htmlspecialchars($purifier->purify(trim($_GET['userid'])), ENT_QUOTES);
			$params = array(':id_user' => $id_user);
			$cek_user = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user");
			if($cek_user->rowCount() >= 1){
				$data_user = $cek_user->fetch(PDO::FETCH_OBJ);
				$title= "Edit Data User";
				$ketPasword = "Password";
				$nama = 'value="'.$data_user->nama .'"';
				$nik = 'value="'.$data_user->nik .'"';
				$uname = 'value="'.$data_user->uname .'"';
				$upass = 'value="'.$data_user->upass .'"';
				$email = 'value="'.$data_user->email .'"';
				if(isset($data_user->rule_disposisi) == '' OR $data_user->rule_disposisi == "null"){
					$dummy_arr = '[""]';
					$cekDisposisi = json_decode($dummy_arr, true);
				}else{
					$cekDisposisi = json_decode($data_user->rule_disposisi, true);
				}
				$cekLevel = $this->model->selectprepare("user_level", $field=null, $params, "id_user=:id_user");
				$dataCekLevel = $cekLevel->fetch(PDO::FETCH_OBJ);
			}else{
				$title= "Entri Data User";
				$ketPasword = "Password *";
				$validasifile = "required";
				$dummy_arr = '[""]';
				$cekDisposisi = json_decode($dummy_arr, true);
			}
		}else{
			$title= "Entri Data User";
			$validasifile = "required";
			$ketPasword = "Password *";
			$dummy_arr = '[""]';
			$cekDisposisi = json_decode($dummy_arr, true);
		}
		if(isset($_GET['userid']) && (isset($_GET['act']) && $_GET['act'] == "del")){
			$params = array(':id_user' => $_GET['userid']);
			$lihat_memo = $this->model->selectprepare("arsip_sm", $field=null, $params, "id_user=:id_user");
			if($lihat_memo->rowCount() >= 1){
				die("<script>alert('Nama User ini tidak dapat dihapus karena terkait dengan data surat masuk. Jika tetap ingin menghapus, silahkan hapus data disposisi surat yang terkait dengan user ini terlebih dahulu. Terimakasih');window.history.go(-1);</script>");
			}else{
				$params = array(':id_user' => $_GET['userid']);
				$lihat_sm = $this->model->selectprepare("memo", $field=null, $params, "id_user=:id_user");
				if($lihat_sm->rowCount() >= 1){
					die("<script>alert('Nama User ini tidak dapat dihapus karena terkait dengan data disposisin Surat. Jika tetap ingin menghapus, silahkan hapus data surat yang terkait dengan user ini terlebih dahulu. Terimakasih');window.history.go(-1);</script>");
				}else{
					$params = array(':disposisi' => $_GET['userid']);
					$memo = $this->model->selectprepare("memo", $field=null, $params, "disposisi=:disposisi");
					if($memo->rowCount() >= 1){
						die("<script>alert('Nama User ini tidak dapat dihapus karena terkait dengan data Tujuan Surat Masuk. Jika tetap ingin menghapus, silahkan hapus data surat Tujuan Surat Masuk terkait terlebih dahulu. Terimakasih');window.history.go(-1);</script>");
					}else{
						$params = array(':id_user' => $_GET['userid']);
						$lihat_sk = $this->model->selectprepare("arsip_sk", $field=null, $params, "id_user=:id_user");
						if($lihat_sk->rowCount() >= 1){
							die("<script>alert('Nama User ini tidak dapat dihapus karena terkait dengan data Surat Keluar. Jika tetap ingin menghapus, silahkan hapus data surat keluar yang terkait dengan user ini terlebih dahulu. Terimakasih');window.history.go(-1);</script>");
						}else{
							$params = array(':id_user' => $_GET['userid']);
							$delete = $this->model->hapusprepare("user", $params, "id_user=:id_user");
							$delete2 = $this->model->hapusprepare("user_level", $params, "id_user=:id_user");
							if($delete){
								echo "<script type=\"text/javascript\">alert('Data User Berhasil di Hapus...!!');window.location.href=\"./index.php?op=user\";</script>";
							}else{
								die("<script>alert('Gagal menghapus data User, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
							}
						}
					}
				}
			}
		}else{?>
		<div class="widget-box"><?php
			if(isset($_GET['do']) && $_GET['do'] == "entri"){?>
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
					<div class="widget-main"><?php /*?>
						<form class="form-horizontal" role="form" name="formku" method="GET">	
							<input type="hidden" name="op" value="user"/>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1">Pilih Level User</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" title="Level User">?</span>
								<div class="col-sm-3">
									<select class="form-control" id="form-field-select-3" name="Getlevel" data-placeholder="Pilih Level..." onchange='this.form.submit()'>
										<option value="">Pilih Level</option><?php
										$Getlevel = array("Pegawai", "Admin");
										foreach($Getlevel as $level_value){
											if(isset($_GET['Getlevel']) && $_GET['Getlevel'] == $level_value){?>
												<option value="<?php echo $level_value;?>" selected><?php echo $level_value;?></option><?php
											}else{?>
												<option value="<?php echo $level_value;?>"><?php echo $level_value;?></option><?php
											}
										}?>
									</select>
								</div>
							</div>
						</form><?php
						*/?>
						<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
							<?php /*<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Level Akses</label>
								<div class="col-sm-2">
									<input class="form-control" type="text" name="level" value="<?php echo trim($_GET['Getlevel'])?>" id="form-field-mask-1" disabled>
								</div>
							</div>*/?>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> NIK/NIP *</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan NIK/NIP Pengguna." title="NIK/NIP">?</span>
								<div class="col-sm-5">
									<input class="form-control" type="hidden" name="level" value="Pegawai">
									<input class="form-control" placeholder="NIK/NIP" type="number" name="nik" <?php if(isset($nik)){ echo $nik; }?> id="form-field-mask-1" required/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nama Lengkap *</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan Nama Lengkap Pengguna." title="Nama Lengkap">?</span>
								<div class="col-sm-5">
									<input class="form-control" placeholder="Nama Lengkap" type="text" name="nama" <?php if(isset($nama)){ echo $nama; }?> id="form-field-mask-1" required/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Username *</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left"  data-content="Username untuk login." title="Username">?</span>
								<div class="col-sm-3">
									<input class="form-control" placeholder="Username" type="text" name="uname" <?php if(isset($uname)){ echo $uname; }?> id="form-field-mask-1" required/>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> <?php echo $ketPasword;?></label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left"  data-content="Password untuk login" title="Password">?</span>
								<div class="col-sm-3">
									<input class="form-control" placeholder="Password" type="text" name="upass" id="form-field-mask-1" <?php if(isset($validasifile)){ echo $validasifile; }?>>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Email</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left"  data-content="Email Pengguna" title="Email">?</span>
								<div class="col-sm-3">
									<input class="form-control" placeholder="Alamat Email" type="email" name="email" id="form-field-mask-1" <?php if(isset($email)){ echo $email; }?>>
								</div>
							</div><?php
							/*
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Hak Akses</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="penentuan Level/Hak Akses User" title="Hak Akses">?</span>
								<div class="col-sm-2">
									<select class="form-control" id="form-field-select-3" name="level" data-placeholder="Pilih Level...">
										<option value="" selected>Pilih Level</option><?php
										$Getlevel = array("Konseptor", "Pemeriksa", "Admin");
										foreach($Getlevel as $level_value){
											if(isset($level) && $level == $level_value){?>
												<option value="<?php echo $level_value;?>" selected><?php echo $level_value;?></option><?php
											}else{?>
												<option value="<?php echo $level_value;?>"><?php echo $level_value;?></option><?php
											}
										}?>
									</select>
								</div>
							</div>*/?>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Jabatan *</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih Jabatan" title="Jabatan">?</span>
								<div class="col-sm-4">
									<select class="form-control" id="form-field-select-3" name="jabatan" data-placeholder="Pilih Jabatan..." required><?php
										$JabUser= $this->model->selectprepare("user_jabatan", $field=null, $params=null, $where=null, $order=null);
										if($JabUser->rowCount() >= 1){
											while($dataJabUser= $JabUser->fetch(PDO::FETCH_OBJ)){
												if(isset($data_user->jabatan) && $data_user->jabatan == $dataJabUser->id_jab){?>
													<option value="<?php echo $dataJabUser->id_jab;?>" selected><?php echo $dataJabUser->nama_jabatan;?></option><?php
												}else{?>
													<option value="<?php echo $dataJabUser->id_jab;?>"><?php echo $dataJabUser->nama_jabatan;?></option><?php
												}
											}
										}?>
									</select>
								</div>
							</div>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Dapat melakukan Disposisi ke</label>
								<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Pilih Group Level Disposisi" title="Group Level Disposisi">?</span>
								<div class="col-sm-6">
									<select multiple="" class="chosen-select form-control" name="disposisi[]" id="form-field-select-3" data-placeholder="Pilih user..."><?php
										$GetUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
										if($GetUser->rowCount() >= 1){
											while($dataUser = $GetUser->fetch(PDO::FETCH_OBJ)){
												$NamaUser = $dataUser->nama ." (".$dataUser->nama_jabatan .")";
												if(false !== array_search($dataUser->id_user, $cekDisposisi)){?>
													<option value="<?php echo $dataUser->id_user;?>" selected><?php echo $NamaUser;?></option><?php
												}else{?>
													<option value="<?php echo $dataUser->id_user;?>"><?php echo $NamaUser;?></option><?php
												}
											}								
										}else{?>
											<option value="">Not Found</option><?php
										}?>
									</select>
								</div>
							</div>
							<hr/>
							<div class="space-4"></div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Arsip Surat Masuk</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										$ArrAkses1 = array("W" => "Tulis", "R" => "Baca", "N" => "Tanpa Hak Akses");
										$ArrAkses2 = array("Y" => "Ya", "N" => "Tidak");
										foreach($ArrAkses1 as $value => $nilai){
											if(isset($dataCekLevel->sm) && $dataCekLevel->sm == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_sm" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_sm" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Arsip Surat Keluar</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses1 as $value => $nilai){
											if(isset($dataCekLevel->sk) && $dataCekLevel->sk == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_sk" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_sk" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Arsip File</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses1 as $value => $nilai){
											if(isset($dataCekLevel->arsip) && $dataCekLevel->arsip == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_arsip" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_arsip" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Laporan Surat Masuk</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->report_sm) && $dataCekLevel->report_sm == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_reportsm" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_reportsm" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Laporan Progress Surat</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->report_progress) && $dataCekLevel->report_progress == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_reportprogress" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_reportprogress" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Laporan Surat Keluar</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->report_sk) && $dataCekLevel->report_sk == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_reportsk" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_reportsk" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Laporan Disposisi</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->report_dispo) && $dataCekLevel->report_dispo == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_reportdispo" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_reportdispo" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Laporan Arsip File</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->report_arsip) && $dataCekLevel->report_arsip == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_reportarsip" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_reportarsip" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Atur Layout</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->atur_layout) && $dataCekLevel->atur_layout == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_atur_layout" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_atur_layout" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Atur Klasifikasi Surat Masuk</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->atur_klasifikasi_sm) && $dataCekLevel->atur_klasifikasi_sm == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_atur_klas_sm" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_atur_klas_sm" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Atur Klasifikasi Surat Keluar</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->atur_klasifikasi_sk) && $dataCekLevel->atur_klasifikasi_sk == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_atur_klas_sk" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_atur_klas_sk" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Atur Klasifikasi File Arsip</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->atur_klasifikasi_arsip) && $dataCekLevel->atur_klasifikasi_arsip == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_atur_klas_arsip" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_atur_klas_arsip" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Atur User</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->atur_user) && $dataCekLevel->atur_user == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_atur_user" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_atur_user" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label no-padding-right" for="form-field-mask-1"> Entri Memo</label>
								<div class="col-sm-5">
									<div class="radio"><?php
										foreach($ArrAkses2 as $value => $nilai){
											if(isset($dataCekLevel->info) && $dataCekLevel->info == $value){?>
												<label>
													<input type="radio" class="ace" name="akses_atur_info" value="<?php echo $value;?>" checked />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php
											}else{?>
												<label>
													<input type="radio" class="ace" name="akses_atur_info" value="<?php echo $value;?>" />
													<span class="lbl"> <?php echo $nilai;?></span>
												</label><?php												
											}
										}?>
									</div>
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
				</div><?php
			}
		}
		/* PAGINATION */
		$batas = 15;
		$pg = isset( $_GET['halaman'] ) ? $_GET['halaman'] : "";
		if(empty($pg)){
			$posisi = 0;
			$pg = 1;
		}else{
			$posisi = ($pg-1) * $batas;
		}
		$user = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "order by id_user DESC LIMIT $posisi, $batas");
		if($user->rowCount() >= 1){
			$Totaluser = $this->model->selectprepare("user", $field=null, $params=null, $where=null);
			$total = $Totaluser->rowCount();?>
			<div class="widget-body">
				<div class="widget-main">
					<a href="./index.php?op=user&do=entri" title="Data User">
						<button class="btn btn-white btn-info btn-bold">
							<i class="ace-icon fa fa-users bigger-120 blue"></i>Tambah User
						</button>
					</a>
					<a href="./index.php?op=user&act=jabatan" title="Pengaturan Jabatan">
						<button class="btn btn-white btn-info btn-bold">
							<i class="ace-icon fa fa-users bigger-120 blue"></i>Jabatan
						</button>
					</a>
					<div class="space-4"></div><?php
					if(!isset($_GET['userid'])){?>
						<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
							<thead>
								<tr>
									<th width="40">No</th>
									<th>NIK/NIP</th>
									<th>Nama Lengkap</th>
									<th>Username</th>
									<th>Jabatan</th>
									<th>Level Disposisi</th>
									<th width="70"><center>ACT</center></th>
								</tr>
							</thead>
							<tbody><?php
								$no=1+$posisi;
								$LevelDisposisi = '';
								while($data_user = $user->fetch(PDO::FETCH_OBJ)){									
									$ruleDispo = json_decode($data_user->rule_disposisi, true);
									$params = array(':id_jab' => $data_user->jabatan);
									$GetJabatan = $this->model->selectprepare("user_jabatan", $field=null, $params, "id_jab=:id_jab", $order=null);
									if($GetJabatan->rowCount() >= 1){
										$dataGetJabatan = $GetJabatan->fetch(PDO::FETCH_OBJ);
										$JabUser = $dataGetJabatan->nama_jabatan;
									}else{
										$JabUser = "-";
									}?>
									<tr>
										<td><center><?php echo $no;?></center></td>
										<td><?php echo $data_user->nik;?></td>
										<td><?php echo $data_user->nama;?></td>
										<td><?php echo $data_user->uname;?></td>
										<td><?php echo $JabUser;?></td>
										<td><?php 
											if(is_array($ruleDispo)){
												foreach($ruleDispo as $field => $value){
													$GetUserDis = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user='$value'")->fetch(PDO::FETCH_OBJ);
													echo '- '.$GetUserDis->nama .'<br/>';
												}
											}?>
										</td>
										<td><center>
											<div class="hidden-sm hidden-xs btn-group">
												<a href="./index.php?op=user&userid=<?php echo $data_user->id_user;?>&do=entri">								
													<button class="btn btn-minier btn-info">
														<i class="ace-icon fa fa-pencil bigger-100"></i>
													</button>
												</a><?php
												if($data_user->level != "Admin"){?>
													<a href="./index.php?op=user&userid=<?php echo $data_user->id_user;?>&act=del" onclick="return confirm('Anda yakin akan menghapus data ini??')">
														<button class="btn btn-minier btn-danger">
															<i class="ace-icon fa fa-trash-o bigger-110"></i>
														</button>
													</a><?php
												}?>
											</div></center>
										</td>
									</tr><?php
								$no++;
								}?>
							</tbody>
						</table><?php
						$jml_data = $total;
						//Jumlah halaman
						$JmlHalaman = ceil($jml_data/$batas); 
						//Navigasi ke sebelumnya
						if($pg > 1){
							$link = $pg-1;
							$prev = "index.php?op=user&halaman=$link";
							$prev_disable = " ";
						}else{
							$prev = "#";
							$prev_disable = "disabled";
						}
						//Navigasi ke selanjutnya
						if($pg < $JmlHalaman){
							$link = $pg + 1;
							$next = "index.php?op=user&halaman=$link";
							$next_disable = " ";
						}else{
							$next = "#";
							$next_disable = "disabled";
						}
						if($batas < $jml_data){?>
							<ul class="pager">
								<li class="previous <?php echo $prev_disable;?>"><a href="<?php echo $prev;?>">&larr; Sebelumnya </a></li>
								<li class="next <?php echo $next_disable;?>"><a href="<?php echo $next;?>">Selanjutnya &rarr;</a></li>
							</ul>
							<span class="text-muted">Halaman <?php echo $pg;?> dari <?php echo $JmlHalaman;?> (Total : <?php echo $jml_data;?> records)</span> <?php
						}
						/* END PAGINATION */
					}?>
				</div>
			</div><?php
		}?>
		</div><?php
	}
}?>