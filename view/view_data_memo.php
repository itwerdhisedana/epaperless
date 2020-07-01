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
		//$field = array("a.id_user as iduser_dis","a.*","b.*");
		if(empty($_GET['keyword'])){
			$CekInfo = $this->model->selectprepare("info a join user c on a.pengirim_info=c.id_user", $field=null, $params=null, $where=null, "WHERE a.pengirim_info = '$_SESSION[id_user]' order by a.tgl_info DESC LIMIT $posisi, $batas");
			
			$CekInfo2 = $this->model->selectprepare("info a join user c on a.pengirim_info=c.id_user", $field=null, $params=null, $where=null, "WHERE a.pengirim_info = '$_SESSION[id_user]'");
		}else{
			$params = array(':judul_info' => $keyword, ':ket_info' => $keyword);
			$CekInfo = $this->model->selectprepare("info a join user c on a.pengirim_info=c.id_user", $field=null, $params, "(a.judul_info LIKE :judul_info OR a.ket_info LIKE :ket_info)", "AND a.pengirim_info = '$_SESSION[id_user]' order by a.tgl_info DESC LIMIT $posisi, $batas");
			
			$CekInfo2 = $this->model->selectprepare("info a join user c on a.pengirim_info=c.id_user", $field=null, $params, "(a.judul_info LIKE :judul_info OR a.ket_info LIKE :ket_info)", "AND a.tujuan_info LIKE '%\"$_SESSION[id_user]\"%'");
		}
	}else{
		$field = array("id_info","DATE_FORMAT(tgl_info, '%Y') as thn");
		$lastData = $this->model->selectprepare("info", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_info, '%Y') order by DATE_FORMAT(tgl_info, '%Y') DESC LIMIT 1");
		$dataLast = $lastData->fetch(PDO::FETCH_OBJ);
		if(isset($_GET['yearinfo'])){
			$params = array(':year' => $_GET['yearinfo'], ':id_user' => $_SESSION['id_user']);
		}else{
			$params = array(':year' => $dataLast->thn, ':id_user' => $_SESSION['id_user']);
		}
		/* $arsip_sm = $this->model->selectprepare("arsip_sm", $field=null, $params, "DATE_FORMAT(tgl_terima, '%Y')=:year", "order by tgl_terima DESC LIMIT $posisi, $batas"); */
			
		$CekInfo = $this->model->selectprepare("info a join user c on a.pengirim_info=c.id_user", $field=null, $params, "DATE_FORMAT(a.tgl_info, '%Y')=:year AND a.pengirim_info=:id_user", "ORDER BY a.tgl_info DESC LIMIT $posisi, $batas");
	}
	if($CekInfo->rowCount() >= 1){
		while($DataCekInfo = $CekInfo->fetch(PDO::FETCH_OBJ)){
			$dump_info[]=$DataCekInfo;
		}?>
		<table class="table tile">
			<thead>
				<tr>
					<th width="50">No</th>
					<th>Perihal Memo</th>
					<th>Tujuan</th>
					<th width="170">Tgl Memo</th>
					<th width="100">Aksi</th>
				</tr>
			</thead>
			<tbody><?php
				$no=1+$posisi;
				foreach($dump_info as $key => $object){
					$tgl_info = substr($object->tgl_info,0,10);
					$TujuanInfo = json_decode($object->tujuan_info, true);?>
					<tr>
						<td><?php echo $no;?></td>
						<td>
							<a href="./index.php?op=add_memo&infoid=<?php echo $object->id_info;?>">
							<?php echo $object->judul_info;?></a>
						</td>
						<td><?php 
						foreach($TujuanInfo as $key1 => $object1){
							$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$object1'")->fetch(PDO::FETCH_OBJ);
							echo "- ".$CekUser->nama ." ";
						} //echo $object->tujuan_info;?>
						</td>
						<td><?php echo tgl_indo($tgl_info);?></td>
						<td>
							<div class="hidden-sm hidden-xs btn-group">
								<a href="./index.php?op=add_memo&infoid=<?php echo $object->id_info;?>">		
									<button class="btn btn-minier btn-info">
										<i class="ace-icon fa fa-pencil"></i>
									</button>
								</a>
								
								<a href="./index.php?op=add_memo&infoid=<?php echo $object->id_info;?>&act=del" onclick="return confirm('Anda yakin akan menghapus data ini??')">
									<button class="btn btn-minier btn-danger">
										<i class="ace-icon fa fa-trash-o"></i>
									</button>
								</a>
							</div>
						</td>
					</tr><?php
				$no++;
				}?>
			</tbody>
		</table><?php
		/* PAGINATION */
		//hitung jumlah data
		if(isset($_GET['keyword'])){
			$jml_data = $CekInfo2->rowCount();
			$link_order="&keyword=$_GET[keyword]";
		}else{
			if(isset($_GET['yearsk'])){
				$params = array(':year' => $_GET['yearsk']);
				$link_order="&yearsk=$_GET[yearsk]";
			}else{
				$params = array(':year' => $dataLast->thn);
				$link_order="";
			}
			$jlhdata = $this->model->selectprepare("info a join user c on a.pengirim_info=c.id_user", $field=null, $params=null, $where=null, "WHERE a.pengirim_info = '$_SESSION[id_user]'");
			$jml_data = $jlhdata->rowCount();
		}
		//Jumlah halaman
		$JmlHalaman = ceil($jml_data/$batas); 
		//Navigasi ke sebelumnya
		if($pg > 1){
			$link = $pg-1;
			$prev = "index.php?op=add_memo&halaman=$link$link_order";
			$prev_disable = " ";
		}else{
			$prev = "#";
			$prev_disable = "disabled";
		}
		//Navigasi ke selanjutnya
		if($pg < $JmlHalaman){
			$link = $pg + 1;
			$next = "index.php?op=add_memo&halaman=$link$link_order";
			$next_disable = " ";
		}else{
			$next = "#";
			$next_disable = "disabled";
		}
		if($batas < $jml_data){?>
			<ul class="pager">
				<li class="previous <?php echo $prev_disable;?>"><a href="<?php echo $prev;?>"><font color="black"><b>&larr; Sebelumnya </font></b></a></li>
				<li class="next <?php echo $next_disable;?>"><a href="<?php echo $next;?>"><font color="black"><b>Selanjutnya &rarr;</b></font></a></li>
			</ul>
			<span class="text-muted color"><font color="white">Halaman <?php echo $pg;?> dari <?php echo $JmlHalaman;?> (Total : <?php echo $jml_data;?> records)</font></span><?php
		}
		/* END PAGINATION */
	}else{?>
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			</button>
			<p>
				<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
				Data tidak ditemukan. Terimakasih.
			</p>
		</div><?php
	}
?>
<script src="assets/jquery.min.js"></script>
<script type="text/javascript" src="assets/jquery.media.js"></script>

<script>
	$(function () {
		$(document).on('click', '.edit-record', function (e) {
			e.preventDefault();
			$("#myModal").modal('show');
			$.post('view/PdfPreview.php',
					{id: $(this).attr('data-id')},
			function (html) {
				$(".modal-body").html(html);
			}
			);
		});
	});
</script>