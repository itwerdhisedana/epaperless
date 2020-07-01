<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$bagian = htmlspecialchars($purifier->purify(trim($_POST['bagian'])), ENT_QUOTES);
	$kepala = htmlspecialchars($purifier->purify(trim($_POST['kepala'])), ENT_QUOTES);
	$tgl = date("Y-m-d H:i:s", time());
	if(isset($_GET['idbagian'])){
		$idbagian = htmlspecialchars($purifier->purify(trim($_GET['idbagian'])), ENT_QUOTES);
		$params = array(':id_bag' => $idbagian);
		$bag = $this->model->selectprepare("bagian", $field=null, $params, "id_bag=:id_bag");
		if($bag->rowCount() >= 1){
			$data_bag = $bag->fetch(PDO::FETCH_OBJ);
			$idbagian = $data_bag->id_bag;
			$field = array('nama_bagian' => $bagian, 'kepala' => $kepala);
			$params = array(':id_bag' => $idbagian);
			$update = $this->model->updateprepare("bagian", $field, $params, "id_bag=:id_bag");
			if($update){
				echo "<script type=\"text/javascript\">alert('Data Berhasil diperbaharui...!!');window.location.href=\"./index.php?op=bagian\";</script>";
			}else{
				die("<script>alert('Data menyimpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}
	}else{
		$field = array('nama_bagian' => $bagian, 'kepala' => $kepala, 'created'=>$tgl);
		$params = array(':nama_bagian' => $bagian, ':kepala'=>$kepala, ':created'=>$tgl);
		$insert = $this->model->insertprepare("bagian", $field, $params);
		if($insert->rowCount() >= 1){
			echo "<script type=\"text/javascript\">alert('Data Berhasil Tersimpan...!!');window.location.href=\"./index.php?op=bagian\";</script>";
		}else{
			die("<script>alert('Data Gagal di simpan ke Database, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
		}
	}
}else{
	if(isset($_GET['idbagian']) && empty($_GET['act'])){
		$idbagian = htmlspecialchars($purifier->purify(trim($_GET['idbagian'])), ENT_QUOTES);
		$params = array(':id_bag' => $idbagian);
		$cek_bag = $this->model->selectprepare("bagian", $field=null, $params, "id_bag=:id_bag");
		if($cek_bag->rowCount() >= 1){
			$data_bag = $cek_bag->fetch(PDO::FETCH_OBJ);
			$title= "Edit Data Bagian";
			$nama_bag = 'value="'.$data_bag->nama_bagian .'"';
			$kepala = 'value="'.$data_bag->kepala .'"';
		}else{
			$title= "Entri Data Bagian";
		}
	}else{
		$title= "Entri Data Bagian";
	}
	if(isset($_GET['idbagian']) && (isset($_GET['act']) && $_GET['act'] == "del")){
		$params = array(':disposisi' => $_GET['idbagian']);
		$lihat_memo = $this->model->selectprepare("memo", $field=null, $params, "disposisi=:disposisi");
		if($lihat_memo->rowCount() >= 1){
			die("<script>alert('Nama Bagian ini tidak dapat dihapus karena terkait dengan data disposisi. Jika tetap ingin menghapus, silahkan hapus data disposisi surat yang terkait dengan bagian menggunakan user dengan level Manager. Terimakasih');window.history.go(-1);</script>");
		}else{
			$params = array(':id_bag' => $_GET['idbagian']);
			$delete = $this->model->hapusprepare("bagian", $params, "id_bag=:id_bag");
			if($delete){
				echo "<script type=\"text/javascript\">alert('Data Berhasil di Hapus...!!');window.location.href=\"./index.php?op=bagian\";</script>";
			}else{
				die("<script>alert('Gagal menghapus data bagian, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
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
				<form class="form-horizontal" role="form" method="POST" name="formku" action="<?php echo $_SESSION['url'];?>">
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Nama Bagian *</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan nama Bagian/Unit Kerja." title="Nomor Surat Keluar">?</span>
						<div class="col-sm-5">
							<input class="form-control" placeholder="Nama Bagian/Unit Kerja" type="text" name="bagian" <?php if(isset($nama_bag)){ echo $nama_bag; }?> id="form-field-mask-1" required/>
						</div>
					</div>
					<div class="space-4"></div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right" for="form-field-mask-1"> Kepala</label>
						<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Di isi dengan nama Kepala/Ketua Bagian/Unit Kerja title="Kepala">?</span>
						<div class="col-sm-4">
							<input class="form-control" placeholder="Nama Kepala/Ketua" type="text" name="kepala" <?php if(isset($kepala)){ echo $kepala; }?> id="form-field-mask-1" />
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
		</div>
	</div><?php
	$bagian = $this->model->selectprepare("bagian", $field=null, $params=null, $where=null, "order by nama_bagian ASC");
	if($bagian->rowCount() >= 1){
		while($data_bagian = $bagian->fetch(PDO::FETCH_OBJ)){
			$dump_bagian[]=$data_bagian;
		}?>
		<div class="widget-body">
			<div class="widget-main">
				<table id="simple-table" class="table  table-bordered table-hover">
					<thead>
						<tr>
							<th width="50">No</th>
							<th>Nama Bagian</th>
							<th width="250">Kepala</th>
							<th width="100">Aksi</th>
						</tr>
					</thead>
					<tbody><?php
						$no=1;
						foreach($dump_bagian as $key => $object){?>
							<tr>
								<td><center><?php echo $no;?></center></td>
								<td><?php echo $object->nama_bagian;?></td>
								<td><?php echo $object->kepala;?></td>
								<td>
									<div class="hidden-sm hidden-xs btn-group">
										<a href="./index.php?op=bagian&idbagian=<?php echo $object->id_bag;?>">								
											<button class="btn btn-minier btn-info">
												<i class="ace-icon fa fa-pencil bigger-100"></i>
											</button>
										</a>
										<a href="./index.php?op=bagian&idbagian=<?php echo $object->id_bag;?>&act=del" onclick="return confirm('Anda yakin akan menghapus data ini??')">
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
		</div><?php
	}
}?>