<?php
if(isset($_GET['smid'])){
	require_once "view_tembusan_detail.php";
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
		$field = array("a.id_user as userDis","a.*","b.*");
		if(empty($_GET['keyword'])){
			$CekTembusan = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.tembusan LIKE '%\"$_SESSION[id_user]\"%' order by a.tgl DESC LIMIT $posisi, $batas");
			
			$CekTembusan2 = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.tembusan LIKE '%\"$_SESSION[id_user]\"%'");
		}else{
			$params = array(':pengirim' => $keyword, ':perihal' => $keyword);
			$CekTembusan = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field=null, $params, "(b.pengirim LIKE :pengirim OR b.perihal LIKE :perihal)", "AND a.tembusan LIKE '%\"$_SESSION[id_user]\"%' order by a.tgl DESC LIMIT $posisi, $batas");
			
			$CekTembusan2 = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field=null, $params, "(b.pengirim LIKE :pengirim OR b.perihal LIKE :perihal)", " AND a.tembusan LIKE '%\"$_SESSION[id_user]\"%'");
		}
	}else{
		$field = array("a.id_user as userDis","a.*","b.*");
		$CekTembusan = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.tembusan LIKE '%\"$_SESSION[id_user]\"%' order by a.tgl DESC LIMIT $posisi, $batas");
	}
	if($CekTembusan->rowCount() >= 1){?>
		<div class="widget-box">
			<div id="inbox" class="tab-pane in active">
				<div class="message-container">
					<div class="message-list-container">
						<div class="message-list" id="message-list"><?php
						$no=1+$posisi;
						while($dataTembusan = $CekTembusan->fetch(PDO::FETCH_OBJ)){
							$tgl = substr($dataTembusan->tgl,0,10);
							$params = array(':id_user' => $dataTembusan->userDis);
							$cek_pengirim = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $order=null);
							$data_cek_pengirim = $cek_pengirim->fetch(PDO::FETCH_OBJ);
							
							$params1 = array(':id_sm' => $dataTembusan->id_sm);
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

							$params2 = array(':id_sm' => $dataTembusan->id_sm, ':id_user' => $_SESSION['id_user'], ':kode' => 'CC');
							$CekRead = $this->model->selectprepare("surat_read", $field=null, $params2, "id_sm=:id_sm AND id_user=:id_user AND kode=:kode", $order=null);
							if($CekRead->rowCount() <= 0){?>
								<div class="message-item message-unread">
									<label class="inline">
										<span class="lbl" style="color:#609FC4;font-weight:700"><small><?php echo $no;?></small></span>
									</label>
									<span class="sender" title="<?php echo $data_cek_pengirim->nama;?>"><?php echo $data_cek_pengirim->nama;?> </span>
									<span class="time" style="width:100px;"><small><?php echo tgl_indo($tgl);?></small></span>
									<span class="summary">
										<span class="text">
											<a href="./index.php?op=tembusan&smid=<?php echo $dataTembusan->id_sm;?>"><?php echo $dataTembusan->pengirim;?> : <?php echo $dataTembusan->perihal;?></a>
										</span>&nbsp;
										<?php echo $ProgresStat;?>
									</span>
								</div><?php
							}else{?>						
								<div class="message-item">
									<label class="inline">
										<span style="color:#467287;"><small><?php echo $no;?></small></span>
									</label>
									<span class="sender" title="<?php echo $data_cek_pengirim->nama;?>"><?php echo $data_cek_pengirim->nama;?></span>
									<span class="time" style="width:100px;"><small><?php echo tgl_indo($tgl);?></small></span>
									<span class="summary">
										<span class="text">
											<a href="./index.php?op=tembusan&smid=<?php echo $dataTembusan->id_sm;?>"><?php echo $dataTembusan->pengirim;?> : <?php echo $dataTembusan->perihal;?></a>
										</span>&nbsp;
										<?php echo $ProgresStat;?>
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
			<p>
				<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
				Belum ada data surat tembusan untuk anda. Terimakasih.
			</p>
		</div><?php
	}
	/* PAGINATION */
	//hitung jumlah data
	if(isset($_GET['keyword'])){
		$jml_data = $CekTembusan2->rowCount();
		$link_order="&keyword=$_GET[keyword]";
	}else{
		$jlhdata = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.tembusan LIKE '%\"$_SESSION[id_user]\"%'");
		$jml_data = $jlhdata->rowCount();
		$link_order="";
	}
	//Jumlah halaman
	$JmlHalaman = ceil($jml_data/$batas); 
	//Navigasi ke sebelumnya
	if($pg > 1){
		$link = $pg-1;
		$prev = "index.php?op=tembusan&halaman=$link$link_order";
		$prev_disable = " ";
	}else{
		$prev = "#";
		$prev_disable = "disabled";
	}
	//Navigasi ke selanjutnya
	if($pg < $JmlHalaman){
		$link = $pg + 1;
		$next = "index.php?op=tembusan&halaman=$link$link_order";
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