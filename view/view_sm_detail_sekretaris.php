<?php
$params = array(':id_sm' => trim($_GET['smid']));
$sm = $this->model->selectprepare("arsip_sm a INNER JOIN user b on a.id_user=b.id_user", $field=null, $params, "a.id_sm=:id_sm", "ORDER BY tgl_terima DESC");
if($sm->rowCount() >= 1){
	$data_sm = $sm->fetch(PDO::FETCH_OBJ);
	$idsm= $data_sm->id_sm;
	if(isset($_GET['act']) && $_GET['act'] == "del"){
		$params = array(':id_sm' => $idsm);
		$lihat_sm = $this->model->selectprepare("memo", $field=null, $params, "id_sm=:id_sm");
		if($lihat_sm->rowCount() >= 1){
			die("<script>alert('Data surat masuk ini tidak dapat dihapus karena terkait dengan data disposisi. Jika tetap ingin menghapus, silahkan hapus data disposisi surat ini terlebih dahulu. Terimakasih');window.history.go(-1);</script>");
		}else{
			@unlink('berkas/'.$data_sm->file);
			$params = array(':id_sm' => $idsm);
			$delete = $this->model->hapusprepare("arsip_sm", $params, "id_sm=:id_sm");
			if($delete){
				$cek = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null);
				if($cek->rowCount() <= 0){
					$delete = $this->model->truncate("arsip_sm");
				}
				echo "<script type=\"text/javascript\">alert('Data Berhasil di Hapus...!!');window.location.href=\"./index.php?op=sm\";</script>";
			}else{
				die("<script>alert('Gagal menghapus data surat masuk, Silahkan Coba Kembali..!!');window.history.go(-1);</script>");
			}
		}
	}
	$params = array(':id_sm' => $data_sm->id_sm);
	$cekDisposisi = $this->model->selectprepare("memo a join user b on a.id_user=b.id_user", $field=null, $params, "a.id_sm=:id_sm", "ORDER BY a.tgl ASC");?>
	<div class="widget-box">
		<div class="message-header clearfix">
			<div class="pull-left" style="padding:0 9px;">
				<span class="blue bigger-125">
					<?php echo $data_sm->perihal;?>
				</span>
				<div class="space-4"></div>
				<img class="middle" alt="John's Avatar" src="assets/images/avatars/<?php echo $data_sm->picture;?>" width="32" />
				<a href="#" class="sender"><?php echo $data_sm->nama;?></a>
				<i class="ace-icon fa fa-clock-o bigger-110 orange middle"></i>
				<span class="time grey"><?php echo tgl_indo($data_sm->tgl_terima);?></span>
			</div>
		</div>
		<div class="hr hr-double"></div>
		<div class="message-body">
				Tgl terima/No agenda: <br/><b><?php echo tgl_indo($data_sm->tgl_terima);?> | <?php echo $data_sm->custom_noagenda;?></b>
			<p>
			</p>
			<p>
				Dari: <br/><b><?php echo $data_sm->pengirim;?></b>
			</p>
			<p>
				Tgl/No surat: <br/><b><?php echo tgl_indo($data_sm->tgl_surat);?> | <?php echo $data_sm->no_sm;?></b>
			</p>
			<p>
				Perihal: <br/><b><?php echo $data_sm->perihal;?></b>
			</p>
			<p>
				Keterangan: <br/><b><?php echo $data_sm->ket;?></b>
			</p><?php
			$params = array(':id_sm' => $data_sm->id_sm);
			$cek_memo = $this->model->selectprepare("memo a inner join bagian b on a.disposisi=b.id_bag", $field=null, $params, "a.id_sm=:id_sm");
			if($cek_memo->rowCount() >= 1){
				$data_cek = $cek_memo->fetch(PDO::FETCH_OBJ);?>
				<p>
					<b>Status Surat: </b><br/>Diteruskan ke : <b><a href="./index.php?op=memo&memoid=<?php echo $data_cek->id_status;?>" target="_blank"><?php echo $data_cek->nama_bagian;?></b></a>
				</p><?php
			}?>
			
			<hr/>
			<p>
				Bukti Terima Surat:<br/>
				<span class="label label-xs label-primary label-white middle">
					<a href="./index.php?op=smprint&smid=<?php echo $data_sm->id_sm;?>" target="_blank"><b>Lihat</b></a>
				</span>
				<span class="label label-xs label-danger label-white middle">
					<a href="./index.php?op=smprint&smid=<?php echo $data_sm->id_sm;?>&act=pdf" target="_blank"><b>Cetak</b> <i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
				</span>
			</p>
			<p>
				Detail Surat:<br/>
				<span class="label label-xs label-primary label-white middle">
					<a href="./index.php?op=memoprint&memoid=<?php echo $data_sm->id_sm;?>" target="_blank"><b>Lihat</b></a>
				</span>
				<span class="label label-xs label-danger label-white middle">
					<a href="./index.php?op=memoprint&memoid=<?php echo $data_sm->id_sm;?>&act=pdf" target="_blank"><b>Cetak</b> <i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
				</span>
			</p><?php
			$params = array(':id_sm' => $data_sm->id_sm);
			$StatSurat = $this->model->selectprepare("status_surat a join user b on a.id_user=b.id_user", $field=null, $params, "a.id_sm=:id_sm", "ORDER BY a.id_status ASC");
			if($StatSurat->rowCount() >= 1){?>
				<hr/>
				<div class="widget-body">
					<div class="widget-main padding-0">
						<div id="profile-feed-1" class="profile-feed">
						
						</div>
					</div>
				</div><?php
				$no=1;
				while($dataStatSurat= $StatSurat->fetch(PDO::FETCH_OBJ)){?>
					<div class="profile-activity clearfix"><?php
						if($no == 1){?>
							<b>PROGRES SURAT</b><?php
						}
						if($dataStatSurat->statsurat == 1){
							$statusSirat = "Sedang diproses";
						}elseif($dataStatSurat->statsurat == 2){
							$statusSirat = "Selesai";
						}elseif($dataStatSurat->statsurat == 0){
							$statusSirat = "Dibatalkan";
						}?>
						<div>
							<a class="user" href="#"><?php echo $dataStatSurat->nama; ?></a>
							update status surat : <span class="text-primary"><?php echo $statusSirat; ?></span><?php
							if($dataStatSurat->ket !==''){?>
								<?php echo '('.$dataStatSurat->ket .')';
								if($dataStatSurat->file_progress != ''){ ?>							
									&nbsp;<a href="./berkas/<?php echo $dataStatSurat->file_progress;?>" title="Dokumen Pendukung" target="_blank"><i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a> <?php
								}
							}?>
							<div class="time">
								<?php echo tgl_indo1($dataStatSurat->created);?>, <?php echo substr($dataStatSurat->created,-9,-3);?> WIB
							</div>
						</div><?php
						/*if($dataStatSurat->id_user == $_SESSION['id_user']){?>
							<div class="tools action-buttons">
								<a href="./index.php?op=memo&memoid=<?php echo $data_memo->id_sm;?>&act=progres&id=<?php echo $dataStatSurat->id_status;?>" class="blue">
									<i class="ace-icon fa fa-pencil bigger-125"></i>
								</a>
								<a href="./index.php?op=memo&memoid=<?php echo $data_memo->id_sm;?>&act=progres&id=<?php echo $dataStatSurat->id_status;?>&do=delete" class="red">
									<i class="ace-icon fa fa-times bigger-125"></i>
								</a>
							</div><?php
						}*/ ?>
					</div>	
					<?php
				$no++;
				}
			}
			
			//echo $data_sm->disposisi;
			if($cekDisposisi->rowCount() >= 1){?>
				<p><hr/><b>RIWAYAT DISPOSISI SURAT:</b><br/>
					<table id="simple-table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th width="200">Disposisi dari</th>
								<th width="200">Tujuan Disposisi</th>
								<th>Catatan</th>
								<th width="200">Waktu</th>
							</tr>
						</thead>
						<tbody><?php
							while($dataDisposisi= $cekDisposisi->fetch(PDO::FETCH_OBJ)){
								$ListDisposisi2 = json_decode($dataDisposisi->disposisi, true);
								$tgl_dispolevel = substr($dataDisposisi->tgl,0,10);?>
								<tr>
									<td><?php echo $dataDisposisi->nama;?></td>
									<td><?php
										foreach($ListDisposisi2 as $listdispo){
											$TampilUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user='$listdispo'")->fetch(PDO::FETCH_OBJ);
											echo "- ".$TampilUser->nama;?>
												<a href="./index.php?op=disposisiprint&smid=<?php echo $data_sm->id_sm;?>&iduser=<?php echo $TampilUser->id_user; ?>&dispo=<?php echo $dataDisposisi->id_user;?>" target="_blank"><i class="ace-icon fa fa-globe align-top bigger-125 icon-on-right"></i></a>
												<a href="./index.php?op=disposisiprint&smid=<?php echo $data_sm->id_sm;?>&iduser=<?php echo $TampilUser->id_user; ?>&dispo=<?php echo $dataDisposisi->id_user;?>&act=pdf" target="_blank"><i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a><br/><?php
										}?>
									</td>
									<td><?php echo $dataDisposisi->note;?></td>
									<td><?php echo tgl_indo($tgl_dispolevel);?>, <?php echo substr($dataDisposisi->tgl,-9,-3);?> WIB</td>
								</tr><?php
							}?>
						</tbody>
					</table>
				</p><?php
			}?>
			<ul class="pager"><?php
				if($data_sm->file != ''){?>
				<li class="previous">
					<a href="./berkas/<?php echo $data_sm->file;?>" target="_blank" class="btn btn-primary">Lihat Surat<i class="ace-icon fa fa-file-pdf-o align-top bigger-125 icon-on-right"></i></a>
				</li><?php
				}
				if($HakAkses->sm == "W"){?>
				<li class="next">
					<a href="./index.php?op=add_sm&smid=<?php echo $data_sm->id_sm;?>" class="btn btn-danger">Edit Surat<i class="ace-icon fa fa-pencil align-top bigger-125 icon-on-right"></i></a>
				</li><?php
				}?>
			</ul>
		</div>
	</div><?php
}else{?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<p>
			<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
			Data Surat masuk tidak ditemukan. Terimakasih.
		</p>
		<p>
			<a href="./index.php?op=sm"><button class="btn btn-minier btn-danger">Kembali</button></a>
		</p>
	</div><?php
}?>