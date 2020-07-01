<?php
if(isset($_GET['smid'])){
	require_once "view_sm_detail_manager.php";
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
	$sm = $this->model->selectprepare("arsip_sm a INNER JOIN user b on a.id_user=b.id_user", $field=null, $params=null, "a.status=:status", "ORDER BY tgl_terima DESC LIMIT $posisi, $batas");
	if($sm->rowCount() >= 1){
		while($data_sm = $sm->fetch(PDO::FETCH_OBJ)){
			$dump_sm[]=$data_sm;
		}?>
		<div class="widget-box">
			<div id="inbox" class="tab-pane in active">
				<div class="message-container">
					<div class="message-list-container">
						<div class="message-list" id="message-list"><?php
						$no=1+$posisi;
						foreach($dump_sm as $key => $object){
							$params = array(':id_sm' => $object->id_sm);
							$cek_memo = $this->model->selectprepare("memo a inner join bagian b on a.disposisi=b.id_bag", $field=null, $params, "a.id_sm=:id_sm");
							if($cek_memo->rowCount() >= 1){
								$data_cek = $cek_memo->fetch(PDO::FETCH_OBJ);
								$labelDis = " <i class=\"ace-icon fa fa-share bigger-110 green\" title=\"Disposisi ke: $data_cek->nama_bagian\"></i>";
							}else{
								$labelDis = "";
							}
							if($object->view == 0){?>
								<div class="message-item message-unread">
									<label class="inline">
										<span class="lbl" style="color:#609FC4;font-weight:700"><small><?php echo $no;?></small></span>
									</label>
									<span class="sender" title="<?php echo $object->nama;?>"><?php echo $object->nama;?> </span>
									<span class="time" style="width:100px;"><small><?php echo tgl_indo($object->tgl_terima);?></small></span>
									<span class="summary">
										<span class="text">
											<a href="./index.php?op=sm&smid=<?php echo $object->id_sm;?>">Surat dari: <?php echo $object->pengirim;?> : <?php echo $object->perihal;?></a>
											<?php echo " ".$labelDis;?>	
										</span>
									</span>
								</div><?php
							}else{?>						
								<div class="message-item">
									<label class="inline">
										<span style="color:#467287;"><small><?php echo $no;?></small></span>
									</label>
									<span class="sender" title="<?php echo $object->nama;?>"><?php echo $object->nama;?></span>
									<span class="time" style="width:100px;"><small><?php echo tgl_indo($object->tgl_terima);?></small></span>
									<span class="summary">
										<span class="text">
											<a href="./index.php?op=sm&smid=<?php echo $object->id_sm;?>">Surat dari: <?php echo $object->pengirim;?> : <?php echo $object->perihal;?></a>
											<?php echo " ".$labelDis;?>	
										</span>
									</span>
								</div><?php
							}
							$no++;
						}?>
						</div>
					</div>
				</div>
			</div>
		</div><?php
	}else{?>
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">
				<i class="ace-icon fa fa-times"></i>
			</button>
		</div><?php
	}
	/* PAGINATION */
	//hitung jumlah data
	$jlhdata = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, $other=null);
	$jml_data = $jlhdata->rowCount();
	//Jumlah halaman
	$JmlHalaman = ceil($jml_data/$batas); 
	//Navigasi ke sebelumnya
	if($pg > 1){
		$link = $pg-1;
		$prev = "index.php?op=sm&halaman=$link";
		$prev_disable = " ";
	}else{
		$prev = "#";
		$prev_disable = "disabled";
	}
	//Navigasi ke selanjutnya
	if($pg < $JmlHalaman){
		$link = $pg + 1;
		$next = "index.php?op=sm&halaman=$link";
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