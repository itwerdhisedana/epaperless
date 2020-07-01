<?php
/* PAGINATION */
$batas = 15;
$pg = isset( $_GET['halaman'] ) ? $_GET['halaman'] : "";
if(empty($pg)){
	$posisi = 0;
	$pg = 1;
}else{
	$posisi = ($pg-1) * $batas;
}
/* END PAGINATION */
if(isset($_GET['keyword'])){
		$keyword = "%".$_GET['keyword']."%";
		if(empty($_GET['keyword'])){
			$ArsipFile = $this->model->selectprepare("arsip_file a JOIN klasifikasi_arsip b on a.id_klasifikasi=b.id_klasifikasi", $field=null, $params=null, $where=null, "order by a.tgl_arsip DESC LIMIT $posisi, $batas");
			$ArsipFile2 = $this->model->selectprepare("arsip_file a JOIN klasifikasi_arsip b on a.id_klasifikasi=b.id_klasifikasi", $field=null, $params=null, $where=null);
		}else{
			$params = array(':ket' => $keyword, ':nama_unit' => $keyword, ':nama_klasifikasi' => $keyword, ':tgl_arsip' => $keyword, ':no_arsip' => $keyword, ':keamanan' => $keyword);
			$ArsipFile = $this->model->selectprepare("arsip_file a JOIN klasifikasi_arsip b on a.id_klasifikasi=b.id_klasifikasi", $field=null, $params, "a.ket LIKE :ket OR c.nama_unit LIKE :nama_unit OR b.nama_klasifikasi LIKE :nama_klasifikasi OR a.tgl_arsip LIKE :tgl_arsip OR a.no_arsip LIKE :no_arsip OR a.keamanan LIKE :keamanan", "order by a.tgl_arsip DESC LIMIT $posisi, $batas");
			$ArsipFile2 = $this->model->selectprepare("arsip_file a JOIN klasifikasi_arsip b on a.id_klasifikasi=b.id_klasifikasi", $field=null, $params, "a.ket LIKE :ket OR c.nama_unit LIKE :nama_unit OR b.nama_klasifikasi LIKE :nama_klasifikasi OR a.tgl_arsip LIKE :tgl_arsip OR a.no_arsip LIKE :no_arsip OR a.keamanan LIKE :keamanan", $other=null);
		}
}else{
	$field = array("id_arsip","DATE_FORMAT(tgl_arsip, '%Y') as thn");
	$lastData = $this->model->selectprepare("arsip_file", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_arsip, '%Y') order by DATE_FORMAT(tgl_arsip, '%Y') DESC LIMIT 1");
	if($lastData->rowCount() >= 1){
		$dataLast = $lastData->fetch(PDO::FETCH_OBJ);
		if(isset($_GET['yearfile'])){
			$params = array(':year' => $_GET['yearfile']);
		}else{
			$params = array(':year' => $dataLast->thn);
		}
	}else{
		$params = array(':year' => '');
	}
	$ArsipFile = $this->model->selectprepare("arsip_file a JOIN klasifikasi_arsip b on a.id_klasifikasi=b.id_klasifikasi", $field=null, $params, "DATE_FORMAT(a.tgl_arsip, '%Y')=:year", "order by a.tgl_arsip DESC LIMIT $posisi, $batas");
}
if($ArsipFile->rowCount() >= 1){
	while($dataArsipFile = $ArsipFile->fetch(PDO::FETCH_OBJ)){
		$dump_ArsipFile[]=$dataArsipFile;
	}?>
	<table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
		<thead>
			<tr>
				<th width="10">No</th>
				<th width="180">Unit Kerja</th>
				<th width="100">Nomor Arsip</th>
				<th>Keamanan</th>
				<th>Urgensi</th>
				<th>Diteruskan</th>
				<th>Klasifikasi</th>
				<th width="120">Tanggal Arsip</th>
				<th width="120">Tanggal Upload</th>
				<th>Ket</th>
				<th width="20">File</th>
			</tr>
		</thead>
		<tbody><?php
			$no=1+$posisi;
			foreach($dump_ArsipFile as $key => $object){
				$tglFile = explode("-", $object->tgl_upload);
				$tglFile = $tglFile[2]."-".$tglFile[1]."-".$tglFile[0];
				$tgl_arsip = explode("-", $object->tgl_arsip);
				$tgl_arsip = $tgl_arsip[2]."-".$tgl_arsip[1]."-".$tgl_arsip[0];?>
				<tr>
					<td><center><?php echo $no;?></center></td>
					<td><?php echo $object->nama_unit;?></td>
					<td><?php echo sprintf("%04d", $object->no_arsip);?></td>
					<td><?php echo $object->keamanan;?></td>
					<td><?php echo $object->nama_klasifikasi;?></td>
					<td><?php echo $tgl_arsip;?></td>
					<td><?php echo $tglFile;?></td>
					<td><?php echo $object->ket;?></td>
					<td><a href="./berkas/<?php echo $object->file_arsip;?>" target="_blank"><button class="btn btn-minier btn-success">view</button></a>
				</tr><?php
			$no++;
			}?>
		</tbody>
	</table><?php
}else{?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<p>
			<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
			Data Arsip belum ada. Terimakasih.
		</p>
	</div><?php
}
/* PAGINATION */
//hitung jumlah data
if(isset($_GET['keyword'])){
	$jml_data = $ArsipFile2->rowCount();
	$link_order="&keyword=$_GET[keyword]";
}else{
	if(isset($_GET['yearfile'])){
		$params = array(':year' => $_GET['yearfile']);
		$link_order="&yearfile=$_GET[yearfile]";
	}else{
		if($lastData->rowCount() >= 1){
			$params = array(':year' => $dataLast->thn);
			$link_order="";
		}
	}
	$jlhdata = $this->model->selectprepare("arsip_file", $field=null, $params, "DATE_FORMAT(tgl_arsip, '%Y')=:year", $other=null);
	$jml_data = $jlhdata->rowCount();
}
//Jumlah halaman
$JmlHalaman = ceil($jml_data/$batas); 
//Navigasi ke sebelumnya
if($pg > 1){
	$link = $pg-1;
	$prev = "index.php?op=arsip_file&halaman=$link$link_order";
	$prev_disable = " ";
}else{
	$prev = "#";
	$prev_disable = "disabled";
}
//Navigasi ke selanjutnya
if($pg < $JmlHalaman){
	$link = $pg + 1;
	$next = "index.php?op=arsip_file&halaman=$link$link_order";
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
?>