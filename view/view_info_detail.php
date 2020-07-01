<?php
$params = array(':id_info' => trim($_GET['infoid']));
$userLike = "'%\"$_SESSION[id_user]\"%'";
$memo = $this->model->selectprepare("info a join user b on a.pengirim_info=b.id_user", $field=null, $params, "a.id_info=:id_info", "AND a.tujuan_info LIKE $userLike");
if($memo->rowCount() >= 1){
	$data_memo = $memo->fetch(PDO::FETCH_OBJ);
	$tgl_memo = substr($data_memo->tgl_info,0,10);
	$params = array(':id_sm' => $data_memo->id_info, ':id_user' => $_SESSION['id_user'], ':kode' => 'INFO');
	$CekRead = $this->model->selectprepare("surat_read", $field=null, $params, "id_sm=:id_sm AND id_user=:id_user AND kode=:kode");
	if($CekRead->rowCount() <= 0){
		$field = array('id_user' => $_SESSION['id_user'], 'id_sm' => $data_memo->id_info, 'kode' => 'INFO');
		$insert2 = $this->model->insertprepare("surat_read", $field, $params);
	}?>
	<div class="widget-box">
		<div class="message-header clearfix">
			<div class="pull-left" style="padding:0 9px;">
				<span class="blue bigger-125"> Memo : <?php echo $data_memo->judul_info;?></span>
				<div class="space-4"></div>
				<img class="middle" alt="<?php echo $data_memo->nama;?>" src="assets/images/avatars/<?php echo $data_memo->picture;?>" width="32" />
				<a href="#" class="sender"><?php echo $data_memo->nama;?></a>
				<i class="ace-icon fa fa-clock-o bigger-110 orange middle"></i>
				<span class="time grey"><?php echo tgl_indo($tgl_memo);?>, <?php echo substr($data_memo->tgl_info,-9,-3);?> WIB</span>
			</div>
		</div>
		<div class="hr hr-double"></div>
		<div class="message-body">
			<p>
				<?php echo $data_memo->ket_info;?>
			</p>
		</div>
	</div><?php
}else{?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<p>
			<strong><i class="ace-icon fa fa-check"></i>Perhatian!!!</strong>
			Belum ada data. Terimakasih.
		</p>
	</div><?php	
}
?>