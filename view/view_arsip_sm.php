<?php
	if(isset($_GET['smid'])){
		require_once "view_sm_detail_sekretaris.php";
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
			$params = array(':custom_noagenda' => $keyword, ':no_sm' => $keyword, ':pengirim' => $keyword, ':perihal' => $keyword);
			$arsip_sm = $this->model->selectprepare("arsip_sm", $field=null, $params, "custom_noagenda LIKE :custom_noagenda OR no_sm LIKE :no_sm 
			OR pengirim LIKE :pengirim OR perihal LIKE :perihal", "order by tgl_terima DESC LIMIT $posisi, $batas");
			$arsip_sm2 = $this->model->selectprepare("arsip_sm", $field=null, $params, "custom_noagenda LIKE :custom_noagenda OR no_sm LIKE :no_sm 
			OR pengirim LIKE :pengirim OR perihal LIKE :perihal", $other=null);
		}else{
			$field = array("id_sm","DATE_FORMAT(tgl_terima, '%Y') as thn");
			$lastData = $this->model->selectprepare("arsip_sm", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_terima, '%Y') order by DATE_FORMAT(tgl_terima, '%Y') DESC LIMIT 1");
			$dataLast = $lastData->fetch(PDO::FETCH_OBJ);
			if(isset($_GET['yearsm'])){
				$params = array(':year' => $_GET['yearsm']);
			}else{
				$params = array(':year' => $dataLast->thn);
			}
			$arsip_sm = $this->model->selectprepare("arsip_sm", $field=null, $params, "DATE_FORMAT(tgl_terima, '%Y')=:year", "order by tgl_terima DESC LIMIT $posisi, $batas");
		}
		if($arsip_sm->rowCount() >= 1){
			while($data_sm = $arsip_sm->fetch(PDO::FETCH_OBJ)){
				$dump_sm[]=$data_sm;
			}?>
			<!--Modal Preview PDF-->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="myModalLabel">Preview Surat Masuk</h4>
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
						<th width="110">No Agenda</th>
						<th width="180">No Surat</th>
						<th width="160">Pengirim</th>
						<th>Perihal</th>
						<th width="120">Tgl Surat</th>
						<th width="120">Tgl Terima</th>
						<th width="100">Aksi</th>
					</tr>
				</thead>
				<tbody><?php
					$no=1+$posisi;
					foreach($dump_sm as $key => $object){
						$params = array(':id_sm' => $object->id_sm);
						$cekDisposisi = $this->model->selectprepare("memo a join user b on a.id_user=b.id_user", $field=null, $params, "id_sm=:id_sm");
						$tglSrt = explode("-", $object->tgl_surat);
						$tglSrt = $tglSrt[2]."-".$tglSrt[1]."-".$tglSrt[0];
						$tgltrm = explode("-", $object->tgl_terima);
						$tgltrm = $tgltrm[2]."-".$tgltrm[1]."-".$tgltrm[0];
						
						$params = array(':id_sm' => $object->id_sm, ':kode' => 'SM');
						$lihat_sm = $this->model->selectprepare("surat_read", $field=null, $params, "id_sm=:id_sm AND kode=:kode");
						if($lihat_sm->rowCount() <= 0){
							$labelstatus = "";
						}else{
							$labelstatus = "<i class=\"ace-icon fa fa-check bigger-110 green\" title=\"was read\"></i>";
						}
						
						$params1 = array(':id_sm' => $object->id_sm);
						$CekStatFinish = $this->model->selectprepare("status_surat a join user b on a.id_user=b.id_user", $field=null, $params1, "a.id_sm=:id_sm", "ORDER BY a.id_status DESC LIMIT 1");
						if($CekStatFinish->rowCount() >= 1){
							$dataCekStatFinish = $CekStatFinish->fetch(PDO::FETCH_OBJ);
							if($dataCekStatFinish->statsurat == 1){
								$ProgresStat = " <i class=\"ace-icon fa fa-history bigger-110 green\" title=\"Surat sedang ditindaklanjuti\"></i>";
							}elseif($dataCekStatFinish->statsurat == 2){
								$ProgresStat = " <i class=\"ace-icon fa fa-thumbs-o-up bigger-110 green\" title=\"Surat sudah selesai ditindaklanjuti\"></i>";
							}elseif($dataCekStatFinish->statsurat == 0){
								$ProgresStat = " <i class=\"ace-icon fa fa-times bigger-110 green\" title=\"Surat tidak dapat diproses\"></i>";
							}
						}else{
							$ProgresStat = " <i class=\"ace-icon fa fa-info bigger-110 green\" title=\"Surat belum diproses\"></i>";
						}
						
						if($cekDisposisi->rowCount() >= 1){
							$labelDis = " <i class=\"ace-icon fa fa-share bigger-110 green\" title=\"Telah di Disposisi\"></i>";
						}else{
							$labelDis = "";
						}?>
						<tr>
							<td><?php echo $no;?></td>
							<td>
								<a href="./index.php?op=sm&smid=<?php echo $object->id_sm;?>"><?php echo $object->custom_noagenda;?></a>
								<?php echo $labelstatus;?>
								<?php echo $labelDis;?>	
								<?php echo $ProgresStat;?>
							</td>
							<td><?php echo $object->no_sm;?></td>
							<td><?php echo $object->pengirim;?></td>
							<td><?php echo $object->perihal;?></td>
							<td><?php echo $tglSrt;?></td>
							<td><?php echo $tgltrm;?></td>
							<td>
								<div class="hidden-sm hidden-xs btn-group"><?php 
									if($object->file != ""){?>
										<a href="#" class="edit-record" data-id="<?php echo $object->file;?>" title="Preview Surat">					
											<button class="btn btn-minier btn-info">
												<i class="ace-icon fa fa-globe bigger-100"></i>
											</button>
										</a><?php
									}?>
								</div>
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
			$jml_data = $arsip_sm2->rowCount();
			$link_order="&keyword=$_GET[keyword]";
		}else{
			if(isset($_GET['yearsm'])){
				$params = array(':year' => $_GET['yearsm']);
				$link_order="&yearsm=$_GET[yearsm]";
			}else{
				$params = array(':year' => $dataLast->thn);
				$link_order="";
			}
			$jlhdata = $this->model->selectprepare("arsip_sm", $field=null, $params, "DATE_FORMAT(tgl_terima, '%Y')=:year", $other=null);
			$jml_data = $jlhdata->rowCount();
		}
		//Jumlah halaman
		$JmlHalaman = ceil($jml_data/$batas); 
		//Navigasi ke sebelumnya
		if($pg > 1){
			$link = $pg-1;
			$prev = "index.php?op=arsip_sm&halaman=$link$link_order";
			$prev_disable = " ";
		}else{
			$prev = "#";
			$prev_disable = "disabled";
		}
		//Navigasi ke selanjutnya
		if($pg < $JmlHalaman){
			$link = $pg + 1;
			$next = "index.php?op=arsip_sm&halaman=$link$link_order";
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
	}
//}?>
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