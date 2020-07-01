<?php
if(isset($_GET['skid'])){
	require_once "view_sk_detail.php";
}else{
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
			$params = array(':no_sk' => $keyword, ':pengolah' => $keyword, ':tujuan_surat' => $keyword, ':perihal' => $keyword);
			$arsip_sk = $this->model->selectprepare("arsip_sk", $field=null, $params, "no_sk LIKE :no_sk OR pengolah LIKE :pengolah 
			OR tujuan_surat LIKE :tujuan_surat OR perihal LIKE :perihal", "order by tgl_surat DESC LIMIT $posisi, $batas");
			$arsip_sk2 = $this->model->selectprepare("arsip_sk", $field=null, $params, "no_sk LIKE :no_sk OR pengolah LIKE :pengolah 
			OR tujuan_surat LIKE :tujuan_surat OR perihal LIKE :perihal", $other=null);
	}else{
		$field = array("id_sk","DATE_FORMAT(tgl_surat, '%Y') as thn");
		$lastData = $this->model->selectprepare("arsip_sk", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_surat, '%Y') order by DATE_FORMAT(tgl_surat, '%Y') DESC LIMIT 1");
		$dataLast = $lastData->fetch(PDO::FETCH_OBJ);
		if(isset($_GET['yearsk'])){
			$params = array(':year' => $_GET['yearsk']);
		}else{
			$params = array(':year' => $dataLast->thn);
		}
		$arsip_sk = $this->model->selectprepare("arsip_sk", $field=null, $params, "DATE_FORMAT(tgl_surat, '%Y')=:year", "order by tgl_surat DESC LIMIT $posisi, $batas");
	}
	if($arsip_sk->rowCount() >= 1){
		while($data_sk = $arsip_sk->fetch(PDO::FETCH_OBJ)){
			$dump_sk[]=$data_sk;
		}?>
		<!--Modal Preview PDF-->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Preview Surat Keluar</h4>
					</div>
					<div class="modal-body" style="height: 450px;"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!--Modal Preview PDF-->
		<table id="simple-table" class="table  table-bordered table-hover">
			<thead>
				<tr>
					<th width="50">No</th>
					<th width="200">No Agenda</th>
					<th width="150">No Surat</th>
					<th width="250">Tujuan</th>
					<th width="250">Perihal</th>
					<th width="120">Klasifikasi</th>
					<th width="100">Tgl Surat</th>
					<th width="100">Aksi</th>
				</tr>
			</thead>
			<tbody><?php
				$no=1+$posisi;
				foreach($dump_sk as $key => $object){
					$tglsrt = explode("-", $object->tgl_surat);
					$tglsrt = $tglsrt[2]."-".$tglsrt[1]."-".$tglsrt[0];
					$CekKlasifikasi = $this->model->selectprepare("klasifikasi_sk", $field=null, $params=null, $where=null, "WHERE id_klas='$object->klasifikasi'");
					$ViewKlasifikasi = $CekKlasifikasi->fetch(PDO::FETCH_OBJ);?>
					<tr>
						<td><?php echo $no;?></td>
						<td><a href="./index.php?op=sk&skid=<?php echo $object->id_sk;?>"><?php echo $object->custom_noagenda; ?></a></td>
						<td><?php echo $object->no_sk;?></td>
						<td><?php echo $object->tujuan_surat;?></td>
						<td><?php echo $object->perihal;?></td>
						<td><?php echo $ViewKlasifikasi->nama;?></td>
						<td><?php echo $tglsrt;?></td>
						<td><center>
							<div class="hidden-sm hidden-xs btn-group"><?php
								if($object->file != ""){?>
									<a href="#" class="edit-record" data-id="<?php echo $object->file;?>" title="Preview Surat">					
										<button class="btn btn-minier btn-info">
											<i class="ace-icon fa fa-globe bigger-100"></i>
										</button>
									</a><?php
								}?>
								<a href="./index.php?op=add_sk&skid=<?php echo $object->id_sk;?>">								
									<button class="btn btn-minier btn-info">
										<i class="ace-icon fa fa-pencil bigger-100"></i>
									</button>
								</a>
								<a href="./index.php?op=sk&skid=<?php echo $object->id_sk;?>&act=del" onclick="return confirm('Anda yakin akan menghapus data ini??')">
									<button class="btn btn-minier btn-danger">
										<i class="ace-icon fa fa-trash-o bigger-110"></i>
									</button>
								</a>
							</div></center>
						</td>
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
				Data tidak ditemukan. Terimakasih.
			</p>
		</div><?php
	}
	/* PAGINATION */
	//hitung jumlah data
	if(isset($_GET['keyword'])){
		$jml_data = $arsip_sk2->rowCount();
		$link_order="&keyword=$_GET[keyword]";
	}else{
		if(isset($_GET['yearsk'])){
			$params = array(':year' => $_GET['yearsk']);
			$link_order="&yearsk=$_GET[yearsk]";
		}else{
			$params = array(':year' => $dataLast->thn);
			$link_order="";
		}
		$jlhdata = $this->model->selectprepare("arsip_sk", $field=null, $params, "DATE_FORMAT(tgl_surat, '%Y')=:year", $other=null);
		$jml_data = $jlhdata->rowCount();
	}
	//Jumlah halaman
	$JmlHalaman = ceil($jml_data/$batas); 
	//Navigasi ke sebelumnya
	if($pg > 1){
		$link = $pg-1;
		$prev = "index.php?op=sk&halaman=$link$link_order";
		$prev_disable = " ";
	}else{
		$prev = "#";
		$prev_disable = "disabled";
	}
	//Navigasi ke selanjutnya
	if($pg < $JmlHalaman){
		$link = $pg + 1;
		$next = "index.php?op=sk&halaman=$link$link_order";
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